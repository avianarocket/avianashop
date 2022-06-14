<?php

use Hcode\DB\Sql;
use \Hcode\Model\User;
use \Hcode\Model\Cart;

function formatPrice($vlprice)
{

	if (!$vlprice > 0) $vlprice = 0;

	return number_format($vlprice, 2, ",", ".");

}

function checkLogin($inadmin = true)
{

	return User::checkLogin($inadmin);

}

function getUserName($is_admin = True) {
	$user = User::getFromSession();
 
	$sql = new Sql();
 
	$login = $user -> getdeslogin();
 
	$results = $sql -> select("SELECT * FROM tb_users a INNER JOIN tb_persons b ON a.idperson = b.idperson WHERE a.deslogin = :LOGINS", array(
		":LOGINS" => $login,
	));
 
	if (count($results) > 0) {
		return $results[0]["desperson"];
	}
 
	else {
		return "";
	}
}

function getCartNrQtd()
{

	$cart = Cart::getFromSession();

	$totals = $cart->getProductsTotals();

	return $totals['nrqtd'];

}

function getCartVlSubTotal()
{

	$cart = Cart::getFromSession();

	$totals = $cart->getProductsTotals();

	return formatPrice($totals['vlprice']);

}


?>