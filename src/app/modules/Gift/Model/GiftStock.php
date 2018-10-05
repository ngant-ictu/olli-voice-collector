<?php
namespace Gift\Model;

use Core\Model\AbstractModel;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Uniqueness;
use Core\Helper\Utils as Helper;

/**
 * @Source('fly_gift_stock');
 * @Behavior('\Shirou\Behavior\Model\Timestampable');
 * @HasOne('gaid', '\Gift\Model\GiftAttribute', 'id', {'alias': 'attribute'})
 */
class GiftStock extends AbstractModel
{
    /**
    * @Column(type="integer", nullable=true, column="g_id")
    */
    public $gid;

    /**
    * @Column(type="integer", nullable=true, column="ga_id")
    */
    public $gaid;

    /**
    * @Primary
    * @Identity
    * @Column(type="integer", nullable=false, column="gs_id")
    */
    public $id;

    /**
    * @Column(type="string", nullable=true, column="gs_value")
    */
    public $value;

    /**
    * @Column(type="integer", nullable=true, column="gs_date_created")
    */
    public $datecreated;

    /**
    * @Column(type="integer", nullable=true, column="gs_date_modified")
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
