<?php

namespace App\Models;

use PDO;


class Users extends \Core\Model
{

    static function HashPassword(string $passwd): string
    {
        $salt = "kkklllttt";
        return md5($passwd . $salt);
    }

    public function Registration(string $username, string $email, string $passwd): bool
    {
        try {
            $db = static::getDB();

            $result = $db->prepare("insert into reg_user (username, email, passwd) values (?,?,?)");
            $hash_passwd = self::HashPassword($passwd);
            $result->execute([$username, $email, $hash_passwd]);

            return true;

        } catch (\PDOException $e) {
            echo $e->getMessage(); // TODO Потом удалить

            return false;
        }
    }

    public function CheckUser(string $email, string $passwd): bool
    {
        try {
            $db = static::getDB();

            $result = $db->prepare("SELECT * FROM reg_user WHERE email = ? AND passwd = ?"); // TODO добавить проверку активации почты
            $hash_passwd = self::HashPassword($passwd);
            $result->execute([$email, $hash_passwd]);

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
