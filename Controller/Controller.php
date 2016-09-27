<?php

//Clase "abstracta" de controller, todos los controllers conocer el dispatcher y se fijan los permisos del usuario asociado al controller

include_once(dirname(__DIR__)."/Dispatcher.php");


class Controller
{
    protected $dispatcher;

    public function __contruct(){

        $this->dispatcher = new Dispatcher();

    }

    public function getPermission(){

        return (true);

    }
}

?>