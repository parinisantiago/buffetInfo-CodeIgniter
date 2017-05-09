<?php
include_once('MainController.php');

class ConfigController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if($this->permissions()) $this->display("IndexTemplate.twig");
    }

    public function setMensajeError($error)
    {
        $this->addData('mensajeError', $error);
        $this->index();
    }

    public function getPermission()
    {
        Session::init();
        return (Session::getValue('rol') == '0');
    }

    public function configuracionSitio()
    {
        if($this->permissions()) $this->display("ConfiguracionTemplate.twig");
        else $this->setMensajeError('Debes iniciar sesion para acceder a este sitio');
    }

    public function changeConf()
    {
        if($this->permissions())
        {
            $this->validarConf();
            $this->conf->updateConf($_POST);
            $this->addData('config', $this->conf->getConfiguracion());
            $this->display("ConfiguracionTemplate.twig");
        }
    }

    private function validarConf()
    {
        if($this->permissions())
        {
            try
            {
                if (!isset($_POST['submitButton'])) throw new Exception('Apreta el boton de modificar macho');
                if (!isset($_POST['titulo'])) throw new Exception('Falta escribir el titulo');
                elseif (!preg_match("/^[a-zA-Z0-9 ]+$/", $_POST['titulo'])) throw new Exception('Valor del titulo no valido');
                if (!isset($_POST['descripcion'])) throw new Exception('Falta escribir el descripcion');
                elseif (!preg_match("/^[a-zA-Z0-9 ;.,àáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹ]+$/", $_POST['descripcion'])) throw new Exception('Valor de la  descripcion no valido');
                if (!isset($_POST['mensaje'])) throw new Exception('Falta escribir el mensaje');
                elseif (!preg_match("/^[a-zA-Z ]+$/", $_POST['mensaje'])) throw new Exception('Valor del mensaje no valido');
                if (!isset($_POST['lista'])) throw new Exception('Falta escribir el numero de la lista');
                elseif (!preg_match("/^[0-9]+$/", $_POST['lista'])) throw new Exception('Valor del numero de la lista no valido');
            }
            catch (Exception $e)
            {
                $controller = new MainController();
                $controller->index();
            }
        }
    }
}