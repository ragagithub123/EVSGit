<?php ob_start();
session_start();
include('../includes/functions.php');

if($_POST['status'] == 'addloc'){
	
		$getloc = $db->joinquery("SELECT locationid FROM location WHERE locationSearch='".$_POST['location']."'");
	 $row = mysqli_fetch_array($getloc);

	
	$excnt = $db->joinquery("SELECT * FROM weekely_worksheet WHERE locationid='".$row['locationid']."' ORDER BY id DESC LIMIT 0,1");
	if(mysqli_num_rows($excnt) == 0){
			$week = 1;
		 $percent = 0;
		}
	else{
		 $rowval = mysqli_fetch_array($excnt);
				$week = $rowval['week']+1;
				$percent = $rowval['percentcomplete'];

		}
		
		$db->joinquery("INSERT INTO `weekely_worksheet`(`locationid`, `agentid`, `week`, `glass`, `kit`, `assemble`, `install`, `prep`, `seals`, `dates`, `percentcomplete`) VALUES ('".$row['locationid']."','".$_POST['agentid']."',".$week.",0,0,0,0,0,0,'".$_POST['weekenddate']."',".$percent.")");

		
	echo $row['locationid'];
}


if($_POST['status'] == 'staff_insert'){
	
	if($_POST['staffid'] == 0){
		
		$save_staff = $db->ins_rec("agentStaffs", array('staff_name'=>$_POST['filedvalue'],'agentid'=>$_SESSION['agentid']));	

		
	}
else{
	
	$db->joinquery("UPDATE agentStaffs SET staff_name='".$_POST['filedvalue']."' WHERE staff_id='".$_POST['staffid']."' AND agentid='".$_SESSION['agentid']."'");

$save_staff = $_POST['staffid'];
	
}


echo $save_staff;

}



if($_POST['status']=='insert'){
	
$startday= date('Y-m-d', strtotime('-7 day', strtotime($_POST['weekdate'])));	

$result = $db->joinquery("SELECT * FROM staffReports WHERE week_date='".$_POST['weekdate']."' AND staff_id='".$_POST['staffid']."' AND agentid='".$_SESSION['agentid']."'");

if(mysqli_num_rows($result)==0){
	
	
	
	 $db->joinquery("INSERT INTO staffReports(".$_POST['filedname'].",week_date,agentid,fieldcount,staff_id)VALUES('".$_POST['filedvalue']."','".$_POST['weekdate']."','".$_SESSION['agentid']."','".$_POST['filedcnt']."','".$_POST['staffid']."')");
}
else{
	
	$db->joinquery("UPDATE staffReports SET ".$_POST['filedname']."='".$_POST['filedvalue']."' WHERE staff_id='".$_POST['staffid']."' AND week_date='".$_POST['weekdate']."' AND agentid='".$_SESSION['agentid']."'");
}


$bonustotal = $db->joinquery("SELECT * FROM staffReports WHERE agentid='".$_SESSION['agentid']."' AND staff_id='".$_POST['staffid']."' AND week_date='".$_POST['weekdate']."'");

$restotal =mysqli_fetch_array($bonustotal);

$bonustask = ($restotal['mon']+$restotal['tue']+$restotal['wed']+$restotal['thu']+$restotal['fri']+$restotal['sat']+$restotal['sun'])-($restotal['non_bonus_hrs']);


$db->joinquery("UPDATE staffReports SET bonus_hrs='".abs($bonustask)."' WHERE staff_id='".$_POST['staffid']."' AND week_date='".$_POST['weekdate']."' AND agentid='".$_SESSION['agentid']."'");

 
$ressum=$db->joinquery("SELECT SUM(bonus_hrs) AS total_bonus,SUM(non_bonus_hrs) AS total_nonbonus FROM staffReports WHERE agentid='".$_SESSION['agentid']."' AND week_date='".$_POST['weekdate']."'");

$row_res = mysqli_fetch_array($ressum);

/* hourly rate*/

$getstaffreport = $db->joinquery("SELECT SUM(bonus_hrs) AS total_bonus FROM staffReports WHERE `agentid`='".$_SESSION['agentid']."' AND week_date ='".$_POST['weekdate']."'");

if(mysqli_num_rows($getstaffreport)>0){
	
$rowstaffrep = mysqli_fetch_assoc($getstaffreport);

$total_bonus = $rowstaffrep['total_bonus'];

}
else{
	
	 $total_bonus =0;
	
}


$gettask = $db->joinquery("SELECT SUM(glass) AS glassum, SUM(`kit`) AS kitsum,SUM(`assemble`) AS assemblesum,SUM(`install`) AS installsum,SUM(`prep`) AS prepsum,SUM(`seals`) AS sealssum FROM weekely_worksheet WHERE agentid='".$_SESSION['agentid']."' AND `dates` BETWEEN '".$startday."' AND '".$_POST['weekdate']."'");

if(mysqli_num_rows($gettask)>0){
	
	$rowtask = mysqli_fetch_array($gettask);
	
	if($rowtask['glassum']!=NULL)
	
	$glasssum = $rowtask['glassum'];
	
	else
	
	$glasssum = 0;
	
	if($rowtask['kitsum']!=NULL)
	
	$kitsum = $rowtask['kitsum'];
	
	else
	
	$kitsum = 0;
	
	if($rowtask['assemblesum']!=NULL)
	
	$assemblesum = $rowtask['assemblesum'];
	
	else
	
	$assemblesum = 0;
	
if($rowtask['installsum']!=NULL)
	
	$installsum = $rowtask['installsum'];
	
	else
	
	$installsum = 0;	
	
	if($rowtask['prepsum']!=NULL)
	
	$prepsum = $rowtask['prepsum'];
	
	else
	
	$prepsum = 0;	
	
if($rowtask['sealssum']!=NULL)
	
	$sealssum = $rowtask['sealssum'];
	
	else
	
	$sealssum = 0;		
	
	
}

else{
	
	 $glasssum = 0;
	
	$kitsum = 0;
	
	$assemblesum = 0;
	
	$installsum = 0;
	
	$prepsum = 0;
	
	$sealssum = 0;
	
}

$kitsbonus = $kitsum*2.50;

$assemblebonus = $assemblesum*2.50;

$prepbonus =  $prepsum*2.50;

$installbonus = $installsum*10.0;

$sealsbonus = $sealssum*2.50;

$totalbonustask = $kitsbonus+$assemblebonus+$prepbonus+$installbonus+$sealsbonus;

if($total_bonus!=0)

$hourlybonus = round($totalbonustask/$total_bonus,2);

else

$hourlybonus=0;


$array = array('bonus_hr'=>$row_res['total_bonus'],'non_bonus'=>$row_res['total_nonbonus'],'bonustask' =>abs($bonustask),'hourlybonus'=>$hourlybonus);

echo json_encode($array);
}

