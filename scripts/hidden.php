<?php
	if (!isset($_SESSION))
		session_start();
	// echo "<h3>It is ".$_SESSION['hidden']['Rname']."</h3><br> <img src='".$_SESSION['hidden']['Rimage']."'> ";
	echo $_SESSION['hidden']['Rname'] . "~!|!~" . $_SESSION['hidden']['Rimage'];
?>
