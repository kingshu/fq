<?php
	
	define('DB_HOST', getenv('OPENSHIFT_MYSQL_DB_HOST'));
    define('DB_USER',getenv('OPENSHIFT_MYSQL_DB_USERNAME'));
    define('DB_PASS',getenv('OPENSHIFT_MYSQL_DB_PASSWORD'));
    define('DB_BASE','class');
    define('DB_PORT',getenv('OPENSHIFT_MYSQL_DB_PORT')); 
    $dsn = 'mysql:dbname='.DB_BASE.';host='.DB_HOST.';port='.DB_PORT;
    $dbh = new PDO($dsn, DB_USER, DB_PASS);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

	$ar = array(
		"Master Programmer Level 9000eXtreme!",
		"I know OOP, recursion, etc in a few languages.",
		"I know what variables and loops are in one language",
		"I've never seen any code in any programming language",
		"How to internets? Halp pls."
	);
	
	for ($i=1; $i<=5; $i++) {
		$sql = "SELECT count(*) FROM survey WHERE level=".$i;   
		$statement = $dbh->prepare($sql);
		$statement->execute();	
		$result = $statement->fetchAll();
		echo $result[0][0]." - ".$ar[$i-1]."<br><br>";
	}
	
?>
