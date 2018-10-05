<?php
namespace Gift\Transformer;

use League\Fractal\TransformerAbstract;
use Phalcon\Di;
use Core\Helper\Utils as Helper;

class GiftStore extends TransformerAbstract
{
    protected $availableIncludes = [];
    protected $defaultIncludes = [];

    public function transform($giftinfo)
    {
        $di = Di::getDefault();

        $config = $di->get('config');
        $url = $di->get('url');

        if ($giftinfo['g_cover'] != '') {
            $coverPath = Helper::getFileUrl(
                $url->getBaseUri(),
                $config->default->gifts->directory,
                $giftinfo['g_cover']
            );
        } else {
            $coverPath = '';
        }


        return [
            'name' => (string) $giftinfo['g_name'],
            'cover' => (string) $coverPath,
            'quantity' => (string) $giftinfo['quantity'],
            'required' => (string) $giftinfo['g_required_point']
        ];
    }


}
