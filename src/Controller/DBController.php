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
                ]
            );
            $response->setContent($content);
            return $response;
        } else { // si troba usuari el redirigeix cap a home logejat

            //echo("adios");
            $repo->logejarUsuari($name);
            $url = '/iniciarSession/' . $name;
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

        //$pass2 = htmlspecialchars($_POST['password2']);
        //$path = htmlspecialchars($_POST['files[]']);

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
                'imagen' =>$img
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
        $exists = $repo->checkUser($nickname);
        $response = new Response();


        if (!$exists) {
            //$sender = new EmailSender();
            //if ($sender->sendEmail($email)){$repo->RegisterUser($nickname, $email, $birthdate, $password, $img);
            $repo->RegisterUser($nickname, $email, $birthdate, $password, $img);
            $response->setStatusCode(Response::HTTP_OK);

            $content = $app['twig']->render('validate.twig', [
                'message' => 'Email enviado correctamente:',
                'logejat' => false,
                'name' => $nickname,
            ]);
        }else{
            $response->setStatusCode(Response::HTTP_ALREADY_REPORTED);
            $content = $app['twig']->render('error.twig', [
                     'message' => 'El usuario ya existe',
                     'logejat' => false
                ]
              );
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
        //llamar al resize image
        //var_dump($size);
        var_dump($img);
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
            $path_img = './assets/uploads/' . $title . '.jpeg';
            $path_direct = './assets/uploads/';
            $resize = new resampleService();
            //$resize->resizeImage($img->getPathname(),$path_direct.$title,400,300);
            //$resize->resizeImage($img->getPathname(),$path_direct.$title,100,100);
            //move_uploaded_file($newImages4, './assets/uploads/' . $title . "400" . date("m-d-y") .date("h:i:sa") . ".jpg");
            //move_uploaded_file($newImages1, './assets/uploads/' . $title . "100" . date("m-d-y") .date("h:i:sa") . ".jpg");

            if ($size === "gran"){
                $direccioImg = './assets/uploads';
                $resize->resizeImage($path_img,$direccioImg,400,300);
                $size = 400;
            }else{
                $direccioImg = './assets/uploads';
                $resize->resizeImage($path_img,$direccioImg,100,100);
                $size = 100;
            }


            if ($privada === "on") {
                $private = 1;
            } else {
                $private = 0;
            }

            $ok = $repo->DBnewPost($title, $img, $private, $size);
            $repo = new UserTasks($app['db']);
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

        //var_dump($path_name);
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
        $content = $app['twig']->render('galeria.twig', [
            'logejat' => true,
            'dades' => $dades,
            'message' => 'Se ha editado correctamente!'
        ]);
        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);
        return $response;


    }
    public function publicProfile(Application $app, Request $request, $username){
        $opcio = htmlspecialchars($request->get('opcio'));
        $response = new Response();
        $repo = new UserTasks($app['db']);
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
        $sql = "SELECT id FROM usuari WHERE username = ?";
        $s = $app['db']->fetchAssoc($sql, array($username));
        $id = $s['id'];
        $repo->imatgesUsuari($id);

        $imatgesPublic = $repo->imatgesPerfil($username, $opcio);
        $dadesUsuari = $repo->dadesUsuari($username,$id);
        $response->setStatusCode(Response::HTTP_NOT_FOUND);

        $content = $app['twig']->render('publicProfile.twig',[
            'logejat' => false,
            'imatgesPublic' =>$imatgesPublic,
            'dadesUsuari' =>$dadesUsuari
        ]);
        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);
        return $response;
    }


}