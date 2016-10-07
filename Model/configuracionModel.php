<?php

require_once("Model.php");

class configuracionModel extends Model
{

    public function __construct(){
        parent::__construct();
    }

    public function getConfiguracion(){
        return $this->queryPreparadaSQL("SELECT * FROM configuracion");
    }

    public function updateConf($Prod){
        return $this -> query("
            UPDATE producto
            SET titulo = :titulo,
                descripcion = :descripcion,
                mail = :mail,
                cantPagina = :cantPagina,
                habilitado = :habilitado,
                mensajeHabilitado = :mensajeHabilitado",
            array('titulo' => $Prod["titulo"],
                'descripcion' => $Prod["descripcion"],
                'mail' => $Prod["mail"],
                'cantPagina' => $Prod["cantPagina"],
                'habilitado' => $Prod["habilitado"],
                'mensajeHabilitado' => $Prod["mensajeHabilitado"],
                ));
    }

}