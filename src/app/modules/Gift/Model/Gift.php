<?php
namespace Gift\Model;

use Core\Model\AbstractModel;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Core\Helper\Utils as Helper;
use Shirou\Behavior\Model\Fileable;
use Gift\Model\GiftStock as GiftStockModel;

/**
 * @Source('fly_gift');
 * @Behavior('\Shirou\Behavior\Model\Timestampable');
 * @HasMany('id', '\Gift\Model\GiftStock', 'gid', {'alias': 'stocks'})
 */
class Gift extends AbstractModel
{
    /**
    * @Column(type="integer", nullable=true, column="gt_id")
    */
    public $gtid;

    /**
    * @Primary
    * @Identity
    * @Column(type="integer", nullable=false, column="g_id")
    */
    public $id;

    /**
    * @Column(type="string", nullable=true, column="g_name")
    */
    public $name;

    /**
    * @Column(type="string", nullable=true, column="g_description")
    */
    public $description;

    /**
    * @Column(type="integer", nullable=true, column="g_display_order")
    */
    public $displayorder;

    /**
    * @Column(type="string", nullable=true, column="g_cover")
    */
    public $cover;

    /**
    * @Column(type="integer", nullable=true, column="g_is_used")
    */
    public $isused;

    /**
    * @Column(type="integer", nullable=true, column="g_required_point")
    */
    public $requiredpoint;

    /**
    * @Column(type="integer", nullable=true, column="g_date_created")
    */
    public $datecreated;

    /**
    * @Column(type="integer", nullable=true, column="g_date_modified")
    */
    public $datemodified;

    /**
    * @Column(type="integer", nullable=true, column="g_date_used")
    */
    public $dateused;

    const IS_USED = 1;
    const IS_NOT_USED = 3;

    public function validation()
    {
        $validator = new Validation();

        $validator->add('name', new PresenceOf([
            'message' => 'message-name-notempty'
        ]));

        $validator->add('gtid', new PresenceOf([
            'message' => 'message-gtid-notempty'
        ]));

        $validator->add('isused', new PresenceOf([
            'message' => 'message-isused-notempty'
        ]));

        return $this->validate($validator);
    }

    public function initialize()
    {
        $config = $this->getDI()->get('config');

        if (!$this->getDI()->get('app')->isConsole()) {
            $configBehavior = [
                'field' => 'cover',
                'uploadPath' => $config->default->gifts->directory,
                'allowedFormats' => $config->default->gifts->mimes->toArray(),
                'allowedMaximumSize' => $config->default->gifts->maxsize,
                'allowedMinimumSize' => $config->default->gifts->minsize,
                'isOverwrite' => $config->default->gifts->isoverwrite
            ];

            $this->addBehavior(new Fileable([
                'beforeCreate' => $configBehavior,
                'beforeDelete' => $configBehavior,
                'beforeUpdate' => $configBehavior
            ]));
        }
    }

    public function getUsedName(): string
    {
        $name = '';
        $lang = self::getStaticDi()->get('lang');

        switch ($this->isused) {
            case self::IS_USED:
                $name = $lang->_('label-is-used');
                break;
            case self::IS_NOT_USED:
                $name = $lang->_('label-is-not-used');
                break;
        }

        return $name;
    }

    public static function getUsedList()
    {
        $lang = self::getStaticDi()->get('lang');

        return $data = [
            [
                'label' => $lang->_('label-is-used'),
                'value' => (string) self::IS_USED
            ],
            [
                'label' => $lang->_('label-is-not-used'),
                'value' => (string) self::IS_NOT_USED
            ],
        ];
    }

    public function getUsedStyle(): string
    {
        $name = '';

        switch ($this->isused) {
            case self::IS_USED:
                $name = 'danger';
                break;
            case self::IS_NOT_USED:
                $name = 'success';
                break;
        }

        return $name;
    }

    public function getCoverPath(): string
    {
        $config = $this->getDI()->get('config');
        $url = $this->getDI()->get('url');

        if ($this->cover != '') {
            return Helper::getFileUrl(
                $url->getBaseUri(),
                $config->default->gifts->directory,
                $this->cover
            );
        } else {
            return '';
        }
    }

    public function afterDelete()
    {
        return $this->getStocks()->delete();
    }
}
