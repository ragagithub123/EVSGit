<?php
	
# includes
include("files/constants.php");
include("files/library/passwordlib.php");

# connect to database
$mysqli = new mysqli($gDBHost, $gDBUser, $gDBPassword, $gDBName);
if($mysqli->connect_errno)
	throw new Exception($mysqli->connect_error, $mysqli->connect_errno);
$mysqli->set_charset('UTF');
	
session_start(); 
$error=''; 

if(count($_POST) > 0) {
	if($_POST['username'] == '' || $_POST['password'] == '')
		$error = "<div class=\"alert alert-danger\" role=\"alert\">Incorrect username or password</div>";
	else {
		$querySQL = "SELECT * FROM administrator WHERE username = '". $mysqli->real_escape_string(stripslashes(trim($_POST['username']))). "'";
		if(!($query = $mysqli->query($querySQL)))
			throw new Exception($mysqli->error);
		
		if($query->num_rows == 1) {
			$admin = $query->fetch_assoc();
			if(HashPassword($_POST['password'], $admin['passwordsalt']) == $admin['passwordhash']) {
				$_SESSION['session'] = $admin['adminid'];
				Header("Location: agents.php");
				$mysqli->close();
				exit();
			}
		}
		else
			$error = "<div class=\"alert alert-danger\" role=\"alert\">Incorrect username or password</div>";
	}
}

# render page
include("files/templates/signin.htm");

# done
$mysqli->close();
