<?php
require_once 'Controller/Controller.php';
class AdminController extends Controller
{

    public $model;
    public $rolModel;

    public function __construct()
    {
            parent::__contruct();
            $this->model = new MainUserModel();
            $this->rolModel = new RolModel();
    }


    public function getPermission()
    {
            Session::init();
            return strcmp(Session::getValue('rol'), 'admin');
    }

    public function init()
    {
            $this->dispatcher->render("Backend/adminIndexTemplate.twig");
    }

/* usuarios */

    public function setMensajeError($error){
        $this->dispatcher->mensajeError = $error;
        $this->init();
    }

    public function abmUsuario()
    {
            $this->dispatcher->users = $this->model->getAllUser();
            $this->dispatcher->render('Backend/abmUsuarios.twig');
    }

    public function eliminarUsuario()
    {

            if (!isset($_POST['submitButton'])) throw new Exception("Apreta el botón de eliminar macho");

            if (!isset($_POST['idUsuario'])) throw new Exception("Faltan datos para poder eliminar el usuario");
            else $idUsuario = $_POST['idUsuario'];

            $this->dispatcher->user = $this->model->deleteUser($idUsuario);

            $this->abmUsuario();

    }

	public function registroUsuario()
	{
        $this->dispatcher->rols = $this->rolModel->getAllRols();
        $this->dispatcher->render("Backend/registroUsuariosTemplate.twig");

	}

    public function modificarUsuario()
    {
        if (! isset($_POST['submitButton'])) throw new Exception('Apreta el boton de modificar macho');
        if (! isset($_POST['idUsuario'])) throw new Exception('Como vas a modificar un usuario sin ID?');

        $this->dispatcher->user = $this->model->getUserById($_POST['idUsuario']);
        $this->registroUsuario();
    }



/* ---Productos---*/


    public function productosListar(){
        $this->model = new ProductosModel();
        $this->dispatcher->producto =$this ->model->getAllProducto();
        $this->dispatcher->render("Backend/ProductosListarTemplate.twig");
    }
    public function productosAM(){
        $this->model = new ProductosModel();
        if (isset($_POST["idProducto"])){
            $this->dispatcher->producto =$this ->model->searchIdProducto($_POST["idProducto"]);
        }
        $this->dispatcher->render("Backend/ProductosAMTemplate.twig");
    }
    public function productosAMPost(){
        $this->model = new ProductosModel();
        if (isset($_POST["idProducto"])){}
    }
    public function productosE(){
     $this->dispatcher->render("Backend/ProductosAMTemplate.twig");
    }
}
?>