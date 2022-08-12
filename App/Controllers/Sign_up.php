<?php

namespace App\Controllers;

use \Core\View;
use App\Models\Users;

class Sign_up extends \Core\Controller
{
    /**
     * Show the Sign_in page
     *
     * @return void
     * @throws \Exception
     */

    public function indexAction(): void
    {
        View::render("sign_up.html");
    }

    public function registrationAction(): void
    {
        $username = $_POST["username"];
        $email = $_POST["email"];
        $password = $_POST["password"];
//        $hash = password_hash($email . time(), PASSWORD_DEFAULT);

        $users = new Users();
        $users->Registration($username, $email, $password) or die();


    }
}