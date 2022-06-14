<?php

use Hcode\Model\Product;
use Hcode\Page;
use Hcode\Model\User;

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

$app->get("/login", function(){	

	$page = new Page();

	$page->setTpl("login", [

		'error' => User::getError(),
		'errorRegister' => User::getErrorRegister(),
		'registerValues'=>(isset($_SESSION['registerValues'])) ? $_SESSION['registerValues'] : ['name'=>'', 'email'=>'', 'phone'=>'']

	]);
	

});

$app->post("/login", function(){

	try {

		User::login($_POST['login'], $_POST['password']);

	} catch(Exception $e) {

		User::setError($e->getMessage());

	}

	header("Location: /checkout");
	exit;

});

$app->get("/logout", function(){

	User::logout();

	header("Location: /login");
	exit;

});

$app->post("/register", function(){

	$_SESSION['registerValues'] = $_POST;

	if (!isset($_POST['name']) || $_POST['name'] == '') {

		User::setErrorRegister("Preencha o seu nome.");
		header("Location: /login");
		exit;

	}

	if (!isset($_POST['email']) || $_POST['email'] == '') {

		User::setErrorRegister("Preencha o seu e-mail.");
		header("Location: /login");
		exit;

	}

	if (!isset($_POST['password']) || $_POST['password'] == '') {

		User::setErrorRegister("Preencha a senha.");
		header("Location: /login");
		exit;

	}

	if (User::checkLoginExist($_POST['email']) === true) {

		User::setErrorRegister("Este endereço de e-mail já está sendo usado por outro usuário.");
		header("Location: /login");
		exit;

	}

	$user = new User();

	$user->setData([
		'inadmin'=>0,
		'deslogin'=>$_POST['email'],
		'desperson'=>$_POST['name'],
		'desemail'=>$_POST['email'],
		'despassword'=>$_POST['password'],
		'nrphone'=>$_POST['phone']
	]);

	$user->save();

	User::login($_POST['email'], $_POST['password']);

	header('Location: /checkout');
	exit;

});

//rota Page esqueci a senha//////////////////////////////////
$app->get("/forgot", function(){
	//passar valores do contrutor | desabilitar o head e footer
	$page = new Page();
	//desenhando pagina com o setTpl que criamos
	$page->setTpl("forgot");	

});

//rota form esqueci a senha forgot
$app->post("/forgot", function(){
	//receber email pelo bwouser com metodo no USer
	$user = User::getForgot($_POST["email"], false);
	//redireciona
	header("Location: forgot/sent");
	//para execução
	exit;

});

//rota da page forgot sent
$app->get("/forgot/sent", function(){
	//passar valores do contrutor | desabilitar o head e footer
	$page = new Page();
	//desenhando pagina com o setTpl que criamos
	$page->setTpl("forgot-sent");

});

//rota da page reset
$app->get("/forgot/reset", function(){
	//ppegar e validar o codigo que foi no email
	$user = User::validForgotDecrypt($_GET["code"]);
	//passar valores do contrutor | desabilitar o head e footer
	$page = new Page();
	//desenhando pagina com o setTpl que criamos
	$page->setTpl("forgot-reset", array(
		"name"  =>$user["desperson"],
		"code"  =>$_GET["code"]
	));

});

//rota post reset
$app->post("/forgot/reset", function(){
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
	$page = new Page();
	//desenhando pagina com o setTpl que criamos
	$page->setTpl("forgot-reset-success");

});

$app->get("/profile", function(){

	User::verifyLogin(false);

	$user = User::getFromSession();

	$page = new Page();

	$page->setTpl("profile", [

		'user' =>$user->getValues(),
		'profileMsg' => User::getSuccess(),
		'profileError' => User::getError()

	]);

});

$app->post("/profile", function(){

	User::verifyLogin(false);

	if (!isset($_POST['desperson']) || $_POST['desperson'] === '') {
		User::setError("Preencha o seu nome.");
		header('Location: /profile');
		exit;
	}

	if (!isset($_POST['desemail']) || $_POST['desemail'] === '') {
		User::setError("Preencha o seu e-mail.");
		header('Location: /profile');
		exit;
	}

	$user = User::getFromSession();

	if ($_POST['desemail'] !== $user->getdesemail()) {

		if (User::checkLoginExist($_POST['desemail']) === true) {

			User::setError("Este endereço de e-mail já está cadastrado.");
			header('Location: /profile');
			exit;

		}

	}

	$_POST['inadmin'] = $user->getinadmin();
	$_POST['despassword'] = $user->getdespassword();
	$_POST['deslogin'] = $_POST['desemail'];

	$user->setData($_POST);

	$user->update();

	$_SESSION[User::SESSION] = $user->getValues();

	User::setSuccess("Dados alterados com sucesso!");

	header('Location: /profile');
	exit;

});


?>