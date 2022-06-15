<?php

use Hcode\Model\Order;
use Hcode\Model\OrderStatus;
use Hcode\PageAdmin;
use Hcode\Model\User;

$app->get("/admin/orders/:idorder", function($idorder){

    User::verifyLogin();

    $order = new Order();

    $order->get((int)$idorder);

    $cart = $order->getCart();

    $page = new PageAdmin();

    $page->setTpl("order", [

        "order"    => $order->getValues(),
        "cart"     => $cart->getValues(),
        "products" => $cart->getProducts()

    ]);

});

$app->get("/admin/orders/:idorder/status", function($idorder){

    //verificando login
    User::verifyLogin();
    //criaobjeto
    $order = new Order();
    //pega o numero do pedido
    $order->get((int)$idorder);
    //CARREGANDO TEMPLATE
    $page = new PageAdmin();

    $page->setTpl("order-status", [

        "order"      => $order->getValues(),
        "status"     => OrderStatus::listAll(),
        "msgSuccess" => Order::getSuccess(),
        "msgError"   => Order::getError()
    
    ]);

});

$app->post("/admin/orders/:idorder/status", function($idorder){

    //verificando login
    User::verifyLogin();
    //verificando se recebemos o idstatus
    if (!isset($_POST['idstatus']) || !(int)$_POST['idstatus'] > 0) {
        Order::setError("Informe o Status atual do pedido.");
        header("Location: /admin/orders/" .$idorder. "/status");
        exit;
    }
    //criaobjeto
    $order = new Order();
    //pega o numero do pedido
    $order->get((int)$idorder);
    //setando status pelo post
    $order->setidstatus((int)$_POST['idstatus']);
    //salvando
    $order->save();
    //mensagem do sucesso
    Order::setSuccess("Status alterado com Sucesso!");    
    //redirecionando...
    header("Location: /admin/orders/" .$idorder. "/status");
    exit;

});

$app->get("/admin/orders/:idorder/delete", function($idorder){

    //verificando login
    User::verifyLogin();
    //criaobjeto
    $order = new Order();
    //pega o numero do pedido
    $order->get((int)$idorder);
    //deletando...
    $order->delete();
    //redirecionando...
    header("Location: /admin/orders");
    exit;

 });

//rota raiz Admin Order
 $app->get("/admin/orders", function(){

    //verificando o login
    User::verifyLogin();
    //criar Objeto pagina
    $page = new PageAdmin();
    //chamando o template
    $page->setTpl("orders", [
        //passando variaveis para o template
        //trazendo todos os pedidos
        "orders" => Order::listAll()

    ]);

 });

?>