<?php
class Validador{
    public function validarString( $var ){
        if (!$this->varSet($var) || !preg_match("/^[a-zA-Z0-9]+$/", $var) )
            throw new Exception($var.' debe ser letras o numeros');
    }
    
    public function validarStringEspeciales($var, $tam){
        if ( $this->tam($var, $tam) || $this->varSet($var) || !preg_match("/^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹ ]+$/", $var))
            throw new Exception($var.' este campo no acepta numeros');
    }
    public function validarNumeros($var, $tam){
        if ($this->tam($var, $tam) || $this->varSet($var) || !preg_match("/^[0-9]+$/", $var))
            throw new Exception($var.' este campo solo acepta numeros');
    }
    
    public function validarMail($var, $tam){
        if ($this->tam($var, $tam) || $this->varSet($var) ||  !preg_match("/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/", $var))
            throw new Exception($var.' formato de mail invalido');
    }
    public function validarNumerosPunto($var, $tam){
        if ($this->tam($var, $tam) || $this->varSet($var) || !preg_match("/^[0-9]+$/.", $var))
            throw new Exception($var.' este campo solo acepta numeros');
    }
    public function varSet($var){
        if (!isset($var)) throw new Exception("Debes completar todos los campos");
        return true;
    }

    public function tam($var, $tam){
        if ( 0 >= strlen($var) ||  strlen($var) > $tam ) throw new Exception($var." no es de un tamaño permitido");
        return true;
    }
}