<?php

namespace SilexApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SilexApp\Model\Repository\UserTasks;

class LikeController{

    public function likeImage(Application $app, $id, $usuari_log){
        $response = new Response();
        $repo = new UserTasks($app['db']);
        $repo->like($id, $usuari_log);
        $type = 2;
        $repo->notificacio($id, $usuari_log, $type);
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
        $sql = "SELECT * FROM imatge WHERE id = ?";
        $s = $app['db']->fetchAssoc($sql, array((int)$id));
        $autor = $s['user_id'];
        $img = $s['img_path'];
        $sql1 = "SELECT username FROM usuari WHERE id = ?";
        $s2 = $app['db']->fetchAssoc($sql1, array((int)$autor));
        $sql3 = "SELECT * FROM usuari WHERE id = ?";
        $s3 = $app['db']->fetchAssoc($sql3, array((int)$autor));
        $usuari =  $app['session']->get('name');
        $sql4 = "SELECT count(*) as total FROM likes WHERE image_id = ?";
        $l = $app['db']->fetchAssoc($sql4, array((int)$s['id']));
        $likes = $l['total'];
        $content = $app['twig']->render('imatgePublica.twig', [
                'id' => $id,
                'usuari_log' => $usuari,
                'username' =>$usuari,
                'logejat' => true,
                'autor' => $s2['username'],
                'title' => $s['title'],
                'dia' => date("Y-m-d H:i:s"),
                'visites' => $s['visits'],
                'likes' => $likes,
                'message' => null,
                'image' => $s3['img_path'],
                'Imagen' => $img

            ]
        );
        $response->setContent($content);
        return $response;

    }

    public function likeHome(Application $app, $id, $usuari_log){
        $repo = new UserTasks($app['db']);
        $type = 2;
        $repo->like($id, $usuari_log);
        $repo->notificacio($id, $usuari_log,$type );
        if($app['session']->has('name')){
            $log = true;
        }
        $usuari =  $app['session']->get('name');
        $imgMesVistes = $repo->home1($log,$usuari);
        if(!$app['session']->has('name')) {
            $content = $app['twig']->render('hello.twig', [
                'logejat' => false,
                'dades' => $imgMesVistes,
                'username' => '',
                'image' => null

            ]);
        }else{
            $content = $app['twig']->render('hello.twig', [
                'logejat' => true,
                'dades' => $imgMesVistes,
                'username' => $usuari,
                'image'=>null

            ]);
        }
        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);
        return $response;
    }
}