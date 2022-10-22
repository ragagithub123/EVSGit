<?php ob_start();
session_start();
//echo ini_get('max_execution_time'); die();
include('includes/functions.php');
if(!empty($_SESSION['agentid'])){
	
$Locationid=base64_decode($_REQUEST['id']);

	$get_details=$db->joinquery("SELECT location.locationid,location.`unitnum`,location.`street`,location.`suburb`,location.`city`,`location`.locationstatusid,location.notes,location.photoid,location.`status1`,location.`status2`,location.status3,location.status4,location.status5,location.status6,location.status7,location.status8,location.status9,location.status10,location.status11,location.status12,location.status13,location.status14,location.status15,location.hs_overheadpower,location.hs_siteaccess_notes,location.hs_vegetation_notes,location.hs_heightaccess_notes,location.hs_heightaccess_photoid,location.hs_childrenanimals_notes,location.hs_traffic_notes,location.hs_weather_notes,location.hs_worksite_notes,location.distance,location.travel_rate,location.`quotesdg`,location.`quotemaxe`,location.`quotexcle`,location.`quoteevsx2`,location.`quoteevsx3`,customer.customerid,customer.firstname,customer.lastname,customer.email,customer.phone,agent.labourrate,photo.width,photo.height FROM location LEFT JOIN photo ON location.photoid=photo.photoid ,agent,customer WHERE location.agentid=agent.agentid AND location.customerid=customer.customerid AND location.`locationid`=".$Locationid."");
	
	$row_details= mysqli_fetch_array($get_details);

$getsettings = $db->joinquery("SELECT * FROM tasktoolsettings WHERE locationid ='".$Locationid."'");

if(mysqli_num_rows($getsettings)>0)

$rowsettings = mysqli_fetch_array($getsettings);

else

$rowsettings ="";

$famecat =array();

$get_window=$db->joinquery("SELECT window.roomid,window.windowid,window.`selected_product`,window.`selected_hours` FROM room,window WHERE window.roomid=room.roomid AND room.locationid=".$Locationid."  GROUP BY window.windowid ");
if(mysqli_num_rows($get_window)>0){
while($row_window=mysqli_fetch_array($get_window))
{
	
	    $get_extras=$db->joinquery("SELECT window_extras.*,products.* FROM window_extras,products WHERE window_extras.productid=products.productid AND window_extras.windowid='".$row_window['windowid']."' ORDER BY extraid DESC");
					if(mysqli_num_rows($get_extras)>0){
							
							while($row_extras=mysqli_fetch_array($get_extras)){
								
								$totalextrahours[] =$row_extras['hours'];
							
								
				}
			}
			else{
				   $totalextrahours =array();
			}
					
			$getlabour=$db->joinquery("SELECT panelid,SUM(dglabour) AS igulabour, SUM(evslabour) AS evslabour FROM panel WHERE windowid = ".$row_window['windowid']."");
			
			if(mysqli_num_rows($getlabour)>0){
				
				$row_labour=mysqli_fetch_array($getlabour);
				
				if($row_window['selected_product']!='HOLD'){
					
					if($row_window['selected_product']=="SGU" || $row_window['selected_product']=="SDG" || $row_window['selected_product']=="IGUX2" || $row_window['selected_product']=="IGUX3" || $row_window['selected_product']=="XCLe"){
					
				  $total_install_hrs[]= $row_labour['igulabour'];
						
						$row_window['producttype'] = 'Retro';
				
					}
					   
     
					else if($row_window['selected_product']=="EVSx2" || $row_window['selected_product']=="EVSx3"){
						
							
							$total_install_hrs[]=$row_labour['evslabour'];
							
							$row_window['producttype'] = 'EVS';
					}
				
					else{
					
					$total_install_hrs=array();
				
				}
					
					
				}
			}
			else{
					$total_install_hrs=array();
			}
				$taskTools = array();
				$taskMaterials=array();
   $get_panel = $db->joinquery("SELECT panel.panelid, panel.styleid,paneloption_style.category,famecategory.category,famecategory.famecategoryid FROM panel,paneloption_style,famecategory WHERE panel.styleid=paneloption_style.styleid AND paneloption_style.category=famecategory.famecategoryid AND panel.windowid='".$row_window['windowid']."'  GROUP BY paneloption_style.category");
			
			if(mysqli_num_rows($get_panel)>0){
			
			while($row_panel =mysqli_fetch_array($get_panel)){
				
				if($row_window['selected_product']=="SGU" || $row_window['selected_product']=="SDG" || $row_window['selected_product']=="IGUX2" || $row_window['selected_product']=="IGUX3" || $row_window['selected_product']=="XCLe"){
					
						
						$producttype = 'Retro';
						
						$pdt ='retro';
				
					}
					   
     
					else if($row_window['selected_product']=="EVSx2" || $row_window['selected_product']=="EVSx3"){
						
							$pdt ='evs';
							
							$producttype = 'EVS';
					}
					
					if(!in_array($row_panel['famecategoryid'],$famecat)){
				
				$famecat[] = $row_panel['famecategoryid'];
				
				$row_window['categoryType'] =$producttype." ".$row_panel['category'];
				
		  $gettasks = $db->joinquery("SELECT * FROM taskTools WHERE categoryid ='".$row_panel['famecategoryid']."' AND type='".$pdt."'");
				
				while($rowtask = mysqli_fetch_assoc($gettasks)){
					
					$getfolw = $db->joinquery("SELECT * FROM  tasktoolflow WHERE tasktoolid='".$rowtask['tasktoolid']."' AND category='".$row_panel['famecategoryid']."' AND type='".$pdt."' AND locationid='".$Locationid."'");
					
					if(mysqli_num_rows($getfolw)>0)
					
					$rowtask['checkStatus'] =1;
					
					else
					
					$rowtask['checkStatus'] =0;
					
					 $taskTools[] = $rowtask;
					
				}
				
				$getmaterials = $db->joinquery("SELECT * FROM taskMaterials WHERE categoryid ='".$row_panel['famecategoryid']."' AND type='".$pdt."' ");
				
				while($rowmat = mysqli_fetch_assoc($getmaterials)){
					
						$getmat = $db->joinquery("SELECT * FROM  taskmaterialflow WHERE materialid='".$rowmat['taskmaterialid']."' AND category='".$row_panel['famecategoryid']."' AND type='".$pdt."' AND locationid='".$Locationid."'");
					
					if(mysqli_num_rows($getmat)>0)
					
					$rowmat['checkstatus'] =1;
					
					else
					
					$rowmat['checkstatus'] =0;
					
					 $taskMaterials[] = $rowmat;
					
				}

				$row_window['tasktool'] = $taskTools;
				
				$row_window['materials'] = $taskMaterials;
				
					$postpanel[] = $row_window;
				
			}
				
			}
			
}
			
			
		
	
}
}

else{
	$postpanel =array();
}
$gettools = $db->joinquery("SELECT * FROM Tools");
if(mysqli_num_rows($gettools)>0){
while($rowtool = mysqli_fetch_assoc($gettools)){
	
	$tools[]= $rowtool;
	
}
}
else{
	 
		$tools =array();
	
}

if(count($totalextrahours)>0)
				
	$total_extra_hrs = array_sum($totalextrahours);
				
	else
				
	$total_extra_hrs = 0;
	
		if(count($total_install_hrs)>0)
			
			$totalinstallhrs = array_sum($total_install_hrs);
			
			else
			
			$totalinstallhrs = 0;

	
 include('templates/header.php');
 include('views/tasktoolsheet.htm');
 include('templates/footer.php');

	
}
else
{
	  header('Location:index.php');
}