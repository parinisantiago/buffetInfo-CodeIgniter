<?php
require_once 'Controller/Controller.php';
class GestorController extends Controller
{

    public function __construct()
    {
        parent::__contruct();

    }

    public function getPermission()
    {
        Session::init();
        return strcmp(Session::getValue('rol'), 'Proveedor');
    }

    public function index()
    {
        $this->dispatcher->render("Gestor/GestorIndexTemplate.twig");
    }

    public function setMensajeError($error){
        $this->dispatcher->mensajeError = $error;
        $this->index();
    }
    
    
}