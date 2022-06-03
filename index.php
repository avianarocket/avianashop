<?php 
//iniciando sessão
session_start()/
//chamando autoload das classes
require_once("vendor/autoload.php");
//chamando os namespaces dos vendors
use \Slim\Slim;
//criando objeto framework Slim
$app = new Slim();
//passando as configs do Slim
$app->config('debug', true);

require_once("function.php");

//criando as rotas SITE
require_once("R-site.php");
require_once("R-site-categories.php");
//chamando rotas ADMIN
require_once("R-admin.php");
require_once("R-admin-users.php");
require_once("R-admin-categories.php");
require_once("R-admin-products.php");
// rodar tudo o projeto
$app->run();

 ?>


