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
        $this->rol();
    }

    public function getPermission(){

        return (true);

    }
    public function getRol(){
        return Session::getValue('rol');
    }

    protected function rol()
    {
        if (Session::userLogged()) $this->dispatcher->rol = Session::getValue('rol');
        else  $this->dispatcher->rol = "NaN";
    }
}

?>