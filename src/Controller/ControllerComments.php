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
}