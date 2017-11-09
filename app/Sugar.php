<?php

namespace App;
use Asakusuma\SugarWrapper\Rest;

/**
 * Sugar Connection an API
 *
 * @author davidcallizaya
 */
class Sugar
{
    private static $connection=null;

    /**
     *
     * @return \Asakusuma\SugarWrapper\Rest
     */
    public static function getConnection() {
        if (empty(self::$connection)) {
            self::$connection = new Rest;
            self::$connection->setUrl(env('SUGAR_URL').'/service/v2/rest.php');
            self::$connection->setUsername(env('SUGAR_USER'));
            self::$connection->setPassword(env('SUGAR_PASSWORD'));
            self::$connection->connect();
        }
        return self::$connection;
    }
}
