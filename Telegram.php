<?php
    require_once 'Utils/Const.php';
    foreach ( glob ( "Controller/*Controller.php" ) as $app ) {
        require_once $app;
    }
    foreach ( glob ( "Model/*Model.php" ) as $mod ) {
        require_once $mod;
    }
    $controller=new TelegramController;
    $controller->responder();
?>