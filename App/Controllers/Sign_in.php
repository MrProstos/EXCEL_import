<?php

namespace App\Controllers;

use \Core\View;
use App\Models\Users;

class Sign_in extends \Core\Controller
{

    /**
     * Show the Sign_in page
     *
     * @return bool
     * @throws \Exception
     */

    function before(): bool
    {

        $email = $_COOKIE["email"];
        $password = $_COOKIE["password"];

        $users = new Users();
        $users->CheckUser($email, $password);
        if ($users) {
            View::render("import.html");
            return false;
        }

        return true;
    }


    public function indexAction(): void
    {
        View::render("sign_in.html");
    }



    public function sign_in()
    {

        $email = $_POST["email"];
        $password = $_POST["password"];

        $users = new Users();
        $users->CheckUser($email, $password);
        if (!$users) {
            echo "Такого пользователя нету"; // TODO Доделать
            die();
        }

        setcookie("email",$email);
        setcookie("password", Users::HashPassword($password));
    }
}
