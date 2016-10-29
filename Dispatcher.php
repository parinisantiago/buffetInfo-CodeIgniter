<?php

include_once 'twig/lib/Twig/Autoloader.php';

//Se le pasa la vista que querés instanciar y la renderisa


class Dispatcher
{
    
    //renderisa el template, se le pasan 3 argumentos en el controlodar, primero la vista, luego el header y luego el footer.

    public function render( $template ){
        $this -> loadByTwig("Template/".$template);
        
    }

    //renderisa con Twig

    public function loadByTwig($view) {
        $dir = __DIR__;
        $loader = new Twig_Loader_Filesystem ( "$dir" );
        $twig = new Twig_Environment ( $loader, array ());
        $template = $twig->loadTemplate ( $view );

        $params = array ();
        foreach ( $this as $key => $value ) {
            if ((strcmp ( $key, "js" ) != 0)) {
                $params [$key] = $value;
            }
        }

        echo $template->render ( $params );
    }


}

?>