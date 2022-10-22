<?php ob_start();
session_start();
include('../includes/functions.php');
$getprop=$db->joinquery("SELECT styledgvalue,styleevsvalue,IGUassemble,EVSassemble FROM paneloption_style WHERE styleid='".$_POST['styelid']."'");
$row_prop=mysqli_fetch_array($getprop);
	
#	-----------------------------------------------------------Size code Start--------------------------------------------------	
# calc dimensions
	if($_POST['center'] > $_POST['height']) {
		$m2 = (($_POST['width']+24) * ($_POST['center']+24)) * 0.000001;
		$lm = (($_POST['width']+72) + ($_POST['center']+72)) * 0.002;
	}
	else {
		$m2 = (($_POST['width']+24) * ($_POST['height']+24)) * 0.000001;
		$lm = (($_POST['width']+72) + ($_POST['height']+72)) * 0.002;
	}
	
#	-----------------------------------------------------------Size code End--------------------------------------------------	

	if($m2 < 0.3) # enforce minimum size
		$m2 = 0.3;

	# calc labour costs
	$get_conditionvalue=$db->joinquery("SELECT conditionvalue FROM paneloption_condition WHERE conditionid='".$_POST['conditionid']."'");
	$rowCondition=mysqli_fetch_array($get_conditionvalue);
	$get_atragalvalue=$db->joinquery("SELECT astragalvalue FROM paneloption_astragal WHERE astragalsid='".$_POST['astragal']."'");
	$rowAstragal=mysqli_fetch_array($get_atragalvalue);
	$panDGLabour = $row_prop['styledgvalue'] + $m2 + ($rowCondition['conditionvalue'] * $lm) + ($rowAstragal['astragalvalue'] * 2);
	$panEVSLabour = ($row_prop['styleevsvalue'] + $m2) + ($rowCondition['conditionvalue'] * $lm * 0.3) + ($rowAstragal['astragalvalue'] * $rowCondition['conditionvalue']);
	
	if(($_POST['option'] == 'width')||($_POST['option'] == 'height') ||($_POST['option'] == 'center')){
		
		 $getsafty_valorg=$db->joinquery("SELECT paneloption_safety.`safetyvalue` FROM `paneloption_safety`,`panel` WHERE panel.safetyid=paneloption_safety.safetyid AND panel.panelid='".$_POST['panelid']."'");
			$row_saftyorg=mysqli_fetch_array($getsafty_valorg);
			$saftyvalue=$row_saftyorg['safetyvalue'];
			$getglass_valorg=$db->joinquery("SELECT paneloption_glasstype.`typevalue` FROM `paneloption_glasstype`,panel WHERE panel.glasstypeid=paneloption_glasstype.`glasstypeid`AND panel.panelid='".$_POST['panelid']."'");
			$row_glassorg=mysqli_fetch_array($getglass_valorg);
			$typevalue=$row_glassorg['typevalue'];
			
		
		echo round($panDGLabour,2)."@".round($panEVSLabour,2)."@";
	}
	
	
if($_POST['option']=='safty'){
$getsafty=$db->joinquery("SELECT name,safetyvalue FROM paneloption_safety WHERE safetyid='".$_POST['saftyid']."'");
$rowsafty=mysqli_fetch_array($getsafty);
if($gPanelOptionsPhotoDir."saftyicons/".$_POST['saftyid'].".png"){
	$image=$gPanelOptionsPhotoURL."saftyicons/".$_POST['saftyid'].".png";
}
else{
	 $image="";
}
   $saftyvalue=$rowsafty['safetyvalue'];
			$getglass_valorg=$db->joinquery("SELECT paneloption_glasstype.`typevalue` FROM `paneloption_glasstype`,panel WHERE panel.glasstypeid=paneloption_glasstype.`glasstypeid`AND panel.panelid='".$_POST['panelid']."'");
			$row_glassorg=mysqli_fetch_array($getglass_valorg);
			$typevalue=$row_glassorg['typevalue'];
			
echo $rowsafty['name']."@".$rowsafty['safetyvalue']."@".$image."@".round($panDGLabour,2)."@".round($panEVSLabour,2)."@";
}

if($_POST['option']=='glasstype'){
						$getglass=$db->joinquery("SELECT name,typevalue FROM paneloption_glasstype WHERE glasstypeid='".$_POST['glasstypeid']."'");
						$rowglass=mysqli_fetch_array($getglass);
						if($gPanelOptionsPhotoDir."glassicons/".$_POST['glasstypeid'].".png"){
						$image=$gPanelOptionsPhotoURL."glassicons/".$_POST['glasstypeid'].".png";
					}
					else{
							$image="";
					}
					$typevalue=$rowglass['typevalue'];
					$getsafty_valorg=$db->joinquery("SELECT paneloption_safety.`safetyvalue` FROM `paneloption_safety`,`panel` WHERE panel.safetyid=paneloption_safety.safetyid AND panel.panelid='".$_POST['panelid']."'");
					$row_saftyorg=mysqli_fetch_array($getsafty_valorg);
					$saftyvalue=$row_saftyorg['safetyvalue'];
					echo $rowglass['name']."@".$rowglass['typevalue']."@".$image."@".round($panDGLabour,2)."@".round($panEVSLabour,2)."@";
					
					

}

