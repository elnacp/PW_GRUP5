<?php

namespace SilexApp\Controller;

use SilexApp\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BaseController
{
    public function indexAction(Application $app)
    {
        if($app['session']->has('name')){
            $app['session']->remove('name');
            return new Response ('Session finished');
        }
        $app['session']->set('name', 'Jaume');
        $content = 'Session started for the user ' . $app['session']->get('name');
        return new Response($content);
    }

    public function adminAction(Application $app)
    {
        $content = 'Session started for the user ' . $app['session']->get('name');
        return new Responese($content);
    }
}