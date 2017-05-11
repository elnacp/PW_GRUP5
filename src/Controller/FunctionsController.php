<?php

namespace SilexApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SilexApp\Model\Repository\UserTasks;

class FunctionsController{


    public function likeHome(Application $app, $id, $usuari_log){
        $repo = new UserTasks($app['db']);
        $repo->like($id, $usuari_log);
        if($app['session']->has('name')){
            $log = true;
        }
        $usuari =  $app['session']->get('name');
        $imgMesVistes = $repo->home1($log,$usuari);
        if(!$app['session']->has('name')) {
            $content = $app['twig']->render('hello.twig', [
                'logejat' => false,
                'dades' => $imgMesVistes,

            ]);
        }else{
            $content = $app['twig']->render('hello.twig', [
                'logejat' => true,
                'dades' => $imgMesVistes,

            ]);
        }
        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);
        return $response;
    }

    public function comentari(Application $app, $id, $usuari_log){
        $repo = new UserTasks($app['db']);
        $message = $repo->comentari($id, $usuari_log);
        $response = new Response();
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
                'message' => $message,
                'imPerfil' => $s3['img_path']

            ]
        );
        $response->setContent($content);
        return $response;
    }

    public function comentarisUser(Application $app){
        $response = new Response();
        $response = new Response();
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
        $repo = new UserTasks($app['db']);
        $titols_img[] = "";
        $dades = $repo->comentarisUser();
        $content = $app['twig']->render('userComments.twig', [
                'logejat' => true,
                'comentaris' => $dades,
                'message' => null

            ]
        );
        $response->setContent($content);
        return $response;



    }




}