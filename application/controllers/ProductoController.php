<?php

include_once('Controller.php');
include_once('MainController.php');
include_once(dirname(__DIR__).'/models/ProductosModel.php');
include_once(dirname(__DIR__).'/models/CategoriaModel.php');
class ProductoController extends Controller
{

    public function __construct(){
        parent::__construct();
        $this->load->model('ProductosModel');
        $this->load->model('CategoriaModel');
    }

    public function getPermission()
    {
        Session::init();
        $rol = Session::getValue('rol');
        return (($rol == '0') || ($rol == '1'));
    }

    public function productosListar(){
        $this->paginaCorrecta($this->ProductoModel->totalProductos());
        $this->addData('producto', $this->ProductoModel->getAllProducto($this->conf->getConfiguracion()->cantPagina,$_GET['offset']));
        $this->addData('pag', $_GET['pag']);
        $this->addData('method', "ProductosListar");
        $this->display("ProductosListarTemplate.twig");
    }
    public function productosAM(){
        if (isset($_POST["idProducto"])){
            $this->addData('producto', $this->ProductoModel->searchIdProducto($_POST["idProducto"]));
        }
        $this->addData('categoria',$this ->CategoriaModel->getAllCategorias());
        $this->display("ProductosAMTemplate.twig");
    }
    public function productosAMPost(){
        try{
            $this->validarProductos($_POST);
            if (isset($_POST['idProducto'])){
                $this->addData('producto',$this ->ProductoModel->actualizarProducto($_POST));
            }else{
                $this->addData('producto',$this ->ProductoModel->insertarProducto($_POST));
            }
            $_GET['pag'] = 0;
            $this->addData('method', "ProductosListar");
            $this->productosListar();
        } catch (Exception $e){
            $this->addData('producto',$_POST);
            $this->addData('mensajeError', $e->getMessage());
            $this->productosAM();
        }
    }

    public function productosEliminar(){
        $this->addData('producto', $this ->ProductoModel->deleteProducto($_POST["idProducto"]));
        $_GET['pag'] = 0;
        $this->addData('method', "ProductosListar");
        $this->productosListar();
    }

    public function validarProductos($var){
        $this->validator->varSet($var['submit'],"Presione el boton de submit");
        $this->validator->validarStringEspeciales($var['nombre'],"Error: el campo 'Nombre' solo admite letras.",25);
        $this->validator->validarStringEspeciales($var['marca'],"Error: el campo 'Marca' solo admite letras.",25);
        $this->validator->validarNumeros($var['stock'],"Error: el campo 'Stock' solo admite numeros.",3);
        $this->validator->validarNumeros($var['stockMinimo'],"Error: el 'Stock Minimo' nombre solo admite letras.",3);
        $this->validator->validarNumeros($var['idCategoria'],"No existe la categoria.",3);
        $this->validator->validarNumerosPunto($var['precioVentaUnitario'],"Error: solo se admite el formato xxx.xx",5);
        if (! $this->CategoriaModel->getCategoriaById($var['idCategoria'])) throw new valException("Error:No existe la categoria");
    }


    /*--- paginacion ---*/
    public function paginaCorrecta($total){
        if (! isset($_GET['pag'])) throw new Exception('Error:No hay una página que mostrar');
        elseif ($total->total <= $_GET['pag'] *  $this->conf->getConfiguracion()->cantPagina){  $_GET['pag'] = 0; $_GET['offset'] = 0;}
        else $_GET['offset'] = $this->conf->getConfiguracion()->cantPagina * $_GET['pag'];
        if ($_GET['offset'] < 0) $_GET['offset'] = 0;
        $_GET['offset'] .= "";
    }


}