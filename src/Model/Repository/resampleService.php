<?php

namespace SilexApp\Model\Repository;

use Doctrine\DBAL\Connection;
use Silex\Application;
use Doctrine\DBAL\Configuration;

class resampleService{

    public function resizeImage($Orpath, $Despath, $width, $height){

        $auximg = imagecreatefromjpeg($Orpath);

        list($w2, $h2) = getimagesize($Orpath);

        $img = imagecreatetruecolor($width, $height);

        imagecopyresampled($img,$auximg, 0, 0, 0, 0, $width, $height, $w2, $h2);

        imagejpeg($img, $Despath);

        imagedestroy($auximg);
    }
}