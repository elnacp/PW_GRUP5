<?php

namespace SilexApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SilexApp\Model\Repository\UserTasks;
use SilexApp\Model\Repository\Ajax;

class TaskController{
    public function indexAction(Application $app){
        $repo = new UserTasks($app['db']);
        $log = false;
        if($app['session']->has('name')){
            $log = true;
        }
        $usuari =  $app['session']->get('name');
        $imgMesVistes = $repo->home1($log,$usuari);
        //$data = $repo->ultimesImatges($log, $usuari);
        if(!$app['session']->has('name')) {
            $content = $app['twig']->render('hello.twig', [
                'logejat' => false,
                'dades' => $imgMesVistes,
                //'data' => $data
            ]);
        }else{
            $content = $app['twig']->render('hello.twig', [
                'logejat' => true,
                'dades' => $imgMesVistes,
                //'data' => $data
            ]);
        }
        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);
        return $response;

    }
    public function editProfile(Application $app){
        $sql = "SELECT * FROM logejat";
        $usuari = $app['db']->fetchAssoc($sql);
        $id = $usuari['user_id'];
        $sql = "SELECT * FROM usuari WHERE id = ?";
        $usuari = $app['db']->fetchAssoc($sql, array((int)$id));
        $content = $app['twig']->render('editProfile.twig', [
            'username' => $usuari['username'],
            'birthdate' => $usuari['birthdate'],

            'imagen' => $usuari['img_path'],

            'logejat' => true

        ]);
        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);
        return $response;
    }
    public function registerUser(Application $app){
        $content = $app['twig']->render('registerUser.twig',[
            'logejat' => false,
        ] );
        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);
        return $response;
    }
    public function LogIn(Application $app){
        $content = $app['twig']->render('LogIn.twig',[
            'logejat' => false,
            'message' => null
        ] );
        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);
        return $response;
    }
    public function newPost(Application $app){
        $content = $app['twig']->render('newPost.twig', [
            'logejat' => true,
            'message' => null
        ]);
        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);
        return $response;
    }
    public function galeria(Application $app){
        $repo = new UserTasks($app['db']);
        $dades = $repo->dadesImatges();

        $content = $app['twig']->render('galeria.twig', [
            'logejat' => true,
            'dades' => $dades,
            'message' => NULL

        ]);
        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);
        return $response;
    }

}

