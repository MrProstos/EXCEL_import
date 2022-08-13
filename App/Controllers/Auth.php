<?php

namespace App\Controllers;

use App\Models\Users;

class Auth
{
    public function Auth(): bool
    {
        if (isset($_COOKIE["email"]) and isset($_COOKIE["password"])) {

            $email = $_COOKIE["email"];
            $password = $_COOKIE["password"];

            $users = new Users();
            $users->CheckUser($email, $password);
            if ($users) {
                return true;
            }
        }
        return false;
    }
}