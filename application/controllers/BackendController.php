<?php
require_once 'Controller.php';
require_once 'MainController.php';
require_once(dirname(__DIR__).'/models/RolModel.php');
require_once(dirname(__DIR__).'/models/ProductosModel.php');
require_once(dirname(__DIR__).'/models/CategoriaModel.php');

/*
 * Esta clase encapsula el comportamiento comun de los 2 tipos de usuario que 
 * pueden llegar al backend Admin y Gestion
 */
class BackendController extends Controller{
    public $model;
    public $rolModel;
    public $productoModel;
    public $categoriaModel;

    public function __construct(){
            parent::__construct();
            $this->model = new MainModel();
            $this->rolModel = new RolModel();
            $this->productoModel = new ProductosModel();
            $this->categoriaModel = new CategoriaModel();
    }

    public function getPermission()
    {
        Session::init();
        $rol = Session::getValue('rol');
        return (($rol == '0') || ($rol == '1'));
    }

    public function index(){
        try
        {
            if (!$this->getPermission()) throw new Exception('El usuario no posee permisos para acceder a esta funcionalidad');
            $this->display("IndexTemplate.twig");
        }
        catch (Exception $e)
        {
            $this->addData('mensajeError', $e->getMessage());
            $main = new MainController();
            $main->index();
        }
    }

    public function setMensajeError($error){
        $this->addData('mensajeError', $error);
        $this->index();
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

    /*--- paginacion ---*/
    public function paginaCorrecta($total){
        if (! isset($_GET['pag'])) throw new Exception('Error:No hay una página que mostrar');
        elseif ($total->total <= $_GET['pag'] *  $this->conf->getConfiguracion()->cantPagina){  $_GET['pag'] = 0; $_GET['offset'] = 0;}
        else $_GET['offset'] = $this->conf->getConfiguracion()->cantPagina * $_GET['pag'];
        if ($_GET['offset'] < 0) $_GET['offset'] = 0;
        $_GET['offset'] .= "";
    }
}
