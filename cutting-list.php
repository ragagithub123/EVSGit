<?php
ob_start();
session_start();
include('includes/functions.php');
if(!empty($_SESSION['agentid'])){
	
	if(isset($_REQUEST['id'])){
	
$locid = base64_decode($_REQUEST['id']);
//$get_window=$db->joinquery("SELECT room.`roomid`,room.`name` AS room_name,window.windowid,window.windowtypeid,window.`selected_product`,window.`materialCategory`,window_type.name FROM room,window,window_type WHERE window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid  AND room.locationid=".base64_decode($_REQUEST['id'])."  GROUP BY window.windowid ORDER BY room_name ASC");
 
	$get_window = $db->joinquery("SELECT room.`roomid`,room.`name` AS room_name,window.quote_status,window.notes,window.extras, window.windowid,window.windowtypeid,window.`selected_product`,window.`materialCategory`,window_type.name FROM room,window,window_type WHERE window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid  AND room.locationid=" . base64_decode($_REQUEST['id']) . " GROUP BY window.windowid ORDER BY room_name ASC");

 $i=64;
	while($row_window=mysqli_fetch_array($get_window)){
	
		$i=$i+1;
		
		if($row_window['selected_product']!='HOLD'){

		
		$get_panel =$db->joinquery("SELECT panel.panelid,panel.width,panel.height,panel.center,panel.measurement,panel.panelnum,panel.profileid,panel.windowid,panel.`safetyid`,panel.`glasstypeid`,panel.`styleid`,panel.`conditionid`,panel.`astragalsid`,`paneloption_style`.name AS stylename,paneloption_style.`evsProfileTop`,paneloption_style.`evsProfileSides`,paneloption_style.`evsProfileBottom`,paneloption_style.`evsGlassX`,paneloption_style.`evsGlassY`,paneloption_style.`evsProfileX`,paneloption_style.`evsProfileY`,paneloption_style.`retroProfileTop`,paneloption_style.`retroProfileSides`,paneloption_style.`retroProfileBottom`,paneloption_style.`retroGlassX`,paneloption_style.`retroGlassY`,paneloption_style.`retroProfileX`,paneloption_style.`retroProfileY`,paneloption_style.evsProfileRight,paneloption_style.evsProfileLeft,paneloption_style.evsOutPanelThickness,paneloption_style.evsOutPanelType,paneloption_style.evsInPanelThickness,paneloption_style.evsInPanelType,paneloption_style.retroOutPanelThickness,paneloption_style.retroOutPanelType,paneloption_style.retroInPanelThickness,paneloption_style.retroInPanelType,paneloption_style.retroProfileLeft,paneloption_style.retroProfileRight,paneloption_astragal.name AS astragal_name,paneloption_condition.name AS condition_name,paneloption_safety.name AS safty_name,paneloption_glasstype.name AS galsstype_name,paneloption_glasstype.typevalue FROM panel,paneloption_astragal,paneloption_safety,paneloption_style,paneloption_glasstype,paneloption_condition WHERE 
panel.styleid=paneloption_style.styleid AND panel.safetyid=paneloption_safety.safetyid AND panel.astragalsid=paneloption_astragal.astragalsid AND panel.glasstypeid=paneloption_glasstype.glasstypeid AND panel.conditionid=paneloption_condition.conditionid AND panel.windowid=".$row_window['windowid']."");
if(mysqli_num_rows($get_panel)>0){
	
	$j=0;
while($row_panel=mysqli_fetch_array($get_panel)){
	
	$j++;
	
		$row_window['panels'] =$row_panel;
	
	$row_window['ID']=chr($i).$j;
	  
		$postpanel[]=$row_window;
	
	 
 }
				
	}
	
			
			
	} // if
	
} // while window
	
} // isset
	 $getprop=$db->joinquery("SELECT locationSearch FROM location WHERE agentid='".$_SESSION['agentid']."'");		
		if(mysqli_num_rows($getprop)>0)
			{
					while($row_prop=mysqli_fetch_array($getprop))
					{
							$postLocation[]=$row_prop['locationSearch'];
							
					}
					
			}
include('templates/header.php');
include('views/cutting-list.htm');
include('templates/footer.php');
}else{
	 header('Location:index.php');
}
?>