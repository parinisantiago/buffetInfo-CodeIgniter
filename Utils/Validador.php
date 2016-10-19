<?php

require_once 'valException.php';

class Validador{
    public function validarString( $var, $error, $tam ){
        if (!$this->varSet($var, $error) || !$this->tam($var, $tam) || !preg_match("/^[a-zA-Z0-9]+$/", $var) )
            throw new valException($error);
    }

    public function validarStringEspeciales($var, $error, $tam){
        if ( !$this->tam($var, $tam) || !$this->varSet($var, $error) || !preg_match("/^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹ ]+$/", $var))
            throw new valException($error);
    }
    public function validarNumeros($var, $error, $tam){
        if (!$this->tam($var, $tam) || !$this->varSet($var, $error) || !preg_match("/^[0-9]+$/", $var))
            throw new valException($error);
    }

    public function validarFecha($var, $error){
        if (!$this->tam($var, 25) || !$this->varSet($var, $error) ||  !preg_match("/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})+$/", $var))
            throw new valException($error);
    }

    public function validarMail($var, $error, $tam){
        if (!$this->tam($var, $tam) || !$this->varSet($var, $error) ||  !preg_match("/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/", $var))
            throw new valException($error);
    }
    public function validarNumerosPunto($var, $error, $tam){
        if (!$this->tam($var, $tam) || !$this->varSet($var, $error) || !preg_match("/^[0-9. ]+$/", $var))
            throw new valException($error);
    }
    public function varSet($var, $error){
        if (!isset($var)) throw new valException($error);
        return true;
    }

    public function tam($var, $tam){
        if (0 >= strlen($var)) throw new valException("todos los campos son obligatorios");
        if (  strlen($var) > $tam ) throw new valException($var." no es de un tamaño permitido");
        return true;
    }
}