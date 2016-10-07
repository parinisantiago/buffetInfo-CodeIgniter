<?php
require_once 'Controller/BackendController.php';
class GestorController extends BackendController{
    
    public function getPermission(){
        Session::init();  
        return strcmp(Session::getValue('rol'), '1');
    }

    public function setMensajeError($error){
        $this->dispatcher->mensajeError = $error;
        $this->index();
    }
    
    /*---listado productos---*/
    
    public function listadoFaltantes(){
        $this->dispatcher->producto = $this->productoModel->listarProductosFaltantes();
        $this->dispatcher->render("backend/ProductosTemplate.twig");
    }
    public function listadoStockMinimo(){
        $this->dispatcher->producto = $this->productoModel->listarProductosStockMinimo();
        $this->dispatcher->render("backend/ProductosTemplate.twig");
    }
}