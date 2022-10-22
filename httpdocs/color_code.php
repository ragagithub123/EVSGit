<?php
	
# includes
include("files/constants.php");
include("files/library/session.php");
include("files/library/formlib.php");
include("files/library/common.php");

if(isset($_REQUEST['cancel'])) {
	header("Location: ". $_SERVER['PHP_SELF']."?type=". $_REQUEST["type"]);
	exit(0);
}

# connect to database
$mysqli = new mysqli($gDBHost, $gDBUser, $gDBPassword, $gDBName);
if($mysqli->connect_errno)
	throw new Exception($mysqli->connect_error, $mysqli->connect_errno);
$mysqli->set_charset('UTF');

$adminId = SessionCheck($mysqli);

$querySQL = "SELECT colorcode FROM sapcercolor WHERE colourid=".$_POST['colourid']."";
		if(!($query = $mysqli->query($querySQL)))
			throw new Exception($mysqli->error);
		$option = $query->fetch_assoc();
		echo $option['colorcode'];
		//$query->free();