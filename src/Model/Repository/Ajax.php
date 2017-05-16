<?php


namespace SilexApp\Model\Repository;
use Silex\Application;


class Ajax{

    public function query($from,$to)
    {
        $sql = "SELECT * FROM imatge ORDER  BY created_at DESC LIMIT 5";
        $stm = $this->db->fetchAll($sql);
        $c1 = 5;
        $imgMesVistes = "";
        foreach ($stm as $s) {
            if($s['id'] == $from ) {
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
                $hrefPerfil = "/perfil/" . $autor;
                $imgMesVistes = $imgMesVistes . "<div class=\"[ panel panel-default ] panel-google-plus\">

                                            <div class=\"panel-heading\">                                         
                                                <h2>
                                                    <a href=" . $href . ">" . $title . " </a>
                                                </h2>
                                                <h3>
                                                    <a href=" . $hrefPerfil . "> " . $autor . " </a>
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
                if ($c1 == 1) {
                    $id_seguent = $id + 1;
                    $imgMesVistes = $imgMesVistes . "<br>
                                                <div class=\"final\" val=\"'.$id_seguent.'\" ></div>
                                                <button class=\"btn btnt-primary loadmore\" >Loadmore</button>";
                }

                $imgMesVistes = $imgMesVistes . "</div>";
                $c1--;
                //img - titol - autor - dia publicación - numero likes - número de visualizaciones
            }
        }

    }

    public function main()
    {
        if(isset($_POST['from']))
        {
            $from=$_POST['from'];
            echo($from);
            $to = $from-5;
            $data = $this->query($from,$to);
            echo $data;
        }else
        {
            $data = $this->query(0,11);
            return $data;
        }
    }

}


$obj = new Ajax();
$data = $obj->main();

?>