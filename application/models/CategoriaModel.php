<?php
include_once("Model.php");
class CategoriaModel extends Model{

    public function __construct(){
        parent::__construct();
    }

    public function getCategoriaById($id){
        return $this -> queryPreparadaSQL('SELECT * FROM categoria WHERE idCategoria = :id', array('id' => $id));
    }

    public function getAllCategorias(){
        return $this -> queryTodasLasFilas('SELECT * FROM categoria c', array());
    }
    
}  
?>