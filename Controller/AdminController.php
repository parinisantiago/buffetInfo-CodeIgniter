<?php
require_once 'Controller/Controller.php';
class AdminController extends Controller{

	public $model;

	public function __construct(){
		parent::__contruct();
		$this->model = new MainUserModel();
    }

	public function init(){
		$this->dispatcher->render("Backend/adminIndexTemplate.twig");
	}

	public function abmUsuario(){
		$this->dispatcher->users = $this->model->getAllUser();
		$this->dispatcher->render('Backend/abmUsuarios.twig');
	}

	public function productos(){
		$this->dispatcher->render("Backend/ProductosTemplate.twig");
	}

	public function productosA(){
		
	}
}
?>