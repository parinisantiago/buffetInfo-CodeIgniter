<?php

/**
 * Created by PhpStorm.
 * User: piturro
 * Date: 29/10/16
 * Time: 19:47
 */
class UserController extends Controller
{
    public $model;
    public $rolModel;
    public $productoModel;
    public $categoriaModel;
    public $ventaModel;
    public $compraModel;
    public $menuModel;

    public function __construct(){
        parent::__contruct();
        $this->model = new MainUserModel();
        $this->rolModel = new RolModel();
        $this->productoModel = new ProductosModel();
        $this->categoriaModel = new CategoriaModel();
        $this->ventaModel = new VentaModel();
        $this->compraModel= new CompraModel();
        $this->menuModel= new MenuModel();
    }

    public function index(){
        $this->dispatcher->render("Backend/IndexTemplate.twig");
    }

    public function setMensajeError($error){
        $this->dispatcher->mensajeError = $error;
        $this->index();
    }

    public function getPermission()
    {
        Session::init();
        return (Session::getValue('rol') == '0');
    }

    public function abmUsuario()
    {
        $this->paginaCorrecta($this->model->totalUsuario());
        $this->dispatcher->users = $this->model->getAllUser($this->conf->getConfiguracion()->cantPagina,$_GET['offset']);
        $this->dispatcher->pag = $_GET['pag'];
        $this->dispatcher->render('Backend/abmUsuarios.twig');
    }

    public function insertUsuario(){

        if ($this->model->userExist($_POST['nombreUsuario'])) throw new Exception("El usuario ya existe");
        $this->model->addUser($_POST['usuario'], $_POST['nombre'], $_POST['apellido'],$_POST['clave'], $_POST['documento'], $_POST['email'], $_POST['telefono'],$_POST['idRol'], $_POST['idUbicacion']);
    }



    public function usuarioAM()
    {
        try{
            $this->validarUsuario(); //realiza validaciones mediante expresiones regulares

            if (!isset($_POST['idUsuario'])) $this->insertUsuario();
            else if ((Session::getValue('modUserId') == $_POST['idUsuario']) && ($this->model->userExistInDB($_POST['usuario'])->usuario == $_POST['usuario'])) $this->model->modUser($_POST['idUsuario'], $_POST['usuario'], $_POST['nombre'], $_POST['apellido'], $_POST['clave'], $_POST['documento'], $_POST['email'], $_POST['telefono'], $_POST['idRol'], $_POST['idUbicacion'], $_POST['habilitado']); //si es el usaurio guardado, lo modifica
            else throw new Exception('Error: El id de usuario se vio modificado durante la operacion');
            $_GET['pag'] = 0;
            $this->abmUsuario();
        }  catch (valException $e){
            $this->dispatcher->user = $_POST;
            $this->dispatcher->mensajeError = $e->getMessage();
            $this->registroUsuario();
        }
    }

    public function eliminarUsuario()
    {

        if (!isset($_POST['submitButton'])) throw new Exception("Apreta el botón de eliminar macho");

        if (!isset($_POST['idUsuario'])) throw new Exception("Faltan datos para poder eliminar el usuario");
        else $idUsuario = $_POST['idUsuario'];

        $this->model->deleteUser($idUsuario);

        $_GET['pag'] = 0;
        $this->abmUsuario();

    }

    public function registroUsuario()
    {
        $this->dispatcher->ubicacion = $this->rolModel->getAllUbicacion();
        $this->dispatcher->rols = $this->rolModel->getAllRols(); //para poder crear el listado de roles
        $this->dispatcher->render("Backend/registroUsuariosTemplate.twig");
    }

    public function modificarUsuario()
    {
        try{
            if (! isset($_POST['submitButton'])) throw new Exception('Apreta el boton de modificar macho');
            if (! isset($_POST['idUsuario'])) throw new Exception('Como vas a modificar un usuario sin ID?');
            if (! $this->model->getUserById($_POST['idUsuario'])) throw new Exception('Id invalido');
            Session::setValue($this->model->getUserById($_POST['idUsuario'])->idUsuario, 'modUserId'); //se guarda el usuario a modificar, para validar que no se cambie el valor del input del id.
            $this->dispatcher->user = $this->model->getUserById($_POST['idUsuario']);
            $this->registroUsuario();
        } catch (valException $e){
            $this->dispatcher->mensajeError = $e->getMessage();
            $this->registroUsuario();
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
        if ($_POST['idRol'] == 2 and $_POST['idUbicacion']=='0') throw  new valException("Los usuarios web deben poseer una ubicación");

    }

    public function paginaCorrecta($total){
        if (! isset($_GET['pag'])) throw new Exception('Error:No hay una página que mostrar');
        elseif ($total->total <= $_GET['pag'] *  $this->conf->getConfiguracion()->cantPagina){  $_GET['pag'] = 0; $_GET['offset'] = 0;}
        else $_GET['offset'] = $this->conf->getConfiguracion()->cantPagina * $_GET['pag'];
        if ($_GET['offset'] < 0) $_GET['offset'] = 0;
        $_GET['offset'] .= "";
    }

}