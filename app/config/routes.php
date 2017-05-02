<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


//SESSION
$before = function (Request $request, Application $app){
    if(!$app['session']->has('name')){
        $response = new Response();
        $content = $app['twig']->render('error.twig', [
            'message' => 'You must be logged',
            'logejat' => false
        ]);
        $response->setContent($content);
        $response->setStatusCode(Response::HTTP_FORBIDDEN);
        return $response;
    }
};


$app->get('/', 'SilexApp\\Controller\\TaskController::indexAction');
$app->get('/edit', 'SilexApp\\Controller\\TaskController::editProfile')->before($before);

$app->get('/test', 'SilexApp\\Controller\\BaseController::indexAction');
$app->get('/admin', 'SilexApp\\Controller\\BaseController::adminAction')->before($before);
$app->get('/iniciarSession/{name}', 'SilexApp\\Controller\\BaseController::iniciarSession');
$app->get('/cerrarSession', 'SilexApp\\Controller\\BaseController::cerrarSession');

$app->get('/register', 'SilexApp\\Controller\\TaskController::registerUser');
$app->get('/logIn', 'SilexApp\\Controller\\TaskController::LogIn');
$app->get('/newPost', 'SilexApp\\Controller\\TaskController::newPost')->before($before);
$app->get('/galeria', 'SilexApp\\Controller\\TaskController::galeria')->before($before);



$app->post('/DBeditProfile', 'SilexApp\\Controller\\DBController::DBeditProfile');
$app->match('/DBlogin','SilexApp\\Controller\\DBController::DBlogin');
$app->post('/DBRegister','SilexApp\\Controller\\DBController::DBRegister');
$app->post('/DBnewPost','SilexApp\\Controller\\DBController::DBnewPost');



