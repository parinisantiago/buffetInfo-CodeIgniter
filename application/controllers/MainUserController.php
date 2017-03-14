<?php
//controlador principal

include_once("Controller.php");
include_once(dirname(__DIR__)."/models/MainUserModel.php");
include_once(dirname(__DIR__)."/models/MenuModel.php");

class MainUserController extends Controller
{
    private $model;
    private $user;
    private $usernamePOST;
    private $passPOST;
    private $controller;
    private $menuModel;

    public function __construct(){

        parent::__construct();
        $this->model = new MainUserModel();
        $this->menuModel = new MenuModel();

    }

    //carga el index para usuarios no logueados
    public function init()
    {
        if( Session::userLogged() )$this->callUserRolController();
        else $this->index();
    }

    public function index()
    {
        $this->data['menu'] = $this->menuModel->getMenuByDia2();
        $this->render('MainTemplate');
    }

    public function initError($error)
    {
        if (Session::userLogged())
        {
            $this->selectRol(); //llama al controlador de la sesion correspondiente
            $this->controller->setMensajeError($error); //sete el mensaje de error en ese controlador, porque el dispatcher depende del controlador
        }
        else
        {
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
            case '2':
                $this->controller = new BackendController();
                break;
            default:
                throw new Exception("usuario no valido");

        }
    }

}

?>
