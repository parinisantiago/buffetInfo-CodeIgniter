<?php


class LoginController extends Controller
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

    public function login(){
        //si no inicio sesion previamente que se loguee.
        if (! Session::userLogged()) {
            $this->validateLogin();
            //si se logueo de forma correcta setea la sesion del usuario y llama al controllador correspondiente
            $this->user = $this->model->getUser($_POST['username'], $_POST['pass']);
            $this->setSession();
        }
        $this->callUserRolController();
    }

    private function setSession()
    {
        Session::setValue( $this -> user -> usuario, 'username');
        Session::setValue( $this -> user -> idRol, 'rol');
        Session::setValue(true, 'logged');

    }

    public function validateLogin()
    {
        $this->validator->varSet($_POST['submit'], "Presione el boton de submit");

        $this->validator->validarString($_POST['username'], "Error: En usuario o contraseña", 15);
        if ($this->model->isDeleted($_POST['username'])) throw new Exception("El usuario a sido eliminado");

        $this->validator->validarString($_POST['pass'], "Error: En usuario o contraseña", 15);

        if (!$this->model->userExist($_POST['username'])) throw new Exception("Error: El usuario no existe");


        elseif (!$this->model->passDontMissmatch($_POST['pass'])) throw new Exception("Contraseña incorrecta");
        return true;
    }

    public function callUserRolController()
    {
        $this->selectRol();
        $this->controller -> index();
        return true;
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