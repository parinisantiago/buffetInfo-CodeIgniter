<?php
class VenderController extends Controller{
    public $productoModel;
    public $ventaModel;

    public function __construct(){
        parent::__contruct();
        $this->ventaModel = new VentaModel();
        $this->productoModel = new ProductosModel();
    }

    public function getPermission()
    {
        Session::init();
        $rol = Session::getValue('rol');
        return (($rol == '0') || ($rol == '1'));
    }

    public function vender(){
        $this->paginaCorrecta($this->productoModel->totalProductos());
        $this->dispatcher->producto = $this->productoModel->getAllProducto($this->conf->getConfiguracion()->cantPagina,$_GET['offset']);
        $this->dispatcher->pag = $_GET['pag'];
        $this->dispatcher->render("Backend/VenderTemplate.twig");
    }
    public function venta(){
        $this->validator->varSet($_POST['submitButton'],"Presione el boton de envio");
        $this->validator->validarNumeros($_POST['idProducto'],"Error: no se ah encontrado la venta deseada", 3);
        $this->validator->validarNumeros($_POST['cant'],"Error: este campo solo acepta numeros", 3);
        if ($_POST['cant'] < 1) throw new Exception("Error:Cantidad inferior a 1");
        if (! $this->productoModel->searchIdProducto($_POST['idProducto'])) throw new Exception("Error:no se ah encontrado el producto deseado");
        $this->productoModel->actualizarCantProductos($_POST['idProducto'], $_POST['stock'] - $_POST['cant']);
        $this->ventaModel->insertarVenta($_POST);
        $this->vender();
    } 
    public function venderListar(){
        $this->paginaCorrecta($this->ventaModel->totalVenta());
        $this->dispatcher->ventas = $this->ventaModel->getAllVenta($this->conf->getConfiguracion()->cantPagina,$_GET['offset']);
        $this->dispatcher->totales = $this->ventaModel->getAlltotales();
        $this->dispatcher->pag = $_GET['pag'];
        $this->dispatcher->render("Backend/VenderListarTemplate.twig");
    }
    public function ventaModificar(){
        $this->validator->varSet($_POST["submitButton"], "Presione el boton de modificacion");
        $this->validator->varSet($_POST['idIngresoDetalle'], "Error: la venta deseada no ah sido encontrada");
        $this->dispatcher->venta = $this->ventaModel->getVentaById($_POST['idIngresoDetalle']);
        $this->dispatcher->render("Backend/VentaAMTemplate.twig");
    }
    public function modVenta(){
        try{
            $this->validator->varSet($_POST["submitButton"], "Presione el boton de modificar");
            $this->validator->validarNumeros($_POST['idIngresoDetalle'], "Error: no se a encotrado la venta deseada",3);
            $this->validator->validarNumeros($_POST["stockViejo"], "Error: este campo solo acepta numeros", 2);
            $this->validator->validarNumeros($_POST["stock"], "Error: este campo solo acepta numeros", 2);
            $this->validator->validarNumeros($_POST["idProducto"], "Error: no se a encontrado el producto deseado", 2);
            $stock = $this->productoModel->searchIdProducto($_POST["idProducto"])->stock + $_POST["stockViejo"] - $_POST["stock"];
            if ($stock <= 0) throw new Exception("Error: la cantidad vendida supera el stock actual de productos");
            $this->productoModel->actualizarCantProductos($_POST['idProducto'], $stock);
            $this->ventaModel->actualizarVenta($_POST['stock'], $_POST['idIngresoDetalle']);
            $_GET['pag'] = 0;
            $this -> venderListar();
        } catch (valException $e){
            $this->dispatcher->ventas = $_POST;
            $this->dispatcher->mensajeError = $e->getMessage();
            $this->venderListar();
        }
    }
    public function ventaEliminar(){
        $this->validator->varSet($_POST['submitButton'], "Presione el botón de eliminar");
        $this->validator->varSet($_POST['idIngresoDetalle'], "Error: la venta deseada no existe.");
        $valor = $this->productoModel->searchIdProducto($_POST['idProducto'])->stock + $_POST['cantidad'];
        $valor .= '';
        $this->productoModel->actualizarCantProductos($_POST['idProducto'],$valor);
        $this->ventaModel->eliminarVenta($_POST['idIngresoDetalle']);
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