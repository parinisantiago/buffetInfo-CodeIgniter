<?php

include_once ("Controller.php");
class PedidosController extends Controller
{
    public function getPermission()
    {
        Session::init();
        return ((Session::getValue('rol') == '2' ));
    }

    function __construct()
    {
        parent::__construct();
        $this->load->model('PedidosModel');
        $this->load->model('MenuModel');
        $this->load->model('VentaModel');
        $this->load->model('ProductosModel');
    }

    //le pasa el menu del dia a la vista, si no hay lo avisa.
    function hacerPedido()
    {
        if($this->permissions()) {
            $menuHoy = $this->MenuModel->getMenuByDia2(99, 0, date('Y-m-d'));
            if ($menuHoy)
            {
                $this->token();
                $this->addData('minimo', $this->MenuModel->getMinimoMenu(date('Y-m-d')));
                $this->addData('idMenu', $menuHoy[0]->idMenu);
                $this->addData('menu', $menuHoy);
                $this->display("RegistroPedidoTemplate.twig");
            }
            else
            {
                $this->addData('mensajeError', "No hay un menu para el dia de hoy, por lo que no se pueden hacer pedidos");
                $this->token();
                $this->display("IndexTemplate.twig");
            }
        }
    }

    function validarPedido()
    {
        //bueno, vamos con las validaciones YAY :D
        try
        {
            //primero valido que me hayan pasado todas las variables

            if (! isset($_POST['tokenScrf'])) throw new Exception("no hay un token de validación");
            if (! $this->tokenIsValid($_POST['tokenScrf'])) throw new Exception("el token no es valido");
            $this->validator->varSet($_POST['idMenu'], "sin menu, no hay validacion");
            $this->validator->varSet($_POST['submit'], "algo raro hiciste, keep trying");
            $this->validator->validarNumeros($_POST['numPedidos'], "Error en la cantidad", 2);

            //ahora que se que tengo todo me traigo el menu.

            $idMenu = $_POST['idMenu'];
            $menuHoy = $this->MenuModel->getMenuByDia2(99,0,date('Y-m-d'));

            //si el menu de hoy no es como el de id, me estuvieron tocando las variables

            if ($menuHoy[0] -> idMenu != $idMenu ) throw new Exception("Algo raro hiciste, keep trying");

            //ahora valido que haya suficientes productos para la cantidad de menus que me pidieron

            $cantidad = $_POST['numPedidos'];

            foreach ($menuHoy as $producto)
            {
                if ($producto->stock <  $cantidad) throw new Exception("No hay suficiente cantidad de $producto->nombre para completar $cantidad pedidos");
            }

            //Primero hay que agregar el pedido y recuperar su id.
            //hay que agregar un pedido detalle.

            $pedidoId = $this->PedidosModel->insertarPedido($_SESSION['idUsuario']);

            //tengo que agregar los productos al detalle y despues descontar la cantidad en productos
            //para que despues en el cancelar solo modificar los productos
            //en el aceptar se hace lo que esta comentado en el foreach

            foreach ($menuHoy as $producto)
            {
                $this->PedidosModel->insertPedidoDetalle($pedidoId, $producto->idProducto, $cantidad);
                //actualizo el producto
                $this->ProductosModel->actualizarCantProductos($producto->idProducto, $producto->stock - $cantidad);

                /*
                    $venta['idProducto'] = $producto->idProducto;
                    $venta['precioVentaUnitario'] = $producto->precioVentaUnitario;
                    $venta['cant'] = $cantidad;
                    echo ($this->venta->insertarVentaId($venta));
                */
            }

            $this->addData('pedidos', $this->PedidosModel->pedidosUsuarios($_SESSION['idUsuario'], $this->conf->getConfiguracion()->cantPagina, "0"));

            $this->addData('pag', 0);
            $this->token();

            $this->display("PedidosListarTemplate.twig");
        }
        catch (Exception $e)
        {
            $this->addData('mensajeError', $e -> getMessage());
            $this->hacerPedido();
        }
    }

    public function verPedidos()
    {
        if($this->permissions())
        {
            $this->token();
            $this->paginaCorrecta($this->PedidosModel->totalPedidos($_SESSION['idUsuario']));
            $this->addData('pedidos', $this->PedidosModel->pedidosUsuarios($_SESSION['idUsuario'], $this->conf->getConfiguracion()->cantPagina, $_GET['offset']));
            $this->addData('pag', $_GET['pag']);
            $this->display("PedidosListarTemplate.twig");
        }
    }
    public function paginaCorrecta($total)
    {
        if (! isset($_GET['pag'])) throw new Exception('Error:No hay una página que mostrar');
        elseif ($total->total <= $_GET['pag'] *  $this->conf->getConfiguracion()->cantPagina){  $_GET['pag'] = 0; $_GET['offset'] = 0;}
        else $_GET['offset'] = $this->conf->getConfiguracion()->cantPagina * $_GET['pag'];
        if ($_GET['offset'] < 0) $_GET['offset'] = 0;
        $_GET['offset'] .= "";
    }
 

