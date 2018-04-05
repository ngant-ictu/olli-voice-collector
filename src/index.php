<?php
define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', dirname(__FILE__));

require_once ROOT_PATH . '/vendor/autoload.php';

set_error_handler(
    function ($errorCode, $errorMessage, $errorFile, $errorLine) {
        throw new \ErrorException($errorMessage, $errorCode, 1, $errorFile, $errorLine);
    }
);

switch (php_sapi_name()) {
    case 'cli':
        $app = new Shirou\Cli();

        set_exception_handler(
            function ($e) use ($app) {
                print($e);
                return true;
            }
        );
        break;

    default:
        $app = new Shirou\App();

        set_exception_handler(
            function ($e) use ($app) {
                $app->getDI()->get('response')->sendErrorMessage($e);
                return true;
            }
        );
        break;
}

$app->init();
$app->run();
