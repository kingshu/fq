<html>
<head>  
</head>
<body>
<pre>
<?php
    
    require '../lib/setters.php';  
     
    $facebook = fbInit();
    
    $db = mysqlConnector();

    if ($_REQUEST['anon']=="Yes") 
       $sql = "INSERT INTO comments VALUES ('0','".$_REQUEST['to']."','".$_REQUEST['comment']."')";
    
    else 
       $sql = "INSERT INTO comments VALUES ('".$_REQUEST['from']."','".$_REQUEST['to']."','".$_REQUEST['comment']."')";
    
    
    $statement = $db->prepare($sql);
    $statement->execute();
   
    if ($_REQUEST['anon']=="Yes") 
        $sql = "UPDATE users SET coins = coins-10 WHERE user_id = '".$_REQUEST['from']."'";  
	
    else 
        $sql = "UPDATE users SET coins = coins+2 WHERE user_id = '".$_REQUEST['from']."'";  
	
    
    $statement = $db->prepare($sql);    
    $statement->execute();
   
    if ($_REQUEST['anon']=="Yes") 
        $sql = "INSERT INTO notifications (user_id, header, notif, hasBeenRead) VALUES ('".$_REQUEST['to']."','Someone anonymous commented on you', '".$_REQUEST['comment']."', '0')";  
	
    else {
		$fromName = $facebook->api('/'.$_REQUEST['from']);
        $sql = "INSERT INTO notifications (user_id, header, notif, hasBeenRead) VALUES ('".$_REQUEST['to']."','".$fromName['name']." commented on you', '".$_REQUEST['comment']."', '0')";
	}
    
    
 //   print_r ($sql);
    $statement = $db->prepare($sql);    
    $statement->execute();
    
?>
</pre>
</body>
</html>
