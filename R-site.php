<?php

use Hcode\Page;

//rota Raiz /
$app->get('/', function() {
    //variavel page iniciando objeto vazio
	$page = new Page();
	//desenhando pagina com o setTpl que criamos
	$page->setTpl("index");	
});
//fim da rota Raiz//////////////////////////////

?>