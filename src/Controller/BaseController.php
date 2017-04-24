<?php

namespace  SilexApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        $content = 'Welcome back' . $app['session']->get('name');
        return new Response($content);
    }
}