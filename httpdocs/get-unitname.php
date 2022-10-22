<?php
	
# includes
include("files/constants.php");
include("files/library/session.php");
include("files/library/formlib.php");
include("files/library/common.php");


# connect to database
$mysqli = new mysqli($gDBHost, $gDBUser, $gDBPassword, $gDBName);
if($mysqli->connect_errno)
	throw new Exception($mysqli->connect_error, $mysqli->connect_errno);
$mysqli->set_charset('UTF');

$adminId = SessionCheck($mysqli);
$gtetTag="SELECT unitTag FROM Units WHERE unitName='".$_POST['unitname']."'";
if(!($query = $mysqli->query($gtetTag)))
 throw new Exception($mysqli->error);
$row = $query->fetch_assoc();
echo $row['unitTag'];
