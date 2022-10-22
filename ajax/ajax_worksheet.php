<?php ob_start();

session_start();

include('../includes/functions.php');

$locdetails = $db->joinquery("SELECT * FROM worksheet WHERE locationid ='".$_POST['locationid']."'");

$getpjtid = $db->joinquery("SELECT projectid FROM location WHERE locationid ='".$_POST['locationid']."'");
$rowpjtid = mysqli_fetch_array($getpjtid);


if(mysqli_num_rows($locdetails)==0){
	
	$db->joinquery("INSERT INTO worksheet(locationid,agentid)VALUES(".$_POST['locationid'].",".$_SESSION['agentid'].")");
	
}


if($_POST['status'] == 'datesettings'){
	
	
	$date =date('Y-m-d',strtotime($_POST['date']));
	
	
	$db->joinquery("UPDATE worksheet SET ".$_POST['field']." = '".$date."' WHERE locationid ='".$_POST['locationid']."'");
	
	echo $_POST['field']."@".$_POST['date']."@".$_POST['locationid'];
	
}


if($_POST['status'] == 'worksheet'){
	
	if($_POST['date']!='0000-00-00')
	
	$date =date('Y-m-d',strtotime($_POST['date']));
	
	else
	
	$date =$_POST['date'];
	
	if(!empty($rowpjtid['projectid']) && ($rowpjtid['projectid']!=NULL))
	
	$getpreviouswork = $db->joinquery("SELECT SUM(glass) AS glasssum,SUM(kit) AS kitsum,SUM(assemble) AS assemblesum,SUM(install) AS installsum, SUM(prep) AS prepsum, SUM(seals) AS sealssum FROM weekely_worksheet WHERE locationid='".$_POST['locationid']."' AND projectid='".$rowpjtid['projectid']."'");
	
	else

	$getpreviouswork = $db->joinquery("SELECT SUM(glass) AS glasssum,SUM(kit) AS kitsum,SUM(assemble) AS assemblesum,SUM(install) AS installsum, SUM(prep) AS prepsum, SUM(seals) AS sealssum FROM weekely_worksheet WHERE locationid='".$_POST['locationid']."'");
	
	
	if(mysqli_num_rows($getpreviouswork)>0){
		
		
		while($rowprev = mysqli_fetch_array($getpreviouswork)){
			
			
			$glasstot = $rowprev['glasssum'];
			
			
			$kittot = $rowprev['kitsum'];
			
			
			$assemtot = $rowprev['assemblesum'];
			
			
			$insttot = $rowprev['installsum'];
			
			
			$preptot = $rowprev['prepsum'];
			
			
			$sealtot = $rowprev['sealssum'];
			
			
		}
		
		
	}
	
	
	else{
		
		
		$glasstot = 0;
		
		
		$kittot = 0;
		
		
		$assemtot = 0;
		
		
		$insttot = 0;
		
		
		$preptot = 0;
		
		
		$sealtot = 0;
		
		
	}
	
	if($date != '0000-00-00'){
	
$newglass = $_POST['glass'] - $glasstot;

$newkit  =  $_POST['kit'] - $kittot;

$newassemble = $_POST['assemble'] - $assemtot;

$newinstall  = $_POST['install'] - $insttot;

$newprep  = $_POST['prep'] - $preptot;

$newseals = $_POST['seals'] - $sealtot;

$totglass = $newglass+$glasstot;

$totkit = $newkit+$kittot;

$totassemble = $newassemble+$assemtot;

$totinstall = $newinstall+$insttot;

$totprep = $newprep+ $preptot;

$totseals = $newseals+$sealtot;
}

else{
	
	if(!empty($rowpjtid['projectid']) && ($rowpjtid['projectid']!=NULL))
	
	$getrowdata = $db->joinquery("SELECT * FROM weekely_worksheet WHERE locationid='".$_POST['locationid']."' AND week='".$_POST['week']."'  AND projectid='".$rowpjtid['projectid']."'");

	else

	$getrowdata = $db->joinquery("SELECT * FROM weekely_worksheet WHERE locationid='".$_POST['locationid']."' AND week='".$_POST['week']."'");
	
	$rowdata  = mysqli_fetch_array($getrowdata);
	
	$newglass = 0;

$newkit  =  0;

$newassemble = 0;

$newinstall  = 0;

$newprep  = 0;

$newseals = 0;

$totglass = ($glasstot-$rowdata['glass']);

$totkit = ($kittot-$rowdata['kit']);

$totassemble = ($assemtot-$rowdata['assemble']);

$totinstall = ($insttot-$rowdata['install']);

$totprep =  ($preptot-$rowdata['prep']);

$totseals = ($sealtot-$rowdata['seals']);
	
	
	
}
	
	$totalproprice =array();
	
	if(!empty($rowpjtid['projectid']) && ($rowpjtid['projectid']!=NULL))
	
	$getPanel =$db->joinquery("SELECT sum(window_type.numpanels) AS panelcount FROM room,window,window_type WHERE window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid AND window.selected_product!='HOLD' AND window.projectid='".$rowpjtid['projectid']."' AND room.locationid=".$_POST['locationid']."");

	else

	$getPanel =$db->joinquery("SELECT sum(window_type.numpanels) AS panelcount FROM room,window,window_type WHERE window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid AND window.selected_product!='HOLD'  AND room.locationid=".$_POST['locationid']."");
	
	$rowPanel = mysqli_fetch_array($getPanel);
	
	$totaljobPanels = $rowPanel['panelcount'];

	if(!empty($rowpjtid['projectid']) && ($rowpjtid['projectid']!=NULL))
	
	$get_window = $db->joinquery("SELECT window.windowid,window.selected_product,window.costsdg,window.costmaxe,window.costxcle,window.costevsx2,window.costevsx3 FROM room,window WHERE window.roomid=room.roomid AND window.selected_product!='HOLD' AND window.projectid='".$rowpjtid['projectid']."' AND room.locationid=".$_POST['locationid']." GROUP BY window.windowid ORDER BY room.name ASC");

	else

	$get_window = $db->joinquery("SELECT window.windowid,window.selected_product,window.costsdg,window.costmaxe,window.costxcle,window.costevsx2,window.costevsx3 FROM room,window WHERE window.roomid=room.roomid AND window.selected_product!='HOLD'  AND room.locationid=".$_POST['locationid']." GROUP BY window.windowid ORDER BY room.name ASC");
	
	
	
	while($rowpro_window = mysqli_fetch_array($get_window)){
		
		
		if($rowpro_window['selected_product']=="SGU" || $rowpro_window['selected_product']=="SDG"){
			
			
			$totalproprice[]= $rowpro_window['costsdg'];
			
			
		}
		
		else if($rowpro_window['selected_product']=="IGUX2" || $rowpro_window['selected_product']=="MAXe"){
			
			
			$totalproprice[]= $rowpro_window['costmaxe'];
			
			
		}
		
		
		else if($rowpro_window['selected_product']=="IGUX3" || $rowpro_window['selected_product']=="XCLe"){
			
			
			$totalproprice[]= $rowpro_window['costxcle'];
			
			
		}
		
		
		else if($rowpro_window['selected_product']=="EVSx2"){
			
			
			$totalproprice[]= $rowpro_window['costevsx2'];
			
			
			
		}
		
		
		else if($rowpro_window['selected_product']=="EVSx3"){
			
			
			$totalproprice[]= $rowpro_window['costevsx3'];
			
			
			
		}
		
		
		
		
		
	}
	
	
	if(count($totalproprice)>0)
	
	$jobSum = array_sum($totalproprice);
	
	
	else
	
	$jobSum =0;
	
	
	$percentGlass = 0.25;
	
	
	$percentKits   = 0.12;
	
	
	$percentAssembled = 0.13;
	
	
	$percentInstalled  = 0.25;
	
	
	$percentSeals   = 0;
	
	
	$percentPrepped    =  0.25;
	
	
	
	
	$jobGlassVal=(((((100/$totaljobPanels)*$totglass)*$percentGlass)*0.01)*$jobSum);
	
	$percentGlassComplete=(((100/$jobSum)*$jobGlassVal)*0.01);
	
	
	$jobKitsVal =(((((100/$totaljobPanels)*$totkit)*$percentKits)*0.01)*$jobSum);
	
	$percentKitsComplete=(((100/$jobSum)*$jobKitsVal)*0.01);
	
	
	$jobAssembledVal=(((((100/$totaljobPanels)*$totassemble)*$percentAssembled)*0.01)*$jobSum);
	
	$percentAssembledComplete=(((100/$jobSum)*$jobAssembledVal)*0.01);
	
	
	$jobPreppedVal=(((((100/$totaljobPanels)*$totprep)*$percentPrepped)*0.01)*$jobSum);
	
	$percentPreppedComplete=(((100/$jobSum)*$jobPreppedVal)*0.01);
	
	
	$jobInstalledVal=(((((100/$totaljobPanels)*$totinstall)*$percentInstalled)*0.01)*$jobSum);
	
	$percentInstalledComplete=(((100/$jobSum)*$jobInstalledVal)*0.01);
	
	
	$jobSealedVal=(((((100/$totaljobPanels)*$totseals)*$percentSeals)*0.01)*$jobSum);
	
	$percentSealsComplete=(((100/$jobSum)*$jobSealedVal)*0.01);
	
	
	$percentJobComplete=round(($percentGlassComplete+$percentKitsComplete+$percentAssembledComplete+$percentPreppedComplete+$percentInstalledComplete+$percentSealsComplete),2);
	
	
	if(!empty($rowpjtid['projectid']) && ($rowpjtid['projectid']!=NULL)){

		$db->joinquery("DELETE FROM weekely_worksheet WHERE locationid='".$_POST['locationid']."' AND week='".$_POST['week']."' AND projectid='".$rowpjtid['projectid']."'");
	
		
	$db->joinquery("INSERT INTO `weekely_worksheet`( `locationid`,`projectid`,`agentid`,`week`,`dates`,`glass`, `kit`, `assemble`, `install`, `prep`, `seals`,`percentcomplete`)VALUES('".$_POST['locationid']."','".$rowpjtid['projectid']."','".$_SESSION['agentid']."',
		'".$_POST['week']."','".$date."','".$newglass."','".$newkit."','".$newassemble."','".$newinstall."','".$newprep."','".$newseals."','$percentJobComplete')");


	}

	else{

		$db->joinquery("DELETE FROM weekely_worksheet WHERE locationid='".$_POST['locationid']."' AND week='".$_POST['week']."'");
	
		
		$db->joinquery("INSERT INTO `weekely_worksheet`( `locationid`,`agentid`,`week`,`dates`,`glass`, `kit`, `assemble`, `install`, `prep`, `seals`,`percentcomplete`)VALUES('".$_POST['locationid']."','".$_SESSION['agentid']."',
			'".$_POST['week']."','".$date."','".$newglass."','".$newkit."','".$newassemble."','".$newinstall."','".$newprep."','".$newseals."','$percentJobComplete')");
	}
	
 	
	$array = array('glass'=>$newglass,'kit'=>$newkit,'assemble'=>$newassemble,'install'=>$newinstall,'prep'=>$newprep,'seals'=>$newseals,'totglass'=>$totglass,'totkit'=>$totkit,'totassemble'=>$totassemble,'totinstall'=>$totinstall,'totprep'=>$totprep,'totseals'=>$totseals,'jobcomplete'=>($percentJobComplete*100)."%");

		
	
	
	
	echo json_encode($array);
	
}

