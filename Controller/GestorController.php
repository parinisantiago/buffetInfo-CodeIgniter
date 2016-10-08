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
        $this->dispatcher->producto = $this->productoModel->listarProductosFaltantes();
        $_GET['pag'] = 0;
        $_GET['method'] = 'listadoFaltantes';
        $this->dispatcher->method = $_GET['method'];
        $this->dispatcher->pag = $_GET['pag'];

        $this->listarFiltrado();
    }
    public function listadoStockMinimo(){
        $this->dispatcher->producto = $this->productoModel->listarProductosStockMinimo();
        $_GET['pag'] = 0;
        $_GET['method'] = 'listadoStockMinimo';
        $this->dispatcher->pag = $_GET['pag'];

        $this->dispatcher->method = $_GET['method'];
        $this->listarFiltrado();
    }
}