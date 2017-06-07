<?php
require_once 'Controller.php';
include_once dirname(__DIR__).'/models/TelegramModel.php';

class MenuController extends Controller{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('MenuModel');
        $this->load->model('ProductosModel');
        $this->load->model('TelegramModel');
    }

    public function getPermission()
    {
        Session::init();
        $rol = Session::getValue('rol');
        return (($rol == '0') || ($rol == '1'));
    }

    public function menu()
    {
        /*deberia mostrar los menu para el dia que llegue como parametro
         * si no hay parametros muestra el de hoy
         * tambien numeros de paginas
         * agregar un marco de color sobre la fecha seleccionada en el calendario
         */

        if($this->permissions())
        {
            $date = date('Y-m-d');
            $this->paginaCorrecta($this->MenuModel->totalMenu());
            $this->addData('menu', $this->MenuModel->getMenuByDia($this->conf->getConfiguracion()->cantPagina, $_GET['offset'], $date));
            if (!isset($this->data['menu'][1])) $this->data['menu'][1] = NULL;
            $this->addData('datos', $this->data['menu'][1]);
            $this->addData('pag', $_GET['pag']);
            $this->addData('method', "menu");
            $this->token();
            $this->display("MenuListarTemplate.twig");
        }
    }

    public function menuDia()
    {
        /* muestra el menu para un dia en particular, le mande un try catch por las dudas de que pasen cualquier cosa por get */
        if($this->permissions())
        {
            try
            {
                $this->token();
                if (!isset($_POST['fecha']))
                {
                    if (isset($_GET['fecha'])) $_POST['fecha'] = $_GET['fecha'];
                    else $_POST['fecha'] = date("Y-m-d");
                }
                $this->validator->validarFecha($_POST['fecha'], "Fecha no valida");
                $this->paginaCorrecta($this->MenuModel->totalProd($_POST['fecha']));


                $this->addData('menu', $this->MenuModel->getMenuByDia($this->conf->getConfiguracion()->cantPagina, $_GET['offset'], $_POST['fecha']));
                //$this->dispatcher->productos = $this->menuModel->getProductos($this->conf->getConfiguracion()->cantPagina,$_GET['offset'])

                if (!isset($this->data['menu'][0])) $this->data['menu'][0] = NULL;
                $this->addData('datos', $this->data['menu'][0]);
                $this->addData('fecha', $_POST['fecha']);
                $this->addData('pag', $_GET['pag']);
                $this->addData('method', "menuDia");
                $this->display("MenuListarTemplate.twig");
            }
            catch (Exception $e)
            {
                $this->addData('mensajeError', $e->getMessage());
                $this->menu();
            }
        }
    }

    public function menuAM()
    {
        /*falta agregar que pregunte si le envian un m por parametro y que lo setee */
        if($this->permissions())
        {
            $this->addData('producto', $this->ProductosModel->getAllProducto(99, 0));
            $this->token();
            $this->display("MenuAMTemplate.twig");
        }
    }

    public function menuAMPOST()
    {
        /*valida que los parametros sean correctos y despues se fija si ya existe el menu*/
        if($this->permissions())
        {
            try
            {
                $this->validateMenu($_POST);
                if (!isset($_POST['idMenu']))
                {
                    $this->agregarMenu($_POST);
                    //$this->notificarTelegram($_POST['fecha']);
                }
                else $this->modificarMenu($_POST);
                if (!isset($_POST['un valor']))
                {
                    $_GET['pag'] = 0;
                    $this->menu();
                }
            }
            catch (Exception $e)
            {
                /* falta que setee post */
                $_POST['un valor'] = true;
                $this->addData('mensajeError', $e->getMessage());
                $_GET['pag'] = 0;

                if (!isset($_POST['idMenu'])) $this->menuAM();
                else if (isset($_POST['idMenu']) && isset($_POST['fecha']))
                {
                    $menu = $this->MenuModel->idMenu($_POST['idMenu']);
                    $_POST['fecha'] = $menu->fecha;
                    $this->menuAMMod();
                }
                else $this->menu();

            }
        }
    }

    public function agregarMenu($menu)
    {
        if($this->permissions())
        {
            $image = basename($_FILES['foto']['name']);
            $fecha = $_POST['fecha'];
            try
            {
                if (!isset($_POST['tokenScrf'])) throw new Exception("no hay un token de validación");
                if (!$this->tokenIsValid($_POST['tokenScrf'])) throw new Exception("el token no es valido");

               // if (!move_uploaded_file($_FILES['foto']['tmp_name'], files . $image)) throw new Exception("no se pudo guardar la imagen del menu");

                if ($this->MenuModel->getMenuDia($fecha)) throw new Exception("Ya existe un menu para esta fecha");

                $idMenu = $this->MenuModel->insertarMenu($fecha, $image);

                foreach ($menu['selectProdMult'] as $prod)
                {
                    $this->MenuModel->insertarProd($idMenu, $prod);
                }
                $this->addData('fecha', $fecha);
            }
            catch (Exception $e)
            {
                $_POST['un valor'] = true;
                $this->addData('mensajeError', $e->getMessage());
                $this->addData('valores', $_POST);
                $this->menuAM();
            }
        }
    }

    public function menuAMMod()
    {
        if($this->permissions())
        {
            try
            {
                if (!isset($_POST['tokenScrf'])) throw new Exception("no hay un token de validación");
                if (!$this->tokenIsValid($_POST['tokenScrf'])) throw new Exception("el token no es valido");
                $this->token();
                if (!isset($_POST['fecha']))
                {
                    $this->validator->varSet($_GET['fecha'], "no hay fecha");
                    $fecha = $_GET['fecha'];
                }
                else $fecha = $_POST['fecha'];

                if (!$this->MenuModel->getMenuDia($fecha)) throw new Exception("No existe el menu para modificar");
                $this->addData('menu', $this->MenuModel->getMenuByDia(99, 0, $fecha));
                $this->addData('producto', $this->MenuModel->getProdNotInMenu($this->data['menu']['0']->idMenu));
                $this->addData('datos', $this->data['menu'][0]);
                $this->display("MenuAMTemplate.twig");

            }
            catch (Exception $e)
            {
                $this->addData('mensajeError', $e->getMessage());
                $this->display("MenuListarTemplate.twig");
            }
        }
    }

    public function modificarMenu($menu)
    {
        if($this->permissions())
        {
            $fecha = $_POST['fecha'];
            $idMenu = $_POST['idMenu'];
            $menu = $this->MenuModel->getMenuDia($fecha);
            $miMenu = $this->MenuModel->idMenu($idMenu);
            //validaciones
            try
            {
                if (!$miMenu) throw new Exception("El menu que desea modificar no existe");
                if (($menu && $menu->idMenu != $idMenu)) throw new Exception("La fecha elegida ya pertenece a otro menu");
                if (!isset($_POST['habilitado'])) throw new Exception("No hay una habilitacion/deshabilitacion");
                if ($_POST['habilitado'] == "true") $habilitado = 0;
                elseif ($_POST['habilitado'] == "false") $habilitado = 1;
                else throw new Exception("error en habilitado/deshabilitado");
                if (!isset($_POST['tokenScrf'])) throw new Exception("no hay un token de validación");
                if (!$this->tokenIsValid($_POST['tokenScrf'])) throw new Exception("el token no es valido");
                if (($_FILES['foto']['size'] == 0)) $foto = $_POST['foto2'];

                else
                {
                    if (!($_FILES['foto']['type'] == 'image/png' || $_FILES['foto']['type'] == 'image/jpg' || $_FILES['foto']['type'] = 'image/jpge')) throw new valException("el formato de la imagen no es valido");
                    else
                    {
                        $foto = basename($_FILES['foto']['name']);
                        if (!move_uploaded_file($_FILES['foto']['tmp_name'], files . $foto)) throw new Exception("no se pudo guardar la imagen del menu");
                    }
                }

                foreach ($_POST['selectProdMult'] as $prod)
                {
                    if (!$this->ProductosModel->searchIdProducto($prod)) throw new Exception("Uno de los productos seleccionados no es valido");
                }
                $this->MenuModel->eliminarMenu($idMenu);

                $idMenu2 = $this->MenuModel->insertarMenu2($fecha, $foto, $habilitado);
                foreach ($_POST['selectProdMult'] as $prod)
                {
                    $this->MenuModel->insertarProd($idMenu2, $prod);

                }
                $_GET['pag'] = 0;
                $_GET['fecha'] = $fecha;
                $this->paginaCorrecta($this->MenuModel->totalMenu());
                $this->addData('menu', $this->MenuModel->getMenuByDia($this->conf->getConfiguracion()->cantPagina, $_GET['offset'], $_GET['fecha']));
                //$this->dispatcher->productos = $this->menuModel->getProductos($this->conf->getConfiguracion()->cantPagina,$_GET['offset'])
                if (!isset($this->data['menu'][1])) $this->data['menu'][1] = NULL;
                $this->addData('datos', $this->data['menu'][1]);
                $this->addData('fecha', $_GET['fecha']);
                $this->addData('pag', $_GET['pag']);
                $this->addData('method', "menuDia");
            }
            catch (Exception $e)
            {
                $_POST['un valor'] = true;
                $this->addData('mensajeError', $e->getMessage());
                $menu = $this->MenuModel->idMenu($_POST['idMenu']);
                $_POST['fecha'] = $menu->fecha;
                $this->menuAMMod();
            }
        }
    }

    public function menuEliminar()
    {
        try{
            if (! isset($_POST['tokenScrf'])) throw new Exception("no hay un token de validación");
            if (! $this->tokenIsValid($_POST['tokenScrf'])) throw new Exception("el token no es valido");
            $this->addData('menuModel', $this ->MenuModel->eliminarMenu($_POST["idMenu"]));
            $_GET['pag'] = 0;
            $this->addData('method', "menu");
            $this->menu();
        } catch (Exception $e){
            $this->menu();
        }

    }

    public function validateMenu($menu)
    {
        if(! ($_FILES['foto']['type'] == 'image/png' ||  $_FILES['foto']['type'] == 'image/jpg' || $_FILES['foto']['type'] = 'image/jpge')) throw new valException("el formato de la imagen no es valido");

        $this->validator->validarFecha($menu['fecha'], "Fecha no valida");
        if(!isset($menu['selectProdMult'])) $menu['selectProdMult'] = NULL;
        $this->validator->varSet($menu['selectProdMult'], "Debe seleccionar por lo menos un producto para el menu");

        foreach ($menu['selectProdMult'] as $prod)
        {
            if (! $this->ProductosModel->searchIdProducto($prod)) throw new Exception("Uno de los productos seleccionados no es valido");
        }

    }

    public function paginaCorrecta($total)
    {
        if (! isset($_GET['pag'])) throw new Exception('Error:No hay una página que mostrar');
        elseif ($total->total <= $_GET['pag'] *  $this->conf->getConfiguracion()->cantPagina){  $_GET['pag'] = 0; $_GET['offset'] = 0;}
        else $_GET['offset'] = $this->conf->getConfiguracion()->cantPagina * $_GET['pag'];
        if ($_GET['offset'] < 0) $_GET['offset'] = 0;
        $_GET['offset'] .= "";
    }
    
    public function notificarTelegram ($fecha)
    {
        $users= $this->TelegramModel->getAll();
        foreach ($users as $idUser)
        {
            $returnArray = true;
            $rawData = file_get_contents('php://input');
            $response = json_decode($rawData, $returnArray);
            $regExp = '#^(\/[a-zA-Z0-9\/]+?)(\ .*?)$#i';
            $tmp = preg_match($regExp, $response['message']['text'], $aResults);
            if (isset($aResults[1]))
            {
                $cmd = trim($aResults[1]);
                $cmd_params = trim($aResults[2]);
            }
            else
            {
                $cmd = trim($response['message']['text']);
                $cmd_params = '';
            }
            $msg = array();
            $msg['chat_id'] =  $idUser->idUsuario;
            $msg['text'] = null;
            $msg['disable_web_page_preview'] = true;
            $msg['reply_to_message_id'] = $response['message']['message_id'];
            $msg['reply_markup'] = null;
            $msg['text'] = 'Hola ' . $response['message']['from']['first_name'] . PHP_EOL;
            $msg['text'] = 'Se a añadido un menu para el dia ' . $fecha . PHP_EOL;
            $menu = $this->MenuModel->getMenuByDia(100,0,$fecha);
            foreach ($menu as $producto)
            {
                $msg['text'] .= $producto->nombre;
                $msg['text'] .=', ';
                $msg['text'] .= $producto->descripcion;
                $msg['text'] .=', a un precio de $';
                $msg['text'] .= $producto->precioVentaUnitario;
                $msg['text'] .='.' . PHP_EOL;
            }
            $msg['reply_to_message_id'] = null;
            $url = 'https://api.telegram.org/bot383650181:AAGPzLovM1KS6wRjtlrw7aQaD4RplEUTrto/sendMessage';
            $options = array(
                'http' => array(
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method' => 'POST',
                    'content' => http_build_query($msg)
                )
            );
            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
        }
    }
}