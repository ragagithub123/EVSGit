<?php ob_start();
session_start();
ini_set('memory_limit', '16M');
include('../includes/functions.php');
echo $_POST['status'];die();
if(!empty($_FILES['file']['name']))
{
	$temp = explode(".", $_FILES["file"]["name"]);
			$newfilename = $temp[0].time().$temp[1];
			move_uploaded_file($_FILES['file']['tmp_name'], $gAttachmentDir."/".$newfilename);
			$db->upd_rec('location_attachments',array('attachment'=>$newfilename),"attachmentid = '".$_POST['attachmentid']."'");
				$gte_loc=$db->joinquery('SELECT locationid FROM location_attachments WHERE attachmentid='.$_POST['attachmentid'].'');
			$row_loc=mysqli_fetch_array($gte_loc);
			$locationid=$row_loc['locationid'];
			echo $locationid;
	 //$image_info = getimagesize($_FILES["file"]["tmp_name"]);
		
//echo $image_info[0];
}
