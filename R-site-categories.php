<?php

use Hcode\Page;
use Hcode\Model\Category;
use Hcode\Model\Product;

//rota Categorias home
$app->get("/categories/:idcategory", function($idcategory){
	//verificar se est
	//Estanciar objeto
	$category = new Category();
	//ppeagr o idcategory
	$category->get((int)$idcategory);
	//estanciando objeto Page
	$page = new Page();
	//chamar template Riamtpl
	$page->setTpl("category", [
		//setando dados
		"category" => $category->getValues(),
		"products" => Product::checkList($category->getProducts())
	]);

});




?>