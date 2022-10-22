<?php

require 'datadict.php';

function create($queryString, $params) {
	global $gDataDict;
	
	$return = array('success' => true, 'errorcode' => '');
	
	try {
		# check access token 
		if(!($agentId = ValidAccessToken($params['accesstoken'])))
			throw new Exception("BadCredentials");

		# check arguments
		$customerId = 0;
		if(isset($params['data']['customerid']) && intval($params['data']['customerid']) > 0)
			$customerId = intval($params['data']['customerid']);
		if($customerId == 0)
			throw new Exception("MissingArgument");
		
		$mysqli = $params['mysqli'];
		
		# load defaults
		$querySQL = "SELECT * FROM params WHERE paramid = 1";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
		$defaults = $query->fetch_assoc();
		$query->free();
		
		# create new location
		$querySQL = "INSERT INTO location (created, agentid, customerid, quotegreeting, quotedetails) VALUES (". GetUTCTime(). ", $agentId, $customerId, '". $mysqli->real_escape_string($defaults['quotegreeting']). "', '". $mysqli->real_escape_string($defaults['quotedetails']). "')";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
		$locationId = $mysqli->insert_id;
		
		# now update new location
		$updateFields = array();
		$location_Search="";
		if(isset($params['data']['fields']) && is_array($params['data']['fields'])) {
			foreach($params['data']['fields'] as $field => $value) {
				if(array_key_exists($field, $gDataDict['location'])) {
					
						if($field=='unitnum' && !empty($value)){
						$location_Search.=$value;
					}
					
					if($field=='street' && !empty($value)){
					
					
						$destination_location.=preg_replace('/\s/', '', $value);
						$location_Search.=",".$value;
					}
				
					if($field=='suburb' && !empty($value)){
						$destination_location.=",".preg_replace('/\s/', '', $value);
						$location_Search.=",".$value;
						}
					if($field=='city' && !empty($value)){
						$destination_location.=",".preg_replace('/\s/', '', $value);
						$location_Search.=",".$value;
					}
					
					
					
					
					if($gDataDict['location'][$field] == 's')
						array_push($updateFields, "$field = '". $mysqli->real_escape_string($value). "'");
					else
						array_push($updateFields, "$field = ". $mysqli->real_escape_string($value));
				}
			}
		}
		if(!empty($location_Search)){
				array_push($updateFields, "locationSearch = '". $mysqli->real_escape_string($location_Search). "'");
		}
		if(count($updateFields) == 0)
			throw new Exception("MissingArgument");

		# build update query
		$querySQL = "UPDATE location SET ". implode(', ', $updateFields). " WHERE locationid = $locationId";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
		
		$return['data'] = array('locationid' => $locationId);
	}
	catch(Exception $e) {
		$return['success'] = false;
		$return['errorcode'] = $e->getMessage();
	}
		
	return $return;
}


