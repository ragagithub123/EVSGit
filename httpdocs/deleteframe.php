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

if($_POST['status'] == 'delframe'){

$query="DELETE FROM paneloption_frametype WHERE frametypeid='".$_POST['frameid']."'";
		if(!($query_del = $mysqli->query($query)))
		throw new Exception($mysqli->error);
		
echo $_POST['frameid'];
}

else if($_POST['status'] == 'delcopy'){
	
	$query="DELETE FROM paneloption_style WHERE frametypeid='".$_POST['frameid']."' AND styleid='".$_POST['styleid']."'";
		if(!($query_del = $mysqli->query($query)))
		throw new Exception($mysqli->error);
		
echo $_POST['frameid'];

	
}