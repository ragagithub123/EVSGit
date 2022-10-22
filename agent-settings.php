<?php ob_start();
session_start();
include('includes/functions.php');
$quoteurl = $gquotepagePhotoURL;
if(!empty($_SESSION['agentid'])){
		  if($_POST['type']==1){
	   $db->joinquery("UPDATE agent SET `firstname`='".$_POST['first_name']."',`lastname`='".$_POST['last_name']."',`email`='".$_POST['email']."',`unitnum`='".$_POST['unit_number']."',`street`='".$_POST['street']."',`suburb`='".$_POST['suburb']."',`city`='".$_POST['city']."',`postcode`='".$_POST['postcode']."',`phone`='".$_POST['phone']."',`businessname`='".$_POST['business_name']."' WHERE agentid='".$_SESSION['agentid']."'");
				if(!empty($_FILES['brandimage']['name'])){
				$newfilename = $_SESSION['agentid'].".png" ;
				move_uploaded_file($_FILES['brandimage']['tmp_name'], $gLogoPhotoDir.$newfilename);
			
				}
				
				if(!empty($_FILES['signatureimage']['name'])){
				$newsignature = $_SESSION['agentid'].".png" ;
				move_uploaded_file($_FILES['signatureimage']['tmp_name'], $gSignaturePhotoDir.$newsignature);
	
				}
				
	}
	
	 if($_POST['type']==2){
		$db->joinquery("UPDATE agent SET `SGUname`='".mysqli_escape_string($db->connection,$_POST['sgu_name'])."',`IGUx2name`='".mysqli_escape_string($db->connection,$_POST['igux2_name'])."',`IGUx3name`='".mysqli_escape_string($db->connection,$_POST['igux3_name'])."',`EVSx2name`='".mysqli_escape_string($db->connection,$_POST['EVSx2_name'])."',`EVSx3name`='".mysqli_escape_string($db->connection,$_POST['EVSx3_name'])."',`SGUrate`='".$_POST['SGUrate']."',`wagerate`='".$_POST['wagerate']."',`IGUx2rate`='".$_POST['IGUx2rate']."',`IGUx3rate`='".$_POST['IGUx3rate']."',`EVSx2rate`='".$_POST['EvsX2rate']."',`EVSx3rate`='".$_POST['EvsX3rate']."',`SGUurl`='".mysqli_escape_string($db->connection,$_POST['SGUurl'])."',`IGUx2url`='".mysqli_escape_string($db->connection,$_POST['IGUx2url'])."',`IGUx3url`='".mysqli_escape_string($db->connection,$_POST['IGUx3url'])."',`EvsX2url`='".mysqli_escape_string($db->connection,$_POST['EvSx2url'])."',`EVSx3url`='".mysqli_escape_string($db->connection,$_POST['EvSx3url'])."',`productmargin`='".$_POST['productmargin']."',`igumargin`='".$_POST['igumargin']."',`evsmargin`='".$_POST['evsmargin']."',`agenttravelrate`='".$_POST['agenttravelrate']."',`labourrate`='".$_POST['labourrate']."' WHERE agentid='".$_SESSION['agentid']."'");
			if(!empty($_FILES['sguimage']['name'])){
				$sguimage = "sguimage_".$_SESSION['agentid'].".png" ;
				move_uploaded_file($_FILES['sguimage']['tmp_name'], $gProductDir.$sguimage);
			$db->joinquery("UPDATE agent SET SGUimage='$sguimage' WHERE agentid='".$_SESSION['agentid']."'");
				}
				
				if(!empty($_FILES['igux2image']['name'])){
				$igux2image = "igux2image_".$_SESSION['agentid'].".png" ;
				move_uploaded_file($_FILES['igux2image']['tmp_name'], $gProductDir.$igux2image);
				$db->joinquery("UPDATE agent SET IGUx2image='$igux2image' WHERE agentid='".$_SESSION['agentid']."'");
	
				}
				
				if(!empty($_FILES['igux3image']['name'])){
				$igux3image = "igux3image_".$_SESSION['agentid'].".png" ;
				move_uploaded_file($_FILES['igux3image']['tmp_name'], $gProductDir.$igux3image);
				$db->joinquery("UPDATE agent SET IGUx3image='$igux3image' WHERE agentid='".$_SESSION['agentid']."'");
	
				}
				
				if(!empty($_FILES['evsx2image']['name'])){
				$evsx2image = "evsx2image_".$_SESSION['agentid'].".png" ;
				move_uploaded_file($_FILES['evsx2image']['tmp_name'], $gProductDir.$evsx2image);
				$db->joinquery("UPDATE agent SET EVSx2image='$evsx2image' WHERE agentid='".$_SESSION['agentid']."'");
	
				}
				
				if(!empty($_FILES['evsx3image']['name'])){
				$evsx3image = "evsx3image_".$_SESSION['agentid'].".png" ;
				move_uploaded_file($_FILES['evsx3image']['tmp_name'], $gProductDir.$evsx3image);
				$db->joinquery("UPDATE agent SET EVSx3image='$evsx3image' WHERE agentid='".$_SESSION['agentid']."'");
	
				}
		
	}
	
	 if($_POST['type']==3){
			
			
			$quote_date = date('Y-m-d',strtotime($_POST['quote_date']));
			
			if($_POST['paintlock']==1)
			
			$paintlock =1;
			
			else
			
			$paintlock =0;
			
			if($_POST['stepslock']==1)
			
			$stepslock =1;
			
			else
			
			$stepslock =0;
				
		
		 $db->joinquery("UPDATE agent SET `quotedate`='".$quote_date."',`quotegreeting`='".mysqli_escape_string($db->connection,$_POST['quotegreeting'])."',`quotedetails`='".mysqli_escape_string($db->connection,$_POST['quotedetails'])."',paintdetails='".mysqli_escape_string($db->connection,$_POST['paintdetails'])."',stepdetails='".$_POST['steps']."',paintlock='".$paintlock."',steplock='".$stepslock."'  WHERE agentid='".$_SESSION['agentid']."'");


		    for($i=1;$i<=$_POST['imagecount'];$i++){
							
							$page="page".$i;

						$result_page=  $db->joinquery("SELECT * FROM quote_pages WHERE agentid='".$_SESSION['agentid']."' AND pages='".$page."'");
						
						if(mysqli_num_rows($result_page) == 0)

					$db->joinquery("INSERT INTO quote_pages(`agentid`,`pages`)values('".$_SESSION['agentid']."','$page')");

					if(!empty($_FILES['userfile'.$i]['name'])){

				$file_name = time().$_FILES['userfile'.$i]['name'];

				move_uploaded_file($_FILES['userfile'.$i]['tmp_name'], $gquotepagePhotoDir.$file_name);

		  $db->joinquery("UPDATE quote_pages SET images='".$file_name."' WHERE agentid='".$_SESSION['agentid']."' AND pages='".$page."'");

				

			}
			  
		   }

				
					

	}
	
	if($_POST['type'] == 4){
		
			$salt = rand(10000, 99999). rand(10000, 99999). rand(10000, 99999). rand(10000, 99999);
			
			$password = hash('sha256', $salt.$_POST['team_user_pass']); 
			
			if(!empty($_POST['team_user_induction']))
			
			$induction =date('Y-m-d',strtotime($_POST['team_user_induction']));
			
			else
			
			$induction ="";
			
			if(!empty($_POST['team_user_tools']))
			
			$tools =date('Y-m-d',strtotime($_POST['team_user_tools']));
			
			else
			
			$tools ="";
			
			if(!empty($_POST['team_user_uniform']))
			
			$uniform =date('Y-m-d',strtotime($_POST['team_user_uniform']));
			
			else
			
			$uniform ="";
			
			if(!empty($_POST['team_user_ppe']))
			
			$ppe = date('Y-m-d',strtotime($_POST['team_user_ppe']));
			
		else
		
		$ppe = "";	
			
			if($_POST['team_id']==0){
				
				 $result = $db->ins_rec('Team',array('agentid'=>$_SESSION['agentid'],'first_name'=>$_POST['team_first_name'],'last_name'=>$_POST['team_last_name'],'username'=>$_POST['team_user_name'],'passwordhash'=>$password,'passwordsalt'=>$salt,'password'=>$_POST['team_user_pass'],'email'=>$_POST['team_user_email'],'address1'=>$_POST['team_user_addres1'],'address2'=>$_POST['team_user_addres2'],'phone'=>$_POST['team_user_phone'],'roleid'=>$_POST['team_user_role'],'acess'=>$_POST['team_user_access'],'status'=>$_POST['team_user_status'],'induction'=>$induction,'tools'=>$tools,'uniform'=>$uniform,'ppe'=>$ppe,'notes'=>$_POST['team_user_notes'],'ird'=>$_POST['team_user_ird']));
		 
			   $id =$result;
				
			}
			
			else{
				
				$id = $_POST['team_id'];
			 
				$where= "team_id=".$id."";
				
				 $db->upd_rec('Team',array('first_name'=>$_POST['team_first_name'],'last_name'=>$_POST['team_last_name'],'username'=>$_POST['team_user_name'],'passwordhash'=>$password,'passwordsalt'=>$salt,'password'=>$_POST['team_user_pass'],'email'=>$_POST['team_user_email'],'address1'=>$_POST['team_user_addres1'],'address2'=>$_POST['team_user_addres2'],'phone'=>$_POST['team_user_phone'],'roleid'=>$_POST['team_user_role'],'acess'=>$_POST['team_user_access'],'status'=>$_POST['team_user_status'],'induction'=>$induction,'tools'=>$tools,'uniform'=>$uniform,'ppe'=>$ppe,'notes'=>$_POST['team_user_notes'],'ird'=>$_POST['team_user_ird']),$where);
				
			}
		
		
		if(!empty($_FILES['team_profile_pic']['name'])){
			
			 $exp_profile =explode('.',$_FILES['team_profile_pic']['name']);
			 $profilepic = $exp_profile[0]."_".$id.".".$exp_profile[1] ;
				move_uploaded_file($_FILES['team_profile_pic']['tmp_name'], $gTeamPhotoDir.$profilepic);
				$db->joinquery("UPDATE Team SET profile_pic='$profilepic' WHERE team_id='".$id."'");
	
				}
				
					if(!empty($_FILES['team_driving_license']['name'])){
			
			 $exp_driving =explode('.',$_FILES['team_driving_license']['name']);
			 $driving = $exp_driving[0]."_".$id.".".$exp_driving[1] ;
				move_uploaded_file($_FILES['team_driving_license']['tmp_name'], $gTeamdrivingDir.$driving);
				$db->joinquery("UPDATE Team SET driving_licence='$driving' WHERE team_id='".$id."'");
	
				}
				
					if(!empty($_FILES['team_cv']['name'])){
			
			 $exp_cv =explode('.',$_FILES['team_cv']['name']);
			 $cv = $exp_cv[0]."_".$id.".".$exp_cv[1] ;
				move_uploaded_file($_FILES['team_cv']['tmp_name'], $gTeamCVDir.$cv);
				$db->joinquery("UPDATE Team SET cv='$cv' WHERE team_id='".$id."'");
	
				}
				
				if(!empty($_FILES['team_other']['name'])){
			
			 $exp_other =explode('.',$_FILES['team_other']['name']);
			 $other = $exp_other[0]."_".$id.".".$exp_other[1] ;
				move_uploaded_file($_FILES['team_other']['tmp_name'], $gTeamOtherDir.$other);
				$db->joinquery("UPDATE Team SET other='$other' WHERE team_id='".$id."'");
	
				}
		
	}

	$folder = $gAttachmentDir.$_SESSION['agentid']."/";
if(!file_exists($folder))
mkdir($gAttachmentDir."/" . $_SESSION['agentid'], 0777);

$foldersize=foldersize($folder);
$foldercapacity= format_size($foldersize);	
$exp_size = explode(" ",$foldercapacity);
if($exp_size[1]=="KB")
$usedSpace = ($exp_size[0]/1000);//convert to MB
else if($exp_size[1]=="GB")
$usedSpace = ($exp_size[0]*1000);//convert to MB
else if($exp_size[1]=="MB")
$usedSpace = $exp_size[0];
$used_space = round(($usedSpace/1000),2);
$freespace=10000 ;// MB,10 GB;
	
$getteams = $db->joinquery("SELECT * FROM Team WHERE agentid='".$_SESSION['agentid']."'");
while($rowteams = mysqli_fetch_array($getteams))	{
	
	$getdata = $db->joinquery("SELECT role FROM teamroles WHERE roleid='".$rowteams['roleid']."'");
	
	$rowdata = mysqli_fetch_array($getdata);
	
	$rowteams['roleid'] = $rowdata['role'];
	
	$Teams[]= $rowteams;
	
}
	
$getRoles = $db->joinquery("SELECT * FROM teamroles ");	
while($rowrols=mysqli_fetch_assoc($getRoles)){
	
	$roles[]= $rowrols;
	
}

$getagentdeatils=$db->joinquery("SELECT * FROM agent WHERE agentid='".$_SESSION['agentid']."'");
$rowagent=mysqli_fetch_assoc($getagentdeatils);
if(file_exists($gLogoPhotoDir.$_SESSION['agentid'].".png"))
	{
				$rowagent['brand_image']=$gLogoPhotoURL.$_SESSION['agentid'].".png";
	}
	else
	{
				$rowagent['brand_image']="";
	}
	if(file_exists($gSignaturePhotoDir.$_SESSION['agentid'].".png"))
	{
				$rowagent['signature']=$gSignaturePhotoURL.$_SESSION['agentid'].".png";
	}
	else
	{
				$rowagent['signature']="";
				
	}
	if(!empty($rowagent['SGUimage']))
		$rowagent['SGUimage']=$gProductURL.$rowagent['SGUimage'];
		else
	$rowagent['SGUimage']="";
	if(!empty($rowagent['IGUx2image']))
		$rowagent['IGUx2image']=$gProductURL.$rowagent['IGUx2image'];
		else
	$rowagent['IGUx2image']="";
	if(!empty($rowagent['IGUx3image']))
		$rowagent['IGUx3image']=$gProductURL.$rowagent['IGUx3image'];
		else
	$rowagent['IGUx3image']="";
		if(!empty($rowagent['EVSx2image']))
		$rowagent['EVSx2image']=$gProductURL.$rowagent['EVSx2image'];
		else
	$rowagent['EVSx2image']="";
	if(!empty($rowagent['EVSx3image']))
		$rowagent['EVSx3image']=$gProductURL.$rowagent['EVSx3image'];
		else
	$rowagent['EVSx3image']="";

	$quotepages=$db->joinquery("SELECT * FROM quote_pages WHERE agentid='".$_SESSION['agentid']."'");
	if(mysqli_num_rows($quotepages)>0){

		while($rowquote=mysqli_fetch_assoc($quotepages)) {


			$images[] = $rowquote;

		}
		
		$imagecnt =count($images);

	}
else

$imagecnt =0;
	
   

include('templates/header.php');
include('views/agent-settings.htm');
include('templates/footer.php');
			
  
}
else
{
	  header('Location:index.php');
}

function foldersize($path) {
	$total_size = 0;
	$files = scandir($path);

	foreach($files as $t) {
		if (is_dir(rtrim($path, '/') . '/' . $t)) {
			if ($t<>"." && $t<>"..") {
					$size = foldersize(rtrim($path, '/') . '/' . $t);

					$total_size += $size;
			}
		} else {
			$size = filesize(rtrim($path, '/') . '/' . $t);
			$total_size += $size;
		}
	}
	return $total_size;
}

function format_size($size) {
	$mod = 1024;
	$units = explode(' ','B KB MB GB TB PB');
	for ($i = 0; $size > $mod; $i++) {
		$size /= $mod;
	}

	return round($size, 2) . ' ' . $units[$i];
}

?>