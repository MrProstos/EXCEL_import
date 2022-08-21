<?php

namespace App\Controllers;

use App\Config;
use \Core\View;
use App\Models\Users;
use PHPMailer\PHPMailer\Exception;

/**
 * Class serving the route SignUp
 */
class SignUp extends \Core\Controller
{
    /**
     * Show the Sign_in page
     * @return void
     */
    public function indexAction(): void
    {
        View::renderTemplate('sign_up.twig', ['title' => 'Регистрация']);
    }

    /**
     * User Registration
     * @return void
     */
    public function registrationAction(): void
    {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        $dbUsers = new Users();
        $dbUsers->registrationUser($username, $email, $password) or die();

        if ($this->sendEmailVerification($email, md5($email . $password))) {
            echo 'Подтвердите почту';
        }
    }

    /**
     * Sending a confirmation email
     * @param string $email User email
     * @param string $hash Email and password concatenation
     * @return bool
     */
    private function sendEmailVerification(string $email, string $hash): bool
    {
        $mail = Config::configPHPMailer();

        try {
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Подтверждение почты';
            $mail->Body = "<form action='http://mrprostos.keenetic.link/?sign_up/emailVerification' method='POST'>
                           <input type='hidden' name='hash' value='$hash'>
                           <button type='submit'>Нажмите для подтверждения почты</button>
                           </form>";

            $mail->send();
            return true;
        } catch (Exception) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            return false;
        }
    }

    /**
     * Email сonfirmation
     * @return void
     */
    function emailVerification(): void
    {
        if (isset($_POST['hash'])) {
            $dbUsers = new Users();

            if (!$dbUsers->confirmMail($_POST['hash'])) {
                echo 'Ошибка подтверждения';
                return;
            }
            echo 'Почта подтверждена';
        }

    }
}
