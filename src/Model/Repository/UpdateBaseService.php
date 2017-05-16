<?php
/**
 * Created by PhpStorm.
 * User: noa
 * Date: 15/5/17
 * Time: 14:08
 */

namespace SilexApp\Model\Repository;


use Doctrine\DBAL\Connection;
use Silex\Application;
use Doctrine\DBAL\Configuration;
class UpdateBaseService{

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


    public function getUserInfo($username){
        $sql = "SELECT img_path FROM usuari WHERE username = ?";
        $img = $this->db->fetchAssoc($sql, array((string)$username));

        return $username.'!=!'.$img['img_path'];
    }
}
