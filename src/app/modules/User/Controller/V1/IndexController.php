<?php
namespace User\Controller\V1;

use Shirou\Constants\ErrorCode;
use Shirou\UserException;
use Core\Controller\AbstractController;
use User\Model\User as UserModel;
use User\Model\UserProfile as UserProfileModel;
use User\Transformer\User as UserTransformer;
use User\Constants\ErrorCode as UserErrorCode;
use Core\Helper\Utils as Helper;
use Kreait\Firebase\Database\RuleSet;
use Gift\Model\Gift as GiftModel;

/**
 * @RoutePrefix("/v1/users")
 */
class IndexController extends AbstractController
{
    protected $recordPerPage = 50;

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
            'email',
            'fullname',
            'mobilenumber'
        ];
        $page = (int) $this->request->getQuery('page', null, 1);
        $orderBy = (string) $this->request->getQuery('orderby', null, 'id');
        $orderType = (string) $this->request->getQuery('ordertype', null, 'desc');
        $keyword = (string) $this->request->getQuery('keyword', null, '');

        // optional Filter
        $status = (int) $this->request->getQuery('status', null, 0);
        $verifytype = (int) $this->request->getQuery('verifytype', null, 0);
        $groupid = (string) $this->request->getQuery('groupid', null, '');

        $formData['columns'] = '*';
        $formData['conditions'] = [
            'keyword' => $keyword,
            'searchKeywordIn' => $searchKeywordInData,
            'filterBy' => [
                'status' => $status,
                'verifytype' => $verifytype,
                'groupid' => $groupid
            ]
        ];
        $formData['orderBy'] = $orderBy;
        $formData['orderType'] = $orderType;

        $myUsers = UserModel::paginate($formData, $this->recordPerPage, $page);

        if ($myUsers->total_pages > 0) {
            if ($page == $myUsers->total_pages) {
                $hasMore = false;
            }

            return $this->createCollection(
                $myUsers->items,
                new UserTransformer,
                'data',
                [
                    'meta' => [
                        'recordPerPage' => $this->recordPerPage,
                        'hasMore' => $hasMore,
                        'totalItems' => $myUsers->total_items,
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
     * @Route("/{id:[0-9]+}", methods={"GET"})
     */
    public function getAction(int $id = 0)
    {
        $myUser = UserModel::findFirst([
            'id = :id:',
            'bind' => ['id' => (int) $id]
        ]);

        if (!$myUser) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }

        return $this->createItem(
            $myUser,
            new UserTransformer,
            'data'
        );
    }

    /**
     * @Route("/", methods={"POST"})
     */
    public function addAction()
    {
        $formData = (array) $this->request->getJsonRawBody();

        $myUser = new UserModel();
        $myUser->email = (string) $formData['email'];
        $myUser->fullname = (string) $formData['fullname'];
        $myUser->groupid = (string) $formData['groupid'];
        $myUser->password = (string) $this->security->hash($formData['password']);
        $myUser->status = (int) $formData['status'];
        $myUser->verifytype = (int) $formData['verifytype'];
        $myUser->isverified = UserModel::IS_VERIFIED;

        // create default avatar
        $avatarImg = new \YoHang88\LetterAvatar\LetterAvatar($myUser->fullname, 'square', 64);
        $avatarModelPath = Helper::getCurrentDateDirName() . time() . '.jpg';
        $avatarPath = $this->config->default->users->directory . $avatarModelPath;
        if ($this->file->write($avatarPath, base64_decode(explode(',',$avatarImg)[1]))) {
            $myUser->avatar = $avatarModelPath;
        }

        if (!$myUser->create()) {
            throw new UserException(ErrorCode::USER_CREATE_FAIL);
        }

        return $this->createItem(
            $myUser,
            new UserTransformer,
            'data'
        );
    }

    /**
     * @Route("/{id:[0-9]+}", methods={"PUT"})
     */
    public function updateAction(int $id = 0)
    {
        $formData = (array) $this->request->getJsonRawBody();

        $myUser = UserModel::findFirst([
            'id = :id:',
            'bind' => ['id' => (int) $id]
        ]);

        if (!$myUser) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }

        $myUser->fullname = (string) $formData['fullname'];
        $myUser->groupid = (string) $formData['groupid'];
        $myUser->status = (int) $formData['status'];
        $myUser->verifytype = (int) $formData['verifytype'];

        if (!$myUser->update()) {
            throw new UserException(ErrorCode::USER_UPDATE_FAIL);
        }

        return $this->createItem(
            $myUser,
            new UserTransformer,
            'data'
        );
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
                    // Start a transaction
                    $this->db->begin();
                    foreach ($formData['itemSelected'] as $item) {
                        $myUser = UserModel::findFirst([
                            'id = :id:',
                            'bind' => ['id' => (int) $item->id]
                        ])->delete();
                        // If fail stop a transaction
                        if ($myUser == false) {
                            $this->db->rollback();
                            return;
                        }
                    }
                    // Commit a transaction
                    if ($this->db->commit() == false) {
                        throw new UserException(ErrorCode::DATA_BULK_FAILED);
                    }

                    break;
                case 'enable':
                    $this->db->begin();
                    foreach ($formData['itemSelected'] as $item) {
                        $myUser = UserModel::findFirst([
                            'id = :id:',
                            'bind' => ['id' => (int) $item->id]
                        ]);
                        $myUser->status = UserModel::STATUS_ENABLE;

                        if (!$myUser->update()) {
                            $this->db->rollback();
                            return;
                        }
                    }

                    if ($this->db->commit() == false) {
                        throw new UserException(ErrorCode::DATA_BULK_FAILED);
                    }

                    break;
                case 'disable':
                    $this->db->begin();
                    foreach ($formData['itemSelected'] as $item) {
                        $myUser = UserModel::findFirst([
                            'id = :id:',
                            'bind' => ['id' => (int) $item->id]
                        ]);
                        $myUser->status = UserModel::STATUS_DISABLE;

                        if (!$myUser->update()) {
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
     * @Route("/{id:[0-9]+}", methods={"DELETE"})
     */
    public function deleteAction(int $id = 0)
    {
        $myUser = UserModel::findFirst([
            'id = :id:',
            'bind' => [
                'id' => (int) $id
            ]
        ]);

        if (!$myUser) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }

        if (!$myUser->delete()) {
            throw new UserException(ErrorCode::DATA_DELETE_FAIL);
        }

        return $this->createItem(
            $myUser,
            new UserTransformer,
            'data'
        );
    }

    /**
     * @Route("/formsource", methods={"GET"})
     */
    public function formsourceAction()
    {
        return $this->respondWithArray([
            'groupList' => UserModel::getGroupList(),
            'statusList' => UserModel::getStatusList(),
            'verifyList' => UserModel::getVerifyList(),
            'genderList' => UserProfileModel::getGenderList()
        ], 'data');
    }

    /**
     * @Route("/securegroup", methods={"GET"})
     */
    public function securegroupAction()
    {
        $acl = $this->config->acl->groups->toArray();
        unset($acl['default']);
        unset($acl['defaultOauth']);
        unset($acl['guest']);

        return $this->respondWithArray(array_keys($acl), 'data');
    }

    /**
     * @Route("/login/{account:[a-z]{1,10}}", methods={"POST"})
     */
    public function loginAction($account)
    {
        $formData = (array) $this->request->getJsonRawBody();
        $username = trim($formData['username']);
        $password = $formData['password'];

        if (isset($formData['username']) && strlen($formData['username']) == 0) {
            throw new UserException(UserErrorCode::AUTH_NO_USERNAME);
        };

        if (strlen($password) == 0) {
            throw new UserException(UserErrorCode::AUTH_NOPASSWORD);
        };

        $this->auth->login($account, $username, $password);

        // Generate jwt authToken for valid user.
        $tokenResponse = $this->auth->getTokenResponse();

        return $this->respondWithArray($tokenResponse, 'data');
    }

    /**
     * @Route("/changepassword", methods={"PUT"})
     */
    public function changepasswordAction()
    {
        $formData = (array) $this->request->getJsonRawBody();

        $myUser = UserModel::findFirst([
            'id = :id: AND status = :status: AND isverified = :isverified:',
            'bind' => [
                'id' => (int) $this->auth->getUser()->id,
                'status' => UserModel::STATUS_ENABLE,
                'isverified' => UserModel::IS_VERIFIED
            ]
        ]);

        if (!$myUser) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }

        if (!$myUser->validatePassword($formData['oldpassword'])) {
            throw new UserException(UserErrorCode::USER_OLD_PASSWORD_NOT_MATCH);
        }

        if ($formData['newpassword'] != $formData['repeatnewpassword']) {
            throw new UserException(UserErrorCode::USER_PASSWORD_NOT_MATCH);
        }

        $myUser->password = $this->security->hash($formData['newpassword']);
        $myUser->datelastchangepassword = time();
        if (!$myUser->update()) {
            throw new UserException(ErrorCode::DATA_UPDATE_FAIL);
        }

        $this->auth->setUser($myUser);

        // Generate jwt authToken for activate user.
        $tokenResponse = $this->auth->getTokenResponse();

        return $this->respondWithArray($tokenResponse, 'data');
    }


    /**
     * Verify token (Firebase)
     *
     * @Route("/verify/{account:[a-z]{1,10}}", methods={"POST"})
     */
    public function verifyAction($account)
    {
        $formData = (array) $this->request->getJsonRawBody();

        if (!isset($formData['token']) || strlen($formData['token']) == 0) {
            throw new \Exception('Missing Firebase Token');
        }

        try {
            $verifiedIdToken = $this->firebase->getAuth()->verifyIdToken($formData['token']);
        } catch (InvalidToken $e) {
            throw new \Exception($e->getMessage());
        }

        $firebaseUid = $verifiedIdToken->getClaim('sub');

        // Check existed Firebase Uid
        $myUser = UserModel::findFirst([
            'oauthuid = :oauthuid: AND status = :status:',
            'bind' => [
                'oauthuid' => (string) $firebaseUid,
                'status' => UserModel::STATUS_ENABLE
            ]
        ]);

        if (!$myUser) {
            $firebaseUser = $this->firebase->getAuth()->getUser($firebaseUid);
            $myFireBase = $this->firebase->getDatabase();
            $myFireBase->getReference('/users/' . $firebaseUid)
                ->set([
                    'record_times' => $this->config->default->voices->limit,
                    'point' => 0
                ]);

            $myUser = new UserModel();
            $myUser->assign([
                'password' => (string) $this->security->hash(rand(1, 999999)),
                'mobilenumber' => (string) $firebaseUser->phoneNumber,
                'verifytype' => (int) UserModel::VERIFY_TYPE_PHONE,
                'isverified' => (int) UserModel::IS_VERIFIED,
                'groupid' => (string) 'member',
                'status' => (int) UserModel::STATUS_ENABLE,
                'oauthprovider' => 'firebase',
                'oauthuid' => (string) $firebaseUid,
                'oauthaccesstoken' => (string) $formData['token'],
                'isprofileupdated' => (int) UserModel::IS_NOT_PROFILE_UPDATED
            ]);

            if (!$myUser->create()) {
                throw new UserException(UserErrorCode::USER_REGISTERFAIL);
            }

            $myUserProfile = new UserProfileModel();
            $myUserProfile->assign([
                'uid' => (int) $myUser->id,
                'point' => 0,
                'recordtimes' => (int) $this->config->default->voices->limit,
            ]);

            if (!$myUserProfile->create()) {
                throw new UserException(UserErrorCode::DATA_CREATE_FAIL);
            }
        }

        $this->auth->setUser($myUser);

        // Generate jwt authToken for activate user.
        $tokenResponse = $this->auth->getTokenResponse();

        return $this->respondWithArray($tokenResponse, 'data');
    }

    /**
     * Update owner password
     *
     * @Route("/updatepassword", methods={"PUT"})
     */
    public function updatepasswordAction()
    {
        $formData = (array) $this->request->getJsonRawBody();

        $myUser = UserModel::findFirst([
            'id = :id: AND status = :status: AND isverified = :isverified:',
            'bind' => [
                'id' => (int) $this->auth->getUser()->id,
                'status' => UserModel::STATUS_ENABLE,
                'isverified' => UserModel::IS_VERIFIED
            ]
        ]);

        if (!$myUser) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }

        if ($formData['newpassword'] != $formData['repeatnewpassword']) {
            throw new UserException(UserErrorCode::USER_PASSWORD_NOT_MATCH);
        }

        $myUser->password = $this->security->hash($formData['newpassword']);
        $myUser->datelastchangepassword = time();
        if (!$myUser->update()) {
            throw new UserException(ErrorCode::DATA_UPDATE_FAIL);
        }

        $this->auth->setUser($myUser);

        // Generate jwt authToken for activate user.
        $tokenResponse = $this->auth->getTokenResponse();

        return $this->respondWithArray($tokenResponse, 'data');
    }

    /**
     * Update owner profile
     *
     * @Route("/profile", methods={"PUT"})
     */
    public function updateprofileAction()
    {
        $formData = (array) $this->request->getJsonRawBody();
        $uid = (int) $this->getDI()->getAuth()->getUser()->id;

        $myUser = UserModel::findFirst([
            'id = :id: AND status = :status: AND isverified = :isverified:',
            'bind' => [
                'id' => (int) $uid,
                'status' => (int) UserModel::STATUS_ENABLE,
                'isverified' => (int) UserModel::IS_VERIFIED
            ]
        ]);

        if (!$myUser) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }

        $myUserProfile = UserProfileModel::findFirstByUid($uid);

        if (!$myUserProfile) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }

        $dob = \DateTime::createFromFormat('d/m/Y', $formData['dob']);

        $myUserProfile->assign([
            'fullname' => (string) $formData['fullname'],
            'email' => (string) $formData['email'],
            'gender' => (int) $formData['gender'],
            'dob' => (int) $dob->getTimestamp(),
            'voiceregion' => (int) $formData['voiceregion']
        ]);

        if (!$myUserProfile->update()) {
            throw new UserException(ErrorCode::DATA_UPDATE_FAIL);
        }

        return $this->createItem(
            $myUser,
            new UserTransformer,
            'data'
        );
    }

    /**
     * Get owner profile
     *
     * @Route("/profile", methods={"GET"})
     */
    public function profileAction()
    {
        $uid = (int) $this->getDI()->getAuth()->getUser()->id;

        $myUser = UserModel::findFirst([
            'id = :id: AND status = :status: AND isverified = :isverified:',
            'bind' => [
                'id' => (int) $uid,
                'status' => (int) UserModel::STATUS_ENABLE,
                'isverified' => (int) UserModel::IS_VERIFIED
            ]
        ]);

        if (!$myUser) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }

        $myUserProfile = UserProfileModel::findFirstByUid($uid);

        if (!$myUserProfile) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }

        return $this->createItem(
            $myUser,
            new UserTransformer,
            'data'
        );
    }

    /**
     * Get user point and record times in firebase
     *
     * @Route("/{id:[0-9]+}/pointandrecordtimes", methods={"GET"})
     */
    public function pointandrecordtimesAction($id = 0)
    {
        $myFireBase = $this->firebase->getDatabase();

        // get owner of this voice
        $myUser = UserModel::findFirst([
            'id = :id: AND status = :status:',
            'bind' => [
                'id' => (int) $id,
                'status' => (int) UserModel::STATUS_ENABLE
            ]
        ]);

        if (!$myUser) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }

        return $this->respondWithArray([
            'point' => $myFireBase->getReference('/users/' . $myUser->oauthuid . '/point')->getValue(),
            'recordtimes' => $myFireBase->getReference('/users/' . $myUser->oauthuid . '/record_times')->getValue()
        ], 'data');
    }

    /**
     * Get gift history of owner
     *
     * @Route("/gift", methods={"GET"})
     */
    public function giftAction($id = 0)
    {
        $uid = (int) $this->auth->getUser()->id;

    }
}