function get($queryString, $params) {
	global $gDataDict;
		
	$return = array('success' => true, 'errorcode' => '');
	
	try {
		# check access token 
		if(!($agentId = ValidAccessToken($params['accesstoken'])))
			throw new Exception("BadCredentials");

		# check arguments
		$locationId = 0;
		if(isset($params['data']['locationid']) && intval($params['data']['locationid']) > 0)
			$locationId = intval($params['data']['locationid']);
		if($locationId == 0)
			throw new Exception("MissingArgument");
		
		$mysqli = $params['mysqli'];

		if(!AgentHasPermission($mysqli, $agentId, 'location', $locationId))
			throw new Exception("SecurityViolation");
		
		# check if need to recalc location first
		if($params['data']['recalc'])
			RecalcLocation($mysqli, $locationId);

		
		# location
		$querySQL = "SELECT * FROM location WHERE locationid = $locationId AND agentid = ". intval($agentId);
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);			
		$location = $query->fetch_assoc();
		$query->free();
		
		
		if($location['locationid'] == $locationId) {
			foreach(CastMysqlTable($location, 'location') as $field => $value)
				$return['data'][$field] = $value;		
		}
		else
			throw new Exception("NoRecordFound");

		
		# customer
		$querySQL = "SELECT * FROM customer WHERE customerid = ". intval($location['customerid']);
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
		$customer = $query->fetch_assoc();
		$query->free();
		$return['data']['customer'] = array();
		if($customer['customerid'] == $location['customerid'])
			$return['data']['customer'] = CastMysqlTable($customer, 'customer');


		# rooms
		$rooms = array();
		$querySQL = "SELECT * FROM room WHERE locationid = $locationId ORDER BY name";
		if(!($roomQuery = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
		while($roomRow = $roomQuery->fetch_assoc()) {
			$room = CastMysqlTable($roomRow, 'room');
			
			# windows 
			$querySQL = "SELECT w.*, wt.name, wt.windowtypeid, wt.numpanels FROM window AS w JOIN window_type AS wt ON w.windowtypeid = wt.windowtypeid WHERE roomid = ". $roomRow['roomid']. " ORDER BY w.windowid";
			if(!($windowQuery = $mysqli->query($querySQL)))
				ThrowDBException($querySQL, $mysqli->error);
			$windows = array();
			while($windowRow = $windowQuery->fetch_assoc()) {
				$window = CastMysqlTable($windowRow, 'window');
				$window['imageurl'] = $params['windowtypephotourl'].$window['windowtypeid'].".png";
				
				# window options
				$querySQL = "SELECT * FROM window_window_option AS wwo JOIN window_option AS wo ON wwo.windowoptionid = wo.windowoptionid WHERE wwo.windowid = ". intval($windowRow['windowid']). " ORDER BY name";
				if(!($windowOptionQuery = $mysqli->query($querySQL)))
					ThrowDBException($querySQL, $mysqli->error);
				$windowOptions = array();
				while($windowOptionRow = $windowOptionQuery->fetch_assoc()) {
					$windowOption = array('windowoptionid' => (int)$windowOptionRow['windowoptionid'], 'name' => $windowOptionRow['name'], 'quantity' => (int)$windowOptionRow['quantity'], 'value' => (float)$windowOptionRow['value']);
					array_push($windowOptions, $windowOption);
	
				}
				$windowOptionQuery->free();
				$window['options'] = $windowOptions;
				
				
				# window photos
				$querySQL = "SELECT * FROM window_photo WHERE windowid = ". intval($windowRow['windowid']);
				if(!($windowPhotoQuery = $mysqli->query($querySQL)))
					ThrowDBException($querySQL, $mysqli->error);
				$windowPhotos = array();
				while($windowPhoto = $windowPhotoQuery->fetch_assoc())
					array_push($windowPhotos, (int)$windowPhoto['photoid']);
				$windowPhotoQuery->free();
				$window['photos'] = $windowPhotos;
				
				
				# window panels
				$querySQL = "SELECT panelid, panelnum, width, height, center, measurement, costsdg, costmaxe, costxcle, costevsx2, costevsx3, dglabour, evslabour, safetyid, glasstypeid, styleid, conditionid, astragalsid FROM panel WHERE windowid = ". intval($windowRow['windowid']). " ORDER BY panelnum";
				if(!($windowPanelQuery = $mysqli->query($querySQL)))
					ThrowDBException($querySQL, $mysqli->error);
				$windowPanels = array();
				while($windowPanel = $windowPanelQuery->fetch_assoc()) {
					array_push($windowPanels, CastMysqlTable($windowPanel, 'panel'));
				}
				$windowPanelQuery->free();
				$window['panels'] = $windowPanels;


				array_push($windows, $window);
			}
			
			$room['windows'] = $windows;
			
			array_push($rooms, $room);
		}
		$roomQuery->free();
		$return['data']['rooms'] = $rooms;
		
		# options
		$options = array();

		$querySQL = "SELECT * FROM window_option ORDER BY isdefault DESC, name";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
		$windowOptions = array();
		while($option = $query->fetch_assoc()) {
			$option = CastMysqlTable($option, 'window_option');
			array_push($windowOptions, $option);
		}
		$query->free();
		$options['windowoptions'] = $windowOptions;
		
		$panelOptionGroups = array(
			'astragal' => array('key' => 'astragalsid', 'value' => 'astragalvalue'),
			'condition' => array('key' => 'conditionid', 'value' => 'conditionvalue'),
			'glasstype' => array('key' => 'glasstypeid', 'value' => 'typevalue'),
			'safety' => array('key' => 'safetyid', 'value' => 'safetyvalue'),
			'style' => array('key' => 'styleid')
		);
		$panelOptions = array();
		foreach($panelOptionGroups as $table => $tableData) {
			$orderBy = ($table == 'condition') ? 'conditionid' : 'name';
			$querySQL = "SELECT * FROM paneloption_$table ORDER BY isdefault DESC, $orderBy";
			if(!($query = $mysqli->query($querySQL)))
				ThrowDBException($querySQL, $mysqli->error);
			$groupOptions = array();
			while($option = $query->fetch_assoc()) {
				$option = CastMysqlTable($option, "paneloption_$table");
				array_push($groupOptions, $option);	
			}
			$query->free();

			$panelOptions[$table] = $groupOptions;
		}
		$options['paneloptions'] = $panelOptions;
		
		$return['data']['options'] = $options;
		
		
		# settings
		$querySQL = "SELECT * FROM params WHERE paramid = 1";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
		$settings = $query->fetch_assoc();
		$query->free();
		$settings = CastMysqlTable($settings, "params");
		
		$return['data']['settings'] = $settings;
	}
	catch(Exception $e) {
		$return['success'] = false;
		$return['errorcode'] = $e->getMessage();
	}
		
	return $return;
}


function set($queryString, $params) {
	global $gDataDict;
	
	$return = array('success' => true, 'errorcode' => '');
	
	try {
		# check access token 
		if(!($agentId = ValidAccessToken($params['accesstoken'])))
			throw new Exception("BadCredentials");

		# check arguments
		$locationId = 0;
		if(isset($params['data']['locationid']) && intval($params['data']['locationid']) > 0)
			$locationId = intval($params['data']['locationid']);
		if($locationId == 0)
			throw new Exception("MissingArgument");

		$mysqli = $params['mysqli'];
		
		if(!AgentHasPermission($mysqli, $agentId, 'location', $locationId))
			throw new Exception("SecurityViolation");
		
		$updateFields = array();
		$location_Search="";
		if(isset($params['data']['fields']) && is_array($params['data']['fields'])) {
			foreach($params['data']['fields'] as $field => $value) {
				if(array_key_exists($field, $gDataDict['location'])) {
					
						if($field=='unitnum' && !empty($value)){
						$location_Search.=$value;
					}
					
					if($field=='street' && !empty($value)){
					
					
						$destination_location.=preg_replace('/\s/', '', $value);
						$location_Search.=",".$value;
					}
				
					if($field=='suburb' && !empty($value)){
						$destination_location.=",".preg_replace('/\s/', '', $value);
						$location_Search.=",".$value;
						}
					if($field=='city' && !empty($value)){
						$destination_location.=",".preg_replace('/\s/', '', $value);
						$location_Search.=",".$value;
					}
					
					
					
					if($gDataDict['location'][$field] == 's')
						array_push($updateFields, "$field = '". $mysqli->real_escape_string($value). "'");
					else
						array_push($updateFields, "$field = ". $mysqli->real_escape_string($value));
				}
			}
		}
		if(!empty($location_Search)){
				array_push($updateFields, "locationSearch = '". $mysqli->real_escape_string($location_Search). "'");
		}
		if(count($updateFields) == 0)
			throw new Exception("MissingArgument");

		# build update query
		$querySQL = "UPDATE location SET ". implode(', ', $updateFields). " WHERE locationid = $locationId";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
	}
	catch(Exception $e) {
		$return['success'] = false;
		$return['errorcode'] = $e->getMessage();
	}
		
	return $return;
}


function calculate($queryString, $params) {
	global $gDataDict;
	
	$return = array('success' => true, 'errorcode' => '');
	
	try {
		# check access token 
		if(!($agentId = ValidAccessToken($params['accesstoken'])))
			throw new Exception("BadCredentials");

		# check arguments
		$locationId = $windowId = $panelId = 0;
		if(isset($params['data']['locationid']) && intval($params['data']['locationid']) > 0)
			$locationId = intval($params['data']['locationid']);
		if(isset($params['data']['windowid']) && intval($params['data']['windowid']) > 0)
			$windowId = intval($params['data']['windowid']);
		if(isset($params['data']['panelid']) && intval($params['data']['panelid']) > 0)
			$panelId = intval($params['data']['panelid']);
		if($locationId == 0 || $windowId == 0 || $panelId == 0)
			throw new Exception("MissingArgument");

		$mysqli = $params['mysqli'];
		
		if(!AgentHasPermission($mysqli, $agentId, 'location', $locationId))
			throw new Exception("SecurityViolation");

		# load constants from params
		$querySQL = "SELECT * FROM params WHERE paramid = 1";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);			
		$calcParams = $query->fetch_assoc();
		$query->free();

		$panelCosts = CalcPanel($mysqli, $panelId, $calcParams);
		$windowCosts = CalcWindow($mysqli, $windowId, $calcParams);

		$return['data'] = array(
			'roomid' => $windowCosts['roomid'],
			'window' => array(
				'windowid' => $windowId,
				'costsdg' => $windowCosts['costsdg'],
				'costmaxe' => $windowCosts['costmaxe'],
				'costxcle' => $windowCosts['costxcle'],
				'costevsx2' => $windowCosts['costevsx2'],
				'costevsx3' => $windowCosts['costevsx3'],
			),
			'panel' => array(
				'panelid' => $panelId,
				'costsdg' => $panelCosts['costsdg'],
				'costmaxe' => $panelCosts['costmaxe'],
				'costxcle' => $panelCosts['costxcle'],
				'costevsx2' => $panelCosts['costevsx2'],
				'costevsx3' => $panelCosts['costevsx3'],
				'dglabour' => $panelCosts['dglabour'],
				'evslabour' => $panelCosts['evslabour'],
			),
		);
	}
	catch(Exception $e) {
		$return['success'] = false;
		$return['errorcode'] = $e->getMessage();
	}
		
	return $return;
}


