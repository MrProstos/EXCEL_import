<?php

namespace App\Controllers;

use \Core\View;
use App\Models\Users;

/**
 * Class serving the route SignIn
 */
class SignIn extends \Core\Controller
{
    /**
     * Checks if such a user exists
     * @return void
     */
    protected function before()
    {
        $auth = new Auth();

        if ($auth->isAuth()) {
            header('Location: ?import/');
        }
    }

    /**
     * Show page
     * @return void
     */
    public function indexAction(): void
    {
        View::renderTemplate('sign_in.html.twig',['title'=>'Вход']);
    }


    /**
     * User Login
     * @return void
     */
    public function signInAction()
    {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $users = new Users();

        if (!$users->checkUser($email, $password)) {
            echo 'Такого пользователя нету'; // TODO Доделать
            return;
        }
        setcookie('hash', md5($email . $password));
        header('Location: ?import/');
    }
}
