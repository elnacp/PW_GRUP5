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
        $nickname = htmlspecialchars($request->get('nickname'));
        $email = htmlspecialchars($request->get('email'));
        $birthdate = htmlspecialchars($request->get('edad'));
        $password = htmlspecialchars($request->get('password'));


        $repo = new UserTasks($app['db']);
        $exists = $repo->checkUser($nickname);
        $response = new Response();
        if (!$exists) {
            $repo->RegisterUser($nickname, $email, $birthdate, $password);
            $response->setStatusCode(Response::HTTP_OK);
            $content = $app['twig']->render('error.twig', [
                    'message' => 'Registro finalizado correctamente'
                ]
            );

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
        $path_name = htmlspecialchars($request->get('files[]'));

        $repo = new UserTasks($app['db']);
        $ok = $repo->DBnewPost($title, $path_name);
        if($ok){
            $response = new Response();
            $content = $app['twig']->render('error.twig', [
                    'message' => 'New Post Ok'
                ]
            );
        }

        $response->setContent($content);
        return $response;
    }

}