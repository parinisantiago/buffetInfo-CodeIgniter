<?php
include_once("Model.php");
class TelegramModel extends Model{

    public function __construct(){
        parent::__construct();
    }
    
    public function registrar($id){
        return $this->queryPreparadaSQL('
            INSERT INTO telegram (idUsuario)
            VALUES (:idUsuario)',
            array('idUsuario' => $id));
    }
    public function eliminar($id){
        return $this->queryPreparadaSQL('
            DELETE FROM telegram
            WHERE idUsuario = :idUsuario',
            array('idUsuario' => $id));
    }
}