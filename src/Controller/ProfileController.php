<?php

namespace SilexApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SilexApp\Model\Repository\UserTasks;
use SilexApp\Model\Repository\EmailSender;
use SilexApp\Model\Repository\Ajax;
use Symfony\Component\HttpFoundation\RedirectResponse;



class ProfileController{

    public function DBeditProfile(Application $app, Request $request)
    {
        $name = htmlspecialchars($_POST['nickname']);
        $birth = htmlspecialchars($_POST['edad']);
        $pass1 = htmlspecialchars($_POST['password1']);
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
            $inserit = $repo->logejarUsuari($name);

            if (!$inserit){
                $response->setStatusCode(Response::HTTP_FORBIDDEN);
                $content = $app['twig']->render('LogIn.twig', [
                        'message' => 'Usuario no activado',
                        'logejat' => false,
                        'username' => '',
                        'image' => null
                    ]
                );
                $response->setContent($content);
                return $response;
            }

            $id = $repo->getUserId($name);
            $act_name = $repo->getName($id);
            $url = '/iniciarSession/' . $act_name;
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
        $exists = $repo->checkUser($nickname, $email);
        $response = new Response();


        if (!$exists) {
            $repo->RegisterUser($nickname, $email, $birthdate, $password, $img);
            $id = $repo->getUserId($nickname);
            $sender = new EmailSender();
            if ($sender->sendEmail($app,$email,$id)){

                $response->setStatusCode(Response::HTTP_OK);

                $content = $app['twig']->render('registerUser.twig', [
                        'message' => 'Email enviado correctamente. Esperando Activación.',
                        'logejat' => false,
                        'username' => '',
                        'image' => null,

                    ]
                );

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
                'message' => 'Este usuario ya ha sido registrado',
                'logejat' => false,
                'username' => '',
                'image' => null

            ]);
        }
        $response->setContent($content);

        return $response;
    }
}