<?php
class Validador{
    public function validarString( $var, $error, $tam ){
        if (!$this->varSet($var, $error) || !$this->tam($var, $tam) || !preg_match("/^[a-zA-Z0-9]+$/", $var) )
            throw new Exception($error);
    }

    public function validarStringEspeciales($var, $tam, $error){
        if ( !$this->tam($var, $tam) || !$this->varSet($var, $error) || !preg_match("/^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹ ]+$/", $var))
            throw new Exception($error);
    }
    public function validarNumeros($var, $tam, $error){
        if (!$this->tam($var, $tam) || !$this->varSet($var, $error) || !preg_match("/^[0-9]+$/", $var))
            throw new Exception($error);
    }

    public function validarMail($var, $tam, $error){
        if (!$this->tam($var, $tam) || !$this->varSet($var, $error) ||  !preg_match("/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/", $var))
            throw new Exception($error);
    }
    public function validarNumerosPunto($var, $tam, $error){
        if (!$this->tam($var, $tam) || !$this->varSet($var, $error) || !preg_match("/^[0-9]+$/.", $var))
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