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
        return $this->queryPreparadaSQL('SELECT name FROM user WHERE username = :username', array('username'=> $username));
    }

    public function passDontMissmatch($pass)
    {
        return $this -> queryPreparadaSQL('SELECT pass FROM user WHERE pass = :pass', array('pass' => $pass));
    }

    public function getUser($username, $pass)
    {
        $user = $this -> queryPreparadaSQL('SELECT * FROM user WHERE username = :username AND pass = :pass', array('username' => $username, 'pass' => $pass ));

    }

}