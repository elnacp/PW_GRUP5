<?php


namespace SilexApp\Model\Repository;

use Doctrine\DBAL\Connection;
use Silex\Application;
use Doctrine\DBAL\Configuration;

interface UserModel
{

    public function validateUser($username, $password);

    public function checkUser($username);

    public function RegisterUser($nickname, $email, $birthdate, $password, $img);

    public function logejarUsuari($name);

    public function validateEditProfile($name, $birth, $pass1, $img1);

    public function DBnewPost($title, $path_name, $private);

    public function dadesImatges();

    public function ActivateUser($nickname);

    public function deleteImage($id);

    public function editInformation($title, $path_name, $private, $id);

    public function home1($log, $usuari);

    public function incrementarVisites($id);

    public function like($id, $usuari_log);

    public function deleteActualPic($nickname);

    public function comentari($id, $usuari_log);

    public function comentarisUser();

    public function eliminarComentari($id);

    public function editarComentari($id);

    public function notificacio($id, $usuari_log, $type);






}
