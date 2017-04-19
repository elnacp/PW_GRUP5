<?php
/**
 * Created by PhpStorm.
 * User: elnacabotparedes
 * Date: 13/4/17
 * Time: 19:22
 */

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

}

