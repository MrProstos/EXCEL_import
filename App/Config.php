<?php

namespace App;

use PHPMailer\PHPMailer\PHPMailer;

/**
 * Application configuration
 */
class Config
{

    /**
     * Database host
     * @var string
     */
    const DB_HOST = "127.0.0.1";

    /**
     * Database name
     * @var string
     */
    const DB_NAME = "mydb";

    /**
     * Database user
     * @var string
     */
    const DB_USER = "vlad";

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = "bktl57m";

    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = true;

    /**
     * Sets the settings for PHPMailer
     * @return PHPMailer
     */
    static function configPHPMailer(): PHPMailer
    {
        $mail = new PHPMailer(true);

        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'vladmihin28@gmail.com';
        $mail->Password = $_ENV['GOOGLE_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        return $mail;
    }
}
