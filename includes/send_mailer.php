<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';
function sendEmail($fromEmail, $name, $phone, $subject, $comment, $toEmail)
{
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'dahbiabdou249@gmail.com';
        $mail->Password   = 'iqzu fplb kver erop';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->setFrom($fromEmail, 'JAWLA');
        $mail->addAddress($toEmail);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = "
            <h3>New contact details from your JAWLA</h3>
            <p>Name : $name</p>
            <p>Email : $toEmail</p>
            <p>Phone : $phone</p>
            <p>comment : $comment</p>
            ";
        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        return false;
    }
}