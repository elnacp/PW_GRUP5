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
            $password = md5($password);
            $pass = $this->db->fetchAssoc($sql, array((string)$password));
            if($pass){
                $trobat = true;
            }
        }else{
            $sql = "SELECT * FROM usuari WHERE email = ?";
            $user = $this->db->fetchAssoc($sql, array((string)$username));
            if($user){
                $sql = "SELECT * FROM usuari WHERE password = ?";
                $password = md5($password);
                $pass = $this->db->fetchAssoc($sql, array((string)$password));
                if($pass){
                    $trobat = true;
                }
            }
        }
        return $trobat;
    }

    public function logejarUsuari($name){
        $sql = "SELECT id FROM usuari WHERE username = ?";
        $stm = $this->db->fetchAssoc($sql, array((string)$name));
        $id = $stm['id'];
        $sql = "INSERT INTO logejat ( user_id) VALUE ($id)";
        $this->db->query($sql);
    }


    public function validateEditProfile($name, $birth, $pass1, $img1){
        $sql = "SELECT user_id FROM logejat LIMIT 1";
        $stm = $this->db->fetchAssoc($sql);
        $id = $stm['user_id'];
        $password = md5($pass1);
        $sql = "UPDATE usuari SET username = ?, birthdate  = ?, password = ?, img_path = ?  WHERE id = ?";
        $this->db->executeUpdate($sql, array($name, $birth, $password, $img1, (int) $id));



    }

    public function checkUser($username)
    {
        $trobat = false;
        $sql = "SELECT * FROM usuari WHERE username = ?";
        $user = $this->db->fetchAssoc($sql, array((string)$username));
        if($user){
            $trobat = true;
        }else{
            $trobat = false;
        }
        return $trobat;

    }


    public function RegisterUser($nickname, $email, $birthdate, $password, $img){
        $pass = md5($password);
        $this->db->insert('usuari', [
            'username' => $nickname,
            'email' => $email,
            'birthdate' => $birthdate,
            'password' =>$pass,
            'img_path' =>$img
        ]);
        return true;
    }

    public function DBnewPost($title, $path_name, $private){
        $sql = "SELECT * FROM logejat LIMIT 1";
        $user_id = $this->db->fetchAssoc($sql);
        $id = $user_id['user_id'];
        var_dump($private);
        $this->db->insert('imatge', [
            'user_id' => $id,
            'title' => $title,
            'img_path' => $path_name,
            'visits' => 0,
            'private'=> $private
        ]);
        return true;

    }

    public function dadesImatges()
    {
        $sql = "SELECT user_id FROM logejat";
        $stm = $this->db->fetchAssoc($sql);
        $id = $stm['user_id'];
        $sql = "SELECT * FROM imatge WHERE user_id = ?";
        $stm = $this->db->fetchAssoc($sql, array((int)$id));

        $dades = "";
        echo ($stm['title']);
        foreach( $stm as $s){

            echo($s['title']);
            /*$dades += "<div class=\"gallery_product col-lg-4 col-md-4 col-sm-4 col-xs-6 filter hdpe\">
                            <h1>". $s['title'] ."</h1>
                            <!--<img src=\"http://fakeimg.pl/365x365/\" class=\"img-responsive\">-->
                            <h3> ". $s['img_path'] ."</h3>
                            <button type=\"submit\" class=\"button button-block\" id=\"comenzar\"/>Editar</button>
                            <button type=\"submit\" class=\"button button-block\" id=\"comenzar\"/>Eliminar</button>
                        </div>";
            echo ($dades);*/
        }
        return $dades;
    }

    public function ActivateUser($nickname)
    {
        $active=1;

        $trobat = false;
        $sql = "SELECT * FROM usuari WHERE username = ?";
        $user = $this->db->fetchAssoc($sql, array((string)$nickname));
        if($user){
            $trobat = true;
        }else{
            $trobat = false;
        }
        return $trobat;



    }


}