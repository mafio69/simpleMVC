<?php

namespace App\Config;

use DateTimeZone;
use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class InjectContainer
{
    public function injectMonolog(): Logger
    {
        $monolog = new Logger('app');

        try {
            $monolog->pushHandler(new StreamHandler(dirname(__DIR__) . '/Logs/DB/dbLog.log'));
        } catch (Exception $e) {
            if (getenv('APP_ENV') === 'dev') {
                echo $e->getMessage();
            } else {
                echo "Sorry, app fail";
            }

            fileLog('MONOLOG: ' . date('Y-m-d H-i-s') . $e->getMessage() . ' : ' . $e->getTraceAsString());
        }

        $monolog->setTimezone(new DateTimeZone('Europe/Warsaw'));

        return $monolog;
    }
}