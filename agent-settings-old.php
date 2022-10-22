<?php ob_start();
session_start();
include('includes/functions.php');
if(!empty($_SESSION['agentid'])){
	# for search property
/*$getprop=$db->joinquery("SELECT locationSearch FROM location WHERE agentid='".$_SESSION['agentid']."'");		
		if(mysqli_num_rows($getprop)>0)
			{
					while($row_prop=mysqli_fetch_array($getprop))
					{
							$postLocation[]=$row_prop['locationSearch'];
							
					}
					
			}*/
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
		$db->joinquery("UPDATE agent SET `SGUname`='".$_POST['sgu_name']."',`IGUx2name`='".$_POST['igux2_name']."',`IGUx3name`='".$_POST['IGUx3_name']."',`SGUrate`='".$_POST['SGUrate']."',`IGUx2rate`='".$_POST['IGUx2rate']."',`IGUx3rate`='".$_POST['IGUx3rate']."',`SGUurl`='".$_POST['SGUurl']."',`IGUx2url`='".$_POST['IGUx2url']."',`IGUx3url`='".$_POST['IGUx3url']."',`productmargin`='".$_POST['productmargin']."',`igumargin`='".$_POST['igumargin']."',`evsmargin`='".$_POST['evsmargin']."',`agenttravelrate`='".$_POST['agenttravelrate']."',`labourrate`='".$_POST['labourrate']."' WHERE agentid='".$_SESSION['agentid']."'");
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
		
	}
	
	 if($_POST['type']==3){
				$db->joinquery("UPDATE agent SET `quotegreeting`='".mysqli_escape_string($_POST['quotegreeting'])."',`quotedetails`='".mysqli_escape_string($_POST['quotedetails'])."' WHERE agentid='".$_SESSION['agentid']."'");

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
include('templates/header.php');
include('views/agent-settings.htm');
include('templates/footer.php');
			
  
}
else
{
	  header('Location:index.php');
}