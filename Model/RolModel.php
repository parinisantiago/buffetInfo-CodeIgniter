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
}