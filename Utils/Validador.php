<?php
class Validador{
    public function validarString( $var, $error, $tam ){
        if (!$this->varSet($var, $error) || !$this->tam($var, $tam) || !preg_match("/^[a-zA-Z0-9]+$/", $var) )
            throw new Exception($error);
    }

    public function validarStringEspeciales($var, $error, $tam){
        if ( !$this->tam($var, $tam) || !$this->varSet($var, $error) || !preg_match("/^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹ ]+$/", $var))
            throw new Exception($error);
    }
    public function validarNumeros($var, $error, $tam){
        if (!$this->tam($var, $tam) || !$this->varSet($var, $error) || !preg_match("/^[0-9]+$/", $var))
            throw new Exception($error);
    }

    public function validarFecha($var, $error){
        if (!$this->tam($var, 25) || !$this->varSet($var, $error) ||  !preg_match("(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})", $var))
            throw new Exception($error);
    }

    public function validarMail($var, $error, $tam){
        if (!$this->tam($var, $tam) || !$this->varSet($var, $error) ||  !preg_match("/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/", $var))
            throw new Exception($error);
    }
    public function validarNumerosPunto($var, $error, $tam){
        if (!$this->tam($var, $tam) || !$this->varSet($var, $error) || !preg_match("/^[0-9. ]+$/", $var))
            throw new Exception($error);
    }
    public function varSet($var, $error){
        if (!isset($var)) throw new Exception($error);
        return true;
    }

    public function tam($var, $tam){
        if ( 0 >= strlen($var) ||  strlen($var) > $tam ) throw new Exception($var." no es de un tamaño permitido");
        return true;
    }
}