<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

$mail = new PHPMailer();
$mail->IsSMTP();
$mail->Mailer = "smtp";

$mail->SMTPDebug  = 1;
$mail->SMTPAuth   = TRUE;
$mail->SMTPSecure = "tls";
$mail->Port       = 587;
$mail->Host       = "smtp.gmail.com";
$mail->Username   = "majorproject1713371@gmail.com";
$mail->Password   = "majorproject-171337";

$mail->IsHTML(true);
$mail->AddAddress("amodhshenoy@gmail.com");
$mail->SetFrom("majorproject1713371@gmail.com", "from-name");
$mail->AddReplyTo("rrshenoy64@gmail.com", "reply-to-name");
$mail->Subject = "Test is Test Email sent via Gmail SMTP Server using PHP Mailer";
$myfile = fopen(__DIR__ . "/templates/mail/otp.html", "r") or die("Unable to open file!");
$content = str_replace('otpgoeshere', '1234', fread($myfile, filesize(__DIR__ . "/templates/mail/otp.html")));

// echo $content;
fclose($myfile);

$mail->MsgHTML($content);
if (!$mail->Send()) {
    echo "Error while sending Email.";
    var_dump($mail);
} else {
    echo "Email sent successfully";
}
