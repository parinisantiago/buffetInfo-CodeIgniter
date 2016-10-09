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
    public $ventaModel;
    public $compraModel;

    public function __construct(){
            parent::__contruct();
            $this->model = new MainUserModel();
            $this->rolModel = new RolModel();
            $this->productoModel = new ProductosModel();
            $this->categoriaModel = new CategoriaModel();
            $this->ventaModel = new VentaModel();
            $this->compraModel= new CompraModel(); 
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
        $this->dispatcher->render("Backend/VenderTemplate.twig");
    }
    public function venta(){
        /***** actualizar ganancias***************************/
        var_dump($_POST);
        $this->dispatcher->producto =$this ->productoModel->insertarProducto($_POST);
        
        $this->paginaCorrecta($this->productoModel->totalProductos());
        $this->dispatcher->producto = $this->productoModel->getAllProducto($this->conf->getConfiguracion()->cantPagina,$_GET['offset']);
        $this->dispatcher->pag = $_GET['pag'];
        
        $this->dispatcher->render("Backend/VenderTemplate.twig"); 
        die();
    }

    public function venderListar(){
        $this->paginaCorrecta($this->ventaModel->totalVenta());
        $this->dispatcher->ventas = $this->ventaModel->getAllVenta($this->conf->getConfiguracion()->cantPagina,$_GET['offset']);
        $this->dispatcher->pag = $_GET['pag'];
        $this->dispatcher->render("Backend/venderListarTemplate.twig");
    }
    public function ventaModificar(){
        /*****************************/
    }
    public function ventaEliminar(){
        $this->validator->varSet($_POST['submitButton'], "Apreta el botón de eliminar macho");
        $this->validator->varSet($_POST['idIngresoDetalle'], "Faltan datos para poder eliminar la venta");
        
        $this->ventaModel->eliminarVenta($_POST['idIngresoDetalle']);
        
        
        
        $_GET['pag'] = 0;

        $this->venderListar();
    }
    /* ---Compra ---*/ 

    public function compraListar(){
/**************AIIIIIIIIIIIIUUUUUUUUUUUUUDAAAAAAAAAAAAAAAA 
 * no ecuentro por que no esta listando las compras, parece todo correcto
 * provee el sql y funca, tengo configurado 5 elemntos por pagina
 * no se que es  $this->dispatcher->method = "compraListar";
 * pero lo copie de vos
 */
        $this->paginaCorrecta($this->compraModel->totalCompras());
        $this->dispatcher->compra = $this->compraModel->getAllCompras($this->conf->getConfiguracion()->cantPagina,$_GET['offset']);
        $this->dispatcher->pag = $_GET['pag'];
        $this->dispatcher->method = "compraListar";
        $this->dispatcher->render("Backend/CompraListarTemplate.twig");
    }
    public function compraAM(){
        
    }
     public function compraModificar(){
        /*****************************/
    }
    public function compraAMPost(){
        
    }
    public function compraEliminar(){
        /*****************************/
    }
    /* ---Productos---*/
    
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
        /*ver botones de venta, solo son del admin*/
        $this->validarProductos($_POST);
        if ($_POST["idProducto"] != ""){
           $this->dispatcher->producto =$this ->productoModel->actualizarProducto($_POST); 
        }else{
            $this->dispatcher->producto =$this ->productoModel->insertarProducto($_POST);
        }
        $_GET['pag'] = 0;
        $this->dispatcher->method = "ProductosListar";
        $this->productosListar();
    }
    public function productosEliminar(){
     $this->dispatcher->producto =$this ->productoModel->deleteProducto($_POST["idProducto"]);
        $_GET['pag'] = 0;
        $this->dispatcher->method = "ProductosListar";
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
