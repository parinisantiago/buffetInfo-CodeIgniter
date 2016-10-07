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

    public function getAllUSer($limit, $offset)
    {
        return $this -> queryOFFSET('
            SELECT usuario.idUsuario,usuario.usuario, usuario.clave, usuario.nombre, usuario.apellido, usuario.documento, usuario.email, usuario.telefono, rol.nombre AS rol, ubicacion.nombre AS ubicacion  
            FROM usuario 
            INNER JOIN rol ON (usuario.idRol = rol.idRol ) 
            INNER JOIN ubicacion ON (usuario.idUbicacion = ubicacion.idUbicacion)
            LIMIT :limit  
            OFFSET :offset ', $limit, $offset);
    }

    public function deleteUser($idUsuario)
    {
        $this -> query('UPDATE usuario SET eliminado= 1 WHERE idUsuario = :idUsuario', array('idUsuario' =>$idUsuario));
    }
    
    public function isDeleted($username){
        return $this->queryPreparadaSQL('SELECT eliminado FROM usuario WHERE usuario = :username AND eliminado = 1 ', array('username' => $username));
    }

    public function getUserById($idUsuario){
        return $this -> queryPreparadaSQL('SELECT * FROM usuario WHERE idUsuario = :idUsuario AND eliminado = 0', array('idUsuario' => $idUsuario));
    }

    public function getUserRol($idUsuario){
        return $this -> queryPreparadaSQL('SELECT idRol FROM usuario WHere idUsuario = :idUsuario', array('idUsuario' => $idUsuario));
    }

    public function modUser($id, $nombreUsuario, $nombre, $apellido,$pass, $dni, $email,$telefono,$rol){
        return $this -> query('
              UPDATE usuario 
              SET 
                 usuario= :nombreUsuario, 
                 clave= :pass,
                 nombre= :nombre,
                 apellido= :apellido,
                 documento= :dni,
                 email= :email,
                 telefono= :telefono,
                 idRol= :rol
              WHERE 
                 idUsuario= :id',
            array(
                'nombreUsuario' => $nombreUsuario,
                'pass' => $pass,
                'nombre' => $nombre,
                'apellido' => $apellido,
                'dni' => $dni,
                'email' => $email,
                'telefono' => $telefono,
                'rol' => $rol,
                'id' => $id
            )
            );
    }

    public function addUser($nombreUsuario, $nombre, $apellido,$pass, $dni, $email,$telefono,$rol){
        return $this -> query(
            'INSERT INTO usuario (usuario, clave, nombre, apellido, documento, email, telefono, idRol, idUbicacion, eliminado) VALUES (:nombreUsuario, :pass, :nombre, :apellido, :dni, :email, :telefono, :rol, 1, 0)',
            array(
                'nombreUsuario' => $nombreUsuario,
                'pass' => $pass,
                'nombre' => $nombre,
                'apellido' => $apellido,
                'dni' => $dni,
                'email' => $email,
                'telefono' => $telefono,
                'rol' => $rol
            )
        );
    }

    public function totalUsuario(){
        return $this->queryPreparadaSQL('SELECT COUNT(*) AS total FROM usuario',array());
    }

}