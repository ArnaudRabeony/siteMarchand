<?php 

require_once("../../model/functions.php");
require_once("../../model/commande.php");

session_start();

if(verifGet(array("orderId")))
{
	updateOrderStatus($db,$_GET['orderId']);
	echo displayOrders($db);
}
elseif(verifGet(array("orderIdUpdateBack")))
{
	updateBackOrderStatus($db,$_GET['orderIdUpdateBack']);
	echo displayOrders($db);
}

