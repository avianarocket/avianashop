<?php 
//iniciando sessão
session_start()/
//chamando autoload das classes
require_once("vendor/autoload.php");
//chamando os namespaces dos vendors
use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use Hcode\Model\User;
use Hcode\Model\Category;
//criando objeto framework Slim
$app = new Slim();
//passando as configs do Slim
$app->config('debug', true);
//criando as rotas do sistema
//rota Raiz /
$app->get('/', function() {
    //variavel page iniciando objeto vazio
	$page = new Page();
	//desenhando pagina com o setTpl que criamos
	$page->setTpl("index");	
});
//fim da rota Raiz//////////////////////////////

//rota raiz Admin
$app->get('/admin', function() {
	//verifica se o admim esta logado
	User::verifyLogin();
    //variavel page iniciando objeto vazio
	$page = new PageAdmin();
	//desenhando pagina com o setTpl que criamos
	$page->setTpl("index");

});
//fim da rota raiz Admin//////////////////////////

//rota Admin/Login
$app->get('/admin/login', function() {
    //passar valores do contrutor | desabilitar o head e footer
	$page = new PageAdmin([
		"header" =>false,
		"footer" =>false
	]);
	//desenhando pagina com o setTpl que criamos
	$page->setTpl("login");	
});
//fim da rota Admin/Login////////////////////////////

//rota do formulario via post do Admin/Login
$app->post('/admin/login', function() {
    //chamando a classe User metodo estatico Login()
	User::login($_POST["login"], $_POST["password"]);
	//redireciona para pagina admin
	header("Location: /admin");
	exit;
});
//rota do formulario via post do Admin/Login

//rota para Deslogar do Admin/Login
$app->get('/admin/logout', function() {
	//chamando a função deslogar
	User::logout();
	//redirecionar para login
	header("Location: /admin/login");
	exit; // parar codigo
});
//fim rota para Deslogar do Admin/Login

//rota para listar todos os usuarios do Admin
$app->get('/admin/users', function() {
	//verifica se o admim esta logado
	User::verifyLogin();
	//listar todos os usuarios
	$users = User::listAll();
	//chamando PageAdmin
	$page = new PageAdmin();
	//chamando template
	$page->setTpl("users", array(
		//pegando os valores na chave users
		"users" => $users
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

//////////////////////////////////////////////////////////////////////////////
//////////////////////////// ROTAS DAS CATEGORIAS ////////////////////////////
//////////////////////////////////////////////////////////////////////////////

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

//rota Categorias home
$app->get("/categories/:idcategory", function($idcategory){
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
		"products" => []
	]);

});

// rodar tudo o projeto
$app->run();

 ?>


