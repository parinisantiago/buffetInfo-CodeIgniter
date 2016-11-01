<?php
require_once 'Controller/Controller.php';

class MenuController extends Controller{
    public $menuModel;
    public $productosModel;

    public function __construct(){
        parent::__contruct();
        $this->menuModel= new MenuModel();
        $this->productosModel = new ProductosModel();
    }

    public function getPermission()
    {
        Session::init();
        $rol = Session::getValue('rol');
        return (($rol == '0') || ($rol == '1'));
    }

    public function menu(){
        /*deberia mostrar los menu para el dia que llegue como parametro
         * si no hay parametros muestra el de hoy
         * tambien numeros de paginas
         * agregar un marco de color sobre la fecha seleccionada en el calendario
         */

        $this->paginaCorrecta($this->menuModel->totalMenu());
        $this->dispatcher->menu = $this->menuModel->getAllMenu($this->conf->getConfiguracion()->cantPagina,$_GET['offset']);
        $this->dispatcher->pag = $_GET['pag'];
        $this->dispatcher->method = "menu";
        $this->dispatcher->render("Backend/calendarioTemplate.twig");
    }

    public function menuDia(){

        /* muestra el menu para un dia en particular, le mande un try catch por las dudas de que pasen cualquier cosa por get */

        try{
            $this->validator->validarFecha($_GET['fecha'], "Fecha no valida");
            $this->paginaCorrecta($this->menuModel->totalMenu());
            $this->dispatcher->menu = $this->menuModel->getAllMenuDia($this->conf->getConfiguracion()->cantPagina,$_GET['offset'],$_GET['fecha']);
            //$this->dispatcher->productos = $this->menuModel->getProductos($this->conf->getConfiguracion()->cantPagina,$_GET['offset'])
            $this->dispatcher->fecha=$_GET['fecha'];
            $this->dispatcher->pag = $_GET['pag'];
            $this->dispatcher->method = "menuDia";
            $this->dispatcher->render("Backend/calendarioTemplate.twig");


        } catch (valException $e){
            $this->dispatcher->mensajeError = $e->getMessage();
            $this->menu();
        }
    }
    public function menuAM(){
        /*falta agregar que pregunte si le envian un menu por parametro y que lo setee */
        $this->dispatcher->producto = $this->productosModel->getAllProducto(99,0);
        $this->dispatcher->render("Backend/MenuAMTemplate.twig");
    }

    public function menuAMPOST()
    {
        /*valida que los parametros sean correctos y despues se fija si ya existe el menu*/

        try{
        $this->validateMenu($_POST);
        }catch (valException $e){
            /* falta que setee post */
            $this->dispatcher->mensajeError = $e->getMessage();
            $_GET['pag'] = 0;
            $this->menu();
        }
        if (!isset($_POST['idMenu'])) $this->agregarMenu($_POST);
        else $this->modificarMenu($_POST);
        $_GET['pag'] = 0;
        $this->menu();
    }

    public function agregarMenu($menu)
    {
        $image= basename($_FILES['foto']['name']);
        $fecha= $_POST['fecha'];

        if(! move_uploaded_file($_FILES['foto']['tmp_name'], files.$image)) throw new valException("no se pudo guardar la imagen del menu");

        $idMenu = $this->menuModel->insertarMenu($fecha, $image);

        foreach ($menu['selectProdMult'] as $prod) {

            $this->menuModel->insertarProd($idMenu,$prod);

        }

    }

    public function modificarMenu($menu)
    {



    }

    public function menuEliminar(){
        echo"DESINTEGRAR";
    }

    public function validateMenu($menu){
        if(! ($_FILES['foto']['type'] == 'image/png' ||  $_FILES['foto']['type'] == 'image/jpg' || $_FILES['foto']['type'] = 'image/jpge')) throw new valException("el formato de la imagen no es valido");

        $this->validator->validarFecha($menu['fecha'], "Fecha no valida");
        $this->validator->varSet($menu['selectProdMult']);

        foreach ($menu['selectProdMult'] as $prod){
            if (! $this->productosModel->searchIdProducto($prod)) throw new valException("Uno de los productos seleccionados no es valido");
        }

    }

    public function paginaCorrecta($total){
        if (! isset($_GET['pag'])) throw new Exception('Error:No hay una página que mostrar');
        elseif ($total->total <= $_GET['pag'] *  $this->conf->getConfiguracion()->cantPagina){  $_GET['pag'] = 0; $_GET['offset'] = 0;}
        else $_GET['offset'] = $this->conf->getConfiguracion()->cantPagina * $_GET['pag'];
        if ($_GET['offset'] < 0) $_GET['offset'] = 0;
        $_GET['offset'] .= "";
    }
}