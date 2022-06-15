<?php

use Hcode\PageAdmin;
use Hcode\Model\User;
use Hcode\Model\Category;
use Hcode\Model\Product;
use Hcode\Page;

//pagina de categorias
$app->get("/admin/categories", function(){
	//verifica se o admim esta logado
	User::verifyLogin();
	//verifica se search existe | pagina atual
	$search = (isset($_GET['search'])) ? $_GET['search'] : "";
	$page 	= (isset($_GET['pege'])) ? $_GET['pege'] : 1;

	if ($search != "") {

		//listar os usuarios da busca
		$pagination = Category::getPageSearch($search, $page);
		
	} else {

		//listar todos os usuarios
		$pagination = Category::getPage($page);

	}
	
	//criando a paginação
	$pages = [];

	for ($x = 0; $x < $pagination['pages']; $x ++)
	{

		array_push($pages, [
			"href" => "/admin/users?" . http_build_query([
				"page"   => $x + 1,
				"search" => $search
			]),
			"text" => $x + 1
		]);

	}	

	$page = new PageAdmin();

	$page->setTpl("categories", [
		"categories"  => $pagination['data'],
		"search" => $search,
		"pages"  => $pages
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

//rota relaciona categorias a produtos
$app->get("/admin/categories/:idcategory/products", function($idcategory){
	//verificar se esta logado
	User::verifyLogin();
	//Estanciar objeto
	$category = new Category();
	//ppeagr o idcategory
	$category->get((int)$idcategory);
	//estanciando objeto Page
	$page = new PageAdmin();
	//chamar template Riamtpl
	$page->setTpl("categories-products", [
		//setando dados
		"category" => $category->getValues(),
		"productsRelated" => $category->getProducts(),
		"productsNotRelated" => $category->getProducts(false)
	]);

});

//rota adiciona categorias a produtos
$app->get("/admin/categories/:idcategory/products/:idproducts/add", function($idcategory, $idproduct){
	//verificar se esta logado
	User::verifyLogin();
	//Estanciar objeto
	$category = new Category();
	//ppeagr o idcategory
	$category->get((int)$idcategory);
	//estancia novo produto
	$product = new Product();
	//pega idproduct
	$product->get((int)$idproduct);

	$category->addProduct($product);
	//redirecionando...
	header("Location: /admin/categories/".$idcategory."/products");
	exit;

});

//rota remove categorias a produtos
$app->get("/admin/categories/:idcategory/products/:idproducts/remove", function($idcategory, $idproduct){
	//verificar se esta logado
	User::verifyLogin();
	//Estanciar objeto
	$category = new Category();
	//ppeagr o idcategory
	$category->get((int)$idcategory);
	//estancia novo produto
	$product = new Product();
	//pega idproduct
	$product->get((int)$idproduct);

	$category->removeProduct($product);
	//redirecionando...
	header("Location: /admin/categories/".$idcategory."/products");
	exit;
});


?>