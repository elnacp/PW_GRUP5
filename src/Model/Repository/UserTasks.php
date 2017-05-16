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
        $id = $stm['id'];
        $sql = "INSERT INTO logejat ( user_id) VALUE ($id)";
        $this->db->query($sql);
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

    public function checkUser($username)
    {
        $trobat = false;
        $sql = "SELECT * FROM usuari WHERE username = ?";
        $user = $this->db->fetchAssoc($sql, array((string)$username));
        if ($user) {
            $trobat = true;
        } else {
            $trobat = false;
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
        //var_dump($private);
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

    public function dadesImatges()
    {
        $sql = "SELECT user_id FROM logejat";
        $stm = $this->db->fetchAssoc($sql);
        $id = $stm['user_id'];
        $sql = "SELECT * FROM imatge WHERE user_id = ?";
        $stm = $this->db->fetchAll($sql, array((int)$id));

        $dades = "";

        foreach ($stm as $s) {
            $eliminar = "/eliminar/" . $s['id'];
            $editar = "/editar/" . $s['id'];
            if ($s['sizeImage'] == 400) {
                $dades = $dades . "<div class=\"gallery_product col-lg-4 col-md-4 col-sm-4 col-xs-6 filter hdpe\">
                            <h1>" . $s['title'] . "</h1>
                            <img src=" . $s['img_path'] . " class=\"img-responsive\" width=\"400\" height=\"300\">
                            <li> <a href=" . $eliminar . "> Eliminar </a> </li>
                            <li><a href=" . $editar . "> Editar </a> </li>
                        </div>";
            } else {
                $dades = $dades . "<div class=\"gallery_product col-lg-4 col-md-4 col-sm-4 col-xs-6 filter hdpe\">
                            <h1>" . $s['title'] . "</h1>
                            <img src=" . $s['img_path'] . " class=\"img-responsive\" width=\"100\" height=\"100\">
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
        } else {
            $trobat = false;
        }
        return $trobat;


    }


    public function deleteImage($id)
    {
        $sql = "DELETE  FROM imatge WHERE id = $id";
        $this->db->exec($sql);
    }

    public function editInformation($title, $path_name, $private, $id, $sizeImage)
    {
        var_dump($sizeImage);
        $sql = "UPDATE imatge SET title = ?, img_path  = ?, private = ?, sizeImage = ? WHERE id = ?";
        $this->db->executeUpdate($sql, array($title, $path_name, $private, $sizeImage, (int)$id));
    }


    public function home1($log, $usuari)
    {

        $sql = "SELECT * FROM imatge  ORDER  BY visits DESC LIMIT 5";
        $stm = $this->db->fetchAll($sql);
        $c1 = 5;
        $c2 = 5;
        $imgMesVistes = "<div class=\"[ panel panel-default ] panel-google-plus\">
                             <h2> Imagenes más vistas: </h2>
                        </div>";
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
                                                <h5><span>Publicat - </span> - <span>" . $dia . "</span> </h5>
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
            $sql = "SELECT * FROM imatge WHERE id = ?";
            $dades = $this->db->fetchAll($sql, array($id));
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
            $dades = $dades . "<div class=\"gallery_product col-lg-4 col-md-4 col-sm-4 col-xs-6 filter hdpe\">
                           <h1> <a href=" . $s['title'] . "></a></h1>
                           <img src=" . $s['img_path'] . " class=\"img-responsive\" width=\"100\" height=\"100\">
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
                $sql = "SELECT * FROM imatge WHERE user_id = ? ORDER BY created_at ASC";
                $stm = $this->db->fetchAll($sql, array((int)$id));
                break;
            case 2:
                $sql = "SELECT * FROM imatge WHERE user_id = ? ORDER BY likes ASC";
                $stm = $this->db->fetchAll($sql, array((int)$id));

                break;
            case 3:
                $sql = "SELECT * FROM imatge WHERE user_id = ? ORDER BY (SELECT SUM(image_id) FROM comentari,imatge WHERE comentari.image_id = imatge.id) ASC";
                $stm = $this->db->fetchAll($sql, array((int)$id));
                break;

            case 4:
                $sql = "SELECT * FROM imatge WHERE user_id = ? ORDER BY visits ASC";
                $stm = $this->db->fetchAll($sql, array((int)$id));
                break;

            default:
                $sql = "SELECT * FROM imatge WHERE user_id = ? ORDER BY created_at ASC";
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
                           <img src=" . $s['img_path'] . " class=\"img-responsive\" width=\"100\" height=\"100\">
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

        $visualitzacioImatge = "/perfil/" . $username;
        $dades = $dades .
            "<div class=\"panel-heading\">
                 <h3 class=\"panel-title\">$username</h3>
            </div>
            
            <div class=\"panel-body\">
            
                <div class=\"col-md-3 col-lg-3 \" align=\"center\">
                    <img src=". $s4 ." alt=\"User Pic\" name=\"img_path\" id=\"perfil\"  class=\"img-circle img-responsive\">
                </div>
            <div class=\"row\">
                 <div class=\" col-md-9 col-lg-9 \">
                    <table class=\"profileTable\">
                        <tbody>
                        <tr>
                            <td>Nom d'usuari:</td>
                            <td>$username</td>
                        </tr>
                        <tr>
                            <td>Imatges Publicades:</td>
                            <td>$s2</td>
                        </tr>
                        <tr>
                            <td>Comentaris Realitzats:</td>
                            <td>$s3</td>
                        </tr>
    
                        </tbody>
                    </table>
                </div>
            </div>";

        return $dades;
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
                                                <h5><span>Publicat - </span> - <span>" . $dia . "</span> </h5>
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

    public function novaInfo(){
        $sql = "SELECT * FROM imatges ORDER  BY created_at";
        $dades = 



    }


    public function ultimsComentaris($id){
        $comentaris = "SELECT * FROM comentari WHERE image_id = ?";
        $total = $this->db->fetchAll($comentaris, array((int)$id));
        return $total;
    }




}