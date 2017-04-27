<?php


namespace SilexApp\Model\Repository;

use Doctrine\DBAL\Connection;
use Silex\Application;
use Doctrine\DBAL\Configuration;

class UserTasks implements UserModel
{
    /** @var  Connection */
    private $db;

    /**
     * UserTasks constructor.
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function validateUser($username, $password)
    {
        $trobat = false;
        $sql = "SELECT * FROM usuari WHERE username = ?";
        $user = $this->db->fetchAssoc($sql, array((string)$username));

        if($user){
            $sql = "SELECT * FROM usuari WHERE password = ?";
            $pass = $this->db->fetchAssoc($sql, array((string)$password));
            if($pass){
                $trobat = true;
                echo("suu");
            }
        }else{
            $sql = "SELECT * FROM usuari WHERE email = ?";
            $user = $this->db->fetchAssoc($sql, array((string)$username));
            if($user){
                $sql = "SELECT * FROM usuari WHERE password = ?";
                $pass = $this->db->fetchAssoc($sql, array((string)$password));
                if($pass){
                    $trobat = true;
                }
            }
        }
        return $trobat;

    }


}