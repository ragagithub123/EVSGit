<?php ob_start();
session_start();
include('../includes/functions.php');
$get_selected_cnt=$db->joinquery("SELECT sum(window_type.numpanels) AS pdt_count FROM room,window,window_type WHERE window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid AND window.selected_product!='HOLD' AND room.locationid='".$_POST['locationid']."'");
			$row_selected=mysqli_fetch_array($get_selected_cnt);
			echo '&nbsp;Product Selected <img src="images/check_small.png" style="width:25px; height:25px;"> &nbsp; '.$row_selected['pdt_count'].' panels selected';
