<?php 

require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
// use \Hcode\DB\Sql;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function() {
    //variavel page iniciando objeto vazio
	$page = new Page();
	//desenhando pagina com o setTpl que criamos
	$page->setTpl("index");	

	///////////////////////////////////////////////////////
	// $sql = new Sql();
	//
    // $results = $sql->select("SELECT * FROM tb_users");
	//
	// echo json_encode($results);
	////////////////////////////////////////////////////
});
// rodar tudo o projeto
$app->run();

 ?>