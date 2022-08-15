<?php

namespace App\Controllers;

use App\Models\Users;

class Auth
{
    public function isAuth(): bool // TODO изменить название методово , прочитать PSR наименование
    {
        if (isset($_COOKIE["hash"])) {

            $hash = $_COOKIE["hash"];

            $dbUsers = new Users();

            if ($dbUsers->isUser($hash)) {

                return true;
            }
        }
        return false;
    }
}