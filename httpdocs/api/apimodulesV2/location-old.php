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
		
		#agentaddress
		$source_address="";
		$queryagent="SELECT street,suburb,city,labourrate FROM agent WHERE agentid='$agentId'";
		if(!($query = $mysqli->query($queryagent)))
			ThrowDBException($queryagent, $mysqli->error);
		$agentaddress = $query->fetch_assoc();
		$agentrate=$agentaddress['labourrate'];
		if(!empty($agentaddress['street']))$source_address.=preg_replace('/\s/', '', $agentaddress['street']);
		if(!empty($agentaddress['suburb']))$source_address.=','.preg_replace('/\s/', '', $agentaddress['suburb']);
			if(!empty($agentaddress['city']))$source_address.=','.preg_replace('/\s/', '', $agentaddress['city']);
		$query->free();
		
		# now update new location
		$updateFields = array();
		
		$destination_location="";
		if(isset($params['data']['fields']) && is_array($params['data']['fields'])) {
			foreach($params['data']['fields'] as $field => $value) {
				if(array_key_exists($field, $gDataDict['location'])) {
					
			
					if($field=='street' && !empty($value)){
					
					
						$destination_location.=preg_replace('/\s/', '', $value);
					}
				
					if($field=='suburb' && !empty($value)){
						$destination_location.=",".preg_replace('/\s/', '', $value);
						}
					if($field=='city' && !empty($value)){
						$destination_location.=",".preg_replace('/\s/', '', $value);
					}
				
					if($gDataDict['location'][$field] == 's')
						array_push($updateFields, "$field = '". $mysqli->real_escape_string($value). "'");
					else
						array_push($updateFields, "$field = ". $mysqli->real_escape_string($value));
					
				}
			}
		}
		
		if(count($updateFields) == 0)
			throw new Exception("MissingArgument");

		# build update query
		if(!empty($source_address) && !empty($destination_location)){
			$url='https://maps.googleapis.com/maps/api/distancematrix/json?origins='.$source_address.',&destinations='.$destination_location.'&departure_time=now&key=xxxxxx';
		$distance_api=file_get_contents($url);
	$output_distance= json_decode($distance_api);
$res_distance=$output_distance->rows[0]->elements[0]->distance->text;
$exp_distance=explode(" ",$res_distance);
$distance= round($exp_distance[0]);
$field="distance";
if($distance==0){$distance=10;}
//$agent_rate=($agentrate*$distance);

/*$upquerySQL = "UPDATE agent SET agenttravelrate='$agent_rate' WHERE agentid = $agentId";
		if(!($query = $mysqli->query($upquerySQL)))
			ThrowDBException($upquerySQL, $mysqli->error);*/

 array_push($updateFields, "$field = ". $mysqli->real_escape_string($distance));
		}
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
		$distance=$location['distance'];
		if($location['alarmStatus']==1 && $location['booking_date'] >= date('Y-m-d H:i:s')){
				$location['alarm_warning']=1;
			}
			else
			{
				 $location['alarm_warning']=0;
			}
		$query->free();
		#agentrate
		
		$querySQL = "SELECT labourrate,outoftownrate,milagerate FROM agent WHERE agentid = ". intval($agentId);
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);			
		$agentLabour = $query->fetch_assoc();
		$agentlabour=$agentLabour['labourrate'];
		$outtownnrate=$agentLabour['outoftownrate'];
		$milrate=$agentLabour['milagerate'];

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
			
			#agent
			$querySQL = "SELECT labourrate,evsmargin,igumargin,productmargin,milagerate as travelrate FROM agent WHERE agentid = ". intval($agentId);
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
		$agent_details = $query->fetch_assoc();
		$query->free();
		if(empty($agent_details)){
			 $return['data']['agent'] = (object)null;
		}else
		{
			 $return['data']['agent'] = $agent_details;
		}
			
			
			#locationmargin
			
			$querySQL = "SELECT * FROM location_margins WHERE locationid = ". intval($locationId);
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
		$margin_details = $query->fetch_assoc();
		$query->free();
		if(empty($margin_details)){
			$return['data']['location_margin'] = (object)null;
		}else
		{
			 $return['data']['location_margin'] = $margin_details;
		}


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
				//$querySQL = "SELECT * FROM window_window_option AS wwo JOIN window_option AS wo ON wwo.windowoptionid = wo.windowoptionid WHERE wwo.windowid = ". intval($windowRow['windowid']). " ORDER BY name";
					$querySQL="SELECT window_extras. *,products.name FROM window_extras,products WHERE window_extras.productid=products.productid AND window_extras.windowid=". intval($windowRow['windowid']). "";

				if(!($windowOptionQuery = $mysqli->query($querySQL)))
					ThrowDBException($querySQL, $mysqli->error);
				$windowOptions = array();
				while($windowOptionRow = $windowOptionQuery->fetch_assoc()) {
					$windowOption = array('windowoptionid' => (int)$windowOptionRow['extraid'], 'name' => $windowOptionRow['name'], 'quantity' => (int)$windowOptionRow['quantity'], 'value' => (float)$windowOptionRow['cost']);
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
				$querySQL = "SELECT panelid, panelnum, width, height, center, measurement, costsdg , costmaxe , costxcle, costevsx2, costevsx3, dglabour, evslabour, safetyid, glasstypeid, styleid, conditionid, astragalsid FROM panel WHERE windowid = ". intval($windowRow['windowid']). " ORDER BY panelnum";
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
				if($table == "style"){
								$option['style_imageurl'] = $params['gPanelOptionsPhotoURL'].$option['styleid'].".png";
								if($option['evsProfileTop']!="")
								$option['evs_top_image']=$params['gProfilePhotoURL'].$option['evsProfileTop'].".png";
								else
								$option['evs_top_image']="";
								if($option['evsProfileBottom']!="")
								$option['evs_bottom_image']=$params['gProfilePhotoURL'].$option['evsProfileBottom'].".png";
								else
								$option['evs_bottom_image']="";
								if($option['evsProfileLeft']!="")
								$option['evs_left_image']=$params['gProfilePhotoURL'].$option['evsProfileLeft'].".png";
								else
								$option['evs_left_image']="";
								if($option['evsProfileRight']!="")
								$option['evs_right_image']=$params['gProfilePhotoURL'].$option['evsProfileRight'].".png";
								else
								$option['evs_right_image']="";
								if($option['retroProfileTop']!="")
								$option['retro_top_image']=$params['gProfilePhotoURL'].$option['retroProfileTop'].".png";
								else
								$option['retro_top_image']="";
								if($option['retroProfileBottom']!="")
								$option['retro_bottom_image']=$params['gProfilePhotoURL'].$option['retroProfileBottom'].".png";
								else
								$option['retro_bottom_image']="";
								if($option['retroProfileLeft']!="")
								$option['retro_left_image']=$params['gProfilePhotoURL'].$option['retroProfileLeft'].".png";
								else
								$option['retro_left_image']="";
								if($option['retroProfileRight']!="")
								$option['retro_right_image']=$params['gProfilePhotoURL'].$option['retroProfileRight'].".png";
								else
								$option['retro_right_image']="";
					}
			if($table == "safety"){
				$option['safety_imageurl'] = $params['gPanelOptionsSaftyURL'].$option['safetyid'].".png";
					}
						if($table == "glasstype"){
				$option['glass_imageurl']=$params['gPanelOptionsGlassURL'].$option['glasstypeid'].".png";
					}
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
		if($settings['travelrate']){
			$settings['travelrate']=($agentlabour*$distance);
			//$settings['travelrate']=20;
		}
			$settings['outoftownrate']=$outtownnrate;
				$settings['milagerate']=$milrate;
		
	
		$query->free();
		$settings = CastMysqlTable($settings, "params");
		
		$return['data']['settings'] = $settings;
		
		#profiles
	/*	$profile_arr=array();
		$queryProfile="SELECT * FROM profiles";
		if(!($query = $mysqli->query($queryProfile)))
		ThrowDBException($queryProfile, $mysqli->error);
		while($row_profiles=$query->fetch_assoc()){
			$row_profiles = CastMysqlTable($row_profiles, "profiles");
			array_push($profile_arr, $row_profiles);
		}
		
		$return['data']['profiles'] = $profile_arr;*/
		
		
		#style categories
		$cat_arr=array();
		$queryCat="SELECT * FROM style_category";
		if(!($query = $mysqli->query($queryCat)))
		ThrowDBException($queryCat, $mysqli->error);
		while($row_cat=$query->fetch_assoc()){
			$row_cat = CastMysqlTable($row_cat, "style_category");
			array_push($cat_arr, $row_cat);
		}
		
		$return['data']['StyleCatgory'] = $cat_arr;
		
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
			
			#agentaddress
		$source_address="";
		$queryagent="SELECT street,suburb,city,labourrate FROM agent WHERE agentid='$agentId'";
		if(!($query = $mysqli->query($queryagent)))
			ThrowDBException($queryagent, $mysqli->error);
		$agentaddress = $query->fetch_assoc();
		$agnt_rate=$agentaddress['labourrate'];
		if(!empty($agentaddress['street']))$source_address.=preg_replace('/\s/', '', $agentaddress['street']);
		if(!empty($agentaddress['suburb']))$source_address.=','.preg_replace('/\s/', '', $agentaddress['suburb']);
			if(!empty($agentaddress['city']))$source_address.=','.preg_replace('/\s/', '', $agentaddress['city']);
		$query->free();
		
		$updateFields = array();
		$marginFields=array();
		$destination_location="";
		if(isset($params['data']['fields']) && is_array($params['data']['fields'])) {
				foreach($params['data']['fields'] as $field => $value) {
					
						if(array_key_exists($field, $gDataDict['location'])) {
				
					if($field=='street' && !empty($value)){
						$destination_location.=preg_replace('/\s/', '', $value);
					}
					if($field=='suburb' && !empty($value))
					{ 
					 $destination_location.=",".preg_replace('/\s/', '', $value);
					}
					if($field=='city' && !empty($value)){
						$destination_location.=",".preg_replace('/\s/', '', $value);
					}
					
					
					if($gDataDict['location'][$field] == 's')
						array_push($updateFields, "$field = '". $mysqli->real_escape_string($value). "'");
					else
						array_push($updateFields, "$field = ". $mysqli->real_escape_string($value));
				}
					
					
					  
							#margin
							
						if(array_key_exists($field, $gDataDict['location_margins'])) {
					     if($gDataDict['location_margins'][$field] == 's')
									array_push($marginFields, "$field = '". $mysqli->real_escape_string($value). "'");
								else
									array_push($marginFields, "$field = ". $mysqli->real_escape_string($value));
				}
					
				}
				
			
		}
		
		if(count($updateFields) == 0 && count($marginFields)==0)
			throw new Exception("MissingArgument");
			
			if(count($updateFields)>0){
		# build update query
		if(!empty($source_address) && !empty($destination_location)){
		
		$distance_api=file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?origins='.$source_address.',&destinations='.$destination_location.'&departure_time=now&key=xxxxxxx');
	$output_distance= json_decode($distance_api);
$res_distance=$output_distance->rows[0]->elements[0]->distance->text;
$exp_distance=explode(" ",$res_distance);
$distance= round($exp_distance[0]);
if($distance == 0){ $distance=10;}
$field="distance";
//$agent_travel=($distance*$agnt_rate);
 array_push($updateFields, "$field = ". $mysqli->real_escape_string($distance));
	
		}
			
			
		$querySQL = "UPDATE location SET ". implode(', ', $updateFields). " WHERE locationid = $locationId";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
		}
			
			
		
			# update margin values
			if(count($marginFields)>0){
			
									$queryCheck="SELECT COUNT(locationid) AS location_cnt FROM location_margins WHERE locationid=$locationId";
							if(!($query = $mysqli->query($queryCheck)))
							ThrowDBException($querySQL, $mysqli->error);
						$countloc = $query->fetch_assoc();
							if($countloc['location_cnt']>=1){
								$querySQL = "UPDATE  location_margins SET ". implode(', ', $marginFields). " WHERE locationid = $locationId";
						if(!($query = $mysqli->query($querySQL)))
							ThrowDBException($querySQL, $mysqli->error);
							}else
							{
													$querySQL = "INSERT INTO location_margins(locationid)VALUES($locationId)";
												if(!($query = $mysqli->query($querySQL)))
													ThrowDBException($querySQL, $mysqli->error);
														$querySQL = "UPDATE  location_margins SET ". implode(', ', $marginFields). " WHERE locationid = $locationId";
													if(!($query = $mysqli->query($querySQL)))
														ThrowDBException($querySQL, $mysqli->error);
				
							}
			
			}
			
			
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
		$get_travelrate="SELECT travel_rate,distance FROM location WHERE locationid='$locationId'";
		if(!($query_rate = $mysqli->query($get_travelrate)))
			ThrowDBException($get_travelrate, $mysqli->error);
			$row_rate = $query_rate->fetch_assoc();
			$travelcost=$row_rate['travel_rate']*$row_rate['distance'];
		
		$querySQL = "SELECT * FROM params WHERE paramid = 1";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);			
		$calcParams = $query->fetch_assoc();
		if($calcParams['travelrate']){
			
		$calcParams['travelrate']=$travelcost;
			 
		}
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
			
			/*'window' => array(
				'windowid' => $windowId,
				'costsgu' => $windowCosts['costsdg'],
				'costigux2' => $windowCosts['costmaxe'],
				'costigux3' => $windowCosts['costxcle'],
				'costevsx2' => $windowCosts['costevsx2'],
				'costevsx3' => $windowCosts['costevsx3'],
			),
			'panel' => array(
				'panelid' => $panelId,
				'costsgu' => $panelCosts['costsdg'],
				'costigux2' => $panelCosts['costmaxe'],
				'costigux3' => $panelCosts['costxcle'],
				'costevsx2' => $panelCosts['costevsx2'],
				'costevsx3' => $panelCosts['costevsx3'],
				'dglabour' => $panelCosts['dglabour'],
				'evslabour' => $panelCosts['evslabour'],
			),*/
			
			
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
	//return array('costsgu' => $panSDG, 'costigux2' => $panMAXe, 'costigux3' => $panXCLe, 'costevsx2' => $panEVSx2, 'costevsx3' => $panEVSx3, 'dglabour' => $panDGLabour, 'evslabour' => $panEVSLabour);
}


