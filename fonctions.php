<?php 
require_once('connexion.php');
// Permet d'appliquer htmlentities() à toutes les variables $_POST et $_GET
function protectPostGet() {
	htmlentitiesArray($_POST);
	htmlentitiesArray($_GET);
}

function htmlentitiesArray (&$tab) {
	foreach($tab as $cle => &$value) {
		if (is_array($value))
			htmlentitiesArray($value);
		else
			$value = htmlentities($value);
	}
}

function isExisting($page)
{
	$pagesArray=array(
				 "connexion",
				 "football",
				 "gestionClients",
				 "page_co",
				 "page_deco",
				 "welcome",
				 "creationCompte",
				 "creationCompte",
				);

	return in_array($page,$pagesArray);
}

function addCustomer($db,$email,$lastname,$firstname,$password,$address,$phone)
{
	$req=$db->prepare("INSERT INTO client(type, email, nom, prenom, mdp, adresse, telephone) values(?,?,?,?,?,?,?)");
	$req->execute(array("client",$email,$lastname,$firstname,$password,$address,$phone));
}

function displayCustomers($db)
{
	$req=$db->prepare('select * from client');
	$req->execute();

	while($res=$req->fetch(PDO::FETCH_ASSOC))
		echo 'Nom : '.$res['nom'];
}

function tryCustomerConnexion($db,$mail,$password)
{
	$req=$db->prepare('select * from client where email=:mail && mdp=:password');
	$req->bindValue(':mail',$mail);
	$req->bindValue(':password',$password);
	$req->execute();
	$res=$req->rowCount();

	return $res==1 ? true : false;
}

 ?>