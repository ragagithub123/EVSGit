<?php ob_start();
session_start();
ini_set('memory_limit', '16M');
include('../includes/functions.php');
if(!empty($_FILES['file']['name']))
{
	 $image_info = getimagesize($_FILES["file"]["tmp_name"]);
		
 if(!empty($image_info[0])&& !empty($image_info[1]))
	{
		 $image_data = array(
			'width'	 =>$image_info[0],
			'height'	 => $image_info[1],
			
		);
		
		 $photoid = $db->ins_rec("photo", $image_data);	
		
			$temp = explode(".", $_FILES["file"]["name"]);
			$newfilename = $photoid.".jpg" ;
			move_uploaded_file($_FILES['file']['tmp_name'], $gPhotoDir."/".$newfilename);
			copy($gPhotoDir . "/" . $newfilename, $DPhotoDir . "/" . $newfilename);
			$db->upd_rec('location',array('photoid'=>$photoid),"locationid = '".$_POST['locationid']."'");
		
	}

		
			
}
if(empty($_POST['loc_status']))$locsttaus=1;else $locsttaus=$_POST['loc_status'];
$locationSearch=$_POST['unitnum'].",".$_POST['street'];
if(!empty($_POST['suburb']))
{
		$locationSearch.=",".$_POST['suburb'];
}
$locationSearch.=",".$_POST['city'];
$newloc=$locationSearch;
	    $location=str_replace(' ', '%20', $newloc);
					$res=file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address='".$location."'&key=AIzaSyD91uB2dG7Lyj4xOWJwxT-lq9uQ0FtwU0o");
					$result_arr=json_decode($res);
					$lat=$result_arr->results[0]->geometry->location->lat;
					$lng=$result_arr->results[0]->geometry->location->lng;
					$db->joinquery("UPDATE location SET `unitnum`='".$_POST['unitnum']."',`street`='".mysqli_escape_string($db->connection,$_POST['street'])."',`suburb`='".mysqli_escape_string($db->connection,$_POST['suburb'])."',`city`='".mysqli_escape_string($db->connection,$_POST['city'])."',`latitude`='".$lat."',`longitude`='".$lng."',`locationstatusid`='".$locsttaus."',`locationSearch`='".mysqli_escape_string($db->connection,$locationSearch)."' WHERE locationid='".$_POST['locationid']."'");
					$db->joinquery("UPDATE customer SET `firstname`='".mysqli_escape_string($db->connection,$_POST['firstname'])."',`lastname`='".mysqli_escape_string($db->connection,$_POST['lastname'])."',`email`='".$_POST['email']."',`phone`='".$_POST['phone']."' WHERE customerid = '".$_POST['customerid']."'");	
					
			 
//$db->upd_rec('location',array('unitnum'=>$_POST['unitnum'],'street'=>$_POST['street'],'suburb'=>$_POST['suburb'],'city'=>$_POST['city'],'latitude'=>$lat,'longitude'=>$lng,'locationstatusid'=>$locsttaus,'locationSearch'=>$locationSearch),"locationid = '".$_POST['locationid']."'");
//$db->upd_rec('customer',array('firstname'=>$_POST['firstname'],'lastname'=>$_POST['lastname'],'email'=>$_POST['email'],'phone'=>$_POST['phone']),"customerid = '".$_POST['customerid']."'");
//echo 'success';
$get_details=$db->joinquery("SELECT location.`unitnum`,location.`street`,location.`suburb`,location.`city`,`location`.locationstatusid,location.notes,location.photoid,location.`status1`,location.`status2`,location.status3,location.status4,location.status5,location.status6,location.status7,location.status8,location.status9,location.status10,location.status11,location.status12,location.status13,location.hs_overheadpower,location.hs_siteaccess_notes,location.hs_vegetation_notes,location.hs_heightaccess_notes,location.hs_heightaccess_photoid,location.hs_childrenanimals_notes,location.hs_traffic_notes,location.hs_weather_notes,location.hs_worksite_notes,customer.customerid,customer.firstname,customer.lastname,customer.email,customer.phone,agent.labourrate,photo.width,photo.height FROM location LEFT JOIN photo ON location.photoid=photo.photoid ,agent,customer WHERE location.agentid=agent.agentid AND location.customerid=customer.customerid AND location.`locationid`=".$_POST['locationid']."");
$row_details=mysqli_fetch_array($get_details);
	$loc=$row_details['unitnum'].",".$row_details['street'];
			 if(!empty($row_details['suburb']))
				{
					 $loc.=",".$row_details['suburb'];
				}

?>
<div class="pro_inner_one_details">
                            	<div class="pro_inner_one_pic">
                                 <?php
																																				if($row_details['photoid'] !=0)
																																				{
																																					?>
                                     <img src="http://evsapp.nz/photos/<?php echo $row_details['photoid'];?>.jpg" class="img-responsive"> 
                                     <?php
                                     
																																				}?>
                                </div>
                                <div class="pro_inner_one_list">
                                	<ul>
                                    	<li>Property Address</li>
                                        <li><?php echo $loc;?></li>
                                        <li><?php echo $row_details['city'];?></li>
                                        <li><?php echo $row_details['firstname']." ".$row_details['lastname'];?></li>
                                        <li><?php echo $row_details['phone'];?></li>
                                        <li><?php echo $row_details['email'];?></li>
                                    </ul>
                                   
                                    <b><a  href="<?php echo $quoteURL;?>" style="font-size:18px;" target="_blank">Preview Quote</a></b>
                                   
                                </div>
                            </div>