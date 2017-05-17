<?php

namespace  SilexApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        $url = "/";
        return new RedirectResponse($url);

    }

    public function cerrarSession(Application $app){
        $sql = "DELETE  FROM logejat";
        //setcookie("guest", "", time() - 3600 * 24 * 7);
        $app['db']->query($sql);
        $app['session']->remove('name');
        $url = "/";
        return new RedirectResponse($url);
    }


    public function DBeditImage(Application $app, Request $request, $id)
    {
        $title = htmlspecialchars($request->get('title'));
        $img = $request->files->get('imagen');
        $privada = htmlspecialchars($request->get('privada'));

        if ($privada === "on") {
            $private = 1;
        } else {
            $private = 0;
        }

        $repo = new UserTasks($app['db']);

        if ($img != NULL){
            $repo->deleteActualPic($title);
            move_uploaded_file($img->getPathname(), './assets/uploads/' . $title . date("m-d-y"). date("h:i:sa") . ".jpg");
            $img = './assets/uploads/' . $title . date("m-d-y"). date("h:i:sa") . ".jpg";
        }else{
            $img = $repo->getActualPostImg($id,$img);
        }
        $repo->editInformation($title, $img, $private, $id);
        $dades = $repo->dadesImatges();
        $aux = new UpdateBaseService($app['db']);
        $info = $aux->getUserInfo($app['session']->get('name'));
        list($name, $img) = explode("!=!", $info);
        $content = $app['twig']->render('galeria.twig', [
            'logejat' => true,
            'dades' => $dades,
            'message' => 'Se ha editado correctamente!',
            'username' => $name,
            'image' => $img
        ]);

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);
        return $response;


    }

}