<?php

include_once(dirname(__DIR__) . '/models/UserModel.php');
include_once(dirname(__DIR__).'/controllers/MainController.php');

class LoginController extends Controller
{

    private $model;
    private $user;
    private $controller;

    public function __construct(){

        parent::__construct();
        $this->load->model('UserModel');

    }

    public function index(){
        if (! Session::userLogged()) {
            $this->validateLogin();
            $this->user = $this->UserModel->getUser($_POST['username'], $_POST['pass']);
            $this->setSession();
        }
        $this->controller = new MainController();
        $this->controller->index();
    }

    private function setSession()
    {
        Session::setValue( $this -> user -> usuario, 'username');
        Session::setValue( $this -> user -> idRol, 'rol');
        Session::setValue( $this-> user -> idUsuario, 'idUsuario');
        Session::setValue(true, 'logged');
    }

    public function validateLogin()
    {
        try
        {
        $this->validator->varSet($_POST['submit'], "Presione el boton de submit");

        $this->validator->validarString($_POST['username'], "Error: En usuario o contraseña", 15);
        if ($this->model->isDeleted($_POST['username'])) throw new Exception("El usuario a sido eliminado");

        $this->validator->validarString($_POST['pass'], "Error: En usuario o contraseña", 15);

        if (!$this->model->userExist($_POST['username'])) throw new Exception("Error: El usuario no existe");


        elseif (!$this->model->passDontMissmatch($_POST['pass'])) throw new Exception("Contraseña incorrecta");
        }
        catch (Exception $e)
        {
            $this->addData('mensajeError',$e->getMessage());
            $this->controller = new MainController();
            $this->controller->index();
        }
        return true;
    }


}