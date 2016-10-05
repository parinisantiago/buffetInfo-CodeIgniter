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
        return $this -> queryTodasLasFilas("SELECT * FROM rol", array());
    }

    public function getRolById($id)
    {
        return $this ->queryPreparadaSQL("SELECT * FROM rol WHERE idRol = :id", array('id' => $id));
    }
}