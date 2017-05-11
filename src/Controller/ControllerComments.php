<?php
namespace SilexApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SilexApp\Model\Repository\UserTasks;
use Symfony\Component\HttpFoundation\RedirectResponse;


class ControllerComments
{
    public function eliminarComentari(Application $app, $id){
        $response = new Response();
        $response = new Response();
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
        $repo = new UserTasks($app['db']);
        $ok = $repo->eliminarComentari($id);
        $dades = $repo->comentarisUser();
        $content = $app['twig']->render('userComments.twig', [
                'logejat' => true,
                'comentaris' => $dades,
                'message' => $ok

            ]
        );
        $response->setContent($content);
        return $response;


    }

    public function editarComentari(Application $app, $id){
        $response = new Response();
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
        $sql = "SELECT comentari FROM comentari WHERE id = ?";
        $s = $app['db']->fetchAssoc($sql, array((int)$id));
        $dades = $s['comentari'];
        echo($dades);
        $content = $app['twig']->render('editarComentari.twig', [
                'logejat' => true,
                'comentari' => $dades,
                'id' => $id
            ]
        );
        $response->setContent($content);
        return $response;
    }

    public function nouComentari(Application $app, $id){
        $response = new Response();
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
        $repo = new UserTasks($app['db']);
        $ok= $repo->editarComentari($id);
        $dades = $repo->comentarisUser();
        $content = $app['twig']->render('userComments.twig', [
                'logejat' => true,
                'comentaris' => $dades,
                'message' => $ok

            ]
        );
        $response->setContent($content);
        return $response;

    }

    public function notificacionsUser(Application $app){
        $response = new Response();
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
        $repo = new UserTasks($app['db']);
        $ok= $repo->notificacionsUser();
        $content = $app['twig']->render('notificacionsUser.twig', [
                'logejat' => true,
                'comentaris' => $dades,
                'message' => $ok

            ]
        );
        $response->setContent($content);
        return $response;
    }
}