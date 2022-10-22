<?php ob_start();
session_start();
include('../includes/functions.php');

$getrow = $db->joinquery("SELECT * FROM paint_specifications WHERE locationid='".$_POST['locationid']."' AND colourid='".$_POST['colourid']."'");
if($_POST['status'] == 1){
	
	if(mysqli_num_rows($getrow)==0){
	
	$db->joinquery("INSERT INTO paint_specifications(locationid,agentid,colourid,panelcount,hoursperpanel,times,costperpanel,cost,totalcost)VALUES('".$_POST['locationid']."','".$_POST['agentid']."','".$_POST['colourid']."','".$_POST['pans']."','".$_POST['hoursper']."','".$_POST['times']."','".$_POST['costper']."','".$_POST['cost']."','".$_POST['totalcost']."')");
	
}

else{
	
	$db->joinquery("UPDATE paint_specifications SET panelcount='".$_POST['pans']."',hoursperpanel='".$_POST['hoursper']."',times='".$_POST['times']."',costperpanel='".$_POST['costper']."',cost='".$_POST['cost']."',totalcost='".$_POST['totalcost']."' WHERE locationid='".$_POST['locationid']."' AND colourid='".$_POST['colourid']."'");
}
	
	echo "success";
	
}

else if($_POST['status'] == 2){
	
		if(mysqli_num_rows($getrow)==0){
	
	$db->joinquery("INSERT INTO paint_specifications(locationid,colourid,agentid,selected_status)VALUES('".$_POST['locationid']."','".$_POST['colourid']."','".$_POST['check_status']."')");
	
}

else{
	
	$db->joinquery("UPDATE paint_specifications SET selected_status='".$_POST['check_status']."' WHERE locationid='".$_POST['locationid']."' AND colourid='".$_POST['colourid']."'");
}
echo 'success';

	
	
}

else if($_POST['status'] == 3){
	
		$getListcolors = $db->joinquery("SELECT * FROM colours");
	
	while($rowcolors = mysqli_fetch_assoc($getListcolors))	{
		
		$rowcolors['content'] = "<div class='colour-box'><span style='padding-left:200px;color:#FFF;background-color:#".$rowcolors['colorcode']."'></span>&nbsp;&nbsp;&nbsp;".$rowcolors['colourname']."</div>";
		
		$postcolor[] = $rowcolors;
		
	}
	
	$getconditions = $db->joinquery("SELECT * FROM paneloption_condition");
	
	while($rowcondition = mysqli_fetch_assoc($getconditions))	{
		
		
		$postcondition[] = $rowcondition;
		
	}
	
	echo json_encode(array('colours'=>$postcolor,'conditions'=>$postcondition));
	
	
}
else if($_POST['status'] == 4){
	
	if($_POST['option'] == 'panel'){
		
		$db->joinquery("UPDATE panel SET conditionid='".$_POST['condition']."',colourid='".$_POST['colourid']."' WHERE panelid='".$_POST['panelid']."'");
		
	}
	
	else if($_POST['option'] == 'window'){
		
		$db->joinquery("UPDATE panel SET conditionid='".$_POST['condition']."',colourid='".$_POST['colourid']."' WHERE windowid='".$_POST['windowid']."'");
		
	}
	
	else if($_POST['option'] == 'project'){
		
		$getwindows = $db->joinquery("SELECT window.`windowid` FROM window,room WHERE window.roomid=room.roomid AND room.locationid='".$_POST['locationid']."'");

  while($rowindows=mysqli_fetch_array($getwindows)){
			
			 $windowid[] = $rowindows['windowid'];
			
	}
	
	if(count($windowid)>0){
		
		 $wind_ids = join(',',$windowid);
			
		$db->joinquery("UPDATE panel SET conditionid='".$_POST['condition']."',colourid='".$_POST['colourid']."' WHERE windowid IN($wind_ids)");

		
	}
		
	}
	
		$db->joinquery("UPDATE panel SET conditionid='".$_POST['condition']."',colourid='".$_POST['colourid']."' WHERE windowid IN($wind_ids)");
	
}
?>