<?php

namespace ProjectesWeb\Model\Entity;

class ImatgeDB{

    private $id;
    private $user_id;
    private $title;
    private $img_path;
    private $visits;
    private $private;
    private $created_at;

    public function __construct($id, $user_id, $title, $img_path, $visits, $private, $created_at)
    {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->title = $title;
        $this->img_path = $img_path;
        $this->visits = $visits;
        $this->private = $private;
        $this->created_at = $created_at;
    }
}