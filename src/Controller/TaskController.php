<?php

namespace SilexApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SilexApp\Model\Repository\UserTasks;

class TaskController{
    public function indexAction(Application $app){
        if(!$app['session']->has('name')) {
            $content = $app['twig']->render('hello.twig', [
                'logejat' => false
            ]);
        }else{
            $content = $app['twig']->render('hello.twig', [
                'logejat' => true
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
            'logejat' => true,
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
            'logejat' => false
        ] );
        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);
        return $response;
    }
    public function newPost(Application $app){
        $content = $app['twig']->render('newPost.twig', [
            'logejat' => true
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

    public function editarImatge(Application $app, $id){
        $sql = "SELECT * FROM imatge WHERE id=$id";
        $imatge = $app['db']->fetchAssoc($sql);

        $estat = "";
        if($imatge['private'] == 1){
            $estat = "checked";
        }
        $content = $app['twig']->render('editarImatge.twig', [
            'logejat' => true,
            'titol' => $imatge['title'],
            'privada' => $estat,
            'id' => $id

        ]);
        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);
        return $response;
    }

    public function eliminarImatge(Application $app, $id)
    {
        $repo = new UserTasks($app['db']);
        $repo->deleteImage($id);
        $dades = $repo->dadesImatges();
        $content = $app['twig']->render('galeria.twig', [
            'logejat' => true,
            'dades' => $dades,
            'message' => 'Se ha eliminado correctamente!'

        ]);
        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);
        return $response;
    }

}

