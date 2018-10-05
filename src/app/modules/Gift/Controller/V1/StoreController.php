<?php
namespace Gift\Controller\V1;

use Shirou\Constants\ErrorCode;
use Shirou\UserException;
use Core\Controller\AbstractController;
use Core\Helper\Utils as Helper;
use Gift\Model\Gift as GiftModel;
use Gift\Model\GiftType as GiftTypeModel;
use Gift\Transformer\Gift as GiftTransformer;
use Gift\Transformer\GiftType as GiftTypeTransformer;
use Gift\Transformer\GiftStore as GiftStoreTransformer;
use User\Model\UserGift as UserGiftModel;
use User\Model\User as UserModel;
use Kreait\Firebase\Exception\ApiException;

/**
 * @RoutePrefix("/v1/stores")
 */
class StoreController extends AbstractController
{
    protected $recordPerPage = 30;

    /**
     * @Route("/", methods={"GET"})
     */
    public function listAction()
    {
        $myGiftTypes = GiftTypeModel::find([
            'status = :status:',
            'bind' => [
                'status' => (int) GiftTypeModel::STATUS_ENABLE
            ]
        ]);

        if (count($myGiftTypes) > 0) {
            return $this->createCollection(
                $myGiftTypes,
                new GiftTypeTransformer,
                'data'
            );
        } else {
            return $this->respondWithArray([], 'data');
        }
    }

    /**
     * @Route("/{typeid:[0-9]+}", methods={"GET"})
     */
    public function getAction($typeid = 0)
    {
        $sql = 'SELECT gt_id, g_name, g_required_point, COUNT(g_name) as quantity, g_cover FROM fly_gift '
            . 'WHERE gt_id = '. (int) $typeid
            . ' AND g_is_used = '. GiftModel::IS_NOT_USED
            . ' GROUP BY g_name, g_required_point';
        $raws = $this->getDI()->get('db')->fetchAll($sql);

        if (count($raws) > 0) {
            return $this->createCollection(
                $raws,
                new GiftStoreTransformer,
                'data'
            );
        } else {
            return $this->respondWithArray([], 'data');
        }
    }

    /**
     * @Route("/claim", methods={"POST"})
     */
    public function claimAction()
    {
        $formData = (array) $this->request->getJsonRawBody();
        $myFireBase = $this->firebase->getDatabase();
        $uid = (int) $this->getDI()->get('auth')->getUser()->id;

        $myGifts = GiftModel::find([
            'name = :name: AND isused = :isused:',
            'bind' => [
                'name' => (string) $formData['name'],
                'isused' => (int) GiftModel::IS_NOT_USED
            ]
        ]);

        if (count($myGifts) == 0) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }

        // get first gift
        $myReceiveGift = $myGifts[0];

        $myUser = UserModel::findFirstById($uid);
        if (!$myUser) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }
        $myProfile = $myUser->getProfile();

        // Compare current point of user with gift required point
        if ($myProfile->point < $myReceiveGift->requiredpoint) {
            throw new \Exception('Not enough point!!!');
        }

        $myUserGift = new UserGiftModel();
        $myUserGift->assign([
            'uid' => (int) $myUser->id,
            'gid' => (int) $myReceiveGift->id
        ]);

        if ($myUserGift->create()) {
            // Change gift status to used
            $myReceiveGift->isused = GiftModel::IS_USED;
            $myReceiveGift->dateused = time();
            if (!$myReceiveGift->update()) {
                throw new UserException(ErrorCode::DATA_UPDATE_FAIL);
            }

            // reduce point
            $myProfile->point = $myProfile->point - $myReceiveGift->requiredpoint;
            if (!$myProfile->update()) {
                throw new UserException(ErrorCode::DATA_UPDATE_FAIL);
            }

            try {
                $myFireBase->getReference('/users/' . $myUser->oauthuid . '/point')->set($myProfile->point);
            } catch (ApiException $e) {
                $response = $e->getResponse();
                throw new \Exception($response->getBody());
            }

            // Return a gift information
            return $this->createItem(
                $myReceiveGift,
                new GiftTransformer,
                'data'
            );
        } else {
            throw new \Exception('Can not receive a gift.');
        }
    }
}
