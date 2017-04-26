<?php


namespace SilexApp\Model\Repository;

use Silex\Application;
use Doctrine\DBAL\Connection;

interface UserModel
{

    public function validateUser($username, $password);

    public function logejarUsuari($name);

    public function validateEditProfile($name, $birth, $pass1, $pass2, $path);


}
