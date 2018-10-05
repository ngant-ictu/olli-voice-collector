<?php
namespace Gift\Transformer;

use League\Fractal\TransformerAbstract;
use Moment\Moment;
use Gift\Model\GiftAttribute as GiftAttributeModel;

class GiftAttribute extends TransformerAbstract
{
    protected $availableIncludes = [];

    public function transform(GiftAttributeModel $giftattribute)
    {
        $humandatecreated = new Moment($giftattribute->datecreated);

        return [
            'id' => (string) $giftattribute->id,
            'name' => (string) $giftattribute->name,
            'unit' => (string) $giftattribute->unit,
            'displayorder' => (string) $giftattribute->displayorder,
            'datecreated' => (string) $giftattribute->datecreated,
            'humandatecreated' => (string) $humandatecreated->format('d-m-Y, H:i')
        ];
    }
}
