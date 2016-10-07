<?php
class Validador{
    public function validarString( $var ){
        if (! preg_match("/^[a-zA-Z0-9]+$/", $var) )
            throw new Exception($var.' debe ser letras o numeros');
    }
    
    public function validarStringEspeciales($var){
        if (! preg_match("/^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹ ]+$/", $var))
            throw new Exception($var.' este campo no acepta numeros');
    }
    public function validarNumeros($var){
        if (! preg_match("/^[0-9]+$/", $var))
            throw new Exception($var.' este campo solo acepta numeros');
    }
    
    public function validarMail($var){
        if (! preg_match("/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/", $var))
            throw new Exception($var.' formato de mail invalido');
    }
    public function validarNumerosPunto($var){
        if (! preg_match("/^[0-9]+$/.", $var))
            throw new Exception($var.' este campo solo acepta numeros');
    }
}