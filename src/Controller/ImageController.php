<?php

namespace SilexApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SilexApp\Model\Repository\UserTasks;
use SilexApp\Model\Repository\UpdateBaseService;
use Symfony\Component\HttpFoundation\RedirectResponse;


class ImageController{

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
            'image' => $img,
            'actual' => '.'.$imatge['img_path']
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
        $url = '/galeria';
        return new RedirectResponse($url);
    }

    public function visualitzacioImatge(Application $app, $id)
    {
        $response = new Response();
        $repo = new UserTasks($app['db']);
        $privada = $repo->incrementarVisites($id);
        $logejat = false;
        $name = '';
        $img = null;

        if ($app['session']->has('name')) {
            $logejat = true;
            $aux = new UpdateBaseService($app['db']);
            $info = $aux->getUserInfo($app['session']->get('name'));
            list($name, $img) = explode("!=!", $info);
        }

        if ($privada == 1) {
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            $content = $app['twig']->render('error.twig', [
                    'message' => ' Imagen privada',
                    'logejat' => $logejat,
                    'username' => $name,
                    'image' => $img
                ]
            );
            $response->setContent($content);
            return $response;
        } else {
            $sql = "SELECT * FROM imatge WHERE id = ?";
            $s = $app['db']->fetchAssoc($sql, array((int)$id));
            $autor = $s['user_id'];
            $birthdate = $s['created_at'];
            list($yy, $mm, $daux) = explode("-", $birthdate);
            list($dd, $taux) = explode(" ", $daux);
            list($hh, $min, $ss) = explode(":", $taux);

            if ((date("Y") == $yy) && (date("m") == $mm) && (date("d") == $dd)) {
                if (date("H") == $hh) {
                    $birthdate = date("i") - $min;
                    $birthdate = 'Hace ' . $birthdate . ' minutos';
                }
                if (date("H") > $hh) {
                    $birthdate = date("H") - $hh;
                    $birthdate = 'Hace ' . $birthdate . ' horas';
                }
            }

            if ((date("Y") == $yy) && (date("m") == $mm) && (date("d") > $dd)) {
                $birthdate = date("d") - $dd;
                $birthdate = 'Hace ' . $birthdate . ' dias';
            }
            if ((date("Y") == $yy) && (date("m") > $mm)) {
                $birthdate = date("d") - $dd;
                if ($birthdate > 30) {
                    $birthdate = date("m") - $mm;
                    $birthdate = 'Hace ' . $birthdate . ' meses';
                }
            }

            if (date("Y") > $yy) {
                $birthdate = date("Y") - $yy;
                $birthdate = 'Hace ' . $birthdate . ' años';
            }

            $image = '.' . $s['img_path'];
            $sql1 = "SELECT username FROM usuari WHERE id = ?";
            $s2 = $app['db']->fetchAssoc($sql1, array((int)$autor));
            $sql3 = "SELECT * FROM usuari WHERE id = ?";
            $s3 = $app['db']->fetchAssoc($sql3, array((int)$autor));
            $usuari = $app['session']->get('name');
            $sql4 = "SELECT count(*) as total FROM likes WHERE image_id = ?";
            $l = $app['db']->fetchAssoc($sql4, array((int)$s['id']));
            $likes = $l['total'];
            $content = $app['twig']->render('imatgePublica.twig', [
                    'id' => $id,
                    'usuari_log' => $usuari,
                    'username' => $usuari,
                    'image' => '.' . $s3['img_path'],
                    'logejat' => $logejat,
                    'autor' => $s2['username'],
                    'title' => $s['title'],
                    'dia' => $birthdate,
                    'visites' => $s['visits'],
                    'likes' => $likes,
                    'message' => null,
                    'imgPerfil' => '.' . $s3['img_path'],
                    'imgPost' => $image


                ]
            );

            $response->setContent($content);
            return $response;
        }
    }
}