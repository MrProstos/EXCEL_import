<?php

namespace App\Models;

use PDO;


class Users extends \Core\Model
{

    public function registrationUser(string $username, string $email, string $passwd): bool
    {
        try {
            $db = static::getDB();

            $result = $db->prepare("insert into reg_user (username, email, passwd, confirm_email) values (?,?,?,'no')");
            $hash = md5($email . $passwd);
            $result->execute([$username, $email, $hash]);

            return true;

        } catch (\PDOException $e) {
            echo $e->getMessage(); // TODO Потом удалить

            return false;
        }
    }


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


    public function checkUser(string $email, string $passwd): bool
    {
        try {
            $db = static::getDB();

            $result = $db->prepare("SELECT * FROM reg_user WHERE passwd = ? and confirm_email = 'yes'"); // TODO добавить проверку активации почты
            $hash = md5($email . $passwd);
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

    public function confirmMail(string $hash): bool
    {
        try {
            $db = static::getDB();

            $result = $db->prepare("UPDATE reg_user SET confirm_email = 'yes' WHERE passwd = ?"); // TODO добавить проверку активации почты
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
