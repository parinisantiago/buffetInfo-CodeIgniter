<?php
//controlador para usuarios anónimos
class MainUserController extends Controller
{
    private $model;
    private $user;
    private $usernamePOST;
    private $passPOST;
    private $controller;

    public function __construct(){

        parent::__contruct();
        $this->model = new MainUserModel();

    }

    //carga el index para usuarios no logueados
    public function init(){
        if( Session::userLogged() )$this->callUserRolController();
        else $this->index();
    }

    public function index(){
        $this->dispatcher->render("Main/MainTemplate.twig");
    }

    public function login(){
        //si no inicio sesion previamente que se loguee.
        if (! Session::userLogged()) {
            $this->validateLogin();
            //si se logueo de forma correcta setea la sesion del usuario y llama al controllador correspondiente
            $this->user = $this->model->getUser($this->usernamePOST, $this->passPOST);
            $this->setSession();
        }

        $this->callUserRolController();
    }

    public function validateLogin()
    {
        if (!isset($_POST['submit'])) throw new Exception("Apreta el botón de logeo macho");

        if (isset ($_POST['username'])) $this->usernamePOST = $_POST['username'];
        else throw new Exception("No se ha ingresado ningun usuario");

        if ($this->model->isDeleted($this->usernamePOST)) throw new Exception("El usuario a sido eliminado");

        if (isset ($_POST['pass'])) $this->passPOST = $_POST['pass'];
        else throw new Exception("No se ha ingresado ninguna contraseña");
        if (!$this->model->userExist($this->usernamePOST)) throw new Exception("El usuario no existe");

        elseif (!$this->model->passDontMissmatch($this->passPOST)) throw new Exception("Contraseña incorrecta");
        return true;
    }

    private function setSession()
    {
        Session::setValue( $this -> user -> usuario, 'username');
        Session::setValue( $this -> user -> idRol, 'rol');
        Session::setValue(true, 'logged');
        return true;
    }

    public function initError($error){
        if (Session::userLogged()) {
            $this->selectRol(); //llama al controlador de la sesion correspondiente
            $this->controller->setMensajeError($error); //sete el mensaje de error en ese controlador, porque el dispatcher depende del controlador
        } else {
            $this->dispatcher->mensajeError= $error;
            $this->index();
        }
    }

    public function callUserRolController()
    {
        $this->selectRol();
        $this->controller -> index();
        return true;
    }


    public function cerrarSesion()
    {
        Session::destroy();
        $this->index();
    }

    protected function selectRol()
    {
        switch (Session::getValue('rol')) {
            //dependiendo del idRol del usuario, instanciamos el rol correspondiente y llamamos a su index();
            case '0':
                $this->controller = new AdminController();
                break;
            case '1':
                $this->controller = new GestorController();
                break;
            default:
                throw new Exception("usuario no valido");

        }
    }

}

?>