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

# get list of window types
$querySQL = "SELECT * FROM window_type ORDER BY name";
if(!($query = $mysqli->query($querySQL)))
	throw new Exception($mysqli->error);
$windowList = '';
while($window = $query->fetch_assoc()) {
	$thumbnail = (file_exists($gWindowTypePhotoDir."/".$window['windowtypeid'].".png")) ? "<img src=\"". $gWindowTypePhotoURL."/".$window['windowtypeid'].".png\" class=\"window-thumbnail\">" : '-';
	$windowList .= "<tr><td>". htmlspecialchars($window['name']). "</td> <td>$thumbnail<td>". $window['numpanels']. "</td> <td style=\"text-align: right\"><a href=\"windowtype.php?id=". $window['windowtypeid']. "\">Edit</a></td></tr>\n";
}
$mysqli->close();
	
$pageContent = "files/templates/windowtypes.htm";
include("files/templates/templateadmin.htm");	
