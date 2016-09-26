<?php

class Model
{

    private $db;
    
    public function _construct(){
        try {
        	$this->base = new PDO ( "mysql:host=" . host . ";dbname=" . db, user, pass ); //las constantes estan definidas en Utils/const.php
			$this->base->setAttribute ( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ ); // para retornar objetos
		}
		catch (PDOException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
			die();
		}
    }
	public function querySQL($sql){
		$conexion = $this-> _construct();
		return $conexion->query($sql);
	}
		
	public function queryPreparadaSQL($sql, $parametros){
		$conexion = $this-> _construct();
	    $resultado = $conexion->prepare($sql);
	    $resultado->execute($parametros);
	    return $resultado;
	}
}

?>