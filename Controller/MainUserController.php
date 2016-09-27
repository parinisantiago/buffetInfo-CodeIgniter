<?php
//controlador para usuarios anónimos
class MainUserController extends Controller
{
    private $model;
    private $user;

    public function __construct(){

        parent::__contruct();
        $this->model = new MainUserModel();

    }

    //carga el index para usuarios no logueados
    public function init(){

        Session::init();
        $this->dispatcher->render("Main/MainTemplate.twig");

    }

    public function login(){


        //if( ! isset($_POST['submit']) ) throw new Exception("Apreta el botón de logeo macho");

        if (isset ($_POST['username'])) $user = $_POST['username'];
        else throw new Exception("No se ha ingresado ningun usuario");

        if (isset ($_POST['pass'])) $pass = $_POST['pass'];
        else throw new Exception("No se ha ingresado ninguna contraseña");
        
        if ( ! $this->model-> userExist($user)) throw new Exception("El usuario no existe");

        elseif( ! $this->model->passDontMissmatch($pass)) throw new Exception("Contraseña incorrecta");
        
        else
        {
            die();
            $this -> user = $this -> model -> getUser($user, $pass);
            $this.setSession();

            switch ( $this -> user -> rol ){



            }

        }
    }

    private function setSession()
    {

        Session::setValue( $this -> user -> username, 'username');
        Session::setValue( $this -> user -> rol, 'rol');

    }


}

?>