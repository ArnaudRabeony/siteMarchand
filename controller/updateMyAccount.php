<?php 

require_once(dirname(__FILE__) . '/../model/client.php');
require_once(dirname(__FILE__) . '/../model/functions.php');

if(verifGet(array("id","address","phone","lastname","firstname","email")))
{
	$id=$_GET['id'];
	$address=$_GET['address'];
	$phone=$_GET['phone'];
	$firstname=$_GET['firstname'];
	$lastname=$_GET['lastname'];
	$email=$_GET['email'];

	//verifier si mail existe
	oneselfUpdate($db,$id,$lastname,$firstname,$email,$address,$phone);
}
 ?>