<?php

namespace SilexApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskController{
    public function indexAction(Application $app){
        $content = $app['twig']->render('hello.twig' );
        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);
        return $response;

    }
    public function editProfile(Application $app){
        $sql = "SELECT * FROM logejat";
        $usuari = $app['db']->fetchAssoc($sql);
        $id = $usuari['id'];
        $sql = "SELECT * FROM usuari WHERE id = ?";
        $usuari = $app['db']->fetchAssoc($sql, array((int)$id));
        $content = $app['twig']->render('editProfile.twig', [
            'username' => $usuari['username'],
            'birthdate' => $usuari['birthdate']
        ]);
        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);
        return $response;
    }
    public function registerUser(Application $app){
        $content = $app['twig']->render('registerUser.twig' );
        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);
        return $response;
    }
    public function LogIn(Application $app){
        $content = $app['twig']->render('LogIn.twig' );
        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);
        return $response;
    }
    public function newPost(Application $app){
        $content = $app['twig']->render('newPost.twig');
        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);
        return $response;
    }


}

