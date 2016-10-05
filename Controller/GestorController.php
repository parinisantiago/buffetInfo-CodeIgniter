<?php
require_once 'Controller/Controller.php';
class GestorController extends Controller
{

    public $modelProductos;

    public function __construct()
    {
        parent::__contruct();
        $this->modelProductos = new ProductosModel();
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

    public function listadoFaltantes(){

        $this->dispatcher->producto = $this->modelProductos->listarProductosFaltantes();
        $this->dispatcher->render("Gestor/ProductosTemplate.twig");

    }

    public function listadoStockMinimo(){

        $this->dispatcher->producto = $this->modelProductos->listarProductosStockMinimo();
        $this->dispatcher->render("Gestor/ProductosTemplate.twig");

    }
    
}