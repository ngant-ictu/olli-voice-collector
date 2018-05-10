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
    * @Column(type="integer", nullable=true, column="up_point")
    */
    public $point;

    /**
    * @Column(type="integer", nullable=true, column="up_record_times")
    */
    public $recordtimes;

    /**
    * @Column(type="integer", nullable=true, column="up_date_created")
    */
    public $datecreated;

    /**
    * @Column(type="integer", nullable=true, column="up_date_modified")
    */
    public $datemodified;

    const GENDER_MALE = 1;
    const GENDER_FEMALE = 3;


    public function validation()
    {
        $validator = new Validation();

        $validator->add('uid', new PresenceOf([
            'message' => 'message-uid-notempty'
        ]));

        return $this->validate($validator);
    }

    public function getGenderName(): string
    {
        $name = '';
        $lang = self::getStaticDi()->get('lang');

        switch ($this->gender) {
            case self::GENDER_MALE:
                $name = $lang->_('label-gender-male');
                break;
            case self::GENDER_FEMALE:
                $name = $lang->_('label-gender-female');
                break;
        }

        return $name;
    }

    public static function getGenderList()
    {
        $lang = self::getStaticDi()->get('lang');

        return $data = [
            [
                'label' => $lang->_('label-gender-male'),
                'value' => (int) self::GENDER_MALE
            ],
            [
                'label' => $lang->_('label-gender-female'),
                'value' => (int) self::GENDER_FEMALE
            ],
        ];
    }

    public function getGenderStyle(): string
    {
        $class = '';
        switch ($this->gender) {
            case self::GENDER_MALE:
                $class = '#67C23A';
                break;
            case self::GENDER_FEMALE:
                $class = '#E6A23C';
                break;
        }

        return $class;
    }

    public function getGenderIcon(): string
    {
        $icon = '';
        switch ($this->gender) {
            case self::GENDER_MALE:
                $icon = 'fa-mars';
                break;
            case self::GENDER_FEMALE:
                $icon = 'fa-venus';
                break;
        }

        return $icon;
    }
}
