<?php

require_once 'Controller.php';

class TelegramController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MenuModel');
        $this->load->model('ProductosModel()');
    }

    public function responder()
    {
        $returnArray = true;
        $rawData = file_get_contents('php://input');
        $response = json_decode($rawData, $returnArray);
        $id_del_chat = $response['message']['chat']['id'];
// Obtener comando (y sus posibles parametros)
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

//armado respuesta
        $msg = array();
        $msg['chat_id'] = $response['message']['chat']['id'];
        $msg['text'] = null;
        $msg['disable_web_page_preview'] = true;
        $msg['reply_to_message_id'] = $response['message']['message_id'];
        $msg['reply_markup'] = null;

        switch ($cmd)
        {
            case '/start':
                $msg['text'] = 'Hola ' . $response['message']['from']['first_name'] . PHP_EOL;
                $msg['text'] .= '¿Como puedo ayudarte? Puedes utilizar el comando /help';
                $msg['reply_to_message_id'] = null;
                break;
            case '/help':
                $msg['text'] = 'Los comandos disponibles son estos:' . PHP_EOL;
                $msg['text'] .= '/start Inicializa el bot' . PHP_EOL;
                $msg['text'] .= '/hoy Muestra el menú del día' . PHP_EOL;
                $msg['text'] .= '/maniana Muestra el menú de mañana' . PHP_EOL;
                $msg['text'] .= '/help Muestra esta ayuda' . PHP_EOL;
                $msg['reply_to_message_id'] = null;
                break;
            case '/hoy':
                $msg['text'] = 'Hola ';
                $menu = $this->MenuModel->getMenuToday();
                if (!isset($menu)) {
                    $msg['text'] = 'El menú del día es: ' . $menu["nombre"] . ' ' . $menu["descripcion"];
                } else {
                    $msg['text'] = 'No han planificado ningun menú para hoy';
                }
                $msg['reply_to_message_id'] = null;
                break;
            case '/maniana':
                $msg['text'] = 'Hola ';
                $today = getDate();
                $fecha = $today['year'] . "-" . $today['mon'] . "-" . ($today['mday'] + 1);
                $menu = $this->MenuModel->getMenuByDia($fecha);
                if (!isset($menu)) {
                    $msg['text'] = 'El menú de mañana es: ' . $menu["nombre"] . ' ' . $menu["descripcion"];
                } else {
                    $msg['text'] = 'No han planificado ningun menú para mañana :(';
                }
                $msg['reply_to_message_id'] = null;
                break;
                default:
                $msg['text'] = 'Lo siento, no es un comando válido.' . PHP_EOL;
                $msg['text'] .= 'Prueba /help para ver la lista de comandos disponibles';
                break;
        }
        //enviando respuesta
        //original:
        //https://api.telegram.org/bot297573593:AAEL7cFsdN55670XjVr89BMu-XBiEzw3ojw/setWebhook?url=https://grupo64.proyecto2016.linti.unlp.edu.ar/pruebasbot.php
        $url = 'https://api.telegram.org/bot297573593:AAEL7cFsdN55670XjVr89BMu-XBiEzw3ojw/sendMessage';

        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($msg)
            )
        );

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        exit(0);
    }

}

?>
