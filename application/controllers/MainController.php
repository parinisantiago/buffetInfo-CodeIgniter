<?php
//controlador principal

include_once("Controller.php");
include_once(dirname(__DIR__) . "/models/MainModel.php");
include_once(dirname(__DIR__)."/models/MenuModel.php");
include_once("BackendController.php");

class MainController extends Controller
{
    private $model;
    private $controller;
    private $menuModel;

    public function __construct()
    {
        parent::__construct();
        $this->model = new MainModel();
        $this->menuModel = new MenuModel();
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
            $this->data['menu'] = $this->menuModel->getMenuByDia2();
            $this->render('MainTemplate');
        }
    }

    public function cerrarSesion()
    {
        Session::destroy();
        $this->index();
    }


}

?>