function CalcPanel($mysqli, $panelId, $calcParams) {
	$querySQL = "SELECT p.*, safety.safetyvalue, glasstype.typevalue, style.styledgvalue, style.styleevsvalue, cond.conditionvalue, astragal.astragalvalue FROM panel AS p JOIN paneloption_safety AS safety ON p.safetyid = safety.safetyid JOIN paneloption_glasstype AS glasstype ON p.glasstypeid = glasstype.glasstypeid JOIN paneloption_style AS style ON p.styleid = style.styleid JOIN paneloption_condition AS cond ON p.conditionid = cond.conditionid JOIN paneloption_astragal AS astragal ON p.astragalsid = astragal.astragalsid WHERE p.panelid = $panelId";
	if(!($query = $mysqli->query($querySQL)))
		ThrowDBException($querySQL, $mysqli->error);		
	$panel = $query->fetch_assoc();
	$query->free();

	# calc dimensions
	if($panel['center'] > $panel['height']) {
		$m2 = ($panel['width'] * $panel['center']) * 0.000001;
		$lm = ($panel['width'] + $panel['center']) * 0.002;
	}
	else {
		$m2 = ($panel['width'] * $panel['height']) * 0.000001;
		$lm = ($panel['width'] + $panel['height']) * 0.002;
	}

	if($m2 < 0.3) # enforce minimum size
		$m2 = 0.3;

	# calc labour costs
	$panDGLabour = $panel['styledgvalue'] + $m2 + ($panel['conditionvalue'] * $lm) + ($panel['astragalvalue'] * 2);
	$panEVSLabour = ($panel['styleevsvalue'] + $m2) + ($panel['conditionvalue'] * $lm * 0.3) + ($panel['astragalvalue'] * $panel['conditionvalue']);

	# calc panel costs
	$panSDG = round(((($calcParams['sdgglassrate'] + $panel['safetyvalue'] + $panel['typevalue']) * $m2) + ($calcParams['dgmaterials'] * $lm) + ($panDGLabour * $calcParams['labourrate'])) * $calcParams['agentmargin'], 2);
	$panMAXe = round(((($calcParams['maxeglassrate'] + $panel['safetyvalue'] + $panel['typevalue']) * $m2) + ($calcParams['dgmaterials'] * $lm) + ($panDGLabour * $calcParams['labourrate'])) * $calcParams['agentmargin'], 2);
	$panXCLe = round(((($calcParams['xcleglassrate'] + $panel['safetyvalue'] + $panel['typevalue']) * $m2) + ($calcParams['dgmaterials'] * $lm) + ($panDGLabour * $calcParams['labourrate'])) * $calcParams['agentmargin'], 2);
	$panEVSx2 = round(((($calcParams['evsx2glassrate'] + ($panel['safetyvalue'] / 2)) * $m2) + ($calcParams['evsmaterials'] * $lm) + ($panEVSLabour * $calcParams['labourrate'])) * $calcParams['agentmargin'], 2);
 	$panEVSx3 = round(((($calcParams['evsx3glassrate'] + ($panel['safetyvalue'] / 2)) * $m2) + ($calcParams['evsmaterials'] * $lm) + ($panEVSLabour * $calcParams['labourrate'])) * $calcParams['agentmargin'], 2);

	$querySQL = "UPDATE panel SET costsdg = $panSDG, costmaxe = $panMAXe, costxcle = $panXCLe, costevsx2 = $panEVSx2, costevsx3 = $panEVSx3, dglabour = $panDGLabour, evslabour = $panEVSLabour WHERE panelid = $panelId";
	if(!($query = $mysqli->query($querySQL)))
		ThrowDBException($querySQL, $mysqli->error);
	
	return array('costsdg' => $panSDG, 'costmaxe' => $panMAXe, 'costxcle' => $panXCLe, 'costevsx2' => $panEVSx2, 'costevsx3' => $panEVSx3, 'dglabour' => $panDGLabour, 'evslabour' => $panEVSLabour);
}


