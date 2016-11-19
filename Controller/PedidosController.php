<?php


class PedidosController extends Controller
{

    private $pedidos;
    private $menu;
    private $venta;

    public function getPermission(){
        Session::init();
        return ((Session::getValue('rol') == '2' ));
    }

    function __construct()
    {
        parent::__contruct();
        $this->pedidos = new PedidosModel();
        $this->menu = new MenuModel();
        $this->venta = new VentaModel();
    }

    //le pasa el menu del dia a la vista, si no hay lo avisa.
    function hacerPedido()
    {
        $menuHoy = $this->menu->getMenuByDia(99,0,date('Y-m-d'));
        if ($menuHoy)
        {
            $this->dispatcher->idMenu= $menuHoy[0]->idMenu;
            $this->dispatcher->menu = $menuHoy;
            $this->dispatcher->render("Backend/RegistroPedidoTemplate.twig");
        }
        else
        {
            $this->dispatcher->mensajeError = "No hay un menu para el dia de hoy, por lo que no se pueden hacer pedidos";
            $this->dispatcher->render("Backend/IndexTemplate.twig");
        }
    }

    function validarPedido()
    {
        //bueno, vamos con las validaciones YAY :D

        try
        {
            //primero valido que me hayan pasado todas las variables

            $this->validator->varSet($_POST['idMenu'], "sin menu, no hay validacion");
            $this->validator->varSet($_POST['submit'], "algo raro hiciste, keep trying");
            $this->validator->validarNumeros($_POST['numPedidos'], "Error en la cantidad", 2);

            //ahora que se que tengo todo me traigo el menu.

            $idMenu = $_POST['idMenu'];
            $menuHoy = $this->menu->getMenuByDia(99,0,date('Y-m-d'));

            //si el menu de hoy no es como el de id, me estuvieron tocando las variables

            if ($menuHoy[1] -> idMenu != $idMenu ) throw new valException("Algo raro hiciste, keep trying");

            //ahora valido que haya suficientes productos para la cantidad de menus que me pidieron


            $cantidad = $_POST['numPedidos'];

            foreach ($menuHoy as $producto)
            {
                if ($producto->stock <  $cantidad) throw new valException("No hay suficiente cantidad de $producto->nombre para completar $cantidad pedidos");
            }

        }
        catch (valException $e)
        {
            ECHO $e->getMessage();
        }

        //Primero hay que agregar el pedido y recuperar su id.
        //despues hay que agregar cada producto y recuperar su id.
        //despues de agregar un producto, hay que agregar un pedido detalle.


        $venta = array();
        foreach ($menuHoy as $producto)
        {

            $venta['idProducto'] = $producto->idProducto;
            $venta['precioVentaUnitario'] = $producto->precioVentaUnitario;
            $venta['cant'] = $cantidad;


            echo ($this->venta->insertarVentaId($venta));

        }


    }

}