if($_POST['status'] == 'workflow'){
	
	
	$workflow = $db->joinquery("SELECT * FROM workflow WHERE locationid ='".$_POST['locationid']."' AND roomid='".$_POST['roomid']."' AND panelid='".$_POST['panelid']."'");
	
	if(mysqli_num_rows($workflow)==0){
		
		
		$db->joinquery("INSERT INTO workflow(locationid,roomid,panelid,".$_POST['field'].")VALUES('".$_POST['locationid']."','".$_POST['roomid']."','".$_POST['panelid']."','".$_POST['checked']."')");
		
		
	}
	
	
	else{
		
		
		$db->joinquery("UPDATE workflow SET ".$_POST['field']." = '".$_POST['checked']."' WHERE locationid='".$_POST['locationid']."' AND roomid='".$_POST['roomid']."' AND panelid='".$_POST['panelid']."'");
		
		
	}
	
	
	echo $_POST['field'];
	
	
}


if($_POST['status'] == 'workflow_array'){
	
	
	$exp_room = explode(',',$_POST['roomid']);
	
	$exp_panel = explode(',',$_POST['panelid']);
	
	for ($i=0;$i<count($exp_room);$i++){
		
		$workflow = $db->joinquery("SELECT * FROM workflow WHERE locationid ='".$_POST['locationid']."' AND roomid='".$exp_room[$i]."' AND panelid='".$exp_panel[$i]."'");
		
		if(mysqli_num_rows($workflow)==0){
			
			
			$db->joinquery("INSERT INTO workflow(agentid,locationid,roomid,panelid,".$_POST['field'].")VALUES(".$_SESSION['agentid'].",'".$_POST['locationid']."','".$exp_room[$i]."','".$exp_panel[$i]."','".$_POST['checked']."')");
			
			
		}
		
		
		else{
			
			
			$db->joinquery("UPDATE workflow SET ".$_POST['field']." = '".$_POST['checked']."' WHERE locationid='".$_POST['locationid']."' AND roomid='".$exp_room[$i]."' AND panelid='".$exp_panel[$i]."'");
			
			
		}
		
	}
	
}



