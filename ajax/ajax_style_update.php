<?php ob_start();
session_start();
include('../includes/functions.php');
if(empty($_POST['frametypeid'])){
	$getframetypeid=$db->joinquery("SELECT frametypeid FROM panel WHERE panelid='".$_POST['panelid']."'");
	$rowframeid=mysqli_fetch_array($getframetypeid);
	$frametypeid=$rowframeid['frametypeid'];
}
else {
$frametypeid=$_POST['frametypeid'];
}
$db->upd_rec('panel',array('materialCategory'=>$_POST['materialCategory'],'width'=>$_POST['width'],'height'=>$_POST['height'],'center'=>$_POST['center'],'measurement'=>$_POST['measurement'],'styleid'=>$_POST['styleid'],'glasstypeid'=>$_POST['glasstype'],'safetyid'=>$_POST['safety'],'conditionid'=>$_POST['condition'],'astragalsid'=>$_POST['astragal'],'frametypeid'=>$frametypeid),"panelid = '".$_POST['panelid']."'");

	//$querySQL = $db->joinquery("SELECT p.*, safety.safetyvalue, glasstype.typevalue, style.styledgvalue, style.styleevsvalue, cond.conditionvalue, astragal.astragalvalue FROM panel AS p JOIN paneloption_safety AS safety ON p.safetyid = safety.safetyid JOIN paneloption_glasstype AS glasstype ON p.glasstypeid = glasstype.glasstypeid JOIN paneloption_style AS style ON p.styleid = style.styleid JOIN paneloption_condition AS cond ON p.conditionid = cond.conditionid JOIN paneloption_astragal AS astragal ON p.astragalsid = astragal.astragalsid WHERE p.panelid = '".$_POST['panelid']."'");
	$querySQL =$db->joinquery("SELECT p.*, safety.safetyvalue, glasstype.typevalue, style.styledgvalue, style.styleevsvalue,style.IGUassemble,style.EVSassemble, cond.conditionvalue, astragal.astragalvalue FROM panel AS p JOIN paneloption_safety AS safety ON p.safetyid = safety.safetyid JOIN paneloption_glasstype AS glasstype ON p.glasstypeid = glasstype.glasstypeid JOIN paneloption_style AS style ON p.styleid = style.styleid JOIN paneloption_condition AS cond ON p.conditionid = cond.conditionid JOIN paneloption_astragal AS astragal ON p.astragalsid = astragal.astragalsid WHERE p.panelid = '".$_POST['panelid']."'");
	$panel = mysqli_fetch_array($querySQL);
	
	#	 Defualt values
	$getparams=$db->joinquery("SELECT * FROM params");
		$calcParams = mysqli_fetch_array($getparams);

	#	-----------------------------------------------------------Size code Start--------------------------------------------------	
	# calc dimensions
	if($panel['width'] ==0){
		$m2 = 0;
		$lm=0;

	}
	else{

		if($panel['center'] > $panel['height']) {
			$m2 = (($panel['width']+24) * ($panel['center']+24)) * 0.000001;
			$lm = (($panel['width']+72) + ($panel['center']+72)) * 0.002;
		}
		else {
			$m2 = (($panel['width']+24) * ($panel['height']+24)) * 0.000001;
			$lm = (($panel['width']+72) + ($panel['height']+72)) * 0.002;
		}

		if($m2 < 0.3) # enforce minimum size
		$m2 = 0.3;

	}

#	-----------------------------------------------------------Size code End--------------------------------------------------



