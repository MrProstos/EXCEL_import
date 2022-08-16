<?php

namespace App\Models;

use PDO;


/**
 * Methods for working with user
 */
class Users extends \Core\Model
{

    /**
     * User Registration
     * @param string $username
     * @param string $email
     * @param string $passwd
     * @return bool
     */
    public function registrationUser(string $username, string $email, string $passwd): bool
    {
        try {
            $db = static::getDB();

            $result = $db->prepare("insert into reg_user (username, email, passwd, confirm_email) values (?,?,MD5(CONCAT(?,?)),'no')");
            $result->execute([$username, $email, $email, $passwd]);

            return true;
        } catch (\PDOException $e) {
            echo $e->getMessage(); // TODO Потом удалить

            return false;
        }
    }


    /**
     * Checking the existence of the user
     * @param string $hash
     * @return bool
     */
    public function isUser(string $hash): bool
    {
        $db = static::getDB();

        $result = $db->prepare("SELECT * FROM reg_user WHERE passwd = ?");
        $result->execute([$hash]);

        if ($result->rowCount() != 1) {
            return false;
        }

        return true;
    }


    /**
     * Verification of successful user registration
     * @param string $email
     * @param string $passwd
     * @return bool
     */
    public function checkUser(string $email, string $passwd): bool
    {
        try {
            $db = static::getDB();

            $result = $db->prepare("SELECT * FROM reg_user WHERE passwd = MD5(CONCAT(?,?)) and confirm_email = 'yes'");
            $result->execute([$email, $passwd]);

            if ($result->rowCount() != 1) {
                return false;
            }

            return true;
        } catch (\PDOException $e) {
            echo $e->getMessage();  // TODO Потом удалить
            return false;
        }
    }

    /**
     * Email сonfirmation
     * @param string $hash
     * @return bool
     */
    public function confirmMail(string $hash): bool
    {
        try {
            $db = static::getDB();

            $result = $db->prepare("UPDATE reg_user SET confirm_email = 'yes' WHERE passwd = ?");
            $result->execute([$hash]);

            if ($result->rowCount() != 1) {
                return false;
            }

            return true;
        } catch (\PDOException $e) {
            echo $e->getMessage();  // TODO Потом удалить

            return false;
        }
    }
}
