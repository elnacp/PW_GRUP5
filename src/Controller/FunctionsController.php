<?php

namespace SilexApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SilexApp\Model\Repository\UserTasks;

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
                'logejat' => true,
                'autor' => $s2['username'],
                'title' => $s['title'],
                'dia' => date("Y-m-d H:i:s"),
                'visites' => $s['visits'],
                'likes' => $likes,
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

    public function publicProfile(Application $app, Request $request, $username){
        $opcio = htmlspecialchars($request->get('opcio'));
        $response = new Response();
        $repo = new UserTasks($app['db']);
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
        $sql = "SELECT id FROM usuari WHERE username = ?";
        $s = $app['db']->fetchAssoc($sql, array($username));
        $id = $s['id'];
        $repo->imatgesUsuari($id);

        $imatgesPublic = $repo->imatgesPerfil($username, $opcio);
        $dadesUsuari = $repo->dadesUsuari($username,$id);
        $response->setStatusCode(Response::HTTP_NOT_FOUND);

        $content = $app['twig']->render('publicProfile.twig',[
            'logejat' => false,
            'imatgesPublic' =>$imatgesPublic,
            'dadesUsuari' =>$dadesUsuari
        ]);
        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);
        return $response;
    }




}