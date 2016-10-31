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
                $this->controller = new BackendController();
                break;
            case '1':
                $this->controller = new BackendController();
                break;
            default:
                throw new Exception("usuario no valido");

        }
    }

}

?>