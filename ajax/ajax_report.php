<?php
ob_start();
session_start();
include('../includes/functions.php');
if(!empty($_SESSION['agentid'])){
	if(!empty($_POST['locationid'])){
		
		$Locationid=$_POST['locationid'];
}

else{
	$text = str_replace("+"," ",$_POST['locationtext']);
	$Stext = '"'.$text.'"';
	$getlocid=$db->joinquery("SELECT locationid FROM location WHERE locationSearch=$Stext AND agentid ='".$_SESSION['agentid']."'");
$locid=mysqli_fetch_array($getlocid);
$Locationid=$locid['locationid'];

	
	}
	

$get_window=$db->joinquery("SELECT room.`roomid`,room.`name` AS room_name,window.quote_status,window.notes,window.extras,window.windowid,window.windowtypeid,window.`selected_product`,window.`materialCategory`,window_type.name FROM room,window,window_type WHERE window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid  AND room.locationid=".$Locationid." GROUP BY window.windowid ORDER BY room_name ASC");
//$get_window = $db->joinquery("SELECT room.`roomid`,room.`name` AS room_name,window.windowid,window.windowtypeid,window.`selected_product`,window.`materialCategory`,window_type.name FROM room,window,window_type WHERE window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid  AND room.locationid=" . $Locationid . " GROUP BY window.windowid ORDER BY room_name ASC");

if(mysqli_num_rows($get_window)>0){
	
	$i=64;
while($row_window=mysqli_fetch_array($get_window))
{
	   
	
	  
	$i=$i+1;
				
if($row_window['selected_product']!='HOLD'){
	
	
	
	
	
	
		if($_POST['list_value'] == "Full list(Measurements,X,Y)")
		{
			
					$get_panel=$db->joinquery("SELECT panel.panelid,panel.width,panel.height,panel.center,panel.measurement,panel.panelnum,panel.profileid,panel.windowid,panel.`safetyid`,panel.`glasstypeid`,panel.`styleid`,panel.`conditionid`,panel.`astragalsid`,`paneloption_style`.name AS stylename,paneloption_style.`evsProfileTop`,paneloption_style.`evsProfileSides`,paneloption_style.`evsProfileBottom`,paneloption_style.`evsGlassX`,paneloption_style.`evsGlassY`,paneloption_style.`evsProfileX`,paneloption_style.`evsProfileY`,paneloption_style.`retroProfileTop`,paneloption_style.`retroProfileSides`,paneloption_style.`retroProfileBottom`,paneloption_style.`retroGlassX`,paneloption_style.`retroGlassY`,paneloption_style.`retroProfileX`,paneloption_style.`retroProfileY`,paneloption_style.evsProfileRight,paneloption_style.evsProfileLeft,paneloption_style.evsOutPanelThickness,paneloption_style.evsOutPanelType,paneloption_style.evsInPanelThickness,paneloption_style.evsInPanelType,paneloption_style.retroOutPanelThickness,paneloption_style.retroOutPanelType,paneloption_style.retroInPanelThickness,paneloption_style.retroInPanelType,paneloption_style.retroProfileLeft,paneloption_style.retroProfileRight,paneloption_astragal.name AS astragal_name,paneloption_condition.name AS condition_name,paneloption_safety.name AS safty_name,paneloption_glasstype.name AS galsstype_name,paneloption_glasstype.typevalue FROM panel,paneloption_astragal,paneloption_safety,paneloption_style,paneloption_glasstype,paneloption_condition WHERE 
panel.styleid=paneloption_style.styleid AND panel.safetyid=paneloption_safety.safetyid AND panel.astragalsid=paneloption_astragal.astragalsid AND panel.glasstypeid=paneloption_glasstype.glasstypeid AND panel.conditionid=paneloption_condition.conditionid AND panel.windowid=".$row_window['windowid']." ORDER BY panel.width DESC");
						

			
		
		}
		else
		{
			  					$get_panel=$db->joinquery("SELECT panel.panelid,panel.width,panel.height,panel.center,panel.measurement,panel.panelnum,panel.profileid,panel.windowid,panel.`safetyid`,panel.`glasstypeid`,panel.`styleid`,panel.`conditionid`,panel.`astragalsid`,`paneloption_style`.name AS stylename,paneloption_style.`evsProfileTop`,paneloption_style.`evsProfileSides`,paneloption_style.`evsProfileBottom`,paneloption_style.`evsGlassX`,paneloption_style.`evsGlassY`,paneloption_style.`evsProfileX`,paneloption_style.`evsProfileY`,paneloption_style.`retroProfileTop`,paneloption_style.`retroProfileSides`,paneloption_style.`retroProfileBottom`,paneloption_style.`retroGlassX`,paneloption_style.`retroGlassY`,paneloption_style.`retroProfileX`,paneloption_style.`retroProfileY`,paneloption_style.evsProfileRight,paneloption_style.evsProfileLeft,paneloption_style.evsOutPanelThickness,paneloption_style.evsOutPanelType,paneloption_style.evsInPanelThickness,paneloption_style.evsInPanelType,paneloption_style.retroOutPanelThickness,paneloption_style.retroOutPanelType,paneloption_style.retroInPanelThickness,paneloption_style.retroInPanelType,paneloption_style.retroProfileLeft,paneloption_style.retroProfileRight,paneloption_astragal.name AS astragal_name,paneloption_condition.name AS condition_name,paneloption_safety.name AS safty_name,paneloption_glasstype.name AS galsstype_name,paneloption_glasstype.typevalue FROM panel,paneloption_astragal,paneloption_safety,paneloption_style,paneloption_glasstype,paneloption_condition WHERE 
panel.styleid=paneloption_style.styleid AND panel.safetyid=paneloption_safety.safetyid AND panel.astragalsid=paneloption_astragal.astragalsid AND panel.glasstypeid=paneloption_glasstype.glasstypeid AND panel.conditionid=paneloption_condition.conditionid AND panel.windowid=".$row_window['windowid']."");
}

			
		

		
		$j=0;
	 
	while($row_panel=mysqli_fetch_array($get_panel)){
		
		
		$j++;

	$row_window['panels'] =$row_panel;
	
	$row_window['ID']=chr($i).$j;
	
	  
	$postpanel[]=$row_window;
   
			
			
		 }
				
	
	
	
	
	
	}
	

	
	

	
	
	
}


	
	
}

else{
	
	 $postpanel =array();
}



$get_details=$db->joinquery("SELECT location.`unitnum`,location.`street`,location.`suburb`,location.`city`,location.photoid,agent.firstname AS afirstname,agent.lastname AS alastname,agent.email AS aemail,agent.phone AS aphone,agent.`unitnum` AS aunitnum,agent.`street` AS astreet,agent.`suburb` AS asuburb,agent.`city` AS acity,agent.`postcode` AS apostcode,photo.width,photo.height FROM location LEFT JOIN photo ON location.photoid=photo.photoid ,agent WHERE location.agentid=agent.agentid AND location.`locationid`=".$Locationid."");
$row_details=mysqli_fetch_array($get_details);
$loc=$row_details['unitnum'].",".$row_details['street'];
			 if(!empty($row_details['suburb']))
				{
					 $loc.=",".$row_details['suburb'];
				}
	$aloc=	$row_details['aunitnum'].",".$row_details['astreet'];
	if(!empty($row_details['asuburb']))
				{
					 $aloc.=",".$row_details['asuburb'];
				}
$quoteId = $Locationid."-".hash('sha256', $Locationid.$gQuoteHashSecret);

if($_POST['list_value'] == "Glass list(Glass,X,Y)")
{			

  $quoteURL = $siteURL."/cutlist/$quoteId";
		
		if(isset($_POST['Download'])){
			
	 include_once('glass-reportCSV.php');
		
		}
		
		else{
		
		echo '<div class="table-responsive" >';
	 
		include_once('glass-report.php');
		
		}

}


else if($_POST['list_value'] == "Profile list(Profile,X,Y)")
{
	
	  	if(isset($_POST['Download'])){
			
	 include_once('profile-listCSV.php');
		
		}
		
		else{
			
			  $quoteURL = $siteURL."/profilelist/$quoteId";
						
						echo '<div class="table-responsive" >';

	    include_once('profile-report.php');

			
		}
	
	
	  			
}



else
{
	
	 if($_POST['list_value'] == "Full list(Glass,X,Y)" && isset($_POST['Download'])){
			
			include_once('glassListCSV.php');
			
		}
	else{
		
		  if(isset($_POST['Download'])){
				
				include_once('measurementCSV.php');
				
				}
				
				else{
		
	 	echo '<div class="table-responsive" >';
																																
																																
																																$i=0;
																																if(count($postpanel)>0)
																																{ 
?>
                    	<table class="table table-bordered table-striped fontstyle blue-border-table">
                        	<thead>
                            	<tr style="color:#fff; background:#565759;">
                             <th>ID</th>
                                	<th colspan="2">Cut List</th>
                                    <th colspan="5">Measurements</th>
                                    <th colspan="5">Glass</th>
                                    <th colspan="3">Top</th>
                                    <th colspan="3">Bottom</th>
                                    <th colspan="3">Sides(Left)</th>
                                    <th colspan="3">Sides(Right)</th>
                                </tr>
                            </thead>
                            <tbody>
                            	<tr>
                             <th>#</th>
                               <th>Room</th>
                                	<th>Window[Panel]</th>
                                    <th>Style</th>
                                     <th>Safety</th>
                                   
                                    <th>Height</th>
                                     <th>Width</th>
                                    <th>Center</th>
                                    <th>(+w)</th>
                                    <th>W</th>
                                    <th>(+h)</th>
                                    <th>H</th>
                                    <th>m2</th>
                                    <th>Profile</th>
                                    <th>(+w)</th>
                                    <th>(W)</th>
                                    <th>Profile</th>
                                    <th>(+w)</th>
                                    <th>(W)</th>
                                    <th>Profile</th>
                                     <th>(+h)</th>
                                   <th>(H)</th>
                                  <th>Profile</th>
                                   <th>(+h)</th>
                                   <th>(H)</th>
                                </tr>
                                
                                <?php
																														
																																     if($_POST['list_value'] == "Full list(Glass,X,Y)"){
																																				
																																									include_once('glass-list.php');
																																						}
																																						else{
																																						     
																																				   foreach($postpanel as $row_panel){
																																								
																																									 $i++;
																																									?>
                                           	<tr>
                                            <td><?php echo $row_panel['ID'];?></td>
                                            <td><?php echo $row_panel['room_name'];?></td>
                                	<td><?php echo $row_panel['name']."[".$row_panel['panels']['panelnum']."]";?></td>
                                    <td><?php if($row_panel['panels']['styleid'] > 0 && file_exists($gPanelOptionsPhotoDir.$row_panel['panels']['styleid'].".png"))
																													{
																																	echo "<img src=\"". $gPanelOptionsPhotoURL.$row_panel['panels']['styleid'].".png?". time(). "\" class=\"img-responsive\" style=\"width: 50px; height; 50px;\">";
																													} ?></td>
                             <td><?php echo $row_panel['panels']['safty_name'];?></td>
                                   
                                   <td <?php if($row_panel['panels']['measurement']=='estimate'){?> style="color:#F00"<?php }?>><?php echo $row_panel['panels']['height'];?> </td>
                                   <td <?php if($row_panel['panels']['measurement']=='estimate'){?> style="color:#F00"<?php }?>><?php echo $row_panel['panels']['width'];?> </td>
                                   <td <?php if($row_panel['panels']['measurement']=='estimate'){?> style="color:#F00"<?php }?>><?php echo $row_panel['panels']['center'];?> </td>
                                   <?php
																																			if($row_panel['selected_product']=="EVSx3" || $row_panel['selected_product']=="EVSx2"){
																																				$profiletop=$row_panel['panels']['evsProfileTop'];
																																				$profilebottom=$row_panel['panels']['evsProfileBottom'];
																																				$leftprofile=$row_panel['panels']['evsProfileLeft'];
													                       $rightprofile=$row_panel['panels']['evsProfileRight'];
																																				$glassX=$row_panel['panels']['evsGlassX'];
																																				$glassY=$row_panel['panels']['evsGlassY'];
																																				$profileX=$row_panel['panels']['evsProfileX'];
																																				$profileY=$row_panel['panels']['evsProfileY'];
																																				
																																				}
																																				else{
																																				$profiletop=$row_panel['panels']['retroProfileTop'];
																																				$profilebottom=$row_panel['panels']['retroProfileBottom'];
																																				$profilesides=$row_panel['panels']['retroProfileSides'];
																																				$leftprofile=$row_panel['panels']['retroProfileLeft'];
													                       $rightprofile=$row_panel['panels']['retroProfileRight'];
																																				$glassX=$row_panel['panels']['retroGlassX'];
																																				$glassY=$row_panel['panels']['retroGlassY'];
																																				$profileX=$row_panel['panels']['retroProfileX'];
																																				$profileY=$row_panel['panels']['retroProfileY'];
																																				
																																				}
																																				 if($glassX == NULL)$glassX=0;
																																					if($glassY == NULL)$glassY=0;
																																					if($profileX == NULL)$profileX=0;
																																					if($profileY == NULL)$profileY=0;
																																					  if(($row_panel['panels']['center']) > ($row_panel['panels']['height'])){
                                   //  $glassSizey=($row_panel['center'])+($row_panel['height']);
                                     //$profilesizey=($row_panel['center'])+($row_panel['height']);
																																					$glassSizey=($row_panel['panels']['center'])+$glassY;
                                     $profilesizey=($row_panel['panels']['center'])+$profileY;
                                     $m2=round(((($row_panel['panels']['width'] + $glassX)*($row_panel['panels']['center']))*0.000001),2);
                                     }
                                     else{
                                     if($row_panel['panels']['height']>0){
                                     $glassSizey=($row_panel['panels']['height']) + ($glassY);
                                     $profilesizey=($row_panel['panels']['height']) + ($profileY);
                                     }else{
                                       $glassSizey=0;
                                       $profilesizey=0;
                                     }
                                    
                                     $m2=round(((($row_panel['panels']['width'] + $glassX)*($row_panel['panels']['height'] + $glassY))*0.000001),2);
                                     }
																																				?>
                                    <td><?php echo $glassX;?></td>
                                    <td><?php if($row_panel['panels']['width'] >0){echo ($row_panel['panels']['width'] + $glassX);}?></td>
                                    <td><?php echo $glassY;?></td>
                                    <td><?php echo $glassSizey;?></td>
                                    <td><?php if($row_panel['panels']['width'] >0){ echo round(((($row_panel['panels']['width'] + $glassX)*($row_panel['panels']['height'] + $glassY))*0.000001),2);}?></td>
                                    <td>
                                    <?php
																																				if(file_exists($gProfilePhotoDir.$profiletop.".png"))
																																				{
																																					?>
                                      <span><a class="fs-gal" data-url="<?php echo $gProfilePhotoURL.$profiletop;?>.png" style="color:blue;"><?php echo $profiletop;?></a></span>
                                     <?php
																																				}
																																				else
																																				{?><span ><?php echo $profiletop;?></span><?php
																																				}
																																				?>
                                    </td>
                                    <td><?php echo $profileX;?></td>
                                    <td><?php if($row_panel['panels']['width'] >0){echo ($row_panel['panels']['width'] + $profileX);}?></td>
                                    <td> <?php
																																				if(file_exists($gProfilePhotoDir.$profilebottom.".png"))
																																				{
																																					?>
                                      <span><a class="fs-gal" data-url="<?php echo $gProfilePhotoURL.$profilebottom;?>.png" style="color:blue;"><?php echo $profilebottom;?></a></span>
                                     <?php
																																				}
																																				else
																																				{?><span ><?php echo $profilebottom;?></span><?php
																																				}
																																				?></td>
                                    <td><?php echo $profileX;?></td>
                                    <td><?php if($row_panel['panels']['width'] >0){echo ($row_panel['panels']['width'] + $profileX);}?></td>
                                    <td> <?php
																					if(file_exists($gProfilePhotoDir.$leftprofile.".png"))
																					{
																						?>
																							<span><a class="fs-gal" data-url="<?php echo $gProfilePhotoURL.$leftprofile;?>.png" style="color:blue;"><?php echo $leftprofile;?></a></span>
																						<?php
																					}
																					else
																					{?><span><?php echo $leftprofile;?></span><?php
																					}
																					?></td>
                     <td><?php echo $profileY;?></td>
                      <td><?php echo $profilesizey;?></td>
                      <td> <?php
																					if(file_exists($gProfilePhotoDir.$rightprofile.".png"))
																					{
																						?>
																							<span><a class="fs-gal" data-url="<?php echo $gProfilePhotoURL.$rightprofile;?>.png" style="color:blue;"><?php echo $rightprofile;?></a></span>
																						<?php
																					}
																					else
																					{?><span><?php echo $rightprofile;?></span><?php
																					}
																					?></td>
                                    
                                    <td><?php echo $profileY;?></td>
                                     <td><?php echo $profilesizey;?></td>
                                    
                                </tr>
                                         <?php
																																								}//whiile
																																}//else
                
																															?>
                            </tbody>
                        </table>
                        <?php
																								 }//if
																									else{
																										 echo '<span style="color:#F00; font-size:16px; padding-left:250px;">No Data Found</span>';
																									}
																									?>
                    </div>


<?php
	}
}
}

}

else{
	
	  echo '0';
}
?>


