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
        $this->validator->varSet($_POST['submitButton'],"apreta el boton de envio");
        $this->validator->validarNumeros($_POST['idProducto'],"no hay un id valido", 3);
        $this->validator->validarNumeros($_POST['cant'],"no hay una cantidad valida", 3);
        if ($_POST['cant'] < 1) throw new Exception("Cantidad inferior a 1");
        if (! $this->productoModel->searchIdProducto($_POST['idProducto'])) throw new Exception("idProducto no valido");
        $this->productoModel->actualizarCantProductos($_POST['idProducto'], $_POST['stock'] - $_POST['cant']);
        $this->ventaModel->insertarVenta($_POST);

        $this->vender();
    } 

    public function venderListar(){
        $this->paginaCorrecta($this->ventaModel->totalVenta());
        $this->dispatcher->ventas = $this->ventaModel->getAllVenta($this->conf->getConfiguracion()->cantPagina,$_GET['offset']);
        $this->dispatcher->pag = $_GET['pag'];
        $this->dispatcher->render("Backend/venderListarTemplate.twig");
    }
    public function ventaModificar(){
        /*****************************/
        $this->validator->varSet($_POST["submitButton"], "apreta el boton de modificacion");
        $this->validator->varSet($_POST['idIngresoDetalle'], "no se puede modificar una venta sin id");
        $this->dispatcher->venta = $this->ventaModel->getVentaById($_POST['idIngresoDetalle']);
        $this->dispatcher->render("Backend/ModificarVenta.twig");
    }

    public function modVenta(){
        $this->validator->varSet($_POST["submitButton"], "apreta el boton de modificacion");
        $this->validator->validarNumeros($_POST['idIngresoDetalle'], "no se puede modificar una venta sin id",3);
        $this->validator->validarNumeros($_POST["stockViejo"], "No modifiques los inputs gentes malas", 2);
        $this->validator->validarNumeros($_POST["stock"], "No modifiques los inputs gentes malas", 2);
        $this->validator->validarNumeros($_POST["idProducto"], "No modifiques los inputs gentes malas", 2);
        $stock = $this->productoModel->searchIdProducto($_POST["idProducto"])->stock + $_POST["stockViejo"] - $_POST["stock"];
        if ($stock <= 0) throw new Exception("No podes vender tantos productos");
        $this->productoModel->actualizarCantProductos($_POST['idProducto'], $stock);
        $this->ventaModel->actualizarVenta($_POST['stock'], $_POST['idIngresoDetalle']);

        $_GET['pag'] = 0;
        $this -> venderListar();

    }

    public function ventaEliminar(){
        $this->validator->varSet($_POST['submitButton'], "Apreta el botón de eliminar macho");
        $this->validator->varSet($_POST['idIngresoDetalle'], "Faltan datos para poder eliminar la venta");

        $valor = $this->productoModel->searchIdProducto($_POST['idProducto'])->stock + $_POST['cantidad'];
        $valor .= '';
        $this->productoModel->actualizarCantProductos($_POST['idProducto'],$valor);

        $this->ventaModel->eliminarVenta($_POST['idIngresoDetalle']);
  
        $_GET['pag'] = 0;

        $this->venderListar();
    }
    /* ---Compra ---*/ 

    public function compraListar(){
        $this->paginaCorrecta($this->compraModel->totalCompras());
        $this->dispatcher->compras = $this->compraModel->getAllCompras($this->conf->getConfiguracion()->cantPagina,$_GET['offset']);
        $this->dispatcher->pag = $_GET['pag'];
        $this->dispatcher->method = "compraListar";
        $this->dispatcher->render("Backend/CompraListarTemplate.twig");
    }
    public function compraAM(){
        if (isset($_POST["idCompra"])){
            $this->dispatcher->compra =$this ->compraModel->searchIdCompra($_POST["idCompra"]);
        }
        $this->dispatcher->proveedor =$this ->compraModel->getAllProveedor(99999,0);
        $this->dispatcher->productos =$this ->productoModel->getAllProducto(99999,0);
        $this->dispatcher->render("Backend/CompraAMTemplate.twig");
    }
    public function compraAMPost(){
        $this->validarCompra($_POST);
        if ($_POST["idCompra"] != ""){
           $this->dispatcher->compra =$this->compraModel->actualizarCompra($_POST);

        }else{
            $this->dispatcher->compra =$this ->compraModel->insertarCompra($_POST);
            $producto = $this->productoModel->searchIdProducto($_POST['producto']);
            $nuevoStock = $producto -> stock + $_POST['cantidad'];
            $nuevoStock .= "";
            $this->productoModel->actualizarCantProductos($_POST['producto'], $nuevoStock);
        }
        $_GET['pag'] = 0;
        $this->dispatcher->method = "CompraListar";
        $this->compraListar();
    }
    
    public function compraEliminar(){

        $this->validator->varSet($_POST['idCompra'], "no hay un id de compra");
        $this->validator->varSet($_POST['submitButton'], "apreta el boton de submit");

        $compra = $this->compraModel->searchIdCompra($_POST['idCompra']);

        $producto = $this->productoModel->searchIdProducto($compra->idProducto);

        $nuevoStock = $producto -> stock - $compra -> cantidad;

        if ($nuevoStock < 0) throw new Exception("Ya se han vendido productos como para cancelar la compra");
        $nuevoStock .= "";

        $this->productoModel->actualizarCantProductos($producto->idProducto, $nuevoStock);

        $this->dispatcher->compra =$this ->compraModel->eliminarCompra($compra->idCompra);


        $_GET['pag'] = 0;
        $this->dispatcher->method = "CompraListar";
        $this->compraListar();
    }
    public function validarCompra($var){
        $this->validator->varSet($var['submit'],"Apreta el boton de submit");
        if (! $this->compraModel->searchIdCompra($var['proveedor'])) throw new Exception("No existe el proveedor");
        if (! $this->productoModel->searchIdProducto($var['producto'])) throw new Exception("No existe el producto");
        $this->validator->validarNumeros($var['cantidad'],"error en cantidad",5);
        $this->validator->validarNumerosPunto($var['precioUnitario'],"error en precio unitario",50);
        if (isset($var['fecha'])){
            $this->validator->validarFecha($var['fecha'],"error en fecha");
        }
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

        elseif ($total->total <= $_GET['pag'] *  $this->conf->getConfiguracion()->cantPagina){  $_GET['pag'] = 0; $_GET['offset'] = 0;}
        else $_GET['offset'] = $this->conf->getConfiguracion()->cantPagina * $_GET['pag'];
        if ($_GET['offset'] < 0) $_GET['offset'] = 0;
        $_GET['offset'] .= "";

    }

}
