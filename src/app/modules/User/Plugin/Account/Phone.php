<?php
namespace User\Plugin\Account;

use Phalcon\Mvc\User\Plugin as PhPlugin;
use Shirou\Constants\ErrorCode;
use Shirou\UserException;
use User\Plugin\AccountInterface;
use User\Model\User as UserModel;
use User\Constants\ErrorCode as UserErrorCode;

class Phone extends PhPlugin implements AccountInterface
{
    public function __construct($name)
    {
        $this->name = $name;
    }

    public function login($phone = null, $password = null)
    {
        preg_match('/^([0\+]{1})+/', $phone, $matches);

        if (empty($matches)) {
            throw new UserException(UserErrorCode::AUTH_BADLOGIN);
        }

        if ($matches[0] == '0') {
            $phone = '+84' . substr($phone, 1);
        }

        $phoneAccount = UserModel::findFirst([
            'mobilenumber = :mobilenumber: AND status = :status: AND isverified = :isverified:',
            'bind' => [
                'mobilenumber' => (string) $phone,
                'status' => (int) UserModel::STATUS_ENABLE,
                'isverified' => (int) UserModel::IS_VERIFIED
            ]
        ]);

        // Check if password is valid
        if (!$phoneAccount || !$phoneAccount->validatePassword($password)) {
            return false;
        }

        return $phoneAccount;
    }
}
