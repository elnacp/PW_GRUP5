<?php

namespace  SilexApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\Configuration;
use SilexApp\Model\Repository\UserTasks;
use SilexApp\Model\Repository\UpdateBaseService;



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
        $aux = new UpdateBaseService($app['db']);
        $info = $aux->getUserInfo($app['session']->get('name'));
        list($name, $img) = explode("!=!", $info);
        $content = $app['twig']->render('hello.twig',[
            'logejat' => true,
            'username' =>$name,
            'image' => $img
        ]);
        return new Response($content);
    }


    public function iniciarSession(Application $app, $name){
        $app['session']->set('name', $name);
        //setcookie("guest", "guest", time() + 3600 * 24 * 7);
        $repo = new UserTasks($app['db']);
        $log = false;
        if($app['session']->has('name')){
            $log = true;
        }
        $usuari = $app['session']->get('name');
        $aux = new UpdateBaseService($app['db']);
        $info = $aux->getUserInfo($usuari);
        list($name, $img) = explode("!=!", $info);
        $repo = new UserTasks($app['db']);
        $imgMesVistes = $repo->home1($log, $usuari);
        $content = $app['twig']->render('hello.twig',[
            'logejat' => true,
            'dades' => $imgMesVistes,
            'username' =>$usuari,
            'image' => $img
        ]);
        return new Response($content);
    }

    public function cerrarSession(Application $app){
        $sql = "DELETE  FROM logejat";
        //setcookie("guest", "", time() - 3600 * 24 * 7);
        $app['db']->query($sql);
        $app['session']->remove('name');
        $repo = new UserTasks($app['db']);
        $log = false;
        if($app['session']->has('name')){
            $log = true;
        }
        $imgMesVistes = $repo->home1($log, NULL);
        $content = $app['twig']->render('hello.twig',[
            'logejat' => false,
            'dades' => $imgMesVistes,
            'username' => null,
            'image' => null
        ]);
        return new Response($content);
    }


}