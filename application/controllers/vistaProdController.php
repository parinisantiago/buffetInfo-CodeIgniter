<?php
require_once 'BackendController.php';
class vistaProdController extends BackendController{
    
    public function getPermission(){
        Session::init();  
        return (Session::getValue('rol') == '1');
    }

    public function setMensajeError($error){
        $this->addData('mensajeError', $error);
        $this->index();
    }
    
    /*---listado productos---*/



    public function listadoFaltantes(){

        $this->paginaCorrecta($this->ProductoModel-> totalProductosFaltantes());
        $this->addData('producto', $this->ProductoModel->listarProductosFaltantes($this->conf->getConfiguracion()->cantPagina,$_GET['offset']));
        $this->addData('pag', $_GET['pag']);
        $this->addData('method', "listadoFaltantes");
        $this->display("ProductosListarTemplate.twig");

    }
    public function listadoStockMinimo(){
        $this->paginaCorrecta($this->ProductoModel-> totalProductosStockMinimo());
        $this->addData('producto', $this->ProductoModel->listarProductosStockMinimo($this->conf->getConfiguracion()->cantPagina,$_GET['offset']);
        $this->addData('pag', $_GET['pag']);
        $this->addData('method', "listadoStockMinimo");
        $this->display("ProductosListarTemplate.twig");
    }
}