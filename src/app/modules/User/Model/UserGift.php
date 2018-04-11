<?php
namespace User\Model;

use Core\Model\AbstractModel;

/**
 * @Source('fly_user_gift');
 * @Behavior('\Shirou\Behavior\Model\Timestampable');
 */
class UserGift extends AbstractModel
{
    /**
    * @Column(type="integer", nullable=true, column="u_id")
    */
    public $uid;

    /**
    * @Column(type="integer", nullable=true, column="g_id")
    */
    public $gid;

    /**
    * @Primary
    * @Identity
    * @Column(type="integer", nullable=false, column="ug_id")
    */
    public $id;

    /**
    * @Column(type="integer", nullable=true, column="ug_date_created")
    */
    public $datecreated;

    /**
    * @Column(type="integer", nullable=true, column="ug_date_modified")
    */
    public $datemodified;
}
