<?php

//require 'src/facebook.php';
require 'lib/setters.php';
$db = mysqlConnector();

$facebook = fbInit();

$loginUrl = $facebook->getLoginUrl(array('scope' => 'user_status,publish_actions,user_photos,friends_photos'));
// Get User ID
$user = $facebook->getUser();
$accessToken = $facebook->getAccessToken();

if ($user) {
  try {
    // Proceed knowing you have a logged in user who's authenticated.
    $user_profile = $facebook->api('/me');
    $sql = "SELECT * FROM users WHERE user_id = '".$user."'";
    $statement = $db->prepare($sql);
    $statement->execute(); 
    $result = $statement->fetchAll();
    if ( count($result)==0 ) {
		
		// Create new user in the table
		$sql = "INSERT INTO users (user_id, coins) VALUES ('".$user."', 10)";		
		$statement = $db->prepare($sql);
		$statement->execute(); 
		
		// Give 50 coins to everyone who invited this guy
		$sql = "UPDATE users SET coins=coins+50 WHERE user_id IN (SELECT from FROM requests WHERE to='".$user."')";		
		$statement = $db->prepare($sql);
		$statement->execute(); 
		
		// Get all inviters
		$sql = "SELECT from FROM requests WHERE to='".$user."'";		
		$statement = $db->prepare($sql);
		$statement->execute(); 
		$inviters = $statement->fetchAll();
		
		// Push notifications
		foreach ($inviters as $inv) {
			$sql = "INSERT INTO notifications (user_id, header, notif, read) VALUES ('".$inv['from']."', 'You got 50 coins!', '".$user_profile['name']." joined after you invited ".( ($user_profile['gender']=='male')?"him":"her" ).".', '0')";		
			$statement = $db->prepare($sql);
			$statement->execute(); 
		}
	}
  } catch (FacebookApiException $e) {
    error_log($e);
    $user = null;
  }
}

// Login or logout url will be needed depending on current user state.
if ($user) {
  $logoutUrl = $facebook->getLogoutUrl(array('next'=>'scripts/logout.html'));
} else {
  $loginUrl = $facebook->getLoginUrl(array('scope' => 'user_status,publish_actions,user_photos,friends_photos'));
}


?>
<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
    <title>Who's that friend?</title> 
    <link rel="stylesheet" type="text/css" href="css/fonts/fonts.css">
    <link rel="stylesheet" type="text/css" href="css/index.css">
    <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
	<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.js"></script> 
	<script src="http://malsup.github.com/jquery.form.js"></script> 
	<script>
  		$(document).ready(function() {

  			$('#topBar').slideDown(1000);
    		$('#bottomBar').slideDown(1000);
    		$('.floatingButton').animate({
	    			top: "50%",
	    			opacity: "1.0"
	    		}, 1000);
		});
  	</script>
  </head>
  <body>
        <div id="wrapper">
        	<div id="topBar" style='display:none'>
		    	<span class="title">who's that friend?</span><br><br>
		    	<span class="slogan">find out who's saying what about whom! say something about your friends! see who said what about you.</span>
			</div>
            <?php if ($user): ?>
		<div class="floatingButton" style="opacity:0.5; top:75%;">
			<a href='scripts/container.php?load=play&logouturl=<?php echo urlencode($logoutUrl) ?>'>play</a>
		</div>
              <!--<input type="button" value="PLAY"    onclick="location.href='scripts/container.php?load=play&logouturl=<?php echo urlencode($logoutUrl) ?>'"    style="width:100px;height:45px;"/>-->
<!--
              &emsp;
              <input type="button" value="COMMENT" onclick="location.href='http://fq-kingshu.rhcloud.com/scripts/comment.php'" style="width:100px;height:45px;"/>
-->               
              <!--<a href="<?php echo $logoutUrl; ?>">Logout</a>-->
            <?php else: ?>
                <div class="floatingButton">
			<a href='<?php echo $loginUrl; ?>'>login with facebook</a>
		</div>
            <?php endif ?>
            <?php
				if (isset($_REQUEST['state']) && isset($_REQUEST['code'])) 
					echo "<script>window.close();window.opener.location.reload();</script>";
            ?>
        <div id="bottomBar" style="display:none;">
			<span class="footer">Copyright 2013 Kingshu, LLC.</span>
		</div>
	</div>
  </body>

</html>
