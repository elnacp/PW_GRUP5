<?php

namespace SilexApp\Model\Repository;


use Doctrine\DBAL\Connection;
use Silex\Application;
use Doctrine\DBAL\Configuration;


class Ajax{

    public function ultimesImatges($app, $from, $to)
    {

        $query = "select * from imatge where id>$from and id<$to order by created_at ";
        $result = $app['db']->fetchAll($query);
        $data = '';
        if($result>0)
        {
            $sql = "SELECT * FROM logejat";
            $d = $app['db']->fetchAssoc($sql);
            $id = $d['user_id'];
            $sql = "SELECT username FROM usuari WHERE id = ?";
            $r = $app['db']->fetchAssoc($sql, array($id));
            $usuari = $r['username'];
            foreach ( $result as $s){
                $id = $s['user_id'];
                $sql1 = "SELECT * FROM usuari WHERE id = ?";
                $stm1 = $app['db']->fetchAssoc($sql1, array((int)$id));
                $autor = $stm1['username'];
                $profilePic = $stm1['img_path'];
                $title = $s['title'];
                $image = $s['img_path'];
                $image = str_replace(" ", "_", $image);

                $dia = $s['created_at'];
                $sql2 = "SELECT count(*) as total FROM likes WHERE image_id = ?";
                $l = $app['db']->fetchAssoc($sql2, array((int)$s['id']));
                $likes = $l['total'];
                $visites = $s['visits'];
                $href = "/visualitzacioImatge/".$s['id'];



                $href1 = "/likeHome/".$s['id']."/".$usuari;
                $hrefComentari = "/comentari/".$s['id']."/".$usuari;


                ////////////
                $data = $data."<div class=\"[ panel panel-default ] panel-google-plus\">
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
                if($app['session']->has('name')){

                    $data.= $data."<a  href=".$href1." class=\"[ btn btn-default ]\">Likes: +".$likes."</a>";
                }else{
                    $data = $data."<a class=\"[ btn btn-default ]\">Likes: +".$likes."</a>";
                }
                $data = $data." <button type=\"button\" class=\"[ btn btn-default ]\">
                                                     Visitas: +". $visites."</span>
                                                </button>
                                                <div class=\"input-placeholder\">Escribe un comentario...</div>
                                            </div>";
                if($app['session']->has('name')) {
                    $data = $data."<div class=\"panel-google-plus-comment\">
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

                $data = $data."</div>";

                //img - titol - autor - dia publicación - numero likes - número de visualizaciones
            }
            $data=$data.'<div class="final" val="'.$id.'" ></div>';
            return $data;
        }


    }

    public function main(Application $app)
    {

            $sql = "SELECT id FROM imatge ORDER BY id DESC LIMIT 1";
            $d  = $app['db']->fetchAssoc($sql);
            $id = $d['id'];
            $next = $id - 5;
            $data = $this->query($app,$id, $next);
            return $data;

    }

}

?>