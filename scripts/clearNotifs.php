<?php
 
	require '../lib/setters.php';
	$db = mysqlConnector();
	$sql = "UPDATE notifications SET `read`=1 WHERE `user_id` = '".$_REQUEST['user']."' AND `id` <= '".$_REQUEST['last']."'";
	$statement = $db->prepare($sql);
	$statement->execute();
	header("Location: notifications.php");

?>
