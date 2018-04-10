<?php
namespace Gift\Transformer;

use League\Fractal\TransformerAbstract;
use Moment\Moment;
use Gift\Model\GiftStock as GiftStockModel;
use Gift\Transformer\GiftAttribute as GiftAttributeTransformer;

class GiftStock extends TransformerAbstract
{
    protected $availableIncludes = [];
    protected $defaultIncludes = [
        'attribute'
    ];

    public function transform(GiftStockModel $giftstock)
    {
        $humandatecreated = new Moment($giftstock->datecreated);

        return [
            'id' => (string) $giftstock->id,
            'gid' => (string) $giftstock->gid,
            'gaid' => (string) $giftstock->gaid,
            'value' => (string) $giftstock->value,
            'datecreated' => (string) $giftstock->datecreated,
            'humandatecreated' => (string) $humandatecreated->format('d M Y, H:i')
        ];
    }

    public function includeAttribute(GiftStockModel $giftstock)
    {
        $giftAttribute = $giftstock->getAttribute();

        return $this->item($giftAttribute, new GiftAttributeTransformer);
    }
}
