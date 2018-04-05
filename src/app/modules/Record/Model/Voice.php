<?php
namespace Record\Model;

use Core\Model\AbstractModel;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Uniqueness;
use Shirou\Behavior\Model\Fileable;
use Core\Helper\Utils as Helper;

/**
 * @Source('fly_voice');
 * @Behavior('\Shirou\Behavior\Model\Timestampable');
 */
class Voice extends AbstractModel
{
    /**
    * @Column(type="integer", nullable=true, column="s_id")
    */
    public $sid;

    /**
    * @Column(type="integer", nullable=true, column="u_id")
    */
    public $uid;

    /**
    * @Primary
    * @Identity
    * @Column(type="integer", nullable=false, column="v_id")
    */
    public $id;

    /**
    * @Column(type="integer", nullable=true, column="v_file_path")
    */
    public $filepath;

    /**
    * @Column(type="integer", nullable=true, column="v_status")
    */
    public $status;

    /**
    * @Column(type="integer", nullable=true, column="v_validated_by")
    */
    public $validatedby;

    /**
    * @Column(type="integer", nullable=true, column="v_date_created")
    */
    public $datecreated;

    /**
    * @Column(type="integer", nullable=true, column="v_date_modified")
    */
    public $datemodified;

    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 3;
    const STATUS_PENDING = 5;

    /**
     * Initialize model
     */
    public function initialize()
    {
        $config = $this->getDI()->get('config');

        if (!$this->getDI()->get('app')->isConsole()) {
            $configBehavior = [
                'field' => 'filepath',
                'uploadPath' => $config->default->voices->directory,
                'allowedFormats' => $config->default->voices->mimes->toArray(),
                'allowedMaximumSize' => $config->default->voices->maxsize,
                'allowedMinimumSize' => $config->default->voices->minsize,
                'isOverwrite' => $config->default->voices->isoverwrite
            ];

            $this->addBehavior(new Fileable([
                'beforeCreate' => $configBehavior,
                'beforeDelete' => $configBehavior,
                'beforeUpdate' => $configBehavior
            ]));
        }
    }

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
            case self::STATUS_APPROVED:
                $name = $lang->_('label-status-approved');
                break;
            case self::STATUS_REJECTED:
                $name = $lang->_('label-status-rejected');
                break;
            case self::STATUS_PENDING:
                $name = $lang->_('label-status-pending');
                break;
        }

        return $name;
    }

    public static function getStatusList()
    {
        $lang = self::getStaticDi()->get('lang');

        return $data = [
            [
                'label' => $lang->_('label-status-approved'),
                'value' => (string) self::STATUS_APPROVED
            ],
            [
                'label' => $lang->_('label-status-rejected'),
                'value' => (string) self::STATUS_REJECTED
            ],
            [
                'label' => $lang->_('label-status-pending'),
                'value' => (string) self::STATUS_PENDING
            ],
        ];
    }

    public function getStatusStyle(): string
    {
        $class = '';
        switch ($this->status) {
            case self::STATUS_APPROVED:
                $class = 'primary';
                break;
            case self::STATUS_REJECTED:
                $class = 'danger';
                break;
            case self::STATUS_PENDING:
                $class = 'warning';
                break;
        }

        return $class;
    }

    // return file path response support api
    public function getFilePath(): string
    {
        $config = $this->getDI()->get('config');
        $url = $this->getDI()->get('url');

        if ($this->filepath != '') {
            return Helper::getFileUrl(
                $url->getBaseUri(),
                $config->default->voices->directory,
                $this->filepath
            );
        } else {
            return '';
        }
    }
}
