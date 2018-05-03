<?php
namespace Gift\Controller\V1;

use Shirou\Constants\ErrorCode;
use Shirou\UserException;
use Core\Controller\AbstractController;
use Core\Helper\Utils as Helper;
use Gift\Model\GiftType as GiftTypeModel;
use Gift\Model\Gift as GiftModel;
use Gift\Model\GiftStock as GiftStockModel;
use Gift\Transformer\Gift as GiftTransformer;
use Gift\Transformer\GiftStock as GiftStockTransformer;

/**
 * @RoutePrefix("/v1/gifts")
 */
class IndexController extends AbstractController
{
    protected $recordPerPage = 30;

    /**
     * @Route("/", methods={"GET"})
     */
    public function listAction()
    {
        $page = (int) $this->request->getQuery('page', null, 1);
        $formData = [];
        $hasMore = true;

        // Search keyword in specified field model
        $searchKeywordInData = [
            'name'
        ];
        $page = (int) $this->request->getQuery('page', null, 1);
        $orderBy = (string) $this->request->getQuery('orderby', null, 'id');
        $orderType = (string) $this->request->getQuery('ordertype', null, 'desc');
        $keyword = (string) $this->request->getQuery('keyword', null, '');

        // optional Filter
        $isused = (int) $this->request->getQuery('isused', null, 0);

        $formData['columns'] = '*';
        $formData['conditions'] = [
            'keyword' => $keyword,
            'searchKeywordIn' => $searchKeywordInData,
            'filterBy' => [
                'isused' => $isused
            ]
        ];
        $formData['orderBy'] = $orderBy;
        $formData['orderType'] = $orderType;

        $myGifts = GiftModel::paginate($formData, $this->recordPerPage, $page);

        if ($myGifts->total_pages > 0) {
            if ($page == $myGifts->total_pages) {
                $hasMore = false;
            }

            return $this->createCollection(
                $myGifts->items,
                new GiftTransformer,
                'data',
                [
                    'meta' => [
                        'recordPerPage' => $this->recordPerPage,
                        'hasMore' => $hasMore,
                        'totalItems' => $myGifts->total_items,
                        'orderBy' => $orderBy,
                        'orderType' => $orderType,
                        'page' => $page
                    ]
                ]
            );
        } else {
            return $this->respondWithArray([], 'data');
        }
    }

    /**
     * @Route("/bulk", methods={"POST"})
     */
    public function bulkAction()
    {
        $formData = (array) $this->request->getJsonRawBody();

        if (count($formData['itemSelected']) > 0 && $formData['actionSelected'] != '') {
            switch ($formData['actionSelected']) {
                case 'delete':
                    $this->db->begin();
                    foreach ($formData['itemSelected'] as $item) {
                        $myGift = GiftModel::findFirst([
                            'id = :id:',
                            'bind' => ['id' => (int) $item->id]
                        ])->delete();

                        if ($myGift == false) {
                            $this->db->rollback();
                            return;
                        }
                    }

                    if ($this->db->commit() == false) {
                        throw new UserException(ErrorCode::DATA_BULK_FAILED);
                    }

                    break;
            }
        }

        return $this->respondWithOK();
    }

    /**
     * @Route("/", methods={"POST"})
     */
    public function addAction()
    {
        $formData = (array) $this->request->getPost();

        $myGift = new GiftModel();
        $myGift->assign([
            'name' => (string) $formData['name'],
            'gtid' => (int) $formData['type'],
            'isused' => (int) GiftModel::IS_NOT_USED,
            'requiredpoint' => (int) $formData['requiredpoint']
        ]);

        if (!$myGift->create()) {
            throw new UserException(ErrorCode::DATA_CREATE_FAIL);
        }

        $attrs = json_decode($formData['attrs'], JSON_UNESCAPED_UNICODE);
        if (count($attrs) > 0) {
            foreach ($attrs as $attr) {
                $myGiftStock = new GiftStockModel();
                $myGiftStock->assign([
                    'gid' => (int) $myGift->id,
                    'gaid' => (int) $attr['key'],
                    'value' => (string) $attr['value']
                ]);

                if (!$myGiftStock->create()) {
                    throw new UserException(ErrorCode::DATA_CREATE_FAIL);
                }
            }
        }

        return $this->createItem(
            $myGift,
            new GiftTransformer,
            'data'
        );
    }

    /**
     * @Route("/{id:[0-9]+}", methods={"GET"})
     */
    public function getAction(int $id = 0)
    {
        $myGift = GiftModel::findFirst([
            'id = :id:',
            'bind' => ['id' => (int) $id]
        ]);

        if (!$myGift) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }

