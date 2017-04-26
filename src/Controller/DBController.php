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

    public function DBeditProfile(Application $app, Request $request){
        $name = htmlspecialchars($_POST['nickname']);
        $birth = htmlspecialchars($_POST['birthdate']);
        $pass1 = htmlspecialchars($_POST['password1']);
        $pass2 = htmlspecialchars($_POST['password2']);
        $path = htmlspecialchars($_POST['files[]']);

        $repo = new UserTasks($app['db']);
        $ok = $repo->validateEditProfile($name, $birth, $pass1, $pass2, $path);
        $response = new Response();
    }
}