<?php ob_start();
session_start();
include('../includes/functions.php');
if($_POST['status'] == 'role'){
$result = $db->joinquery("SELECT access FROM teamroles WHERE roleid='".$_POST['role']."'");
$row =mysqli_fetch_array($result);
echo '<option value="'.$row['access'].'">'.$row['access'].'</option>';
}

if($_POST['status'] == 'user'){
	
	$teamid = $_POST['teamid'];
	
	$result = $db->joinquery("SELECT * FROM Team WHERE team_id = '".$teamid."'");
	$row =mysqli_fetch_assoc($result);
	$row['acess'] = '<option value="'.$row['acess'].'">'.$row['acess'].'</option>';
	$row['profile_pic'] =$gTeamPhotoURL.$row['profile_pic'];
	echo json_encode($row);
	//echo $row['first_name']."@".$row['last_name'].$row['user_name']."@".$row['email']."@".$row['address1']."@".$row['address2']."@".
	//$row[];
	
}