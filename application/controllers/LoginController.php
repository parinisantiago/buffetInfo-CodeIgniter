<?php

include_once(dirname(__DIR__) . '/models/UserModel.php');
include_once(dirname(__DIR__).'/controllers/MainController.php');

class LoginController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('UserModel');
    }

    public function index()
    {
        try
        {
            if (! Session::userLogged())
            {
                $this->validateLogin();
                $this->user = $this->UserModel->getUser($_POST['username'], $_POST['pass']);
                $this->setSession();
            }
            $this->display('IndexTemplate.twig');
        }
        catch (Exception $e)
        {
            $this->addData('mensajeError',$e->getMessage());
            $this->display('MainTemplate.twig');
        }
        return true;
    }

    private function setSession()
    {
        Session::setValue( $this -> user[0] -> usuario, 'username');
        Session::setValue( $this -> user[0] -> idRol, 'rol');
        Session::setValue( $this-> user[0] -> idUsuario, 'idUsuario');
        Session::setValue($this -> user[0] -> habilitado, 'userHabilitado');
        Session::setValue(true, 'logged');
        $this->addData('rol', Session::getValue('rol'));
        $this->addData('userHabilitado',Session::getValue('userHabilitado'));
    }

    public function validateLogin()
    {
        $this->validator->varSet($_POST['submit'], "Presione el boton de submit");
        $this->validator->validarString($_POST['username'], "Error: En usuario o contraseña", 15);
        $this->validator->validarString($_POST['pass'], "Error: En usuario o contraseña", 15);
        if (empty($this->UserModel->selectUser($_POST['username'], $_POST['pass']))) throw new Exception("El usuario o contraseña son incorrectos");

    }


}