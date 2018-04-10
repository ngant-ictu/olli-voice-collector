<?php
namespace Record\Transformer;

use League\Fractal\TransformerAbstract;
use Moment\Moment;
use Record\Model\VoiceScript as VoiceScriptModel;

class VoiceScript extends TransformerAbstract
{
    protected $availableIncludes = [];

    public function transform(VoiceScriptModel $voicescript)
    {
        $humandatecreated = new Moment($voicescript->datecreated);

        return [
            'id' => (string) $voicescript->id,
            'command' => (string) $voicescript->command,
            'text' => (string) $voicescript->text,
            'status' =>  [
                'label' => (string) $voicescript->getStatusName(),
                'value' => (string) $voicescript->status,
                'style' => (string) $voicescript->getStatusStyle()
            ],
            'point' => (string) $voicescript->point,
            'datecreated' => (string) $voicescript->datecreated,
            'humandatecreated' => (string) $humandatecreated->format('d M Y, H:i')
        ];
    }
}
