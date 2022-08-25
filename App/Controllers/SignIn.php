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
    protected function before(): void
    {
        $dbUsers = new Users();

        if ($dbUsers->isAuth()) {
            header('Location: /import/');
        }
    }

    /**
     * Show page
     * @return void
     */
    public function indexAction(): void
    {
        View::renderTemplate('sign_in.twig', ['title' => 'Вход']);
    }


    /**
     * User Login
     * @return void
     */
    public function signInAction(): void
    {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $dbUsers = new Users();

        if (!$dbUsers->checkUser($email, $password)) {
            echo 'Такого пользователя нету';
            return;
        }
        setcookie('hash', md5($email . $password),0,'/');
        header('Location: /import/');
    }
}
