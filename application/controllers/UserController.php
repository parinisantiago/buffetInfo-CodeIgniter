<?php
include_once('Controller.php');
include_once('MainController.php');
include_once(dirname(__DIR__)."/models/UserModel.php");
include_once(dirname(__DIR__)."/models/RolModel.php");

class UserController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('UserModel');
        $this->load->model('RolModel');
    }

    public function index()
    {
        if($this->permissions()) $this->display("IndexTemplate.twig");
    }

    public function setMensajeError($error)
    {
        $this->addData('mensajeError', $error);
        $this->index();
    }

    public function getPermission()
    {
        Session::init();
        return (Session::getValue('rol') == '0');
    }

    public function abmUsuario()
    {
        if($this->permissions())
        {
            $this->paginaCorrecta($this->UserModel->totalUsuario());
            $this->addData('users', $this->UserModel->getAllUser($this->conf->getConfiguracion()->cantPagina, $_GET['offset']));
            $this->addData('pag', $_GET['pag']);
            $this->display('abmUsuarios.twig');
        }
    }

    public function insertUsuario()
    {
        if($this->permissions())
        {
            if ($this->UserModel->userExist($_POST['usuario'])) throw new Exception("El usuario ya existe");
            $this->UserModel->addUser($_POST['usuario'], $_POST['nombre'], $_POST['apellido'], $_POST['clave'], $_POST['documento'], $_POST['email'], $_POST['telefono'], $_POST['idRol'], $_POST['idUbicacion']);
        }
    }



    public function usuarioAM()
    {
        if($this->permissions())
        {
            try
            {
                $this->validarUsuario(); //realiza validaciones mediante expresiones regulares
                if (!isset($_POST['idUsuario'])) $this->insertUsuario();
                else if(isset($this->UserModel->userExistInDB($_POST['usuario'])->usuario)){
                    if ($this->UserModel->userExistInDB($_POST['usuario'])->usuario == $_POST['usuario']) $this->UserModel->modUser($_POST['idUsuario'], $_POST['usuario'], $_POST['nombre'], $_POST['apellido'], $_POST['clave'], $_POST['documento'], $_POST['email'], $_POST['telefono'], $_POST['idRol'], $_POST['idUbicacion'], $_POST['habilitado']); //si es el usaurio guardado, lo modifica
                } else $this->UserModel->modUser($_POST['idUsuario'], $_POST['usuario'], $_POST['nombre'], $_POST['apellido'], $_POST['clave'], $_POST['documento'], $_POST['email'], $_POST['telefono'], $_POST['idRol'], $_POST['idUbicacion'], $_POST['habilitado']);
            else throw new Exception('Ya existe el usuario');
                $_GET['pag'] = 0;
                $this->abmUsuario();
            }
            catch (Exception $e)
            {
                $this->addData('user', $_POST);
                $this->addData('mensajeError', $e->getMessage());
                $this->registroUsuario();
            }
        }
    }

    public function eliminarUsuario()
    {
        if($this->permissions())
        {
            try
            {
                if (!isset($_POST['submitButton'])) throw new Exception("Apreta el botón de eliminar macho");

                if (!isset($_POST['idUsuario'])) throw new Exception("Faltan datos para poder eliminar el usuario");
                else $idUsuario = $_POST['idUsuario'];

                $this->UserModel->deleteUser($idUsuario);

                $_GET['pag'] = 0;
                $this->abmUsuario();
            }
            catch (Exception $e)
            {
                $this->addData('mensajeError', $e->getMessage());
                $main = new MainController();
                $main->index();
            }
        }
    }

    public function registroUsuario()
    {
        if($this->permissions())
        {
            $this->addData('ubicacion', $this->RolModel->getAllUbicacion());
            $this->addData('rols', $this->RolModel->getAllRols()); //para poder crear el listado de roles
            $this->display("registroUsuariosTemplate.twig");
        }
    }

    public function modificarUsuario()
    {
        if($this->permissions())
        {
            try
            {
                if (!isset($_POST['submitButton'])) throw new Exception('Apreta el boton de modificar macho');
                if (!isset($_POST['idUsuario'])) throw new Exception('Como vas a modificar un usuario sin ID?');
                if (!$this->UserModel->getUserById($_POST['idUsuario'])) throw new Exception('Id invalido');
                Session::setValue($this->UserModel->getUserById($_POST['idUsuario'])->idUsuario, 'modUserId'); //se guarda el usuario a modificar, para validar que no se cambie el valor del input del id.
                $this->addData('user', $this->UserModel->getUserById($_POST['idUsuario']));
                $this->registroUsuario();
            }
            catch (Exception $e)
            {
                $this->addData('mensajeError', $e->getMessage());
                $this->registroUsuario();
            }
        }
    }

    private function validarUsuario()
    {
        $this->validator->varSet($_POST['submitButton'], "apreta el boton de sumbit");
        $this->validator->validarString( $_POST['usuario'], 'Error en nombreUsuarip', 15 );
        $this->validator->validarStringEspeciales( $_POST['nombre'], 'Error en nombre', 25 );
        $this->validator->validarString( $_POST['clave'], 'Error en contraseña', 15 );
        $this->validator->validarStringEspeciales( $_POST['apellido'], 'Error en apellido', 25 );
        $this->validator->validarNumeros( $_POST['documento'], 'Error en dni', 8 );
        $this->validator->validarMail( $_POST['email'], 'Error en email', 25 );
        $this->validator->validarNumeros( $_POST['telefono'],'Error en telefono', 25 );
        $this->validator->varSet($_POST['idRol'], "envia un rol");
        if ( $_POST['idRol'] != '2' ) $_POST['idUbicacion'] = '0';
        if ($_POST['idRol'] == 2 and $_POST['idUbicacion']=='0') throw  new Exception("Los usuarios web deben poseer una ubicación");
    }

    public function paginaCorrecta($total)
    {
        try
        {
        if (! isset($_GET['pag'])) throw new Exception('Error:No hay una página que mostrar');
        elseif ($total->total <= $_GET['pag'] *  $this->conf->getConfiguracion()->cantPagina){  $_GET['pag'] = 0; $_GET['offset'] = 0;}
        else $_GET['offset'] = $this->conf->getConfiguracion()->cantPagina * $_GET['pag'];
        if ($_GET['offset'] < 0) $_GET['offset'] = 0;
        $_GET['offset'] .= "";
        }
        catch (Exception $e)
        {
            $this->addData('mensajeError', $e->getMessage());
            $main = new MainController();
            $main->index();
        }
    }
}