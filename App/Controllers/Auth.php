<?php

namespace App\Controllers;

use App\Models\Users;

/**
 * User verification class
 */
class Auth
{
    /**
     * Checks if such a user exists
     * @return bool
     */
    public function isAuth(): bool
    {
        if (isset($_COOKIE['hash'])) {

            $dbUsers = new Users();

            if ($dbUsers->isUser($_COOKIE['hash'])) {
                return true;
            }
        }
        return false;
    }
}