<?php
namespace Gift\Transformer;

use League\Fractal\TransformerAbstract;

class GiftStore extends TransformerAbstract
{
    protected $availableIncludes = [];
    protected $defaultIncludes = [];

    public function transform($giftinfo)
    {
        return [
            'name' => (string) $giftinfo['g_name'],
            'quantity' => (string) $giftinfo['quantity'],
            'required' => (string) $giftinfo['g_required_point']
        ];
    }


}
