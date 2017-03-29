<?php
//controlador principal

include_once("Controller.php");
include_once(dirname(__DIR__) . "/models/MainModel.php");
include_once(dirname(__DIR__)."/models/MenuModel.php");
include_once("BackendController.php");

class MainController extends Controller
{
    private $controller;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('MenuModel');
    }

    public function index()
    {
        if( Session::userLogged() )
        {
            $this->controller = new BackendController();
            $this->controller->index();
        }
        else
        {
            $this->data['menu'] = $this->MenuModel->getMenuToday2();
            $this->display('MainTemplate.twig');
        }
    }

    public function cerrarSesion()
    {
        Session::destroy();
        $this->index();
    }


}

?>
