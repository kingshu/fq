<?php
require 'facebook.php';

function fbInit() {
	return new Facebook(array(
	  'appId'  => '627800690577489',
      'secret' => '96cab81b2b573e3c796304ee5905c601',
	));
}

function setSession() {

	if (!isset($_SESSION))
		session_start();

	if (!isset($_SESSION['top_friends'])) {

	    $facebook = fbInit();

	    $user = $facebook->getUser();
	    $accessToken = $facebook->getAccessToken();
	    $logoutUrl = $facebook->getLogoutUrl(array('next'=>'logout.html'));

        $params = array('method' => 'fql.query',
                    'query' => 'SELECT uid, pic_square, name 
                     FROM user 
                     WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 = me()) 
                     ORDER BY mutual_friend_count DESC LIMIT 100');

        $_SESSION['top_friends'] = $facebook->api($params);   
        shuffle($_SESSION['top_friends']);
        
    }

    if (!isset($_SESSION['tfIDs'])) {
    	$_SESSION['tfIDs'] = array();
        foreach ($_SESSION['top_friends'] as $t) {
            array_push($_SESSION['tfIDs'], $t['uid']);
        }
    }


    if (!isset($_SESSION['comments'])) {
  	    $db = mysqlConnector();
		$csvFs = implode("','", $_SESSION['tfIDs']) ;
		$sql = "SELECT * FROM `comments` WHERE `to` IN ('".$csvFs."')";   
		$statement = $db->prepare($sql);    
		$statement->execute();
		$_SESSION['comments'] = $statement->fetchAll();
		shuffle ($_SESSION['comments']);
	}	

	if (!isset($_SESSION['playCount'])) 
		$_SESSION['playCount'] = 1;

	if (!isset($_SESSION['i']))
        $_SESSION['i']=0;

}


function mysqlConnector() {
	define('DB_HOST', getenv('OPENSHIFT_MYSQL_DB_HOST'));
    define('DB_USER',getenv('OPENSHIFT_MYSQL_DB_USERNAME'));
    define('DB_PASS',getenv('OPENSHIFT_MYSQL_DB_PASSWORD'));
    define('DB_BASE','fq');
    define('DB_PORT',getenv('OPENSHIFT_MYSQL_DB_PORT')); 
    $dsn = 'mysql:dbname='.DB_BASE.';host='.DB_HOST.';port='.DB_PORT;
    $dbh = new PDO($dsn, DB_USER, DB_PASS);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
     
    return $dbh;
}
