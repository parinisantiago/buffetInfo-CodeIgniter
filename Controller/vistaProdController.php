<?php
require_once 'Controller/BackendController.php';
class vistaProdController extends BackendController{
    
    public function getPermission(){
        Session::init();  
        return (Session::getValue('rol') == '1');
    }

    public function setMensajeError($error){
        $this->dispatcher->mensajeError = $error;
        $this->index();
    }
    
    /*---listado productos---*/



    public function listadoFaltantes(){

        $this->paginaCorrecta($this->productoModel-> totalProductosFaltantes());
        $this->dispatcher->producto = $this->productoModel->listarProductosFaltantes($this->conf->getConfiguracion()->cantPagina,$_GET['offset']);
        $this->dispatcher->pag = $_GET['pag'];
        $this->dispatcher->method = "listadoFaltantes";
        $this->dispatcher->render("Backend/ProductosListarTemplate.twig");

    }
    public function listadoStockMinimo(){
        $this->paginaCorrecta($this->productoModel-> totalProductosStockMinimo());
        $this->dispatcher->producto = $this->productoModel->listarProductosStockMinimo($this->conf->getConfiguracion()->cantPagina,$_GET['offset']);
        $this->dispatcher->pag = $_GET['pag'];
        $this->dispatcher->method = "listadoStockMinimo";
        $this->dispatcher->render("Backend/ProductosListarTemplate.twig");
    }
}