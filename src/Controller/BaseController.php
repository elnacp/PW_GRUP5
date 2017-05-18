<?php

namespace  SilexApp\Controller;

use Silex\Application;
use SilexApp\Model\Repository\resampleService;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SilexApp\Model\Repository\UserTasks;
use Symfony\Component\HttpFoundation\RedirectResponse;
use SilexApp\Model\Repository\EmailSender;
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
        $size = $request->get('size');

        if ($privada === "on") {
            $private = 1;
        } else {
            $private = 0;
        }

        $repo = new UserTasks($app['db']);
        $resize = new resampleService();


        if($size == "gran"){
            $size = 400;

        }

        if($size == "petita"){
            $size = 100;

        }

        $sql = "SELECT * FROM imatge WHERE id = $id";
        $d = $app['db']->fetchAssoc($sql);
        $img_antiga = $d['img_path'];

        if($size == 400){
            list($p1,$p2) = explode("400", $img_antiga);

        }
        if($size == 100){
            list($p1,$p2) = explode("100", $img_antiga);
        }

        $antiga_original = $p1.'Original'.$p2;

        if ($img != NULL){

            unlink($antiga_original);
            unlink($img_antiga);

        }else{
            if ($size != $d['sizeImage']) {
                $img = $d['img_path'];
                $img_antiga = $d['img_path'];
                if ($size == 400) {
                    list($p1, $p2) = explode("400", $img_antiga);
                }

                if ($size == 100) {
                    list($p1, $p2) = explode("100", $img_antiga);
                }
                unlink($img_antiga);

            }else{
                $img = $d['img_path'];
                $repo->editInformation($title, $img, $private, $id, $size);
                $url = '/galeria';
                return new RedirectResponse($url);
            }

        }
        $imgAux = new DBController();

        $imgAux->uploadImageResize($img, $title, $size);
        $img_d = './assets/uploads/' . $size . $title . date("m-d-y") . date("h:i:sa") . ".jpg";

        $repo->editInformation($title, $img_d, $private, $id, $size);

        $url = '/galeria';
        return new RedirectResponse($url);


    }

}