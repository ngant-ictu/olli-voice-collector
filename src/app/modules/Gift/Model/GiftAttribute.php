<?php
namespace Gift\Model;

use Core\Model\AbstractModel;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Uniqueness;
use Core\Helper\Utils as Helper;

/**
 * @Source('fly_gift_attribute');
 * @Behavior('\Shirou\Behavior\Model\Timestampable');
 * @HasOne('gtid', '\Gift\Model\GiftType', 'id', {'alias': 'type'})
 */
class GiftAttribute extends AbstractModel
{
    /**
    * @Column(type="integer", nullable=true, column="gt_id")
    */
    public $gtid;

    /**
    * @Primary
    * @Identity
    * @Column(type="integer", nullable=false, column="ga_id")
    */
    public $id;

    /**
    * @Column(type="string", nullable=true, column="ga_name")
    */
    public $name;

    /**
    * @Column(type="integer", nullable=true, column="ga_display_order")
    */
    public $displayorder;

    /**
    * @Column(type="string", nullable=true, column="ga_unit")
    */
    public $unit;

    /**
    * @Column(type="integer", nullable=true, column="ga_date_created")
    */
    public $datecreated;

    /**
    * @Column(type="integer", nullable=true, column="ga_date_modified")
    */
    public $datemodified;


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

}
