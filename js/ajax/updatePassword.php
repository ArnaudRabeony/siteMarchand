<?php 

 include('../../model/functions.php');
 include('../../model/client.php');
 include('../../model/sql_functions.php');

if(verifPost(array("id","email","password","newPassword")))
{
	//checks if the password matches
	if(customerConnection($db,$_POST['email'],$_POST["password"]))
	{
		updatePassword($db,$_POST["id"],$_POST["newPassword"]);
		session_destroy();
		header("location: index.php?page=view/co_page");
	}
	else
		echo "passwordError";
}
?>