else if($_POST['status'] == 'remove'){
	
	$startday= date('Y-m-d', strtotime('-7 day', strtotime($_POST['weekdate'])));	
	
	$db->joinquery("DELETE FROM staffReports WHERE `agentid`='".$_SESSION['agentid']."' AND staff_id='".$_POST['staffid']."'");
	
	$ressumdata=$db->joinquery("SELECT SUM(bonus_hrs) AS total_bonus_hrs,SUM(non_bonus_hrs) AS total_nonbonus_hrs FROM staffReports WHERE agentid='".$_SESSION['agentid']."' AND week_date='".$_POST['weekdate']."'");

 $row_res_data = mysqli_fetch_array($ressumdata);
	
	$total_bonus_data = $row_res_data['total_bonus_hrs'];
	
	$total_nonbonus_data = $row_res_data['total_nonbonus_hrs'];
	
$gettask_data = $db->joinquery("SELECT SUM(glass) AS glassum, SUM(`kit`) AS kitsum,SUM(`assemble`) AS assemblesum,SUM(`install`) AS installsum,SUM(`prep`) AS prepsum,SUM(`seals`) AS sealssum FROM weekely_worksheet WHERE agentid='".$_SESSION['agentid']."' AND `dates` BETWEEN '".$startday."' AND '".$_POST['weekdate']."'");

if(mysqli_num_rows($gettask_data)>0){
	
	$rowtask_data = mysqli_fetch_array($gettask_data);
	
	if($rowtask_data['glassum']!=NULL)
	
	$glasssum_data = $rowtask_data['glassum'];
	
	else
	
	$glasssum_data = 0;
	
	if($rowtask_data['kitsum']!=NULL)
	
	$kitsum_data = $rowtask_data['kitsum'];
	
	else
	
	$kitsum_data = 0;
	
	if($rowtask_data['assemblesum']!=NULL)
	
	$assemblesum_data = $rowtask_data['assemblesum'];
	
	else
	
	$assemblesum_data = 0;
	
if($rowtask_data['installsum']!=NULL)
	
	$installsum_data = $rowtask_data['installsum'];
	
	else
	
	$installsum_data = 0;	
	
	if($rowtask_data['prepsum']!=NULL)
	
	$prepsum_data = $rowtask_data['prepsum'];
	
	else
	
	$prepsum_data = 0;	
	
if($rowtask_data['sealssum']!=NULL)
	
	$sealssum_data = $rowtask_data['sealssum'];
	
	else
	
	$sealssum_data = 0;		
	
	
}

else{
	
	 $glasssum_data = 0;
	
	$kitsum_data = 0;
	
	$assemblesum_data = 0;
	
	$installsum_data = 0;
	
	$prepsum_data = 0;
	
	$sealssum_data = 0;
	
}

$kitsbonus_data = $kitsum_data*2.50;

$assemblebonus_data = $assemblesum_data*2.50;

$prepbonus_data =  $prepsum_data*2.50;

$installbonus_data = $installsum_data*10.0;

$sealsbonus_data = $sealssum_data*2.50;

$totalbonustask_data = $kitsbonus_data+$assemblebonus_data+$prepbonus_data+$installbonus_data+$sealsbonus_data;

if($totalbonustask_data!=0 && $total_bonus_data!=0)

$hourlybonus_data = round($totalbonustask_data/$total_bonus_data,2);

else

$hourlybonus_data=0;

$array = array('hourlybonus_data'=>$hourlybonus_data,'total_bonus_data'=>$total_bonus_data,'total_nonbonus_data'=>$total_nonbonus_data);

echo json_encode($array);


	
}
else if($_POST['status']=='track'){	


$startday= date('Y-m-d', strtotime('-7 day', strtotime($_POST['enddate'])));	
	
	$locationarr = $_POST['locationid'];
	
	$prephr = $_POST['prephr'];
	$makehr = $_POST['makehr'];
	$installhr = $_POST['installhr'];
	$extrahr = $_POST['extrahr'];
	//$trackhrarr = $_POST['trackhour'];
	
	//if(count($prephr)>0)

	//print_r($locationarr);
	
	$totalhr = array_sum($_POST['prephr'])+array_sum($_POST['makehr'])+array_sum($_POST['installhr'])+array_sum($_POST['extrahr']);
	
	$flag =0;
	
	for($i=0;$i<count($locationarr);$i++){ 

    $getpjtid = $db->joinquery("SELECT projectid FROM location WHERE locationid='".$locationarr[$i]."'");

	$rowpjtid = mysqli_fetch_array($getpjtid);

	
	if(!empty($rowpjtid['projectid'])|| ($rowpjtid['projectid']!=NULL)){ 

		$details = $db->joinquery("SELECT * FROM staffTrack WHERE projectid='".$rowpjtid['projectid']."' AND staff_report_id ='".$_POST['staffreportid']."' AND day='".$_POST['trackday']."' AND staff_id='".$_POST['trackstaffid']."' AND week_date='".$_POST['enddate']."' AND fieldcount='".$_POST['filedtrackcnt']."' AND locationid='".$locationarr[$i]."'");
	
		if(mysqli_num_rows($details)==0){
			
		$flag =1;

		
		
		$db->joinquery("INSERT INTO staffTrack(projectid,staff_report_id,day,staff_id,week_date,fieldcount,locationid,agentid,prep,make,install,extra)VALUES('".$rowpjtid['projectid']."','".$_POST['staffreportid']."','".$_POST['trackday']."','".$_POST['trackstaffid']."','".$_POST['enddate']."','".$_POST['filedtrackcnt']."','".$locationarr[$i]."','".$_SESSION['agentid']."','".$prephr[$i]."','".$makehr[$i]."','".$installhr[$i]."','".$extrahr[$i]."')");
		
		}
		
		else 
		
	   $db->joinquery("UPDATE staffTrack SET prep='".$prephr[$i]."',make='".$makehr[$i]."',install='".$installhr[$i]."', extra='".$extrahr[$i]."' WHERE day='".$_POST['trackday']."' AND staff_report_id ='".$_POST['staffreportid']."' AND staff_id='".$_POST['trackstaffid']."' AND week_date='".$_POST['enddate']."' AND fieldcount='".$_POST['filedtrackcnt']."' AND locationid='".$locationarr[$i]."' AND projectid='".$rowpjtid['projectid']."'");
		
   }else{ 

	$details = $db->joinquery("SELECT * FROM staffTrack WHERE staff_report_id ='".$_POST['staffreportid']."' AND day='".$_POST['trackday']."' AND staff_id='".$_POST['trackstaffid']."' AND week_date='".$_POST['enddate']."' AND fieldcount='".$_POST['filedtrackcnt']."' AND locationid='".$locationarr[$i]."'");
	
	if(mysqli_num_rows($details)==0){
		
	$flag =1;
	
	$db->joinquery("INSERT INTO staffTrack(staff_report_id,day,staff_id,week_date,fieldcount,locationid,agentid,prep,make,install,extra)VALUES('".$_POST['staffreportid']."','".$_POST['trackday']."','".$_POST['trackstaffid']."','".$_POST['enddate']."','".$_POST['filedtrackcnt']."','".$locationarr[$i]."','".$_SESSION['agentid']."','".$prephr[$i]."','".$makehr[$i]."','".$installhr[$i]."','".$extrahr[$i]."')");
	
	}
	
	else 
	
   $db->joinquery("UPDATE staffTrack SET prep='".$prephr[$i]."',make='".$makehr[$i]."',install='".$installhr[$i]."', extra='".$extrahr[$i]."' WHERE day='".$_POST['trackday']."' AND staff_report_id ='".$_POST['staffreportid']."' AND staff_id='".$_POST['trackstaffid']."' AND week_date='".$_POST['enddate']."' AND fieldcount='".$_POST['filedtrackcnt']."' AND locationid='".$locationarr[$i]."'");

   }


		}
		
		if($flag == 1)
	
		$db->joinquery("UPDATE staffReports SET loadedday=CONCAT('".$_POST['trackday']."',',', loadedday) WHERE staff_report_id ='".$_POST['staffreportid']."'");
		
	 //$db->joinquery("UPDATE staffReports SET ".$_POST['trackday']."='".$totalhr."' WHERE staff_report_id ='".$_POST['staffreportid']."'");
  
		$getallsum = $db->joinquery("SELECT `non_bonus_hrs`, SUM(mon+tue+wed+thu+fri+sat+sun) AS sum FROM staffReports WHERE `staff_report_id`='".$_POST['staffreportid']."'");
		
		if(mysqli_num_rows($getallsum)>0){
			
			 $rowsum = mysqli_fetch_array($getallsum);
				
				$bonus = $rowsum['sum']- $rowsum['non_bonus_hrs'];
				
				$db->joinquery("UPDATE staffReports SET bonus_hrs ='".$bonus."' WHERE staff_report_id ='".$_POST['staffreportid']."'");
			
		}
		
		else
		
		$bonus=0;
		
		/* get hourly rate*/
		
	$getstaffreport1 = $db->joinquery("SELECT SUM(bonus_hrs) AS total_bonus FROM staffReports WHERE `agentid`='".$_SESSION['agentid']."' AND week_date='".$_POST['enddate']."'");

if(mysqli_num_rows($getstaffreport1)>0){
	
$rowstaffrep1 = mysqli_fetch_assoc($getstaffreport1);

$total_bonus1 = $rowstaffrep1['total_bonus'];

}
else{
	
	 $total_bonus1 =0;
	
}


$gettask1 = $db->joinquery("SELECT SUM(glass) AS glassum, SUM(`kit`) AS kitsum,SUM(`assemble`) AS assemblesum,SUM(`install`) AS installsum,SUM(`prep`) AS prepsum,SUM(`seals`) AS sealssum FROM weekely_worksheet WHERE agentid='".$_SESSION['agentid']."' AND `dates` BETWEEN '".$startday."' AND '".$_POST['enddate']."'");

if(mysqli_num_rows($gettask1)>0){
	
	$rowtask1 = mysqli_fetch_array($gettask1);
	
	if($rowtask1['glassum']!=NULL)
	
	$glasssum1 = $rowtask1['glassum'];
	
	else
	
	$glasssum1 = 0;
	
	if($rowtask1['kitsum']!=NULL)
	
	$kitsum1 = $rowtask1['kitsum'];
	
	else
	
	$kitsum1 = 0;
	
	if($rowtask1['assemblesum']!=NULL)
	
	$assemblesum1 = $rowtask1['assemblesum'];
	
	else
	
	$assemblesum1 = 0;
	
if($rowtask1['installsum']!=NULL)
	
	$installsum1 = $rowtask1['installsum'];
	
	else
	
	$installsum1 = 0;	
	
	if($rowtask1['prepsum']!=NULL)
	
	$prepsum1 = $rowtask1['prepsum'];
	
	else
	
	$prepsum1 = 0;	
	
if($rowtask1['sealssum']!=NULL)
	
	$sealssum1 = $rowtask1['sealssum'];
	
	else
	
	$sealssum1 = 0;		
	
	
}

else{
	
	 $glasssum1 = 0;
	
	$kitsum1 = 0;
	
	$assemblesum1 = 0;
	
	$installsum1 = 0;
	
	$prepsum1 = 0;
	
	$sealssum1 = 0;
	
}

$kitsbonus1 = $kitsum1*2.50;

$assemblebonus1 = $assemblesum1*2.50;

$prepbonus1 =  $prepsum1*2.50;

$installbonus1 = $installsum1*10.0;

$sealsbonus1 = $sealssum1*2.50;

$totalbonustask1 = $kitsbonus1+$assemblebonus1+$prepbonus1+$installbonus1+$sealsbonus1;

if($total_bonus1!=0)

$hourlybonus1 = round($totalbonustask1/$total_bonus1,2);

else

$hourlybonus1=0;

/* end hourly rate*/  
		
		echo $totalhr."@".$bonus."@".$total_bonus1."@".$hourlybonus1;
		
}

