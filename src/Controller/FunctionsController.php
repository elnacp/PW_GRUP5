<?php

namespace SilexApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SilexApp\Model\Repository\UserTasks;
use SilexApp\Model\Repository\UpdateBaseService;



class FunctionsController{


    public function comentari(Application $app, $id, $usuari_log){
        $repo = new UserTasks($app['db']);
        $message = $repo->comentari($id, $usuari_log);
        $type = 1;
        $repo->notificacio($id, $usuari_log, $type);
        $response = new Response();
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
        $sql = "SELECT * FROM imatge WHERE id = ?";
        $s = $app['db']->fetchAssoc($sql, array((int)$id));
        $autor = $s['user_id'];
        $private = $s['private'];
        $sql1 = "SELECT username FROM usuari WHERE id = ?";
        $s2 = $app['db']->fetchAssoc($sql1, array((int)$autor));
        $sql3 = "SELECT * FROM usuari WHERE id = ?";
        $s3 = $app['db']->fetchAssoc($sql3, array((int)$autor));
        $birthdate = $s3['birthdate'];
        $usuari =  $app['session']->get('name');
        $sql4 = "SELECT count(*) as total FROM likes WHERE image_id = ?";
        $l = $app['db']->fetchAssoc($sql4, array((int)$s['id']));
        $likes = $l['total'];

        if($private == 0){
            $content = $app['twig']->render('imatgePublica.twig', [
                    'id' => $id,
                    'usuari_log' => $usuari,
                    'username' => $usuari,
                    'logejat' => true,
                    'autor' => $s2['username'],
                    'title' => $s['title'],
                    'dia' => $birthdate,
                    'visites' => $s['visits'],
                    'likes' => $likes,
                    'message' => $message,
                    'image' => '/.'.$s3['img_path'],
                    'Imagen' => $autor = '/.'.$s['img_path'],
                    'imgPerfil' =>'/.'.$s3['img_path'],
                    'imgPost' => '/.'.$s['img_path']
                ]
            );
        }else{
            $content = $app['twig']->render('error.twig', [
                    'message' => 'Las imágenes privadas no se pueden visualizar',
                    'logejat' => true,
                    'username' => $usuari,
                    'image' => '/.'.$s3['img_path']

                ]
            );
        }

        $response->setContent($content);
        return $response;
    }


    public function comentarisUser(Application $app){
        $response = new Response();
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
        $repo = new UserTasks($app['db']);
        $titols_img[] = "";
        $dades = $repo->comentarisUser();
        $aux = new UpdateBaseService($app['db']);
        $info = $aux->getUserInfo($app['session']->get('name'));
        list($name, $img) = explode("!=!", $info);
        $content = $app['twig']->render('userComments.twig', [
                'logejat' => true,
                'comentaris' => $dades,
                'message' => null,
                'image' => $img,
                'Imagen'=>null,
                'username' =>$name
            ]
        );
        $response->setContent($content);
        return $response;



    }

    public function publicProfile(Application $app, Request $request, $username){
        $opcio = htmlspecialchars($request->get('opcio'));
        $response = new Response();
        $repo = new UserTasks($app['db']);
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
        $sql = "SELECT id FROM usuari WHERE username = ?";
        $s = $app['db']->fetchAssoc($sql, array($username));
        $id = $s['id'];
        $repo->imatgesUsuari($id);
        $log = false;
        $imatgesPublic = $repo->imatgesPerfil($username, $opcio);
        $dadesUsuari = $repo->dadesUsuari($username,$id);
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
        $name = '';
        $img = null;
        if ($app['session']->has('name')){
            $log = true;
            $aux = new UpdateBaseService($app['db']);
            $info = $aux->getUserInfo($app['session']->get('name'));
            list($name, $img) = explode("!=!", $info);
            $img = '.'.$img;
        }
        $content = $app['twig']->render('publicProfile.twig',[
            'logejat' => $log,
            'imatgesPublic' =>$imatgesPublic,
            'dadesUsuari' =>$dadesUsuari,
            'image' =>$img,
            'username'=>$name
        ]);
        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);
        return $response;
    }




}