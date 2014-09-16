<html>
	
	<head>

		<meta charset="UTF-8" />
		<meta name="google" content="notranslate">
		<meta http-equiv="Content-Language" content="en" />

		<script src="../lib/invitePopper.js"></script>

		<link rel="stylesheet" type="text/css" href="../css/global.css">
		
		<style>
			tr.read {
				background-image:url('../images/translucent.png');
			}
			tr.unread {
				background-color:#D8FFFF;
			}
			div.head {
				font-weight:bold;
			}
			div.note {
				font-style:italic;
			}
			td.reftd {
				right:0px;
				width:40px;
			}
		</style>
	</head>

	<body>
<h2>Notifications</h2>
<?php

	require '../lib/setters.php';
	require '../lib/facebook.php'; 
	$db = mysqlConnector();
	$facebook = fbInit();
	
    $user = $facebook->getUser();
       
	$sql = "SELECT * FROM notifications WHERE user_id='".$user."' ORDER BY id DESC LIMIT 30";
	$statement = $db->prepare($sql);
	$statement->execute();
	$notes = $statement->fetchAll();

	echo "<table border=0 cellpadding=25 width=600><tr><td>";
	echo "<input type='button' value='Clear new notifications' onclick=\"location.href='clearNotifs.php?last=".$notes[0]['id']."&user=".$user."'\"  style='width:250px;height:45px;'/>";
	echo "<td class='reftd'><button><a href='notifications.php'><img height=40 width=40 src='../images/refresh.png'></a></button></td></t>";
	
	foreach ($notes as $n) {
		echo "<tr class=".( ($n['read']==0)?"unread":"read" )."><td colspan='2'>";
		echo "<div class='head'>" .$n['header']. "</div>";
		echo "&emsp;<div class='note'>\"" .$n['notif']. "\"</div>";
		echo "</td></tr>";
	}
	echo "</table>";
?>

	</body>
</html>
