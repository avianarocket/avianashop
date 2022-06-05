<?php

use Hcode\Page;
use Hcode\Model\Product;

//rota produtos home
$app->get("/products/:desurl", function($desurl){

	$product = new Product;

    $product->getFormURL($desurl);

    $page = new Page();

    $page->setTpl("product-detail", [
        'product' => $product->getValues(),
        'categories' => $product->getCategories()
    ]);

});

?>