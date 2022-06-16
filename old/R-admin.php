<?php

use Hcode\PageAdmin;
use Hcode\Model\User;

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


?>