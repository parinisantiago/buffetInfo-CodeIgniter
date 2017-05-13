<?php

require_once 'Controller.php';

class ComprarController extends Controller{

    public function __construct()
    {
            parent::__construct();
            $this->load->model('ProductosModel');
            $this->load->model('CompraModel');
    }

    public function getPermission()
    {
        Session::init();
        $rol = Session::getValue('rol');
        return (($rol == '0') || ($rol == '1'));
    }

    public function compraListar()
    {
        if($this->permissions()) {
            $this->paginaCorrecta($this->CompraModel->totalCompras());
            $this->addData('compras', $this->CompraModel->getAllCompras($this->conf->getConfiguracion()->cantPagina, $_GET['offset']));
            $this->addData('pag', $_GET['pag']);
            $this->addData('method', "compraListar");
            $this->display("CompraListarTemplate.twig");
        }
    }
    public function compraAM(){
        if($this->permissions())
        {
            if (isset($_POST["idCompra"]))
            { //modificar una compra
                $this->addData('compra', $this->CompraModel->searchIdCompra($_POST["idCompra"]));
            }
            //crear una compra nueva
            $this->addData('proveedor', $this->CompraModel->getAllProveedor(99999, 0));
            $this->addData('productos', $this->ProductosModel->getProductos());
            $this->display("CompraAMTemplate.twig");
        }
    }
    public function compraAMPost()
    {
        if($this->permissions())
        {
            try
            {
                $this->validarCompra($_POST);
                $producto = $this->ProductosModel->searchIdProducto($_POST['producto']);
                if ($_FILES['uploadedfile']['size'] > 1)
                {//si hay foto de la factura  !isset($_POST['fotoFactura']
                    $this->subirFoto();
                }
                else
                {//pregunto si ya hay una foto
                    if (!isset($_POST['fotoFactura']))
                    {
                        $_POST["fotoFactura"] = basename($_FILES['uploadedfile']['name']);
                    }
                }
                if ($_POST["idCompra"] != "")
                {
                    $compra = $this->CompraModel->searchIdCompra($_POST['idCompra']);
                    $nuevoStock = $producto->stock - $compra->cantidad + $_POST['cantidad'];
                    if ($nuevoStock < 0) throw new Exception("No se puede modificar la compra porque quedaria sin stock el producto");
                    $this->ProductosModel->actualizarCantProductos($producto->idProducto, $nuevoStock);
                    $this->addData('compra', $this->CompraModel->actualizarCompra($_POST));
                }
                else
                {
                    $this->addData('compra', $this->CompraModel->insertarCompra($_POST));
                    $nuevoStock = $producto->stock + $_POST['cantidad'];
                    $nuevoStock .= "";
                    $this->ProductosModel->actualizarCantProductos($_POST['producto'], $nuevoStock);
                }
                $_GET['pag'] = 0;
                $this->addData('method', "CompraListar");
                $this->compraListar();
            }
            catch (Exception $e)
            {
                $this->addData('ventas', $_POST);
                $this->addData('mensajeError', $e->getMessage());
                $this->compraAM();
            }
        }
    }
    public function compraEliminar()
    {
        if($this->permissions())
        {
            $this->validator->varSet($_POST['idCompra'], "Error: no existe la compra");
            $this->validator->varSet($_POST['submitButton'], "Presione el boton de submit");
            $compra = $this->CompraModel->searchIdCompra($_POST['idCompra']);
            $producto = $this->ProductosModel->searchIdProducto($compra->idProducto);
            $nuevoStock = $producto->stock - $compra->cantidad;
            if ($nuevoStock < 0) throw new Exception("Error: no se puede eliminar la compra, ya que se han vendido productos");
            $nuevoStock .= "";
            $this->ProductosModel->actualizarCantProductos($producto->idProducto, $nuevoStock);
            $this->addData('compra', $this->CompraModel->eliminarCompra($compra->idCompra));
            $_GET['pag'] = 0;
            $this->addData('method', "CompraListar");
            $this->compraListar();
        }
    }
    public function subirFoto()
    {
        $target_path = "public/uploads/";
        $target_path = $target_path . basename( $_FILES['uploadedfile']['name']);
        $aux = move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path);
        $_POST["fotoFactura"]=basename( $_FILES['uploadedfile']['name']);
        if (! $aux)
        {
            throw new Exception('Error: no se pude subir el archivo.');
        }
    } 
    public function validarCompra($var)
    {
        $this->validator->varSet($var['submit'],"Presione el boton de submit");
        if (! $this->CompraModel->searchIdProveedor($var['proveedor'])) throw new Exception("Error: No existe el proveedor");
        if (! $this->ProductosModel->searchIdProducto($var['producto'])) throw new Exception("Error: No existe el producto");
        $this->validator->validarNumeros($var['cantidad'],"Error: error en cantidad",5);
        $this->validator->validarNumerosPunto($var['precioUnitario'],"Error: error en precio unitario",50);
    }

    public function paginaCorrecta($total)
    {
        if (! isset($_GET['pag'])) throw new Exception('Error:No hay una página que mostrar');
        elseif ($total->total <= $_GET['pag'] *  $this->conf->getConfiguracion()->cantPagina){  $_GET['pag'] = 0; $_GET['offset'] = 0;}
        else $_GET['offset'] = $this->conf->getConfiguracion()->cantPagina * $_GET['pag'];
        if ($_GET['offset'] < 0) $_GET['offset'] = 0;
        $_GET['offset'] .= "";
    }
}
