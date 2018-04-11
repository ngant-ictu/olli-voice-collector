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
        $sql = 'SELECT gt_id, g_name, g_required_point, COUNT(g_name) as quantity FROM fly_gift '
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
        $myUser = $this->getDI()->get('auth')->getUser();

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

        // Compare current point of user with gift required point
        $myUserPoint = (int) $myFireBase->getReference('/users/' . $myUser->oauthuid . '/point')->getValue();
        $myUserRecordTimes = (int) $myFireBase->getReference('/users/' . $myUser->oauthuid . '/record_times')->getValue();

        if ($myUserPoint < $myReceiveGift->requiredpoint) {
            throw new \Exception('Not enough point!!!');
        }



        die;

    }
}