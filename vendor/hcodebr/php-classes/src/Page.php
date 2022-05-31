<?php
//especificando nname epace desta classe para chamar depois
namespace Hcode;
//chamando o Rain Tpl
use Rain\Tpl;
//criando a classe Page com pleta
class Page {
    //atributos da classe
    //atributos do Tpl
    private $tpl;
    //atributos options como array
    private $options = []; // recebe ['data']
    //atributo de opts defaut
    private $default = [
        "data"=>[] //dados da variavel
    ];


    //criando os metodos...
    //metodo contrutor, passando opcoes, 
    public function __construct($opts = array())
    {
        //juntar as variaveis das options, se nao for enviado nada, ou opts se vier do usuario
        $this->options = array_merge($this->default, $opts);
        //configurando o template Rain
        $config = array(
            "tpl_dir"       => $_SERVER["DOCUMENT_ROOT"]."/views/",
            "cache_dir"     => $_SERVER["DOCUMENT_ROOT"]."/views-cache/",
            "debug"         => false // set to false to improve the speed
        );

        Tpl::configure( $config );

        //criando projeto com $this para ter acesso de outras classes - Tpl object
        $this->tpl = new Tpl;

        //seria assim antes... pegando os dados da variavel data de options
        // foreach ($this->options["data"] as $key => $value) {
        //     //retornado o assign do tpl
        //     $this->tpl->assign( $key, $value );
        // }
        ////////////////////////////////////////////////////////////////////

        //chama o setData + option com a variavel data
        $this->setData($this->options["data"]);

        //desenhando o Tpl na tela
        $this->tpl->draw("header");

    }
    //fim do contrutor

    //metodo para setar variavel data | para nao ficar repetindo o foreach
    private function setData($data = array())
    {
        //pegando os dados da variavel data de options
        foreach ($data as $key => $value) {
            //retornado o assign do tpl
            $this->tpl->assign( $key, $value);
        }
    }

    //metodo body, nome do tamplete e os dados retornando o html
    public function setTpl($name, $data = array(), $returnHTML = false)
    {
        //seria assim ...pegando os dados da variavel data de options
        // foreach ($data as $key => $value) {
        //     //retornado o assign do tpl
        //     $this->tpl->assign( $key, $value );
        // }
        ////////////////////////////////////////////////////////////

        //chama metodo setData    
        $this->setData($data);

        //desenhar o template na tela que vem com nome pela variavel name
        //return caso precise adicionar em algum local
        return $this->tpl->draw($name, $returnHTML);
    }
    //fim do body




    //metodo destrutor
    public function __destruct()
    {
        //criando o rodape e fechando o Tpl
        $this->tpl->draw("footer");
    }
    //fim do destrutor

//fim da classe Pge
}


?>