<?php
/**
 * Created by PhpStorm.
 * User: noa
 * Date: 14/5/17
 * Time: 19:47
 */
namespace SilexApp\Model\Repository;

//require ("PHPMailer_5.2.4/class.phpmailer.php");
require 'PHPMailerAutoload.php';
use PHPMailer;

class EmailSender{

//$mail->SMTPDebug = 3;                               // Enable verbose debug output
    function sendEmail($email){

        $mail = new PHPMailer;


        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp-mail.outlook.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'clabaza123@hotmail.com';                 // SMTP username
        $mail->Password = 'whiterose';                           // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                    // TCP port to connect to

        $mail->setFrom('Dogygram@example.com', 'Mailer');
        $mail->addAddress($email);               // Name is optional

        $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        $mail->isHTML(true);                                  // Set email format to HTML

        $message = 'Gracias por registrarte en <b>Dogygram</b>. Para activar la cuenta accede al Link:';
        $message .= '<a href="grup5.dev"> grup5.dev</a>';
        $mail->Subject = 'Activacion de Cuenta';
        $mail->Body    = $message;
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';


        return $mail->send();

    }
}

?>