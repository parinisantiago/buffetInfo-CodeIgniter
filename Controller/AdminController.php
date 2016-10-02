<?php
require_once 'Controller/Controller.php';
class AdminController extends Controller{

	public $model;

	public function __construct(){
		parent::__contruct();
    }

	public function init(){
		$this->dispatcher->render("Backend/adminIndexTemplate.twig");
	}

	public function abmUsuario(){

	}

	public function productos(){
		$this->dispatcher->render("Backend/ProductosTemplate.twig");
	}

	public function productosA(){
		
	}
}
?>