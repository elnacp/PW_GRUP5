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
        $name = $request->get('nickname');
        $password = $request->get('password');

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
            $response->setStatusCode(Response::HTTP_OK);
            $content = $app['twig']->render('error.twig', [
                    'message' => $name
                ]
            );
        }
        $response->setContent($content);
        return $response;
    }
    public function DBRegister(Application $app, Request $request)
    {
        $nickname = $request->get('nickname');
        $email = $request->get('email');
        $birthdate = $request->get('edad');
        $password = $request->get('password');


        $repo = new UserTasks($app['db']);
        $exists = $repo->checkUser($nickname);
        $response = new Response();

        if (!$exists) {

            $aleatorio = uniqid(); //Genera un id único para identificar la cuenta a traves del correo.

            $mensaje = "Registro en tuweb.com\n\n";
            $mensaje .= "Estos son tus datos de registro:\n";
            $mensaje .= "Usuario: $nickname.\n";
            $mensaje .= "Contraseña: $password.\n\n";
            $mensaje .= "Debes activar tu cuenta pulsando este enlace: http://www.tuweb.com/activacion.php?id=$aleatorio";

            $cabeceras = 'From: webmaster@example.com' . "\r\n" .
                'Reply-To: webmaster@example.com' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();


            if(mail('noaduran@hotmail.com','Activar cuenta',$mensaje, $cabeceras)){
                $repo->RegisterUser($nickname, $email, $birthdate, $password);
                $response->setStatusCode(Response::HTTP_OK);
                $content = $app['twig']->render('error.twig', [
                        'message' => 'Registro finalizado correctamente'
                    ]
                );
            }else{
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                $content = $app['twig']->render('error.twig', [
                        'message' => 'No se ha podido enviar el email'
                    ]
                );             }

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
}