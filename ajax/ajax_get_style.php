<?php ob_start();
session_start();
include('../includes/functions.php');
if($_POST['status']==1){
	$getprop=$db->joinquery("SELECT styleid,name,styledgvalue,styleevsvalue FROM paneloption_style WHERE styleid='".$_POST['styleid']."'");
 $row_prop=mysqli_fetch_array($getprop);

	# calc dimensions
	if($_POST['center'] > $_POST['height']) {
		$m2 = ($_POST['width'] * $_POST['center']) * 0.000001;
		$lm = ($_POST['width'] + $_POST['center']) * 0.002;
	}
	else {
		$m2 = ($_POST['width'] * $_POST['height']) * 0.000001;
		$lm = ($_POST['width'] + $_POST['height']) * 0.002;
	}

	if($m2 < 0.3) # enforce minimum size
		$m2 = 0.3;

	# calc labour costs
	$get_conditionvalue=$db->joinquery("SELECT conditionvalue FROM paneloption_condition WHERE conditionid='".$_POST['conditionid']."'");
	$rowCondition=mysqli_fetch_array($get_conditionvalue);
	$get_atragalvalue=$db->joinquery("SELECT astragalvalue FROM paneloption_astragal WHERE astragalsid='".$_POST['astragal']."'");
	$rowAstragal=mysqli_fetch_array($get_atragalvalue);
	$panDGLabour = $row_prop['styledgvalue'] + $m2 + ($rowCondition['conditionvalue'] * $lm) + ($rowAstragal['astragalvalue'] * 2);
	$panEVSLabour = ($row_prop['styleevsvalue'] + $m2) + ($rowCondition['conditionvalue'] * $lm * 0.3) + ($rowAstragal['astragalvalue'] * $rowCondition['conditionvalue']);

				$getCatgory=$db->joinquery("SELECT TRIM(window.materialCategory) AS matcategory FROM window,panel WHERE panel.windowid=window.windowid AND panel.panelid='".$_POST['panelid']."'");
			$rowCategory=mysqli_fetch_array($getCatgory);
			$cat=rtrim($rowCategory['matcategory']);
			$getCat=$db->joinquery("SELECT category,famecategoryid FROM famecategory WHERE material_tag='$cat'");
			echo '<select name="styleop" id="styleop" class="form-control">
			<option value="'.$row_prop['styleid'].'" selected="selected">'.$row_prop['name'].'</option>
			<option value="'.$cat.'">All</option>';
			while($rowCat=mysqli_fetch_array($getCat))
			{
					echo '<option value='.$rowCat['famecategoryid'].'>'.$rowCat['category'].'</option>';
			}
			echo '</select>'."@".$gPanelOptionsPhotoURL.$row_prop['styleid'].".png"."@".round($panDGLabour,2)."@".round($panEVSLabour,2);


}
if($_POST['status']==0){
$getstyle=$db->joinquery("SELECT styleid,name FROM paneloption_style WHERE frametypeid='".$_POST['frametypeid']."'"); 
while($row=mysqli_fetch_assoc($getstyle)){
	$row['image']=$gPanelOptionsPhotoURL.$row['styleid'].".png";
	$list[]=$row;
}
echo json_encode($list);
}