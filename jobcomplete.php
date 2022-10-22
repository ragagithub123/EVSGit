<?php
$totalglass =array();
	$totalkit =array();
	$totalassemble =array();
	$totalprep=array();
	$totalinstall =array();
	$totalseals =array();
	$totalproprice =array();
	$getPanelProd =$db->joinquery("SELECT sum(window_type.numpanels) AS panelcount FROM room,window,window_type WHERE window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid AND window.selected_product!='HOLD' AND room.locationid=".$passloc."");
	if(mysqli_num_rows($getPanelProd)>0){
 $rowPanelProd = mysqli_fetch_array($getPanelProd);
	$totaljobPanels = $rowPanelProd['panelcount'];
	}else{
		$totaljobPanels =0;
		}
		if(!empty($passPjtid))
		$getweektimesheet = $db->joinquery("SELECT * FROM  weekely_worksheet WHERE locationid=".$passloc." AND projectid=".$passPjtid." AND dates!='0000-00-00' ORDER BY week ASC");
	 else
	$getweektimesheet = $db->joinquery("SELECT * FROM  weekely_worksheet WHERE locationid=".$passloc." AND dates!='0000-00-00' ORDER BY week ASC");
	$numrows = mysqli_num_rows($getweektimesheet);
	if($numrows >0){
		while($rowweek = mysqli_fetch_assoc($getweektimesheet)){
			$rowweek['dates'] = date('d-m-Y',strtotime($rowweek['dates']));
			 $totalglass[] = $rowweek['glass'];
				$totalkit[] = $rowweek['kit'];
				$totalassemble[] = $rowweek['assemble'];
				$totalprep[] = $rowweek['prep'];
				$totalinstall[] = $rowweek['install'];
				$totalseals[] = $rowweek['seals'];
			$weekely[] = $rowweek;
		}
	}
	$get_window = $db->joinquery("SELECT window.windowid,window.selected_product,window.costsdg,window.costmaxe,window.costxcle,window.costevsx2,window.costevsx3 FROM room,window WHERE window.roomid=room.roomid AND window.selected_product!='HOLD' AND room.locationid=".$passloc." GROUP BY window.windowid ORDER BY room.name ASC");
		while($rowpro_window = mysqli_fetch_array($get_window)){
			if($rowpro_window['selected_product']=="SGU" || $rowpro_window['selected_product']=="SDG"){
				$totalproprice[]= $rowpro_window['costsdg'];
			}
			else if($rowpro_window['selected_product']=="IGUX2" || $rowpro_window['selected_product']=="MAXe"){
				$totalproprice[]= $rowpro_window['costmaxe'];
			}
			else if($rowpro_window['selected_product']=="IGUX3" || $rowpro_window['selected_product']=="XCLe"){
				$totalproprice[]= $rowpro_window['costxcle'];
			}
			else if($rowpro_window['selected_product']=="EVSx2"){
				$totalproprice[]= $rowpro_window['costevsx2'];
			}
			else if($rowpro_window['selected_product']=="EVSx3"){
				$totalproprice[]= $rowpro_window['costevsx3'];
			}
		}
		if(count($totalproprice)>0)
		$jobSum = array_sum($totalproprice);
	 else
	 $jobSum =0;
		if(count($totalglass)>0) $tot_glass = array_sum($totalglass);
	else $tot_glass=0;
	if(count($totalassemble)>0) $tot_assemble = array_sum($totalassemble);
	else $tot_assemble=0;
	if(count($totalkit)>0) $tot_kit = array_sum($totalkit);
	else $tot_kit=0;
	if(count($totalprep)>0) $tot_prep = array_sum($totalprep);
	else $tot_prep=0;
	if(count($totalinstall)>0) $tot_install = array_sum($totalinstall);
	else $tot_install=0;
	if(count($totalseals)>0) $tot_seals = array_sum($totalseals);
	else $tot_seals=0;
$percentGlass = 0.25;
$percentKits   = 0.12;
$percentAssembled = 0.13;
$percentInstalled  = 0.25;
$percentSeals   = 0;
$percentPrepped    =  0.25;
if ($totaljobPanels == 0) $jobGlassVal = 0;
else $jobGlassVal=(((((100/$totaljobPanels)*$tot_glass)*$percentGlass)*0.01)*$jobSum);

if ($jobSum == 0) $percentGlassComplete = 0;
else $percentGlassComplete=(((100/$jobSum)*$jobGlassVal)*0.01);

if ($totaljobPanels == 0) $jobKitsVal = 0;
else $jobKitsVal =(((((100/$totaljobPanels)*$tot_kit)*$percentKits)*0.01)*$jobSum);

if ($jobSum == 0) $percentKitsComplete = 0;
else $percentKitsComplete=(((100/$jobSum)*$jobKitsVal)*0.01);

if ($totaljobPanels == 0) $jobAssembledVal = 0;
else $jobAssembledVal=(((((100/$totaljobPanels)*$tot_assemble)*$percentAssembled)*0.01)*$jobSum);

if ($jobSum == 0) $percentAssembledComplete = 0;
else $percentAssembledComplete=(((100/$jobSum)*$jobAssembledVal)*0.01);

if ($totaljobPanels == 0) $jobPreppedVal = 0;
else $jobPreppedVal=(((((100/$totaljobPanels)*$tot_prep)*$percentPrepped)*0.01)*$jobSum);

if ($jobSum == 0) $percentPreppedComplete = 0;
else $percentPreppedComplete=(((100/$jobSum)*$jobPreppedVal)*0.01);

if ($totaljobPanels == 0) $jobInstalledVal = 0;
else $jobInstalledVal=(((((100/$totaljobPanels)*$tot_install)*$percentInstalled)*0.01)*$jobSum);

if ($jobSum == 0) $percentInstalledComplete = 0;
else $percentInstalledComplete=(((100/$jobSum)*$jobInstalledVal)*0.01);

if ($totaljobPanels == 0) $jobSealedVal = 0;
else $jobSealedVal=(((((100/$totaljobPanels)*$tot_seals)*$percentSeals)*0.01)*$jobSum);

if ($jobSum == 0) $percentSealsComplete = 0;
else $percentSealsComplete=(((100/$jobSum)*$jobSealedVal)*0.01);

$percentJobComplete=round(($percentGlassComplete+$percentKitsComplete+$percentAssembledComplete+$percentPreppedComplete+$percentInstalledComplete+$percentSealsComplete),2);
//$jobpercent = ($percentJobComplete*100)."%";
