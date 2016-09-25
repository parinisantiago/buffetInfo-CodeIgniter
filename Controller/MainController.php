<?php
//controlador para usuarios anónimos
class MainController extends Controller
{
    private $model;

    public function __construct(){

        parent::__contruct();
        $this->model = new MainModel();

    }

    //carga el index para usuarios no logueados
    public function init(){

        Session::init();
        $this->dispatcher->render("MainTemplate.php");

    }
}

?>