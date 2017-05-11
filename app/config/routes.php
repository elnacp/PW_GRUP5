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
$app->get('/eliminar/{id}','SilexApp\\Controller\\TaskController::eliminarImatge')->before($before);
$app->get('/editar/{id}','SilexApp\\Controller\\TaskController::editarImatge')->before($before);
$app->get('/visualitzacioImatge/{id}','SilexApp\\Controller\\TaskController::visualitzacioImatge');
$app->get('/like/{id}/{usuari_log}','SilexApp\\Controller\\TaskController::likeImage')->before($before);

//$app->get('/comment/{usuari_log}','SilexApp\\Controller\\FunctionsController::commentImage' );
$app->get('/likeHome/{id}/{usuari_log}','SilexApp\\Controller\\FunctionsController::likeHome')->before($before);
$app->post('/comentari/{id}/{usuari_log}','SilexApp\\Controller\\FunctionsController::comentari')->before($before);
$app->get('/comentaris','SilexApp\\Controller\\FunctionsController::comentarisUser')->before($before);

$app->get('/eliminarComment/{id}','SilexApp\\Controller\\ControllerComments::eliminarComentari')->before($before);
$app->get('/editarComment/{id}','SilexApp\\Controller\\ControllerComments::editarComentari')->before($before);
$app->post('/nouComentari/{id}', 'SilexApp\\Controller\\ControllerComments::nouComentari')->before($before);

$app->post('/DBeditProfile', 'SilexApp\\Controller\\DBController::DBeditProfile');
$app->match('/DBlogin','SilexApp\\Controller\\DBController::DBlogin');
$app->post('/DBRegister','SilexApp\\Controller\\DBController::DBRegister');
$app->post('/DBnewPost','SilexApp\\Controller\\DBController::DBnewPost');
$app->post('/DBeditImage/{id}', 'SilexApp\\Controller\\DBController::DBeditImage');
$app->post('/ValidateUser', 'SilexApp\\Controller\\DBController::validateUser');


