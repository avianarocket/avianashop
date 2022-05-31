<?php
//declarando name space
namespace Hcode;
//criando classe e extendendo da classe Page
class PageAdmin extends Page {

    //criando os metodos...
    //metodo contrutor, pegando $opts da classe Page do site || passando a pasta do views/admin para variavel $tpl_dir no Page do site
    public function __construct($opts = array(), $tpl_dir = "/views/admin/")
    {
        //como a classe PageAdmin fa tudo igual a classe Page do site vamos usar as funcoes de la com herança
        //chamando o __construct da classe Page do site com parent::
        parent::__construct($opts, $tpl_dir);
    }


}

?>