<?php

namespace  SilexApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\Configuration;

class BaseController{
    public function indexAction(Application $app){
        if($app['session']->has('name')){
            $app['session']->remove('name');
            return new Response(('Session finished'));
        }
        $app['session']->set('name', 'Elna');
        $content = 'Session started for the user' . $app['session']->get('name');
        return new Response($content);
    }
    public function adminAction(Application $app){
        $content = $app['twig']->render('hello.twig',[
            'logejat' => true
        ]);
        return new Response($content);
    }


    public function iniciarSession(Application $app, $name){
        $app['session']->set('name', $name);
        $content = $app['twig']->render('hello.twig',[
            'logejat' => true
        ]);
        return new Response($content);
    }

    public function cerrarSession(Application $app){
        $sql = "DELETE  FROM logejat";
        $app['db']->query($sql);
        $app['session']->remove('name');
        $content = $app['twig']->render('hello.twig',[
            'logejat' => false
        ]);
        return new Response($content);
    }
}