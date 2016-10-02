<?php
require_once 'Controller/Controller.php';
class AdminController extends Controller{
	public function __construct(){
		parent::__contruct();
    }

	public function init(){
		$this->dispatcher->render("Backend/ProductosTemplate.twig");

	}
}
?>