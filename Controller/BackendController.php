<?php
require_once 'Controller/Controller.php';
/*
 * Esta clase encapsula el comportamiento comun de los 2 tipos de usuario que 
 * pueden llegar al backend Admin y Gestion
 */
class BackendController extends Controller{
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
    
    public function index(){
        $this->dispatcher->render("Backend/IndexTemplate.twig");
    }
    
    /* ---Venta---*/
    public function vender(){
        /*muestra productos a vender
    /*****************ver el template que el boton tiene caca*/
        $this->paginaCorrecta($this->productoModel->totalProductos());
        $this->dispatcher->producto = $this->productoModel->getAllProducto($this->conf->getConfiguracion()->cantPagina,$_GET['offset']);
        $this->dispatcher->pag = $_GET['pag'];
        var_dump($_GET['pag']);
        $this->dispatcher->render("Backend/VenderTemplate.twig"); 
    }
    public function venta(){
        /***** actualizar ganancias***************************/
        var_dump($_POST);
        die();
        $this->dispatcher->producto =$this ->productoModel->insertarProducto($_POST);
        $this->paginaCorrecta($this->productoModel->totalProductos());
        $this->dispatcher->producto = $this->productoModel->getAllProducto($this->conf->getConfiguracion()->cantPagina,$_GET['offset']);
        $this->dispatcher->pag = $_GET['pag'];
        
        $this->dispatcher->render("Backend/VenderTemplate.twig"); 
        var_dump($var);
        die();
    }

    public function ventaListar(){
        /*botones de pasar pagina=caca*/
        /******muestra todos los productos vendidos***********************/
    }
    public function ventaModificar(){
        /*****************************/
    }
    public function ventaEliminar(){
        /*****************************/
    
    /* ---Compra ---*/ }

    public function compraListar(){
        /*botones de pasar pagina=caca*/
        /******muestra todos los productos comprados***********************/
    }
    
    
    /* ---Productos---*/
    
    public function productosListar(){
        $this->paginaCorrecta($this->productoModel->totalProductos());
        $this->dispatcher->producto = $this->productoModel->getAllProducto($this->conf->getConfiguracion()->cantPagina,$_GET['offset']);
        $this->dispatcher->pag = $_GET['pag'];
        $this->dispatcher->render("Backend/ProductosListarTemplate.twig");
    }


    public function listarFiltrado(){
        $this->paginaCorrecta($this->productoModel->totalProductos());
        $this->dispatcher->pag = $_GET['pag'];
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
        /*ver botones de venta, solo son del admin*/
        $this->validarProductos($_POST);
        if ($_POST["idProducto"] != ""){
           $this->dispatcher->producto =$this ->productoModel->actualizarProducto($_POST); 
        }else{
            $this->dispatcher->producto =$this ->productoModel->insertarProducto($_POST);
        }
        $_GET['pag'] = 0;
        $this->productosListar();
    }
    public function productosE(){
     $this->dispatcher->producto =$this ->productoModel->deleteProducto($_POST["idProducto"]);
     $this->productosListar();
    }



    /*validar productos en el serverrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrr */
      public function validarProductos($var){

          $this->validator->varSet($var['submit'],"Apreta el boton de submit");
          $this->validator->validarStringEspeciales($var['nombre'],"erroe en nombre",25);
          $this->validator->validarStringEspeciales($var['marca'],"error en marca",25);
          $this->validator->validarNumeros($var['stock'],"error en stock",3);
          $this->validator->validarNumeros($var['stockMinimo'],"error en stock minimo",3);
          $this->validator->validarNumeros($var['categoria'],"error en categoria",3);
          $this->validator->validarString($var['proveedor'],"error en proveedor",25);
          $this->validator->validarNumerosPunto($var['precioVentaUnitario'],"error en precio de venta unitario",5);
          if (! $this->categoriaModel->getCategoriaById($var['categoria'])) throw new Exception("No existe la categoria");

        }


    /*--- paginacion ---*/


    public function paginaCorrecta($total){

        if (! isset($_GET['pag'])) throw new Exception('No hay una página que mostrar');

        elseif ($total->total < $_GET['pag'] *  $this->conf->getConfiguracion()->cantPagina + $this->conf->getConfiguracion()->cantPagina){  $_GET['pag'] = 0; $_GET['offset'] = 0;}
        else $_GET['offset'] = $this->conf->getConfiguracion()->cantPagina * $_GET['pag'];
        if ($_GET['offset'] < 0) $_GET['offset'] = 0;
        $_GET['offset'] .= "";

    }

}
