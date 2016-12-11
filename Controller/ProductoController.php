<?php

/**
 * Created by PhpStorm.
 * User: piturro
 * Date: 31/10/16
 * Time: 09:46
 */
class ProductoController extends Controller
{
    public $model;
    public $rolModel;
    public $productoModel;
    public $categoriaModel;

    public function __construct(){
        parent::__contruct();
        $this->model = new MainUserModel();
        $this->rolModel = new RolModel();
        $this->productoModel = new ProductosModel();
        $this->categoriaModel = new CategoriaModel();
    }

    public function getPermission()
    {
        Session::init();
        $rol = Session::getValue('rol');
        return (($rol == '0') || ($rol == '1'));
    }

    public function productosListar(){
        $this->paginaCorrecta($this->productoModel->totalProductos());
        $this->dispatcher->producto = $this->productoModel->getAllProducto($this->conf->getConfiguracion()->cantPagina,$_GET['offset']);
        $this->dispatcher->pag = $_GET['pag'];
        $this->dispatcher->method = "ProductosListar";
        $this->dispatcher->render("Backend/ProductosListarTemplate.twig");
    }
    public function productosAM(){
        if (isset($_POST["idProducto"])){
            $this->dispatcher->producto =$this ->productoModel->searchIdProducto($_POST["idProducto"]);
        }
        $this->dispatcher->categoria =$this ->categoriaModel->getAllCategorias();
        $this->dispatcher->render("Backend/ProductosAMTemplate.twig");
    }
    public function productosAMPost(){
        try{
            $this->validarProductos($_POST);
            if (isset($_POST['idProducto'])){
                $this->dispatcher->producto =$this ->productoModel->actualizarProducto($_POST);
            }else{
                $this->dispatcher->producto =$this ->productoModel->insertarProducto($_POST);
            }
            $_GET['pag'] = 0;
            $this->dispatcher->method = "ProductosListar";
            $this->productosListar();
        } catch (valException $e){
            $this->dispatcher->producto = $_POST;
            $this->dispatcher->mensajeError = $e->getMessage();
            $this->productosAM();
        }
    }
    public function productosEliminar(){
        $this->dispatcher->producto =$this ->productoModel->deleteProducto($_POST["idProducto"]);
        $_GET['pag'] = 0;
        $this->dispatcher->method = "ProductosListar";
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
        if (! $this->categoriaModel->getCategoriaById($var['idCategoria'])) throw new valException("Error:No existe la categoria");
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