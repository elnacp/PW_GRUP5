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
                            <img src=".$s['img_path']." class=\"img-responsive\">
                            <li> <a href=".$eliminar."> Eliminar </a> </li>
                            <li><a href=".$editar."> Editar </a> </li>
                        </div>";

        }
        return $dades;
    }

    public function ActivateUser($nickname)
    {
        $active=1;
        $sql = "SELECT * FROM usuari WHERE username = ?";
        $user = $this->db->fetchAssoc($sql, array((string)$nickname));
        if($user){

            $sql = "UPDATE usuari SET active = ?  WHERE username = ?";
            $this->db->executeUpdate($sql, array($active, (string) $nickname));
            $trobat = true;
        }else{
            $trobat = false;
        }
        return $trobat;



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

    public function home1($log, $usuari){

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
            $profilePic = $stm1['img_path'];
            $title = $s['title'];
            $image = $s['img_path'];
            $image = str_replace(" ", "_", $image);

            $dia = $s['created_at'];
            $sql2 = "SELECT count(*) as total FROM likes WHERE image_id = ?";
            $l = $this->db->fetchAssoc($sql2, array((int)$s['id']));
            $likes = $l['total'];
            $visites = $s['visits'];
            $href = "/visualitzacioImatge/".$s['id'];



            $href1 = "/likeHome/".$s['id']."/".$usuari;
            $hrefComentari = "/comentari/".$s['id']."/".$usuari;


            ////////////
            $imgMesVistes = $imgMesVistes."<div class=\"[ panel panel-default ] panel-google-plus\">
                                            <div class=\"panel-heading\">                                         
                                                <h2>
                                                    <a href=".$href.">".$title." </a>
                                                </h2>
                                                <h3>".$autor."</h3>
                                                <h5><span>Publicat - </span> - <span>".$dia."</span> </h5>
                                                <img class=\"img-circle\" id=\"ProfileImg\" src=".$profilePic." alt=\"User Image\" />
                                            </div>
                                            <!-- IMATGE -->
                                             <img class=\"img-thumbnail img-responsive center-block\"  id=\"imgPost\" src=".$image." alt=\"User Image\" />
                                            <div class=\"panel-footer\">";
            if($log){

                $imgMesVistes = $imgMesVistes."<a  href=".$href1." class=\"[ btn btn-default ]\">Likes: +".$likes."</a>";
            }else{
                $imgMesVistes = $imgMesVistes."<a class=\"[ btn btn-default ]\">Likes: +".$likes."</a>";
            }
                $imgMesVistes = $imgMesVistes." <button type=\"button\" class=\"[ btn btn-default ]\">
                                                     Visitas: +". $visites."</span>
                                                </button>
                                                <div class=\"input-placeholder\">Escribe un comentario...</div>
                                            </div>";
            if($log) {
                $imgMesVistes = $imgMesVistes."<div class=\"panel-google-plus-comment\">
                                                <div class=\"panel-google-plus-textarea\">
                                                   <form action=".$hrefComentari." method=\"POST\">
                                                    <textarea rows=\"4\" name=\"comentari\"></textarea>
                                                    <button type=\"submit\" class=\"[ btn btn-success disabled ]\">Comentar</button>
                                                   </form>
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
        if($privada == 0){
            $visits = $visits + 1;
            $sql = "UPDATE imatge SET visits = ?  WHERE id = ?";
            $this->db->executeUpdate($sql, array((int)$visits, (int)$id));
        }

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
            $sql = "DELETE FROM likes WHERE image_id = '$id' AND user_id =  '$id_usuari'";
            //echo($id);
            //echo($id_usuari);
            $this->db->query($sql);


        }

    }

    public function deleteActualPic($nickname){
        $sql = "SELECT * FROM usuari WHERE username = ?";
        $info = $this->db->fetchAssoc($sql, array($nickname));
        if ($info){
            $image = $info['img_path'];
            unlink($image);

        }else{
            $sql = "SELECT * FROM imatge WHERE title = ?";
            $info = $this->db->fetchAssoc($sql, array($nickname));
            $image = $info['img_path'];
            unlink($image);
        }

    }


    public function comentari($id, $usuari_log){
        $comentari = htmlspecialchars($_POST['comentari']);
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

        }

        $message = "";
        $sql = "SELECT * FROM comentari WHERE image_id = ? and user_id = ?";
        $exist = $this->db->fetchAll($sql, array($id, (int)$id_usuari));
        if( !$exist){
            $sql = "SELECT * FROM imatge WHERE id = ?";
            $s = $this->db->fetchAssoc($sql, array($id));
            $titol1 = $s['title'];

            $sql = "INSERT INTO comentari (user_id, image_id, comentari, titol) VALUES (?,?,?,?)";
            $this->db->executeUpdate($sql, array($id_usuari, $id, $comentari, $titol1));
        }else{
            $message = "Ya has comentado 1 vez en esta imagen, elimina el comentario existente.";
        }

        return $message;

    }

    public function comentarisUser()
    {
        $sql = "SELECT * FROM logejat";
        $d = $this->db->fetchAssoc($sql);
        $id = $d['user_id'];
        $sql = "SELECT username FROM usuari WHERE id = ?";
        $u = $this->db->fetchAssoc($sql, array((int)$id));
        $usuari = $u['username'];
        $sql = "SELECT * FROM comentari WHERE user_id = ?";
        $d = $this->db->fetchAll($sql, array((int)$id));
        return $d;

    }

    public function eliminarComentari($id){
        $sql = "DELETE  FROM comentari WHERE id = $id";
        $ok = $this->db->exec($sql);
        $message = "";
        if( $ok){
            $message = "Se ha eliminado correctamente";
        }else{
            $message = "No se ha podido eliminar";
        }
        return $message;
    }

    public function editarComentari($id){
        $comentari = htmlspecialchars($_POST['comentari']);
        $sql = "UPDATE comentari SET comentari = ? WHERE id = ?";
        $ok = $this->db->executeUpdate($sql, array($comentari, (int)$id));
        if( $ok){
            $message = "Se ha editado correctamente";
        }else{
            $message = "No se ha podido editar";
        }
        return $message;
    }

    public function notificacio($id, $usuari_log, $type){
        $sql = "SELECT * FROM usuari WHERE username = ?";
        $trobat = $this->db->fetchAssoc($sql, array($usuari_log));
        $username = "";
        if(!$trobat){
            $sql = "SELECT * FROM usuari WHERE email = ?";
            $trobat = $this->db->fetchAssoc($sql, array($usuari_log));

            if($trobat){
                $sql = "SELECT * FROM usuari WHERE email = ?";
                $i = $this->db->fetchAssoc($sql, array($usuari_log));
                $id_usuari = $i['id'];
                $username = $i['username'];
                //echo("email" . $id_usuari);
            }
        }else{
            $sql = "SELECT * FROM usuari WHERE username = ?";
            $i = $this->db->fetchAssoc($sql, array($usuari_log));
            $id_usuari = $i['id'];
            $username = $i['username'];

        }
        $sql = "SELECT * FROM imatge WHERE id = ? and user_id = ?";
        $exist = $this->db->fetchAssoc($sql, array($id, (int)$id_usuari));
        if( !$exist){
            $sql = "SELECT * FROM imatge WHERE id = ?";
            $d = $this->db->fetchAssoc($sql, array((int)$id));
            $title = $d['title'];
            $id_img = $d['id'];

            if($type == 1){
                $sql = "SELECT * FROM notificacions WHERE nom_usuari = ? and titol = ? and type = ?";
                $exist = $this->db->fetchAssoc($sql, array($username, $title, $type));
                if(!$exist) {
                    $sql = "INSERT INTO notificacions (nom_usuari, titol, type, image_id) VALUE (?,?,?, ?)";
                    $this->db->executeUpdate($sql, array($username, $title, $type, $id_img));
                }
            }else if( $type == 2){
                $sql = "SELECT * FROM notificacions WHERE nom_usuari = ? and titol = ? and type = ?";
                $exist = $this->db->fetchAssoc($sql, array($username, $title, $type));
                if(!$exist) {
                    $sql = "INSERT INTO notificacions (nom_usuari, titol, type, image_id) VALUE (?,?,?, ?)";
                    $this->db->executeUpdate($sql, array($username, $title, $type, $id_img));
                }else {
                    $sql = "DELETE FROM notificacions WHERE nom_usuari ='$username'  AND titol =  '$title' AND type= '$type'";
                    $this->db->query($sql);
                }
            }





        }
    }

    public function notificacionsUser(){
        $sql = "SELECT * FROM logejat";
        $s = $this->db->fetchAssoc($sql);
        $id = $s['user_id'];
        $sql = "SELECT id FROM imatge WHERE user_id = ?";
        $dades = $this->db->fetchAll($sql, array((int)$id));

        foreach($dades as $i){
            $id_img = $i['id'];
            $sql1 = "SELECT * FROM notificacions WHERE image_id = ? ";
            $info = $this->db->fetchAll($sql1, array($id_img));
            foreach ($info as $in){
                $sql2 = "INSERT INTO notificacionsUsuari (usuari, titol, type, image_id, visualitzada, id_notificacio) VALUE (?,?,?, ?, ?,?)";
                $this->db->executeUpdate($sql2, array($in['nom_usuari'], $in['titol'], $in['type'], $in['image_id'], $in['visualitzada'], $in['id']));
            }

        }
        $sql  = "SELECT * FROM notificacionsUsuari";
        $dades = $this->db->fetchAll($sql);
        return $dades;

    }

    public function visualitzada($id){
        $sql = "DELETE FROM notificacions WHERE id = '$id'";
        $this->db->exec($sql);
    }

    public function getActualProfilePic($username, $img){
        $sql = "SELECT img_path FROM usuari WHERE username = ?";
        $stm = $this->db->fetchAssoc($sql, array((string)$username));
        if ($img == NULL){
            $img = $stm['img_path'];
        }
        return $img;
    }

    public function getActualPostImg($id, $img){
        $sql = "SELECT img_path FROM imatge WHERE id = ?";
        $stm = $this->db->fetchAssoc($sql, array((string)$id));
        if ($img == NULL){
            $img = $stm['img_path'];
        }
        return $img;
    }

    public function getUserId($username){
        $sql = "SELECT id FROM usuari WHERE username = ?";
        $stm = $this->db->fetchAssoc($sql, array((string)$username));
        return $stm['id'];
    }

    public function createUserActivation($id, $code)
    {
        $this->db->insert('activation', [
            'user_id' => $id,
            'code' => $code
        ]);
    }

    public function searchValidation($id, $code){
        $trobat = false;
        $sql = "SELECT * FROM activation WHERE code = ? and user_id = ?";
        $user = $this->db->fetchAssoc($sql, array((string)$code, $id));
        if($user){
            $sql = "SELECT username FROM usuari WHERE id = ?";
            $username = $this->db->fetchAssoc($sql, array((string)$id));
            $this->ActivateUser($username['username']);

            $trobat = true;

        }else{
            $trobat = false;
        }
        return $trobat;
    }

    public function getName($id){
        $sql = "SELECT username FROM usuari WHERE id = ? ";
        $user = $this->db->fetchAssoc($sql, array($id));
        return $user['username'];
    }

}