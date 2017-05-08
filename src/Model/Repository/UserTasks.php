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


    public function validateEditProfile($name, $birth, $pass1){
        $sql = "SELECT user_id FROM logejat LIMIT 1";
        $stm = $this->db->fetchAssoc($sql);
        $id = $stm['user_id'];
        $password = md5($pass1);
        $sql = "UPDATE usuari SET username = ?, birthdate  = ?, password = ? WHERE id = ?";
        $this->db->executeUpdate($sql, array($name, $birth, $password, (int) $id));



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
        //var_dump($private);
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
        $stm = $this->db->fetchAll($sql, array((int)$id));

        $dades = "";

        foreach( $stm as $s){
            $eliminar = "/eliminar/".$s['id'];
            $editar = "/editar/".$s['id'];
            $dades = $dades."<div class=\"gallery_product col-lg-4 col-md-4 col-sm-4 col-xs-6 filter hdpe\">
                            <h1>". $s['title'] ."</h1>
                            <!--<img src=\"http://fakeimg.pl/365x365/\" class=\"img-responsive\">-->
                            <h3> ". $s['img_path'] ."</h3>
                            <li> <a href=".$eliminar."> Eliminar </a> </li>
                            <li><a href=".$editar."> Editar </a> </li>
                        </div>";

        }
        return $dades;
    }


    public function deleteImage($id)
    {
        $sql = "DELETE  FROM imatge WHERE id = $id";
        $this->db->exec($sql);
    }

    public function editInformation($title, $path_name, $private, $id)
    {
        $sql = "UPDATE imatge SET title = ?, img_path  = ?, private = ? WHERE id = ?";
        $this->db->executeUpdate($sql, array($title, $path_name, $private, (int) $id));
    }

    public function home1($log){

        $sql = "SELECT * FROM imatge  ORDER  BY visits DESC ";
        $stm = $this->db->fetchAll($sql);
        $c1 = 5;
        $c2 = 5;
        $imgMesVistes ="<div class=\"[ panel panel-default ] panel-google-plus\">
                             <h2> Imagenes más vistas: </h2>
                        </div>";
        foreach ( $stm as $s){
            $id = $s['user_id'];
            $sql1 = "SELECT * FROM usuari WHERE id = ?";
            $stm1 = $this->db->fetchAssoc($sql1, array((int)$id));
            $autor = $stm1['username'];
            $title = $s['title'];
            $dia = $s['created_at'];
            $likes = $s['likes'];
            $visites = $s['visits'];
            $href = "/visualitzacioImatge/".$s['id'];

            $imgMesVistes = $imgMesVistes."<div class=\"[ panel panel-default ] panel-google-plus\">
                                            <div class=\"panel-heading\">                                         
                                                <h2>
                                                    <a href=".$href.">".$title." </a>
                                                </h2>
                                                <h3>".$autor."</h3>
                                                <h5><span>Publicat - </span> - <span>".$dia."</span> </h5>
                                                <img class=\"img-circle\" src=\"https://lh3.googleusercontent.com/uFp_tsTJboUY7kue5XAsGA=s46\" alt=\"User Image\" />
                                            </div>
                                            <!-- IMATGE -->
                                            <div class=\"panel-footer\">
                                                <button type=\"button\" class=\"[ btn btn-default ]\">Likes: +". $likes."</button>
                                                <button type=\"button\" class=\"[ btn btn-default ]\">
                                                     Visitas: +". $visites."</span>
                                                </button>
                                                <div class=\"input-placeholder\">Escribe un comentario...</div>
                                            </div>";
            if($log) {
                $imgMesVistes = $imgMesVistes."<div class=\"panel-google-plus-comment\">
                                                <div class=\"panel-google-plus-textarea\">
                                                    <textarea rows=\"4\"></textarea>
                                                    <button type=\"submit\" class=\"[ btn btn-success disabled ]\">Comentar</button>
                                                    <button type=\"reset\" class=\"[ btn btn-default ]\">Cancelar</button>
                                                </div>
                                                <div class=\"clearfix\"></div>
                                                </div>";
            }

            $imgMesVistes = $imgMesVistes."</div>";

            $c1--;
            //img - titol - autor - dia publicación - numero likes - número de visualizaciones
        }

        return $imgMesVistes;

    }


    public function incrementarVisites($id)
    {
        $sql = "SELECT * FROM imatge WHERE id = ?";
        $s = $this->db->fetchAssoc($sql, array((int)$id));
        $visits = $s['visits'];
        $privada = $s['private'];
        $visits = $visits + 1;
        $sql = "UPDATE imatge SET visits = ?  WHERE id = ?";
        $this->db->executeUpdate($sql, array((int)$visits, (int)$id));

        return $privada;
    }

    public function like($id, $usuari_log){
        $trobat = false;
        $id_usuari = "";
        $sql = "SELECT * FROM usuari WHERE username = ?";
        $trobat = $this->db->fetchAssoc($sql, array($usuari_log));
        if(!$trobat){
            $sql = "SELECT * FROM usuari WHERE email = ?";
            $trobat = $this->db->fetchAssoc($sql, array($usuari_log));

            if($trobat){
                $sql = "SELECT id FROM usuari WHERE email = ?";
                $i = $this->db->fetchAssoc($sql, array($usuari_log));
                $id_usuari = $i['id'];
                //echo("email" . $id_usuari);
            }
        }else{
            $sql = "SELECT id FROM usuari WHERE username = ?";
            $i = $this->db->fetchAssoc($sql, array($usuari_log));
            $id_usuari = $i['id'];
            //echo("usuari" . $id_usuari);
        }


        $sql = "SELECT * FROM likes WHERE image_id = ? and user_id = ?";
        $exist = $this->db->fetchAll($sql, array($id, (int)$id_usuari));
        if( !$exist){
            //echo("no existeix");
            $sql = "SELECT * FROM imatge WHERE id = ?";
            $s = $this->db->fetchAssoc($sql, array((int)$id));
            $likes = $s['likes'];
            $l = $likes +1;
            $sql = "UPDATE imatge SET likes = ? WHERE id = ?";
            $this->db->executeUpdate($sql, array($l, (int)$id));
            $sql = "INSERT INTO likes (user_id, image_id) VALUES (?,?)";
            $this->db->executeUpdate($sql, array($id_usuari, $id));
        }else{
            //echo("existeix");
            $sql = "SELECT * FROM imatge WHERE id = ?";
            $s = $this->db->fetchAssoc($sql, array((int)$id));
            $likes = $s['likes'];
            //echo($likes);

            //echo($l);
            $sql = "UPDATE imatge SET likes = ? WHERE id = ?";
            $this->db->executeUpdate($sql, array($likes-1, (int)$id));
            //DELETE  FROM logejat
            $sql = "DELETE FROM likes WHERE image_id = ? ";
            //echo($id);
            //echo($id_usuari);
            $s = $this->db->query($sql, array((int)$id));
            echo($s);

        }

    }


}