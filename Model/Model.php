<?php

class Model
{

    private $db;
    
    public function _construct(){
        
        $this->base = new PDO ( "mysql:host=" . host . ";dbname=" . db, user, pass ); //las constantes estan definidas en Utils/const.php
        
    }
    
}

?>