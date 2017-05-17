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
            'logejat' => false,
            'imagen' => null
        ]);
        $response->setContent($content);
        $response->setStatusCode(Response::HTTP_FORBIDDEN);
        return $response;
    }
};

$inici = function (Request $request, Application $app){
    if($app['session']->has('name')){
        $response = new Response();
        $content = $app['twig']->render('error.twig', [
            'message' => 'Ya estas logeado!',
            'logejat' => true,
            'imagen' => null
        ]);
        $response->setContent($content);
        $response->setStatusCode(Response::HTTP_FORBIDDEN);
        return $response;
    }
};

//TASK CONTROLLER
$app->get('/', 'SilexApp\\Controller\\TaskController::indexAction');
$app->get('/edit', 'SilexApp\\Controller\\TaskController::editProfile')->before($before);
$app->get('/register', 'SilexApp\\Controller\\TaskController::registerUser')->before($inici);
$app->get('/logIn', 'SilexApp\\Controller\\TaskController::LogIn')->before($inici);
$app->get('/newPost', 'SilexApp\\Controller\\TaskController::newPost')->before($before);
$app->get('/galeria', 'SilexApp\\Controller\\TaskController::galeria')->before($before);
$app->get('/reload', 'SilexApp\\Controller\\TaskController::editProfile')->before($before);
//BASE CONTROLLER
$app->get('/test', 'SilexApp\\Controller\\BaseController::indexAction');
$app->get('/admin', 'SilexApp\\Controller\\BaseController::adminAction')->before($before);
$app->get('/iniciarSession/{name}', 'SilexApp\\Controller\\BaseController::iniciarSession');
$app->get('/cerrarSession', 'SilexApp\\Controller\\BaseController::cerrarSession');
//AJAX
$app->post('/afegir','SilexApp\\Controller\\ControllerComments::afegir' );
$app->post('/post','SilexApp\\Controller\\ControllerComments::post');
//IMAGE CONTROLLER
$app->get('/eliminar/{id}','SilexApp\\Controller\\ImageController::eliminarImatge')->before($before);
$app->get('/editar/{id}','SilexApp\\Controller\\ImageController::editarImatge')->before($before);
$app->get('/visualitzacioImatge/{id}','SilexApp\\Controller\\ImageController::visualitzacioImatge');
//LIKE CONTROLLER
$app->get('/like/{id}/{usuari_log}','SilexApp\\Controller\\LikeController::likeImage')->before($before);
$app->get('/likeHome/{id}/{usuari_log}','SilexApp\\Controller\\LikeController::likeHome')->before($before);
//$app->get('/comment/{usuari_log}','SilexApp\\Controller\\FunctionsController::commentImage' );
//FUNCTIONS CONTROLLER
$app->post('/comentari/{id}/{usuari_log}','SilexApp\\Controller\\FunctionsController::comentari')->before($before);
$app->get('/comentaris','SilexApp\\Controller\\FunctionsController::comentarisUser')->before($before);
$app->get('/perfil/{username}', 'SilexApp\\Controller\\FunctionsController::publicProfile');
//CONTROLLER COMMENTS
$app->get('/eliminarComment/{id}','SilexApp\\Controller\\ControllerComments::eliminarComentari')->before($before);
$app->get('/editarComment/{id}','SilexApp\\Controller\\ControllerComments::editarComentari')->before($before);
$app->post('/nouComentari/{id}', 'SilexApp\\Controller\\ControllerComments::nouComentari')->before($before);
$app->get('/notificacions', 'SilexApp\\Controller\\ControllerComments::notificacionsUser')->before($before);
$app->get('/visualitzada/{id}','SilexApp\\Controller\\ControllerComments::notificacioVisualitzada')->before($before);
//PROFILE CONTROLLER
$app->post('/DBeditProfile', 'SilexApp\\Controller\\ProfileController::DBeditProfile')->before($before);
$app->match('/DBlogin','SilexApp\\Controller\\ProfileController::DBlogin');
$app->post('/DBRegister','SilexApp\\Controller\\ProfileController::DBRegister');

//DB CONTROLLER
$app->post('/DBnewPost','SilexApp\\Controller\\DBController::DBnewPost');
$app->post('/DBeditImage/{id}', 'SilexApp\\Controller\\BaseController::DBeditImage');
$app->post('/ValidateUser', 'SilexApp\\Controller\\DBController::validateUser');



$app->get('/reload', 'SilexApp\\Controller\\TaskController::editProfile')->before($before);
$app->match('/activateUser/{code}/{id}', 'SilexApp\\Controller\\ActivationUser::validateUser');


