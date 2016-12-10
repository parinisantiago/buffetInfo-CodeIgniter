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
        $date= date('Y-m-d');
        $this->paginaCorrecta($this->menuModel->totalMenu());
      
        $this->dispatcher->menu = $this->menuModel->getMenuByDia($this->conf->getConfiguracion()->cantPagina,$_GET['offset'],$date);
        $this->dispatcher->datos = $this->dispatcher->menu[1];
        $this->dispatcher->pag = $_GET['pag'];
        $this->dispatcher->method = "menu";
        $this->dispatcher->render("Backend/MenuListarTemplate.twig");
         
    }

    public function menuDia(){
        /* muestra el menu para un dia en particular, le mande un try catch por las dudas de que pasen cualquier cosa por get */
       try{
           if (!isset($_POST['fecha'])){
               $_POST['fecha']=date("Y-m-d");
           }
            $this->validator->validarFecha($_POST['fecha'], "Fecha no valida");
            $this->paginaCorrecta($this->menuModel->totalMenu());
            $this->dispatcher->menu = $this->menuModel->getMenuByDia($this->conf->getConfiguracion()->cantPagina,$_GET['offset'],$_POST['fecha']);
            //$this->dispatcher->productos = $this->menuModel->getProductos($this->conf->getConfiguracion()->cantPagina,$_GET['offset'])
           if(!empty($this->dispatcher->menu)) $this->dispatcher->datos = $this->dispatcher->menu[1];
            $this->dispatcher->fecha=$_POST['fecha'];
            $this->dispatcher->pag = $_GET['pag'];
            $this->dispatcher->method = "menuDia";
            $this->dispatcher->render("Backend/MenuListarTemplate.twig");


        } catch (valException $e){
            $this->dispatcher->mensajeError = $e->getMessage();
            $this->menu();
        }
    }
    public function menuAM(){
        /*falta agregar que pregunte si le envian un m por parametro y que lo setee */
        $this->dispatcher->producto = $this->productosModel->getAllProducto(99,0);
        $this->token();
        $this->dispatcher->render("Backend/MenuAMTemplate.twig");
    }

    public function menuAMPOST()
    {
        /*valida que los parametros sean correctos y despues se fija si ya existe el menu*/

        try{


        $this->validateMenu($_POST);
            if (!isset($_POST['idMenu'])) $this->agregarMenu($_POST);
            else $this->modificarMenu($_POST);
            if(!isset($_POST['un valor'])){
            $_GET['pag'] = 0;
            $this->menu();
            }
        }catch (valException $e){
            /* falta que setee post */
            $_POST['un valor'] = true;
            $this->dispatcher->mensajeError = $e->getMessage();
            $_GET['pag'] = 0;

            if(!isset($_POST['idMenu'])){
                $this->menuAM();
            }
            else if(isset($_POST['idMenu']) && isset($_POST['fecha'])){
                $menu = $this->menuModel->idMenu($_POST['idMenu']);
                $_POST['fecha'] = $menu->fecha;
                var_dump($_GET);
                $this->menuAMMod();

            } else{
                $this->menu();
            }
        }

    }

    public function agregarMenu($menu)
    {
        $image = basename($_FILES['foto']['name']);
        $fecha = $_POST['fecha'];
        try {

            if (! isset($_POST['tokenScrf'])) throw new valException("no hay un token de validación");
            if (! $this->tokenIsValid($_POST['tokenScrf'])) throw new valException("el token no es valido");

            if (!move_uploaded_file($_FILES['foto']['tmp_name'], files . $image)) throw new valException("no se pudo guardar la imagen del menu");

            if ($this->menuModel->getMenuDia($fecha)) throw new valException("Ya existe un menu para esta fecha");

            $idMenu = $this->menuModel->insertarMenu($fecha, $image);


            foreach ($menu['selectProdMult'] as $prod) {
                $this->menuModel->insertarProd($idMenu, $prod);

            }
        } catch (valException $e) {
            $_POST['un valor'] = true;
            $this->dispatcher->mensajeError = $e->getMessage();
            $this->dispatcher->valores = $_POST;
            $this->menuAM();
        }
    }

    public function menuAMMod(){

        try{
            $this->token();
            echo "entre";
            if(!isset($_POST['fecha'])) {
                $this->validator->varSet($_GET['fecha'], "no hay fecha");
                $fecha = $_GET['fecha'];
            } else {
                $fecha = $_POST['fecha'];
            }

            if (! $this->menuModel->getMenuDia($fecha)) throw new valException("No existe el menu para modificar");
            $this->dispatcher->menu = $this->menuModel->getMenuByDia(99,0,$fecha);
            $this->dispatcher->producto= $this->menuModel->getProdNotInMenu($this->dispatcher->menu['1']->idMenu);
            $this->dispatcher->datos = $this->dispatcher->menu[1];
            $this->dispatcher->render("Backend/MenuAMTemplate.twig");

        } catch (valException $e){
            $this->dispatcher->mensajeError = $e -> getMessage();
            $this->dispatcher->render("Backend/MenuListarTemplate.twig");
        }
        
    }

    public function modificarMenu($menu)
    {

        $fecha = $_POST['fecha'];
        $idMenu = $_POST['idMenu'];

        $menu= $this->menuModel->getMenuDia($fecha);

        //validaciones
        try{
            if( !$menu) throw new valException("no hay menu para este dia");
            if (($menu->idMenu != $idMenu)) throw new valException("La fecha elegida ya pertenece a otro menu");
            if (! isset($_POST['tokenScrf'])) throw new valException("no hay un token de validación");
            if (! $this->tokenIsValid($_POST['tokenScrf'])) throw new valException("el token no es valido");
            if( ($_FILES['foto']['size'] == 0 )) $foto= $_POST['foto2'];
            else{
                if(! ($_FILES['foto']['type'] == 'image/png' ||  $_FILES['foto']['type'] == 'image/jpg' || $_FILES['foto']['type'] = 'image/jpge')) throw new valException("el formato de la imagen no es valido");
                else{
                    $foto= basename($_FILES['foto']['name']);
                    if(! move_uploaded_file($_FILES['foto']['tmp_name'], files.$foto)) throw new valException("no se pudo guardar la imagen del menu");
                }
            }

            foreach ($_POST['selectProdMult'] as $prod){
                if (! $this->productosModel->searchIdProducto($prod)) throw new valException("Uno de los productos seleccionados no es valido");
            }
            $this->menuModel->eliminarMenu($idMenu);

            $idMenu2 = $this->menuModel->insertarMenu($fecha, $foto);
            foreach ($_POST['selectProdMult'] as $prod) {
                $this->menuModel->insertarProd($idMenu2,$prod);

            }
            $_GET['pag'] = 0;
            $_GET['fecha'] = $fecha;
            $this->paginaCorrecta($this->menuModel->totalMenu());
            $this->dispatcher->menu = $this->menuModel->getMenuByDia($this->conf->getConfiguracion()->cantPagina,$_GET['offset'],$_GET['fecha']);
            //$this->dispatcher->productos = $this->menuModel->getProductos($this->conf->getConfiguracion()->cantPagina,$_GET['offset'])
            $this->dispatcher->datos = $this->dispatcher->menu[1];
            $this->dispatcher->fecha=$_GET['fecha'];
            $this->dispatcher->pag = $_GET['pag'];
            $this->dispatcher->method = "menuDia";
        } catch (valException $e){
            $_POST['un valor'] = true;
            $this->dispatcher->mensajeError = $e -> getMessage();
            $this->menuAMMod();
        }





    }

    public function menuEliminar()
    {
       $this->dispatcher->menuModel =$this ->menuModel->eliminarMenu($_POST["idMenu"]);
        $_GET['pag'] = 0;
        $this->dispatcher->method = "menu";
        $this->menu();
    }

    public function validateMenu($menu){
        if(! ($_FILES['foto']['type'] == 'image/png' ||  $_FILES['foto']['type'] == 'image/jpg' || $_FILES['foto']['type'] = 'image/jpge')) throw new valException("el formato de la imagen no es valido");

        $this->validator->validarFecha($menu['fecha'], "Fecha no valida");
        $this->validator->varSet($menu['selectProdMult'], "Debe seleccionar por lo menos un producto para el menu");

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