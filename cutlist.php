<?php ob_start();
session_start();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>EVS</title>

    <!-- Bootstrap -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <!-- Google font -->
    <link href="https://fonts.googleapis.com/css?family=Lato:400,700&display=swap" rel="stylesheet">
    <!-- Custom style -->
    <link href="../css/styles.css" rel="stylesheet">
    <link href="../css/fs-gal.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.css">
 
  </head>

<body>
<?php
include('includes/functions.php');
?>



 <!-- Full screen gallery. -->
    <div class="fs-gal-view">
        <h1></h1>
        <img class="fs-gal-close" src="images/close.svg" alt="Close gallery" title="Close gallery" />
        <img class="fs-gal-main" src="" alt="" />
    </div>
    <!--end gallery-->

<section class="evs-main-body">
    	<div class="container">
            <div class="row">
                <div class="col-lg-12">
               
                	
                    
                   <?php
																			if(!isset($_REQUEST['id']) || $_REQUEST['id'] == '')
	die("Not Found");
	list($locationId, $hash) = explode('-', $_REQUEST['id']);
if($hash != hash('sha256', $locationId.$gQuoteHashSecret))
	die("Not Found");
	else
{
	 	// $get_panel = $db->joinquery("SELECT room.roomid,room.name as room_name,window_type.name,panel.panelid,panel.width,panel.height,panel.center,panel.panelnum,panel.profileid,panel.windowid,panel.`safetyid`,panel.`glasstypeid`,panel.`styleid`,panel.`conditionid`,panel.`astragalsid`,paneloption_style.`evsProfileTop`,paneloption_style.`evsProfileSides`,paneloption_style.`evsProfileBottom`,paneloption_style.`evsGlassX`,paneloption_style.`evsGlassY`,paneloption_style.`evsProfileX`,paneloption_style.`evsProfileY`,paneloption_style.`retroProfileTop`,paneloption_style.`retroProfileSides`,paneloption_style.`retroProfileBottom`,paneloption_style.`retroGlassX`,paneloption_style.`retroGlassY`,paneloption_style.`retroProfileX`,paneloption_style.`retroProfileY`,paneloption_style.evsProfileRight,paneloption_style.evsProfileLeft,paneloption_style.evsOutPanelThickness,paneloption_style.evsOutPanelType,paneloption_style.evsInPanelThickness,paneloption_style.evsInPanelType,paneloption_style.retroOutPanelThickness,paneloption_style.retroOutPanelType,paneloption_style.retroInPanelThickness,paneloption_style.retroInPanelType,paneloption_style.retroProfileLeft,paneloption_style.retroProfileRight,paneloption_astragal.name AS astragal_name,paneloption_condition.name AS condition_name,paneloption_safety.name AS safty_name,paneloption_glasstype.name AS galsstype_name,paneloption_glasstype.typevalue,window_option.value,window.selected_product FROM room,window_type,window,window_option,window_window_option,panel,paneloption_astragal,paneloption_safety,paneloption_style,paneloption_glasstype,paneloption_condition WHERE room.roomid=window.roomid AND window.windowtypeid=window_type.windowtypeid AND panel.windowid=window.windowid AND window_window_option.windowoptionid=window_option.windowoptionid AND panel.glasstypeid=paneloption_glasstype.glasstypeid AND window_window_option.windowid=window.windowid AND window.selected_product!='HOLD' AND room.locationid ='".$locationId."' GROUP BY panel.panelid");
			$get_panel=$db->joinquery("SELECT room.roomid,room.name as room_name,window_type.name,panel.panelid,panel.width,panel.height,panel.center,panel.panelnum,panel.profileid,panel.windowid,panel.`safetyid`,panel.`glasstypeid`,panel.`styleid`,panel.`conditionid`,panel.`astragalsid`,panel.measurement,paneloption_style.`evsProfileTop`,paneloption_style.`evsProfileSides`,paneloption_style.`evsProfileBottom`,paneloption_style.`evsGlassX`,paneloption_style.`evsGlassY`,paneloption_style.`evsProfileX`,paneloption_style.`evsProfileY`,paneloption_style.`retroProfileTop`,paneloption_style.`retroProfileSides`,paneloption_style.`retroProfileBottom`,paneloption_style.`retroGlassX`,paneloption_style.`retroGlassY`,paneloption_style.`retroProfileX`,paneloption_style.`retroProfileY`,paneloption_style.evsProfileRight,paneloption_style.evsProfileLeft,paneloption_style.evsOutPanelThickness,paneloption_style.evsOutPanelType,paneloption_style.evsInPanelThickness,paneloption_style.evsInPanelType,paneloption_style.retroOutPanelThickness,paneloption_style.retroOutPanelType,paneloption_style.retroInPanelThickness,paneloption_style.retroInPanelType,paneloption_style.retroProfileLeft,paneloption_style.retroProfileRight,paneloption_astragal.name AS astragal_name,paneloption_condition.name AS condition_name,paneloption_safety.name AS safty_name,paneloption_glasstype.name AS galsstype_name,paneloption_glasstype.typevalue,window_option.value,window.selected_product FROM room,window_type,window,window_option,window_window_option,panel,paneloption_astragal,paneloption_safety,paneloption_style,paneloption_glasstype,paneloption_condition WHERE 
room.roomid=window.roomid AND window.windowtypeid=window_type.windowtypeid AND panel.windowid=window.windowid AND window_window_option.windowoptionid=window_option.windowoptionid AND panel.styleid=paneloption_style.styleid AND panel.safetyid=paneloption_safety.safetyid AND panel.astragalsid=paneloption_astragal.astragalsid AND panel.glasstypeid=paneloption_glasstype.glasstypeid AND panel.conditionid=paneloption_condition.conditionid AND window_window_option.windowid=window.windowid AND window.selected_product!='HOLD' AND room.locationid ='".$locationId."' GROUP BY panel.panelid");
				$get_details=$db->joinquery("SELECT location.`unitnum`,location.`street`,location.`suburb`,location.`city`,location.photoid,agent.firstname AS afirstname,agent.lastname AS alastname,agent.email AS aemail,agent.phone AS aphone,agent.`unitnum` AS aunitnum,agent.`street` AS astreet,agent.`suburb` AS asuburb,agent.`city` AS acity,agent.`postcode` AS apostcode,photo.width,photo.height FROM location LEFT JOIN photo ON location.photoid=photo.photoid ,agent WHERE location.agentid=agent.agentid AND location.`locationid`=".$locationId."");
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
				if(mysqli_num_rows($get_panel)>0)
			{
											while($row_panes=mysqli_fetch_assoc($get_panel))
											{  
														if($row_panes['selected_product']=="EVSx3" || $row_panes['selected_product']=="EVSx2")
														{
																	
																	$glassX=$row_panes['evsGlassX'];
																	$glassY=$row_panes['evsGlassY'];
																
																	
															}
															else{
																
																	$glassX=$row_panes['retroGlassX'];
																	$glassY=$row_panes['retroGlassY'];
																
																	
																	}
																		if($glassX == NULL)$glassX=0;
																		if($glassY == NULL)$glassY=0;
																		
																		if($row_panes['width'] >0)$row_panes['GlassX']= ($row_panes['width'] + $glassX);
																	if($row_panes['height'] >0)$row_panes['GlassY']=($row_panes['height'] + $glassY);
																			
																		
									
									
										$result_arr[]=$row_panes;
								}
							if(!empty($result_arr))
							{
										$final_array=	sort_2d_desc($result_arr,'GlassX');
			
							}
			}
			 if(!empty($final_array))
				{
					  
					    foreach($final_array as $row_panel)
							{
								  if($row_panel['selected_product']=="EVSx3" || $row_panel['selected_product']=="EVSx2")
										{
													
													$glassX=$row_panel['evsGlassX'];
													$glassY=$row_panel['evsGlassY'];
													$outsideprofile=$row_panel['evsOutPanelType'];
													$outsidethickness=$row_panel['evsOutPanelThickness'];
													$insideprofile=$row_panel['evsInPanelType'];
													$insidethickness=$row_panel['evsInPanelThickness'];
												
													
													}
													else{
												
													$glassX=$row_panel['retroGlassX'];
													$glassY=$row_panel['retroGlassY'];
													$outsideprofile=$row_panel['retroOutPanelType'];
													$outsidethickness=$row_panel['retroOutPanelThickness'];
													$insideprofile=$row_panel['retroInPanelType'];
													$insidethickness=$row_panel['retroInPanelThickness'];
												
													
													}
														if($glassX == NULL)$glassX=0;
														if($glassY == NULL)$glassY=0;
														$arr_roomname[]=$row_panel['room_name'];
														$outsideprofile_arr[]=$outsideprofile;
															$outsidethickness_arr[]=$outsidethickness;
															$insideprofile_arr[]=$insideprofile;
															$insidethickness_arr[]=$insidethickness;
																$arr_safty[]=$row_panel['safty_name'];
															$arr_window[]=$row_panel['name']."[".$row_panel['panelnum']."]";
														if($row_panel['width'] >0){
														 $area[]=	round(((($row_panel['width'] + $glassX)*($row_panel['height'] + $glassY))*0.000001),2);
														 $key=($row_panel['width'] + $glassX).'&nbsp;&#10006;'.($row_panel['height'] + $glassY);
															$arr_width[]=$key;
															$arr_outsideprofile[]=$outsideprofile;
															$arr_outsidethickness[]=$outsidethickness;
															$arr_insideprofile[]=$insideprofile;
															$arr_insidethickness[]=$insidethickness;
															$arr_width_mes[]=$row_panel['width'] + $glassX;
															$arr_height_mes[]=$row_panel['height'] + $glassY;
														
													}
													
							}
				}
					if(!empty($arr_width[0]))
			$measuremnt_arr = array_count_values($arr_width);
		 else
			 $measuremnt_arr =array();
				
				?>
    
    
    
    <div class="property_dtls">
                    	<div class="pro_inner_one">
                        	<h4 class="main_title">Job Summary</h4>
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
                                        </ul>
                                        <ul>
                                        <li><?php echo $row_details['afirstname']." ".$row_details['alastname'];?></li>
                                        <li><?php echo $row_details['aloc'];?></li>
                                         <li><?php echo $row_details['acity'];?></li>
                                        <li><?php echo $row_details['aphone'];?></li>
                                        <li><?php echo $row_details['aemail'];?></li>
                                    </ul>
                                 
                                </div>
                            </div><!-- ./pro_inner_one_details -->
                        </div><!-- ./pro_inner_one -->
                        
                       
                        
                        <div class="pro_inner_two">
                        	<div class="pro_inner_two_details" >
                            <h4 class="main_title"><span></span> </h4>
                               <ul >
                               <?php 
																														
																															if(!empty($measuremnt_arr)){
																																	$j=0;
																															foreach($measuremnt_arr as $key => $value) {
																														
																																 echo '<li  style="color:#000">'.$arr_outsideprofile[$j].'('.$arr_outsidethickness[$j].' mm) &nbsp;&nbsp;'.$value.'@'.$key.'</li>';
																																	$j++;
																															}}
																													if(!empty($area[0])){
																															?>
																																
                                <li style="color:#060; font-weight:bold;font-size:20px;">Outside Total : <?php echo array_sum($area);?>m2</li><?php } ?>
                                </ul>
                                <ul >
                               <?php 
																															if(!empty($measuremnt_arr)){
																																$j=0;
																															foreach($measuremnt_arr as $key => $value) {
																														
																																 echo '<li  style="color:#000">'.$arr_insideprofile[$j].'('.$arr_insidethickness[$j].' mm) &nbsp;&nbsp;'.$value.'@'.$key.'</li>';
																																	$j++;
																															}}
																																					
																																		
																																			if(!empty($area[0])){
																															
																															?>
																																 <li style="color:#060; font-weight:bold;font-size:20px;">Inside Total : <?php echo array_sum($area);?>m2</li><?php } ?>
                                
                                </ul>
                            </div>
                        </div><!-- ./pro_inner_two -->
                    </div>
                     <table class="table table-bordered table-striped">
    <thead>
    <tr style="color:#fff; background:#565759;">
    <th></th>
    <th colspan="2">Cut List</th>
    <th colspan="3">Glass</th>
    <th>Outside</th>
    <th></th>
    <th></th>
    <th>Inside</th>
    <th></th>
    <th></th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <th>#</th>
    <th>Room</th>
    <th>Window[Panel]</th>
    <th>X</th>
    <th>Y</th>
    <th>m2</th>
    <th>Sort</th>
    <th>mm</th>
    <th>Safety</th>
    <th>Sort</th>
    <th>mm</th>
    <th>Safety</th>
    </tr>
    </tbody>
   
  <?php
																																									
			if(!empty($arr_roomname[0]))
			{
				 $no=0;
				  for($k=0;$k<count($arr_roomname);$k++)
							{ 
								 $no++;
										?>
          <tr>
           <th><?php echo $no;?></th>
           <td><?php echo $arr_roomname[$k];?></td>
           <td><?php echo $arr_window[$k];?></td>
         
             <td><?php echo $arr_width_mes[$k];?></td>
             <td><?php echo $arr_height_mes[$k];?></td>
             <td><?php echo $area[$k];?></td>
             <td><?php echo $outsideprofile_arr[$k];?></td>
              <td><?php echo $outsidethickness_arr[$k];?></td>
              <td><?php echo $arr_safty[$k];?></td>
               <td><?php echo $insideprofile_arr[$k];?></td>
              <td><?php echo $insidethickness_arr[$k];?></td>
              <td><?php echo $arr_safty[$k];?></td>
         
             
             </tr>
             <?php
													
							}	

			}
						echo ' </table>';

    
    
  
		
}
?>
                    
    
    
 <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="../js/bootstrap.js"></script>
     <script src="../js/validate.js"></script>
     <script src="../js/portal.js"></script>
     <script src="../js/login.js"></script>
      <script src="../js/fs-gal.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
     
     
  </body>
</html>













