<?php ob_start();

session_start();

include('includes/functions.php');

if(!empty($_SESSION['agentid'])){
	
	$get_location = $db->joinquery("SELECT locationSearch FROM location WHERE locationid='".base64_decode($_REQUEST['id'])."'");	
	
	$row_location = mysqli_fetch_array($get_location);
	
	$get_track    =  $db->joinquery("SELECT * FROM TrakingWork WHERE locationid='".base64_decode($_REQUEST['id'])."' ORDER BY created_at DESC");
	
	while($row_track = mysqli_fetch_array($get_track)){
		
		$Dates[]=date('Y-m-d',strtotime($row_track['created_at']));
		
		if($row_track['created_at']){
			
			 $row_track['created_at']=date('Y-m-d',strtotime($row_track['created_at']));
		}
	
			
		$post[]=$row_track;
		
}
	if(!empty($Dates))
	
	$tracked_dates = array_unique($Dates);
	
	$getusers     = $db->joinquery("SELECT staffid,color FROM staffs WHERE locationid='".base64_decode($_REQUEST['id'])."'");
	
	while($rowusers = mysqli_fetch_array($getusers)){
		
		foreach($tracked_dates as $valdate){
				
				$getlastactivity = $db->joinquery("SELECT * FROM TrakingWork WHERE staffid ='".$rowusers['staffid']."' AND DATE(`created_at`)='".$valdate."' AND locationid='".base64_decode($_REQUEST['id'])."' ORDER BY trackingid DESC LIMIT 0,1" );
				
				if(mysqli_num_rows($getlastactivity) > 0){
				
				$rowactivity=mysqli_fetch_array($getlastactivity);
				
				$rowactivity['color'] = $rowusers['color'];
				
				$rowactivity['tracked_date'] = $valdate;
				
				if($rowactivity['total_time']!='00:00:00')
				
					$total_time[] = $rowactivity['staffcount'] * (strtotime($rowactivity['total_time']));
					
						if($row_track['travel_time']!='00:00:00')

 	    $traveltime[] = $rowactivity['staffcount'] * (strtotime($rowactivity['travel_time']));
				
				$activity[]=$rowactivity;
				}
		}
	}
	
	if(!empty($total_time)){
	
	$total_hrs=array_sum($total_time);
	
	$overall_total=date('H:i:s',$total_hrs);
	
}

else{
	
	$overall_total="00:00:00";
}
	
	
	if(!empty($traveltime)){
	
	$total_travel = array_sum($traveltime);
	
	
	$Total_travel_time = date('H:i:s',$total_travel);
	}
	else
	
	$Total_travel_time="00:00:00";
	
			include('templates/header.php');
		
		 include('views/tracking-view.htm');
		
		 include('templates/footer.php');
			


}
else
{
	  header('Location:index.php');
}