<?php


namespace SilexApp\Model\Repository;


use Doctrine\DBAL\Connection;
use Silex\Application;
use Doctrine\DBAL\Configuration;



class UserTasks implements UserModel
{
    /** @var  Connection */
    private $db;
    private $id_seguent;

    /**
     * UserTasks constructor.
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
        $this->id_seguent = 0;
    }

    public function validateUser($username, $password)
    {
        $trobat = false;
        $sql = "SELECT * FROM usuari WHERE username = ?";
        $user = $this->db->fetchAssoc($sql, array((string)$username));
        if ($user) {
            $sql = "SELECT * FROM usuari WHERE password = ?";
            $password = md5($password);
            $pass = $this->db->fetchAssoc($sql, array((string)$password));
            if ($pass) {
                $trobat = true;
            }
        } else {
            $sql = "SELECT * FROM usuari WHERE email = ?";
            $user = $this->db->fetchAssoc($sql, array((string)$username));
            if ($user) {
                $sql = "SELECT * FROM usuari WHERE password = ?";
                $password = md5($password);
                $pass = $this->db->fetchAssoc($sql, array((string)$password));
                if ($pass) {
                    $trobat = true;
                }
            }
        }
        return $trobat;
    }

    public function logejarUsuari($name)
    {

        $sql = "SELECT id FROM usuari WHERE username = ?";
        $stm = $this->db->fetchAssoc($sql, array((string)$name));
        if(!$stm){
            $sql2 = "SELECT id FROM usuari WHERE email = ?";
            $stm2 = $this->db->fetchAssoc($sql2, array((string)$name));
            $id = $stm2['id'];
        }else{
            $id = $stm['id'];

        }
        $sqlAct = "SELECT active FROM usuari WHERE id = ?";
        $stmAct = $this->db->fetchAssoc($sqlAct, array((int)$id));

        if ($stmAct['active'] == 1 ){
            $sql = "INSERT INTO logejat (user_id) VALUE ($id)";
            $this->db->query($sql);
            return true;
        }else{
            return false;
        }
    }


    public function validateEditProfile($name, $birth, $pass1, $img1)
    {
        $sql = "SELECT user_id FROM logejat LIMIT 1";
        $stm = $this->db->fetchAssoc($sql);
        $id = $stm['user_id'];
        $password = md5($pass1);
        $sql = "UPDATE usuari SET username = ?, birthdate  = ?, password = ?, img_path = ?  WHERE id = ?";
        $this->db->executeUpdate($sql, array($name, $birth, $password, $img1, (int)$id));


    }

    public function checkUser($username, $email)
    {
        $trobat = false;
        $sql = "SELECT * FROM usuari WHERE username = ?";
        $user = $this->db->fetchAssoc($sql, array((string)$username));
        if ($user) {
            $trobat = true;
        } else {
            $sql2 = "SELECT * FROM usuari WHERE email = ?";
            $email = $this->db->fetchAssoc($sql2, array((string)$email));
            if($email){
                $trobat = true;
            }else{
                $trobat = false;

            }
        }
        return $trobat;

    }


    public function RegisterUser($nickname, $email, $birthdate, $password, $img)
    {
        $pass = md5($password);
        $this->db->insert('usuari', [
            'username' => $nickname,
            'email' => $email,
            'birthdate' => $birthdate,
            'password' => $pass,
            'img_path' => $img
        ]);
        return true;
    }

    public function DBnewPost($title, $path_name, $private, $sizeImage)
    {
        $sql = "SELECT * FROM logejat LIMIT 1";
        $user_id = $this->db->fetchAssoc($sql);
        $id = $user_id['user_id'];
        $this->db->insert('imatge', [
            'user_id' => $id,
            'title' => $title,
            'img_path' => $path_name,
            'visits' => 0,
            'private' => $private,
            'sizeImage' => $sizeImage
        ]);
        return true;

    }

    public function dadesImatges($string)
    {
        $sql = "SELECT user_id FROM logejat";
        $stm = $this->db->fetchAssoc($sql);
        $id = $stm['user_id'];
        $sql = "SELECT * FROM imatge WHERE user_id = ?";
        $stm = $this->db->fetchAll($sql, array((int)$id));
        $dades = "";

        foreach ($stm as $s) {
            $img = $s['img_path'];
            $size = $s['sizeImage'];

            if ($size == 400){
                list($p1, $p2) = explode("400", "$img ");
            } else{
                list($p1, $p2) = explode("100", "$img ");
            }
            $img = $p1.'Original'.$p2;
            if ($string == "eliminado"){
                $img = '.'.$s['img_path'];
            }

            $eliminar = "/eliminar/" . $s['id'];
            $editar = "/editar/" . $s['id'];
            if ($s['sizeImage'] == 400) {
                $dades = $dades . "<div class=\"gallery_product col-lg-4 col-md-4 col-sm-4 col-xs-6 filter hdpe\">
                            <h1>" . $s['title'] . "</h1>
                            <img src=" . $img . " class=\"img-responsive\" width=\"400\" height=\"300\">
                            <li> <a href=" . $eliminar . " id=\"delete\" onclick= \"return confirm ('Are you sure?')\"> Eliminar </a> </li>
                            <li><a href=" . $editar . "> Editar </a> </li>
                        </div>";
            } else {
                $dades = $dades . "<div class=\"gallery_product col-lg-4 col-md-4 col-sm-4 col-xs-6 filter hdpe\">
                            <h1>" . $s['title'] . "</h1>
                            <img src=" . $img . " class=\"img-responsive\" width=\"100\" height=\"100\">
                            <li> <a href=" . $eliminar . "> Eliminar </a> </li>
                            <li><a href=" . $editar . "> Editar </a> </li>
                        </div>";
            }


        }
        //var_dump($dades);
        return $dades;
    }

    public function ActivateUser($nickname)
    {

        $active = 1;

        $trobat = false;
        $sql = "SELECT * FROM usuari WHERE username = ?";
        $user = $this->db->fetchAssoc($sql, array((string)$nickname));
        if ($user) {
            $sql = "UPDATE usuari SET active = ?  WHERE username = ?";
            $this->db->executeUpdate($sql, array($active, (string)$nickname));
            $trobat = true;

            $sql2 = "SELECT id FROM usuari  WHERE username = ?";
            $id = $this->db->executeUpdate($sql2, array((string) $nickname));
            $sql3 = "DELETE FROM activation WHERE user_id = $id";
            $this->db->exec($sql3);
        }else{

            $trobat = false;
        }
        return $trobat;


    }


    public function deleteImage($id)
    {

        $sql = "DELETE FROM notificacionsUsuari WHERE image_id = $id";
        $this->db->exec($sql);
        
        $sql = "DELETE FROM notificacions WHERE image_id = $id";
        $this->db->exec($sql);

        $sql = "DELETE FROM comentari WHERE image_id = $id";
        $this->db->exec($sql);

        $sql = "DELETE FROM likes WHERE image_id = $id";
        $this->db->exec($sql);


        $sql2 = "SELECT img_path FROM imatge WHERE id = $id";
        $aux = $this->db->fetchAssoc($sql2);

        $sql3 = "SELECT sizeImage FROM imatge WHERE id = $id";
        $aux2 = $this->db->fetchAssoc($sql3);

        $img_antiga = $aux['img_path'];

        if($aux2['sizeImage'] == 400){
            list($p1,$p2) = explode("400", $img_antiga);

        }else{
            list($p1,$p2) = explode("100", $img_antiga);
        }

        $img_antiga = $p1.'Original'.$p2;

        unlink($aux['img_path']);
        unlink($img_antiga);

        $sql = "DELETE  FROM imatge WHERE id = $id";
        $this->db->exec($sql);

    }

    public function editInformation($title, $path_name, $private, $id, $sizeImage)
    {

        $sql = "UPDATE imatge SET title = ?, img_path  = ?, private = ?, sizeImage = ? WHERE id = ?";
        $this->db->executeUpdate($sql, array($title, $path_name, $private, $sizeImage, (int)$id));

    }


    public function home1($log, $usuari, $action)
    {

        $sql = "SELECT * FROM imatge  ORDER  BY visits DESC LIMIT 5";
        $stm = $this->db->fetchAll($sql);
        $c1 = 5;
        $c2 = 5;
        $imgMesVistes = "<div class=\"[ panel panel-default ] panel-google-plus\" id=\"popEntryPan\">
                             <h2 class=\"popEntry\"> Imagenes más vistas: </h2>
                        </div>";
        foreach ($stm as $s) {

            $id = $s['user_id'];
            $sql1 = "SELECT * FROM usuari WHERE id = ?";
            $stm1 = $this->db->fetchAssoc($sql1, array((int)$id));
            $autor = $stm1['username'];
            $profilePic = $stm1['img_path'];
            $image = $s['img_path'];
            if($log){
                $profilePic = '.'.$stm1['img_path'];
                $image = '.'.$image;
            }
            $title = $s['title'];


            $birthdate = $s['created_at'];

            list($yy, $mm, $daux) = explode("-", $birthdate);
            list($dd, $taux) = explode(" ", $daux);
            list($hh, $min, $ss) = explode(":", $taux);


            if ((date("Y") == $yy) && (date("m") == $mm) && (date("d") == $dd)){

                if (date("H") == $hh){

                    $birthdate = date("i") - $min;
                    $birthdate = 'Hace '.$birthdate.' minutos';

                }
                if(date("H")>$hh){
                    $birthdate = date("H") - $hh;
                    $birthdate = 'Hace '.$birthdate.' horas';
                }


            }


            if((date("Y") == $yy) && (date("m") == $mm) && (date("d") > $dd)){
                $birthdate = date("d") - $dd;
                $birthdate = 'Hace '.$birthdate.' dias';
            }
            if((date("Y") == $yy) && (date("m") > $mm)){
                $birthdate = date("d") - $dd;
                if ($birthdate > 30){
                    $birthdate = date("m") - $mm;
                    $birthdate = 'Hace '.$birthdate.' meses';
                }
            }

            if(date("Y")>$yy){
                $birthdate = date("Y") - $yy;
                $birthdate = 'Hace '.$birthdate.' años';
            }


            $sql2 = "SELECT count(*) as total FROM likes WHERE image_id = ?";
            $l = $this->db->fetchAssoc($sql2, array((int)$s['id']));
            $likes = $l['total'];
            $visites = $s['visits'];
            $href = "/visualitzacioImatge/" . $s['id'];

            if ($action == "likes"){
                $profilePic = '/'.$profilePic;
                $image = '/'.$image;
            }

            //seguir el mateix exemple que el href anterior per a fer el perfil del usuari
            $href1 = "/likeHome/" . $s['id'] . "/" . $usuari;
            $hrefComentari = "/comentari/" . $s['id'] . "/" . $usuari;
            $hrefPerfil = "/perfil/" .$autor;
            $imgMesVistes = $imgMesVistes . "<div id =\"Panel\"class=\"[ panel panel-default ] panel-google-plus center-block\">

                                            <div class=\"panel-heading\">                                         
                                                <h2>
                                                    <a href=" . $href . ">" . $title . " </a>
                                                </h2>
                                                <h3>
                                                    <a href=" . $hrefPerfil . "> ".$autor. " </a>
                                                </h3>
                                                <h5><span>Publicado - </span> <span>" . $birthdate . "</span> </h5>
                                                <img class=\"img-circle img-responsive\" src=".$profilePic." alt=\"User Image\"  id=\"ProfileImg\"/>
                                            </div>
                                         
                                            <!-- IMATGE -->
                                             <img class=\"img-thumbnail img-responsive center-block\"  id=\"imgPost\" src=" . $image . " alt=\"User Image\" />
                                            <div class=\"panel-footer\">";

            if ($log) {

                $imgMesVistes = $imgMesVistes . "<a  href=" . $href1 . " class=\"[ btn btn-default ]\">Likes: +" . $likes . "</a>";
            } else {
                $imgMesVistes = $imgMesVistes . "<a class=\"[ btn btn-default ]\">Likes: +" . $likes . "</a>";
            }
            $imgMesVistes = $imgMesVistes . " <button type=\"button\" class=\"[ btn btn-default ]\">
                                                     Visitas: +" . $visites . "</span>

                                                </button>
                                            ";
            if ($log) {
                $imgMesVistes = $imgMesVistes . "<div class=\"panel-google-plus-comment\">
                                                <div class=\"panel-google-plus-textarea\">
                                                   <form action=" . $hrefComentari . " method=\"POST\">
                                                    <textarea rows=\"4\" name=\"comentari\" id=\"CommentBox\"></textarea>
                                                    <br>
                                                    <button type=\"submit\" class=\"[ btn btn-success disabled ]\" id=\"ButtonCom\">Comentar</button>
                                                   </form>
                                                   <button type=\"reset\" class=\"[ btn btn-default ]\">Cancelar</button>
                                                    
                                                </div>
                                                </div>
                                                <div class=\"clearfix\"></div>
                                                </div>";
            }else{
                $imgMesVistes = $imgMesVistes ."</div>";
            }

            $imgMesVistes = $imgMesVistes . "</div>";


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
        if ($privada == 0) {
            $visits = $visits + 1;
            $sql = "UPDATE imatge SET visits = ?  WHERE id = ?";
            $this->db->executeUpdate($sql, array((int)$visits, (int)$id));
        }

        return $privada;
    }

    public function like($id, $usuari_log)
    {
        $trobat = false;
        $id_usuari = "";
        $sql = "SELECT * FROM usuari WHERE username = ?";
        $trobat = $this->db->fetchAssoc($sql, array($usuari_log));
        if (!$trobat) {
            $sql = "SELECT * FROM usuari WHERE email = ?";
            $trobat = $this->db->fetchAssoc($sql, array($usuari_log));

            if ($trobat) {
                $sql = "SELECT id FROM usuari WHERE email = ?";
                $i = $this->db->fetchAssoc($sql, array($usuari_log));
                $id_usuari = $i['id'];
                //echo("email" . $id_usuari);
            }
        } else {
            $sql = "SELECT id FROM usuari WHERE username = ?";
            $i = $this->db->fetchAssoc($sql, array($usuari_log));
            $id_usuari = $i['id'];
            //echo("usuari" . $id_usuari);
        }


        $sql = "SELECT * FROM likes WHERE image_id = ? and user_id = ?";
        $exist = $this->db->fetchAll($sql, array($id, (int)$id_usuari));
        if (!$exist) {
            //echo("no existeix");
            $sql = "SELECT * FROM imatge WHERE id = ?";
            $s = $this->db->fetchAssoc($sql, array((int)$id));
            $likes = $s['likes'];
            $l = $likes + 1;
            $sql = "UPDATE imatge SET likes = ? WHERE id = ?";
            $this->db->executeUpdate($sql, array($l, (int)$id));
            $sql = "INSERT INTO likes (user_id, image_id) VALUES (?,?)";
            $this->db->executeUpdate($sql, array($id_usuari, $id));
        } else {
            //echo("existeix");
            $sql = "SELECT * FROM imatge WHERE id = ?";
            $s = $this->db->fetchAssoc($sql, array((int)$id));
            $likes = $s['likes'];
            //echo($likes);

            //echo($l);
            $sql = "UPDATE imatge SET likes = ? WHERE id = ?";
            $this->db->executeUpdate($sql, array($likes - 1, (int)$id));
            //DELETE  FROM logejat
            $sql = "DELETE FROM likes WHERE image_id = '$id' AND user_id =  '$id_usuari'";
            //echo($id);
            //echo($id_usuari);
            $this->db->query($sql);


        }

    }

    public function deleteActualPic($nickname)
    {
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


    public function comentari($id, $usuari_log)
    {
        $comentari = htmlspecialchars($_POST['comentari']);
        $id_usuari = "";
        $sql = "SELECT * FROM usuari WHERE username = ?";
        $trobat = $this->db->fetchAssoc($sql, array($usuari_log));
        if (!$trobat) {
            $sql = "SELECT * FROM usuari WHERE email = ?";
            $trobat = $this->db->fetchAssoc($sql, array($usuari_log));

            if ($trobat) {
                $sql = "SELECT id FROM usuari WHERE email = ?";
                $i = $this->db->fetchAssoc($sql, array($usuari_log));
                $id_usuari = $i['id'];
                //echo("email" . $id_usuari);
            }
        } else {
            $username = $trobat['username'];
            $sql = "SELECT id FROM usuari WHERE username = ?";
            $i = $this->db->fetchAssoc($sql, array($usuari_log));
            $id_usuari = $i['id'];

        }

        $message = "";
        $sql = "SELECT * FROM comentari WHERE image_id = ? and user_id = ?";
        $exist = $this->db->fetchAll($sql, array($id, (int)$id_usuari));

        if (!$exist) {
            $sql1 = "SELECT * FROM imatge WHERE id = ?";
            $dades = $this->db->fetchAssoc($sql1, array((int)$id));
            $titol = $dades['title'];
            $sql = "INSERT INTO comentari (user_id, image_id, comentari,titol, autor ) VALUES (?,?,?, ?, ?)";
            $this->db->executeUpdate($sql, array($id_usuari, $id, $comentari, $titol, $username));
        } else {

            if (!$exist) {
                $sql = "SELECT * FROM imatge WHERE id = ?";
                $s = $this->db->fetchAssoc($sql, array($id));
                $titol1 = $s['title'];

                $sql = "INSERT INTO comentari (user_id, image_id, comentari,titol, autor ) VALUES (?,?,?, ?, ?)";
                $this->db->executeUpdate($sql, array($id_usuari, $id, $comentari, $titol1, $username));
            } else {

                $message = "Ya has comentado 1 vez en esta imagen, elimina el comentario existente.";
            }

            return $message;

        }
    }


    public function imatgesUsuari($id)
    {
        $sql = "SELECT * FROM imatge WHERE user_id = ?";
        $stm = $this->db->fetchAll($sql, array((int)$id));
        $dades = "";


        foreach ($stm as $s) {
            $images = '.'.$s['img_path'];
            $dades = $dades . "<div class=\"gallery_product col-lg-4 col-md-4 col-sm-4 col-xs-6 filter hdpe\">
                           <h1> <a href=" . $s['title'] . "></a></h1>
                           <img src=" . $images . " class=\"img-responsive\" width=\"100\" height=\"100\">
                       </div>";
        }
        return $dades;
    }

    public function imatgesPerfil($username, $opcio)
    {
        $sql = "SELECT id FROM usuari WHERE username = ?";
        $stm = $this->db->fetchAssoc($sql, array($username));
        $id = $stm['id'];
        $dades = "";
        switch ($opcio) {
            case 1:
                $sql = "SELECT * FROM imatge WHERE user_id = ? ORDER BY created_at DESC";
                $stm = $this->db->fetchAll($sql, array((int)$id));
                break;
            case 2:
                $sql = "SELECT * FROM imatge WHERE user_id = ? ORDER BY likes DESC ";
                $stm = $this->db->fetchAll($sql, array((int)$id));

                break;
            case 3:
                $sql = "SELECT * FROM imatge WHERE user_id = ? ORDER BY (SELECT SUM(image_id) FROM comentari,imatge WHERE comentari.image_id = imatge.id) DESC";
                $stm = $this->db->fetchAll($sql, array((int)$id));
                break;

            case 4:
                $sql = "SELECT * FROM imatge WHERE user_id = ? ORDER BY visits DESC";
                $stm = $this->db->fetchAll($sql, array((int)$id));
                break;

            default:
                $sql = "SELECT * FROM imatge WHERE user_id = ? ORDER BY created_at DESC";
                $stm = $this->db->fetchAll($sql, array((int)$id));
        }

        foreach ($stm as $s) {
            //var_dump($s['title']);
            $title = $s['title'];
            $id = $s['id'];
            $href = "/visualitzacioImatge" ."/". $id;
            $dades = $dades . "<div class=\"gallery_product col-lg-4 col-md-4 col-sm-4 col-xs-6 filter hdpe\">
                           <h3>
                                <a href=" . $href . "> ".$title    . " </a>
                                                </h3>
                           <img src=" . '.'.$s['img_path'] . " class=\"img-responsive\" width=\"100\" height=\"100\">
                       </div>";
        }
        return $dades;
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

    public function getActualPostImg($id, $img)
    {
        $sql = "SELECT img_path FROM imatge WHERE id = ?";
        $stm = $this->db->fetchAssoc($sql, array((string)$id));
        if ($img == NULL) {
            $img = $stm['img_path'];
        }
        return $img;
    }

    public function dadesUsuari($username, $id)
    {
        $sql1 = "SELECT COUNT(user_id) FROM imatge WHERE user_id = ?";
        $s2 = $this->db->fetchAssoc($sql1, array($id));
        $s2 = implode('',$s2);
        $sql2 = "SELECT COUNT(user_id) FROM  comentari WHERE user_id = ?";
        $s3 = $this->db->fetchAssoc($sql2, array($id));
        $s3 = implode('',$s3);
        $sql3 = "SELECT img_path FROM usuari WHERE id= ?";
        $s4 = $this->db->fetchAssoc($sql3, array($id));
        $s4 = implode('',$s4);
        $dades = "";

        $a = array();
        $a['username'] = $username;
        $a['publicades'] = $s2;
        $a['comentaris'] = $s3;
        $a['path'] = '.'.$s4;

        return $a;

    }

    public function getUserId($username){
        $sql = "SELECT id FROM usuari WHERE username = ?";
        $stm = $this->db->fetchAssoc($sql, array((string)$username));
        $id = $stm['id'];
        if(!$stm){
            $sql2 = "SELECT id FROM usuari WHERE email = ?";
            $stm2 = $this->db->fetchAssoc($sql2, array((string)$username));
            $id = $stm2['id'];
        }
        return $id;
    }

    public function createUserActivation($id, $code)
    {
        $this->db->insert('activation', [
            'user_id' => $id,
            'code' => $code
        ]);
    }

    public function ultimesImatges($log, $usuari)
    {

        $sql = "SELECT * FROM imatge ORDER  BY created_at DESC LIMIT 5";
        $stm = $this->db->fetchAll($sql);
        $c1 = 5;
        $imgMesVistes = "";
        foreach ($stm as $s) {

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
            $href = "/visualitzacioImatge/" . $s['id'];

            //seguir el mateix exemple que el href anterior per a fer el perfil del usuari
            $href1 = "/likeHome/" . $s['id'] . "/" . $usuari;
            $hrefComentari = "/comentari/" . $s['id'] . "/" . $usuari;
            $hrefPerfil = "/perfil/" .$autor;
            $imgMesVistes = $imgMesVistes . "<div class=\"[ panel panel-default ] panel-google-plus\">

                                            <div class=\"panel-heading\">                                         
                                                <h2>
                                                    <a href=" . $href . ">" . $title . " </a>
                                                </h2>
                                                <h3>
                                                    <a href=" . $hrefPerfil . "> ".$autor. " </a>
                                                </h3>
                                                <h5><span>Publicado - </span> - <span>" . $dia . "</span> </h5>
                                                <img class=\"img-circle\" src=\"https://lh3.googleusercontent.com/uFp_tsTJboUY7kue5XAsGA=s46\" alt=\"User Image\" />
                                            </div>
                                         
                                            <!-- IMATGE -->
                                             <img class=\"img-thumbnail img-responsive center-block\"  id=\"imgPost\" src=" . $image . " alt=\"User Image\" />
                                            <div class=\"panel-footer\">";

            if ($log) {

                $imgMesVistes = $imgMesVistes . "<a  href=" . $href1 . " class=\"[ btn btn-default ]\">Likes: +" . $likes . "</a>";
            } else {
                $imgMesVistes = $imgMesVistes . "<a class=\"[ btn btn-default ]\">Likes: +" . $likes . "</a>";
            }
            $imgMesVistes = $imgMesVistes . " <button type=\"button\" class=\"[ btn btn-default ]\">
                                                     Visitas: +" . $visites . "</span>

                                                </button>
                                                <div class=\"input-placeholder\">Escribe un comentario...</div>
                                            </div>";
            if ($log) {
                $imgMesVistes = $imgMesVistes . "<div class=\"panel-google-plus-comment\">
                                                <div class=\"panel-google-plus-textarea\">
                                                   <form action=" . $hrefComentari . " method=\"POST\">
                                                    <textarea rows=\"4\" name=\"comentari\"></textarea>
                                                    <button type=\"submit\" class=\"[ btn btn-success disabled ]\">Comentar</button>
                                                   </form>
                                                   <button type=\"reset\" class=\"[ btn btn-default ]\">Cancelar</button>
                                                    
                                                </div>
                                                <div class=\"clearfix\"></div>
                                                </div>";
            }


            $imgMesVistes = $imgMesVistes . "</div>";
            if($c1 == 1){
                $id_seguent = $s['id'];
                $imgMesVistes = $imgMesVistes ."<br>
                                                <div class=\"final\" val=\"'.$id_seguent.'\" ></div>
                                                <button class=\"btn btnt-primary loadmore\" >Loadmore</button>";
            }
            $c1--;
            //img - titol - autor - dia publicación - numero likes - número de visualizaciones
        }

        $this->id_seguent = $id_seguent - 1;

        return $imgMesVistes;
    }

    public function novaInfo()
    {
        $a = array(
            'info' => array()
        );
        $sql = "SELECT * FROM imatge ORDER  BY created_at DESC";
        $d = $this->db->fetchAll($sql);
            foreach ($d as $dades) {
                $info = array();

                $birthdate = $dades['created_at'];
                list($yy, $mm, $daux) = explode("-", $birthdate);
                list($dd, $taux) = explode(" ", $daux);
                list($hh, $min, $ss) = explode(":", $taux);

                if ((date("Y") == $yy) && (date("m") == $mm) && (date("d") == $dd)){
                    if (date("H") == $hh){
                        $birthdate = date("i") - $min;
                        $birthdate = 'Hace '.$birthdate.' minutos';
                    }
                    if(date("H")>$hh){
                        $birthdate = date("H") - $hh;
                        $birthdate = 'Hace '.$birthdate.' horas';
                    }
                }

                if((date("Y") == $yy) && (date("m") == $mm) && (date("d") > $dd)){
                    $birthdate = date("d") - $dd;
                    $birthdate = 'Hace '.$birthdate.' dias';
                }
                if((date("Y") == $yy) && (date("m") > $mm)){
                    $birthdate = date("d") - $dd;
                    if ($birthdate > 30){
                        $birthdate = date("m") - $mm;
                        $birthdate = 'Hace '.$birthdate.' meses';
                    }
                }

                if(date("Y")>$yy){
                    $birthdate = date("Y") - $yy;
                    $birthdate = 'Hace '.$birthdate.' años';
                }

                $info['titol'] = $dades['title'];
                $info['publicat'] = $birthdate;
                $info['img_path'] = $dades['img_path'];
                $info['likes'] = $dades['likes'];
                $info['visitas'] = $dades['visits'];
                $info['privada'] = $dades['private'];
                $info['size'] = $dades['sizeImage'];
                $info['img_id'] = $dades['id'];
                $info['user_id'] = $dades['user_id'];
                $sql = "SELECT * FROM usuari WHERE id = ?";
                $dades = $this->db->fetchAssoc($sql, array($dades['user_id']));
                $info['autor'] = $dades['username'];
                $info['img_perfil'] = $dades['img_path'];




                array_push($a['info'], $info);

            }

       return $a;
    }


    public function ultimsComentaris($id){
        $comentaris = "SELECT * FROM comentari WHERE image_id = ?";
        $total = $this->db->fetchAll($comentaris, array((int)$id));
        return $total;
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