<?php

namespace SilexApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SilexApp\Model\Repository\UserTasks;
use SilexApp\Model\Repository\Ajax;

class ProfileController{
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
}