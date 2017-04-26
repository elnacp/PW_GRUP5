<?php


namespace ProjectesWeb\Model\Entity;

/**
 *  Task
 */
class UserDB
{
    private $id;
    private $username;
    private $email;
    private $birthdate;
    private $password;
    private $img_path;
    private $active;

    public function __construct($id, $username, $email, $birthdate, $password, $img_path, $active)
    {
       $this->id = $id;
       $this->username = $username;
       $this->email = $email;
       $this->birthdate = $birthdate;
       $this->password = $password;
       $this->img_path = $img_path;
       $this->active = $active;

    }




}