if($_POST['option'] == 'condition'){
	
	$getcondition=$db->joinquery("SELECT name,conditionvalue FROM paneloption_condition WHERE conditionid='".$_POST['conditionid']."'");
	$row_condition=mysqli_fetch_array($getcondition);
	$getsafty_valorg=$db->joinquery("SELECT paneloption_safety.`safetyvalue` FROM `paneloption_safety`,`panel` WHERE panel.safetyid=paneloption_safety.safetyid AND panel.panelid='".$_POST['panelid']."'");
			$row_saftyorg=mysqli_fetch_array($getsafty_valorg);
			$saftyvalue=$row_saftyorg['safetyvalue'];
			$getglass_valorg=$db->joinquery("SELECT paneloption_glasstype.`typevalue` FROM `paneloption_glasstype`,panel WHERE panel.glasstypeid=paneloption_glasstype.`glasstypeid`AND panel.panelid='".$_POST['panelid']."'");
			$row_glassorg=mysqli_fetch_array($getglass_valorg);
			$typevalue=$row_glassorg['typevalue'];
	echo $row_condition['name']."@".$row_condition['conditionvalue']."@".round($panDGLabour,2)."@".round($panEVSLabour,2)."@";

	
}

if($_POST['option'] == 'astragal'){
	
	$getastragal=$db->joinquery("SELECT name,astragalvalue FROM paneloption_astragal WHERE astragalsid='".$_POST['astragal']."'");
	$row_astragal=mysqli_fetch_array($getastragal);
	$getsafty_valorg=$db->joinquery("SELECT paneloption_safety.`safetyvalue` FROM `paneloption_safety`,`panel` WHERE panel.safetyid=paneloption_safety.safetyid AND panel.panelid='".$_POST['panelid']."'");
			$row_saftyorg=mysqli_fetch_array($getsafty_valorg);
			$saftyvalue=$row_saftyorg['safetyvalue'];
			$getglass_valorg=$db->joinquery("SELECT paneloption_glasstype.`typevalue` FROM `paneloption_glasstype`,panel WHERE panel.glasstypeid=paneloption_glasstype.`glasstypeid`AND panel.panelid='".$_POST['panelid']."'");
			$row_glassorg=mysqli_fetch_array($getglass_valorg);
			$typevalue=$row_glassorg['typevalue'];
	echo $row_astragal['name']."@".$row_astragal['astragalvalue']."@".round($panDGLabour,2)."@".round($panEVSLabour,2)."@";

	
}



#	-----------------------------------------------------------new code start--------------------------------------------------

# get agent rates
		// add in ,EVSx2rate,EVSx3rate
		$getagentrate=$db->joinquery("SELECT SGUrate,IGUx2rate,IGUx3rate,EVSx2rate,EVSx3rate FROM agent WHERE agentid='".$_SESSION['agentid']."'");
