<?php


class ConfigController extends Controller
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