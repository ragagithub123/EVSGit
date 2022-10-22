<?php ob_start();
session_start();
include('includes/functions.php');
if (!empty($_SESSION['agentid'])) {
	$panelcount = array();
	$glasscount = array();
	$kitcount   = array();
	$sealscount = array();
	$installcount = array();
	$prepcount = array();
	$assemblecount = array();
	$jobsum  = array();
	$jobcomplete = array();
	$jobglass = array();
	$jobkit = array();
	$jobassemble = array();
	$jobprep = array();
	$jobinstall = array();
	$jobseals = array();
	$post = array();
	$panelavg = array();
	$Locationid = array();
	/*$startdate = date('Y-m-d',strtotime('last monday')) ;
	$weekstartdate = date('Y-m-d', strtotime($st . ' -1 day'));
	$weekendate =  date('Y-m-d',strtotime('last day of this week'));*/
	$weeknumber = getWeeknumber(date('Y-m-d')) + 52 + 8;
	$daterange = getcurrentweekdates();
	$weekstartdate = $daterange[0];
	$weekendate = $daterange[1];
	$week_staff_end = $weekendate;
	$pdtweekstart = $weekstartdate;
	$pdtweekend = $weekendate;
	$getprop = $db->joinquery("SELECT locationid,projectid,unitnum,street,city,suburb,locationSearch FROM location WHERE agentid='" . $_SESSION['agentid'] . "'");
	if (mysqli_num_rows($getprop) > 0) {
		while ($row_prop = mysqli_fetch_assoc($getprop)) {
			if (empty($row_prop['locationSearch'])) {
				$locplace = $row_prop['unitnum'] . "," . $row_prop['street'];
				if (!empty($row_prop['suburb']))
					$locplace .= "," . $row_prop['suburb'];
				$row_prop['locationSearch'] = $locplace;
			}
			$postLocations[] = $row_prop;
		}
	}
	$gtelocation = $db->joinquery("SELECT location.locationid,location.projectid,location.unitnum,location.street,location.city,location.suburb,location.locationSearch,weekely_worksheet.* FROM location,weekely_worksheet WHERE location.locationid=weekely_worksheet.locationid AND location.projectid=weekely_worksheet.projectid AND weekely_worksheet.`agentid`='" . $_SESSION['agentid'] . "' AND weekely_worksheet.dates BETWEEN '" . $weekstartdate . "' AND '" . $weekendate . "' GROUP BY weekely_worksheet.locationid");
	if (mysqli_num_rows($gtelocation) > 0) {
		while ($row_loc = mysqli_fetch_assoc($gtelocation)) {
			if ($row_loc['locationid'] != null) {
				if (!empty($row_loc['projectid']) || ($row_loc['projectid'] != NULL)) {
					$getproject = $db->joinquery("SELECT project_name,projectid,project_date FROM location_projects WHERE projectid='" . $row_loc['projectid'] . "'");
					$rowPjt =  mysqli_fetch_array($getproject);
					$row_loc['project_name'] = $rowPjt['project_name'];
					$row_loc['projectid'] = $rowPjt['projectid'];
					$row_loc['project_date'] = date('d-m-Y', strtotime($rowPjt['project_date']));
					$row_loc['project_name'] = $rowPjt['project_name'] . " " . $row_loc['project_date'];
				}
				$Locationid[] = $row_loc['locationid'];
				$get_totalpanel = $db->joinquery("SELECT sum(window_type.numpanels) AS panelcount FROM room,window,window_type WHERE window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid AND window.selected_product!='HOLD' AND room.locationid='" . $row_loc['locationid'] . "'");
				$row_quote = mysqli_fetch_array($get_totalpanel);
				if (empty($row_loc['locationSearch'])) {
					$loc = $row_loc['unitnum'] . "," . $row_loc['street'];
					if (!empty($row_loc['suburb']))
						$loc .= "," . $row_loc['suburb'];
					$row_loc['locationSearch'] = $loc;
				}
				$row_loc['panelcount'] = $row_quote['panelcount'];
				$glasscount[] = $row_loc['glass'];
				$kitcount[]  =  $row_loc['kit'];
				$sealscount[] = $row_loc['seals'];
				$installcount[] = $row_loc['install'];
				$prepcount[] = $row_loc['prep'];
				$assemblecount[] = $row_loc['assemble'];
				$jobcomplete[] = $row_loc['percentcomplete'];
				$total_price = array();
				$panelcount[] = $row_quote['panelcount'];
				$get_window = $db->joinquery("SELECT window.windowid,window.selected_product,window.costsdg,window.costmaxe,window.costxcle,window.costevsx2,window.costevsx3 FROM room,window WHERE window.roomid=room.roomid AND window.selected_product!='HOLD' AND room.locationid=" . $row_loc['locationid'] . " GROUP BY window.windowid ORDER BY room.name ASC");
				while ($row_window = mysqli_fetch_array($get_window)) {
					if ($row_window['selected_product'] == "SGU" || $row_window['selected_product'] == "SDG") {
						$total_price[] = $row_window['costsdg'];
					} else if ($row_window['selected_product'] == "IGUX2" || $row_window['selected_product'] == "MAXe") {
						$total_price[] = $row_window['costmaxe'];
					} else if ($row_window['selected_product'] == "IGUX3" || $row_window['selected_product'] == "XCLe") {
						$total_price[] = $row_window['costxcle'];
					} else if ($row_window['selected_product'] == "EVSx2") {
						$total_price[] = $row_window['costevsx2'];
					} else if ($row_window['selected_product'] == "EVSx3") {
						$total_price[] = $row_window['costevsx3'];
					}
				}
				if (count($total_price) > 0) {
					$jobsum[] = array_sum($total_price);
					$row_loc['jobsum']  =  array_sum($total_price);
				} else {
					$row_loc['jobsum'] = 0;
				}
				/* Formula :: jobGlassVal[i]=(((((100/jobPanels[i])*jobGlass[i])*percentGlass)*0.01)*jobSum[i])*/
				$totalcost = array_sum($total_price);
				$jobGlassVal = (((((100 / $row_quote['panelcount']) * $row_loc['glass']) * 0.25) * 0.01) * $totalcost);
				$jobKitVal =  ((((((100 / $row_quote['panelcount']) * $row_loc['kit']) * 0.12) * 0.01) * $totalcost));
				$jobassembleVal = ((((((100 / $row_quote['panelcount']) * $row_loc['assemble']) * 0.13) * 0.01) * $totalcost));
				$jobprepVal  =  ((((((100 / $row_quote['panelcount']) * $row_loc['prep']) * 0.25) * 0.01) * $totalcost));
				$jobinstallVal =  ((((((100 / $row_quote['panelcount']) * $row_loc['install']) * 0.25) * 0.01) * $totalcost));
				$jobsealsVal =  ((((((100 / $row_quote['panelcount']) * $row_loc['seals']) * 0.0) * 0.01) * $totalcost));
				$panelavg[] = ($totalcost) / $row_quote['panelcount'];
				$jobglass[] = round($jobGlassVal, 2);
				$jobkit[]  = round($jobKitVal, 2);
				$jobassemble[]  = round($jobassembleVal, 2);
				$jobprep[]  = round($jobprepVal, 2);
				$jobinstall[] = round($jobinstallVal, 2);
				$jobseals[] = round($jobsealsVal, 2);
				$postData[] = $row_loc;
			}
		}
	}
	if (count($jobsum) > 0)
		$totalcost = array_sum($jobsum);
	else
		$totalcost = 0;
	$totalglasscount = array_sum($glasscount);
	$totalkitcount = array_sum($kitcount);
	$totalprepcount = array_sum($prepcount);
	$totalassemblecount = array_sum($assemblecount);
	$totalinstallcount  = array_sum($installcount);
	$totalsealscount  = array_sum($sealscount);
	if (count($panelcount) > 0)
		$totalpanels = array_sum($panelcount);
	else
		$totalpanels = 0;
	$glasspercent = 25;
	$kitpercent   = 12;
	$assemblepercent = 13;
	$installpercent  = 25;
	$sealspercent   = 0;
	$preppercent    =  25;
	$datecurnt = date('Y-m-d');
	/*$expdates = explode('-',$datecurnt);
$weekdates[]=getWeeks($expdates[1],$expdates[0]);*/
	if (count($jobglass) > 0)
		$weekglass = array_sum($jobglass);
	else
		$weekglass = 0;
	if (count($jobkit) > 0)
		$weekkit = array_sum($jobkit);
	else
		$weekkit = 0;
	if (count($jobassemble) > 0)
		$weekassemble = array_sum($jobassemble);
	else
		$weekassemble = 0;
	if (count($jobprep) > 0)
		$weekprep = array_sum($jobprep);
	else
		$weekprep = 0;
	if (count($jobinstall) > 0)
		$weekinstall = array_sum($jobinstall);
	else
		$weekinstall = 0;
	if (count($jobseals) > 0)
		$weekseals = array_sum($jobseals);
	else
		$weekseals = 0;
	$totalproduction = $weekglass + $weekassemble + $weekinstall + $weekkit + $weekprep + $weekseals;
	if (count($Locationid) > 0) {
		$totalpanelavg = round(array_sum($panelavg) / count($Locationid), 2);
		$total_job_complete = round(array_sum($jobcomplete) / count($Locationid), 2);
	} else {
		$totalpanelavg = 0;
		$total_job_complete = 0;
	}
	$curntyear = date('Y');
	$lastyear = $curntyear - 1;
	for ($j = 1; $j <= 12; $j++) {
		$allweeks_last[] = getWeeks($j, $lastyear);
	}
	for ($i = 1; $i <= 12; $i++) {
		$allweeks[] = getWeeks($i, $curntyear);
	}
	$allweeks = array_merge($allweeks_last, $allweeks);
	$i = 0;
	//for($i=0;$i<count($weekdates);$i++){
	foreach ($allweeks as $valweek) {
		foreach ($valweek as $yearweek) {
			$i++;
			// echo $dd['sd'].'/'.$dd['end']."<br>";
			$weekstartdate = $yearweek['startDate'];
			$weekendate = $yearweek['endDate'];
			//$mydates =explode("@",$weekdates[$i]);
			$totalproprice = array();
			$jobproglass = array();
			$jobprokit = array();
			$jobproassemble = array();
			$jobproprep = array();
			$jobproinstall = array();
			$jobproseals = array();
			$proLocationid = array();
			$jobpropanelavg = array();
			$getProduction = $db->joinquery("SELECT weekely_worksheet.*,sum(window_type.numpanels) AS panelcount FROM room,window,window_type,weekely_worksheet WHERE window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid AND window.selected_product!='HOLD' AND room.locationid=weekely_worksheet.locationid AND weekely_worksheet.`agentid`='" . $_SESSION['agentid'] . "' AND weekely_worksheet.dates BETWEEN '" . $weekstartdate . "' AND '" . $weekendate . "' GROUP BY weekely_worksheet.locationid");
			while ($rowPro = mysqli_fetch_array($getProduction)) {
				if ($rowPro['locationid'] != null) {
					$proLocationid[] = $rowPro['locationid'];
					$get_totalpanelpro = $db->joinquery("SELECT sum(window_type.numpanels) AS panelcount FROM room,window,window_type WHERE window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid AND window.selected_product!='HOLD' AND room.locationid='" . $rowPro['locationid'] . "'");
					$row_quotepro = mysqli_fetch_array($get_totalpanelpro);
					$totalproprice = array();
					$get_window = $db->joinquery("SELECT window.windowid,window.selected_product,window.costsdg,window.costmaxe,window.costxcle,window.costevsx2,window.costevsx3 FROM room,window WHERE window.roomid=room.roomid AND window.selected_product!='HOLD' AND room.locationid=" . $rowPro['locationid'] . " GROUP BY window.windowid ORDER BY room.name ASC");
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
						$jobprosum = array_sum($totalproprice);
					else
						$jobprosum = 0;
					$jobpropanelavg[] = ($jobprosum) / $row_quotepro['panelcount'];
					$jobproglass[] = round(((((((100 / $row_quotepro['panelcount']) * $rowPro['glass']) * 0.25) * 0.01) * $jobprosum)), 2);
					$jobprokit[]   =  round(((((((100 / $row_quotepro['panelcount']) * $rowPro['kit']) * 0.12) * 0.01) * $jobprosum)), 2);
					$jobproassemble[]   = round(((((((100 / $row_quotepro['panelcount']) * $rowPro['assemble']) * 0.13) * 0.01) * $jobprosum)), 2);
					$jobproprep[]   =  round(((((((100 / $row_quotepro['panelcount']) * $rowPro['prep']) * 0.25) * 0.01) * $jobprosum)), 2);
					$jobproinstall[]   =  round(((((((100 / $row_quotepro['panelcount']) * $rowPro['install']) * 0.25) * 0.01) * $jobprosum)), 2);
					$jobproseals[] =  round(((((((100 / $row_quotepro['panelcount']) * $rowPro['seals']) * 0.0) * 0.01) * $jobprosum)), 2);
				} //if
			}
			if (count($jobproglass) > 0)
				$weekproglass = array_sum($jobproglass);
			else
				$weekproglass = 0;
			if (count($jobprokit) > 0)
				$weekprokit = array_sum($jobprokit);
			else
				$weekprokit = 0;
			if (count($jobproassemble) > 0)
				$weekproassemble = array_sum($jobproassemble);
			else
				$weekproassemble = 0;
			if (count($jobproprep) > 0)
				$weekproprep = array_sum($jobproprep);
			else
				$weekproprep = 0;
			if (count($jobproinstall) > 0)
				$weekproinstall = array_sum($jobproinstall);
			else
				$weekproinstall = 0;
			if (count($jobproseals) > 0)
				$weekproseals = array_sum($jobproseals);
			else
				$weekproseals = 0;
			$totalProproduction = $weekproglass + $weekproassemble + $weekproinstall + $weekprokit + $weekproprep + $weekproseals;
			if (count($proLocationid) > 0)
				$proAvg = round(array_sum($jobpropanelavg) / count($proLocationid), 2);
			else
				$proAvg = 0;
			$active = betweenDates($datecurnt, $weekstartdate, $weekendate);
			$arr_end = $weekendate;
			$exp_end = explode("-", $arr_end);
			$endate = $exp_end[2] . " " . date("M", strtotime($arr_end)) . " " . $exp_end[0];
			$enddates[] = array('dates' => $endate, 'active' => $active, 'production' => $totalProproduction, 'avg' => $proAvg, 'startdate' => $weekstartdate, 'enddates' => $weekendate);
		}
	}
	/* staff report*/
	$date_range = getcurrentweekdates();
	$week_startdate = $date_range[0];
	$week_endate = $date_range[1];
	$getstaffreport = $db->joinquery("SELECT * FROM staffReports WHERE `agentid`='" . $_SESSION['agentid'] . "' AND week_date ='" . $week_endate . "'");
	if (mysqli_num_rows($getstaffreport) > 0) {
		while ($rowstaff = mysqli_fetch_assoc($getstaffreport)) {
			$getstaff = $db->joinquery("SELECT staff_name FROM agentStaffs WHERE staff_id='" . $rowstaff['staff_id'] . "'");
			if (mysqli_num_rows($getstaff) > 0) {
				$staffdata = mysqli_fetch_array($getstaff);
				$rowstaff['staff_name'] = $staffdata['staff_name'];
			} else {
				$rowstaff['staff_name'] = "";
			}
			$totalbonus[] = $rowstaff['bonus_hrs'];
			$totalnonbonus[] = $rowstaff['non_bonus_hrs'];
			$rowstaff['bluemon'] = 0;
			$rowstaff['bluetue'] = 0;
			$rowstaff['bluewed'] = 0;
			$rowstaff['bluethu'] = 0;
			$rowstaff['bluethu'] = 0;
			$rowstaff['bluesat'] = 0;
			$rowstaff['bluesun'] = 0;
			if (!empty($rowstaff['loadedday'])) {
				$expload = explode(',', $rowstaff['loadedday']);
				if (count($expload) > 0) {
					if (in_array('mon', $expload)) $rowstaff['bluemon'] = $rowstaff['fieldcount'];
					if (in_array('tue', $expload)) $rowstaff['bluetue'] = $rowstaff['fieldcount'];
					if (in_array('wed', $expload)) $rowstaff['bluewed'] = $rowstaff['fieldcount'];
					if (in_array('thu', $expload)) $rowstaff['bluethu'] = $rowstaff['fieldcount'];
					if (in_array('fri', $expload)) $rowstaff['bluefri'] = $rowstaff['fieldcount'];
					if (in_array('sat', $expload)) $rowstaff['bluesat'] = $rowstaff['fieldcount'];
					if (in_array('sun', $expload)) $rowstaff['bluesun'] = $rowstaff['fieldcount'];
				}
			}
			$staffreport[] = $rowstaff;
		}
		$total_bonus = array_sum($totalbonus);
		$totalnonbonus = array_sum($totalnonbonus);
	} else {
		$getmaxweek = $db->joinquery("SELECT MAX(`week_date`) AS week_date FROM staffReports WHERE agentid='" . $_SESSION['agentid'] . "'");
		if (mysqli_num_rows($getmaxweek) > 0) {
			$rowmaxweek = mysqli_fetch_array($getmaxweek);
			$getStaffData = $db->joinquery("SELECT staff_id,fieldcount FROM staffReports WHERE agentid='" . $_SESSION['agentid'] . "' AND week_date='" . $rowmaxweek['week_date'] . "'");
			while ($row_satffdata = mysqli_fetch_array($getStaffData)) {
				$db->joinquery("INSERT INTO staffReports(staff_id,fieldcount,agentid,week_date,bonus_hrs,mon,tue,wed,thu,fri)VALUES('" . $row_satffdata['staff_id'] . "','" . $row_satffdata['fieldcount'] . "','" . $_SESSION['agentid'] . "','" . $week_endate . "',40,8,8,8,8,8)");
			}
			$getstaffreport = $db->joinquery("SELECT * FROM staffReports WHERE `agentid`='" . $_SESSION['agentid'] . "' AND week_date ='" . $week_endate . "'");
			while ($rowstaff = mysqli_fetch_assoc($getstaffreport)) {
				$getstaff = $db->joinquery("SELECT staff_name FROM agentStaffs WHERE staff_id='" . $rowstaff['staff_id'] . "'");
				if (mysqli_num_rows($getstaff) > 0) {
					$staffdata = mysqli_fetch_array($getstaff);
					$rowstaff['staff_name'] = $staffdata['staff_name'];
				} else {
					$rowstaff['staff_name'] = "";
				}
				$totalbonus[] = $rowstaff['bonus_hrs'];
				$totalnonbonus[] = $rowstaff['non_bonus_hrs'];
				$staffreport[] = $rowstaff;
			}
			$total_bonus = array_sum($totalbonus);
			$totalnonbonus = array_sum($totalnonbonus);
		} else {
			$staffreport = array();
			$total_bonus = 0;
			$totalnonbonus = 0;
		}
	}
	$gettask = $db->joinquery("SELECT SUM(glass) AS glassum, SUM(`kit`) AS kitsum,SUM(`assemble`) AS assemblesum,SUM(`install`) AS installsum,SUM(`prep`) AS prepsum,SUM(`seals`) AS sealssum FROM weekely_worksheet WHERE agentid='" . $_SESSION['agentid'] . "' AND `dates` BETWEEN '" . $week_startdate . "' AND '" . $week_endate . "'");
	if (mysqli_num_rows($gettask) > 0) {
		$rowtask = mysqli_fetch_array($gettask);
		if ($rowtask['glassum'] != NULL)
			$glasssum = $rowtask['glassum'];
		else
			$glasssum = 0;
		if ($rowtask['kitsum'] != NULL)
			$kitsum = $rowtask['kitsum'];
		else
			$kitsum = 0;
		if ($rowtask['assemblesum'] != NULL)
			$assemblesum = $rowtask['assemblesum'];
		else
			$assemblesum = 0;
		if ($rowtask['installsum'] != NULL)
			$installsum = $rowtask['installsum'];
		else
			$installsum = 0;
		if ($rowtask['prepsum'] != NULL)
			$prepsum = $rowtask['prepsum'];
		else
			$prepsum = 0;
		if ($rowtask['sealssum'] != NULL)
			$sealssum = $rowtask['sealssum'];
		else
			$sealssum = 0;
	} else {
		$glasssum = 0;
		$kitsum = 0;
		$assemblesum = 0;
		$installsum = 0;
		$prepsum = 0;
		$sealssum = 0;
	}
	$kitsbonus = $kitsum * 2.50;
	$assemblebonus = $assemblesum * 2.50;
	$prepbonus =  $prepsum * 2.50;
	$installbonus = $installsum * 10.0;
	$sealsbonus = $sealssum * 2.50;
	$totalbonustask = $kitsbonus + $assemblebonus + $prepbonus + $installbonus + $sealsbonus;
	if ($total_bonus != 0)
		$hourlybonus = round($totalbonustask / $total_bonus, 2);
	else
		$hourlybonus = 0;
	foreach ($allweeks as $valweekstaff) {
		foreach ($valweekstaff as $yearweekstaff) {
			$weekstaff_startdate = $yearweekstaff['startDate'];
			$weekstaff_endate = $yearweekstaff['endDate'];
			$totalbonus_week = array();
			$getstaffreport_week = $db->joinquery("SELECT * FROM staffReports WHERE `agentid`='" . $_SESSION['agentid'] . "' AND week_date BETWEEN '" . $weekstaff_startdate . "' AND '" . $weekstaff_endate . "'");
			if (mysqli_num_rows($getstaffreport_week) > 0) {
				while ($rowstaff_week = mysqli_fetch_assoc($getstaffreport_week)) {
					$totalbonus_week[] = $rowstaff_week['bonus_hrs'];
				}
				$total_bonus_week = array_sum($totalbonus_week);
				$gettask_week = $db->joinquery("SELECT SUM(glass) AS glassum, SUM(`kit`) AS kitsum,SUM(`assemble`) AS assemblesum,SUM(`install`) AS installsum,SUM(`prep`) AS prepsum,SUM(`seals`) AS sealssum FROM weekely_worksheet WHERE agentid='" . $_SESSION['agentid'] . "' AND `dates` BETWEEN '" . $weekstaff_startdate . "' AND '" . $weekstaff_endate . "'");
				if (mysqli_num_rows($gettask_week) > 0) {
					$rowtask_week = mysqli_fetch_array($gettask_week);
					if ($rowtask_week['glassum'] != NULL)
						$glasssum_week = $rowtask_week['glassum'];
					else
						$glasssum_week = 0;
					if ($rowtask_week['kitsum'] != NULL)
						$kitsum_week = $rowtask_week['kitsum'];
					else
						$kitsum_week = 0;
					if ($rowtask_week['assemblesum'] != NULL)
						$assemblesum_week = $rowtask_week['assemblesum'];
					else
						$assemblesum_week = 0;
					if ($rowtask_week['installsum'] != NULL)
						$installsum_week = $rowtask_week['installsum'];
					else
						$installsum_week = 0;
					if ($rowtask_week['prepsum'] != NULL)
						$prepsum_week = $rowtask_week['prepsum'];
					else
						$prepsum_week = 0;
					if ($rowtask_week['sealssum'] != NULL)
						$sealssum_week = $rowtask_week['sealssum'];
					else
						$sealssum_week = 0;
				} else {
					$glasssum_week = 0;
					$kitsum_week = 0;
					$assemblesum_week = 0;
					$installsum_week = 0;
					$prepsum_week = 0;
					$sealssum_week = 0;
				}
				$kitsbonus_week = $kitsum_week * 2.50;
				$assemblebonus_week = $assemblesum_week * 2.50;
				$prepbonus_week =  $prepsum_week * 2.50;
				$installbonus_week = $installsum_week * 10.0;
				$sealsbonus_week = $sealssum_week * 2.50;
				$totalbonustask_week = $kitsbonus_week + $assemblebonus_week + $prepbonus_week + $installbonus_week + $sealsbonus_week;
				$hourlybonus_week = round($totalbonustask_week / $total_bonus_week, 2);
			} else {
				$total_bonus_week = 0;
				$glasssum_week = 0;
				$kitsum_week = 0;
				$assemblesum_week = 0;
				$installsum_week = 0;
				$prepsum_week = 0;
				$sealssum_week = 0;
				$kitsbonus_week = 0;
				$assemblebonus_week = 0;
				$prepbonus_week =  0;
				$installbonus_week = 0;
				$sealsbonus_week = 0;
				$totalbonustask_week = 0;
				$hourlybonus_week = 0;
			}
			$active_week = betweenDates($datecurnt, $weekstaff_startdate, $weekstaff_endate);
			$arr_end_week = $weekstaff_endate;
			$exp_end_week = explode("-", $arr_end_week);
			$endate_week = $exp_end_week[2] . " " . date("F", strtotime($arr_end_week)) . " " . $exp_end_week[0];
			$enddates_week[] = array('dates' => $endate_week, 'active' => $active_week, 'bonusTotal' => $total_bonus_week, 'Hourrly_bonus' => $hourlybonus_week, 'startdate' => $weekstaff_startdate, 'enddates' => $weekstaff_endate);
		}
	}
	/*-------------------------------------------------------Productivity Report-------------------------------------------------------------------------------*/
	$ProductivityTot = array();
	$getProdcutLocation = $db->joinquery("SELECT location.projectid,location.locationid,location.unitnum,location.street,location.city,location.suburb,location.locationSearch,location.jobstatusid FROM `location`,`jobstatus` WHERE location.jobstatusid=jobstatus.jobstatusid AND jobstatus.status NOT IN('EN','ER','QB','QP','QF','OA','QX') AND location.agentid='" . $_SESSION['agentid'] . "'");
	$cntLocations = mysqli_num_rows($getProdcutLocation);
	if ($cntLocations > 0) {
		while ($rowPOst = mysqli_fetch_array($getProdcutLocation)) {
			if (!empty($rowPOst['projectid']) || ($rowPOst['projectid'] != NULL)) {
				$getproject = $db->joinquery("SELECT project_name,projectid,project_date FROM location_projects WHERE projectid='" . $rowPOst['projectid'] . "'");
				$rowPjt =  mysqli_fetch_array($getproject);
				$rowPOst['projectid'] = $rowPjt['projectid'];
				$rowPOst['project_date'] = date('d-m-Y', strtotime($rowPjt['project_date']));
				$rowPOst['project_name'] = $rowPjt['project_name'] . " " . $rowPOst['project_date'];
			}
			if (empty($rowPOst['locationSearch'])) {
				$locdet = $rowPOst['unitnum'] . "," . $rowPOst['street'];
				if (!empty($rowPOst['suburb']))
					$locdet .= "," . $rowPOst['suburb'];
				$rowPOst['locationSearch'] = $locdet;
			}
			$passPjtid =  $rowPOst['projectid'];
			$passloc = $rowPOst['locationid'];
			include('jobcomplete.php');
			$rowPOst['panelcnt'] = $totaljobPanels;
			$rowPOst['install'] = $tot_install;
			$rowPOst['percentcomplete'] = $percentJobComplete;
			include('summaryCal.php');
			$rowPOst['toatlcost'] = $ttoal_price;
			if ($rowPOst['panelcnt'] == 0) $rowPOst['panelavg'] = 0;
			else $rowPOst['panelavg'] = round(($rowPOst['toatlcost'] / $rowPOst['panelcnt']), 2);
			$rowPOst['marginTotal'] = round($totalmargin, 2);
			$timesheethours = $installTotal + $makeTotal + $prepTotal;
			//$rowPOst['quotedhrs'] = round((array_sum($installation)+array_sum($manufature)+$timesize+$painttimes),2);
			$rowPOst['quotedhrs'] = round(($ttoal_man_insatll_hrs), 2);
			$rowPOst['timesheethrs'] = $timesheethours;
			$rowPOst['targetprodcutivity'] = $hourlyreturn;
			if ($timesheethours == 0) $rowPOst['actualprodcutivity'] = 0;
			else $rowPOst['actualprodcutivity'] = round(($totalmargin / 1.15 / $timesheethours), 2);
			$prod_panelcount_arr[] = $totaljobPanels;
			$prod_panel_insatll_arr[] = $tot_install;
			$ProductivityTot[] = $ttoal_price;
			$percentcomplet_arr[] = ($percentJobComplete);
			$prod_avgpanel[] = 	$rowPOst['panelavg'];
			$prod_targetProdcutivity[] = $rowPOst['targetprodcutivity'];
			//$prod_actualProdcutivity[] = $rowPOst['actualprodcutivity'];
			$prod_totalmargin[] = $totalmargin;
			$QuotedHours[] = $rowPOst['quotedhrs'];
			$pdtloc[] = $rowPOst;
		}
		$pdtloc = array_sort($pdtloc, 'percentcomplete', SORT_DESC);
		if ($cntLocations == 0) $pipelinecompletepercent = 0;
		else $pipelinecompletepercent = (array_sum($percentcomplet_arr) / $cntLocations);
		$Pipelinecomplete = round(($pipelinecompletepercent * 100), 2);
		$pipe_complete = (100 - $Pipelinecomplete) / 100;
		$Pipelinevalue = round((array_sum($ProductivityTot) * ($pipe_complete)), 2);
		$targetdquoteshrs = round((array_sum($QuotedHours) * $pipe_complete), 2);
		$Pipelinepanels = array_sum($prod_panelcount_arr) - array_sum($prod_panel_insatll_arr);
		if ($cntLocations == 0) $Pipeline_panelavg = 0;
		else $Pipeline_panelavg = round(((array_sum($prod_avgpanel)) / $cntLocations), 2);
		if ($cntLocations == 0) $Pipeline_targetProductivity = 0;
		else $Pipeline_targetProductivity = round((array_sum($prod_targetProdcutivity) / $cntLocations), 2);
		//$Pipeline_actualProductivity = round((array_sum($prod_actualProdcutivity)/$cntLocations),2);
		if ($cntLocations == 0) $Pipeline_avggmargin = 0;
		else $Pipeline_avggmargin = round((array_sum($prod_totalmargin) / $cntLocations), 2);
		if ($Pipeline_targetProductivity == 0) $Pipelien_business = 0;
		else $Pipelien_business = round(($Pipeline_actualProductivity / $Pipeline_targetProductivity), 2);
	}
	/*----------------------------------------------------------Ens Productivity--------------------------------------------------------------------------*/
	include('templates/header.php');
	include('views/report.htm');
	include('templates/footer.php');
} else {
	header('Location:index.php');
}
function getWeeks($month, $year)
{
	$res = array();
	$month = intval($month);				//force month to single integer if '0x'
	$suff = array('st', 'nd', 'rd', 'th', 'th', 'th'); 		//week suffixes
	$end = date('t', mktime(0, 0, 0, $month, 1, $year)); 		//last date day of month: 28 - 31
	$start = date('w', mktime(0, 0, 0, $month, 1, $year)); 	//1st day of month: 0 - 6 (Sun - Sat)
	$last = 7 - $start; 					//get last day date (Sat) of first week
	$noweeks = ceil((($end - ($last + 1)) / 7) + 1);		//total no. weeks in month
	$output = "";						//initialize string		
	$monthlabel = str_pad($month, 2, '0', STR_PAD_LEFT);
	for ($x = 1; $x < $noweeks + 1; $x++) {
		if ($x == 1) {
			$startdate = "$year-$monthlabel-01";
			$day = $last - 6;
		} else {
			$day = $last + 1 + (($x - 2) * 7);
			$day = str_pad($day, 2, '0', STR_PAD_LEFT);
			$startdate = "$year-$monthlabel-$day";
		}
		if ($x == $noweeks) {
			$enddate = "$year-$monthlabel-$end";
		} else {
			$dayend = $day + 6;
			$dayend = str_pad($dayend, 2, '0', STR_PAD_LEFT);
			$enddate = "$year-$monthlabel-$dayend";
		}
		//$output .= "{$x}{$suff[$x-1]} week -> Start date=$startdate End date=$enddate <br />";	
		//$res[] .= $startdate ."@" .$enddate;
		$res[] = array('startDate' => $startdate, 'endDate' => $enddate);
	}
	return $res;
}
function betweenDates($currentDate, $startDate, $endDate)
{
	$currentDate = date('Y-m-d', strtotime($currentDate));
	$startDate = date('Y-m-d', strtotime($startDate));
	$endDate = date('Y-m-d', strtotime($endDate));
	if (($currentDate >= $startDate) && ($currentDate <= $endDate)) {
		return 1;
	} else {
		return 0;
	}
}
function getcurrentweekdates()
{
	$first_day_of_the_week = 'Sunday';
	$start_of_the_week     = strtotime("Last $first_day_of_the_week");
	if (strtolower(date('l')) === strtolower($first_day_of_the_week)) {
		$start_of_the_week = strtotime('today');
	}
	$end_of_the_week = $start_of_the_week + (60 * 60 * 24 * 7) - 1;
	//$date_format =  'l jS \of F Y h:i:s A'; 
	$date_format =  'Y-m-d';
	$output = array(date($date_format, $start_of_the_week), date($date_format, $end_of_the_week));
	return $output;
}
function getWeeknumber($crntdate)
{
	$date = new DateTime($crntdate);
	$week = $date->format("W");
	if ($date->format("M") == 'Jan')
		return 0;
	else
		return $week;
}
function array_sort($array, $on, $order)
{
	$new_array = array();
	$sortable_array = array();
	if (count($array) > 0) {
		foreach ($array as $k => $v) {
			if (is_array($v)) {
				foreach ($v as $k2 => $v2) {
					if ($k2 == $on) {
						$sortable_array[$k] = $v2;
					}
				}
			} else {
				$sortable_array[$k] = $v;
			}
		}
		switch ($order) {
			case SORT_ASC:
				asort($sortable_array);
				break;
			case SORT_DESC:
				arsort($sortable_array);
				break;
		}
		foreach ($sortable_array as $k => $v) {
			$new_array[$k] = $array[$k];
		}
	}
	return $new_array;
}
