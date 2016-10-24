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
        $this->dispatcher->render("Backend/venderListarTemplate.twig");
    }
    
    public function ventaModificar(){
        /*****************************/
        $this->validator->varSet($_POST["submitButton"], "Presione el boton de modificacion");
        $this->validator->varSet($_POST['idIngresoDetalle'], "Error: la venta deseada no ah sido encontrada");
        $this->dispatcher->venta = $this->ventaModel->getVentaById($_POST['idIngresoDetalle']);
        $this->dispatcher->render("Backend/ModificarVenta.twig");
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

    /* ---Compra ---*/ 
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
        $this->dispatcher->productos =$this ->productoModel->getAllProducto(99999,0);
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
        try{
        $this->validarProductos($_POST);
        if ($_POST["idProducto"] != ""){
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
         var_dump($var);
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
