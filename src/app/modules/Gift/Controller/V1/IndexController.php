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
     * @Route("/", methods={"POST"})
     */
    public function addAction()
    {
        $formData = (array) $this->request->getJsonRawBody();

        $myGift = new GiftModel();
        $myGift->assign([
            'name' => (string) $formData['name'],
            'gtid' => (int) $formData['type'],
            'isused' => (int) GiftModel::IS_NOT_USED
        ]);

        if (!$myGift->create()) {
            throw new UserException(ErrorCode::DATA_CREATE_FAIL);
        }

        if (count($formData['attrs']) > 0) {
            foreach ($formData['attrs'] as $attr) {
                $myGiftStock = new GiftStockModel();
                $myGiftStock->assign([
                    'gid' => (int) $myGift->id,
                    'gaid' => (int) $attr->key,
                    'value' => (string) $attr->value
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
     * @Route("/formsource", methods={"GET"})
     */
    public function formsourceAction()
    {
        return $this->respondWithArray([
            'typeList' => GiftTypeModel::find(),
            'isusedList' => GiftModel::getUsedList()
        ], 'data');
    }
}
