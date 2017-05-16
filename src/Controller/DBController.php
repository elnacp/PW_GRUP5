<?php

namespace SilexApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SilexApp\Model\Repository\UserTasks;
use Symfony\Component\HttpFoundation\RedirectResponse;
use SilexApp\Model\Repository\EmailSender;


class DBController
{

    public function save_image($inPath, $outPath)
    { //Download images from remote server
        $in = fopen($inPath, "rb");
        $out = fopen($outPath, "wb");
        while ($chunk = fread($in, 8192)) {
            fwrite($out, $chunk, 8192);
        }
        fclose($in);
        fclose($out);
    }



    public function DBnewPost(Application $app, Request $request)
    {
        $title = htmlspecialchars($request->get('title'));
        $privada = htmlspecialchars($request->get('privada'));
        $size = htmlspecialchars($request->get('size'));
        /** @var UploadedFile $img */
        $img = $request->files->get('ProfileImg');
        //var_dump($size);
        $response = new Response();

        $repo = new UserTasks($app['db']);
        if ($img == NULL){
            $usuari =  $app['session']->get('name');
            $img = $repo ->getActualProfilePic($usuari, null);
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $content = $app['twig']->render('newPost.twig', [
                    'message' => 'IMAGE NOT FOUND',
                    'logejat' => false,
                    'username' => $usuari,
                    'imagen' => $img
                ]
            );
            $response->setContent($content);
            return $response;
        }else{
            move_uploaded_file($img->getPathname(), './assets/uploads/' . $title . date("m-d-y") .date("h:i:sa") . ".jpg");
            $img = './assets/uploads/' . $title . date("m-d-y") .date("h:i:sa"). ".jpg";

            if ($privada === "on") {
                $private = 1;
            } else {
                $private = 0;
            }
            if($size === "gran"){
                $size = 400;
            }else{
                $size = 100;
            }
        $repo = new UserTasks($app['db']);
        $ok = $repo->DBnewPost($title, $img, $private, $size);
        $response = new Response();
        $repo = new UserTasks($app['db']);
        $log = false;
        if($app['session']->has('name')){
            $log = true;
        }
        $usuari =  $app['session']->get('name');
        $imgMesVistes = $repo->home1($log,$usuari);


            if ($ok) {
                $content = $app['twig']->render('hello.twig', [
                        'logejat' => true,
                        'dades' => $imgMesVistes
                    ]
                );
            }
            $response->setContent($content);
        }
        return $response;
    }


    public function validateUser(Application $app, Request $request)
    {
        $nickname = $request->get('nickname');
        $repo = new UserTasks($app['db']);
        $ok = $repo->ActivateUser($nickname);
        $response = new Response();

        if ($ok) {
            $response->setStatusCode(Response::HTTP_OK);

            $content = $app['twig']->render('error.twig', [
                    'message' => 'usuario activado correctamente' . $nickname,
                    'logejat' => false
                ]
            );

        } else {
            $response->setStatusCode(Response::HTTP_ALREADY_REPORTED);
            $content = $app['twig']->render('error.twig', [
                    'message' => 'No se ha podido validar el usuario ' . $nickname,
                    'logejat' => false
                ]
            );

        }
        $response->setContent($content);
        return $response;
    }





}