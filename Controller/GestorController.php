<?php
require_once 'Controller/BackendController.php';
class GestorController extends BackendController{
    
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

        $this->paginaCorrecta($this->productoModel->totalProductos());/* le paso todos los productos, si funciona para todos entonces funciona para un subconjunto */
        $this->dispatcher->producto = $this->productoModel->listarProductosFaltantes($this->conf->getConfiguracion()->cantPagina,$_GET['offset']);
        $this->dispatcher->pag = $_GET['pag'];
        $this->dispatcher->method = "listadoFaltantes";
        $this->dispatcher->render("Backend/ProductosListarTemplate.twig");

    }
    public function listadoStockMinimo(){
        $this->dispatcher->producto = $this->productoModel->listarProductosStockMinimo();
        $_GET['pag'] = 0;
        $_GET['method'] = 'listadoStockMinimo';
        $this->dispatcher->pag = $_GET['pag'];

        $this->dispatcher->method = $_GET['method'];
    }
}