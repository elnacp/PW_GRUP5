<?php

namespace SilexApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SilexApp\Model\Repository\UserTasks;


class DBController
{
    public function DBlogin(Application $app, Request $request)
    {
        $name = htmlspecialchars($_POST['nickname']);
        $password = htmlspecialchars($_POST['password']);
        $repo = new UserTasks($app['db']);
        $exists = $repo->validateUser($name, $password);
        $response = new Response();
        if (!$exists) {
            //echo("hello");
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $content = $app['twig']->render('error.twig', [
                    'message' => 'User not found'
                ]
            );
        } else {
            //echo("adios");
            $repo->logejarUsuari($name);
            $response->setStatusCode(Response::HTTP_OK);
            $content = $app['twig']->render('error.twig', [
                    'message' => $name
                ]
            );
        }
        $response->setContent($content);
        return $response;
    }


    public function DBeditProfile(Application $app)
    {
        $name = htmlspecialchars($_POST['nickname']);
        $birth = htmlspecialchars($_POST['edad']);
        $pass1 = htmlspecialchars($_POST['password1']);
        //$pass2 = htmlspecialchars($_POST['password2']);
        //$path = htmlspecialchars($_POST['files[]']);

        $repo = new UserTasks($app['db']);
        $repo-> validateEditProfile($name, $birth, $pass1);
        $response = new Response();
        $content = $app['twig']->render('error.twig', [
                'message' => 'hola'
            ]
        );
        $response->setContent($content);
        return $response;
    }


    public function DBRegister(Application $app, Request $request)
    {
        $nickname = $request->get('nickname');
        $email = $request->get('email');
        $birthdate = $request->get('edad');
        $password = $request->get('password');
        $img = $request->get('imgP');



        $repo = new UserTasks($app['db']);
        $exists = $repo->checkUser($nickname);
        $response = new Response();

        if (!$exists) {

            /*$aleatorio = uniqid(); //Genera un id único para identificar la cuenta a traves del correo.

            $mensaje = "Registro en tuweb.com\n\n";
            $mensaje .= "Estos son tus datos de registro:\n";
            $mensaje .= "Usuario: $nickname.\n";
            $mensaje .= "Contraseña: $password.\n\n";
            $mensaje .= "Debes activar tu cuenta pulsando este enlace: http://www.tuweb.com/activacion.php?id=$aleatorio";

            $cabeceras = 'From: webmaster@example.com' . "\r\n" .
                'Reply-To: webmaster@example.com' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

            $sendMail = mail('noaduran@hotmail.com','Activar cuenta',$mensaje, $cabeceras);
            if($sendMail){*/
                $repo->RegisterUser($nickname, $email, $birthdate, $password, $img);
                $response->setStatusCode(Response::HTTP_OK);

                $content = $app['twig']->render('error.twig', [
                        'message' => 'Registro finalizado correctamente'. $img
                    ]
                );
           /* }else{
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                $content = $app['twig']->render('error.twig', [
                        'message' => 'No se ha podido enviar el email'
                    ]
                );             }*/

        } else {
            $response->setStatusCode(Response::HTTP_ALREADY_REPORTED);
            $content = $app['twig']->render('error.twig', [
                    'message' => 'El usuario ya existe'
                ]
            );
        }
        $response->setContent($content);
        return $response;

    }
    public function DBnewPost(Application $app, Request $request){
        $title = htmlspecialchars($request->get('title'));
        $imgName = htmlspecialchars($request->files->get('imagen'));
        $privada = htmlspecialchars($request->get('privada'));
        $output = htmlspecialchars($request->get('registerImg'));
        var_dump($output);
        //var_dump($request->files->get('imagen'));
        //$path = $request->files->get('imagen');

        var_dump($path);
        if ($privada ==="privada"){
            $private = 1;
        }else{
            $private = 0;
        }
        $folder = "/assets/img/";
        $path_name = $imgName;
        //var_dump($path_name);
        $repo = new UserTasks($app['db']);
        $ok = $repo->DBnewPost($title, $path_name, $private);
        if($ok) {
            $response = new Response();
            $content = $app['twig']->render('error.twig', [
                    'message' => 'New Post Ok'
                ]
            );
        }
        $response->setContent($content);
        return new Response();
        return $response;
    }

}