else if($_POST['status'] == 'disptrack'){

	$trackids =array();
	

	$getPjt = $db->joinquery("SELECT locationid FROM staffTrack WHERE projectid IS NULL AND agentid='".$_SESSION['agentid']."' AND staff_id='".$_POST['trackstaffid']."' AND day='".$_POST['trackday']."' AND staff_report_id='".$_POST['staffreportid']."' AND week_date='".$_POST['enddate']."' AND fieldcount='".$_POST['filedtrackcnt']."'");

	if(mysqli_num_rows($getPjt)>0){

		while($rowPjts = mysqli_fetch_array($getPjt)){

		$getlocationPjt = $db->joinquery("SELECT projectid FROM location WHERE locationid='".$rowPjts['locationid']."' AND projectid IS NOT NULL");

		if(mysqli_num_rows($getlocationPjt)>0){

		$rowlocationPjt = mysqli_fetch_array($getlocationPjt);
			
		$db->joinquery("UPDATE staffTrack SET projectid='".$rowlocationPjt['projectid']."' WHERE locationid='".$rowPjts['locationid']."' AND agentid='".$_SESSION['agentid']."' AND staff_id='".$_POST['trackstaffid']."' AND day='".$_POST['trackday']."' AND staff_report_id='".$_POST['staffreportid']."' AND week_date='".$_POST['enddate']."' AND fieldcount='".$_POST['filedtrackcnt']."'");

		}

		

		}
	}

	$checkPjts = $db->joinquery("SELECT locationid FROM staffTrack WHERE agentid='".$_SESSION['agentid']."' AND staff_id='".$_POST['trackstaffid']."' AND day='".$_POST['trackday']."' AND staff_report_id='".$_POST['staffreportid']."' AND week_date='".$_POST['enddate']."' AND fieldcount='".$_POST['filedtrackcnt']."'");
	
	if(mysqli_num_rows($checkPjts)>0){

	 while($rowPjtsC = mysqli_fetch_array($checkPjts)){

		

		$get_Pjtid = $db->joinquery("SELECT projectid,locationid FROM location WHERE locationid='".$rowPjtsC['locationid']."'");
		
		$row_PjtID = mysqli_fetch_array($get_Pjtid);
		
         
		if($row_PjtID['projectid']!= NULL){ 

			//echo "SELECT stafftrackid FROM  staffTrack WHERE agentid='".$_SESSION['agentid']."' AND staff_id='".$_POST['trackstaffid']."' AND day='".$_POST['trackday']."' AND staff_report_id='".$_POST['staffreportid']."' AND week_date='".$_POST['enddate']."' AND fieldcount='".$_POST['filedtrackcnt']."' AND projectid='".$row_PjtID['projectid']."' AND locationid='".$row_PjtID['locationid']."'";

			$getStafftrackid = $db->joinquery("SELECT stafftrackid FROM  staffTrack WHERE agentid='".$_SESSION['agentid']."' AND staff_id='".$_POST['trackstaffid']."' AND day='".$_POST['trackday']."' AND staff_report_id='".$_POST['staffreportid']."' AND week_date='".$_POST['enddate']."' AND fieldcount='".$_POST['filedtrackcnt']."' AND projectid='".$row_PjtID['projectid']."' AND locationid='".$row_PjtID['locationid']."'");

			if(mysqli_num_rows($getStafftrackid)>0){

				while($rowStaffpjt = mysqli_fetch_array($getStafftrackid)){

					$trackids[] = $rowStaffpjt['stafftrackid'];
				}
			}


		}else{ 


			$getStafftrackids = $db->joinquery("SELECT stafftrackid FROM  staffTrack WHERE agentid='".$_SESSION['agentid']."' AND staff_id='".$_POST['trackstaffid']."' AND day='".$_POST['trackday']."' AND staff_report_id='".$_POST['staffreportid']."' AND week_date='".$_POST['enddate']."' AND fieldcount='".$_POST['filedtrackcnt']."' AND locationid='".$row_PjtID['locationid']."'");

			if(mysqli_num_rows($getStafftrackids)>0){

				while($rowStaffpjts = mysqli_fetch_array($getStafftrackids)){

					$trackids[] = $rowStaffpjts['stafftrackid'];
				}
			}

		}


	 }


	}

	//print_r($trackids);

	if(count($trackids)>0){

		$track_ids = join(',',$trackids);
		$prep =array();
		$make =array();
		$install=array();
		$extra =array();
		$post=array();

	   $gettrackhrs = $db->joinquery("SELECT prep,make,install,extra,locationid FROM staffTrack WHERE stafftrackid IN($track_ids)");	

	   while($row_hr =mysqli_fetch_assoc($gettrackhrs)){
			
		  $prep[] = $row_hr['prep'];
		   $make[] = $row_hr['make'];
		   $install[] = $row_hr['install'];
		   $extra[] = $row_hr['extra'];
		   $post[] = $row_hr;
	   
       }

	   $preptotal = array_sum($prep);
		$maketotal = array_sum($make);
		$instatotal = array_sum($install);
		$extratotal = array_sum($extra);
		$hrs = $preptotal+$maketotal+$instatotal+$extratotal;
		
		$gethour = $db->joinquery("SELECT ".$_POST['trackday']." AS dayhr FROM staffReports WHERE agentid='".$_SESSION['agentid']."' AND staff_id='".$_POST['trackstaffid']."' AND staff_report_id='".$_POST['staffreportid']."' AND week_date='".$_POST['enddate']."' AND fieldcount='".$_POST['filedtrackcnt']."'");
		$rowhr = mysqli_fetch_array($gethour);
		$balanchr = ($rowhr['dayhr']) - $hrs;
		$post1 = array('details'=>$post,'workshop_hr'=>$rowhr['dayhr'],'balancehr'=>$balanchr);



	}

	else{

		$gethour = $db->joinquery("SELECT ".$_POST['trackday']." AS dayhr FROM staffReports WHERE agentid='".$_SESSION['agentid']."' AND staff_id='".$_POST['trackstaffid']."' AND staff_report_id='".$_POST['staffreportid']."' AND week_date='".$_POST['enddate']."' AND fieldcount='".$_POST['filedtrackcnt']."'");
		
		$rowhr = mysqli_fetch_array($gethour);
		
		$post1 =array('details'=>array(),'workshop_hr'=>$rowhr['dayhr'],'balancehr'=>$rowhr['dayhr']);

	}
	

	echo json_encode($post1);

	
	

	
}

