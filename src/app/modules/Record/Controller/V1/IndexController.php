<?php
namespace Record\Controller\V1;

use Shirou\Constants\ErrorCode;
use User\Constants\ErrorCode as UserErrorCode;
use Shirou\UserException;
use Core\Controller\AbstractController;
use Core\Helper\Utils as Helper;
use Record\Model\Voice as VoiceModel;
use User\Model\User as UserModel;
use Record\Model\VoiceScript as VoiceScriptModel;
use Record\Transformer\VoiceScript as VoiceScriptTransformer;
use Record\Transformer\Voice as VoiceTransformer;
use Record\Transformer\VoiceItem as VoiceItemTransformer;

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
        // check record times of user
        $uid = (int) $this->getDI()->getAuth()->getUser()->id;
        $myUser = UserModel::findFirst([
            'id = :id: AND status = :status: AND isverified = :isverified:',
            'bind' => [
                'id' => (int) $uid,
                'status' => UserModel::STATUS_ENABLE,
                'isverified' => UserModel::IS_VERIFIED
            ]
        ]);

        if (!$myUser) {
            throw new UserException(UserErrorCode::USER_NOTFOUND);
        }

        $myUserProfile = $myUser->getProfile();
        if ($myUserProfile->recordtimes <= 0) {
            throw new UserException(UserErrorCode::USER_REACH_LIMIT_RECORD);
        }

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

            return $this->createCollection(
                [$myVoiceScript],
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
        $page = (int) $this->request->getQuery('page', null, 1);
        $formData = [];
        $hasMore = true;

        // Search keyword in specified field model
        $searchKeywordInData = [];
        $page = (int) $this->request->getQuery('page', null, 1);
        $orderBy = (string) $this->request->getQuery('orderby', null, 'datecreated');
        $orderType = (string) $this->request->getQuery('ordertype', null, 'asc');
        $keyword = (string) $this->request->getQuery('keyword', null, '');

        // optional Filter
        $status = (int) $this->request->getQuery('status', null, 0);
        $uid = (int) $this->request->getQuery('uid', null, 0);

        $formData['columns'] = ['uid', 'status', 'datecreated'];
        $formData['conditions'] = [
            'keyword' => $keyword,
            'searchKeywordIn' => $searchKeywordInData,
            'filterBy' => [
                'status' => $status,
                'uid' => $uid
            ]
        ];
        $formData['orderBy'] = $orderBy;
        $formData['orderType'] = $orderType;
        $formData['groupBy'] = 'uid';

        $myVoices = VoiceModel::paginate($formData, $this->recordPerPage, $page);

        if ($myVoices->total_pages > 0) {
            if ($page == $myVoices->total_pages) {
                $hasMore = false;
            }

            return $this->createCollection(
                $myVoices->items,
                new VoiceTransformer,
                'data',
                [
                    'meta' => [
                        'recordPerPage' => $this->recordPerPage,
                        'hasMore' => $hasMore,
                        'totalItems' => $myVoices->total_items,
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
    public function createAction()
    {
        // openlog('myapplication', LOG_NDELAY, LOG_USER);
        // foreach ($this->request->getUploadedFiles() as $files) {
        //     syslog(LOG_WARNING, json_encode($files));
        // }

        $formData = (array) $this->request->getPost();
        $uid = (int) $this->getDI()->getAuth()->getUser()->id;
        $myFireBase = $this->firebase->getDatabase();

        $myUser = UserModel::findFirstById($uid);
        if (!$myUser) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }

        $myVoice = VoiceModel::findFirst([
            'vsid = :vsid: AND uid = :uid:',
            'bind' => [
                'vsid' => (int) $formData['sid'],
                'uid' => (int) $myUser->id
            ]
        ]);

        if ($myVoice) {
            throw new UserException(ErrorCode::DATA_DUPLICATE);
        }

        $myVoice = new VoiceModel();
        $myVoice->assign([
            'vsid' => (int) $formData['sid'],
            'uid' => (int) $myUser->id,
            'status' => (int) VoiceModel::STATUS_PENDING
        ]);

        if (!$myVoice->create()) {
            throw new UserException(ErrorCode::DATA_CREATE_FAIL);
        }

        // Disable voice script
        $myVoiceScript = VoiceScriptModel::findFirstById((int) $myVoice->vsid);
        $myVoiceScript->status = VoiceScriptModel::STATUS_DISABLE;

        if (!$myVoiceScript->update()) {
            throw new UserException(ErrorCode::DATA_UPDATE_FAIL);
        }

        // Reduce record times & increase tmp point
        $myProfile = $myUser->getProfile();
        $myProfile->recordtimes = (int) $myProfile->recordtimes - 1;
        $myProfile->tmppoint = (int) $myProfile->tmppoint + 1;

        if (!$myProfile->update()) {
            throw new UserException(ErrorCode::DATA_UPDATE_FAIL);
        }

        // set firebase
        try {
            $myFireBase->getReference('/users/' . $myUser->oauthuid . '/record_times')->set($myProfile->recordtimes);
            $myFireBase->getReference('/users/' . $myUser->oauthuid . '/tmp_point')->set($myProfile->tmppoint);
        } catch (ApiException $e) {
            $response = $e->getResponse();
            throw new \Exception($response->getBody());
        }

        return $this->createItem(
            $myVoice,
            new VoiceTransformer,
            'data'
        );
    }

    /**
     * @Route("/formsource", methods={"GET"})
     */
    public function formsourceAction()
    {
        return $this->respondWithArray([
            'statusList' => VoiceModel::getStatusList()
        ], 'data');
    }

    /**
     * @Route("/user", methods={"GET"})
     */
    public function getbyuseridAction()
    {
        $recordPerPage = 30;
        $page = (int) $this->request->getQuery('page', null, 1);
        $formData = [];
        $hasMore = true;

        // Search keyword in specified field model
        $searchKeywordInData = [];
        $page = (int) $this->request->getQuery('page', null, 1);
        $orderBy = (string) $this->request->getQuery('orderby', null, 'id');
        $orderType = (string) $this->request->getQuery('ordertype', null, 'desc');
        $keyword = (string) $this->request->getQuery('keyword', null, '');

        // optional Filter
        $status = $this->request->getQuery('status', null, []);
        $uid = (int) $this->request->getQuery('uid', null, 0);

        $formData['columns'] = '*';
        $formData['conditions'] = [
            'keyword' => $keyword,
            'searchKeywordIn' => $searchKeywordInData,
            'filterBy' => [
                'uid' => $uid,
                'status' => $status
            ]
        ];
        $formData['orderBy'] = $orderBy;
        $formData['orderType'] = $orderType;

        $myVoices = VoiceModel::paginate($formData, $recordPerPage, $page);

        if ($myVoices->total_pages > 0) {
            if ($page == $myVoices->total_pages) {
                $hasMore = false;
            }

            return $this->createCollection(
                $myVoices->items,
                new VoiceItemTransformer,
                'data',
                [
                    'meta' => [
                        'recordPerPage' => $recordPerPage,
                        'hasMore' => $hasMore,
                        'totalItems' => $myVoices->total_items,
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
     * @Route("/validate/{id:[0-9]+}", methods={"PUT"})
     */
    public function validateAction($id = 0)
    {
        $formData = (array) $this->request->getJsonRawBody();
        $myFireBase = $this->firebase->getDatabase();

        $myVoice = VoiceModel::findFirst([
            'id = :id:',
            'bind' => [
                'id' => (int) $id
            ]
        ]);

        if (!$myVoice) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }

        // get owner of this voice
        $myUser = UserModel::findFirst([
            'id = :id: AND status = :status:',
            'bind' => [
                'id' => $myVoice->uid,
                'status' => UserModel::STATUS_ENABLE
            ]
        ]);

        if (!$myUser) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }

        $validatorUserId = (int) $this->auth->getUser()->id;

        if (
            ($myVoice->validatedby != 0 && $myVoice->validatedby != $validatorUserId)
            ||
            ($myVoice->validatedby == 0 && $validatorUserId == $myVoice->uid)
        ) {
            throw new \Exception('User validate rejected!!!');
        }

        $myProfile = $myUser->getProfile();

        if ($formData['status'] == 1) {
            $myProfile->point = (int) $myProfile->point + 1;

            if ($myVoice->status == VoiceModel::STATUS_REJECTED) {
                $myProfile->recordtimes = (int) $myProfile->recordtimes - 1;
            } else {
                $myProfile->tmppoint = (int) $myProfile->tmppoint - 1;
            }
        } else {
            $myProfile->recordtimes = (int) $myProfile->recordtimes + 1;

            // if record has been approved
            if ($myVoice->status == VoiceModel::STATUS_APPROVED) {
                $myProfile->point = (int) $myProfile->point - 1;
            } else if ($myVoice->status == VoiceModel::STATUS_PENDING) {
                $myProfile->tmppoint = (int) $myProfile->tmppoint - 1;
            }
        }

        // force negative tmp_point to zero
        if ($myProfile->tmppoint < 0) {
            $myProfile->tmppoint = 0;
        }

        if (!$myProfile->update()) {
            throw new UserException(ErrorCode::DATA_UPDATE_FAIL);
        }

        try {
            $myFireBase->getReference('/users/' . $myUser->oauthuid . '/record_times')->set((int) $myProfile->recordtimes);
            $myFireBase->getReference('/users/' . $myUser->oauthuid . '/tmp_point')->set((int) $myProfile->tmppoint);
            $myFireBase->getReference('/users/' . $myUser->oauthuid . '/point')->set((int) $myProfile->point);
        } catch (ApiException $e) {
            $response = $e->getResponse();
            throw new \Exception($response->getBody());
        }

        $myVoice->status = (int) $formData['status'];
        $myVoice->validatedby = (int) $validatorUserId;

        if (!$myVoice->update()) {
            throw new UserException(ErrorCode::DATA_UPDATE_FAIL);
        }

        return $this->createItem(
            $myVoice,
            new VoiceTransformer,
            'data'
        );
    }
}
