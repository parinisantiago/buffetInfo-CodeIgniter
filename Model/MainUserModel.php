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

    public function getAllUSer(){
        return $this -> queryTodasLasFilas('SELECT usuario.idUsuario,usuario.usuario, usuario.clave, usuario.nombre, usuario.apellido, usuario.documento, usuario.email, usuario.telefono, rol.nombre AS rol, ubicacion.nombre AS ubicacion  FROM usuario INNER JOIN rol ON (usuario.idRol = rol.idRol ) INNER JOIN ubicacion ON (usuario.idUbicacion = ubicacion.idUbicacion)', array());
    }

    public function deleteUser($idUsuario)
    {
        $this -> query('UPDATE usuario SET eliminado= 1 WHERE idUsuario = :idUsuario', array('idUsuario' =>$idUsuario));
    }
    
    public function isDeleted($username){
        return $this->queryPreparadaSQL('SELECT eliminado FROM usuario WHERE usuario = :username AND eliminado = 1', array('username' => $username));
    }

    public function getUserById($idUsuario){
        return $this -> queryPreparadaSQL('SELECT * FROM usuario WHERE idUsuario = :idUsuario', array('idUsuario' => $idUsuario));
    }

}