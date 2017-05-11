<?php

namespace SilexApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SilexApp\Model\Repository\UserTasks;

class TaskController{
    public function indexAction(Application $app){
        $ultimesImg = "";
        $repo = new UserTasks($app['db']);
        $log = false;
        if($app['session']->has('name')){
            $log = true;
        }
        $usuari =  $app['session']->get('name');
        $imgMesVistes = $repo->home1($log,$usuari);
        if(!$app['session']->has('name')) {
            $content = $app['twig']->render('hello.twig', [
                'logejat' => false,
                'dades' => $imgMesVistes
            ]);
        }else{
            $content = $app['twig']->render('hello.twig', [
                'logejat' => true,
                'dades' => $imgMesVistes
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
        //echo('alert("Desea eliminar la foto?"');
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

    public function visualitzacioImatge(Application $app, $id){
        $response = new Response();
        $repo = new UserTasks($app['db']);
        $privada = $repo->incrementarVisites($id);
        $logejat = false;
        if($app['session']->has('name')){
            $logejat = true;
        }
        if($privada == 1){
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $content = $app['twig']->render('error.twig', [
                    'message' => ' Imagen privada',
                    'logejat' => $logejat
                ]
            );
            $response->setContent($content);
            return $response;
        }else{
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $sql = "SELECT * FROM imatge WHERE id = ?";
            $s = $app['db']->fetchAssoc($sql, array((int)$id));
            $autor = $s['user_id'];
            $sql1 = "SELECT username FROM usuari WHERE id = ?";
            $s2 = $app['db']->fetchAssoc($sql1, array((int)$autor));
            $sql3 = "SELECT * FROM usuari WHERE id = ?";
            $s3 = $app['db']->fetchAssoc($sql3, array((int)$autor));
            $usuari =  $app['session']->get('name');
            $content = $app['twig']->render('imatgePublica.twig', [
                    'id' => $id,
                    'usuari_log' => $usuari,
                    'logejat' => $logejat,
                    'autor' => $s2['username'],
                    'title' => $s['title'],
                    'dia' => date("Y-m-d H:i:s"),
                    'visites' => $s['visits'],
                    'likes' => $s['likes'],
                    'message' => null,
                    'imPerfil' => $s3['img_path']


                ]
            );
            $response->setContent($content);
            return $response;
        }




    }

    public function likeImage(Application $app, $id, $usuari_log){
        $response = new Response();
        $repo = new UserTasks($app['db']);
        $repo->like($id, $usuari_log);
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
        $sql = "SELECT * FROM imatge WHERE id = ?";
        $s = $app['db']->fetchAssoc($sql, array((int)$id));
        $autor = $s['user_id'];
        $sql1 = "SELECT username FROM usuari WHERE id = ?";
        $s2 = $app['db']->fetchAssoc($sql1, array((int)$autor));
        $sql3 = "SELECT * FROM usuari WHERE id = ?";
        $s3 = $app['db']->fetchAssoc($sql3, array((int)$autor));
        $usuari =  $app['session']->get('name');
        $content = $app['twig']->render('imatgePublica.twig', [
                'id' => $id,
                'usuari_log' => $usuari,
                'logejat' => true,
                'autor' => $s2['username'],
                'title' => $s['title'],
                'dia' => date("Y-m-d H:i:s"),
                'visites' => $s['visits'],
                'likes' => $s['likes'],
                'message' => null,
                'imPerfil' => $s3['img_path']

            ]
        );
        $response->setContent($content);
        return $response;

    }



}

