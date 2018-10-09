<?php
namespace Gift\Controller\V1;

use Shirou\Constants\ErrorCode;
use Shirou\UserException;
use Core\Controller\AbstractController;
use Core\Helper\Utils as Helper;
use Gift\Model\GiftType as GiftTypeModel;
use Gift\Model\GiftAttribute as GiftAttributeModel;
use Gift\Transformer\GiftType as GiftTypeTransformer;
use Gift\Transformer\GiftAttribute as GiftAttributeTransformer;

/**
 * @RoutePrefix("/v1/gifttypes")
 */
class TypeController extends AbstractController
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
        $status = (int) $this->request->getQuery('status', null, 0);

        $formData['columns'] = '*';
        $formData['conditions'] = [
            'keyword' => $keyword,
            'searchKeywordIn' => $searchKeywordInData,
            'filterBy' => [
                'status' => $status
            ]
        ];
        $formData['orderBy'] = $orderBy;
        $formData['orderType'] = $orderType;

        $myGifttypes = GiftTypeModel::paginate($formData, $this->recordPerPage, $page);

        if ($myGifttypes->total_pages > 0) {
            if ($page == $myGifttypes->total_pages) {
                $hasMore = false;
            }

            return $this->createCollection(
                $myGifttypes->items,
                new GiftTypeTransformer,
                'data',
                [
                    'meta' => [
                        'recordPerPage' => $this->recordPerPage,
                        'hasMore' => $hasMore,
                        'totalItems' => $myGifttypes->total_items,
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

        $myGiftType = new GiftTypeModel();
        $myGiftType->name = $formData['name'];
        $myGiftType->status = (int) GiftTypeModel::STATUS_DISABLE;

        if (!$myGiftType->create()) {
            throw new UserException(ErrorCode::DATA_CREATE_FAIL);
        }

        if (count($formData['attrs']) > 0) {
            foreach ($formData['attrs'] as $attr) {
                $myGiftAttribute = new GiftAttributeModel();
                $myGiftAttribute->assign([
                    'gtid' => (int) $myGiftType->id,
                    'name' => (string) $attr->name,
                    'unit' => (string) $attr->unit,
                    'displayorder' => (int) $attr->order,
                    'type' => (int) GiftAttributeModel::TYPE_INPUT
                ]);

                if (!$myGiftAttribute->create()) {
                    throw new UserException(ErrorCode::DATA_CREATE_FAIL);
                }
            }
        }

        return $this->createItem(
            $myGiftType,
            new GiftTypeTransformer,
            'data'
        );
    }

    /**
     * @Route("/{id:[0-9]+}/attrs", methods={"GET"})
     */
    public function getattrsAction(int $id = 0)
    {
        $myGiftType = GiftTypeModel::findFirst([
            'id = :id:',
            'bind' => [
                'id' => (int) $id
            ]
        ]);

        $myGiftAttributes = $myGiftType->getAttributes([
            'order' => 'displayorder ASC'
        ]);

        if (count($myGiftAttributes) > 0) {
            return $this->createCollection(
                $myGiftAttributes,
                new GiftAttributeTransformer,
                'data'
            );
        } else {
            return $this->respondWithArray([], 'data');
        }
    }

    /**
     * Update single field
     *
     * @Route("/{id:[0-9]+}/field", methods={"PUT"})
     */
    public function updatefieldAction(int $id = 0)
    {
        $formData = (array) $this->request->getJsonRawBody();

        $myGiftType = GiftTypeModel::findFirst([
            'id = :id:',
            'bind' => ['id' => (int) $id]
        ]);

        if (!$myGiftType) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }

        $myGiftType->{$formData['field']} = $formData['value'];

        if (!$myGiftType->update()) {
            throw new UserException(ErrorCode::DATA_UPDATE_FAIL);
        }

        return $this->createItem(
            $myGiftType,
            new GiftTypeTransformer,
            'data'
        );
    }

    /**
     * Delete
     *
     * @Route("/{id:[0-9]+}", methods={"DELETE"})
     */
    public function deleteAction(int $id = 0)
    {
        $formData = (array) $this->request->getJsonRawBody();

        $myGiftType = GiftTypeModel::findFirst([
            'id = :id:',
            'bind' => [
                'id' => (int) $id
            ]
        ]);

        if (!$myGiftType) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }

        if (!$myGiftType->delete()) {
            throw new UserException(ErrorCode::DATA_DELETE_FAIL);
        }

        return $this->createItem(
            $myGiftType,
            new GiftTypeTransformer,
            'data'
        );
    }
}
