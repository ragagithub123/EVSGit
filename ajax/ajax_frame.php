<?php ob_start();
session_start();
include('../includes/functions.php');
if($_POST['materialtype'] == 'RTIM' || $_POST['materialtype'] == 'NALU' || $_POST['materialtype'] == 'NTIM' || $_POST['materialtype'] == 'RALU' || $_POST['materialtype'] == 'RMET')
{
	 $getfrmaes=$db->joinquery("SELECT frametypeid,name,imageid FROM paneloption_frametype WHERE ".$_POST['materialtype']."=1");
}
else{
	
	 $getfrmaes=$db->joinquery("SELECT frametypeid,name,imageid FROM paneloption_frametype WHERE category='".$_POST['materialtype']."'");
}
/*echo ' <select name="styleframe" id="styleframe" title="Select your frame" class="selectpicker form-control">';

while($row=mysqli_fetch_array($getfrmaes)){
	echo '<option value="'.$row['frametypeid'].'" data-thumbnail="'.$gPanelOptionsPhotoURL.$row['imageid'].'.png" >'.$row['name'].'</option>';
}
                          
                          
echo '</select>';*/

while($row=mysqli_fetch_assoc($getfrmaes)){
	$row['image']=$gPanelOptionsPhotoURL.$row['imageid'].".png";
	$list[]=$row;
}
echo json_encode($list);