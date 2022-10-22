<?php ob_start();

session_start();

include('../includes/functions.php');


if($_POST['status'] == 'settings'){
	
	if($_POST['field'] == 'date')
	
	$value = date('Y-m-d',strtotime($_POST['value']));
	
	else
	
	$value = $_POST['value'];
	
	$settings = $db->joinquery("SELECT * FROM tasktoolsettings WHERE agentid='".$_SESSION['agentid']."' AND locationid ='".$_POST['locationid']."'");
	
	if(mysqli_num_rows($settings) == 0)
	
	$db->joinquery("INSERT INTO tasktoolsettings(agentid,locationid,".$_POST['field'].")VALUES('".$_SESSION['agentid']."','".$_POST['locationid']."','".$value."')");
	
	else
	
	$db->joinquery("UPDATE tasktoolsettings SET ".$_POST['field']." = '".$value."' WHERE locationid ='".$_POST['locationid']."' AND agentid='".$_SESSION['agentid']."'");
	
	echo 'success';
	
}

if($_POST['status'] == 'tools'){
	
	
 if($_POST['check'] == 0)	
	
	$db->joinquery("DELETE FROM tasktoolflow WHERE locationid='".$_POST['locationid']."' AND toolid='".$_POST['toolid']."' AND tasktoolid='".$_POST['tasktoolid']."' AND type='".$_POST['type']."' AND category='".$_POST['category']."'");
	else
	
	$db->joinquery("INSERT INTO tasktoolflow(locationid,agentid,toolid,tasktoolid,type,category)VALUES('".$_POST['locationid']."','".$_SESSION['agentid']."','".$_POST['toolid']."','".$_POST['tasktoolid']."','".$_POST['type']."','".$_POST['category']."')");
	
	echo $_POST['check'];
}
if($_POST['status'] == 'materials'){
	
	
 if($_POST['check'] == 0)	
	
	$db->joinquery("DELETE FROM taskmaterialflow WHERE locationid='".$_POST['locationid']."'  AND materialid='".$_POST['materialid']."' AND type='".$_POST['type']."' AND category='".$_POST['category']."'");
	else
	
	$db->joinquery("INSERT INTO taskmaterialflow(locationid,agentid,materialid,type,category)VALUES('".$_POST['locationid']."','".$_SESSION['agentid']."','".$_POST['materialid']."','".$_POST['type']."','".$_POST['category']."')");
	
	echo $_POST['check'];
}
