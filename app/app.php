<?php

use Silex\Application;
$app = new Application();
/*$app->get('/hello/{name}', function($name) use($app){
    return $app['twig']->render('hello.twig', array(
       'user' => $name,
    ));
});*/


return $app;