<?php

class Model
{

    private $db;
	private $stmnt;
    
    public function __construct()
	{
		try {

        	$this->db = new PDO ( "mysql:host=" . host . ";dbname=" . db, user, pass ); //las constantes estan definidas en Utils/const.php
			$this->db->setAttribute ( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ ); // para retornar objetos
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//Para retornar PDOExecption (creo)

		}
		catch (PDOException $e) {
			print "¡Error!: " . $e->getMessage() . "<br/>";
		}
    }


	protected function queryPreparadaSQL($sql, $parametros)
	{

		$this->query($sql, $parametros);
		
		return $this -> stmnt -> fetch(); //retorna el valor de la consulta como un objeto

	}

	protected function queryTodasLasFilas($sql, $parametros)
	{

		$this->query($sql, $parametros);

		return $this -> stmnt -> fetchAll(); //retorna el valor de la consulta como un objeto
	}
	
	protected function query($sql, $parametros)
	{
		$this->stmnt = $this->db->prepare($sql);
		$this->stmnt->execute($parametros);
		return true;
	}
}

?>