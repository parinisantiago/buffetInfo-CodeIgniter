<?php

require_once 'Controller/BackendController.php';
class AdminController extends BackendController{
    
    public function getPermission()
    {
        Session::init();
        return (Session::getValue('rol') == '0');
    }

    public function setMensajeError($error){
        $this->dispatcher->mensajeError = $error;
        $this->index();
    }

    /* usuarios */
   
    public function abmUsuario()
    {

            $this->paginaCorrecta($this->model->totalUsuario());
            $this->dispatcher->users = $this->model->getAllUser($this->conf->getConfiguracion()->cantPagina,$_GET['offset']);
            $this->dispatcher->pag = $_GET['pag'];
            $this->dispatcher->render('Backend/abmUsuarios.twig');
    }

    public function insertUsuario(){

        if ($this->model->userExist($_POST['nombreUsuario'])) throw new Exception("El usuario ya existe");
        $this->model->addUser($_POST['nombreUsuario'], $_POST['nombre'], $_POST['apellido'],$_POST['pass'], $_POST['dni'], $_POST['email'], $_POST['telefono'],$_POST['rol'], $_POST['ubicacion']);
    }



    public function usuarioAM()
    {

            $this->validarUsuario(); //realiza validaciones mediante expresiones regulares

            if (! isset($_POST['idUsuario'])) $this->insertUsuario();
            else if ((Session::getValue('modUserId') == $_POST['idUsuario']) &&  (  $this->model->userExist($_POST['nombreUsuario'])->usuario == $_POST['nombreUsuario'])) $this->model->modUser($_POST['idUsuario'],$_POST['nombreUsuario'], $_POST['nombre'], $_POST['apellido'],$_POST['pass'], $_POST['dni'], $_POST['email'],$_POST['telefono'], $_POST['rol'], $_POST['ubicacion']); //si es el usaurio guardado, lo modifica
            else throw new Exception('El id de usuario se vio modificado durante la operacion');
            $_GET['pag'] = 0;
            $this->abmUsuario();

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
        if (! isset($_POST['submitButton'])) throw new Exception('Apreta el boton de modificar macho');
        if (! isset($_POST['idUsuario'])) throw new Exception('Como vas a modificar un usuario sin ID?');
        if (! $this->model->getUserById($_POST['idUsuario'])) throw new Exception('Id invalido');
        Session::setValue($this->model->getUserById($_POST['idUsuario'])->idUsuario, 'modUserId'); //se guarda el usuario a modificar, para validar que no se cambie el valor del input del id.
        $this->dispatcher->user = $this->model->getUserById($_POST['idUsuario']);
        $this->registroUsuario();
    }
    
    private function validarUsuario()
    {

        $this->validator->varSet($_POST['submitButton'], "apreta el boton de sumbit");
        $this->validator->validarString( $_POST['nombreUsuario'], 'Error en nombreUsuarip', 15 );
        $this->validator->validarStringEspeciales( $_POST['nombre'], 'Error en nombre', 25 );
        $this->validator->validarString( $_POST['pass'], 'Error en contraseña', 15 );
        $this->validator->validarStringEspeciales( $_POST['apellido'], 'Error en apellido', 25 );
        $this->validator->validarNumeros( $_POST['dni'], 'Error en dni', 8 );
        $this->validator->validarMail( $_POST['email'], 'Error en email', 25 );
        $this->validator->validarNumeros( $_POST['telefono'],'Error en telefono', 25 );
        $this->validator->varSet($_POST['rol'], "envia un rol");
        if ( $_POST['rol'] != '2' ) $_POST['ubicacion'] = '0';


    }
  
    /* ---Configuracion--- */



    public function configuracionSitio()
    {
        $this->dispatcher->render("Backend/ConfiguracionTemplate.twig");
    }

    public function changeConf()
    {
        $this->validarConf();
        $this->conf->updateConf($_POST);
        $this->dispatcher->config = $this->conf->getConfiguracion();
        $this->dispatcher->render("Backend/ConfiguracionTemplate.twig");
    }

    private function validarConf()
    {
        if (! isset($_POST['submitButton'])) throw new Exception('Apreta el boton de modificar macho');
        if (! isset($_POST['titulo'])) throw new Exception('Falta escribir el titulo');
        elseif (! preg_match("/^[a-zA-Z0-9 ]+$/", $_POST['titulo'])) throw new Exception('Valor del titulo no valido');
        if (! isset($_POST['descripcion'])) throw new Exception('Falta escribir el descripcion');
        elseif (! preg_match("/^[a-zA-Z0-9 ;.,àáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹ]+$/", $_POST['descripcion'])) throw new Exception('Valor de la  descripcion no valido');
        if (! isset($_POST['mensaje'])) throw new Exception('Falta escribir el mensaje');
        elseif (! preg_match("/^[a-zA-Z ]+$/", $_POST['mensaje'])) throw new Exception('Valor del mensaje no valido');
        if (! isset($_POST['lista'])) throw new Exception('Falta escribir el numero de la lista');
        elseif (! preg_match("/^[0-9]+$/", $_POST['lista'])) throw new Exception('Valor del numero de la lista no valido');

    }
}
?>