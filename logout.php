<?php session_start();
unset($_SESSION['agentid']);
 session_destroy();
	
	header('location:index.php');
?>