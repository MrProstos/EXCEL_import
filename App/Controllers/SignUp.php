<?php

namespace App\Controllers;

use \Core\View;
use App\Models\Users;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class SignUp extends \Core\Controller
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

        $users = new Users();
        $users->registrationUser($username, $email, $password) or die();

        if ($this->sendEmailVerification($email, md5($email . $password))) {
            echo "Подтвердите почту";
//            header("Location: http://mrprostos.keenetic.link/");
        }
    }

    private function sendEmailVerification(string $email, string $hash): bool
    {

        $mail = new PHPMailer(true);

        try {
            //Server settings

            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host = "smtp.gmail.com";                     //Set the SMTP server to send through
            $mail->SMTPAuth = true;                                   //Enable SMTP authentication
            $mail->Username = $email;                     //SMTP username
            $mail->Password = $_ENV["GOOGLE_PASSWORD"];                              //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            $mail->addAddress('vladmihin28@gmail.com');     //Add a recipient


            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = "Подтверждение почты";
            $mail->Body = "<form action='http://mrprostos.keenetic.link/?sign_up/emailVerification' method='POST'>
                           <input type='hidden' name='hash' value='$hash'>
                           <button type='submit'>Нажмите для подтверждения почты</button>
                           </form>";

            $mail->send();
            return true;
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            return false;
        }
    }

    function emailVerification()
    {
        if (isset($_POST["hash"])) {
            $hash = $_POST["hash"];

            $dbUsers = new Users();
            if (!$dbUsers->confirmMail($hash)) {
                echo "Ошибка подтверждения";
                return;
            }
            echo "Почта подтверждена";
        }

    }
}