        return $this->createItem(
            $myGift,
            new GiftTransformer,
            'data'
        );
    }

    /**
     * @Route("/{id:[0-9]+}", methods={"PUT"})
     */
    public function updateAction(int $id = 0)
    {
        $formData = (array) $this->request->getJsonRawBody();

        $myGift = GiftModel::findFirst([
            'id = :id:',
            'bind' => ['id' => (int) $id]
        ]);

        if (!$myGift) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }

        $myGift->name = (string) $formData['name'];
        $myGift->isused = (int) $formData['isused'];
        $myGift->requiredpoint = (int) $formData['requiredpoint'];

        if (!$myGift->update()) {
            throw new UserException(ErrorCode::DATA_UPDATE_FAIL);
        }

        if (count($formData['stocks']) > 0) {
            foreach ($formData['stocks'] as $item) {
                $myGiftStock = GiftStockModel::findFirst([
                    'id = :id:',
                    'bind' => [
                        'id' => (int) $item->key
                    ]
                ]);

                $myGiftStock->value = (string) $item->value;

                if (!$myGiftStock->update()) {
                    throw new UserException(ErrorCode::DATA_UPDATE_FAIL);
                }
            }
        }

        return $this->createItem(
            $myGift,
            new GiftTransformer,
            'data'
        );
    }

    /**
     * @Route("/{id:[0-9]+}/clone", methods={"POST"})
     */
    public function cloneAction(int $id = 0)
    {
        $formData = (array) $this->request->getJsonRawBody();

        $myGift = GiftModel::findFirst([
            'id = :id:',
            'bind' => ['id' => (int) $id]
        ]);

        if (!$myGift) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }

        $myGiftClone = new GiftModel();
        $myGiftClone->assign([
            'name' => (string) $formData['name'],
            'gtid' => (int) $myGift->gtid,
            'isused' => (int) GiftModel::IS_NOT_USED,
            'requiredpoint' => (string) $formData['requiredpoint']
        ]);

        if ($myGift->cover != '') {
            $giftCoverPath = $this->config->default->gifts->directory . $myGift->cover;
            $fileExt = explode('/', $this->file->getMimetype($giftCoverPath))[1];

            $giftCloneModelPath = Helper::getCurrentDateDirName() . time() . '.' . $fileExt;
            $giftClonePath = $this->config->default->gifts->directory . $giftCloneModelPath;
            if ($this->file->copy($giftCoverPath, $giftClonePath)) {
                $myGiftClone->cover = $giftCloneModelPath;
            }
        }

        if (!$myGiftClone->create()) {
            throw new UserException(ErrorCode::DATA_CREATE_FAIL);
        }

        if (count($formData['stocks']) > 0) {
            foreach ($formData['stocks'] as $item) {
                $myGiftCloneStock = new GiftStockModel();
                $myGiftCloneStock->assign([
                    'gid' => (int) $myGiftClone->id,
                    'gaid' => (int) $item->key,
                    'value' => (string) $item->value
                ]);

                if (!$myGiftCloneStock->create()) {
                    throw new UserException(ErrorCode::DATA_CREATE_FAIL);
                }
            }
        }

        return $this->createItem(
            $myGiftClone,
            new GiftTransformer,
            'data'
        );
    }

    /**
     * @Route("/{id:[0-9]+}", methods={"DELETE"})
     */
    public function deleteAction(int $id = 0)
    {
        $myGift = GiftModel::findFirst([
            'id = :id:',
            'bind' => [
                'id' => (int) $id
            ]
        ]);

        if (!$myGift) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }

        if (!$myGift->delete()) {
            throw new UserException(ErrorCode::DATA_DELETE_FAIL);
        }

        return $this->createItem(
            $myGift,
            new GiftTransformer,
            'data'
        );
    }

    /**
     * @Route("/formsource", methods={"GET"})
     */
    public function formsourceAction()
    {
        return $this->respondWithArray([
            'typeList' => GiftTypeModel::find(),
            'isusedList' => GiftModel::getUsedList()
        ], 'data');
    }

    /**
     * @Route("/test", methods={"GET"})
     */
    public function testAction()
    {
        $myurl = 'https://drive.google.com/get_video_info?docid=1LRyqbopwfrg_gvXKfP9AmfIavXEKkgxq';
        $resourceID = '1LRyqbopwfrg_gvXKfP9AmfIavXEKkgxq';

        $URL = 'https://drive.google.com/e/get_video_info?docid='.$resourceID;
        $curl = curl_init();
        curl_setopt ($curl, CURLOPT_URL, $URL);
        curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt ($curl, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$this->auth));
        curl_setopt ($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt ($curl, CURLOPT_HEADER, 1);
        #curl_setopt ($curl,CURLOPT_POST, 4);
        #curl_setopt ($curl,CURLOPT_POSTFIELDS, 'client_id='.$client_id.'&client_secret='.$client_secret.'&refresh_token='.$value.'&grant_type=refresh_token');
        $response_data = curl_exec ($curl);
        $response_data = urldecode(urldecode($response_data));

        if(curl_error($curl)){
            print 'error:' . curl_error($curl);
        }

        curl_close ($curl);

        echo '<pre>';
        print_r($response_data);
        die;
    }

    /**
     * @Route("/stream", methods={"GET"})
     */
    public function streamAction()
    {
      $url = 'https://r6---sn-i3beln7r.c.drive.google.com/videoplayback?id=e774493f70c208c1&itag=18&source=webdrive&requiressl=yes&mm=30&mn=sn-i3beln7r&ms=nxu&mv=m&pl=21&ttl=transient&ei=rpLpWqLjKIGG_QHi9qqwAw&susc=dr&driveid=1LRyqbopwfrg_gvXKfP9AmfIavXEKkgxq&app=explorer&mime=video/mp4&lmt=1525250255747633&mt=1525256720&ip=113.161.41.28&ipbits=0&expire=1525260478&cp=QVNHV0NfT1RVSVhOOlpjbndNdk1ybVh4&sparams=ip,ipbits,expire,id,itag,source,requiressl,mm,mn,ms,mv,pl,ttl,ei,susc,driveid,app,mime,lmt,cp&signature=105A6B5E26644188FB9A5FFFBD5E93857DCF8E66.A14FE03FB2B14614AA4E2303A691346DD1F73A6D&key=ck2';
      $cookie = 'YelaMsIulZw';

      $ch = curl_init($url);
  		curl_setopt ($ch, CURLOPT_HTTPHEADER, array( 'DRIVE_STREAM' => $cookie) );
	    // curl_setopt($ch,CURLOPT_WRITEFUNCTION , array($this,'__writeFunction'));
	    curl_exec($ch);
	    curl_close($ch);
      exit();
    }

    public function __writeFunction($curl, $data) {
  	    echo $data;
  	    return strlen($data);
  	}
}
