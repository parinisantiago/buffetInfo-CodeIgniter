<?php

class Session
{
    public static function init(){

        if (! isset($_SESSION['on'])) {

            session_start();
            $_SESSION['on'] = true;
        }
        return true;
    }

    public static function setValue($value, $key){
        $_SESSION[$key] = $value;
    }

    public static function getValue($key){
       if ( isset($_SESSION[$key])) return($_SESSION[$key]);
       else return '-1 NaN -1';
    }

    public static function userLogged(){
        return (isset($_SESSION['logged']));
    }

    public static function destroy(){
        session_destroy();
        unset($_SESSION);
    }
}
?>