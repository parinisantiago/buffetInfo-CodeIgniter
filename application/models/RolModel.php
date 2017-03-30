<?php
include_once("Model.php");

class RolModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAllRols()
    {
        $this->db->select('*');
        $this->db->from('rol');
        return $this->db->get()->result();
       /* return $this -> queryTodasLasFilas("SELECT * FROM rol", array());*/
    }

    public function getRolById($id)
    {
        $this->db->select('*');
        $this->db->from('rol');
        $this->db->where('idRol', $id);
        return $this->db->get()->result();
       /* return $this ->queryPreparadaSQL("SELECT * FROM rol WHERE idRol = :id", array('id' => $id));*/
    }

    public function getAllUbicacion()
    {
        $this->db->select('*');
        $this->db->from('ubicacion');
        return $this->db->get()->result();

       /* return $this -> queryTodasLasFilas("SELECT * FROM ubicacion", array());*/
    }
}