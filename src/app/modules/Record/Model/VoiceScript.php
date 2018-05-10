<?php
namespace Record\Model;

use Core\Model\AbstractModel;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Uniqueness;

/**
 * @Source('fly_voice_script');
 * @Behavior('\Shirou\Behavior\Model\Timestampable');
 * @HasMany('id', '\Record\Model\Voice', 'vsid', {'alias': 'voices'})
 */
class VoiceScript extends AbstractModel
{
    /**
    * @Primary
    * @Identity
    * @Column(type="integer", nullable=false, column="vs_id")
    */
    public $id;

    /**
    * @Column(type="integer", nullable=true, column="vs_command")
    */
    public $command;

    /**
    * @Column(type="integer", nullable=true, column="vs_text")
    */
    public $text;

    /**
    * @Column(type="integer", nullable=true, column="vs_status")
    */
    public $status;

    /**
    * @Column(type="integer", nullable=true, column="vs_receive_point")
    */
    public $receivepoint;

    /**
    * @Column(type="integer", nullable=true, column="vs_date_created")
    */
    public $datecreated;

    /**
    * @Column(type="integer", nullable=true, column="vs_date_modified")
    */
    public $datemodified;

    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 3;

    public function getStatusName(): string
    {
        $name = '';
        $lang = self::getStaticDi()->get('lang');

        switch ($this->status) {
            case self::STATUS_ENABLE:
                $name = $lang->_('label-status-enable');
                break;
            case self::STATUS_DISABLE:
                $name = $lang->_('label-status-disable');
                break;
        }

        return $name;
    }

    public static function getStatusList()
    {
        $lang = self::getStaticDi()->get('lang');

        return $data = [
            [
                'label' => $lang->_('label-status-enable'),
                'value' => (string) self::STATUS_ENABLE
            ],
            [
                'label' => $lang->_('label-status-disable'),
                'value' => (string) self::STATUS_DISABLE
            ],
        ];
    }

    public function getStatusStyle(): string
    {
        $class = '';
        switch ($this->status) {
            case self::STATUS_ENABLE:
                $class = 'primary';
                break;
            case self::STATUS_DISABLE:
                $class = 'danger';
                break;
        }

        return $class;
    }

    public function afterDelete()
    {
        $this->getVoices()->delete();
    }
}
