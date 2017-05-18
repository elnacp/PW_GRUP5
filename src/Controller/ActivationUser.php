<?php
/**
 * Created by PhpStorm.
 * User: noa
 * Date: 16/5/17
 * Time: 2:17
 */
namespace SilexApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SilexApp\Model\Repository\UserTasks;
use Symfony\Component\HttpFoundation\RedirectResponse;


class ActivationUser{

    public function validateUser(Application $app, $code,$id, Request $request){
        $repo = new UserTasks($app['db']);
        $exists = $repo->searchValidation($id,$code);

        if($exists){
            $name = $repo->getName($id);
            $repo->logejarUsuari($name);
            $url = '/iniciarSession/' . $name;
            return new RedirectResponse($url);
        }else{
            $response = new Response();
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $content = $app['twig']->render('error.twig', [
                    'message' => 'User not found',
                    'logejat' => false,
                    'image' =>null,
                    'username' =>null
                ]
            );
            $response->setContent($content);
            return $response;

        }


}


}
