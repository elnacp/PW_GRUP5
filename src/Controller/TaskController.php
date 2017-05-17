<?php

namespace SilexApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SilexApp\Model\Repository\UserTasks;
use SilexApp\Model\Repository\Ajax;
use SilexApp\Model\Repository\UpdateBaseService;
use Symfony\Component\HttpFoundation\RedirectResponse;


class TaskController{
    public function indexAction(Application $app){
        $repo = new UserTasks($app['db']);
        $log = false;
        if($app['session']->has('name')){
            $log = true;
        }

        $usuari =  $app['session']->get('name');

        $imgMesVistes = $repo->home1($log,$usuari,"0");
        if(!$app['session']->has('name')) {
            $content = $app['twig']->render('hello.twig', [
                'logejat' => false,
                'username' => '',
                'image' => null,
                'dades' => $imgMesVistes
                //'data' => $data
            ]);

        }else{
            $aux = new UpdateBaseService($app['db']);
            $info = $aux->getUserInfo($app['session']->get('name'));
            list($name, $img) = explode("!=!", $info);
            $content = $app['twig']->render('hello.twig', [
                'logejat' => true,
                'username' => $usuari,
                'image' => $img,
                'dades' => $imgMesVistes
                //'data' => $data
            ]);

        }
        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);
        return $response;

    }

    public function editProfile(Application $app){
        $sql = "SELECT * FROM logejat";
        $usuari = $app['db']->fetchAssoc($sql);
        $id = $usuari['user_id'];
        $sql = "SELECT * FROM usuari WHERE id = ?";
        $usuari = $app['db']->fetchAssoc($sql, array((int)$id));
        $content = $app['twig']->render('editProfile.twig', [
            'username' => $usuari['username'],
            'birthdate' => $usuari['birthdate'],
            'imagen' => $usuari['img_path'],
            'image'=>$usuari['img_path'],
            'logejat' => true,

        ]);
        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);
        return $response;
    }

    public function registerUser(Application $app){
        $content = $app['twig']->render('registerUser.twig',[
            'logejat' => false,
            'imagen' => null,
            'username' => '',
            'image' => null,
            'message' => null
        ] );
        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);
        return $response;
    }

    public function LogIn(Application $app){
        $content = $app['twig']->render('LogIn.twig',[
            'logejat' => false,
            'message' => null,
            'imagen' => null,
            'username' => '',
            'image' => null
        ] );
        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);
        return $response;
    }

    public function newPost(Application $app){
        $aux = new UpdateBaseService($app['db']);
        $info = $aux->getUserInfo($app['session']->get('name'));
        list($name, $img) = explode("!=!", $info);
        $content = $app['twig']->render('newPost.twig', [
            'logejat' => true,
            'message' => null,
            'image' => $img,
            'username' => $name

        ]);
        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);
        return $response;
    }

    public function galeria(Application $app){
        $repo = new UserTasks($app['db']);
        $dades = $repo->dadesImatges("0");
        $aux = new UpdateBaseService($app['db']);
        $info = $aux->getUserInfo($app['session']->get('name'));
        list($name, $img) = explode("!=!", $info);
        $content = $app['twig']->render('galeria.twig', [
            'logejat' => true,
            'dades' => $dades,
            'message' => NULL,
            'image'=> $img,
            'username' => $name

        ]);
        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);
        return $response;
    }


    public function editarImatge(Application $app, $id){
        $sql = "SELECT * FROM imatge WHERE id=$id";
        $imatge = $app['db']->fetchAssoc($sql);

        $estat = "";
        if($imatge['private'] == 1){
            $estat = "checked";
        }
        $aux = new UpdateBaseService($app['db']);
        $info = $aux->getUserInfo($app['session']->get('name'));
        list($name, $img) = explode("!=!", $info);
        $content = $app['twig']->render('editarImatge.twig', [
            'logejat' => true,
            'titol' => $imatge['title'],
            'privada' => $estat,
            'username' =>$name,
            'sizeImage'=>$imatge['sizeImage'],
            'id' => $id,
            'image' => $img
        ]);
        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);
        return $response;
    }

    public function eliminarImatge(Application $app, $id)
    {
        //echo('alert("Desea eliminar la foto?"');
        $repo = new UserTasks($app['db']);
        $repo->deleteImage($id);
        $dades = $repo->dadesImatges("eliminado");
        $aux = new UpdateBaseService($app['db']);
        $info = $aux->getUserInfo($app['session']->get('name'));
        list($name, $img) = explode("!=!", $info);
        $content = $app['twig']->render('galeria.twig', [
            'logejat' => true,
            'dades' => $dades,
            'message' => 'Se ha eliminado correctamente!',
            'username' => $name,
            'image' => $img

        ]);
        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);
        return $response;
    }

    public function visualitzacioImatge(Application $app, $id){
        $response = new Response();
        $repo = new UserTasks($app['db']);
        $privada = $repo->incrementarVisites($id);
        $logejat = false;
        $name = '';
        $img = null;

        if($app['session']->has('name')){
            $logejat = true;
            $aux = new UpdateBaseService($app['db']);
            $info = $aux->getUserInfo($app['session']->get('name'));
            list($name, $img) = explode("!=!", $info);
        }

        if($privada == 1){
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $content = $app['twig']->render('error.twig', [
                    'message' => ' Imagen privada',
                    'logejat' => $logejat,
                    'username' => $name,
                    'image' => $img
                ]
            );
            $response->setContent($content);
            return $response;
        }else{
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $sql = "SELECT * FROM imatge WHERE id = ?";
            $s = $app['db']->fetchAssoc($sql, array((int)$id));
            $autor = $s['user_id'];
            $birthdate = $s['created_at'];
            list($yy, $mm, $daux) = explode("-", $birthdate);
            list($dd, $taux) = explode(" ", $daux);
            list($hh, $min, $ss) = explode(":", $taux);

            if ((date("Y") == $yy) && (date("m") == $mm) && (date("d") == $dd)){
                if (date("h") == $hh){
                    $birthdate = date("i") - $min;
                    $birthdate = 'Hace '.$birthdate.' minutos';
                }
                if(date("h")>$hh){
                    $birthdate = date("h") - $hh;
                    $birthdate = 'Hace '.$birthdate.' horas';
                }
            }

            if((date("Y") == $yy) && (date("m") == $mm) && (date("d") > $dd)){
                $birthdate = date("d") - $dd;
                $birthdate = 'Hace '.$birthdate.' dias';
            }
            if((date("Y") == $yy) && (date("m") > $mm)){
                $birthdate = date("d") - $dd;
                if ($birthdate > 30){
                    $birthdate = date("m") - $mm;
                    $birthdate = 'Hace '.$birthdate.' meses';
                }
            }

            if(date("Y")>$yy){
                $birthdate = date("Y") - $yy;
                $birthdate = 'Hace '.$birthdate.' años';
            }

            $image= $s['img_path'];
            $sql1 = "SELECT username FROM usuari WHERE id = ?";
            $s2 = $app['db']->fetchAssoc($sql1, array((int)$autor));
            $sql3 = "SELECT * FROM usuari WHERE id = ?";
            $s3 = $app['db']->fetchAssoc($sql3, array((int)$autor));
            $usuari =  $app['session']->get('name');
            $sql4 = "SELECT count(*) as total FROM likes WHERE image_id = ?";
            $l = $app['db']->fetchAssoc($sql4, array((int)$s['id']));
            $likes = $l['total'];
            $content = $app['twig']->render('imatgePublica.twig', [
                    'id' => $id,
                    'usuari_log' => $usuari,
                    'username' =>$usuari,
                    'image' =>'.'.$s3['img_path'],
                    'logejat' => $logejat,
                    'autor' => $s2['username'],
                    'title' => $s['title'],
                    'dia' => $birthdate,
                    'visites' => $s['visits'],
                    'likes' => $likes,
                    'message' => null,
                    'imgPerfil' => '.'.$s3['img_path'],
                    'imgPost' => $image


                ]
            );

            $response->setContent($content);
            return $response;
        }




    }


}

