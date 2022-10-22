<?php
include('../includes/functions.php');
$status="status".$_POST['status_id'];
$curnt_status=$db->joinquery("SELECT jobstatusid FROM location WHERE locationid='".$_POST['locationid']."'");
$row_satus=mysqli_fetch_array($curnt_status);
$getoldcards=$db->joinquery("SELECT COUNT(locationid)AS total_old_cards FROM location WHERE (location.locationstatusid!=3 AND location.locationstatusid!='5') AND location.`agentid`=".$_SESSION['agentid']." AND jobstatusid='".$row_satus['jobstatusid']."'");
$row_old_card=mysqli_fetch_array($getoldcards);
$remaining_count=$row_old_card['total_old_cards']-1;

$db->upd_rec('location',array('jobstatusid' =>$_POST['status_id'],$status=>date('Y-m-d H:i:s')),"locationid = '".$_POST['locationid']."'");
$getcards=$db->joinquery("SELECT COUNT(locationid)AS total_cards FROM location WHERE (location.locationstatusid!=3 AND location.locationstatusid!='5') AND location.`agentid`=".$_SESSION['agentid']." AND jobstatusid='".$_POST['status_id']."'");
$row_card=mysqli_fetch_array($getcards);


	$get_total_panel=$db->joinquery("SELECT sum(window_type.numpanels) AS totalcardpanel FROM location,room,window,window_type WHERE location.agentid='".$_SESSION['agentid']."' AND room.locationid=location.locationid AND (location.locationstatusid!=3 AND location.locationstatusid!='5') AND location.jobstatusid='".$_POST['status_id']."' AND window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid");
										$row_total_panel=mysqli_fetch_array($get_total_panel);
									
										$get_total_selected_cnt=$db->joinquery("SELECT sum(window_type.numpanels) AS totalcardselected FROM location,room,window,window_type WHERE location.agentid='".$_SESSION['agentid']."' AND room.locationid=location.locationid AND (location.locationstatusid!=3 AND location.locationstatusid!='5') AND location.jobstatusid='".$_POST['status_id']."' AND window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid AND window.selected_product!='HOLD'");
								   $row_total_selected_panel=mysqli_fetch_array($get_total_selected_cnt);
										if(empty($row_total_panel['totalcardpanel'])){
											$totals_panels=0;
										}else{
											 	$totals_panels=$row_total_panel['totalcardpanel'];
										}
										if(empty($row_total_selected_panel['totalcardselected'])){
											$selected_panels=0;
										}else{
											 $selected_panels=$row_total_selected_panel['totalcardselected'];
										}
										
										
										$get_rem_total_panel=$db->joinquery("SELECT sum(window_type.numpanels) AS totalcardpanel FROM location,room,window,window_type WHERE location.agentid='".$_SESSION['agentid']."' AND room.locationid=location.locationid AND (location.locationstatusid!=3 AND location.locationstatusid!='5') AND location.jobstatusid='".$row_satus['jobstatusid']."' AND window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid");
										$row_rem_total_panel=mysqli_fetch_array($get_rem_total_panel);
									
										$get_rem_total_selected_cnt=$db->joinquery("SELECT sum(window_type.numpanels) AS totalcardselected FROM location,room,window,window_type WHERE location.agentid='".$_SESSION['agentid']."' AND room.locationid=location.locationid AND (location.locationstatusid!=3 AND location.locationstatusid!='5') AND location.jobstatusid='".$row_satus['jobstatusid']."' AND window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid AND window.selected_product!='HOLD'");
								   $row_rem_total_selected_panel=mysqli_fetch_array($get_rem_total_selected_cnt);
										if(empty($row_rem_total_panel['totalcardpanel'])){
											$remaing_panels=0;
										}else{
											 	$remaing_panels=$row_rem_total_panel['totalcardpanel'];
										}
										if(empty($row_rem_total_selected_panel['totalcardselected'])){
											$remaing_selected=0;
										}else{
											 $remaing_selected=$row_rem_total_selected_panel['totalcardselected'];
										}



//$row_status['total_cards']=mysqli_num_rows($getcards);
echo $remaining_count."@".$row_satus['jobstatusid']."@".$row_card['total_cards']."@".$totals_panels."@".$selected_panels."@".$remaing_panels."@".$remaing_selected;
?>