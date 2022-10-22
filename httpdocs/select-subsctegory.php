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

if($_POST['type'] == 1){

	$queryfCategory = "SELECT * FROM famecategory WHERE material_tag = '".$_POST['selectid']."'";
	
	if(!($query = $mysqli->query($queryfCategory)))
		throw new Exception($mysqli->error);
	$fcatgories = array();
	while($fcatgory = $query->fetch_assoc())
		
		array_push($fcatgories, $fcatgory);
		
	$query->free();
	
	
	$queryframetypes = "SELECT * FROM  paneloption_frametype WHERE category ='".$fcatgories[0]['famecategoryid']."'";
	
	
	if(!($query = $mysqli->query($queryframetypes)))
		throw new Exception($mysqli->error);
	$frametypescat = array();
	while($framecatgory = $query->fetch_assoc())
		
		array_push($frametypescat, $framecatgory);
		
	$query->free();
	
	$array = array("subscat"=>$fcatgories,"frametypes"=>$frametypescat);
	
	echo json_encode($array);
}

else if($_POST['type'] == 2){
	
	$queryframetypes = "SELECT * FROM  paneloption_frametype WHERE category='".$_POST['selectid']."'";
	
	
	if(!($query = $mysqli->query($queryframetypes)))
		throw new Exception($mysqli->error);
	$frametypescat = array();
	while($framecatgory = $query->fetch_assoc())
		
		array_push($frametypescat, $framecatgory);
		
	$query->free();
	
	
	echo json_encode($frametypescat);
	
}