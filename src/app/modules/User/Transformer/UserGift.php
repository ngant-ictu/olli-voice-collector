<?php
namespace User\Transformer;

use League\Fractal\TransformerAbstract;
use Gift\Model\Gift as GiftModel;
use User\Model\UserGift as UserGiftModel;
use Gift\Transformer\Gift as GiftTransformer;
use Moment\Moment;

class UserGift extends TransformerAbstract
{
    protected $defaultIncludes = [
        'gift'
    ];

    public function transform(UserGiftModel $usergift)
    {
        $humandatecreated = new Moment($usergift->datecreated);

        return [
            'id' => (int) $usergift->id,
            'datecreated' => (int) $usergift->datecreated,
            'humandatecreated' => (string) $humandatecreated->format('d M Y, H:i')
        ];
    }

    public function includeGift(UserGiftModel $usergift)
    {
        $gift = $usergift->getGift();

        if ($gift) {
            return $this->item($gift, new GiftTransformer);
        } else {
            return $this->item(new GiftModel, new GiftTransformer);
        }
    }
}
