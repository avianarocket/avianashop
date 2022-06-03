<?php

use Hcode\PageAdmin;
use Hcode\Model\User;
use Hcode\Model\Category;

//pagina de categorias
$app->get("/admin/categories", function(){
	//verifica se o admim esta logado
	User::verifyLogin();

	$categories = Category::listAll();

	$page = new PageAdmin();

	$page->setTpl("categories", [
		"categories" => $categories
	]);

});

//pagina criar categorias
$app->get("/admin/categories/create", function(){	
	//verifica se o admim esta logado
	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("categories-create");

});

//salva categorias
$app->post("/admin/categories/create", function(){	
	//verifica se o admim esta logado
	User::verifyLogin();

	$category = new Category();

	$category->setData($_POST);

	$category->save();

	header("Location: /admin/categories");
	exit;

});

//Page editar categorias
$app->get("/admin/categories/:idcategory", function($idcategory){
	//verifica se o admim esta logado
	User::verifyLogin();
	//carregar objeto categoria
	$category = new Category();
	//pegando id e onvertendo em numerico
	$category->get((int)$idcategory);	
	//carrega Page editar categoria
	$page = new PageAdmin();
	//chama template e joga variavel do idcategory e convertendo em array com gatvalues
	$page->setTpl("categories-update", [
		"category" => $category->getValues()
	]);

});

//save editar categorias
$app->post("/admin/categories/:idcategory", function($idcategory){
	//verifica se o admim esta logado
	User::verifyLogin();
	//carregar objeto categoria
	$category = new Category();
	//pegando id e onvertendo em numerico
	$category->get((int)$idcategory);
	//carrega os dados que vem do post
	$category->setData($_POST);
	//salvando
	$category->save();
	//redirecionando
	header("Location: /admin/categories");
	exit;	
	
});

//deletar categria
$app->get("/admin/categories/:idcategory/delete", function($idcategory){
	//verifica se o admim esta logado
	User::verifyLogin();
	//carrega categorias
	$category = new Category();
	//pega id categoria
	$category->get((int)$idcategory);
	//deletando
	$category->delete();
	//redirecionando
	header("Location: /admin/categories");
	exit;

});


?>