else if($_POST['status']=='display'){
	
	$getprop=$db->joinquery("SELECT locationid,locationSearch FROM location WHERE agentid='".$_SESSION['agentid']."' AND locationSearch!=''");		
		  if(mysqli_num_rows($getprop)>0)
			{
					while($row_prop=mysqli_fetch_assoc($getprop))
					{
							$postLocation[]=$row_prop;
							
					}
					
			}
	
$week_startdate = $_POST['startdate'];

$week_endate = $_POST['enddate'];



$gtelocation =$db->joinquery("SELECT location.locationid,location.projectid AS locPjtid,location.unitnum,location.street,location.city,location.suburb,location.locationSearch,weekely_worksheet.* FROM location,weekely_worksheet WHERE location.locationid=weekely_worksheet.locationid  AND weekely_worksheet.`agentid`='".$_SESSION['agentid']."' AND weekely_worksheet.dates BETWEEN '".$week_startdate."' AND '".$week_endate."' GROUP BY weekely_worksheet.locationid");
if(mysqli_num_rows($gtelocation)>0)	{
	while($row_loc = mysqli_fetch_assoc($gtelocation)){
		
		if(empty($row_loc['locationSearch'])){

			$loc=$row_loc['unitnum'].",".$row_loc['street'];
		
			if(!empty($row_loc['suburb']))
						  
			$loc.=",".$row_loc['suburb'];


		}

		if(!empty($row_loc['locPjtid'])|| ($row_loc['locPjtid']!=NULL)){

			$getproject = $db->joinquery("SELECT project_name,projectid,project_date FROM location_projects WHERE projectid='".$row_loc['locPjtid']."'");

			$rowPjt =  mysqli_fetch_array($getproject);

			$row_loc['project_name'] = $rowPjt['project_name'];

			$row_loc['projectid'] = $rowPjt['projectid'];

			$row_loc['project_date'] = date('d-m-Y',strtotime($rowPjt['project_date']));

			$row_loc['project_name'] =$rowPjt['project_name']." ".$row_loc['project_date'];
		}
	
		
		
		
		$post[] =$row_loc;

	}
	
	 
		
	}


$getstaffreport = $db->joinquery("SELECT * FROM staffReports WHERE `agentid`='".$_SESSION['agentid']."' AND week_date ='".$week_endate."'");

if(mysqli_num_rows($getstaffreport)>0){
	
	while($rowstaff = mysqli_fetch_assoc($getstaffreport)){
		
		$getstaff= $db->joinquery("SELECT staff_name FROM agentStaffs WHERE staff_id='".$rowstaff['staff_id']."'");
		
		if(mysqli_num_rows($getstaff)>0){
		
		$staffdata = mysqli_fetch_array($getstaff);
		
		$rowstaff['staff_name'] = $staffdata['staff_name'];
		}
		else{
			
			$rowstaff['staff_name'] ="";
			
		}
		
		 $totalbonus[] = $rowstaff['bonus_hrs'];
			
			$totalnonbonus[] = $rowstaff['non_bonus_hrs'];
		
		 $staffreport[] = $rowstaff;
		
	}
	
	$total_bonus = array_sum($totalbonus);
	
	$totalnonbonus = array_sum($totalnonbonus);
	
	}
	
	else{
		
		$getmaxweek = $db->joinquery("SELECT MAX(`week_date`) AS week_date FROM staffReports WHERE agentid='".$_SESSION['agentid']."'");
		
		if(mysqli_num_rows($getmaxweek)>0){
			
			$rowmaxweek = mysqli_fetch_array($getmaxweek);
			
			$getStaffData = $db->joinquery("SELECT staff_id,fieldcount FROM staffReports WHERE agentid='".$_SESSION['agentid']."' AND week_date='".$rowmaxweek['week_date']."'");
			
			while($row_satffdata = mysqli_fetch_array($getStaffData)){
				
				$db->joinquery("INSERT INTO staffReports(staff_id,fieldcount,agentid,week_date,bonus_hrs,mon,tue,wed,thu,fri)VALUES('".$row_satffdata['staff_id']."','".$row_satffdata['fieldcount']."','".$_SESSION['agentid']."','".$week_endate."',40,8,8,8,8,8)");
				
				}
				
				$getstaffreport = $db->joinquery("SELECT * FROM staffReports WHERE `agentid`='".$_SESSION['agentid']."' AND week_date = '".$week_endate."'");

				
				while($rowstaff = mysqli_fetch_assoc($getstaffreport)){
		
		$getstaff= $db->joinquery("SELECT staff_name FROM agentStaffs WHERE staff_id='".$rowstaff['staff_id']."'");
		
		if(mysqli_num_rows($getstaff)>0){
		
		$staffdata = mysqli_fetch_array($getstaff);
		
		$rowstaff['staff_name'] = $staffdata['staff_name'];
		}
		else{
			
			$rowstaff['staff_name'] ="";
			
		}
		
		 $totalbonus[] = $rowstaff['bonus_hrs'];
			
			$totalnonbonus[] = $rowstaff['non_bonus_hrs'];
		
		 $staffreport[] = $rowstaff;
		
	}
	
	$total_bonus = array_sum($totalbonus);
	
	$totalnonbonus = array_sum($totalnonbonus);
	
			
	
		}
		

		else{
			
			$staffreport =array();
			
			 $total_bonus =0;
			
			$totalnonbonus =0;
			
		}
		

		
		
		
		
	}
	
	
$gettask = $db->joinquery("SELECT SUM(glass) AS glassum, SUM(`kit`) AS kitsum,SUM(`assemble`) AS assemblesum,SUM(`install`) AS installsum,SUM(`prep`) AS prepsum,SUM(`seals`) AS sealssum FROM weekely_worksheet WHERE agentid='".$_SESSION['agentid']."' AND `dates` BETWEEN '".$week_startdate."' AND '".$week_endate."'");

if(mysqli_num_rows($gettask)>0){
	
	$rowtask = mysqli_fetch_array($gettask);
	
	if($rowtask['glassum']!=NULL)
	
	$glasssum = $rowtask['glassum'];
	
	else
	
	$glasssum = 0;
	
	if($rowtask['kitsum']!=NULL)
	
	$kitsum = $rowtask['kitsum'];
	
	else
	
	$kitsum = 0;
	
	if($rowtask['assemblesum']!=NULL)
	
	$assemblesum = $rowtask['assemblesum'];
	
	else
	
	$assemblesum = 0;
	
if($rowtask['installsum']!=NULL)
	
	$installsum = $rowtask['installsum'];
	
	else
	
	$installsum = 0;	
	
	if($rowtask['prepsum']!=NULL)
	
	$prepsum = $rowtask['prepsum'];
	
	else
	
	$prepsum = 0;	
	
if($rowtask['sealssum']!=NULL)
	
	$sealssum = $rowtask['sealssum'];
	
	else
	
	$sealssum = 0;		
	
	
}

else{
	
	 $glasssum = 0;
	
	$kitsum = 0;
	
	$assemblesum = 0;
	
	$installsum = 0;
	
	$prepsum = 0;
	
	$sealssum = 0;
	
}

$kitsbonus = $kitsum*2.50;

$assemblebonus = $assemblesum*2.50;

$prepbonus =  $prepsum*2.50;

$installbonus = $installsum*10.0;

$sealsbonus = $sealssum*2.50;

$totalbonustask = $kitsbonus+$assemblebonus+$prepbonus+$installbonus+$sealsbonus;

if($total_bonus!=0)

$hourlybonus = round($totalbonustask/$total_bonus,2);

else

$hourlybonus=0;
	

	
	
	?>

                    <div class="table-responsive">
                      <table class="table table-bordered">
                        <thead>
                          <tr style="color: #fff; background: #b3b3b3;">
                            <th><div class="staff-name heading"><span>Staff</span> <a href="javascript:void(0)" class="staff-add"><i class="fa fa-plus"></i></a></div></th>
                            <th width="100" style="text-align: center;">Bonus Hours</th>
                            <th width="100" style="text-align: center;">Non-Bonus Hours</th>
                            <th width="90" style="text-align: center;">Mon</th>
                            <th width="90" style="text-align: center;">Tue</th>
                            <th width="90" style="text-align: center;">Wed</th>
                            <th width="90" style="text-align: center;">Thu</th>
                            <th width="90" style="text-align: center;">Fri</th>
                            <th width="90" style="text-align: center;">Sat</th>
                            <th width="90" style="text-align: center;">Sun</th>
                            <th>Comments</th>
                          </tr>
                        </thead>
                        <tbody id="staff-table">
                        <?php
						$filed_cont =0;
						$bluemon=0;
						$bluetue=0;
						$bluewed=0;
						$bluethu=0;
						$bluefri=0;
						$bluesat=0;
						$bluesun=0;
if(!empty($staffreport)){
		
		foreach($staffreport as $valreport){ 
		
		$filed_cont =$valreport['fieldcount'];
																					
			if(!empty($valreport['loadedday'])){
			
			$expload = explode(',',$valreport['loadedday']);
			
			if(count($expload)>0){
			
			if(in_array('mon',$expload)) $bluemon =$valreport['fieldcount'];
			if(in_array('tue',$expload)) $bluetue =$valreport['fieldcount'];
			if(in_array('wed',$expload)) $bluewed =$valreport['fieldcount'];
			if(in_array('thu',$expload)) $bluethu =$valreport['fieldcount'];
			if(in_array('fri',$expload)) $bluefri =$valreport['fieldcount'];
			if(in_array('sat',$expload)) $bluesat =$valreport['fieldcount'];
			if(in_array('sun',$expload)) $bluesun =$valreport['fieldcount'];
			
			}
			
			}
                          
                          ?>
                          
                           <tr id="tr<?php echo $valreport['fieldcount'];?>">
                           <td><div class="staff-name"><input type="text" class="form-control staff-text" value="<?php echo $valreport['staff_name'];?>" data-filedname="staff_name" id="staffname<?php echo $valreport['fieldcount'];?>" data-count="<?php echo $valreport['fieldcount'];?>" data-staff="<?php echo $valreport['staff_id'];?>"> <a href="javascript:void(0)" class="remove-bonus" data-staff="<?php echo $valreport['staff_id'];?>" data-filedcnt="<?php echo $valreport['fieldcount'];?>" id="anchor<?php echo $valreport['fieldcount'];?>"><i class="fa fa-times"></i></a></div></td>
                           
                           <td><input type="text" class="form-control bonus_hrs" value="<?php echo $valreport['bonus_hrs'];?>" data-filedname="bonus_hrs" id="bonus_hrs<?php echo $valreport['fieldcount'];?>" data-count="<?php echo $valreport['fieldcount'];?>" readonly="readonly" data-staff="<?php echo $valreport['staff_id'];?>"></td>
                           <td><input type="text" class="form-control non_bonus_hrs" value="<?php echo $valreport['non_bonus_hrs'];?>" data-filedname="non_bonus_hrs" id="non_bonus_hrs<?php echo $valreport['fieldcount'];?>" data-count="<?php echo $valreport['fieldcount'];?>" data-staff="<?php echo $valreport['staff_id'];?>"></td>
                           <td class="hours-select"><input type="text" class="form-control mon" value="<?php echo $valreport['mon'];?>" data-filedname="mon" id="mon<?php echo $valreport['fieldcount'];?>" data-count="<?php echo $valreport['fieldcount'];?>" data-staff="<?php echo $valreport['staff_id'];?>" <?php if($bluemon == $valreport['fieldcount']){?> style="background-color:#96faf8" <?php }?>><i class="fa fa-caret-down track" data-toggle="modal" data-target="#weekly-popup-ajx" data-trackcount="<?php echo $valreport['fieldcount'];?>" data-trackstaff="<?php echo $valreport['staff_id'];?>" data-trackfiledname="mon" data-staffreportid="<?php echo $valreport['staff_report_id'];?>"></i></td>
                           <td class="hours-select"><input type="text" class="form-control tue" value="<?php echo $valreport['tue'];?>" data-filedname="tue" id="tue<?php echo $valreport['fieldcount'];?>" data-count="<?php echo $valreport['fieldcount'];?>" data-staff="<?php echo $valreport['staff_id'];?>" <?php if($bluetue == $valreport['fieldcount']){?> style="background-color:#96faf8" <?php }?>><i class="fa fa-caret-down track" data-toggle="modal" data-target="#weekly-popup-ajx" data-trackcount="<?php echo $valreport['fieldcount'];?>" data-trackstaff="<?php echo $valreport['staff_id'];?>" data-trackfiledname="tue" data-staffreportid="<?php echo $valreport['staff_report_id'];?>"></i></td>
                           <td class="hours-select"><input type="text" class="form-control wed" value="<?php echo $valreport['wed'];?>" data-filedname="wed" id="wed<?php echo $valreport['fieldcount'];?>" data-count="<?php echo $valreport['fieldcount'];?>" data-staff="<?php echo $valreport['staff_id'];?>" <?php if($bluewed == $valreport['fieldcount']){?> style="background-color:#96faf8" <?php }?>><i class="fa fa-caret-down track" data-toggle="modal" data-target="#weekly-popup-ajx" data-trackcount="<?php echo $valreport['fieldcount'];?>" data-trackstaff="<?php echo $valreport['staff_id'];?>" data-trackfiledname="wed" data-staffreportid="<?php echo $valreport['staff_report_id'];?>"></i></td>
                           <td class="hours-select"><input type="text" class="form-control thu" value="<?php echo $valreport['thu'];?>" data-filedname="thu" id="thu<?php echo $valreport['fieldcount'];?>" data-count="<?php echo $valreport['fieldcount'];?>" data-staff="<?php echo $valreport['staff_id'];?>" <?php if($bluethu == $valreport['fieldcount']){?> style="background-color:#96faf8" <?php }?>><i class="fa fa-caret-down track" data-toggle="modal" data-target="#weekly-popup-ajx" data-trackcount="<?php echo $valreport['fieldcount'];?>" data-trackstaff="<?php echo $valreport['staff_id'];?>" data-trackfiledname="thu" data-staffreportid="<?php echo $valreport['staff_report_id'];?>"></i></td>
                           <td class="hours-select"><input type="text" class="form-control fri" value="<?php echo $valreport['fri'];?>" data-filedname="fri" id="fri<?php echo $valreport['fieldcount'];?>" data-count="<?php echo $valreport['fieldcount'];?>" data-staff="<?php echo $valreport['staff_id'];?>" <?php if($bluefri == $valreport['fieldcount']){?> style="background-color:#96faf8" <?php }?>><i class="fa fa-caret-down track" data-toggle="modal" data-target="#weekly-popup-ajx" data-trackcount="<?php echo $valreport['fieldcount'];?>" data-trackstaff="<?php echo $valreport['staff_id'];?>" data-trackfiledname="fri" data-staffreportid="<?php echo $valreport['staff_report_id'];?>"></i></td>
                           <td class="hours-select"><input type="text" class="form-control sat" value="<?php echo $valreport['sat'];?>" data-filedname="sat" id="sat<?php echo $valreport['fieldcount'];?>" data-count="<?php echo $valreport['fieldcount'];?>" data-staff="<?php echo $valreport['staff_id'];?>" <?php if($bluesat == $valreport['fieldcount']){?> style="background-color:#96faf8" <?php }?>><i class="fa fa-caret-down track" data-toggle="modal" data-target="#weekly-popup-ajx" data-trackcount="<?php echo $valreport['fieldcount'];?>" data-trackstaff="<?php echo $valreport['staff_id'];?>" data-trackfiledname="sat" data-staffreportid="<?php echo $valreport['staff_report_id'];?>"></i></td>
                           <td class="hours-select"><input type="text" class="form-control sun" value="<?php echo $valreport['sun'];?>" data-filedname="sun" id="sun<?php echo $valreport['fieldcount'];?>" data-count="<?php echo $valreport['fieldcount'];?>" data-staff="<?php echo $valreport['staff_id'];?>" <?php if($bluesun == $valreport['fieldcount']){?> style="background-color:#96faf8" <?php }?>><i class="fa fa-caret-down track" data-toggle="modal" data-target="#weekly-popup-ajx" data-trackcount="<?php echo $valreport['fieldcount'];?>" data-trackstaff="<?php echo $valreport['staff_id'];?>" data-trackfiledname="sun" data-staffreportid="<?php echo $valreport['staff_report_id'];?>"></i></td>
                            <td><input type="text" class="form-control staff-comment" value="<?php echo $valreport['comments'];?>" data-filedname="comments" id="comments<?php echo $valreport['fieldcount'];?>" data-count="<?php echo $valreport['fieldcount'];?>" data-staff="<?php echo $valreport['staff_id'];?>"></td>
                          </tr>
                            
                            

                           
                          <?php
                          }
                         
                         }
                        ?>
                        </tbody>
                        <tfoot>
                          <tr>
                            <td style="border-left-color: #fff; border-bottom-color: #fff;"></td>
                            <td style="text-align: center; background: #e6e6e6;" id="bonous_total"><?php echo $total_bonus;?></td>
                            <td style="text-align: center; background: #e6e6e6;" id="non_bonous_total"><?php echo $totalnonbonus;?></td>
                            <td colspan="8" style="border-right-color: #fff; border-bottom-color: #fff;"></td>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                    <br><br>
                    <div class="table-responsive">
                      <table class="table table-spacing">
                        <tbody>
                          <tr>
                            <td width="100">Task</td>
                            <th width="100" style="text-align: center; background: #b3b3b3;">Kits Made</th>
                            <th width="100" style="text-align: center; background: #b3b3b3;">Panels Assembled</th>
                            <th width="100" style="text-align: center; background: #b3b3b3;">Panels Prepped</th>
                            <th width="100" style="text-align: center; background: #b3b3b3;">Panles Installed</th>
                            <th width="100" style="text-align: center; background: #b3b3b3;">Seals Installed</th>
                          </tr>
                          <tr>
                            <td>Task Bonus Rates</td>
                            <td style="text-align: center;">$2.50</td>
                            <td style="text-align: center;">$2.50</td>
                            <td style="text-align: center;">$2.50</td>
                            <td style="text-align: center;">$10.00</td>
                            <td style="text-align: center;">$2.50</td>
                          </tr>
                          <tr>
                            <td>Completed Tasks</td>
                           
                            <td style="text-align: center;"><?php echo $kitsum;?></td>
                            <td style="text-align: center;"><?php echo $assemblesum;?></td>
                            <td style="text-align: center;"><?php echo $prepsum;?></td>
                            <td style="text-align: center;"><?php echo $installsum;?></td>
                             <td style="text-align: center;"><?php echo $sealssum;?></td>
                          </tr>
                          <tr>
                            <td>Task Bonus Totals</td>
                            <td style="text-align: center;"><?php echo $kitsbonus;?></td>
                            <td style="text-align: center;">$<?php echo $assemblebonus;?></td>
                            <td style="text-align: center;">$<?php echo $prepbonus;?></td>
                            <td style="text-align: center;">$<?php echo $installbonus;?></td>
                            <td style="text-align: center;">$<?php echo $sealsbonus;?></td>
                          </tr>
                          
                        </tbody>
                       <tfoot>
                         <tr>
                            <td>Bonus Total</td>
                            <td style="text-align: center;">$<?php echo $totalbonustask;?></td>
                           
                          </tr>
                           <tr>
                            <td>Hourly Bonus Rate</td>
                            <td style="text-align: center; font-weight:bold" id="hourly_bonus">$<?php echo $hourlybonus;?></td>
                           
                          </tr>
                       </tfoot>
                      </table>
                       <input type="hidden" id="field_count" value="<?php echo $filed_cont;?>">
                    </div>
                    
                    <!-- Modal -->

                   <!-- Modal -->

				   <div class="modal fade" id="weekly-popup-ajx" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Select Hours Per Job &nbsp;&nbsp;<span id="dispday" style="margin-left:200px;"></span></h4>
        <p>
        <div class="search_head" style="display:flex;">
           
            
             <input id="agent-location" name="agentlocation" class="form-control" autocomplete="off" placeholder="Search Your Location" list="listlocation" style="width:200px;">
             <button><i class="glyphicon glyphicon-search"></i></button>
             <i class ="fa fa-plus plusbtn" id="sheetaddlo" style="margin-left:20px;"></i>
             
  <datalist id="listlocation">
                 <?php
                   foreach($postLocation as $valuepost){
                    echo ' <option value="'.$valuepost['locationSearch'].'">';
                   }?>
    
  </datalist>
            
            
              </div>	</p> 
      </div>
      <div class="modal-body">
      
        <div class="weekly-popup">
          <h4>Workshop</h4>
         <input type="text" class="form-control form-box" id="workshophr" readonly="readonly">
        </div>
      
        <div class="hours-list">
          <ul>
           <?php $cnt=0; ?>
           <?php foreach($post as $valloc){?>
            <li>
              <span><?php echo $valloc['locationSearch'];?>,<b style="color: #09aced;"><?php echo $valloc['project_name'];?></b></span>
             <!-- <input type="text" class="form-control form-box workshop" name="trackhour[]" id="trackhr<?php //echo $valloc['locationid'];?>" value="0">-->
              <div class="form-group">
              <?php if($cnt == 0){?>
              <label>Prep</label>
              <?php } ?>
              		<input type="text" class="form-control form-box workshop_prephr spendhr" name="prephr[]" id="prep<?php echo $valloc['locationid'];?>" value="0">
              </div>
              <div class="form-group">
              <?php if($cnt == 0){?>
              <label>Make</label>
              <?php } ?>
              		<input type="text" class="form-control form-box workshop_make spendhr" name="makehr[]" id="make<?php echo $valloc['locationid'];?>" value="0">
              </div>
              <div class="form-group">
              <?php if($cnt == 0){?>
              <label>Install</label>
              <?php } ?>
              		<input type="text" class="form-control form-box workshop_install spendhr" name="installhr[]" id="install<?php echo $valloc['locationid'];?>" value="0">
              </div>
              <div class="form-group">
              <?php if($cnt == 0){?>
              <label>Extra</label>
              <?php } ?>
              		<input type="text" class="form-control form-box workshop_extra spendhr" name="extrahr[]" id="extra<?php echo $valloc['locationid'];?>" value="0">
              </div>
               <input type="hidden" name="locationtrackids[]" value="<?php echo $valloc['locationid'];?>" class="locidclas"/>
            </li>
           <?php $cnt++;} ?>
           
           <li id="totalli">
           <span style="border:0">Total</span>
           <input type="text" class="form-control form-box" name="worktotal" id="worktotal" value="0" readonly="readonly">
           </li>
            
          </ul>
          <input type="hidden" id="trackday" />
          <input type="hidden" id="filedtrackcnt" />
           <input type="hidden" id="trackstaffid" />
           <input type="hidden" id="weekenddate" value="<?php echo $_POST['enddate'];?>" />
           <input type="hidden" id="staffreportid" />
            <input type="hidden" id="workshopexisthr" />
            <input type="hidden" id="agentid" value="<?php echo $_SESSION['agentid'];?>"/>
        </div>
         <span id="btnspn"></span>
        <?php if(count($post)>0){?>
         <input type="hidden" id="checkval" value="1"/>
		 <input type="hidden" id="disp_status" value="0"/>
        <button class="btn btn-primary" id="trackupdate">Update</button>
        <?php } else ?><input type="hidden" id="checkval" value="0"/>
      </div>
    </div>
  </div>
</div>
  <!-- end Modal -->
                  	
<?php	
}
?>

