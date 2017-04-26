<?php


namespace SilexApp\Model\Repository;

use Silex\Application;
use Doctrine\DBAL\Connection;

interface UserModel
{

    public function validateUser($username, $password);


}