$rowagent=mysqli_fetch_array($getagentrate);
# check location margins exist
$chk_margins=$db->joinquery("SELECT locationid FROM location_margins WHERE locationid='".$_POST['locationid']."'");
if(mysqli_num_rows($chk_margins) == 0){
		$db->joinquery("INSERT INTO location_margins(locationid)VALUES('".$_POST['locationid']."')");

}
# get margin
$getmargins=$db->joinquery("SELECT evsmargin,igumargin,productmargin,labourrate FROM location_margins WHERE locationid='".$_POST['locationid']."'");
$location_margins=mysqli_fetch_array($getmargins);
# get calcparams
$getcalcparams=$db->joinquery("SELECT * FROM params");
$calcParams=mysqli_fetch_array($getcalcparams);
//echo "raga".$rowagent['SGUrate']."@".$saftyvalue."@".$typevalue."@".$m2."@".$location_margins['igumargin'];
		
	//Glass
	
		$SGU_Glass = ((($rowagent['SGUrate'] + $saftyvalue + $typevalue) * $m2) * $location_margins['igumargin']);
		$IGUx2_Glass = ((($rowagent['IGUx2rate'] + $saftyvalue + $typevalue) * $m2) * $location_margins['igumargin']);
		$IGUx3_Glass = ((($rowagent['IGUx3rate'] + $saftyvalue + $typevalue) * $m2) * $location_margins['igumargin']);
		// change to ,EVSx2rate,EVSx3rate
		$EVSx2_Glass = ((($rowagent['EVSx2rate']+($saftyvalue * 0.5))* $m2)* $location_margins['evsmargin']);
		$EVSx3_Glass = ((($rowagent['EVSx3rate']+($saftyvalue * 0.5))* $m2)* $location_margins['evsmargin']);

	//Materials
	// take out $panel['IGUassemble'] and $panel['EVSassemble']chnage to igumargin
		$SGU_Materials = (($calcParams['dgmaterials'] * $lm) * $location_margins['igumargin']);
		$IGUx2_Materials =(($calcParams['dgmaterials'] * $lm) * $location_margins['igumargin']);
		$IGUx3_Materials =(($calcParams['dgmaterials'] * $lm) * $location_margins['igumargin']);
		$EVSx2_Materials =(($calcParams['evsmaterials'] * $lm) * $location_margins['evsmargin']);
		$EVSx3_Materials =(($calcParams['evsmaterials'] * $lm) * $location_margins['evsmargin']);
	
	//Labour
	// insert +$panel['IGUassemble'] and +$panel['EVSassemble'] and +$m2
		$SGU_Labour = (($panDGLabour + $m2 + $row_prop['IGUassemble']) * $location_margins['labourrate']);
		$IGUx2_Labour =(($panDGLabour + $m2 + $row_prop['IGUassemble']) * $location_margins['labourrate']);
		$IGUx3_Labour =(($panDGLabour + $m2 + $row_prop['IGUassemble']) * $location_margins['labourrate']);
	  //$IGUx3_Labour =(($panDGLabour + $m2 + $row_prop['IGUassemble']) * $location_margins['labourrate']);
	  //$EVSx2_Labour =(($panEVSLabour + $m2 + $row_prop['EVSassemble']) * $location_margins['labourrate']);
		$EVSx2_Labour =(($panEVSLabour + $m2 + $row_prop['EVSassemble']) * $location_margins['labourrate']);
		$EVSx3_Labour =(($panEVSLabour + $m2 + $row_prop['EVSassemble']) * $location_margins['labourrate']);

	//Totals
		$SGUTotal =   $SGU_Glass   + $SGU_Materials   + $SGU_Labour;
		$IGUx2Total = $IGUx2_Glass + $IGUx2_Materials + $IGUx2_Labour;
		$IGUx3Total = $IGUx3_Glass + $IGUx3_Materials + $IGUx3_Labour;
		$EVSx2Total = $EVSx2_Glass + $EVSx2_Materials + $EVSx2_Labour;
		$EVSx3Total = $EVSx3_Glass + $EVSx3_Materials + $EVSx3_Labour;
		
		#	-----------------------------------------------------------new code END--------------------------------------------------
?>
<table class="table">
 <tr>
                                        <td></td>
                                        <td>Glass</td>
                                        <td>Materials</td>
                                        <td>Labour</td>
                                        
                                        <td>Total</td>
                                       
                                        </tr>
                                         <tr>
                                        <td>SGU</td>
                                        <td>$<?php echo round($SGU_Glass, 2);?></td>
                                        <td>$<?php echo round($SGU_Materials,2);?></td>
                                        <td>$<?php echo round($SGU_Labour,2);?></td>
                                        <td>$<?php echo round($SGUTotal,2);?></td>
                                       
                                        </tr>
                                        <tr>
                                        <td>IGUx2</td>
                                         <td>$<?php echo round($IGUx2_Glass,2);?></td>
                                        <td>$<?php echo round($IGUx2_Materials,2);?></td>
                                        <td>$<?php echo round($IGUx2_Labour,2);?></td>
                                        <td>$<?php echo round($IGUx2Total,2);?></td>
                                        
                                        </tr>
                                         <tr>
                                        <td>IGUx3</td>
                                         <td>$<?php echo round($IGUx3_Glass,2);?></td>
                                        <td>$<?php echo round($IGUx3_Materials,2);?></td>
                                        <td>$<?php echo round($IGUx3_Labour,2);?></td>
                                        <td>$<?php echo round($IGUx3Total,2);?></td>
                                        
                                        </tr>
                                         <tr>
                                        <td>EVSx2</td>
                                         <td>$<?php echo round($EVSx2_Glass,2);?></td>
                                        <td>$<?php echo round($EVSx2_Materials,2);?></td>
                                        <td>$<?php echo round($EVSx2_Labour,2);?></td>
                                        <td>$<?php echo round($EVSx2Total,2);?></td>
                                        
                                        </tr>
                                        <tr>
                                        <td>EVSx3</td>
                                         <td>$<?php echo round($EVSx3_Glass,2);?></td>
                                        <td>$<?php echo round($EVSx3_Materials,2);?></td>
                                        <td>$<?php echo round($EVSx3_Labour,2);?></td>
                                        <td>$<?php echo round($EVSx3Total,2);?></td>
                                        <td></td>
                                        </tr>

</table>