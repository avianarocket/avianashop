<?php

use Hcode\PageAdmin;
use Hcode\Model\User;

//rota para listar todos os usuarios do Admin
$app->get('/admin/users', function() {
	//verifica se o admim esta logado
	User::verifyLogin();
	//verifica se search existe | pagina atual
	$search = (isset($_GET['search'])) ? $_GET['search'] : "";
	$page 	= (isset($_GET['pege'])) ? $_GET['pege'] : 1;

	if ($search != "") {

		//listar os usuarios da busca
		$pagination = User::getPageSearch($search, $page);
		
	} else {

		//listar todos os usuarios
		$pagination = User::getPage($page);

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
	//chamando PageAdmin
	$page = new PageAdmin();
	//chamando template
	$page->setTpl("users", array(
		//pegando os valores na chave users
		"users"  => $pagination['data'],
		"search" => $search,
		"pages"  => $pages
	));
});
//fim rota para listar todos os usuarios do Admin

//rota para criar todos os usuarios do Admin
$app->get('/admin/users/create', function() {
	//verifica se o admim esta logado
	User::verifyLogin();
	//chamando PageAdmin
	$page = new PageAdmin();
	//chamando template
	$page->setTpl("users-create");
});
//fim rota para criar todos os usuarios do Admin

//rota para deletar os usuarios do Admin
$app->get('/admin/users/:iduser/delete', function($iduser) {
	//verifica se o admim esta logado
	User::verifyLogin();
	//criar objeto
	$user = new User();
	//chamando o get iduser garantindo que ser inteiro
	$user->get((int)$iduser);
	//deletando
	$user->delete();
	//redirecionando...
	header("Location: /admin/users");
	//parar execução
	exit;
	
});
//fim rota para deletar os usuarios do Admin

//rota para atualizar dados dos usuarios do Admin
$app->get('/admin/users/:iduser', function($iduser) {
	//verifica se o admim esta logado
	User::verifyLogin();
	//chama user
	$user = new User();
	//chamando o get iduser garantindo que ser inteiro
	$user->get((int)$iduser);
	//chamando PageAdmin
	$page = new PageAdmin();
	//chamando template
	$page->setTpl("users-update", array(
		//pegando valores 
		"user" => $user->getValues()
	));
});
//fim rota para atualizar dados dos usuarios do Admin

//rota para salvar criação dos usuarios do Admin
$app->post('/admin/users/create', function() {
	//verifica se o admim esta logado
	User::verifyLogin();
	//chamar objeto User
	$user = new User();
	//verificar se inadim foi definido? se sim = 1 caso nao = 0
	$_POST["inadmin"] = (isset($_POST["inadmin"]))? 1 : 0;
	//chamar set data para pegar os dados das tabelas
	$user->setData($_POST);
	//salvar
	$user->save();
	//redireciona para usuarios
	header("Location: /admin/users");
	//para execução
	exit;
	
});
//fim rota para salvar criação dos usuarios do Admin

//rota para salvar edição dos usuarios do Admin
$app->post('/admin/users/:iduser', function($iduser) {
	//verifica se o admim esta logado
	User::verifyLogin();
	//cria objeto User
	$user = new User();
	//verificar se inadim foi definido? se sim = 1 caso nao = 0
	$_POST["inadmin"] = (isset($_POST["inadmin"]))? 1 : 0;
	//pega dados pelo id inteiro
	$user->get((int)$iduser);
	//setando os dados
	$user->setData($_POST);
	//fazerndo update/salvando
	$user->update();
	//redirecionando
	header("Location: /admin/users");
	//para execução
	exit;
	
});
//fim rota para salvar criação dos usuarios do Admin

//rota Page esqueci a senha//////////////////////////////////
$app->get("/admin/forgot", function(){
	//passar valores do contrutor | desabilitar o head e footer
	$page = new PageAdmin([
		"header" =>false,
		"footer" =>false
	]);
	//desenhando pagina com o setTpl que criamos
	$page->setTpl("forgot");	

});

//rota form esqueci a senha forgot
$app->post("/admin/forgot", function(){
	//receber email pelo bwouser com metodo no USer
	$user = User::getForgot($_POST["email"]);
	//redireciona
	header("Location: /admin/forgot/sent");
	//para execução
	exit;

});

//rota da page forgot sent
$app->get("/admin/forgot/sent", function(){
	//passar valores do contrutor | desabilitar o head e footer
	$page = new PageAdmin([
		"header" =>false,
		"footer" =>false
	]);
	//desenhando pagina com o setTpl que criamos
	$page->setTpl("forgot-sent");

});

//rota da page reset
$app->get("/admin/forgot/reset", function(){
	//ppegar e validar o codigo que foi no email
	$user = User::validForgotDecrypt($_GET["code"]);
	//passar valores do contrutor | desabilitar o head e footer
	$page = new PageAdmin([
		"header" =>false,
		"footer" =>false
	]);
	//desenhando pagina com o setTpl que criamos
	$page->setTpl("forgot-reset", array(
		"name"  =>$user["desperson"],
		"code"  =>$_GET["code"]
	));

});

//rota post reset
$app->post("/admin/forgot/reset", function(){
	//ppegar e validar o codigo que foi no email
	$forgot = User::validForgotDecrypt($_POST["code"]);
	//salvado no banco de dados
	User::setForgotUsed($forgot["idrecovery"]);
	//carregar os dados do usuario
	$user = new User();
	$user->get((int)$forgot["iduser"]);
	//criando hash da senha
	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, [
		"cost" => 12
	]);
	//criar o rash da senha nova
	$user->setPassword($password);
	//chamar template
	//passar valores do contrutor | desabilitar o head e footer
	$page = new PageAdmin([
		"header" =>false,
		"footer" =>false
	]);
	//desenhando pagina com o setTpl que criamos
	$page->setTpl("forgot-reset-success");


});


?>