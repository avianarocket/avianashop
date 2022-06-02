<?php
//declarando o name space
namespace Hcode;
//criando classe
class Model {
    //atributos privados
    private $values = [];

    //criando metodos magicos para ver qndo o metodo foi chamado com nomes e argumentos    
    public function __call($name, $args)
    {
        //verificar qual method get? set?
        //substr sutrai caracteres 
        //strlen conta os caracteres
        $method = substr($name, 0, 3);
        $fieldName = substr($name, 3, strlen($name));
        //ver os dados que chegam com var_dump
        // var_dump($method, $filedName);
        // exit;
        switch ($method) {
            case "get": //VERIFICA SE JA FOI DEFINIDA
                return (isset($this->values[$fieldName])) ? $this->values[$fieldName] : NULL;
            break;
            case "set":
                return $this->values[$fieldName] = $args[0];
            break; 
        }

    }

    //metodo que busca e seta os campos e valores do banco de datos dinamicamente
    public function setData($data = array())
    {
        //foreach vai percorrer tudo e trazer as chaves e valores
        foreach ($data as $key => $value) {
            $this->{"set".$key}($value);
        }
    }

    //função para pegar os dados dos atributos values
    public function getValues()
    {
        //retorna os values
        return $this->values;
    }


}

?>