    public function verDetalle()
    {
        if($this->permissions()) {
            try
            {
                $this->validator->validarNumeros($_POST['idPedido'], "Que estás tocando picaron?", 3);
                $pedido = $this->PedidosModel->getPedido($_POST['idPedido']);
                if (!$pedido) throw new Exception('El pedido no es valido');
                if ($pedido->idUsuario != $_SESSION['idUsuario']) throw new Exception('El pedido no pertenece al usuario');
                $this->addData('detalles', $this->PedidosModel->getDetalle($_POST['idPedido']));
                $this->token();
                $this->display("mostrarDetalle.twig");

            }
            catch (Exception $e)
            {
                $this->addData('mensajeError', $e->getMessage());
                $_GET['pag'] = '0';
                $this->verPedidos();
            }
        }
    }
    
    public function formCancelarPedido()
    {
        if($this->permissions())
        {
            $this->addData('id', $_POST['idPedido']);
            $this->token();
            $this->display('cancerlarPedido.twig');
        }
    }

    public function cancelarPedido()
    {
        if($this->permissions())
        {
            try
            {
                if (!isset($_POST['tokenScrf'])) throw new Exception("no hay un token de validación");
                if (!$this->tokenIsValid($_POST['tokenScrf'])) throw new Exception("el token no es valido");
                $this->validator->validarNumeros($_POST['idPedido'], "Que estás tocando picaron?", 3);
                $pedido = $this->PedidosModel->getPedido($_POST['idPedido']);
                if (!$pedido) throw new Exception('El pedido no es valido');
                if ($pedido->idUsuario != $_SESSION['idUsuario']) throw new Exception('El pedido no pertenece al usuario');
                if (($pedido->idEstado != "pendiente") || ($pedido->intervalo > 1800)) throw new Exception("El pedido no cumple los requisitos para ser cancelado");
                $detalles = $this->PedidosModel->getDetalle($_POST['idPedido']);
                $this->PedidosModel->actualizarEstado("cancelado", $_POST['idPedido']);

                if (strlen($_POST['comentario']) > 0) $this->PedidosModel->actualizarComentario($_POST['comentario'], $_POST['idPedido']);

                foreach ($detalles as $detalle)
                {
                    $this->ProductosModel->actualizarCantProductos($detalle->idProducto, $detalle->stock + $detalle->cantidad);
                }
                $_GET['pag'] = 0;
                $this->verPedidos();
            }
            catch (Exception $e)
            {
                $this->addData('mensajeError', $e->getMessage());
                $this->addData('id', $_POST['idPedido']);
                $this->token();
                $this->display('cancerlarPedido.twig');
            }
        }
    }

    public function pedidosRango()
    {
        try
        {
            if (! isset($_POST['tokenScrf'])) throw new Exception("no hay un token de validación");
            if (! $this->tokenIsValid($_POST['tokenScrf'])) throw new Exception("el token no es valido");
            $this->validator->validarFecha($_POST['fechaInicio'], "la fecha posee un mal formato");
            $this->validator->validarFecha($_POST['fechaFin'], "la fecha posee un mal formato");
            $this->validator->varSet($_POST['submitButton2'], "tenes que entrar por el lugar adecuado");
            $fechaInicio=$_POST['fechaInicio'];
            $fechaFin=$_POST['fechaFin'];
            if ($fechaFin < $fechaInicio) throw new Exception("La fecha de fin no puede ser inferior a la fecha de inicio");
            $_GET['pag']=0;
            $_GET['inicio']= $_POST['fechaInicio'];
            $_GET['fin'] = $_POST['fechaFin'];
            $this->addData('fechaInicio', $_POST['fechaInicio']);
            $this->addData('fechaFin', $_POST['fechaFin']);
            $this->mostrarPedidoRango();
        }
        catch (Exception $e)
        {
            $this->addData('fechaInicio', $_POST['fechaInicio']);
            $this->addData('fechaFin', $_POST['fechaFin']);
            $this->addData('mensajeError', $e -> getMessage());
            $_GET['pag'] = '0';
            $this->verPedidos();
        }

    }

    public function mostrarPedidoRango()
    {
        if($this->permissions())
        {
            $this->paginaCorrecta($this->PedidosModel->totalPedidosRango($_SESSION['idUsuario'], $_POST['fechaInicio'], $_POST['fechaFin']));
            $this->addData('pedidos', $this->PedidosModel->pedidosUsuariosRango($_SESSION['idUsuario'], $this->conf->getConfiguracion()->cantPagina, $_GET['offset'], $_GET['inicio'], $_GET['fin']));
            $this->addData('pag', $_GET['pag']);
            $this->addData('inicio', $_GET['inicio']);
            $this->addData('fin', $_GET['fin']);
            $this->addData('rango', true);
            $this->token();
            $this->display("PedidosListarTemplate.twig");
        }
    }
}
