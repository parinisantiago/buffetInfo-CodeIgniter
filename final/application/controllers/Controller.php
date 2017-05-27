<?php

//Clase "abstracta" de controller, todos los controllers conocer el dispatcher y se fijan los permisos del usuario asociado al controller

include_once(dirname(__DIR__)."/Utils/Validador.php");
include_once(dirname(__DIR__) . "/libraries/Session.php");
include_once(dirname(__DIR__) . "/models/UserModel.php");
include_once(dirname(__DIR__)."/models/MenuModel.php");

class Controller extends CI_controller
{
    protected $validator;
    protected $data;

    public function __construct()
    {
        parent::__construct();
        Session::init();
        $this->validator = new Validador();
        $this->conf = new ConfiguracionModel();
        $this->rol();
        $this->habilitado();
        $this->load->model('MenuModel');
    }
    public function noHabilitado(){
        $this->addData("mensajeError", "el usuario no esta habilitado");
        $this->display('MainTemplate.twig');
    }
    public function getConfig()
    {
	    return $this->conf->getConfiguracion();
    }

    public function getPermission()
    {
        return (true);
    }

    public function getRol()
    {
        return Session::getValue('rol');
    }

    protected function habilitado()
    {
        if (Session::userLogged()) $this->data['userHabilitado'] = Session::getValue('userHabilitado');
        else  $this->data['userHabilitado'] = "NaN";
    }

    protected function rol()
    {
        if (Session::userLogged()) $this->data['rol'] = Session::getValue('rol');
        else  $this->data['rol'] = "NaN";
    }

    protected function display($view)
    {
        $this->load->library('twig');
        $this->addData('config',$this->getConfig());
        //var_dump($this->data);
        $this->twig->render($view,$this->data);
    }

    protected function token()
    {
        $token = md5(uniqid(rand(), TRUE));
        Session::setValue($token, 'tokenScrf');
        $this->data['tokenScrf'] = Session::getValue('tokenScrf');
    }

    protected function tokenIsValid($token)
    {
        return(Session::getValue('tokenScrf') == $token);
    }

    protected function addData($key, $value)
    {
        $this->data[$key] = $value;
    }

    protected function permissions()
    {
        try
        {
            if (!$this->getPermission()) throw new Exception('El usuario no posee permisos para acceder a esta funcionalidad');
            return true;
        }
        catch (Exception $e)
        {
            $this->addData('mensajeError', "El usuario actual no posee permisos para realizar esa operación");
            if( Session::userLogged() )
            {
                $this->display("IndexTemplate.twig");
            }
            else
            {
                $this->addData('menu', $this->MenuModel->getMenuByDia2(NULL, NULL, NULL));
                $this->display('MainTemplate.twig');
            }
        }
    }
}

?>
