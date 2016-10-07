<?php
require_once 'Controller/Controller.php';
require_once 'Controller/Validador.php';
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
        $this->dispatcher->rol = parent::getRol();
        $this->dispatcher->render("Backend/IndexTemplate.twig");
    }
    
    /* ---Compra Venta---*/
    
    
    
    /* ---Productos---*/
    
    public function productosListar(){
        $this->dispatcher->producto =$this ->productoModel->getAllProducto();
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
        validarProductos($_POST);
        if ($_POST["idProducto"] != ""){
           $this->dispatcher->producto =$this ->productoModel->actualizarProducto($_POST); 
        }else{
            $this->dispatcher->producto =$this ->productoModel->insertarProducto($_POST);
        }
        $this->productosListar();
    }
    public function productosE(){
     $this->dispatcher->producto =$this ->productoModel->deleteProducto($_POST["idProducto"]);
     $this->productosListar();
    }
    
    /*validar productos en el serverrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrr
     * public function validarProductos($var){
         if (! isset($var['nombre'])) throw new Exception('Falta escribir el telefono');
        else{
            validarNumeros($_POST['telefono']);
        }
    }*/
}
