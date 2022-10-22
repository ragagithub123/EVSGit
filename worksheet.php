<?php ob_start();
session_start();
include('includes/functions.php');
if (!empty($_SESSION['agentid'])) {
	$Locationid = base64_decode($_REQUEST['id']);
	$get_details = $db->joinquery("SELECT location.locationid,location.projectid,location.`unitnum`,location.`street`,location.`suburb`,location.`city`,`location`.locationstatusid,location.notes,location.photoid,location.`status1`,location.`status2`,location.status3,location.status4,location.status5,location.status6,location.status7,location.status8,location.status9,location.status10,location.status11,location.status12,location.status13,location.status14,location.status15,location.hs_overheadpower,location.hs_siteaccess_notes,location.hs_vegetation_notes,location.hs_heightaccess_notes,location.hs_heightaccess_photoid,location.hs_childrenanimals_notes,location.hs_traffic_notes,location.hs_weather_notes,location.hs_worksite_notes,location.distance,location.travel_rate,location.`quotesdg`,location.`quotemaxe`,location.`quotexcle`,location.`quoteevsx2`,location.`quoteevsx3`,customer.customerid,customer.firstname,customer.lastname,customer.email,customer.phone,agent.labourrate,photo.width,photo.height FROM location LEFT JOIN photo ON location.photoid=photo.photoid ,agent,customer WHERE location.agentid=agent.agentid AND location.customerid=customer.customerid AND location.`locationid`=" . $Locationid . "");
	$row_details = mysqli_fetch_array($get_details);
	if (!empty($row_details['projectid']) && ($row_details['projectid'] != NULL)) {
		$getproject = $db->joinquery("SELECT project_name,projectid,project_date FROM location_projects WHERE projectid='" . $row_details['projectid'] . "'");
		$rowPjt =  mysqli_fetch_array($getproject);
		$row_details['project_name'] = $rowPjt['project_name'];
		$row_details['projectid'] = $rowPjt['projectid'];
		$row_details['project_date'] = date('d-m-Y', strtotime($rowPjt['project_date']));
	}
	$getworksheet = $db->joinquery("SELECT * FROM worksheet WHERE locationid=" . $Locationid . "");
	if (mysqli_num_rows($getworksheet) == 1) {
		$rowsheet =	mysqli_fetch_array($getworksheet);
		$row_details['quoted_date'] = $rowsheet['quoted_date'];
		$row_details['check_measured'] = $rowsheet['check_measured'];
		$row_details['glass_ordered'] = $rowsheet['glass_ordered'];
		$row_details['glass_received'] = $rowsheet['glass_received'];
		$row_details['job_finished'] = $rowsheet['job_finished'];
		$row_details['job_checked'] = $rowsheet['job_checked'];
		$row_details['job_invoiced'] = $rowsheet['job_invoiced'];
	} else {
		$row_details['quoted_date'] = "0000-00-00";
		$row_details['check_measured'] = "0000-00-00";
		$row_details['glass_ordered'] = "0000-00-00";
		$row_details['glass_received'] = "0000-00-00";
		$row_details['job_finished'] = "0000-00-00";
		$row_details['job_checked'] = "0000-00-00";
		$row_details['job_invoiced'] = "0000-00-00";
	}
	$res_pjtdata = $db->joinquery("SELECT projectid FROM weekely_worksheet WHERE locationid=" . $Locationid . " AND dates!='0000-00-00' AND projectid IS NULL");
	if (mysqli_num_rows($res_pjtdata) > 0) {
		if ($row_details['projectid'] != NULL) {
			$db->joinquery("UPDATE weekely_worksheet SET projectid='" . $row_details['projectid'] . "' WHERE locationid=" . $Locationid . "");
			$getweektimesheet = $db->joinquery("SELECT * FROM  weekely_worksheet WHERE locationid=" . $Locationid . " AND dates!='0000-00-00' AND projectid='" . $row_details['projectid'] . "' ORDER BY week ASC");
		} else {
			$getweektimesheet = $db->joinquery("SELECT * FROM  weekely_worksheet WHERE locationid=" . $Locationid . " AND dates!='0000-00-00' ORDER BY week ASC");
		}
	} else {
		$getweektimesheet = $db->joinquery("SELECT * FROM  weekely_worksheet WHERE locationid=" . $Locationid . " AND dates!='0000-00-00' AND projectid='" . $row_details['projectid'] . "' ORDER BY week ASC");
	}
	$numrows = mysqli_num_rows($getweektimesheet);
	$totalglass = array();
	$totalkit = array();
	$totalassemble = array();
	$totalprep = array();
	$totalinstall = array();
	$totalseals = array();
	if ($numrows > 0) {
		while ($rowweek = mysqli_fetch_assoc($getweektimesheet)) {
			$rowweek['dates'] = date('d-m-Y', strtotime($rowweek['dates']));
			$totalglass[] = $rowweek['glass'];
			$totalkit[] = $rowweek['kit'];
			$totalassemble[] = $rowweek['assemble'];
			$totalprep[] = $rowweek['prep'];
			$totalinstall[] = $rowweek['install'];
			$totalseals[] = $rowweek['seals'];
			$weekely[] = $rowweek;
		}
		if ($numrows < 4) {
			$repcnt = 4 - (count($weekely));
			for ($i = 1; $i <= $repcnt; $i++) {
				$pending[] = array('dates' => '00-00-0000', 'glass' => '', 'kit' => '', 'prep' => '', 'install' => '', 'assemble' => '', 'seals' => '');
			}
			$weekely = array_merge($weekely, $pending);
		}
	} else {
		//$		row_details['weeksheet']=array();
		for ($i = 1; $i <= 4; $i++) {
			$weekely[] = array('dates' => '00-00-0000', 'glass' => '', 'kit' => '', 'prep' => '', 'install' => '', 'assemble' => '', 'seals' => '');
		}
	}
	if (count($totalglass) > 0) $tot_glass = array_sum($totalglass);
	else $tot_glass = 0;
	if (count($totalassemble) > 0) $tot_assemble = array_sum($totalassemble);
	else $tot_assemble = 0;
	if (count($totalkit) > 0) $tot_kit = array_sum($totalkit);
	else $tot_kit = 0;
	if (count($totalprep) > 0) $tot_prep = array_sum($totalprep);
	else $tot_prep = 0;
	if (count($totalinstall) > 0) $tot_install = array_sum($totalinstall);
	else $tot_install = 0;
	if (count($totalseals) > 0) $tot_seals = array_sum($totalseals);
	else $tot_seals = 0;
	$totalproprice = array();
	$getPanel = $db->joinquery("SELECT sum(window_type.numpanels) AS panelcount FROM room,window,window_type WHERE window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid AND window.selected_product!='HOLD' AND room.locationid=" . $Locationid . "");
	$rowPanel = mysqli_fetch_array($getPanel);
	$totaljobPanels = $rowPanel['panelcount'];
	$get_window = $db->joinquery("SELECT window.windowid,window.selected_product,window.costsdg,window.costmaxe,window.costxcle,window.costevsx2,window.costevsx3 FROM room,window WHERE window.roomid=room.roomid AND window.selected_product!='HOLD' AND room.locationid=" . $Locationid . " GROUP BY window.windowid ORDER BY room.name ASC");
	while ($rowpro_window = mysqli_fetch_array($get_window)) {
		if ($rowpro_window['selected_product'] == "SGU" || $rowpro_window['selected_product'] == "SDG") {
			$totalproprice[] = $rowpro_window['costsdg'];
		} else if ($rowpro_window['selected_product'] == "IGUX2" || $rowpro_window['selected_product'] == "MAXe") {
			$totalproprice[] = $rowpro_window['costmaxe'];
		} else if ($rowpro_window['selected_product'] == "IGUX3" || $rowpro_window['selected_product'] == "XCLe") {
			$totalproprice[] = $rowpro_window['costxcle'];
		} else if ($rowpro_window['selected_product'] == "EVSx2") {
			$totalproprice[] = $rowpro_window['costevsx2'];
		} else if ($rowpro_window['selected_product'] == "EVSx3") {
			$totalproprice[] = $rowpro_window['costevsx3'];
		}
	}
	if (count($totalproprice) > 0)
		$jobSum = array_sum($totalproprice);
	else
		$jobSum = 0;
	$percentGlass = 0.25;
	$percentKits   = 0.12;
	$percentAssembled = 0.13;
	$percentInstalled  = 0.25;
	$percentSeals   = 0;
	$percentPrepped    =  0.25;
	$jobGlassVal = ($totaljobPanels != 0) ? (((((100 / $totaljobPanels) * $tot_glass) * $percentGlass) * 0.01) * $jobSum) : 0;
	$percentGlassComplete = ($jobSum != 0) ? (((100 / $jobSum) * $jobGlassVal) * 0.01) : 0;
	$jobKitsVal = ($totaljobPanels != 0) ? (((((100 / $totaljobPanels) * $tot_kit) * $percentKits) * 0.01) * $jobSum) : 0;
	$percentKitsComplete = ($jobSum != 0) ? (((100 / $jobSum) * $jobKitsVal) * 0.01) : 0;
	$jobAssembledVal = ($totaljobPanels != 0) ? (((((100 / $totaljobPanels) * $tot_assemble) * $percentAssembled) * 0.01) * $jobSum) : 0;
	$percentAssembledComplete = ($jobSum != 0) ? (((100 / $jobSum) * $jobAssembledVal) * 0.01) : 0;
	$jobPreppedVal = ($totaljobPanels != 0) ? (((((100 / $totaljobPanels) * $tot_prep) * $percentPrepped) * 0.01) * $jobSum) : 0;
	$percentPreppedComplete = ($jobSum != 0) ? (((100 / $jobSum) * $jobPreppedVal) * 0.01) : 0;
	$jobInstalledVal = ($totaljobPanels != 0) ? (((((100 / $totaljobPanels) * $tot_install) * $percentInstalled) * 0.01) * $jobSum) : 0;
	$percentInstalledComplete = ($jobSum != 0) ? (((100 / $jobSum) * $jobInstalledVal) * 0.01) : 0;
	$jobSealedVal = ($totaljobPanels != 0) ? (((((100 / $totaljobPanels) * $tot_seals) * $percentSeals) * 0.01) * $jobSum) : 0;
	$percentSealsComplete = ($jobSum != 0) ? (((100 / $jobSum) * $jobSealedVal) * 0.01) : 0;
	$percentJobComplete = round(($percentGlassComplete + $percentKitsComplete + $percentAssembledComplete + $percentPreppedComplete + $percentInstalledComplete + $percentSealsComplete), 2);
	$jobpercent = ($percentJobComplete * 100) . "%";
	$total_install_hrs = array();
	//$	get_window=$db->joinquery("SELECT room.`roomid`,room.`name` AS room_name,window.windowid,window.windowtypeid,window.`selected_product`,window_type.name FROM room,window,window_type WHERE window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid AND window.selected_product!='HOLD' AND room.locationid=".base64_decode($_REQUEST['id'])." GROUP BY window.windowid ORDER BY room_name ASC");
	$get_window = $db->joinquery("SELECT room.`roomid`,room.`name` AS room_name,window.windowid,window.projectid,window.notes,window.windowtypeid,window.`selected_product`,window.`selected_hours`,window.`materialCategory`,window_type.name FROM room,window,window_type,location WHERE window.roomid=room.roomid AND window.projectid=location.projectid AND window.windowtypeid=window_type.windowtypeid AND room.locationid=location.locationid AND location.locationid='" . $Locationid . "' GROUP BY window.windowid ORDER BY room_name ASC");
	$i = 64;
	while ($row_window = mysqli_fetch_array($get_window)) {
		$i = $i + 1;
		$getlabour = $db->joinquery("SELECT panelid,SUM(dglabour) AS igulabour, SUM(evslabour) AS evslabour FROM panel WHERE windowid = " . $row_window['windowid'] . "");
		$row_labour = mysqli_fetch_array($getlabour);
		if ($row_window['selected_product'] != 'HOLD') {
			if ($row_window['selected_product'] == "SGU" || $row_window['selected_product'] == "SDG") {
				$row_window['selected_hours'] = $row_labour['igulabour'];
				$total_install_hrs[] = $row_window['selected_hours'];
			} else if ($row_window['selected_product'] == "IGUX2" || $row_window['selected_product'] == "IGUX3" || $row_window['selected_product'] == "XCLe") {
				$row_window['selected_hours'] = $row_labour['igulabour'];
				$total_install_hrs[] = $row_window['selected_hours'];
			} else if ($row_window['selected_product'] == "EVSx2") {
				$row_window['selected_hours'] = $row_labour['evslabour'];
				$total_install_hrs[] = $row_window['selected_hours'];
			} else if ($row_window['selected_product'] == "EVSx3") {
				$row_window['selected_hours'] = $row_labour['evslabour'];
				$total_install_hrs[] = $row_window['selected_hours'];
			} else {
				$row_window['selected_hours'] = 0;
				$total_install_hrs = array();
			}
			//$			get_panel =$db->joinquery("SELECT room.roomid,room.name as room_name,window_type.name,panel.panelid,panel.width,panel.height,panel.center,panel.panelnum,panel.profileid,panel.windowid,panel.`safetyid`,panel.`glasstypeid`,panel.`styleid`,panel.`conditionid`,panel.`astragalsid`,paneloption_style.`evsProfileTop`,paneloption_style.`evsProfileSides`,paneloption_style.`evsProfileBottom`,paneloption_style.`evsGlassX`,paneloption_style.`evsGlassY`,paneloption_style.`evsProfileX`,paneloption_style.`evsProfileY`,paneloption_style.`retroProfileTop`,paneloption_style.`retroProfileSides`,paneloption_style.`retroProfileBottom`,paneloption_style.`retroGlassX`,paneloption_style.`retroGlassY`,paneloption_style.`retroProfileX`,paneloption_style.`retroProfileY`,paneloption_style.evsProfileRight,paneloption_style.evsProfileLeft,paneloption_style.evsOutPanelThickness,paneloption_style.evsOutPanelType,paneloption_style.evsInPanelThickness,paneloption_style.evsInPanelType,paneloption_style.retroOutPanelThickness,paneloption_style.retroOutPanelType,paneloption_style.retroInPanelThickness,paneloption_style.retroInPanelType,paneloption_style.retroProfileLeft,paneloption_style.retroProfileRight,paneloption_astragal.name AS astragal_name,paneloption_condition.name AS condition_name,paneloption_safety.name AS safty_name,paneloption_glasstype.name AS galsstype_name,paneloption_glasstype.typevalue,window.selected_product FROM room,window_type,window,panel,paneloption_astragal,paneloption_safety,paneloption_style,paneloption_glasstype,paneloption_condition WHERE room.roomid=window.roomid AND window.windowtypeid=window_type.windowtypeid AND panel.windowid=window.windowid AND panel.styleid=paneloption_style.styleid AND panel.safetyid=paneloption_safety.safetyid AND panel.astragalsid=paneloption_astragal.astragalsid AND panel.glasstypeid=paneloption_glasstype.glasstypeid AND panel.conditionid=paneloption_condition.conditionid AND window.selected_product!='HOLD' AND room.locationid ='".base64_decode($_REQUEST['id'])."' GROUP BY panel.panelid ");
			$panels = array();
			$get_panel = $db->joinquery("SELECT panel.panelid,panel.width,panel.height,panel.center,panel.measurement,panel.panelnum,panel.profileid,panel.windowid,panel.`safetyid`,panel.`glasstypeid`,panel.`styleid`,panel.`conditionid`,panel.`astragalsid`,`paneloption_style`.name AS stylename,paneloption_style.`evsProfileTop`,paneloption_style.`evsProfileSides`,paneloption_style.`evsProfileBottom`,paneloption_style.`evsGlassX`,paneloption_style.`evsGlassY`,paneloption_style.`evsProfileX`,paneloption_style.`evsProfileY`,paneloption_style.`retroProfileTop`,paneloption_style.`retroProfileSides`,paneloption_style.`retroProfileBottom`,paneloption_style.`retroGlassX`,paneloption_style.`retroGlassY`,paneloption_style.`retroProfileX`,paneloption_style.`retroProfileY`,paneloption_style.evsProfileRight,paneloption_style.evsProfileLeft,paneloption_style.evsOutPanelThickness,paneloption_style.evsOutPanelType,paneloption_style.evsInPanelThickness,paneloption_style.evsInPanelType,paneloption_style.retroOutPanelThickness,paneloption_style.retroOutPanelType,paneloption_style.retroInPanelThickness,paneloption_style.retroInPanelType,paneloption_style.retroProfileLeft,paneloption_style.retroProfileRight,paneloption_astragal.name AS astragal_name,paneloption_condition.name AS condition_name,paneloption_safety.name AS safty_name,paneloption_glasstype.name AS galsstype_name,paneloption_glasstype.typevalue FROM panel,paneloption_astragal,paneloption_safety,paneloption_style,paneloption_glasstype,paneloption_condition WHERE 
panel.styleid=paneloption_style.styleid AND panel.safetyid=paneloption_safety.safetyid AND panel.astragalsid=paneloption_astragal.astragalsid AND panel.glasstypeid=paneloption_glasstype.glasstypeid AND panel.conditionid=paneloption_condition.conditionid AND panel.windowid=" . $row_window['windowid'] . "");
			if (mysqli_num_rows($get_panel) > 0) {
				$j = 0;
				while ($row_panel = mysqli_fetch_array($get_panel)) {
					$j++;
					if ($row_window['selected_product'] == "EVSx3" || $row_window['selected_product'] == "EVSx2") {
						$glassX = $row_panel['evsGlassX'];
						$glassY = $row_panel['evsGlassY'];
					} else {
						$glassX = $row_panel['retroGlassX'];
						$glassY = $row_panel['retroGlassY'];
					}
					if ($glassX == NULL) $glassX = 0;
					if ($glassY == NULL) $glassY = 0;
					if ($row_panel['width'] > 0) {
						$row_panel['GlassX'] = 	$row_panel['width'] + $glassX;
					} else {
						$row_panel['GlassX'] = 0;
					}
					if (($row_panel['center']) > ($row_panel['height'])) {
						$row_panel['GlassY'] = ($row_panel['center']) + $glassY;
					} else if ($row_panel['height'] > 0) {
						$row_panel['GlassY'] = ($row_panel['height']) + ($glassY);
					} else {
						$row_panel['GlassY'] = 0;
					}
					$getworkflow = $db->joinquery("SELECT * FROM workflow WHERE locationid=" . $Locationid . " AND roomid =" . $row_window['roomid'] . " AND panelid=" . $row_panel['panelid'] . "");
					if (mysqli_num_rows($getworkflow) > 0) {
						$rowflow = mysqli_fetch_assoc($getworkflow);
						$row_panel['workflow'] = $rowflow;
					}
					$row_panel['ID'] = chr($i) . $j;
					$panels[] = $row_panel;
				}
				$row_window['panelarray'] = $panels;
			}
			$extras = array();
			$hours = array();
			$get_extras = $db->joinquery("SELECT window_extras.*,products.* FROM window_extras,products WHERE window_extras.productid=products.productid AND window_extras.windowid='" . $row_window['windowid'] . "' ORDER BY extraid DESC");
			while ($row_extras = mysqli_fetch_array($get_extras)) {
				if ($row_extras['imageid'] == 0)
					$row_extras['imageid'] = $row_extras['productid'];
				$hours[] = $row_extras['hours'];
				$totalextrahours[] = $row_extras['hours'];
				$extras[] = $row_extras;
			}
			$row_window['extra_hour'] = array_sum($hours);
			$row_window['extras'] = $extras;
			$postpanel[] = $row_window;
		}
	}
	if (count($totalextrahours) > 0)
		$total_extra_hrs = array_sum($totalextrahours);
	else
		$total_extra_hrs = 0;
	if (count($total_install_hrs) > 0)
		$totalinstallhrs = array_sum($total_install_hrs);
	else
		$totalinstallhrs = 0;
	date_default_timezone_set('NZST');
	$date = date('Y-m-d');
	include('templates/header.php');
	include('views/worksheet.htm');
	include('templates/footer.php');
} else {
	header('Location:index.php');
}
