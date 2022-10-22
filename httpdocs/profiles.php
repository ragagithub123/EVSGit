<?php
	
# includes
include("files/constants.php");
include("files/library/session.php");
include("files/library/common.php");

# connect to database
$mysqli = new mysqli($gDBHost, $gDBUser, $gDBPassword, $gDBName);
if($mysqli->connect_errno)
	throw new Exception($mysqli->connect_error, $mysqli->connect_errno);
$mysqli->set_charset('UTF');

$adminId = SessionCheck($mysqli);

# get list of agents
$querySQL = "SELECT * FROM profiles ORDER BY profilename";
if(!($query = $mysqli->query($querySQL)))
	throw new Exception($mysqli->error);
$profileList = '';
while($profile = $query->fetch_assoc()) {
	$profileList .= "<tr><td>". htmlspecialchars($profile['profilename'])."</td>";
	if(file_exists($gProfilePhotoDir.$profile['profilecode'].".png"))
	{
		$profileList .="<td><img src=\"". $gProfilePhotoURL.$profile['profilecode'].".png?". time(). "\" class=\"img-responsive\" style=\"width: 100px; height; 100px;\"></td>";
	}
	else
	{
		  $profileList .="<td></td>";
	}
				
	$profileList .="<td>". htmlspecialchars($profile['profilecode']). "</td> <td>".$profile['pricelm']."</td>";
	
	$profileList .="<td style=\"text-align: right\"><a href=\"profile.php?id=". $profile['profileid']. "\">Edit</a></td></tr>\n";
}
$mysqli->close();
	
$pageContent = "files/templates/profiles.htm";
include("files/templates/templateadmin.htm");	
