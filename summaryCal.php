   <?php ob_start();
	session_start();
	if (!empty($_SESSION['agentid'])) {
		$array_glass_wsvalue = array();
		$array_profile_wsvalue = array();
		$array_time_wsvalue_manufacture = array();
		$array_time_wsvalue_install = array();
		$array_extras_time_wsvalue = array();
		$array_extras_cost_wsvalue = array();
		$array_glass_rprice = array();
		$array_rprice_profile = array();
		$array_time_rprice_manufacture = array();
		$array_time_rprice_install = array();
		$array_cost_rprice_extras = array();
		$traveldisatncecost = array();
		$travelTimecost = array();
		$totalinsallhrs = array();
		$totalmanufacture = array();
		$array_rprice_extras = array();
		$colourid = array();
		$getagentrate = $db->joinquery("SELECT * FROM agent WHERE agentid='" . $_SESSION['agentid'] . "'");
		$rowagnt = mysqli_fetch_array($getagentrate);
		$productmargin = $db->joinquery("SELECT * FROM location_margins WHERE locationid=" . $passloc . "");
		if (mysqli_num_rows($productmargin) > 0) {
			$row_margin  =  mysqli_fetch_array($productmargin);
			$costmargin = $row_margin['productmargin'];
			$travelrate = $row_margin['travelrate'];
			$evsmargin  =  $row_margin['evsmargin'];
			$igumargin  =  $row_margin['igumargin'];
			$labourrate =  $row_margin['labourrate'];
		} else {
			$costmargin = $rowagnt['productmargin'];
			$travelrate = $rowagnt['agenttravelrate'];
			$evsmargin  =  $rowagnt['evsmargin'];
			$igumargin  =  $rowagnt['igumargin'];
			$labourrate =  $rowagnt['labourrate'];
		}
		$getpanelglass = $db->joinquery("SELECT panel.glasstypeid,window.selected_product,paneloption_glasstype.name AS glassname,paneloption_layers.name FROM panel,room,window,paneloption_glasstype,paneloption_layers,window_layers WHERE  panel.windowid=window.windowid AND window.roomid=room.roomid AND window.selected_product!='HOLD' AND paneloption_glasstype.glasstypeid=panel.glasstypeid AND paneloption_layers.layersid=window_layers.layerid AND paneloption_layers.glassType=window_layers.glassid AND window_layers.glassid=paneloption_glasstype.glasstypeid AND room.locationid=" . $passloc . " GROUP BY panel.glasstypeid");
		if (mysqli_num_rows($getpanelglass) > 0) {
			while ($row_glass = mysqli_fetch_array($getpanelglass)) {
				$arrm2 = array();
				$wsvalue_glass = array();
				$rrpvalue_glass = array();
				$getGlass = $db->joinquery("SELECT panel.glasstypeid,panel.center,panel.width,panel.`height`,paneloption_style.evsGlassX,paneloption_style.evsGlassY,paneloption_style.retroGlassX,paneloption_style.retroGlassY,paneloption_layers.name,window.selected_product,paneloption_glasstype.name AS glassname FROM panel,room,window,paneloption_glasstype,paneloption_layers,window_layers,paneloption_style WHERE panel.styleid=paneloption_style.styleid AND panel.windowid=window.windowid AND window.roomid=room.roomid AND window.selected_product!='HOLD' AND panel.glasstypeid='" . $row_glass['glasstypeid'] . "' AND paneloption_glasstype.glasstypeid=panel.glasstypeid AND paneloption_layers.layersid=window_layers.layerid AND paneloption_layers.glassType=window_layers.glassid AND window_layers.glassid=paneloption_glasstype.glasstypeid AND room.locationid=" . $passloc . " GROUP BY panel.panelid");
				while ($rowglass = mysqli_fetch_array($getGlass)) {
					if ($rowglass['selected_product'] == 'SGU' || $rowglass['selected_product'] == 'SDG' || $rowglass['selected_product'] == 'MAXe' || $rowglass['selected_product'] == 'XCLe') {
						$row_glass['glassrate'] = $rowagnt['SGUrate'];
						$glassX = $rowglass['retroGlassX'];
						$glassY = $rowglass['retroGlassY'];
						$margin = $igumargin;
					}
					if ($rowglass['selected_product'] == 'IGUX2') {
						$row_glass['glassrate'] = $rowagnt['IGUx2rate'];
						$glassX = $rowglass['retroGlassX'];
						$glassY = $rowglass['retroGlassY'];
						$margin = $igumargin;
					}
					if ($rowglass['selected_product'] == 'IGUX3') {
						$row_glass['glassrate'] = $rowagnt['IGUx3rate'];
						$glassX = $rowglass['retroGlassX'];
						$glassY = $rowglass['retroGlassY'];
						$margin = $igumargin;
						$assemble = $rowglass['IGUassemble'];
						$materials = $rowglass['IGUx2_Materials'];
					}
					if ($rowglass['selected_product'] == 'EVSx2') {
						$row_glass['glassrate'] = $rowagnt['EVSx2rate'];
						$glassX = $rowglass['evsGlassX'];
						$glassY = $rowglass['evsGlassY'];
						$margin = $evsmargin;
					}
					if ($rowglass['selected_product'] == 'EVSx3') {
						$row_glass['glassrate'] = $rowagnt['EVSx3rate'];
						$glassX = $rowglass['evsGlassX'];
						$glassY = $rowglass['evsGlassY'];
						$margin = $evsmargin;
					}
					if (($rowglass['center']) > ($rowglass['height'])) {
						$m2 = round(((($rowglass['width'] + $glassX) * ($rowglass['center'] + $glassY)) * 0.000001), 2);
						$lm = round((((($rowglass['width'] + 72) + ($rowglass['center'] + 72)) * 2) * 0.001), 2);
					} else {
						$m2 = round(((($rowglass['width'] + $glassX) * ($rowglass['height'] + $glassY)) * 0.000001), 2);
						$lm = round((((($rowglass['width'] + 72) + ($rowglass['height'] + 72)) * 2) * 0.001), 2);
					}
					//$				wsvalue_glass[] = round((($materials * $lm) + ($assemble / $rowagnt['labourrate'])) , 2);
					//$				rrpvalue_glass[] = round((($materials * $lm) + ($assemble / $rowagnt['labourrate']) * $margin) , 2);
					$arrm2[] = $m2;
				}
				$row_glass['margin'] = $margin;
				$row_glass['size'] = array_sum($arrm2);
				$wsvalue = round(($row_glass['glassrate'] * array_sum($arrm2)), 2);
				$row_glass['glasswsvalue'] = $wsvalue;
				$discount = 0;
				$rprice = round((($wsvalue * ($margin / 100)) * (100 - 0)), 2);
				$row_glass['glassrprice'] = $rprice;
				$array_glass_wsvalue[] = $wsvalue;
				$array_glass_rprice[] = $rprice;
				$post[] = $row_glass;
			}
		}
		$getSelected = $db->joinquery("SELECT DISTINCT(window.selected_product) FROM window,room WHERE window.roomid=room.roomid AND window.selected_product!='HOLD' AND room.locationid=" . $passloc . "");
		while ($rowSel = mysqli_fetch_array($getSelected)) {
			if ($rowSel['selected_product'] == "EVSx2" || $rowSel['selected_product'] == "EVSx3") {
				$getProfiles = $db->joinquery("SELECT DISTINCT(paneloption_style.evsProfileTop),window.selected_product,profiles.profilecode,profiles.profilename,profiles.pricelm FROM panel,paneloption_style,room,window,profiles WHERE paneloption_style.evsProfileTop=profiles.profilecode AND panel.styleid=paneloption_style.styleid AND panel.windowid=window.windowid AND window.roomid=room.roomid AND window.selected_product='" . $rowSel['selected_product'] . "' AND room.locationid=" . $passloc . "");
				$margin = $evsmargin;
				$rowSel['margin'] = $margin;
				if (mysqli_num_rows($getProfiles) > 0) {
					while ($rowProfile = mysqli_fetch_array($getProfiles)) {
						$array_lm = array();
						$rowSel['Prfoiledesc'] = $rowProfile['profilename'];
						$rowSel['pricelm'] = $rowProfile['pricelm'];
						$rowSel['profilename'] = $rowProfile['profilecode'];
						$get_profiles = $db->joinquery("SELECT panel.styleid,panel.center,panel.width,panel.`height` FROM panel,room,window,paneloption_style WHERE panel.styleid=paneloption_style.styleid AND panel.windowid=window.windowid AND window.roomid=room.roomid AND window.selected_product!='HOLD' AND panel.styleid=paneloption_style.styleid AND paneloption_style.evsProfileTop='" . $rowProfile['evsProfileTop'] . "' AND room.locationid=" . $passloc . "");
						while ($rowprofiles = mysqli_fetch_array($get_profiles)) {
							if (($rowprofiles['center']) > ($rowprofiles['height']))
								$lm = round((((($rowprofiles['width'] + 72) + ($rowprofiles['center'] + 72)) * 2) * 0.001), 2);
							else
								$lm = round((((($rowprofiles['width'] + 72) + ($rowprofiles['height'] + 72)) * 2) * 0.001), 2);
							$array_lm[] = $lm;
						}
						$rowSel['profileSize'] = array_sum($array_lm);
						$wsvalue_profile = round(($rowProfile['pricelm'] * array_sum($array_lm)), 2);
						$rowSel['wsvalue_profile'] = $wsvalue_profile;
						$discount = 0;
						$rprice_profile = round((($wsvalue_profile * ($margin / 100)) * (100 - 0)), 2);
						$rowSel['rprice_profile'] = $rprice_profile;
						$array_profile_wsvalue[] = $wsvalue_profile;
						$array_rprice_profile[] = $rprice_profile;
						$post_profile[] = $rowSel;
					}
				}
			} else {
				$getProfiles = $db->joinquery("SELECT DISTINCT(paneloption_style.evsProfileTop),window.selected_product,profiles.profilecode,profiles.profilename,profiles.pricelm FROM panel,paneloption_style,room,window,profiles WHERE paneloption_style.evsProfileTop=profiles.profilecode AND panel.styleid=paneloption_style.styleid AND panel.windowid=window.windowid AND window.roomid=room.roomid AND window.selected_product='" . $rowSel['selected_product'] . "' AND room.locationid=" . $passloc . "");
				$margin = $igumargin;
				$rowSel['margin'] = $margin;
				if (mysqli_num_rows($getProfiles) > 0) {
					while ($rowProfile = mysqli_fetch_array($getProfiles)) {
						$array_lm = array();
						$rowSel['Prfoiledesc'] = $rowProfile['profilename'];
						$rowSel['pricelm'] = $rowProfile['pricelm'];
						$rowSel['profilename'] = $rowProfile['profilecode'];
						$get_profiles = $db->joinquery("SELECT panel.styleid,panel.center,panel.width,panel.`height` FROM panel,room,window,paneloption_style WHERE panel.styleid=paneloption_style.styleid AND panel.windowid=window.windowid AND window.roomid=room.roomid AND window.selected_product!='HOLD' AND panel.styleid=paneloption_style.styleid AND paneloption_style.evsProfileTop='" . $rowProfile['evsProfileTop'] . "' AND room.locationid=" . $passloc . "");
						while ($rowprofiles = mysqli_fetch_array($get_profiles)) {
							if (($rowprofiles['center']) > ($rowprofiles['height']))
								$lm = round((((($rowprofiles['width'] + 72) + ($rowprofiles['center'] + 72)) * 2) * 0.001), 2);
							else
								$lm = round((((($rowprofiles['width'] + 72) + ($rowprofiles['height'] + 72)) * 2) * 0.001), 2);
							$array_lm[] = $lm;
						}
						$rowSel['profileSize'] = array_sum($array_lm);
						$wsvalue_profile = $rowProfile['pricelm'] * array_sum($array_lm);
						$rowSel['wsvalue_profile'] = $wsvalue_profile;
						$discount = 0;
						$rprice_profile = ($wsvalue_profile * ($margin / 100)) * (100 - 0);
						$rowSel['rprice_profile'] = $rprice_profile;
						$array_profile_wsvalue[] = $wsvalue_profile;
						$array_rprice_profile[] = $rprice_profile;
						$post_profile[] = $rowSel;
					}
				}
			}
		}
		$gettotalPreploc = $db->joinquery("SELECT sum(`prep`) AS preptotal FROM `staffTrack` WHERE `agentid`='" . $_SESSION['agentid'] . "' AND locationid='" . $passloc . "'");
		$rowprepTotal    = mysqli_fetch_array($gettotalPreploc);
		$prepTotal       =  $rowprepTotal['preptotal'];
		$gettotalInstallloc = $db->joinquery("SELECT sum(`install`) AS installtotal FROM `staffTrack` WHERE `agentid`='" . $_SESSION['agentid'] . "' AND locationid='" . $passloc . "'");
		$rowinstallTotal    = mysqli_fetch_array($gettotalInstallloc);
		$installTotal       =  $rowinstallTotal['installtotal'];
		$gettotalMakeloc = $db->joinquery("SELECT sum(`make`) AS maketotal FROM `staffTrack` WHERE `agentid`='" . $_SESSION['agentid'] . "' AND locationid='" . $passloc . "'");
		$rowmakeTotal    = mysqli_fetch_array($gettotalMakeloc);
		$makeTotal       =  $rowmakeTotal['maketotal'];
		$gettotalExtraloc = $db->joinquery("SELECT sum(`extra`) AS extratotal FROM `staffTrack` WHERE `agentid`='" . $_SESSION['agentid'] . "' AND locationid='" . $passloc . "'");
		$rowextraTotal    = mysqli_fetch_array($gettotalExtraloc);
		$extraTotal       =  $rowextraTotal['extratotal'];
		$getTimeproduct  = $db->joinquery("SELECT DISTINCT(window.selected_product)FROM panel,room,window WHERE panel.windowid=window.windowid AND window.roomid=room.roomid AND window.selected_product!='HOLD' AND room.locationid=" . $passloc . "");
		if (mysqli_num_rows($getTimeproduct) > 0) {
			while ($row_time = mysqli_fetch_array($getTimeproduct)) {
				$installation = array();
				$manufature = array();
				$get_times = $db->joinquery("SELECT panel.styleid,panel.center,panel.width,panel.`height`,paneloption_style.styledgvalue,paneloption_style.styleevsvalue,paneloption_style.EVSassemble,paneloption_style.IGUassemble,paneloption_safety.safetyvalue,paneloption_glasstype.typevalue,paneloption_astragal.astragalvalue,paneloption_condition.conditionvalue,window.selected_product FROM panel,room,window,paneloption_condition,paneloption_style, paneloption_astragal,paneloption_glasstype,paneloption_safety WHERE panel.windowid=window.windowid AND window.roomid=room.roomid AND window.selected_product!='HOLD' AND panel.styleid=paneloption_style.styleid AND panel.safetyid=paneloption_safety.safetyid AND panel.glasstypeid=paneloption_glasstype.glasstypeid AND panel.astragalsid=paneloption_astragal.astragalsid AND panel.conditionid=paneloption_condition.conditionid AND window.selected_product='" . $row_time['selected_product'] . "' AND room.locationid=" . $passloc . " GROUP BY panel.panelid");
				while ($rowtimes = mysqli_fetch_array($get_times)) {
					#--------------------------------------------new Code Start-------------------------------------------------------------------------------------		
					if (($rowtimes['center']) > ($rowtimes['height'])) {
						$m2 = (($rowtimes['width'] + 24) * ($rowtimes['center'] + 24)) * 0.000001;
						$lm = (($rowtimes['width'] + 72) + ($rowtimes['center'] + 72)) * 0.002;
					} else {
						$m2 = (($rowtimes['width'] + 24) * ($rowtimes['height'] + 24)) * 0.000001;
						$lm = (($rowtimes['width'] + 72) + ($rowtimes['height'] + 72)) * 0.002;
					}
					if ($m2 < 0.3) # enforce minimum size
						$m2 = 0.3;
					$panEVSLabour = (($rowtimes['styleevsvalue'] + $m2) + ($rowtimes['conditionvalue'] * $lm * 0.3) + ($rowtimes['astragalvalue'] * $rowtimes['conditionvalue']));
					$panDGLabour = (($rowtimes['styledgvalue'] + $m2 + ($rowtimes['conditionvalue'] * $lm) + ($rowtimes['astragalvalue'] * 2)));
					if ($rowtimes['selected_product'] == "EVSx2" || $rowtimes['selected_product'] == "EVSx3") {
						$installation[] = round(($panEVSLabour), 2);
						$manufature[] = round(($rowtimes['EVSassemble'] + $m2), 2);
						$totalinsallhrs[] = round(($panEVSLabour), 2);
						$totalmanufacture[] = round(($rowtimes['EVSassemble'] + $m2), 2);
					} else {
						$installation[] = round(($panDGLabour), 2);
						$totalinsallhrs[] = round(($panDGLabour), 2);
						$manufature[] = round(($rowtimes['IGUassemble'] + $m2), 2);
						$totalmanufacture[] = round(($rowtimes['IGUassemble'] + $m2), 2);
					}
				}
				#--------------------------------------------new Code End-------------------------------------------------------------------------------------
				$timemargin = round($rowagnt['labourrate'] / $rowagnt['wagerate'], 2);
				$row_time['wsvalue_install'] = round(($rowagnt['wagerate'] * array_sum($installation)), 2);
				$discount = 0;
				$row_time['rprice_install'] = round((($row_time['wsvalue_install'] * ($timemargin / 100)) * (100 - 0)), 2);
				$row_time['wsvalue_manufacture'] = round(($rowagnt['wagerate'] * array_sum($manufature)), 2);
				$discount = 0;
				$row_time['rprice_manufature'] = round((($row_time['wsvalue_manufacture'] * ($timemargin / 100)) * (100 - 0)), 2);
				$row_time['timemargin'] = $timemargin;
				$row_time['installationsize'] = array_sum($installation);
				$row_time['manufacturesize'] = array_sum($manufature);
				$array_time_wsvalue_manufacture[] = $row_time['wsvalue_manufacture'];
				$array_time_rprice_manufacture[] = $row_time['rprice_manufature'];
				$array_time_wsvalue_install[] = $row_time['wsvalue_install'];
				$array_time_rprice_install[] = $row_time['rprice_install'];
				$discount = 0;
				$postTime[] = $row_time;
			}
			$post1 = array();
			$post1['insallpdt'] = 'Time';
			$post1['installtimes'] = $installTotal;
			$post1['timemargin'] = $timemargin;
			if ($installTotal > array_sum($totalinsallhrs))
				$post1['colourbk'] = '#FFC0CB';
			else
				$post1['colourbk'] = '#96faf8';
			$post1['pdt_wsvalue_install'] = round(($rowagnt['wagerate'] * $installTotal), 2);
			$discount = 0;
			$post1['pdt_rprice_install'] = round((($post1['pdt_wsvalue_install'] * ($timemargin / 100)) * (100 - 0)), 2);
			$post1['manufacturepdt'] = 'Time';
			$post1['manufacturetimes'] = $makeTotal;
			if ($makeTotal > array_sum($totalmanufacture))
				$post1['colourbk_manu'] = '#FFC0CB';
			else
				$post1['colourbk_manu'] = '#96faf8';
			$post1['pdt_wsvalue_manufacture'] = round(($rowagnt['wagerate'] * $makeTotal), 2);
			$post1['pdt_rprice_manufacture'] = round((($post1['pdt_wsvalue_manufacture'] * ($timemargin / 100)) * (100 - 0)), 2);
		}
		$extrahours = array();
		$getExtras = $db->joinquery("SELECT window.selected_product,window.windowid,products.name,products.description,products.wsvalue,products.hours,products.costvalue,products.unittag,window_extras.quantity,window_extras.cost FROM window_extras,products,room,window WHERE window.roomid=room.roomid AND window.windowid=window_extras.windowid AND window_extras.productid=products.productid AND window.selected_product!='HOLD' AND room.locationid=" . $passloc . "");
		if (mysqli_num_rows($getExtras) > 0) {
			while ($row_extras = mysqli_fetch_array($getExtras)) {
				$extramargin = round(($rowagnt['labourrate'] / $rowagnt['wagerate']), 2);
				$extrahours[] = $row_extras['hours'];
				$wsvalue_extras = $rowagnt['wagerate'] * $row_extras['hours'];
				$rprice_extras = $wsvalue_extras * ($extramargin / 100) * 100;
				$array_extras_time_wsvalue[] = $wsvalue_extras;
				$array_rprice_extras[] = $rprice_extras;
				$wsvalue_costextras = $row_extras['wsvalue'] * $row_extras['quantity'];
				//$			rprice_costextras =( $wsvalue_costextras * ($costmargin/100)) * (100 -0);
				$rprice_costextras = $wsvalue_costextras * ($costmargin / 100) * 100;
				$array_extras_cost_wsvalue[] = $wsvalue_costextras;
				$array_cost_rprice_extras[] = $rprice_costextras;
				$row_extras['extramargin'] = $extramargin;
				$row_extras['wsvalue_extras'] = $wsvalue_extras;
				$row_extras['rprice_extras'] = $rprice_extras;
				$row_extras['wsvalue_costextras'] = $wsvalue_costextras;
				$row_extras['rprice_costextras'] = $rprice_costextras;
				$postextras[] = $row_extras;
			}
		}
		$post_extras = array();
		if ($extraTotal != 0 || !empty($extraTotal)) {
			$extra_margin = round(($rowagnt['labourrate'] / $rowagnt['wagerate']), 2);
			$post_extras['extra_margin'] = $extra_margin;
			$post_extras['extrapdt'] = 'Time';
			$post_extras['extratimes'] = $extraTotal;
			if (empty($extrahours)) $extra_hour_total = 0;
			else $extra_hour_total = array_sum($extrahours);
			if ($extraTotal > $extra_hour_total)
				$post_extras['colourbk_extra'] = '#FFC0CB';
			else
				$post_extras['colourbk_extra'] = '#96faf8';
			$post_extras['pdt_wsvalue_extras'] = round(($rowagnt['wagerate'] * $extraTotal), 2);
			$post_extras['pdt_rprice_extras'] = round((($row_time['pdt_wsvalue_extras'] * ($extra_margin / 100)) * (100 - 0)), 2);
		}
		$getTravel = $db->joinquery("SELECT `number_staff`,`distance`,`return_trip`,`travel_status` FROM `location` WHERE locationid=" . $passloc . "");
		if (mysqli_num_rows($getTravel) > 0) {
			$row_travel = mysqli_fetch_array($getTravel);
			if ($row_travel['travel_status'] == 1) {
				$getPanels = $db->joinquery("SELECT window.selected_product,window.windowid FROM window,room WHERE window.selected_product!='HOLD' AND window.roomid=room.roomid AND room.locationid='" . $passloc . "'");
				while ($rowPanel = mysqli_fetch_array($getPanels)) {
					$getlabour = $db->joinquery("SELECT panelid,SUM(dglabour) AS igulabour, SUM(evslabour) AS evslabour FROM panel WHERE windowid = " . $rowPanel['windowid'] . "");
					$row_labour = mysqli_fetch_array($getlabour);
					if (($rowPanel['selected_product'] == 'EVSx2') || ($rowPanel['selected_product'] == 'EVSx3')) {
						$travelDaysEVS = ($row_labour['evslabour'] / (7 * $row_travel['number_staff']));
						$travelHoursEVS = ((($row_travel['distance'] * 2) * $row_travel['number_staff']) / 90) * $travelDaysEVS;
						$travelTimecost[] = ($travelHoursEVS * $labourrate);
						$traveldisatncecost[] = round((((($row_travel['distance'] * 2) * $travelDaysEVS) * $travelrate)), 2);
					} else {
						$travelDaysIGU = ($row_labour['igulabour'] / (5 * $row_travel['number_staff']));
						$travelHoursIGU = ((($row_travel['distance'] * 2) * $row_travel['number_staff']) / 90) * $travelDaysIGU;
						$travelTimecost[] = ($travelHoursIGU * $labourrate);
						$traveldisatncecost[] = round((((($row_travel['distance'] * 2) * $travelDaysIGU) * $travelrate)), 2);
					}
				}
				//$			travel_rPrice = round(($travelrate * $row_travel['return_trip']),2);
				$travel_rPrice = array_sum($traveldisatncecost);
				$travel_wscost = round(($travel_rPrice / $costmargin), 2);
				$timesize = round((($row_travel['return_trip'] * $row_travel['number_staff']) * 0.0166), 2);
				$timemargin = round(($rowagnt['labourrate'] / $rowagnt['wagerate']), 2);
				$time_wscost = round(($rowagnt['wagerate'] * $timesize), 2);
				//$			time_rPrice = round(($time_wscost * $timemargin),2);
				$time_rPrice = round(array_sum($travelTimecost), 2);
			} else {
				$travel_rPrice = 0;
				$travel_wscost = 0;
				$timesize = 0;
				$timemargin = round(($rowagnt['labourrate'] / $rowagnt['wagerate']), 2);
				$time_wscost = 0;
				$time_rPrice = 0;
			}
			$row_travel['timesize'] = $timesize;
			$row_travel['timemargin'] = $timemargin;
			$row_travel['travel_wscost'] = $travel_wscost;
			$row_travel['travel_rPrice'] = $travel_rPrice;
			$row_travel['time_wscost'] = $time_wscost;
			$row_travel['time_rPrice'] = $time_rPrice;
		}
		$getColor = $db->joinquery("SELECT panel.colourid FROM panel,colours,room,window WHERE panel.colourid = colours.colourid AND panel.windowid=window.windowid AND window.roomid=room.roomid AND window.selected_product!='HOLD' AND room.locationid=" . $passloc . "");
		if (mysqli_num_rows($getColor) > 0) {
			while ($rowColor = mysqli_fetch_array($getColor))
				$colourid[] =  $rowColor['colourid'];
		}
		$panelcount = array();
		$timecount = array();
		$costcount = array();
		$totalcostcount = array();
		if (count($colourid) > 0) {
			$replicated = array_count_values($colourid);
			$uniquecolor = array_unique($colourid);
			$joincolor = join(',', $uniquecolor);
			$getcolour = $db->joinquery("SELECT `colourid`,`colourname`,`colorcode` FROM colours WHERE colourid IN($joincolor)");
			if (mysqli_num_rows($getcolour) > 0) {
				while ($rowcolor = mysqli_fetch_assoc($getcolour)) {
					$rowcolor['countpanels'] = $replicated[$rowcolor['colourid']];
					$getspeific = $db->joinquery("SELECT * FROM paint_specifications WHERE locationid='" . $passloc . "' AND colourid='" . $rowcolor['colourid'] . "'");
					if (mysqli_num_rows($getspeific) > 0) {
						while ($row_paint = mysqli_fetch_array($getspeific)) {
							if ($row_paint['selected_status'] == 1) {
								$panelcount[] = $rowcolor['countpanels'];
								$timecount[] = $row_paint['times'];
								$costcount[] = $row_paint['cost'];
							}
						}
					} else {
						$panelcount = array();
						$timecount = array();
						$costcount = array();
					}
					if (count($panelcount) > 0)
						$total_panelcount = array_sum($panelcount);
					else
						$total_panelcount = 0;
					if (count($timecount) > 0)
						$total_timecount = array_sum($timecount);
					else
						$total_timecount = 0;
					if (count($costcount) > 0)
						$total_costcount = array_sum($costcount);
					else
						$total_costcount = 0;
				}
			}
			$paintprepdt = 'Time';
			$painttimes = $prepTotal;
			if ($prepTotal > $total_timecount)
				$colourbk_prep = '#FFC0CB';
			else
				$colourbk_prep = '#96faf8';
			$pdt_wsvalue_prep = round(($rowagnt['wagerate'] * $prepTotal), 2);
			$paint_timemargin = round(($rowagnt['labourrate'] / $rowagnt['wagerate']), 2);
			$pdt_rprice_prep = round(($pdt_wsvalue_prep * $paint_timemargin), 2);
			$paint_time_wscost = round(($rowagnt['wagerate'] * $total_timecount), 2);
			$paint_time_rPrice = round(($paint_time_wscost * $paint_timemargin), 2);
			$material_wsCost = $costmargin != 0 ? round(($total_costcount / $costmargin), 2) : 0;
			$rateWsval = $total_panelcount != 0 ? round(($material_wsCost / $total_panelcount), 2) : 0;
		}
		$totalwscost = round((array_sum($array_glass_wsvalue) + array_sum($array_profile_wsvalue) + array_sum($array_time_wsvalue_install) + array_sum($array_extras_time_wsvalue) + array_sum($array_extras_cost_wsvalue) + $travel_wscost + $time_wscost + $material_wsCost + $paint_time_wscost + array_sum($array_time_wsvalue_manufacture)), 2);
		$ttoalRprice = round((array_sum($array_glass_rprice) + array_sum($array_rprice_profile) + array_sum($array_time_rprice_install) + array_sum($array_rprice_extras) + array_sum($array_cost_rprice_extras) + $travel_rPrice + $time_rPrice + $paint_time_rPrice + $total_costcount + array_sum($array_time_rprice_manufacture)), 2);
		$totalmargin = $ttoalRprice - $totalwscost;
		$total_install_hours = round((array_sum($totalinsallhrs) + array_sum($extrahours) + $timesize + $total_timecount), 2);
		$ttoal_man_insatll_hrs = array_sum($totalmanufacture) + $total_install_hours;
		$hourlyreturn = $ttoal_man_insatll_hrs != 0 ? round((($totalmargin / $ttoal_man_insatll_hrs) / 1.15), 2) : 0;
		$gst = round((($ttoalRprice * 3) / 23), 2);
		$ttoal_price = round(($ttoalRprice), 2);
	}
