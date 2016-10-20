<?php

//incluye lo mismo que en index.pxp.

include_once 'Utils/Const.php';
include_once 'Utils/Session.php';
include_once 'Controller/Controller.php';
foreach ( glob ( "Controller/*Controller.php" ) as $app ) {
include_once $app;
}
include_once 'twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register ();

foreach ( glob ( "Model/*Model.php" ) as $mod ) {
    include_once $mod;
}

//validaciones de seguridad

try {

    //se fija que se hayan pasado valores en el get

    if (! isset ( $_GET ['controller']  )) {
        throw new Exception ( "El parametro Class no esta setteado" );
    }
    if (! isset ( $_GET ['method'] )) {
        throw new Exception ( "El parametro method no esta setteado" );
    }

    $classController = $_GET ['controller'];
    $method = $_GET ['method'];

    //se fija que los valores pasados en el get sean valios

    if (! class_exists ( $classController )) {
        throw new Exception ( "No existe la clase" );
    }
    if (! method_exists ( $classController, $method )) {
        throw new Exception ( "No existe ese metodo en la clase" );
    }


    $controller = new $classController ();

    //Se fija si el usuario que inicio sesion tiene permisos para acceder a las funciones del controlador
    if (! $controller->getPermission ()) {
        throw new Exception ( "el usuario no tiene permisos" );
    }

    
    $controller->$method (); // si todo funciona llama al método 

   

} catch ( Exception $e ) {  //si exise algún error redirecciona al index.
    

        if( $e instanceof valException){
            throw $e;
        }

        $mainController = new MainUserController ();
        $mainController->initError($e->getMessage());
}


?>