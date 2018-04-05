<?php
namespace Record\Transformer;

use League\Fractal\TransformerAbstract;
use Moment\Moment;
use Record\Model\Voice as VoiceModel;

class Voice extends TransformerAbstract
{
    protected $availableIncludes = [];

    public function transform(VoiceModel $voice)
    {
        $humandatecreated = new Moment($voice->datecreated);

        return [
            'id' => (string) $voice->id,
            'sid' => (string) $voice->sid,
            'uid' => (string) $voice->uid,
            'filepath' => (string) $voice->getFilePath(),
            'status' =>  [
                'label' => (string) $voice->getStatusName(),
                'value' => (string) $voice->status,
                'style' => (string) $voice->getStatusStyle()
            ],
            'datecreated' => (string) $voice->datecreated,
            'humandatecreated' => (string) $humandatecreated->format('d M Y, H:i')
        ];
    }
}
