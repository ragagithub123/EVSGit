<?php

function SessionCheck($mysqli) {
	session_start();

	if(!isset($_SESSION['session']))
		SessionRedirectSignin($mysqli);

	$adminId = intval($_SESSION['session']);
	if($adminId <= 0)
		SessionRedirectSignin($mysqli);
	
	$querySQL = "SELECT * FROM administrator WHERE adminid = ". $adminId;
	if(!($query = $mysqli->query($querySQL)))
		throw new Exception($mysqli->error);
	$admin = $query->fetch_assoc();
	if($admin['adminid'] != $adminId)
		SessionRedirectSignin($mysqli);	
	$query->close();

	return $adminId;
}

function SessionRedirectSignin($mysqli) {
	header("Location: signin.php");
	$mysqli->close();
	exit(0);
}
