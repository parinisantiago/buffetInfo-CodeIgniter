<?php

//Clase "abstracta" de controller, todos los controllers conocer el dispatcher y se fijan los permisos del usuario asociado al controller

include_once(dirname(__DIR__)."/Dispatcher.php");


class Controller
{
    protected $dispatcher;
    protected $conf;

    public function __contruct(){

        Session::init();
        $this->dispatcher = new Dispatcher();
        $this->conf = new ConfiguracionModel();
        $this->dispatcher->config = $this->conf->getConfiguracion();
    }

    public function getPermission(){

        return (true);

    }
    
}

?>