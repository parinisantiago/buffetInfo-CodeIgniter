<?php

require_once 'Controller/Controller.php';

class ComprarController extends Controller{
    public $productoModel;
    public $compraModel;
  
    public function __construct(){
            parent::__contruct();
            $this->productoModel = new ProductosModel();
            $this->compraModel= new CompraModel(); 
    }

    public function getPermission()
    {
        Session::init();
        $rol = Session::getValue('rol');
        return (($rol == '0') || ($rol == '1'));
    }

    public function compraListar(){
        $this->paginaCorrecta($this->compraModel->totalCompras());
        $this->dispatcher->compras = $this->compraModel->getAllCompras($this->conf->getConfiguracion()->cantPagina,$_GET['offset']);
        $this->dispatcher->pag = $_GET['pag'];
        $this->dispatcher->method = "compraListar";
        $this->dispatcher->render("Backend/CompraListarTemplate.twig");
    }
    public function compraAM(){
        if (isset($_POST["idCompra"])){ //modificar una compra
            $this->dispatcher->compra =$this ->compraModel->searchIdCompra($_POST["idCompra"]);
        }//crear una compra nueva
        $this->dispatcher->proveedor =$this ->compraModel->getAllProveedor(99999,0);
        $this->dispatcher->productos =$this ->productoModel->getProductos();
        $this->dispatcher->render("Backend/CompraAMTemplate.twig");
    }
    public function compraAMPost(){
        try{
        $this->validarCompra($_POST);
        $producto = $this->productoModel->searchIdProducto($_POST['producto']);
        if ($_FILES['uploadedfile']['size'] >1){//si hay foto de la factura  !isset($_POST['fotoFactura']
            $this->subirFoto();
        }else{//pregunto si ya hay una foto
            if (!isset($_POST['fotoFactura'])){
                $_POST["fotoFactura"]=basename( $_FILES['uploadedfile']['name']);
            }
        }
        if ($_POST["idCompra"] != ""){
           $compra =  $this->compraModel->searchIdCompra($_POST['idCompra']);
           $nuevoStock = $producto->stock - $compra->cantidad + $_POST['cantidad'];
            if ($nuevoStock < 0) throw new Exception("No se puede modificar la compra porque quedaria sin stock el producto");
            $this->productoModel->actualizarCantProductos($producto->idProducto, $nuevoStock);
            $this->dispatcher->compra =$this->compraModel->actualizarCompra($_POST);
        }else{
            $this->dispatcher->compra =$this ->compraModel->insertarCompra($_POST);
            $nuevoStock = $producto -> stock + $_POST['cantidad'];
            $nuevoStock .= "";
            $this->productoModel->actualizarCantProductos($_POST['producto'], $nuevoStock);
        }
        $_GET['pag'] = 0;
        $this->dispatcher->method = "CompraListar";
        $this->compraListar();
    } catch (valException $e){
        $this->dispatcher->ventas = $_POST;
        $this->dispatcher->mensajeError = $e->getMessage();
        $this->compraAM();
}
    }
    public function compraEliminar(){
        $this->validator->varSet($_POST['idCompra'], "Error: no existe la compra");
        $this->validator->varSet($_POST['submitButton'], "Presione el boton de submit");
        $compra = $this->compraModel->searchIdCompra($_POST['idCompra']);
        $producto = $this->productoModel->searchIdProducto($compra->idProducto);
        $nuevoStock = $producto -> stock - $compra -> cantidad;
        if ($nuevoStock < 0) throw new Exception("Error: no se puede eliminar la compra, ya que se han vendido productos");
        $nuevoStock .= "";
        $this->productoModel->actualizarCantProductos($producto->idProducto, $nuevoStock);
        $this->dispatcher->compra =$this ->compraModel->eliminarCompra($compra->idCompra);
        $_GET['pag'] = 0;
        $this->dispatcher->method = "CompraListar";
        $this->compraListar();
    }
    public function subirFoto(){
        $target_path = "uploads/";
        $target_path = $target_path . basename( $_FILES['uploadedfile']['name']);
        $aux = move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path); 
        $_POST["fotoFactura"]=basename( $_FILES['uploadedfile']['name']);
        if (! $aux) {
            throw new Exception('Error: no se pude subir el archivo.');
        }
    } 
    public function validarCompra($var){
        $this->validator->varSet($var['submit'],"Presione el boton de submit");
        if (! $this->compraModel->searchIdProveedor($var['proveedor'])) throw new valException("Error: No existe el proveedor");
        if (! $this->productoModel->searchIdProducto($var['producto'])) throw new valException("Error: No existe el producto");
        $this->validator->validarNumeros($var['cantidad'],"Error: error en cantidad",5);
        $this->validator->validarNumerosPunto($var['precioUnitario'],"Error: error en precio unitario",50);
    }

    public function paginaCorrecta($total){
        if (! isset($_GET['pag'])) throw new Exception('Error:No hay una página que mostrar');
        elseif ($total->total <= $_GET['pag'] *  $this->conf->getConfiguracion()->cantPagina){  $_GET['pag'] = 0; $_GET['offset'] = 0;}
        else $_GET['offset'] = $this->conf->getConfiguracion()->cantPagina * $_GET['pag'];
        if ($_GET['offset'] < 0) $_GET['offset'] = 0;
        $_GET['offset'] .= "";
    }
}
