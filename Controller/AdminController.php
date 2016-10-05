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

    public function index()
    {
            $this->dispatcher->render("Backend/adminIndexTemplate.twig");
    }

/* usuarios */

    public function setMensajeError($error){
        $this->dispatcher->mensajeError = $error;
        $this->index();
    }

    public function abmUsuario()
    {
            $this->dispatcher->users = $this->model->getAllUser();
            $this->dispatcher->render('Backend/abmUsuarios.twig');
    }

    public function usuarioAM(){

            $this->validarUsuario(); //realiza validaciones mediante expresiones regulares
            if (! isset($_POST['idUsuario'])) $this->model->addUser($_POST['nombreUsuario'], $_POST['nombre'], $_POST['apellido'],$_POST['pass'], $_POST['dni'], $_POST['email'], $_POST['telefono'],$_POST['rol']);

            else if (Session::getValue('modUserId') == $_POST['idUsuario']) $this->model->modUser($_POST['idUsuario'],$_POST['nombreUsuario'], $_POST['nombre'], $_POST['apellido'],$_POST['pass'], $_POST['dni'], $_POST['email'],$_POST['telefono'], $_POST['rol']); //si es el usaurio guardado, lo modifica
            else throw new Exception('El id de usuario se vio modificado durante la operacion');
            $this->abmUsuario();

    }

    private function validarUsuario(){

        if (!isset($_POST['submitButton'])) throw new Exception("Apreta el botón de eliminar macho");

        if (! isset($_POST['nombreUsuario'])) throw new Exception('Falta escribir el nombreUsuarip');
        elseif (! preg_match("/^[a-zA-Z0-9]+$/", $_POST['nombreUsuario'])) throw new Exception('Valor del nombreUsuario no valido');

        if (! isset($_POST['nombre'])) throw new Exception('Falta escribir el nombre');
        elseif (! preg_match("/^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹ ]+$/", $_POST['nombre'])) throw new Exception('Valor del nombre no valido');

        if (! isset($_POST['pass'])) throw new Exception('Falta escribir la contraseaña');
        elseif (! preg_match("/^[a-zA-Z0-9]+$/", $_POST['pass'])) throw new Exception('Valor de la contraseña no valido');

        if (! isset($_POST['apellido'])) throw new Exception('Falta escribir el apellido');
        elseif (! preg_match("/^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹ ]+$/", $_POST['apellido'])) throw new Exception('Valor del apellido no valido');

        if (! isset($_POST['dni'])) throw new Exception('Falta escribir el dni');
        elseif (! preg_match("/^[0-9]+$/", $_POST['dni'])) throw new Exception('Valor del dni no valido');

        if (! isset($_POST['email'])) throw new Exception('Falta escribir el email');
        elseif (! preg_match("/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/", $_POST['email'])) throw new Exception('Valor del email no valido');

        if (! isset($_POST['telefono'])) throw new Exception('Falta escribir el telefono');
        elseif (! preg_match("/^[0-9]+$/", $_POST['telefono'])) throw new Exception('Valor del telefono no valido');

        if (!isset($_POST['rol'])) throw new Exception("Falta el rol");
        elseif (! $this->rolModel->getRolById($_POST['rol'])) throw new Exception('Rol invalido');
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
        if (! $this->model->getUserById($_POST['idUsuario'])) throw new Exception('Id invalido');
        Session::setValue($this->model->getUserById($_POST['idUsuario'])->idUsuario, 'modUserId'); //se guarda el usuario a modificar, para validar que no se cambie el valor del input del id.
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

/* ---Configuracion--- */

    public function configuracionSitio(){
        $this->dispatcher->render("Backend/ConfiguracionTemplate.twig");
    }

    public function changeConf(){
        $this->validarConf();
        if (isset($_POST['habilitado'])) echo 'bien';
    }

    private function validarConf(){
        var_dump($_POST);
        if (! isset($_POST['submitButton'])) throw new Exception('Apreta el boton de modificar macho');
        if (! isset($_POST['titulo'])) throw new Exception('Falta escribir el titulo');
        elseif (! preg_match("/^[a-zA-Z0-9 ]+$/", $_POST['titulo'])) throw new Exception('Valor del titulo no valido');
        if (! isset($_POST['descripcion'])) throw new Exception('Falta escribir el descripcion');
        elseif (! preg_match("/^[a-zA-Z0-9 .;,]+$/", $_POST['descripcion'])) throw new Exception('Valor de la  descripcion no valido');
        if (! isset($_POST['mensaje'])) throw new Exception('Falta escribir el mensaje');
        elseif (! preg_match("/^[a-zA-Z]+$/", $_POST['mensaje'])) throw new Exception('Valor del mensaje no valido');
        if (! isset($_POST['lista'])) throw new Exception('Falta escribir el numero de la lista');
        elseif (! preg_match("/^[0-9]+$/", $_POST['lista'])) throw new Exception('Valor del numero de la lista no valido');


    }
}
?>