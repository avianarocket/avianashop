<?php 

require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;

// use \Hcode\DB\Sql;

$app = new Slim();

$app->config('debug', true);

//rota raiz
$app->get('/', function() {
    //variavel page iniciando objeto vazio
	$page = new Page();
	//desenhando pagina com o setTpl que criamos
	$page->setTpl("index");	
});
//fim da rota raiz

//rota raiz Admin
$app->get('/admin/', function() {
    //variavel page iniciando objeto vazio
	$page = new PageAdmin();
	//desenhando pagina com o setTpl que criamos
	$page->setTpl("index");	
});
//fim da rota raiz Admin

// rodar tudo o projeto
$app->run();

 ?>