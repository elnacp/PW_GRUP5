<?php

namespace SilexApp\Controller;

use Silex\Application;
use SilexApp\Model\Repository\resampleService;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SilexApp\Model\Repository\UserTasks;
use Symfony\Component\HttpFoundation\RedirectResponse;
use SilexApp\Model\Repository\EmailSender;
use SilexApp\Model\Repository\UpdateBaseService;


class DBController
{
    public function DBnewPost(Application $app, Request $request)
    {
        $title = htmlspecialchars($request->get('title'));
        $privada = htmlspecialchars($request->get('privada'));
        $size = htmlspecialchars($request->get('size'));
        /** @var UploadedFile $img */
        $img = $request->files->get('ProfileImg');

        $response = new Response();
        $repo = new UserTasks($app['db']);

        if ($img == null) {
            $usuari = $app['session']->get('name');
            $img = $repo->getActualProfilePic($usuari, null);
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $aux = new UpdateBaseService($app['db']);
            $info = $aux->getUserInfo($app['session']->get('name'));
            list($name, $img2) = explode("!=!", $info);
            $content = $app['twig']->render('newPost.twig', [
                    'message' => 'IMAGE NOT FOUND',
                    'logejat' => true,
                    'username' => $usuari,
                    'image' => $img2
                ]
            );
            $response->setContent($content);
            return $response;
        } else {
            if ($size == "gran") {
                $size = 400;
            }
            if ($size == "petita") {
                $size = 100;
            }

            $this->uploadImageResize($img, $title, $size);

            if ($privada === "on") {
                $private = 1;
            } else {
                $private = 0;
            }
            $img_d = $img_d = './assets/uploads/' . $size . $title . date("m-d-y") . date("h:i:sa") . ".jpg";

            $repo = new UserTasks($app['db']);
            $ok = $repo->DBnewPost($title, $img_d, $private, $size);

            if ($ok) {
                $url = "/";
                return new RedirectResponse($url);
            }
        }
        //return $response;
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
                    'logejat' => false,
                    'username' => '',
                    'image' => null

                ]
            );

        } else {
            $response->setStatusCode(Response::HTTP_ALREADY_REPORTED);
            $content = $app['twig']->render('error.twig', [
                    'message' => 'No se ha podido validar el usuario ' . $nickname,
                    'logejat' => false,
                    'username' => '',
                    'imagen' => null
                ]
            );

        }
        $response->setContent($content);
        return $response;
    }


    public function publicProfile(Application $app, Request $request, $username)
    {
        $opcio = htmlspecialchars($request->get('opcio'));
        //f$response = new Response();
        $repo = new UserTasks($app['db']);
        $log = false;
        if ($app['session']->has('name')) {
            $log = true;
        }
        //$response->setStatusCode(Response::HTTP_NOT_FOUND);
        $sql = "SELECT id FROM usuari WHERE username = ?";
        $s = $app['db']->fetchAssoc($sql, array($username));
        $id = $s['id'];
        $repo->imatgesUsuari($id);

        $imatgesPublic = $repo->imatgesPerfil($username, $opcio);
        $dadesUsuari = $repo->dadesUsuari($username, $id);
        // $response->setStatusCode(Response::HTTP_NOT_FOUND);
        $aux = new UpdateBaseService($app['db']);
        $info = $aux->getUserInfo($app['session']->get('name'));
        list($name, $img) = explode("!=!", $info);
        $content = $app['twig']->render('publicProfile.twig', [
            'logejat' => $log,
            'imatgesPublic' => $imatgesPublic,
            'dadesUsuari' => $dadesUsuari,
            'username' => $username,
            'image' => '.' . $img
        ]);
        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);
        return $response;
    }

    /**
     * @param $img: Imatge de FILE.
     * @param $title: Nom de la imatge.
     * @param $size: Mida (400 o 100).
     */
    public function uploadImageResize($img, $title, $size){

        $width = 400;
        $height = 300;

        if ($size == 100) {
            $width = 100;
            $height = 100;
        }

        $img_o = 'assets/uploads/Original' . $title . date("m-d-y") . date("h:i:sa") . ".jpg";

        if(is_string($img)){
            move_uploaded_file($img, $img_o);
        }else{
            move_uploaded_file($img->getPathname(), $img_o);
        }

        $img_d = './assets/uploads/' . $size . $title . date("m-d-y") . date("h:i:sa") . ".jpg";

        $resize = new resampleService();
        $resize->resizeImage($img_o, $img_d, $width, $height);
    }


}