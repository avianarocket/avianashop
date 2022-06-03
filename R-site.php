<?php

use Hcode\Model\Product;
use Hcode\Page;

//rota Raiz /
$app->get('/', function() {
	//pegando todos os produtos
	$products = Product::listAll();
    //variavel page iniciando objeto vazio
	$page = new Page();
	//desenhando pagina com o setTpl que criamos
	$page->setTpl("index", [
		"products" => Product::checkList($products)
	]);	
});
//fim da rota Raiz//////////////////////////////

?>