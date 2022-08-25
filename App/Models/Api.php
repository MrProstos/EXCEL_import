<?php

namespace App\Models;

use PDO;

class Api extends \Core\Model
{
    /**
     * Get token
     * @param string $hash Cookies hash
     * @return string API token
     */
    public function getToken(string $hash): string
    {
        $db = $this->getDB();

        $result = $db->prepare('SELECT api_token FROM mydb.reg_user WHERE passwd = ?');
        $result->execute([$hash]);

        $row = $result->fetchAll(PDO::FETCH_ASSOC);
        $token = $row[0]['api_token'];

        if ($token == '') {
            $token = uniqid('', true);
            $result = $db->prepare('UPDATE mydb.reg_user SET api_token = ? WHERE passwd = ?');
            $result->execute([$token, $hash]);
        }
        return $token;
    }

    /**
     * Checks the validity of the token
     * @param string $token Authorization token
     * @return bool
     */
    public function tokenVerification(string $token): bool
    {
        if ($token != '') {
            $db = $this->getDB();

            $result = $db->prepare('SELECT * FROM mydb.reg_user WHERE api_token = ?');
            $result->execute([$token]);
            if ($result->rowCount() != 1) {
                return false;
            }
            return true;
        }
        return false;
    }

}