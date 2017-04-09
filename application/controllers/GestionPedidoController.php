<?php
include_once ("Controller.php");

class GestionPedidoController extends Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('PedidosModel');
        $this->load->model('VentaModel');
    }

    public function getPermission()
    {
        Session::init();
        return ((Session::getValue('rol') == '1' ) || (Session::getValue('rol') == '0' ) );
    }

    public function mostrarPedidos()
    {
        if($this->permissions())
        {
            $_GET['pag'] = 0;
            $this->paginaCorrecta($this->PedidosModel->totalPedidosPendientes());
            $this->addData('pedidos', $this->PedidosModel->getPedidosPendientes($this->conf->getConfiguracion()->cantPagina, $_GET['offset']));
            $this->display('GestionPedidosListarTemplate.twig');
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

    public function verPedidos()
    {
        if($this->permissions())
        {
            $this->paginaCorrecta($this->PedidosModel->totalPedidosPendientes());
            $this->addData('pedidos', $this->PedidosModel->getPedidosPendientes($this->conf->getConfiguracion()->cantPagina, $_GET['offset']));
            $this->addData('pag', $_GET['pag']);
            $this->display('GestionPedidosListarTemplate.twig');
        }
    }


    public function verDetalle()
    {
        if($this->permissions())
        {
            try
            {
                $this->validator->validarNumeros($_POST['idPedido'], "Que estás tocando picaron?", 3);
                $pedido = $this->PedidosModel->getPedido($_POST['idPedido']);
                if (!$pedido) throw new valException('El pedido no es valido');
                $this->addData('detalles', $this->PedidosModel->getDetalle($_POST['idPedido']));
                $this->display("mostrarDetalle.twig");
            }
            catch (Exception $e)
            {
                $this->addData('mensajeError', $e->getMessage());
                $this->display('GestionPedidosListarTemplate.twig');
            }
        }
    }

    public function formCancelarPedido()
    {
        if($this->permissions())
        {
            $this->token();
            $this->addData('id', $_POST['idPedido']);
            $this->display('cancerlarPedido.twig');
        }
    }

    public function cancelarPedido()
    {
        if($this->permissions())
        {
            try
            {
                if (!isset($_POST['tokenScrf'])) throw new valException("no hay un token de validación");
                if (!$this->tokenIsValid($_POST['tokenScrf'])) throw new valException("el token no es valido");
                $this->validator->validarNumeros($_POST['idPedido'], "Que estás tocando picaron?", 3);
                $pedido = $this->PedidosModel->getPedido($_POST['idPedido']);
                if (!$pedido) throw new valException('El pedido no es valido');
                if (($pedido->idEstado != "pendiente")) throw new valException("El pedido no cumple los requisitos para ser cancelado");
                $detalles = $this->PedidosModel->getDetalle($_POST['idPedido']);
                $this->PedidosModel->actualizarEstado("cancelado", $_POST['idPedido']);

                if (strlen($_POST['comentario']) > 0) $this->PedidosModel->actualizarComentario($_POST['comentario'], $_POST['idPedido']);

                foreach ($detalles as $detalle)
                {
                    $this->PedidosModel->actualizarCantProductos($detalle->idProducto, $detalle->stock + $detalle->cantidad);
                }

                $_GET['pag'] = 0;

                $this->verPedidos();

            }
            catch (Exception $e)
            {
                $this->addData('mensajeError', $e->getMessage());
                $this->addData('id', $_POST['idPedido']);
                $this->display('cancerlarPedido.twig');
            }
        }
    }

    public function formAceptarPedido()
    {
        if($this->permissions())
        {
            $this->token();
            $this->addData('id', $_POST['idPedido']);
            $this->display('aceptarPedido.twig');
        }
    }

    public function aceptarPedido()
    {
        if ($this->permissions())
        {
            try
            {
                if (!isset($_POST['tokenScrf'])) throw new valException("no hay un token de validación");
                if (!$this->tokenIsValid($_POST['tokenScrf'])) throw new valException("el token no es valido");
                $this->validator->validarNumeros($_POST['idPedido'], "Que estás tocando picaron?", 3);
                $pedido = $this->PedidosModel->getPedido($_POST['idPedido']);
                if (!$pedido) throw new valException('El pedido no es valido');
                if (($pedido->idEstado != "pendiente")) throw new valException("El pedido no cumple los requisitos para ser cancelado");
                $detalles = $this->PedidosModel->getDetalle($_POST['idPedido']);
                $this->PedidosModel->actualizarEstado("Entregado", $_POST['idPedido']);

                if (strlen($_POST['comentario']) > 0) $this->PedidosModel->actualizarComentario($_POST['comentario'], $_POST['idPedido']);

                foreach ($detalles as $detalle)
                {
                    $venta['idProducto'] = $detalle->idProducto;
                    $venta['precioVentaUnitario'] = $detalle->precioVentaUnitario;
                    $venta['cant'] = $detalle->cantidad;
                    $this->VentaModel->insertarVentaId($venta);
                }
                $_GET['pag'] = 0;
                $this->verPedidos();
            }
            catch (Exception $e)
            {
                $this->addData('mensajeError', $e->getMessage());
                $this->addData('id', $_POST['idPedido']);
                $this->display('aceptarPedido.twig');
            }
        }
    }
}