#	-----------------------------------------------------------new code start--------------------------------------------------
	// add in ,EVSx2rate,EVSx3rate
	# calc labour costs
	$panDGLabour = $panel['styledgvalue'] + $m2 + ($panel['conditionvalue'] * $lm) + ($panel['astragalvalue'] * 2);
	//$panDGLabour = (($panel['styledgvalue'] + $m2 + ($panel['conditionvalue'] * $lm) + ($panel['astragalvalue'] * 2)));
	$panEVSLabour = ($panel['styleevsvalue'] + $m2) + ($panel['conditionvalue'] * $lm * 0.3) + ($panel['astragalvalue'] * $panel['conditionvalue']);
	//$panEVSLabour = (($panel['styleevsvalue'] + $m2) + ($panel['conditionvalue'] * $lm * 0.3) + ($panel['astragalvalue'] * $panel['conditionvalue']));
	
	
	# check margin exist
	$chk_margins=$db->joinquery("SELECT locationid FROM location_margins WHERE locationid='".$_POST['locationid']."'");
 if(mysqli_num_rows($chk_margins) == 0){
		$insert_margin=$db->joinquery("INSERT INTO location_margins(locationid)VALUES('".$_POST['locationid']."')");
}
			
			#getmargin
			$getmargin=$db->joinquery("SELECT * FROM location_margins WHERE locationid='".$_POST['locationid']."'");
			$location_margins = mysqli_fetch_array($getmargin);


	  $getagent=$db->joinquery("SELECT SGUrate,IGUx2rate,IGUx3rate,EVSx2rate,EVSx3rate FROM agent WHERE agentid='".$_SESSION['agentid']."'");
	  $agent = mysqli_fetch_array($getagent);
			

			//Glass
			$SGU_Glass = ((($agent['SGUrate'] + $panel['safetyvalue'] + $panel['typevalue']) * $m2) * $location_margins['igumargin']); 
		 	$IGUx2_Glass = ((($agent['IGUx2rate'] + $panel['safetyvalue'] + $panel['typevalue']) * $m2) * $location_margins['igumargin']); 
		  	$IGUx3_Glass = ((($agent['IGUx3rate'] + $panel['safetyvalue'] + $panel['typevalue']) * $m2) * $location_margins['igumargin']);
          // change to ,EVSx2rate,EVSx3rate
			$EVSx2_Glass = ((($agent['EVSx2rate']+($panel['safetyvalue'] * 0.5))* $m2)* $location_margins['evsmargin']);
			$EVSx3_Glass = ((($agent['EVSx3rate']+($panel['safetyvalue'] * 0.5))* $m2)* $location_margins['evsmargin']);
			
//Materials
	      // take out $panel['IGUassemble'] and $panel['EVSassemble']
	
			$SGU_Materials = (($calcParams['dgmaterials'] * $lm) * $location_margins['igumargin']);
			$IGUx2_Materials =(($calcParams['dgmaterials'] * $lm) * $location_margins['igumargin']);
			$IGUx3_Materials = (($calcParams['dgmaterials'] * $lm) * $location_margins['igumargin']);
			$EVSx2_Materials =(($calcParams['evsmaterials'] * $lm) * $location_margins['evsmargin']);
			$EVSx3_Materials = (($calcParams['evsmaterials'] * $lm)* $location_margins['evsmargin']);
			//$SGU_Materials = ((($calcParams['dgmaterials'] * $lm) + ($panel['IGUassemble']* $lm)) *$location_margins['productmargin']); 
			//$IGUx2_Materials =((($calcParams['dgmaterials'] * $lm) + ($panel['IGUassemble']* $lm)) *$location_margins['productmargin']); 
			//$IGUx3_Materials = ((($calcParams['dgmaterials'] * $lm) + ($panel['IGUassemble']* $lm)) *$location_margins['productmargin']); 
			//$EVSx2_Materials =((($calcParams['evsmaterials'] * $lm)+($panel['EVSassemble']* $lm))*$location_margins['evsmargin']); 
			//$EVSx3_Materials = ((($calcParams['evsmaterials'] * $lm)+($panel['EVSassemble']* $lm))*$location_margins['evsmargin']); 
//Labour
			// insert +$panel['IGUassemble'] and +$panel['EVSassemble'] and +$m2
	
			$SGU_Labour = (($panDGLabour + $m2 + $panel['IGUassemble'])* $location_margins['labourrate']);
			$IGUx2_Labour = (($panDGLabour + $m2 + $panel['IGUassemble'])* $location_margins['labourrate']);
			$IGUx3_Labour = (($panDGLabour + $m2 + $panel['IGUassemble'])* $location_margins['labourrate']);
			$EVSx2_Labour =(($panEVSLabour + $m2 + $panel['EVSassemble']) * $location_margins['labourrate']);
			$EVSx3_Labour = (($panEVSLabour + $m2 + $panel['EVSassemble'])* $location_margins['labourrate']);
	
			//$SGU_Labour = ($panDGLabour*$location_margins['labourrate']);
			//$IGUx2_Labour =($panDGLabour*$location_margins['labourrate']); 
			//$IGUx3_Labour =($panDGLabour*$location_margins['labourrate']); 
			//$EVSx2_Labour =($panEVSLabour*$location_margins['labourrate']); 
			//$EVSx3_Labour = ($panEVSLabour*$location_margins['labourrate']); 