function CalcWindow($mysqli, $windowId, $calcParams) {
	# get total panel labour and style costs for this window
	$querySQL = "SELECT dglabour,evslabour,SUM(dglabour) AS sumdglabour, SUM(evslabour) AS sumevslabour, SUM(costsdg) AS sumcostsdg, SUM(costmaxe) AS sumcostmaxe, SUM(costxcle) AS sumcostxcle, SUM(costevsx2) AS sumcostevsx2, SUM(costevsx3) AS sumcostevsx3 FROM panel WHERE windowid = $windowId";

	error_log($querySQL);
	if(!($query = $mysqli->query($querySQL)))
		throw new Exception($mysqli->error);
	$panelTotals = $query->fetch_assoc();
	$query->free();

	# calc window style labour total
	/*$windowLabourSDG = $windowLabourMAXe = $windowLabourXCLe = $panelTotals['sumdglabour'];
	$windowLabourEVSx2 = $panelTotals['sumevslabour'] * 0.9;
	$windowLabourEVSx3 = $panelTotals['sumevslabour'];*/
	
	# calc window travel total
	 $travelDaysEVS=($panelTotals['evslabour']/7);
		$travelHoursEVS = (($calcParams['distance']*2)/60)*$travelDaysEVS;
		$travelEVSx2 =(((($calcParams['distance']*2)*$travelDaysEVS)*$calcParams['travelrate'])+($travelHoursEVS*$calcParams['labourrate']));
		$travelEVSx3 = (((($calcParams['distance']*2)*$travelDaysEVS)*$calcParams['travelrate'])+($travelHoursEVS*$calcParams['labourrate']));
		
		$travelDaysDG=($panelTotals['dglabour']/7);
  $travelHoursDG = (($calcParams['distance']*2)/60)*$travelDaysDG;
  $travelSDG =(((($calcParams['distance']*2)*$travelDaysDG)*$calcParams['travelrate'])+($travelHoursDG*$calcParams['labourrate']));
		$travelMAXe=(((($calcParams['distance']*2)*$travelDaysDG)*$calcParams['travelrate'])+($travelHoursDG*$calcParams['labourrate']));
		$travelXCLe=(((($calcParams['distance']*2)*$travelDaysDG)*$calcParams['travelrate'])+($travelHoursDG*$calcParams['labourrate']));
		
		/*$travelDaysDG=(dglabour/7)
		$travelHoursDG = ((distance*2)/60)*travelDaysDG
		$travelDG =((((distance*2)*travelDaysDG)*travel_rate)+(travelHoursDG*LabourRate))*/
	
	
	/*$travelSDG = round(($windowLabourSDG / 12) * $calcParams['travelrate'], 2);
	$travelMAXe = round(($windowLabourMAXe / 12) * $calcParams['travelrate'], 2);
	$travelXCLe = round(($windowLabourXCLe / 12) * $calcParams['travelrate'], 2);
	$travelEVSx2 = round(($windowLabourEVSx2 / 24) * $calcParams['travelrate'], 2);
	$travelEVSx3 = round(($windowLabourEVSx3 / 24) * $calcParams['travelrate'], 2);*/
	
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
	//return array('roomid' => $extras['roomid'], 'costsgu' => $costSDG, 'costigux2' => $costMAXe, 'costigux3' => $costXCLe, 'costevsx2' => $costEVSx2, 'costevsx3' => $costEVSx3);
}


function RecalcLocation($mysqli, $locationId) {	
	# load constants from params
	
	$get_travelrate="SELECT travel_rate,distance,agentid FROM location WHERE locationid='$locationId'";
		if(!($query_rate = $mysqli->query($get_travelrate)))
			ThrowDBException($get_travelrate, $mysqli->error);
			$row_rate = $query_rate->fetch_assoc();
			
			$get_agnt="SELECT labourrate FROM agent WHERE agentid='".$row_rate['agentid']."'";
		if(!($query_agnt= $mysqli->query($get_agnt)))
			ThrowDBException($query_agnt, $mysqli->error);
			$row_agnt = $query_agnt->fetch_assoc();
	
	$querySQL = "SELECT * FROM params WHERE paramid = 1";
	if(!($query = $mysqli->query($querySQL)))
		ThrowDBException($querySQL, $mysqli->error);			
	$calcParams = $query->fetch_assoc();
	if($calcParams['travelrate']){
		$calcParams['travelrate']=$row_rate['travel_rate'];
		
	}
	if($calcParams['labourrate']){
		$calcParams['labourrate']=$row_agnt['labourrate'];
		
	}
	//if($calcParams['distance']){
		$calcParams['distance']=$row_rate['distance'];
		
	//}

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
