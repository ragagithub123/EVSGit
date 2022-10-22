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
$querySQL = "SELECT * FROM agent ORDER BY firstname, lastname";
if(!($query = $mysqli->query($querySQL)))
	throw new Exception($mysqli->error);
$agentList = '';
while($agent = $query->fetch_assoc()) {
	$agentList .= "<tr><td>". htmlspecialchars($agent['businessname'])."</td><td>". htmlspecialchars($agent['firstname'].' '.$agent['lastname']). "</td> <td>".$agent['status']."</td><td>".$agent['city']."</td>";
	if($agent['enabled']==1)
	{
		 $agentList .= "<td><i class='fa fa-check' aria-hidden='true'></i></td>";
	}
	else
	{
		 $agentList .= "<td><i class='fa fa-times' aria-hidden='true'></i></td>";
	}
	$agentList .="<td style=\"text-align: right\"><a href=\"agent.php?id=". $agent['agentid']. "\">Edit</a></td></tr>\n";
}
$mysqli->close();
	
$pageContent = "files/templates/agents.htm";
include("files/templates/templateadmin.htm");	
