<?php

namespace App\Controllers;

use \Core\View;
use App\Models\Users;

class Sign_in extends \Core\Controller
{

    protected function before()
    {
        $controller = new Auth();
        if ($controller->Auth()) {
            header("Location: http://mrprostos.keenetic.link/?import/");
        }
    }

    public function indexAction(): void
    {
        View::renderTemplate("sign_in.html");
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

        setcookie("email", $email);
        setcookie("password", Users::HashPassword($password));

        header("Location: http://mrprostos.keenetic.link/?import/");
    }

}
