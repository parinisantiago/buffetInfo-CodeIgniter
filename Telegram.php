<?php
include_once 'Model/MenuModel.php';
require_once 'Utils/Const.php';

        $returnArray = true;
        $rawData = file_get_contents('php://input');
        $response = json_decode($rawData, $returnArray);
        $id_del_chat = $response['message']['chat']['id'];

// Obtener comando (y sus posibles parametros)
        $regExp = '#^(\/[a-zA-Z0-9\/]+?)(\ .*?)$#i';

        $tmp = preg_match($regExp, $response['message']['text'], $aResults);

        if (isset($aResults[1])) {
            $cmd = trim($aResults[1]);
            $cmd_params = trim($aResults[2]);
        } else {
            $cmd = trim($response['message']['text']);
            $cmd_params = '';
        }

//armado respuesta
        $menuModel = new MenuModel();
        $msg = array();
        $msg['chat_id'] = $response['message']['chat']['id'];
        $msg['text'] = null;
        $msg['disable_web_page_preview'] = true;
        $msg['reply_to_message_id'] = $response['message']['message_id'];
        $msg['reply_markup'] = null;

        switch ($cmd) {
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
                $msg['text'] .= '/suscribir Suscribe a este bot' . PHP_EOL;
                $msg['text'] .= '/help Muestra esta ayuda' . PHP_EOL;
                $msg['reply_to_message_id'] = null;
                break;
            case '/hoy':
                $msg['text'] = 'Hola los productos del menu del dia son: ';
                $menu = $menuModel->getMenuToday();
                if ($menu) {
                    foreach ($menu as $producto){
                        $msg['text'] .= $producto->nombre;
                         $msg['text'] .=', ';
                         $msg['text'] .= $producto->descripcion;
                          $msg['text'] .=', a un precio de $';
                          $msg['text'] .= $producto->precioVentaUnitario;
                          $msg['text'] .='.' . PHP_EOL;
                    }
                } else {
                    $msg['text'] = 'No han planificado ningun menú para hoy';
                }
                $msg['reply_to_message_id'] = null;
                break;
            case '/maniana':
                 $msg['text'] = 'Hola los productos del menu de mañana son:';
                 $tomorrow = date("Y-m-d", strtotime('tomorrow'));
                $menu = $menuModel->getMenuByDia(100,0,$tomorrow);
                if ($menu) {
                    foreach ($menu as $producto){
                        $msg['text'] .= $producto->nombre;
                         $msg['text'] .=', ';
                         $msg['text'] .= $producto->descripcion;
                          $msg['text'] .=', a un precio de $';
                          $msg['text'] .= $producto->precioVentaUnitario;
                          $msg['text'] .='.' . PHP_EOL;
                    }
                } else {
                    $msg['text'] = 'No han planificado ningun menú para mañana :(';
                }
                $msg['reply_to_message_id'] = null;
                break;
                default:
                $msg['text'] = 'Lo siento, no es un comando válido.' . PHP_EOL;
                $msg['text'] .= 'Prueba /help para ver la lista de comandos disponibles';
                break;
            case '/suscribir':    
                // Standalone
                $response = $telegram->setWebhook(['url' => 'https://api.telegram.org/bot297573593:AAEL7cFsdN55670XjVr89BMu-XBiEzw3ojw/webhook']);

                // Or if you are supplying a self-signed-certificate
                $response = $telegram->setWebhook([
                    'url' => 'https://api.telegram.org/bot297573593:AAEL7cFsdN55670XjVr89BMu-XBiEzw3ojw/webhook',
                    'certificate' => '/path/to/public_key_certificate.pub'
                ]);

                // Laravel - Setup a POST route.
                $response = Telegram::setWebhook(['url' => 'https://api.telegram.org/bot297573593:AAEL7cFsdN55670XjVr89BMu-XBiEzw3ojw/webhook']);

                // Or if you are supplying a self-signed-certificate
                $response = Telegram::setWebhook([
                    'url' => 'https://api.telegram.org/bot297573593:AAEL7cFsdN55670XjVr89BMu-XBiEzw3ojw/webhook',
                    'certificate' => '/path/to/public_key_certificate.pub'
                ]);
                // Standalone
                $updates = $telegram->getWebhookUpdates();

                // Laravel - Put this inside the POST route /<token>/webhook
                $updates = Telegram::getWebhookUpdates();
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
?>