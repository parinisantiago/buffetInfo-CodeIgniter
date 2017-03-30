<?php
include_once("Model.php");
class UserModel extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function userExist($username)
    {
        $this->db->select('usuario');
        $this->db->from('usuario');
        $this->db->where('usuario', $username);
        $this->db->where('eliminado', 0);
        $this->db->where('habilitado', 0);
        return $this->db->get()->result();
        /* return $this->queryPreparadaSQL('SELECT usuario FROM usuario WHERE usuario = :username AND eliminado = 0 AND habilitado = 0', array('username'=> $username));*/
    }

    public function userExistInDB($username)
    {
        $this->db->select('usuario');
        $this->db->from('usuario');
        $this->db->where('usuario', $username);
        $exist = $this->db->get()->result();
        return $exist[0];
        /* return $this->queryPreparadaSQL('SELECT usuario FROM usuario WHERE usuario = :username', array('username'=> $username));*/
    }

    public function passDontMissmatch($pass)
    {
        $this->db->select('clave');
        $this->db->from('usuario');
        $this->db->where('clave', $pass);
    /*    return $this -> queryPreparadaSQL('SELECT clave FROM usuario WHERE clave = :pass', array('pass' => $pass));*/
    }

    public function getUser($username, $pass)
    {
        $this->db->select('*');
        $this->db->from('usuario');
        $this->db->where('usuario', $username);
        $this->db->where('clave', $pass);
        return $this->db->get()->result();

      /*  return $this -> queryPreparadaSQL('SELECT * FROM usuario WHERE usuario = :username AND clave = :pass', array('username' => $username, 'pass' => $pass ));*/
    }

    public function getAllUSer($limit, $offset)
    {

        $this->db->select('usuario.idUsuario,usuario.usuario, usuario.clave, usuario.nombre, usuario.apellido, usuario.documento, usuario.email, usuario.telefono, rol.nombre AS rol, ubicacion.nombre AS ubicacion');
        $this->db->from('usuario');
        $this->db->join('rol', 'usuario.idRol = rol.idRol');
        $this->db->join('ubicacion', 'usuario.idUbicacion = ubicacion.idUbicacion');
        $this->db->where('eliminado', 0);
        $this->db->limit($limit);
        $this->db->offset($offset);
        return $this->db->get()->result();

        /*return $this -> queryOFFSET('
            SELECT usuario.idUsuario,usuario.usuario, usuario.clave, usuario.nombre, usuario.apellido, usuario.documento, usuario.email, usuario.telefono, rol.nombre AS rol, ubicacion.nombre AS ubicacion  
            FROM usuario 
            INNER JOIN rol ON (usuario.idRol = rol.idRol ) 
            INNER JOIN ubicacion ON (usuario.idUbicacion = ubicacion.idUbicacion) WHERE eliminado = 0
            LIMIT :limit  
            OFFSET :offset ', $limit, $offset);*/
    }

    public function deleteUser($idUsuario)
    {

        $data = array('eliminado' => 1);
        $this->db->where('idUsuario', $idUsuario);
        $this->db->update('usuario', $data);

      /*  $this -> query('UPDATE usuario SET eliminado= 1 WHERE idUsuario = :idUsuario', array('idUsuario' =>$idUsuario));*/
    }
    
    public function isDeleted($username){
        $this->db->select('eliminado');
        $this->db->from('usuario');
        $this->db->where('usuario', $username);
        $this->db->where('eliminado', 1);
        return $this->db->get()->result();

        /*return $this->queryPreparadaSQL('SELECT eliminado FROM usuario WHERE usuario = :username AND eliminado = 1 ', array('username' => $username));*/
    }

    public function getUserById($idUsuario){
        $this->db->select('*');
        $this->db->from('usuario');
        $this->db->where('idUsuario', $idUsuario);
        $this->db->where('eliminado', 0);
        $user = $this->db->get()->result();
        return $user[0];

        /*    return $this -> queryPreparadaSQL('SELECT * FROM usuario WHERE idUsuario = :idUsuario AND eliminado = 0', array('idUsuario' => $idUsuario));*/
    }

    public function getUserRol($idUsuario){
        $this->db->select('idRol');
        $this->db->from('usuario');
        $this->db->where('idUsuario', $idUsuario);
        return $this->db->get()->result();

     /*   return $this -> queryPreparadaSQL('SELECT idRol FROM usuario WHere idUsuario = :idUsuario', array('idUsuario' => $idUsuario));*/
    }

    public function modUser($id, $nombreUsuario, $nombre, $apellido,$pass, $dni, $email,$telefono,$rol, $ub, $hab){
       $data=  array(
           'usuario' => $nombreUsuario,
           'clave' => $pass,
           'nombre' => $nombre,
           'apellido' => $apellido,
           'documento' => $dni,
           'email' => $email,
           'telefono' => $telefono,
           'idRol' => $rol,
           'idUbicacion' => $ub,
           'habilitado' => $hab
       );
       $this->db->where('idUsuario', $id);
       $this->db->update('usuario', $data);

      /*  return $this -> query('
              UPDATE usuario 
              SET 
                 usuario= :nombreUsuario, 
                 clave= :pass,
                 nombre= :nombre,
                 apellido= :apellido,
                 documento= :dni,
                 email= :email,
                 telefono= :telefono,
                 idRol= :rol,
                 idUbicacion = :ub,
                 habilitado = :hab
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
                'id' => $id,
                'ub' => $ub,
                'hab' => $hab
            )
            );*/
    }

    public function addUser($nombreUsuario, $nombre, $apellido,$pass, $dni, $email,$telefono,$rol, $ub){
        $data = array(
            'usuario' => $nombreUsuario,
            'clave' => $pass,
            'nombre' => $nombre,
            'apellido' => $apellido,
            'documento' => $dni,
            'email' => $email,
            'telefono' => $telefono,
            'idRol' => $rol,
            'idUbicacion' => $ub,
            'eliminado' => 0
        );
        $this->db->insert('usuario', $data);

        /*return $this -> query(
            'INSERT INTO usuario (usuario, clave, nombre, apellido, documento, email, telefono, idRol, idUbicacion, eliminado)
             VALUES (:nombreUsuario, :pass, :nombre, :apellido, :dni, :email, :telefono, :rol, :ub, 0)',
            array(
                'nombreUsuario' => $nombreUsuario,
                'pass' => $pass,
                'nombre' => $nombre,
                'apellido' => $apellido,
                'dni' => $dni,
                'email' => $email,
                'telefono' => $telefono,
                'rol' => $rol,
                'ub' => $ub
            )
        );*/
    }

    public function totalUsuario(){
        $this->db->select('COUNT(*) AS total');
        $this->db->from('usuario');
        $this->db->where('eliminado', 0);
        $total = $this->db->get()->result();
        return $total[0];
       /* return $this->queryPreparadaSQL('SELECT COUNT(*) AS total FROM usuario WHERE eliminado = 0',array());*/
    }

}