<?php
namespace Gift\Controller\V1;

use Shirou\Constants\ErrorCode;
use Shirou\UserException;
use Core\Controller\AbstractController;
use Core\Helper\Utils as Helper;
use Gift\Model\GiftType as GiftTypeModel;
use Gift\Transformer\GiftType as GiftTypeTransformer;

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

}
