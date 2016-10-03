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

	public function productos()
	{
		$this->dispatcher->render("Backend/ProductosTemplate.twig");
	}

	public function productosA()
	{

	}

	public function eliminarUsuario()
	{

		if (!isset($_POST['submitButton'])) throw new Exception("Apreta el botón de envío macho");

		if (!isset($_POST['idUsuario'])) throw new Exception("Faltan datos para poder eliminar el usuario");
		else $idUsuario = $_POST['idUsuario'];

		$this->dispatcher->user = $this->model->deleteUser($idUsuario);

		$this->abmUsuario();

	}

}

?>