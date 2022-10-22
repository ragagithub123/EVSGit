<?php
include('includes/functions.php');
$getsearchtext=$db->joinquery("SELECT `agentid`,`unitnum`,`street`,`suburb`,`city` FROM `agent`");
while($row_text=mysqli_fetch_array($getsearchtext)){
	$newloc=$row_text['unitnum'].",".$row_text['street'].",".$row_text['suburb'].",".$row_text['city'];
	$location=str_replace(' ', '%20', $newloc);
$res=file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address='".$location."'&key=AIzaSyD91uB2dG7Lyj4xOWJwxT-lq9uQ0FtwU0o");
$result_arr=json_decode($res);
$lat=$result_arr->results[0]->geometry->location->lat;
$lng=$result_arr->results[0]->geometry->location->lng;
$db->joinquery("UPDATE agent SET latitude='$lat',longitude='$lng' WHERE agentid='".$row_text['agentid']."'");
echo $row_text['agentid']."@".$lat."@".$lng."<br>";
}
