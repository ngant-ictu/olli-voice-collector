<?php
namespace Gift\Model;

use Core\Model\AbstractModel;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Uniqueness;
use Core\Helper\Utils as Helper;

/**
 * @Source('fly_gift_type');
 * @Behavior('\Shirou\Behavior\Model\Timestampable');
 * @HasMany('id', '\Gift\Model\GiftAttribute', 'gtid', {'alias': 'attributes'})
 * @HasMany('id', '\Gift\Model\Gift', 'gtid', {'alias': 'gifts'})
 */
class GiftType extends AbstractModel
{
    /**
    * @Primary
    * @Identity
    * @Column(type="integer", nullable=false, column="gt_id")
    */
    public $id;

    /**
    * @Column(type="string", nullable=true, column="gt_name")
    */
    public $name;

    /**
    * @Column(type="integer", nullable=true, column="gt_status")
    */
    public $status;

    /**
    * @Column(type="integer", nullable=true, column="gt_date_created")
    */
    public $datecreated;

    /**
    * @Column(type="integer", nullable=true, column="gt_date_modified")
    */
    public $datemodified;

    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 3;


    // /**
    //  * Form field validation
    //  */
    // public function validation()
    // {
    //     $validator = new Validation();
    //
    //     $validator->add('groupid', new PresenceOf([
    //         'message' => 'message-groupid-notempty'
    //     ]));
    //
    //     $validator->add('status', new PresenceOf([
    //         'message' => 'message-status-notempty'
    //     ]));
    //
    //     $validator->add('email', new Uniqueness([
    //         'message' => 'message-email-unique'
    //     ]));
    //
    //     return $this->validate($validator);
    // }

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
        $this->getAttributes()->delete();
        $this->getGifts()->delete();
    }
}
