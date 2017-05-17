<?php


namespace SilexApp\Model\Repository;

use Doctrine\DBAL\Connection;
use Silex\Application;
use Doctrine\DBAL\Configuration;

interface UserModel
{

    public function validateUser($username, $password);

    public function checkUser($username, $email);

    public function RegisterUser($nickname, $email, $birthdate, $password, $img);

    public function logejarUsuari($name);

    public function validateEditProfile($name, $birth, $pass1, $img1);

    public function DBnewPost($title, $path_name, $private, $sizeImage);

    public function dadesImatges($string);

    public function ActivateUser($nickname);

    public function deleteImage($id);

    public function editInformation($title, $path_name, $private, $id, $sizeImage);

    public function home1($log, $usuari, $action);

    public function incrementarVisites($id);

    public function like($id, $usuari_log);

    public function deleteActualPic($nickname);

    public function comentari($id, $usuari_log);

    public function comentarisUser();

    public function eliminarComentari($id);

    public function editarComentari($id);

    public function notificacio($id, $usuari_log, $type);

    public function notificacionsUser();

    public function visualitzada($id);

    public function getActualProfilePic($username, $img);

    public function ultimesImatges($log, $usuari);

    public function novaInfo();

    public function ultimsComentaris($id);

    public function createUserActivation($id, $code);

    public function searchValidation($id, $code);

    public function getName($id);



}

