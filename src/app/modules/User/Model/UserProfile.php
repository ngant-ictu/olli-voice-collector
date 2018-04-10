<?php
namespace User\Model;

use Core\Model\AbstractModel;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;

/**
 * @Source('fly_user_profile');
 * @Behavior('\Shirou\Behavior\Model\Timestampable');
 */
class UserProfile extends AbstractModel
{
    /**
    * @Column(type="integer", nullable=true, column="u_id")
    */
    public $uid;

    /**
    * @Primary
    * @Identity
    * @Column(type="integer", nullable=false, column="up_id")
    */
    public $id;

    /**
    * @Column(type="string", nullable=true, column="up_address")
    */
    public $address;

    /**
    * @Column(type="string", nullable=true, column="up_gender")
    */
    public $gender;

    /**
    * @Column(type="string", nullable=true, column="up_dob")
    */
    public $dob;

    /**
    * @Column(type="string", nullable=true, column="up_city")
    */
    public $city;

    /**
    * @Column(type="string", nullable=true, column="up_country")
    */
    public $country;

    /**
    * @Column(type="string", nullable=true, column="up_bio")
    */
    public $bio;

    /**
    * @Column(type="integer", nullable=true, column="up_voice_region")
    */
    public $voiceregion;

    /**
    * @Column(type="integer", nullable=true, column="up_voice_age_range")
    */
    public $voiceagerange;

    /**
    * @Column(type="integer", nullable=true, column="up_point")
    */
    public $point;

    /**
    * @Column(type="integer", nullable=true, column="up_date_created")
    */
    public $datecreated;

    /**
    * @Column(type="integer", nullable=true, column="up_date_modified")
    */
    public $datemodified;

    public function validation()
    {
        $validator = new Validation();

        $validator->add('uid', new PresenceOf([
            'message' => 'message-uid-notempty'
        ]));

        return $this->validate($validator);
    }
}
