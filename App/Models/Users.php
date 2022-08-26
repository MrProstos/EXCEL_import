<?php

namespace App\Models;

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
            $db =$this->getDB();

            $result = $db->prepare("INSERT INTO reg_user (username, email, passwd, confirm_email) VALUES (?, ?, MD5(CONCAT(?, ?)), 'no')");
            $result->execute([$username, $email, $email, $passwd]);

            return true;
        } catch (\PDOException) {
            return false;
        }
    }


    /**
     * Checking the existence of the user
     * @param string $hash Email and password concatenation
     * @return bool
     */
    public function isUser(string $hash): bool
    {
        $db = $this->getDB();

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
            $db = $this->getDB();

            $result = $db->prepare("SELECT * FROM reg_user WHERE passwd = MD5(CONCAT(?,?)) AND confirm_email = 'yes'");
            $result->execute([$email, $passwd]);

            if ($result->rowCount() != 1) {
                return false;
            }

            return true;
        } catch (\PDOException) {
            return false;
        }
    }

    /**
     * Email сonfirmation
     * @param string $hash Email and password concatenation
     * @return bool
     */
    public function confirmMail(string $hash): bool
    {
        try {
            $db = $this->getDB();

            $result = $db->prepare("UPDATE reg_user SET confirm_email = 'yes' WHERE passwd = ?");
            $result->execute([$hash]);

            if ($result->rowCount() != 1) {
                return false;
            }

            return true;
        } catch (\PDOException) {
            return false;
        }
    }

    /**
     * Checks if the user has cookies
     * @return bool
     */
    public function isAuth(): bool
    {
        if (isset($_COOKIE['hash'])) {

            if ($this->isUser($_COOKIE['hash'])) {
                return true;
            }
        }
        return false;
    }
}
