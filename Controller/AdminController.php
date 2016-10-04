<?php
require_once 'Controller/Controller.php';
class AdminController extends Controller
{

    public $model;


    public function __construct()
    {
            parent::__contruct();
            $this->model = new MainUserModel();
    }


    public function getPermission()
    {
            Session::init();
            return strcmp(Session::getValue('rol'), 'admin');
    }

    public function init()
    {
            $this->dispatcher->render("Backend/adminIndexTemplate.twig");
    }

    public function abmUsuario()
    {
            $this->dispatcher->users = $this->model->getAllUser();
            $this->dispatcher->render('Backend/abmUsuarios.twig');
    }

    public function eliminarUsuario()
    {

            if (!isset($_POST['submitButton'])) throw new Exception("Apreta el botón de envío macho");

            if (!isset($_POST['idUsuario'])) throw new Exception("Faltan datos para poder eliminar el usuario");
            else $idUsuario = $_POST['idUsuario'];

            $this->dispatcher->user = $this->model->deleteUser($idUsuario);

            $this->abmUsuario();

    }
/*aiiiuuuuddaaaaa 
* que hago con el modelo? lo cambio en cada funcion?
*/
    public function productosListar(){
        $this->model = new ProductosModel();
        $this->dispatcher->producto =$this ->model->getAllProducto();
        $this->dispatcher->render("Backend/ProductosListarTemplate.twig");
    }
    public function productosAM(){
        $this->model = new ProductosModel();
        if ($_POST["idProducto"] != null){
            $this->dispatcher->producto =$this ->model->searchIdProducto($_POST["idProducto"]);
            // VER no le manda el id al parecer
        }
        $this->dispatcher->render("Backend/ProductosAMTemplate.twig");
    }
    public function productosE(){
     $this->dispatcher->render("Backend/ProductosAMTemplate.twig");
    }
}
?>