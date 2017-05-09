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

}