<?php

namespace App\Controllers;

use \Core\View;
use App\Models\Users;


class SignIn extends \Core\Controller
{

    protected function before()
    {
        $auth = new Auth();

        if ($auth->isAuth()) {

            header("Location: http://mrprostos.keenetic.link/?import/");
        }
    }

    public function indexAction(): void
    {
        View::renderTemplate("sign_in.html");
    }


    public function signInAction()
    {

        $email = $_POST["email"];
        $password = $_POST["password"];

        $users = new Users();

        if (!$users->checkUser($email, $password)) {

            echo "Такого пользователя нету"; // TODO Доделать
            return;
        }

        setcookie("hash", md5($email.$password));

        header("Location: http://mrprostos.keenetic.link/?import/");
    }

}
