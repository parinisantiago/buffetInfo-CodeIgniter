<?php

require_once("Model.php");

class configuracionModel extends Model
{

    public function __construct(){
        parent::__construct();
    }

    public function getConfiguracion(){
        return $this->queryPreparadaSQL("SELECT * FROM configuracion", array());
    }

    public function updateConf($Prod){
         $this -> query("
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
                ));
    }

}