function CalcWindow($mysqli, $windowId, $calcParams) {
	# get total panel labour and style costs for this window
	$querySQL = "SELECT SUM(dglabour) AS sumdglabour, SUM(evslabour) AS sumevslabour, SUM(costsdg) AS sumcostsdg, SUM(costmaxe) AS sumcostmaxe, SUM(costxcle) AS sumcostxcle, SUM(costevsx2) AS sumcostevsx2, SUM(costevsx3) AS sumcostevsx3 FROM panel WHERE windowid = $windowId";
	error_log($querySQL);
	if(!($query = $mysqli->query($querySQL)))
		throw new Exception($mysqli->error);
	$panelTotals = $query->fetch_assoc();
	$query->free();

	# calc window style labour total
	$windowLabourSDG = $windowLabourMAXe = $windowLabourXCLe = $panelTotals['sumdglabour'];
	$windowLabourEVSx2 = $panelTotals['sumevslabour'] * 0.9;
	$windowLabourEVSx3 = $panelTotals['sumevslabour'];
	
	# calc window travel total
	$travelSDG = round(($windowLabourSDG / 12) * $calcParams['travelrate'], 2);
	$travelMAXe = round(($windowLabourMAXe / 12) * $calcParams['travelrate'], 2);
	$travelXCLe = round(($windowLabourXCLe / 12) * $calcParams['travelrate'], 2);
	$travelEVSx2 = round(($windowLabourEVSx2 / 24) * $calcParams['travelrate'], 2);
	$travelEVSx3 = round(($windowLabourEVSx3 / 24) * $calcParams['travelrate'], 2);
	
	# calc extras
	$querySQL = "SELECT roomid, SUM(quantity * value) AS extras FROM window AS w join window_window_option AS wwo ON w.windowid = wwo.windowid JOIN window_option AS wo ON wwo.windowoptionid = wo.windowoptionid WHERE w.windowid = $windowId";
	error_log($querySQL);
	if(!($query = $mysqli->query($querySQL)))
		ThrowDBException($querySQL, $mysqli->error);
	$extras = $query->fetch_assoc();
	$query->free();

	# finally calc window style costs
	$costSDG = $panelTotals['sumcostsdg'] + $extras['extras'] + $travelSDG;
	$costMAXe = $panelTotals['sumcostmaxe'] + $extras['extras'] + $travelMAXe;
	$costXCLe = $panelTotals['sumcostxcle'] + $extras['extras'] + $travelXCLe;
	$costEVSx2 = $panelTotals['sumcostevsx2'] + $extras['extras'] + $travelEVSx2;
	$costEVSx3 = $panelTotals['sumcostevsx3'] + $extras['extras'] + $travelEVSx3;
	$querySQL = "UPDATE window SET costsdg = $costSDG, costmaxe = $costMAXe, costxcle = $costXCLe, costevsx2 = $costEVSx2, costevsx3 = $costEVSx3 WHERE windowid = $windowId";
	error_log($querySQL);
	if(!($query = $mysqli->query($querySQL)))
		ThrowDBException($querySQL, $mysqli->error);
	
	return array('roomid' => $extras['roomid'], 'costsdg' => $costSDG, 'costmaxe' => $costMAXe, 'costxcle' => $costXCLe, 'costevsx2' => $costEVSx2, 'costevsx3' => $costEVSx3);
}


function RecalcLocation($mysqli, $locationId) {	
	# load constants from params
	$querySQL = "SELECT * FROM params WHERE paramid = 1";
	if(!($query = $mysqli->query($querySQL)))
		ThrowDBException($querySQL, $mysqli->error);			
	$calcParams = $query->fetch_assoc();
	$query->free();
		
	# get all panelids and windowids for this location
	$querySQL = "SELECT DISTINCT w.windowid, p.panelid FROM room AS r JOIN window AS w ON r.roomid = w.roomid JOIN panel AS p ON w.windowid = p.windowid WHERE r.locationid = $locationId";
	if(!($query = $mysqli->query($querySQL)))
		ThrowDBException($querySQL, $mysqli->error);
	$windowIds = $panelIds = array();
	while($row = $query->fetch_assoc()) {
		array_push($windowIds, $row['windowid']);
		array_push($panelIds, $row['panelid']);
	}
	$query->free();
	$windowIds = array_unique($windowIds);
	
	# recalc panels
	foreach($panelIds as $panelId)
		CalcPanel($mysqli, $panelId, $calcParams);

	# recalc windows
	foreach($windowIds as $windowId)
		CalcWindow($mysqli, $windowId, $calcParams);
}
