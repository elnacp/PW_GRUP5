<?php

namespace SilexApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SilexApp\Model\Repository\UserTasks;
use Symfony\Component\HttpFoundation\RedirectResponse;


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
            $content = $app['twig']->render('error.twig', [
                    'message' => 'User not found',
                    'logejat' => false
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

    public function save_image($inPath,$outPath)
    { //Download images from remote server
        $in=    fopen($inPath, "rb");
        $out=   fopen($outPath, "wb");
        while ($chunk = fread($in,8192))
        {
            fwrite($out, $chunk, 8192);
        }
        fclose($in);
        fclose($out);
    }




    public function DBeditProfile(Application $app)
    {
        $name = htmlspecialchars($_POST['nickname']);
        $birth = htmlspecialchars($_POST['edad']);
        $pass1 = htmlspecialchars($_POST['password1']);
        $img1 = $_POST['imgP'];

        //$pass2 = htmlspecialchars($_POST['password2']);
        //$path = htmlspecialchars($_POST['files[]']);

        $repo = new UserTasks($app['db']);
        $repo-> validateEditProfile($name, $birth, $pass1, $img1);
        $response = new Response();
        $content = $app['twig']->render('error.twig', [
                'logejat' => true,
                'message' => 'hola'
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
        $img = $request->get('ProfileImg');

        //copy($img, '/../web/assets/img'.$img);

        //save_image($img,$img.'.jpg');


        $repo = new UserTasks($app['db']);
        $exists = $repo->checkUser($nickname);
        $response = new Response();

        if (!$exists) {

            /*$aleatorio = uniqid(); //Genera un id único para identificar la cuenta a traves del correo.

            $mensaje = "Registro en tuweb.com\n\n";
            $mensaje .= "Estos son tus datos de registro:\n";
            $mensaje .= "Usuario: $nickname.\n";
            $mensaje .= "Contraseña: $password.\n\n";
            $mensaje .= "Debes activar tu cuenta pulsando este enlace: http://www.tuweb.com/activacion.php?id=".$aleatorio;

            $cabeceras = 'From: webmaster@example.com' . "\r\n" .
                'Reply-To: webmaster@example.com' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

            $sendMail = mail($email,'Activar cuenta',$mensaje, $cabeceras);
            if($sendMail){*/
                $repo->RegisterUser($nickname, $email, $birthdate, $password, $img);
                $response->setStatusCode(Response::HTTP_OK);

                $content = $app['twig']->render('validate.twig', [
                        'message' => 'Activa tu usuario mediante el siguiente link:',
                        'logejat' => false,
                        'name' => $nickname
                    ]
                );
            /*}else{
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                $content = $app['twig']->render('error.twig', [
                        'message' => 'No se ha podido enviar el email',
                        'logejat' => false

                ]);
            }*/
        } else {
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

    public function DBnewPost(Application $app, Request $request){
        $title = htmlspecialchars($request->get('title'));
        $imgName = htmlspecialchars($request->files->get('imagen'));
        $privada = htmlspecialchars($request->get('privada'));
        //var_dump($privada);
        var_dump($request->files->get('imagen'));
        //var_dump($request->files);
        if ($privada ==="privada"){
            $private = 1;
        }else{
            $private = 0;
        }
        $folder = "/assets/img/";
        $path_name = $imgName;
        //var_dump($path_name);
        $repo = new UserTasks($app['db']);
        $ok = $repo->DBnewPost($title, $path_name, $private);
        $response = new Response();
        if($ok) {
            $content = $app['twig']->render('hello.twig', [
                    'logejat'=> true
                ]
            );
        }
        $response->setContent($content);

        return $response;
    }


    public function validateUser(Application $app, Request $request)
    { //Download images from remote server
        $nickname = $request->get('toVal');
        $repo = new UserTasks($app['db']);
        $ok = $repo->ActivateUser($nickname);
        $response = new Response();

        if($ok){
            $response->setStatusCode(Response::HTTP_OK);

            $content = $app['twig']->render('error.twig', [
                    'message' => 'usuario activado correctamente',
                    'logejat' => false
                ]
            );

        }else{
            $response->setStatusCode(Response::HTTP_ALREADY_REPORTED);
            $content = $app['twig']->render('error.twig', [
                    'message' => 'No se ha podido validar el usuario',
                    'logejat' => false
                ]
            );

        }
        $response->setContent($content);
        return $response;
    }

}