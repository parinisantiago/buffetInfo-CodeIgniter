<?php
require_once 'Controller/Controller.php';

class MenuController extends Controller{
    public $menuModel;

    public function __construct(){
        parent::__contruct();
        $this->menuModel= new MenuModel();
    }
    public function menu(){
        /*deberia mostrar los menu para el dia que llegue como parametro
         * si no hay parametros muestra el de hoy
         * tambien numeros de paginas
         * agregar un marco de color sobre la fecha seleccionada en el calendario
         */
        $this->paginaCorrecta($this->menuModel->totalMenu());
        $this->dispatcher->menu = $this->menuModel->getAllMenu($this->conf->getConfiguracion()->cantPagina,$_GET['offset']);
        $this->dispatcher->pag = $_GET['pag'];
        $this->dispatcher->method = "menu";
        $this->dispatcher->render("Backend/calendarioTemplate.twig");
    }
    public function menuAM(){
        echo"POLIMORFISAR FUNESTO";
    }
    public function menuEliminar(){
        echo"DESINTEGRAR";
    }
}