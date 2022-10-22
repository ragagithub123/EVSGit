<?php ob_start();
session_start();
include('../includes/functions.php');
$flag=0;
if($_POST['status']=='move'){
$db->upd_rec('location',array('jobstatusid'=>$_POST['status_id']),"locationid = '".$_POST['locationid']."'");
if($_POST['status_id']==15){

	$getattachments = $db->joinquery("SELECT attachment FROM location_attachments WHERE locationid='".$_POST['locationid']."'");
	if(mysqli_num_rows($getattachments)>0){
		while($rowattach=mysqli_fetch_array($getattachments)){
			
			unlink($gAttachmentDir.$rowattach['attachment']); 
			unlink($gAttachmentDir. $_SESSION['agentid']."/".$rowattach['attachment']); 

		}

		$db->del_rec("attachment","locationid='".$_POST['locationid']."'");
	
	}

}
$flag=1;
}
else if($_POST['status']=='delete'){
		 $get_photo=$db->joinquery("SELECT photoid FROM location WHERE locationid=".$_POST['locationid']."");
		 $row_photo=mysqli_fetch_array($get_photo);
			if($row_photo['photoid']>0){
   unlink($gPhotoDir.$row_photo['photoid']); 
		 unlink($DPhotoDir.$row_photo['photoid']); 
		$db->del_rec("photo","photoid='".$row_photo['photoid']."'");

			}
			$getattachments = $db->joinquery("SELECT attachment FROM location_attachments WHERE locationid='".$_POST['locationid']."'");
			if(mysqli_num_rows($getattachments)>0){
				while($rowattach=mysqli_fetch_array($getattachments)){
					
					unlink($gAttachmentDir.$rowattach['attachment']); 
					unlink($gAttachmentDir. $_SESSION['agentid']."/".$rowattach['attachment']); 

				}

				$db->del_rec("attachment","locationid='".$_POST['locationid']."'");
			
			}
	 $db->del_rec("location","locationid='".$_POST['locationid']."'");
  $flag=1;

}

else if($_POST['status']=='move_account'){
	
 $get_agent=$db->joinquery("SELECT agentid FROM agent WHERE email='".$_POST['email']."'");
	if(mysqli_num_rows($get_agent)>0){
		 $row_agent=mysqli_fetch_array($get_agent);	
					$db->upd_rec('location',array('agentid'=>$row_agent['agentid']),"locationid = '".$_POST['locationid']."'");
					$flag=1;


	}
 else
	{
		$flag=0;

	}


}
	else if($_POST['status']=='duedate')
	{
		 $db->upd_rec('location',array('booking_date'=>$_POST['duedate'],'booking_end_date'=>$_POST['enddate'],'booking_status'=>1,'alarm_Type'=>$_POST['alarm_type'],'booking_notes'=>$_POST['booking_notes']),"locationid = '".$_POST['locationid']."'");
   $flag=1;
	}
	else if($_POST['status']=='donedate'){
		$db->upd_rec('location',array('booking_status'=>0),"locationid = '".$_POST['locationid']."'");
		 $flag=1;
	}
		else if($_POST['status']=='remove'){
		$db->upd_rec('location',array('booking_date'=>'','booking_end_date'=>'','booking_status'=>0),"locationid = '".$_POST['locationid']."'");
		 $flag=1;
	}

  else if($_POST['status']=='newbooking'){

	$db->upd_rec('location',array('booking_date'=>$_POST['startdate'],'booking_end_date'=>$_POST['enddate'],'booking_status'=>1,'alarm_Type'=>$_POST['alarm_type'],'booking_notes'=>$_POST['booking_notes']),"locationid = '".$_POST['locationid']."'");
    $flag=1;

  }

  else if($_POST['status']=='leave'){

	$db->ins_rec('StaffLeaves',array('startdate'=>$_POST['startdate'],'enddate'=>$_POST['enddate'],'agentId'=>$_SESSION['agentid'],'leavetype'=>$_POST['alarm_type'],'leave_notes'=>$_POST['booking_notes'],'staffid'=>$_POST['locationid']));
	$flag=1;

  }



echo $flag;
