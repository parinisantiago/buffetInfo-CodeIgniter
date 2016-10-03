<?php

class Session
{
    public static function init(){
        session_start();
    }

    public static function setValue($value, $key){
        $_SESSION[$key] = $value;
    }

    public static function getValue($key){
       if ( isset($_SESSION[$key])) return($_SESSION[$key]);
       else return false;
    }

    public static function userLogged(){
        return (isset($_SESSION['logged']));
    }

    public static function destroy(){
        session_destroy();
    }
}
?>