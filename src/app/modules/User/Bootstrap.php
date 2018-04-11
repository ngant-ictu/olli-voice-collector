<?php
namespace User;

use Phalcon\{
    DI,
    Events\Manager as PhEventsManager
};
use Shirou\Bootstrap as ShBootstrap;
use User\Plugin\Account\Email as UserEmailAccount;
use User\Plugin\Account\Phone as UserPhoneAccount;
use User\Session\JWT;
use User\Constants\AccountType;
use User\Session\Firebase\JWT\JWT as FirebaseJWT;
use User\Plugin\AuthManager as UserAuthManager;
use Kreait\Firebase\Factory as FirebaseFactory;
use Kreait\Firebase\ServiceAccount as FirebaseServiceAccount;

class Bootstrap extends ShBootstrap
{
    protected $_moduleName = 'User';

    public function __construct(DI $di, PhEventsManager $em)
    {
        parent::__construct($di, $em);

        $di->set('auth', function () use ($di) {
            $sessionManager = new JWT(new FirebaseJWT());
            $authManager = new UserAuthManager($sessionManager);

            // 1. Instantiate Account Type
            $authEmail = new UserEmailAccount(AccountType::EMAIL);
            $authPhone = new UserPhoneAccount(AccountType::PHONE);

            $authManager->setGenSalt(getenv('AUTH_SALT'));

            return $authManager
                ->addAccount(AccountType::EMAIL, $authEmail)
                ->addAccount(AccountType::PHONE, $authPhone)
                ->setExpireTime(getenv('AUTH_EXPIRE'));
        }, true);

        $di->set('firebase', function() use ($di) {
            $serviceAccount = FirebaseServiceAccount::fromJsonFile(ROOT_PATH . '/olli-event-app-firebase-adminsdk-g2i53-e7d5cf80da.json');
            $firebase = (new FirebaseFactory)
                ->withServiceAccount($serviceAccount)
                ->create();

            return $firebase;
        }, true);

        $em->attach('init', $this);
    }

    public function afterEngine()
    {
        $di = $this->getDI();

        $this->getEventsManager()->attach('dispatch', $di->get('user')->authentication());
        $this->getEventsManager()->attach('dispatch', $di->get('user')->authorization());
        $this->getEventsManager()->attach('dispatch', $di->get('user')->tracking());
    }
}
