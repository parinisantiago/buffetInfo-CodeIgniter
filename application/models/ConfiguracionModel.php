<?php

require_once("Model.php");

class ConfiguracionModel extends Model
{

    public function __construct(){
        parent::__construct();
    }

    public function getConfiguracion(){
        $this->db->select('*');
        $this->db->from('configuracion');
        $config = $this->db->get()->result();
        return $config[0];
        /*return $this->queryPreparadaSQL("SELECT * FROM configuracion", array());*/
    }

    public function updateConf($Prod){
        $data = array(
            'titulo' => $Prod["titulo"],
            'descripcion' => $Prod["descripcion"],
            'mail' => $Prod["email"],
            'cantPagina' => $Prod["lista"],
            'habilitado' => $Prod["habilitado"],
            'mensajeHabilitado' => $Prod["mensaje"]
        );
        $this->db->update('configuracion', $data);

        /* $this -> query("
            UPDATE configuracion
            SET titulo = :titulo,
                descripcion = :descripcion,
                mail = :mail,
                cantPagina = :cantPagina,
                habilitado = :habilitado,
                mensajeHabilitado = :mensajeHabilitado",
            array('titulo' => $Prod["titulo"],
                'descripcion' => $Prod["descripcion"],
                'mail' => $Prod["email"],
                'cantPagina' => $Prod["lista"],
                'habilitado' => $Prod["habilitado"],
                'mensajeHabilitado' => $Prod["mensaje"],
                ));*/
    }

}