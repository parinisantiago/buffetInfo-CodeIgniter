<?php

include_once('Controller.php');

class VenderController extends Controller{

    public function __construct(){
        parent::__construct();
        $this->load->model('VentaModel');
        $this->load->model('ProductosModel');
    }

    public function getPermission()
    {
        Session::init();
        $rol = Session::getValue('rol');
        return (($rol == '0') || ($rol == '1'));
    }

    public function vender(){
        $this->paginaCorrecta($this->ProductosModel->totalProductos());
        $this->addData('producto', $this->ProductosModel->getAllProducto($this->conf->getConfiguracion()->cantPagina,$_GET['offset']));
        $this->addData('pag', $_GET['pag']);
        $this->display("BackendVenderTemplate.twig");
    }
    public function venta(){
        $this->validator->varSet($_POST['submitButton'],"Presione el boton de envio");
        $this->validator->validarNumeros($_POST['idProducto'],"Error: no se ah encontrado la venta deseada", 3);
        $this->validator->validarNumeros($_POST['cant'],"Error: este campo solo acepta numeros", 3);
        if ($_POST['cant'] < 1) throw new Exception("Error:Cantidad inferior a 1");
        if (! $this->ProductoModel->searchIdProducto($_POST['idProducto'])) throw new Exception("Error:no se ah encontrado el producto deseado");
        $this->ProductoModel->actualizarCantProductos($_POST['idProducto'], $_POST['stock'] - $_POST['cant']);
        $this->VentaModel->insertarVenta($_POST);
        $this->vender();
    } 
    public function venderListar(){
        $this->paginaCorrecta($this->VentaModel->totalVenta());
        $this->addData('ventas', $this->VentaModel->getAllVenta($this->conf->getConfiguracion()->cantPagina,$_GET['offset']));
        $this->addData('totales', $this->VentaModel->getAlltotales());
        $this->addData('pag', $_GET['pag']);
        $this->display("venderListarTemplate.twig");
    }
    public function ventaModificar(){
        $this->validator->varSet($_POST["submitButton"], "Presione el boton de modificacion");
        $this->validator->varSet($_POST['idIngresoDetalle'], "Error: la venta deseada no ah sido encontrada");
        $this->addData('venta', $this->VentaModel->getVentaById($_POST['idIngresoDetalle']));
        $this->display("VentaAMTemplate.twig");
    }
    public function modVenta(){
        try{
            $this->validator->varSet($_POST["submitButton"], "Presione el boton de modificar");
            $this->validator->validarNumeros($_POST['idIngresoDetalle'], "Error: no se a encotrado la venta deseada",3);
            $this->validator->validarNumeros($_POST["stockViejo"], "Error: este campo solo acepta numeros", 2);
            $this->validator->validarNumeros($_POST["stock"], "Error: este campo solo acepta numeros", 2);
            $this->validator->validarNumeros($_POST["idProducto"], "Error: no se a encontrado el producto deseado", 2);
            $stock = $this->ProductoModel->searchIdProducto($_POST["idProducto"])->stock + $_POST["stockViejo"] - $_POST["stock"];
            if ($stock <= 0) throw new Exception("Error: la cantidad vendida supera el stock actual de productos");
            $this->ProductoModel->actualizarCantProductos($_POST['idProducto'], $stock);
            $this->VentaModel->actualizarVenta($_POST['stock'], $_POST['idIngresoDetalle']);
            $_GET['pag'] = 0;
            $this -> venderListar();
        } catch (Exception $e){
            $this->addData('ventas',$_POST);
            $this->addData('mensajeError', $e->getMessage());
            $this->venderListar();
        }
    }
    public function ventaEliminar(){
        $this->validator->varSet($_POST['submitButton'], "Presione el botón de eliminar");
        $this->validator->varSet($_POST['idIngresoDetalle'], "Error: la venta deseada no existe.");
        $valor = $this->ProductoModel->searchIdProducto($_POST['idProducto'])->stock + $_POST['cantidad'];
        $valor .= '';
        $this->ProductoModel->actualizarCantProductos($_POST['idProducto'],$valor);
        $this->VentaModel->eliminarVenta($_POST['idIngresoDetalle']);
        $_GET['pag'] = 0;
        $this->venderListar();
    }

    public function paginaCorrecta($total){
        if (! isset($_GET['pag'])) throw new Exception('Error:No hay una página que mostrar');
        elseif ($total->total <= $_GET['pag'] *  $this->conf->getConfiguracion()->cantPagina){  $_GET['pag'] = 0; $_GET['offset'] = 0;}
        else $_GET['offset'] = $this->conf->getConfiguracion()->cantPagina * $_GET['pag'];
        if ($_GET['offset'] < 0) $_GET['offset'] = 0;
        $_GET['offset'] .= "";
    }
}