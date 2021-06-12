<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class HelperModel
{
    public function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function sendMail($content, $subject, $recemail, $recname = 'customer', $username = 'majorproject1713371@gmail.com', $password = 'majorproject-171337')
    {

        require dirname(dirname(dirname(__FILE__))) . '/vendor/autoload.php';

        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPDebug = 0;
        $mail->Mailer = "smtp";
        // $mail->SMTPDebug  = 1;
        $mail->SMTPAuth   = TRUE;
        $mail->SMTPSecure = "tls";
        $mail->Port       = 587;
        $mail->Host       = "smtp.gmail.com";
        $mail->Username   = $username;
        $mail->Password   = $password;
        $mail->IsHTML(true);
        if ($recname == 'customer')
            $mail->AddAddress($recemail);
        else
            $mail->AddAddress($recemail, $recname);
        $mail->SetFrom($username, 'Kare Forever Essentials');
        $mail->AddReplyTo($username, 'Kare Forever Essentials');
        $mail->Subject = $subject;
        $mail->MsgHTML($content);
        if (!$mail->Send()) {
            echo "Error while sending Email.";
        }
    }
}
