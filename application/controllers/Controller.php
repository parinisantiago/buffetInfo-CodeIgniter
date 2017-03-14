<?php

//Clase "abstracta" de controller, todos los controllers conocer el dispatcher y se fijan los permisos del usuario asociado al controller

include_once(dirname(__DIR__)."/Utils/Validador.php");


class Controller extends CI_controller
{
    protected $conf;
    protected $validator;

    public function __contruct(){
        Session::init();
        $this->validator = new Validador();
        $this->conf = new ConfiguracionModel();
        $this->rol();
    }

    public function getConfig(){
	return $this->conf->getConfiguracion();
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

    protected function render($data, $view){
		
    }

    protected function token(){
        $token = md5(uniqid(rand(), TRUE));
        Session::setValue($token, 'tokenScrf');
        $this->dispatcher->tokenScrf = Session::getValue('tokenScrf');

    }

    protected function tokenIsValid($token)
    {
        return(Session::getValue('tokenScrf') == $token);
    }

}

?>
