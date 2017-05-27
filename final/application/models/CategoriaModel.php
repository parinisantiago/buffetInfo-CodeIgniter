<?php
include_once("Model.php");
class CategoriaModel extends Model{

    public function __construct(){
        parent::__construct();
    }

    public function getCategoriaById($id){
        $this->db->select('*');
        $this->db->from('categoria');
        $this->db->where('idCategoria', $id);
        return $this->db->get()->result();

        /*return $this -> queryPreparadaSQL('SELECT * FROM categoria WHERE idCategoria = :id', array('id' => $id));*/
    }

    public function getAllCategorias(){
        $this->db->select('*');
        $this->db->from('categoria');
        return $this->db->get()->result();

        /*return $this -> queryTodasLasFilas('SELECT * FROM categoria c', array());*/
    }
    
}  
?>