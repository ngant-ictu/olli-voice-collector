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
    * @Column(type="integer", nullable=true, column="gt_date_created")
    */
    public $datecreated;

    /**
    * @Column(type="integer", nullable=true, column="gt_date_modified")
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
