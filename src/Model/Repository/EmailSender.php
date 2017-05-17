<?php


namespace SilexApp\Model\Repository;

use PHPMailer;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\Configuration;

require 'PHPMailerAutoload.php';

use SilexApp\Model\Repository\UserTasks;

class EmailSender{


    function sendEmail(Application $app, $email, $id){


        $code = '';
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyz';
        $max = strlen($pattern)-1;
        for($i=0;$i < 15;$i++) $code .= $pattern{mt_rand(0,$max)};

        $repo = new UserTasks($app['db']);

        $repo->createUserActivation($id,$code);
        $url = 'grup5.dev/activateUser/'.$code.'/'.$id;
        $msg = 'Muchas gracias por crear una cuenta en Doggygram. Para activar la cuenta entra en el siguiente link: ';
        $msg.= '<a href='.$url.'>'.$url.'/a>';
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPDebug = 2;
        $mail->SMTPAuth= true;
        $mail->Port = 587; // Or 5
        $mail->Username= 'doggygram2017@gmail.com';
        $mail->Password= 'ProjectesWeb2';
        $mail->SMTPSecure = 'tls';
        $mail->From = 'doggygram2017@gmail.com';
        $mail->FromName= 'Doggygram';
        $mail->isHTML(true);
        $mail->Subject = 'Activar cuenta';
        $mail->Body = $msg;
        $mail->addAddress($email);

        if ($mail->send()){
            return true;
        }else{
            return false;
        }

    }
}

?>