<?php
/**
 * Created by PhpStorm.
 * User: elnacabotparedes
 * Date: 5/4/17
 * Time: 18:28
 */

use Silex\Application;
$app = new Application();
/*$app->get('/hello/{name}', function($name) use($app){
    return $app['twig']->render('hello.twig', array(
       'user' => $name,
    ));
});*/
$app['app.name'] = 'SilexApp';
$app['calc'] = function(){
    return new \SilexApp\Model\Services\Calculator();
};
return $app;