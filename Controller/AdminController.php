<?php
require_once 'Controller/BackendController.php';
class AdminController extends BackendController{
    
    public function getPermission(){
        Session::init();
        return strcmp(Session::getValue('rol'), 'admin');
    }

    public function setMensajeError($error){
        $this->dispatcher->mensajeError = $error;
        $this->index();
    }

    /* usuarios */
   
    public function abmUsuario()
    {
            $this->dispatcher->users = $this->model->getAllUser();
            $this->dispatcher->render('Backend/abmUsuarios.twig');
    }

    public function usuarioAM(){

            $this->validarUsuario(); //realiza validaciones mediante expresiones regulares
            if (! isset($_POST['idUsuario'])) $this->model->addUser($_POST['nombreUsuario'], $_POST['nombre'], $_POST['apellido'],$_POST['pass'], $_POST['dni'], $_POST['email'], $_POST['telefono'],$_POST['rol']);

            else if (Session::getValue('modUserId') == $_POST['idUsuario']) $this->model->modUser($_POST['idUsuario'],$_POST['nombreUsuario'], $_POST['nombre'], $_POST['apellido'],$_POST['pass'], $_POST['dni'], $_POST['email'],$_POST['telefono'], $_POST['rol']); //si es el usaurio guardado, lo modifica
            else throw new Exception('El id de usuario se vio modificado durante la operacion');
            $this->abmUsuario();

    }
    
    public function eliminarUsuario()
    {

            if (!isset($_POST['submitButton'])) throw new Exception("Apreta el botón de eliminar macho");

            if (!isset($_POST['idUsuario'])) throw new Exception("Faltan datos para poder eliminar el usuario");
            else $idUsuario = $_POST['idUsuario'];

            $this->dispatcher->user = $this->model->deleteUser($idUsuario);

            $this->abmUsuario();

    }
    
    public function registroUsuario()
    {
        $this->dispatcher->rols = $this->rolModel->getAllRols();
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
    
    private function validarUsuario(){

        if (!isset($_POST['submitButton'])) throw new Exception("Apreta el botón de eliminar macho");

        if (! isset($_POST['nombreUsuario'])) throw new Exception('Falta escribir el nombreUsuarip');
        else{
            validarString($_POST['nombreUsuario']);
        }
        if (! isset($_POST['nombre'])) throw new Exception('Falta escribir el nombre');
        else{
            validarStringEspeciales($_POST['nombre']);
        }
        if (! isset($_POST['pass'])) throw new Exception('Falta escribir la contraseaña');
        else{
            validarString($_POST['pass']);
        }
        if (! isset($_POST['apellido'])) throw new Exception('Falta escribir el apellido');
        else{
            validarStringEspeciales($_POST['apellido']);
        }
        if (! isset($_POST['dni'])) throw new Exception('Falta escribir el dni');
        else{
            validarNumero($_POST['dni']);
        }
        if (! isset($_POST['email'])) throw new Exception('Falta escribir el email');
        else{
            validarMail($_POST['email']);
        }
        if (! isset($_POST['telefono'])) throw new Exception('Falta escribir el telefono');
        else{
            validarNumeros($_POST['telefono']);
        }
        if (!isset($_POST['rol'])) throw new Exception("Falta el rol");
        elseif (! $this->rolModel->getRolById($_POST['rol'])) throw new Exception('Rol invalido');
    }
  
    /* ---Configuracion--- */

    public function configuracionSitio(){
        $this->dispatcher->render("Backend/ConfiguracionTemplate.twig");
    }

    public function changeConf(){
        $this->validarConf();
        if (isset($_POST['habilitado'])) echo 'bien';
    }

    private function validarConf(){
        var_dump($_POST);
        if (! isset($_POST['submitButton'])) throw new Exception('Apreta el boton de modificar macho');
        if (! isset($_POST['titulo'])) throw new Exception('Falta escribir el titulo');
        elseif (! preg_match("/^[a-zA-Z0-9 ]+$/", $_POST['titulo'])) throw new Exception('Valor del titulo no valido');
        if (! isset($_POST['descripcion'])) throw new Exception('Falta escribir el descripcion');
        elseif (! preg_match("/^[a-zA-Z0-9 .;,]+$/", $_POST['descripcion'])) throw new Exception('Valor de la  descripcion no valido');
        if (! isset($_POST['mensaje'])) throw new Exception('Falta escribir el mensaje');
        elseif (! preg_match("/^[a-zA-Z]+$/", $_POST['mensaje'])) throw new Exception('Valor del mensaje no valido');
        if (! isset($_POST['lista'])) throw new Exception('Falta escribir el numero de la lista');
        elseif (! preg_match("/^[0-9]+$/", $_POST['lista'])) throw new Exception('Valor del numero de la lista no valido');


    }
}
?>