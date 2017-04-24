<?php
$app->get('/', 'SilexApp\\Controller\\TaskController::indexAction');

//SESSION
$before = function (Request $request, Application $app){
    if (!$app['session']->has('name')){
        $response =  new Response();
        $content = $app['twig']->render('error.twig',[
            'message' => 'You must be logged'
        ]);
    }
}
$app->get('/test', 'SilexApp\\Controller\\TaskController::indexAction');
$app->get('/admin', 'SilexApp\\Controller\\TaskController::adminAction');
