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
    }

    public static function destroy(){
        session_distroy();
    }
}
?>