<?php
	echo "Submitted";
	
	define('DB_HOST', getenv('OPENSHIFT_MYSQL_DB_HOST'));
    define('DB_USER',getenv('OPENSHIFT_MYSQL_DB_USERNAME'));
    define('DB_PASS',getenv('OPENSHIFT_MYSQL_DB_PASSWORD'));
    define('DB_BASE','class');
    define('DB_PORT',getenv('OPENSHIFT_MYSQL_DB_PORT')); 
    $dsn = 'mysql:dbname='.DB_BASE.';host='.DB_HOST.';port='.DB_PORT;
    $dbh = new PDO($dsn, DB_USER, DB_PASS);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
	
	$sql = "INSERT INTO survey (level) VALUES ('".$_REQUEST['lvl']."')";
	   
	$statement = $dbh->prepare($sql);
	$statement->execute();	
?>
