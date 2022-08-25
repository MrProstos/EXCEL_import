<?php

namespace Core;

use PDO;
use App\Config;

/**
 * Base model
 *
 * PHP version 7.0
 */
abstract class Model
{

    static PDO $db;

    /**
     * Get the PDO database connection
     *
     * @return PDO
     */
    protected function getDB(): PDO
    {
        if (!isset(self::$db)) {
            $dsn = 'mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME . ';charset=utf8';
            self::$db = new PDO($dsn, Config::DB_USER, Config::DB_PASSWORD);

            // Throw an Exception when an error occurs
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$db;
    }
}
