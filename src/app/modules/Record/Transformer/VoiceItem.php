<?php
namespace Record\Transformer;

use League\Fractal\TransformerAbstract;
use Moment\Moment;
use Record\Model\Voice as VoiceModel;
use User\Model\User as UserModel;
use Record\Transformer\VoiceScript as VoiceScriptTransformer;

class VoiceItem extends TransformerAbstract
{
    protected $defaultIncludes = [];
    protected $availableIncludes = [
        'voicescript'
    ];

    public function transform(VoiceModel $voiceitem)
    {
        $humandatecreated = new Moment($voiceitem->datecreated);

        if ($voiceitem->validatedby > 0) {
            $myUserValidated = UserModel::findFirstById($voiceitem->validatedby);
            $myUserValidated->avatar = $myUserValidated->getAvatarJson();
            $myUserValidated = $myUserValidated->toArray();
        } else {
            $myUserValidated = 0;
        }

        return [
            'id' => (string) $voiceitem->id,
            'vsid' => (string) $voiceitem->vsid,
            'filepath' => (string) $voiceitem->getFilePath(),
            'validatedby' => $myUserValidated,
            'status' =>  [
                'label' => (string) $voiceitem->getStatusName(),
                'value' => (string) $voiceitem->status,
                'style' => (string) $voiceitem->getStatusStyle()
            ],
            'datecreated' => (string) $voiceitem->datecreated,
            'humandatecreated' => (string) $humandatecreated->format('d M Y, H:i')
        ];
    }

    public function includeVoicescript(VoiceModel $voiceitem)
    {
        return $this->item($voiceitem->getVoicescript(), new VoiceScriptTransformer);
    }
}
