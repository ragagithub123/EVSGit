<?php

ob_start();
session_start();
// include('../includes/functions.php');
// $get_user = $connection->query("SELECT * FROM agent WHERE email='" . $_POST['username'] . "'");
$connection = mysqli_connect('localhost', 'admin_evsapp', '&Dl5w9m1', 'db.evsapp');
$get_user = mysqli_query($connection, "SELECT * FROM agent WHERE email='" . $_POST['username'] . "'");
// echo "SELECT * FROM agent WHERE email='" . $_POST['username'] . "'"; die();
// echo mysqli_num_rows($get_user);
if (mysqli_num_rows($get_user) > 0) {
	$row = mysqli_fetch_assoc($get_user);
	// echo $row['agentid']; die();
	if ($row['passwordhash'] != hash('sha256', $row['passwordsalt'] . $_POST['password'])) {
		echo "Invalid password";
	} else {
		// $db->joinquery("UPDATE agent SET activity='1' WHERE agentid='".$row['agentid']."'");
		$_SESSION['agentid'] = $row['agentid'];
		$_SESSION['username'] = $row['email'];
		$_SESSION['timestamp'] = time();
		echo 'success';
	}
} else {
	echo "Username doesn't exist";
}
