<?php

namespace SilexApp\Model\Repository;

use Doctrine\DBAL\Connection;
use Silex\Application;
use Doctrine\DBAL\Configuration;

class resampleService{

    public function resizeImage($pathOrigen, $pathDestino,$nuevo_ancho,$nuevo_alto){

        $rutaOrigen = $pathOrigen;
        //var_dump($pathOrigen);
        $imagen = imagecreatefromjpeg($rutaOrigen);

        list($ancho, $alto) = getimagesize($pathOrigen);

        $imagen_1 = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);

        imagecopyresampled($imagen_1, $imagen, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);
        imagedestroy($imagen);

        return imagejpeg($imagen_1, $pathDestino, 100);;
        }
}