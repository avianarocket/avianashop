<?php

use Hcode\Model\Product;
use Hcode\PageAdmin;
use Hcode\Model\User;

//Pge produtos
$app->get("/admin/products" , function(){

    User::verifyLogin();
    //verifica se search existe | pagina atual
	$search = (isset($_GET['search'])) ? $_GET['search'] : "";
	$page 	= (isset($_GET['pege'])) ? $_GET['pege'] : 1;

	if ($search != "") {

		//listar os usuarios da busca
		$pagination = Product::getPageSearch($search, $page);
		
	} else {

		//listar todos os usuarios
		$pagination = Product::getPage($page);

	}
	
	//criando a paginação
	$pages = [];

	for ($x = 0; $x < $pagination['pages']; $x ++)
	{

		array_push($pages, [
			"href" => "/admin/product?" . http_build_query([
				"page"   => $x + 1,
				"search" => $search
			]),
			"text" => $x + 1
		]);

	}	        

    $page = new PageAdmin();

    $page->setTpl("products", [
        "products"  => $pagination['data'],
		"search" => $search,
		"pages"  => $pages
    ]);

});

//Pge produtos
$app->get("/admin/products/create" , function(){

    User::verifyLogin();  

    $page = new PageAdmin();

    $page->setTpl("products-create");

});

//Salva produtos
$app->post("/admin/products/create" , function(){

    User::verifyLogin();  

    $product = new Product();

    $product->setData($_POST);

    $product->save();

    header("Location: /admin/products");
    exit;

});

//Page editar produtos
$app->get("/admin/products/:idproduct" , function($idproduct){

    User::verifyLogin();  

    $product = new Product();

    $product->get((int)$idproduct);

    $page = new PageAdmin();

    $page->setTpl("products-update", [
        "product" => $product->getValues()
    ]);

});

//save editar produtos
$app->post("/admin/products/:idproduct", function($idproduct){

	User::verifyLogin();

	$product = new Product();

	$product->get((int)$idproduct);

	$product->setData($_POST);

	$product->save();

	$product->setPhoto($_FILES["file"]);

	header('Location: /admin/products');
	exit;

});

//deletar produtos
$app->get("/admin/products/:idproduct/delete", function($idproduct){

	User::verifyLogin();

	$product = new Product();

	$product->get((int)$idproduct);

	$product->delete();

	header('Location: /admin/products');
	exit;

});


?>