//Totals
			$SDG_Total = round(($SGU_Glass + $SGU_Materials + $SGU_Labour),2); 
			$IGUx2_Total = round(($IGUx2_Glass + $IGUx2_Materials + $IGUx2_Labour),2); 
			$IGUx3_Total = round(($IGUx3_Glass + $IGUx3_Materials + $IGUx3_Labour),2); 
			$EVSx2_Total = round(($EVSx2_Glass + $EVSx2_Materials + $EVSx2_Labour),2); 
			$EVSx3_Total = round(($EVSx3_Glass + $EVSx3_Materials + $EVSx3_Labour),2); 

			// Change variable to $panIGUx2, $panIGUx3
	
			if($m2>0){

				$panSDG = $SDG_Total;
				$panIGUx2 = $IGUx2_Total;
				$panIGUx3 = $IGUx3_Total;
				$panEVSx2 = $EVSx2_Total;
				$panEVSx3= $EVSx3_Total;

			}else{

				$panSDG = 0;
				$panIGUx2 = 0;
				$panIGUx3 = 0;
				$panEVSx2 = 0;
				$panEVSx3= 0;

				$SGU_Labour =0;
				$IGUx2_Labour=0;
				$IGUx3_Labour=0;
				$EVSx2_Labour=0;
				$EVSx3_Labour=0;

			}
	
			
			//$panSDG = $SDG_Total;
			//$panMAXe = $IGUx2_Total;
			//$panXCLe = $IGUx3_Total;
			//$panEVSx2 = $EVSx2_Total;
			//$panEVSx3= $EVSx3_Total;
	
	// Change variable to $panIGUx2, $panIGUx3
	// insert new costIGUX2 = $panIGUx2, costIGUX3 = $panIGUx3,
			
 $db->upd_rec('panel',array('costsdg'=>$panSDG,'costmaxe'=>$panIGUx2,'costIGUX2'=> $panIGUx2,'costxcle'=>$panIGUx3, 'costIGUX3'=> $panIGUx3,'costevsx2'=>$panEVSx2,'costevsx3'=>$panEVSx3,'dglabour'=>$panDGLabour,'evslabour'=>$panEVSLabour),"panelid = '".$_POST['panelid']."'");
#	-----------------------------------------------------------new code END--------------------------------------------------
	# window cost
//$getwindowid=$db->joinquery("SELECT panel.windowid,room.locationid FROM panel,window,room WHERE panel.windowid=window.windowid AND window.roomid=room.roomid AND panel.panelid='".$_POST['panelid']."'");
$getwindowid=$db->joinquery("SELECT windowid FROM panel WHERE panelid='".$_POST['panelid']."'");
$rowwindowid=mysqli_fetch_array($getwindowid);

