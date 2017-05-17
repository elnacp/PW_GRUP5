<?php

namespace SilexApp\Controller;

use Silex\Application;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SilexApp\Model\Repository\UserTasks;
use SilexApp\Model\Repository\UpdateBaseService;
class LikeController{

    public function likeImage(Application $app, $id, $usuari_log){
        $repo = new UserTasks($app['db']);
        $repo->like($id, $usuari_log);
        $type = 2;
        $repo->notificacio($id, $usuari_log, $type);
        $url = "/visualitzacioImatge/".$id;
        return new RedirectResponse($url);

    }

    public function likeHome(Application $app, $id, $usuari_log){
        $repo = new UserTasks($app['db']);
        $type = 2;
        $repo->like($id, $usuari_log);
        $repo->notificacio($id, $usuari_log,$type );

        if(!$app['session']->has('name')) {
            $url = "/";
            return new RedirectResponse($url);
        }else{
            $url = "/";
            return new RedirectResponse($url);
        }
    }
}