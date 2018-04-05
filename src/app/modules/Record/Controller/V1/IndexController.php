<?php
namespace Record\Controller\V1;

use Shirou\Constants\ErrorCode;
use Shirou\UserException;
use Core\Controller\AbstractController;
use Core\Helper\Utils as Helper;
use Record\Model\Voice as VoiceModel;
use Record\Model\VoiceScript as VoiceScriptModel;
use Record\Transformer\VoiceScript as VoiceScriptTransformer;
use Record\Transformer\Voice as VoiceTransformer;

/**
 * @RoutePrefix("/v1/records")
 */
class IndexController extends AbstractController
{
    protected $recordPerPage = 30;

    /**
     * @Route("/scripts", methods={"GET"})
     */
    public function scriptsAction()
    {
        $sql = 'SELECT * FROM fly_voice_script AS r1 JOIN ';
        $sql .= '(SELECT CEIL(RAND() * (SELECT MAX(vs_id) FROM fly_voice_script)) AS id) AS r2 ';
        $sql .= 'WHERE r1.vs_id >= r2.id AND r1.vs_status = 1 ORDER BY r1.vs_id ASC LIMIT 1';
        $raw = $this->getDI()->get('db')->fetchOne($sql);

        if (!empty($raw)) {
            $myVoiceScript = new VoiceScriptModel();
            $myVoiceScript->id = (int) $raw['vs_id'];
            $myVoiceScript->command = (string) $raw['vs_command'];
            $myVoiceScript->text = (string) $raw['vs_text'];
            $myVoiceScript->status = (int) $raw['vs_status'];
            $myVoiceScript->datecreated = (int) $raw['vs_date_created'];

            return $this->createItem(
                $myVoiceScript,
                new VoiceScriptTransformer,
                'data'
            );
        } else {
            return $this->respondWithArray([], 'data');
        }
    }

    /**
     * @Route("/", methods={"GET"})
     */
    public function listAction()
    {
        die('a');

        // $page = (int) $this->request->getQuery('page', null, 1);
        // $formData = [];
        // $hasMore = true;
        //
        // // Search keyword in specified field model
        // $searchKeywordInData = [
        //     'email',
        //     'fullname',
        //     'mobilenumber'
        // ];
        // $page = (int) $this->request->getQuery('page', null, 1);
        // $orderBy = (string) $this->request->getQuery('orderby', null, 'id');
        // $orderType = (string) $this->request->getQuery('ordertype', null, 'desc');
        // $keyword = (string) $this->request->getQuery('keyword', null, '');
        //
        // // optional Filter
        // $status = (int) $this->request->getQuery('status', null, 0);
        // $verifytype = (int) $this->request->getQuery('verifytype', null, 0);
        // $groupid = (string) $this->request->getQuery('groupid', null, '');
        //
        // $formData['columns'] = '*';
        // $formData['conditions'] = [
        //     'keyword' => $keyword,
        //     'searchKeywordIn' => $searchKeywordInData,
        //     'filterBy' => [
        //         'status' => $status,
        //         'verifytype' => $verifytype,
        //         'groupid' => $groupid
        //     ]
        // ];
        // $formData['orderBy'] = $orderBy;
        // $formData['orderType'] = $orderType;
        //
        // $myUsers = UserModel::paginate($formData, $this->recordPerPage, $page);
        //
        // if ($myUsers->total_pages > 0) {
        //     if ($page == $myUsers->total_pages) {
        //         $hasMore = false;
        //     }
        //
        //     return $this->createCollection(
        //         $myUsers->items,
        //         new UserTransformer,
        //         'data',
        //         [
        //             'meta' => [
        //                 'recordPerPage' => $this->recordPerPage,
        //                 'hasMore' => $hasMore,
        //                 'totalItems' => $myUsers->total_items,
        //                 'orderBy' => $orderBy,
        //                 'orderType' => $orderType,
        //                 'page' => $page
        //             ]
        //         ]
        //     );
        // } else {
        //     return $this->respondWithArray([], 'data');
        // }
    }

    /**
     * @Route("/", methods={"POST"})
     */
    public function createAction()
    {
        $formData = (array) $this->request->getPost();
        $uid = (int) $this->getDI()->getAuth()->getUser()->id;

        $myVoice = VoiceModel::findFirst([
            'sid = :sid: AND uid = :uid:',
            'bind' => [
                'sid' => (int) $formData['sid'],
                'uid' => (int) $uid
            ]
        ]);

        if ($myVoice) {
            throw new UserException(ErrorCode::DATA_DUPLICATE);
        }

        $myVoice = new VoiceModel();
        $myVoice->assign([
            'sid' => (int) $formData['sid'],
            'uid' => (int) $uid,
            'status' => (int) VoiceModel::STATUS_PENDING
        ]);

        if (!$myVoice->create()) {
            throw new UserException(ErrorCode::DATA_CREATE_FAIL);
        }

        // Update recording times in Firebase

        return $this->createItem(
            $myVoice,
            new VoiceTransformer,
            'data'
        );
    }
}
