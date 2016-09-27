<?php
include_once("Model.php");
class MainUserModel extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function userExist($username)
    {
        return $this->queryPreparadaSQL('SELECT usuario FROM usuario WHERE usuario = :username', array('username'=> $username));
    }

    public function passDontMissmatch($pass)
    {
        return $this -> queryPreparadaSQL('SELECT clave FROM usuario WHERE clave = :pass', array('pass' => $pass));
    }

    public function getUser($username, $pass)
    {
        return $this -> queryPreparadaSQL('SELECT * FROM usuario WHERE usuario = :username AND clave = :pass', array('username' => $username, 'pass' => $pass ));
    }

}