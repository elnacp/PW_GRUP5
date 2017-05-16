<?php

namespace SilexApp\Model\Repository;

use Doctrine\DBAL\Connection;
use Silex\Application;
use Doctrine\DBAL\Configuration;

class resampleService{

    public function resizeImage($title, $ancho, $alto,$nuevo_ancho,$nuevo_alto){

        $imagen_1 = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
        $imagen = imagecreatefromjpeg($title);
        $ok = imagecopyresampled($imagen_1, $imagen, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);
        if($ok){
            return imagejpeg($imagen_1, null, 100);;
        }else{
            return null;
        }


    }
}