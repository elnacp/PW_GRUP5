<?php

namespace SilexApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SilexApp\Model\Repository\UserTasks;
use Symfony\Component\HttpFoundation\RedirectResponse;
use SilexApp\Model\Repository\EmailSender;
use SilexApp\Model\Repository\UpdateBaseService;



class DBController
{
    public function DBlogin(Application $app, Request $request)
    {
        $response = new Response();
        $name = htmlspecialchars($_POST['nickname']);
        $password = htmlspecialchars($_POST['password']);
        $repo = new UserTasks($app['db']);
        $exists = $repo->validateUser($name, $password);

        // Si no troba el usuari error en el propi formulari
        if (!$exists) {
            //echo("hello");
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $content = $app['twig']->render('LogIn.twig', [
                    'message' => 'User not found',
                    'logejat' => false,
                    'username' => '',
                    'image' =>null
                ]
            );
            $response->setContent($content);
            return $response;
        } else { // si troba usuari el redirigeix cap a home logejat

            //echo("adios");
            $repo->logejarUsuari($name);
            $id = $repo->getUserId($name);
            $act_name = $repo->getName($id);
            $url = '/iniciarSession/' . $act_name;
            return new RedirectResponse($url);
        }

    }

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


    public function DBeditProfile(Application $app, Request $request)
    {
        $name = htmlspecialchars($_POST['nickname']);
        $birth = htmlspecialchars($_POST['edad']);
        $pass1 = htmlspecialchars($_POST['password1']);
        //$img1 = $_POST['imgP'];
        /** @var UploadedFile $img */
        $img = $request->files->get('newProfileImg');

        $repo = new UserTasks($app['db']);
        if ($img != NULL){
            $repo->deleteActualPic($name);
            move_uploaded_file($img->getPathname(), './assets/uploads/' . $name . date("m-d-y"). date("h:i:sa") . ".jpg");
            $img = './assets/uploads/' . $name . date("m-d-y"). date("h:i:sa") . ".jpg";
        }else{
            $img = $repo->getActualProfilePic($name,$img);
        }

        $repo->validateEditProfile($name, $birth, $pass1, $img);
        $response = new Response();
        $content = $app['twig']->render('editProfile.twig', [
                'logejat' => true,
                'username' => $name,
                'birthdate' =>$birth,
                'image' =>$img
            ]
        );
        $response->setContent($content);
        return $response;
    }


    public function DBRegister(Application $app, Request $request)
    {
        $nickname = $request->get('nickname');
        $email = $request->get('email');
        $birthdate = $request->get('edad');
        $password = $request->get('password');
        /** @var UploadedFile $img */
        $img = $request->files->get('ProfileImg');
        if ($img == NULL){
            $img = './assets/img/' . "User.jpg";
        }else{
            move_uploaded_file($img->getPathname(), './assets/uploads/' . $nickname . date("m-d-y"). date("h:i:sa") . ".jpg");
            $img = './assets/uploads/' . $nickname . date("m-d-y"). date("h:i:sa") . ".jpg";
        }

        $repo = new UserTasks($app['db']);
        $exists = $repo->checkUser($nickname, $email);
        $response = new Response();


        if (!$exists) {
                $repo->RegisterUser($nickname, $email, $birthdate, $password, $img);
                $id = $repo->getUserId($nickname);
                $sender = new EmailSender();
                if ($sender->sendEmail($app,$email, $id)){

                    $response->setStatusCode(Response::HTTP_OK);
                    $content = $app['twig']->render('error.twig', [
                        'message' => 'Email Enviado correctamente',
                        'logejat' => false,
                        'username' => '',
                        'image' => null

                    ]);

                }else{

                    $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                    $content = $app['twig']->render('error.twig', [
                        'message' => 'No se ha podido enviar el email',
                        'logejat' => false,
                        'username' => '',
                        'image' => null

                    ]);
                }

        }else{
            $response->setStatusCode(Response::HTTP_NOT_ACCEPTABLE);
            $content = $app['twig']->render('error.twig', [
                'message' => 'Este email ya ha sido registrado',
                'logejat' => false,
                'username' => '',
                'image' => null

            ]);
        }
        $response->setContent($content);

        return $response;
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
                    'logejat' => true,
                    'username' => $usuari,
                    'image' => $img->getPathname()
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
        $ok = $repo->DBnewPost($title, $img, $private);
        $response = new Response();
        $repo = new UserTasks($app['db']);
        $log = false;
        if($app['session']->has('name')){
            $log = true;
        }
        $usuari =  $app['session']->get('name');
        $imgMesVistes = $repo->home1($log,$usuari, "0");


            if ($ok) {
                $content = $app['twig']->render('hello.twig', [
                        'logejat' => true,
                        'dades' => $imgMesVistes,
                        'image' => $img,
                        'username' => $usuari
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
    public function publicProfile(Application $app, Request $request, $username){
        $opcio = htmlspecialchars($request->get('opcio'));
        //f$response = new Response();
        $repo = new UserTasks($app['db']);
        $log = false;
        if($app['session']->has('name')){
            $log = true;
        }
        //$response->setStatusCode(Response::HTTP_NOT_FOUND);
        $sql = "SELECT id FROM usuari WHERE username = ?";
        $s = $app['db']->fetchAssoc($sql, array($username));
        $id = $s['id'];
        $repo->imatgesUsuari($id);

        $imatgesPublic = $repo->imatgesPerfil($username, $opcio);
        $dadesUsuari = $repo->dadesUsuari($username,$id);
       // $response->setStatusCode(Response::HTTP_NOT_FOUND);
        $aux = new UpdateBaseService($app['db']);
        $info = $aux->getUserInfo($app['session']->get('name'));
        list($name, $img) = explode("!=!", $info);
        $content = $app['twig']->render('publicProfile.twig',[
            'logejat' => $log,
            'imatgesPublic' =>$imatgesPublic,
            'dadesUsuari' =>$dadesUsuari,
            'username' => $username,
            'image' => '.'.$img
        ]);
        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);
        return $response;
    }


}