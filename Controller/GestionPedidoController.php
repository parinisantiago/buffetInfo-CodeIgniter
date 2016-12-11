<?php


class GestionPedidoController extends Controller
{
    private $pedidos;
    private $menu;
    private $venta;
    private $producto;

    function __construct()
    {
        parent::__contruct();
        $this->pedidos = new PedidosModel();
        $this->menu = new MenuModel();
        $this->venta = new VentaModel();
        $this->producto = new ProductosModel();
    }

    public function getPermission(){
        Session::init();
        return ((Session::getValue('rol') == '1' ) || (Session::getValue('rol') == '0' ) );
    }

    public function mostrarPedidos(){
        $_GET['pag'] = 0;
        $this->paginaCorrecta($this->pedidos->totalPedidosPendientes());
        $this->dispatcher->pedidos = $this->pedidos->getPedidosPendientes($this->conf->getConfiguracion()->cantPagina, $_GET['offset']);
        $this->dispatcher->render('Backend/GestionPedidosListarTemplate.twig');
    }

    public function paginaCorrecta($total){

        if (! isset($_GET['pag'])) throw new Exception('Error:No hay una página que mostrar');
        elseif ($total->total <= $_GET['pag'] *  $this->conf->getConfiguracion()->cantPagina){  $_GET['pag'] = 0; $_GET['offset'] = 0;}
        else $_GET['offset'] = $this->conf->getConfiguracion()->cantPagina * $_GET['pag'];
        if ($_GET['offset'] < 0) $_GET['offset'] = 0;
        $_GET['offset'] .= "";
    }

    public function verPedidos()
    {
        $this->paginaCorrecta($this->pedidos->totalPedidosPendientes());
        $this->dispatcher->pedidos = $this->pedidos->getPedidosPendientes($this->conf->getConfiguracion()->cantPagina, $_GET['offset']);
        $this->dispatcher->pag = $_GET['pag'];
        $this->dispatcher->render('Backend/GestionPedidosListarTemplate.twig');
    }


    public function verDetalle()
    {
        try
        {
            $this->validator->validarNumeros($_POST['idPedido'], "Que estás tocando picaron?",3);
            $pedido = $this->pedidos->getPedido($_POST['idPedido']);
            if(!$pedido) throw new valException('El pedido no es valido');

            $this->dispatcher->detalles = $this->pedidos->getDetalle($_POST['idPedido']);
            $this->dispatcher->render("Backend/mostrarDetalle.twig");
        }
        catch (valException $e)
        {
            $this->dispatcher->mensajeError = $e -> getMessage();
            $this->dispatcher->render('Backend/GestionPedidosListarTemplate.twig');
        }


    }

    public function formCancelarPedido(){
        $this->token();
        $this->dispatcher->id = $_POST['idPedido'];
        $this->dispatcher->render('Backend/cancerlarPedido.twig');
    }

    public function cancelarPedido()
    {
        try
        {
            if (! isset($_POST['tokenScrf'])) throw new valException("no hay un token de validación");
            if (! $this->tokenIsValid($_POST['tokenScrf'])) throw new valException("el token no es valido");
            $this->validator->validarNumeros($_POST['idPedido'], "Que estás tocando picaron?",3);
            $pedido = $this->pedidos->getPedido($_POST['idPedido']);
            if(!$pedido) throw new valException('El pedido no es valido');
            if(($pedido->idEstado != "pendiente"))throw new valException("El pedido no cumple los requisitos para ser cancelado");
            $detalles = $this->pedidos->getDetalle($_POST['idPedido']);
            $this->pedidos->actualizarEstado("cancelado", $_POST['idPedido']);

            if(strlen($_POST['comentario']) > 0 ) $this->pedidos->actualizarComentario($_POST['comentario'], $_POST['idPedido']);

            foreach ($detalles as $detalle)
            {

                $this->producto->actualizarCantProductos($detalle->idProducto, $detalle->stock + $detalle->cantidad);
            }

            $_GET['pag'] = 0;

            $this->verPedidos();

        }
        catch (valException $e)
        {
            $this->dispatcher->mensajeError = $e -> getMessage();
            $this->dispatcher->id = $_POST['idPedido'];
            $this->dispatcher->render('Backend/cancerlarPedido.twig');
        }


    }

    public function formAceptarPedido(){
        $this->token();
        $this->dispatcher->id = $_POST['idPedido'];
        $this->dispatcher->render('Backend/aceptarPedido.twig');
    }

    public function aceptarPedido(){
        try
        {
            if (! isset($_POST['tokenScrf'])) throw new valException("no hay un token de validación");
            if (! $this->tokenIsValid($_POST['tokenScrf'])) throw new valException("el token no es valido");
            $this->validator->validarNumeros($_POST['idPedido'], "Que estás tocando picaron?",3);
            $pedido = $this->pedidos->getPedido($_POST['idPedido']);
            if(!$pedido) throw new valException('El pedido no es valido');
            if(($pedido->idEstado != "pendiente"))throw new valException("El pedido no cumple los requisitos para ser cancelado");
            $detalles = $this->pedidos->getDetalle($_POST['idPedido']);
            $this->pedidos->actualizarEstado("Entregado", $_POST['idPedido']);

            if(strlen($_POST['comentario']) > 0 ) $this->pedidos->actualizarComentario($_POST['comentario'], $_POST['idPedido']);

            foreach ($detalles as $detalle)
            {

                $venta['idProducto'] = $detalle->idProducto;
                $venta['precioVentaUnitario'] = $detalle->precioVentaUnitario;
                $venta['cant'] = $detalle->cantidad;

                $this->venta->insertarVentaId($venta);
            }

            $_GET['pag'] = 0;

            $this->verPedidos();
        }
        catch (valException $e)
        {
            $this->dispatcher->mensajeError = $e -> getMessage();
            $this->dispatcher->id = $_POST['idPedido'];
            $this->dispatcher->render('Backend/aceptarPedido.twig');
        }



    }
}