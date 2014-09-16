<?php
 
	if ($_REQUEST['correct'] == 'correct') {
		
		require '../lib/setters.php';
		//$db = new PDO('mysql:dbname=friendquiz;host=localhost','root','k9is1337!');
		$db = mysqlConnector();

		$sql = "UPDATE users SET coins=coins+1 WHERE user_id='".$_REQUEST['user']."'";
	   
		$statement = $db->prepare($sql);
		$statement->execute();	
	}
 
?>
