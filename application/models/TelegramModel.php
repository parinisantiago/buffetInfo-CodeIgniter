<?php
include_once("Model.php");
class TelegramModel extends Model{

    public function __construct(){
        parent::__construct();
    }
    
     public function buscar($id){
        $this->db->select('idUsuario');
        $this->db->from('telegram');
        $this->db->where('idUsuario', $id);
        return $this->db->get()->result();
        /*return $this->queryPreparadaSQL('
            SELECT idUsuario
            FROM telegram
            WHERE idUsuario = :idUsuario',
            array('idUsuario' => $id));*/
    }
    public function registrar($id){
         $data = array(
             'idUsuario' => $id
         );
         $this->db->result('telegram', $data);


       /* return $this->queryPreparadaSQL('
            INSERT INTO telegram (idUsuario)
            VALUES (:idUsuario)',
            array('idUsuario' => $id));*/
    }
    public function eliminar($id){
        $this->db->where('idUsuario', $id);
        $this->db->delete('telegram');

       /* return $this->queryPreparadaSQL('
            DELETE FROM telegram
            WHERE idUsuario = :idUsuario',
            array('idUsuario' => $id));*/
    }
    public function getAll(){
        $this->db->select('idUsuario');
        $this->db->from('telegram');
        return $this->db->get()->result();
/*        return $this->queryPreparadaSQL('
            SELECT idUsuario
            FROM telegram',
            array());*/
    }
}