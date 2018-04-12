<?php
namespace Record\Transformer;

use League\Fractal\TransformerAbstract;
use Moment\Moment;
use Record\Model\Voice as VoiceModel;

class VoiceItem extends TransformerAbstract
{
    protected $availableIncludes = [];
    protected $defaultIncludes = [];

    public function transform(VoiceModel $voiceitem)
    {
        $humandatecreated = new Moment($voiceitem->datecreated);

        return [
            'id' => (string) $voiceitem->id,
            'filepath' => (string) $voiceitem->getFilePath(),
            'validatedby' => (string) $voiceitem->validatedby,
            'status' =>  [
                'label' => (string) $voiceitem->getStatusName(),
                'value' => (string) $voiceitem->status,
                'style' => (string) $voiceitem->getStatusStyle()
            ],
            'datecreated' => (string) $voiceitem->datecreated,
            'humandatecreated' => (string) $humandatecreated->format('d M Y, H:i')
        ];
    }
}
