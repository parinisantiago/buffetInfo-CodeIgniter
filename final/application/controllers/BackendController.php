<?php
require_once 'Controller.php';
require_once 'MainController.php';
/*
 * Esta clase encapsula el comportamiento comun de los 2 tipos de usuario que 
 * pueden llegar al backend Admin y Gestion
 */
class BackendController extends Controller{

    public function __construct(){
            parent::__construct();
            $this->load->model('ProductosModel');
    }

    public function getPermission()
    {
        Session::init();
        $rol = Session::getValue('rol');
        return (($rol == '0') || ($rol == '1'));
    }

    public function index()
    {
       if($this->permissions()) $this->display("IndexTemplate.twig");
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
