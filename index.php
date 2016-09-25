<?php

//carga constantes de conexion de bd y el encapsulamiento de las sessions
include_once 'Utils/Const.php';
include_once 'Utils/Session.php';
include_once 'twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register ();


//carga todos los controller para que no haya quilombo, tienen que terminar en Controller.php
foreach ( glob ( "Controller/*Controller.php" ) as $app ) {
    include_once $app;
}


//idem anterior
foreach ( glob ( "Model/*Model.php" ) as $mod ) {
    include_once $mod;
}

//carga el controlador principal, osea el controlador para los usuarios anónimos.
$mainController = new MainController();
$mainController->init();
?>