# get total panel labour and style costs for this window
	$querySQL = $db->joinquery("SELECT SUM(dglabour) AS dglabour, SUM(evslabour) AS evslabour, SUM(costsdg) AS sumcostsdg, SUM(costmaxe) AS sumcostmaxe, SUM(costxcle) AS sumcostxcle, SUM(costevsx2) AS sumcostevsx2, SUM(costevsx3) AS sumcostevsx3 FROM panel WHERE windowid = '".$rowwindowid['windowid']."'");

	
	$row_labour = mysqli_fetch_array($querySQL);
	
	# calc window travel total
	
	
	$getlocation = $db->joinquery("SELECT agentid,travel_status,distance,number_staff FROM location WHERE locationid='".$_POST['locationid']."'");

	
	$rowtravel = mysqli_fetch_array($getlocation);
	
		if($rowtravel['travel_status']==0 || $m2==0){
		
					
					$travelSDG=0;
					$travelMAXe=0;
					$travelXCLe=0;
					$travelEVSx2=0;
					$travelEVSx3=0;
					
				}else{
					
					    
					$queryMargins =$db->joinquery("SELECT labourrate,travelrate FROM location_margins WHERE locationid='".$_POST['locationid']."'");
						$margins = mysqli_fetch_array($queryMargins);
					if(mysqli_num_rows($queryMargins)==0){
							$queryMargins=$db->joinquery("SELECT labourrate,evsmargin,igumargin,productmargin,agenttravelrate as travelrate FROM agent WHERE agentid='".$rowtravel['agentid']."'");
							$margins = mysqli_fetch_array($queryMargins);
						 $db->joinquery("INSERT INTO location_margins(`locationid`,`evsmargin`,`igumargin`,`productmargin`,`labourrate`,`travelrate`)VALUES('$locationid','".$margins['evsmargin']."','".$margins['igumargin']."','".$margins['productmargin']."','".$margins['labourrate']."','".$margins['travelrate']."')");
					}
					
					$labourrate=$margins['labourrate'];
					
					$travelrate=$margins['travelrate'];
					
				
					
		
				$travelDaysEVS=($row_labour['evslabour']/(7*$rowtravel['number_staff']));
		
		
		$travelHoursEVS = ((($rowtravel['distance']*2)*$rowtravel['number_staff'])/90)*$travelDaysEVS;
		
		$travelEVSx2 = $travelEVSx3 =round((((($rowtravel['distance']*2)*$travelDaysEVS)*$travelrate)+($travelHoursEVS*$labourrate)),2);
		
		
		$travelDaysIGU=($row_labour['dglabour']/(5*$rowtravel['number_staff']));
		
		$travelHoursIGU = ((($rowtravel['distance']*2)*$rowtravel['number_staff'])/90)*$travelDaysIGU;
		
		$travelSDG=$travelMAXe=$travelXCLe =round((((($rowtravel['distance']*2)*$travelDaysIGU)*$travelrate)+($travelHoursIGU*$labourrate)),2);
		
			}
	
	
	$queryextraSQL=$db->joinquery("SELECT sum(cost) AS total_extras FROM `window_extras` WHERE windowid='".$rowwindowid['windowid']."'");

	$extras = mysqli_fetch_array($queryextraSQL);
	
	# finally calc window style costs
	$costSDG = $row_labour['sumcostsdg'] + $extras['total_extras'] + $travelSDG;
	$costMAXe = $row_labour['sumcostmaxe'] + $extras['total_extras'] + $travelMAXe;
	$costXCLe = $row_labour['sumcostxcle'] + $extras['total_extras'] + $travelXCLe;
	$costEVSx2 = $row_labour['sumcostevsx2'] + $extras['total_extras'] + $travelEVSx2;
	$costEVSx3 = $row_labour['sumcostevsx3'] + $extras['total_extras'] + $travelEVSx3;
	$db->joinquery("UPDATE window SET costsdg = $costSDG, costmaxe = $costMAXe, costxcle = $costXCLe, costevsx2 = $costEVSx2, costevsx3 = $costEVSx3 WHERE windowid = '".$rowwindowid['windowid']."'");
$safty_query=$db->joinquery("SELECT name AS saftyname from paneloption_safety WHERE safetyid='".$_POST['safety']."'");
$rowSafty=mysqli_fetch_array($safty_query);
$safty_condition=$db->joinquery("SELECT name AS conditionname from paneloption_condition WHERE conditionid='".$_POST['condition']."'");
$rowCondition=mysqli_fetch_array($safty_condition);
$safty_glasstype=$db->joinquery("SELECT name AS glassname from paneloption_glasstype WHERE glasstypeid='".$_POST['glasstype']."'");
$rowGlass=mysqli_fetch_array($safty_glasstype);
$safty_astragal=$db->joinquery("SELECT name AS astragalname from paneloption_astragal WHERE astragalsid='".$_POST['astragal']."'");
$rowAstragal=mysqli_fetch_array($safty_astragal);


	echo "<img src=\"". $gPanelOptionsPhotoURL.$_POST['styleid'].".png?". time(). "\" class=\"img-responsive\" style=\"width: 50px; height: 50px;\">"."@".$_POST['width']."x".$_POST['height']."x".$_POST['center']."@".$rowSafty['saftyname']."@".$rowGlass['glassname']."@".$rowCondition['conditionname']."@".$rowAstragal['astragalname'];
?>