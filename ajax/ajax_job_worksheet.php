<?php ob_start();

session_start();

include('../includes/functions.php');

$locdetails = $db->joinquery("SELECT * FROM worksheet_settings WHERE locationid ='".$_POST['locationid']."' AND type='".$_POST['type']."'");

if(mysqli_num_rows($locdetails)==0){
	
	$db->joinquery("INSERT INTO worksheet_settings(locationid,agentid,type)VALUES(".$_POST['locationid'].",".$_SESSION['agentid'].",'".$_POST['type']."')");
	
}


if($_POST['status'] == 'settings'){
	
	
	
	$db->joinquery("UPDATE worksheet_settings SET ".$_POST['field']." = '".$_POST['value']."' WHERE locationid ='".$_POST['locationid']."' AND type='".$_POST['type']."'");
	
	
}


if($_POST['status'] == 'workflow_array'){
	
	
	$exp_room = explode(',',$_POST['roomid']);
	
	$exp_panel = explode(',',$_POST['panelid']);
	
	for ($i=0;$i<count($exp_room);$i++){
		
		$workflow = $db->joinquery("SELECT * FROM worksheet_workflow WHERE locationid ='".$_POST['locationid']."' AND roomid='".$exp_room[$i]."' AND panelid='".$exp_panel[$i]."' AND type='".$_POST['type']."'");
		
		if(mysqli_num_rows($workflow)==0){
			
			
			$db->joinquery("INSERT INTO worksheet_workflow(agentid,locationid,roomid,panelid,seals,type)VALUES(".$_SESSION['agentid'].",'".$_POST['locationid']."','".$exp_room[$i]."','".$exp_panel[$i]."','".$_POST['checked']."','".$_POST['type']."')");
			
			
		}
		
		
		else{
			
			
			$db->joinquery("UPDATE worksheet_workflow SET seals = '".$_POST['checked']."' WHERE locationid='".$_POST['locationid']."' AND roomid='".$exp_room[$i]."' AND panelid='".$exp_panel[$i]."'");
			
			
		}
		
	}
	
}


if($_POST['status'] == 'workflow'){
	
	
	$workflow = $db->joinquery("SELECT * FROM worksheet_workflow WHERE locationid ='".$_POST['locationid']."' AND roomid='".$_POST['roomid']."' AND panelid='".$_POST['panelid']."' AND type='".$_POST['type']."'");
	
	if(mysqli_num_rows($workflow)==0){
		
		
		$db->joinquery("INSERT INTO worksheet_workflow(locationid,roomid,panelid,seals,type)VALUES('".$_POST['locationid']."','".$_POST['roomid']."','".$_POST['panelid']."','".$_POST['checked']."','".$_POST['type']."')");
		
		
	}
	
	
	else{
		
		
		$db->joinquery("UPDATE worksheet_workflow SET seals = '".$_POST['checked']."' WHERE locationid='".$_POST['locationid']."' AND roomid='".$_POST['roomid']."' AND panelid='".$_POST['panelid']."'");
		
		
	}
	
	
	
	
}


if($_POST['status'] == 'worksheet'){
	
	if($_POST['date']!='0000-00-00')
	
	$date =date('Y-m-d',strtotime($_POST['date']));
	
	else
	
	$date =$_POST['date'];
	
	$getpreviouswork = $db->joinquery("SELECT SUM(seals) AS sealssum FROM job_measure_worksheet WHERE locationid='".$_POST['locationid']."' AND type='".$_POST['type']."'");
	
	
	if(mysqli_num_rows($getpreviouswork)>0){
		
		
		while($rowprev = mysqli_fetch_array($getpreviouswork)){
		
			$sealtot = $rowprev['sealssum'];
			
			
		}
		
		
	}
	
	
	else{
		
		$sealtot = 0;
		
		
	}
	
	if($date != '0000-00-00'){
	
$newseals = $_POST['seals'] - $sealtot;

$totseals = $newseals+$sealtot;
}

else{
	
	$getrowdata = $db->joinquery("SELECT * FROM job_measure_worksheet WHERE locationid='".$_POST['locationid']."' AND week='".$_POST['week']."' AND type='".$_POST['type']."'");
	
	$rowdata  = mysqli_fetch_array($getrowdata);
	
	

$newseals = 0;


$totseals = ($sealtot-$rowdata['seals']);
	
	
	
}
	

	
	$db->joinquery("DELETE FROM job_measure_worksheet WHERE locationid='".$_POST['locationid']."' AND week='".$_POST['week']."' AND type='".$_POST['type']."'");
	
		
			$db->joinquery("INSERT INTO `job_measure_worksheet`( `locationid`, `agentid`,`week`,`dates`,`seals`,`type`)VALUES('".$_POST['locationid']."','".$_SESSION['agentid']."','".$_POST['week']."','".$date."','".$newseals."','".$_POST['type']."')");

 	$array = array('seals'=>$newseals,'totseals'=>$totseals);

	
	echo json_encode($array);
	
}
