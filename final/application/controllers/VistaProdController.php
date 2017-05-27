<?php
require_once 'BackendController.php';
class VistaProdController extends BackendController{

    public function __construct()
    {
        parent::__construct();
    }

    public function getPermission()
    {
        Session::init();  
        return (Session::getValue('rol') == '1');
    }

    public function setMensajeError($error)
    {
        $this->addData('mensajeError', $error);
        $this->index();
    }
    /*---listado productos---*/
    public function listadoFaltantes()
    {
        if($this->permissions())
        {
            $this->paginaCorrecta($this->ProductosModel-> totalProductosFaltantes());
            $this->addData('producto', $this->ProductosModel->listarProductosFaltantes($this->conf->getConfiguracion()->cantPagina,$_GET['offset']));
            $this->addData('pag', $_GET['pag']);
            $this->addData('method', "listadoFaltantes");
            $this->display("ProductosListarTemplate.twig");
        }
    }

    public function listadoStockMinimo()
    {
        if($this->permissions())
        {
            $this->paginaCorrecta($this->ProductosModel->totalProductosStockMinimo());
            $this->addData('producto', $this->ProductosModel->listarProductosStockMinimo($this->conf->getConfiguracion()->cantPagina, $_GET['offset']));
            $this->addData('pag', $_GET['pag']);
            $this->addData('method', "listadoStockMinimo");
            $this->display("ProductosListarTemplate.twig");
        }
    }
}