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

# get list of colours
$querySQL = "SELECT * FROM colours ORDER BY colourname";
if(!($query = $mysqli->query($querySQL)))
	throw new Exception($mysqli->error);
$colourList = '';
while($colour = $query->fetch_assoc()) {
	
				
	$colourList .="<td>".$colour['colourname']."</td>";
	
	$colourList .="<td><div style='width:70px; height:15px; border:solid;background-color:#".$colour['colorcode'].";border-color:#".$colour['colorcode']."'></div></td>";
	
	$colourList .="<td>".$colour['colorcode']."</td>";
	
	$colourList .="<td style=\"text-align: right\"><a href=\"colour.php?id=". $colour['colourid']. "\">Edit</a></td></tr>\n";
}
$mysqli->close();
	
$pageContent = "files/templates/colours.htm";
include("files/templates/templateadmin.htm");	
