<?php

class Model{
    protected $db;
    protected $stmnt;
    
    public function __construct(){
        try {
            $this->db = new PDO ( "mysql:host=" . host . ";dbname=" . db, user, pass ); //las constantes estan definidas en Utils/const.php
            $this->db->setAttribute ( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ ); // para retornar objetos
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//Para retornar PDOExecption (creo)
        }catch (PDOException $e) {
            print "¡Error!: " . $e->getMessage() . "<br/>";
	}
    }

    protected function queryPreparadaSQL($sql, $parametros){
        $this->query($sql, $parametros);
	    return $this -> stmnt -> fetch(); //retorna el valor de la consulta como un objeto
    }
    protected function queryOFFSET($sql, $limit, $offset){
        $this->stmnt = $this->db->prepare($sql);
        $this->stmnt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $this->stmnt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $this->stmnt->execute();
        return $this->stmnt ->fetchAll();
    }
    protected function queryOFFSETDIA($sql, $limit, $offset, $fecha){
        $this->stmnt = $this->db->prepare($sql);
        $this->stmnt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $this->stmnt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $this->stmnt->bindValue(':fecha', $fecha);
        $this->stmnt->execute();
        return $this->stmnt ->fetchAll();
    }
    protected function queryTodasLasFilas($sql, $parametros){
        $this->stmnt = $this->db->prepare($sql);
        $this->stmnt->execute($parametros);
        return $this -> stmnt -> fetchAll(); //retorna el valor de la consulta como un objeto
    }
    protected function query($sql, $parametros){
        $this->stmnt = $this->db->prepare($sql);
	    $this->stmnt->execute($parametros);
	    return true;
    }

    protected function lastId($sql, $parametros){
        $this->query($sql, $parametros);
        return $this->db->lastInsertId();
    }
}

?>