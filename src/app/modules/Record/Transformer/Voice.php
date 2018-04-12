<?php
namespace Record\Transformer;

use League\Fractal\TransformerAbstract;
use Moment\Moment;
use Record\Model\Voice as VoiceModel;
use Record\Transformer\VoiceItem as VoiceItemTransformer;
use User\Model\User as UserModel;
use User\Transformer\User as UserTransformer;

class Voice extends TransformerAbstract
{
    protected $availableIncludes = [];
    protected $defaultIncludes = [
        'items',
        'user'
    ];

    public function transform($voice)
    {
        return [
            'uid' => (string) $voice->uid
        ];
    }

    public function includeItems($voice)
    {
        $myVoices = VoiceModel::find([
            'uid = :uid:',
            'bind' => [
                'uid' => (int) $voice->uid
            ]
        ]);

        return $this->collection($myVoices, new VoiceItemTransformer);
    }

    public function includeUser($voice)
    {
        $myUser = UserModel::findFirstById($voice->uid);

        return $this->item($myUser, new UserTransformer);
    }
}
