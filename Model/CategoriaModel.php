<?php
include_once("Model.php");
class CategoriaModel extends Model{

    public function __construct(){
        parent::__construct();
    }

    public function getAllCategorias(){
        return $this -> queryTodasLasFilas('SELECT * FROM categoria c', array());
    }
    
}  
?>