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
		
		
		# create location
		
		$queryMarginSQL = "INSERT INTO location_margins (locationid) VALUES ($locationId)";
		
		if(!($query = $mysqli->query($queryMarginSQL)))
		ThrowDBException($queryMarginSQL, $mysqli->error);
		
		
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
		$querySQL = "UPDATE location SET ". implode(', ', $updateFields). " WHERE locationid = $locationId";
		
		if(!($query = $mysqli->query($querySQL)))
		ThrowDBException($querySQL, $mysqli->error);
		
		
		$getlocaaddress="SELECT street,suburb,city,locationSearch FROM location WHERE locationid='$locationId'";
		
		if(!($query = $mysqli->query($getlocaaddress)))
		ThrowDBException($getlocaaddress, $mysqli->error);
		
		$destination = $query->fetch_assoc();
		
		if($destination['street']!=''){
			
			$dest_loc=preg_replace('/\s/', '', $destination['street']);
			
		}
		
		if($destination['suburb']!=''){
			
			$dest_loc.=",".preg_replace('/\s/', '', $destination['suburb']);
			
		}
		
		if($destination['city']!=''){
			
			$dest_loc.=",".preg_replace('/\s/', '', $destination['city']);
			
		}
		
		
		Updatedistance($source_address,$dest_loc,$locationId,$mysqli);
		
		Updatelatlong($destination['locationSearch'],$locationId,$mysqli);
		
		
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
		
		
		
		
		#agentrate
		
		$querySQL = "SELECT labourrate,outoftownrate,milagerate,street,suburb,city FROM agent WHERE agentid = ". intval($agentId);
		
		if(!($query = $mysqli->query($querySQL)))
		ThrowDBException($querySQL, $mysqli->error);
		
		$agentLabour = $query->fetch_assoc();
		
		$agentlabour=$agentLabour['labourrate'];
		
		$outtownnrate=$agentLabour['outoftownrate'];
		
		$milrate=$agentLabour['milagerate'];
		
		$source_address="";
		
		if(!empty($agentLabour['street']))$source_address.=preg_replace('/\s/', '', $agentLabour['street']);
		
		if(!empty($agentLabour['suburb']))$source_address.=','.preg_replace('/\s/', '', $agentLabour['suburb']);
		
		if(!empty($agentLabour['city']))$source_address.=','.preg_replace('/\s/', '', $agentLabour['city']);
		
		
		$query->free();
		
		
		
		
		$getlocaaddress="SELECT street,suburb,city FROM location WHERE locationid='$locationId'";
		
		if(!($query = $mysqli->query($getlocaaddress)))
		ThrowDBException($getlocaaddress, $mysqli->error);
		
		$destination = $query->fetch_assoc();
		
		if($destination['street']!=''){
			
			$dest_loc=preg_replace('/\s/', '', $destination['street']);
			
		}
		
		if($destination['suburb']!=''){
			
			$dest_loc.=",".preg_replace('/\s/', '', $destination['suburb']);
			
		}
		
		if($destination['city']!=''){
			
			$dest_loc.=",".preg_replace('/\s/', '', $destination['city']);
			
		}
		
		
		
		
		
		
		# location
		$querySQL = "SELECT * FROM location WHERE locationid = $locationId AND agentid = ". intval($agentId);
		
		if(!($query = $mysqli->query($querySQL)))
		ThrowDBException($querySQL, $mysqli->error);
		
		$location = $query->fetch_assoc();
		
		$distance=$location['distance'];
		
		if($location['alarmStatus']==1 && $location['booking_date'] >= date('Y-m-d H:i:s') && $location['booking_status']==1){
			
			$location['alarm_warning']=1;
			
		}
		
		else
		{
			
			$location['alarm_warning']=0;
			
		}
		
		if($distance == 0){
			
			$cal_distance=	Updatedistance($source_address,$dest_loc,$locationId,$mysqli);
			
			$location['distance']=$cal_distance;
			
		}
		
		else{
			
			$location['distance']=$distance;
			
		}
		
		if($location['quotegreeting'] || $location['quotedetails']==""){
			
			$agent_quoteSql="SELECT quotegreeting,quotedetails FROM agent WHERE agentid='".$agentId."' AND quotegreeting!=''";
			
			if(!($query_quote = $mysqli->query($agent_quoteSql)))
			throw new Exception($mysqli->error);
			
			if($query_quote->num_rows == 0){
				
				
				$query_quoteSQL="SELECT quotegreeting,quotedetails FROM params";
				
				if(!($query_quote = $mysqli->query($query_quoteSQL)))
				throw new Exception($mysqli->error);
				
				
			}
			
			$quoteDetails = $query_quote->fetch_assoc();
			
			$query_quote->close();
			
			
			$location['quotegreeting']=$quoteDetails['quotegreeting'];
			
			$location['quotedetails']=$quoteDetails['quotedetails'];
			
		}
		
		
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
		
		
		#check-marginvalue
		$queryCheck="SELECT COUNT(locationid) AS location_cnt FROM location_margins WHERE locationid=$locationId";
		
		if(!($query = $mysqli->query($queryCheck)))
		ThrowDBException($queryCheck, $mysqli->error);
		
		$countloc = $query->fetch_assoc();
		
		if($countloc['location_cnt']==0){
			
			$querySQL = "SELECT labourrate,evsmargin,igumargin,productmargin,agenttravelrate as travelrate FROM agent WHERE agentid = ". intval($agentId);
			
			if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
			
			$agent_details = $query->fetch_assoc();
			
			$querySQL = "INSERT INTO location_margins(`locationid`,`evsmargin`,`igumargin`,`productmargin`,`labourrate`,`travelrate`)VALUES($locationId,'".$agent_details['evsmargin']."','".$agent_details['igumargin']."','".$agent_details['productmargin']."','".$agent_details['labourrate']."','".$agent_details['travelrate']."')";
			
			if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
			
		}
		
		
		
		
		#locationmargin
		
		$querySQL = "SELECT * FROM location_margins WHERE locationid = ". intval($locationId);
		
		if(!($query = $mysqli->query($querySQL)))
		ThrowDBException($querySQL, $mysqli->error);
		
		$margin_details = $query->fetch_assoc();
		
		$query->free();
		
		if(empty($margin_details)){
			
			$return['data']['location_margin'] = (object)null;
			
		}
		else
		{
			
			$return['data']['location_margin'] = $margin_details;
			
		}
		
		
		
		
		#agent
		$querySQL = "SELECT labourrate,evsmargin,igumargin,productmargin,agenttravelrate as travelrate FROM agent WHERE agentid = ". intval($agentId);
		
		if(!($query = $mysqli->query($querySQL)))
		ThrowDBException($querySQL, $mysqli->error);
		
		$agent_details = $query->fetch_assoc();
		
		$query->free();
		
		if(empty($agent_details)){
			
			$return['data']['agent'] = (object)null;
			
		}
		else
		{
			
			$return['data']['agent'] = $agent_details;
			
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
				//$				querySQL = "SELECT * FROM window_window_option AS wwo JOIN window_option AS wo ON wwo.windowoptionid = wo.windowoptionid WHERE wwo.windowid = ". intval($windowRow['windowid']). " ORDER BY name";
				
				$querySQL="SELECT window_extras. *,products.name FROM window_extras,products WHERE window_extras.productid=products.productid AND window_extras.windowid=". intval($windowRow['windowid']). "";
				
				if(!($windowOptionQuery = $mysqli->query($querySQL)))
				ThrowDBException($querySQL, $mysqli->error);
				
				$windowOptions = array();
				
				while($windowOptionRow = $windowOptionQuery->fetch_assoc()) {
					
					$windowOption = array('windowoptionid' => (int)$windowOptionRow['extraid'], 'prodcutid'=>(int)$windowOptionRow['productid'],'name' => $windowOptionRow['name'], 'quantity' => $windowOptionRow['quantity'], 'value' => (float)$windowOptionRow['cost'],"status"=>true);
					
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
				
				
				#window after photos
				
				$querySQL = "SELECT * FROM window_after_photo WHERE windowid = ". intval($windowRow['windowid']);
				
				if(!($windowPhotoQueryAfter = $mysqli->query($querySQL)))
				ThrowDBException($querySQL, $mysqli->error);
				
				while($windowPhotoafter = $windowPhotoQueryAfter->fetch_assoc())
				array_push($windowPhotos, (int)$windowPhotoafter['photoid']);
				
				$windowPhotoQueryAfter->free();
				
				$window['photos'] = $windowPhotos;
				
				
				
				# window panels
				$querySQL = "SELECT p.panelid, p.panelnum, p.width, p.height, p.center,p.colourid,p.measurement, p.costsdg , p.costmaxe , p.costxcle,p.costevsx2, p.costevsx3, p.dglabour, p.evslabour, p.safetyid, p.glasstypeid, p.styleid, p.conditionid, p.astragalsid,p.frametypeid,c.colorcode FROM panel p,colours c WHERE p.colourid=c.colourid AND  p.windowid = ". intval($windowRow['windowid']). " ORDER BY panelnum";
				
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
			
			//$			settings['travelrate']=20;
			
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
		
		
		# Colorcodes
		$colors=array();
		
		$queryColor="SELECT colourid,colourname,colorcode FROM colours";
		
		if(!($query = $mysqli->query($queryColor)))
		ThrowDBException($queryColor, $mysqli->error);
		
		while($row_color=$query->fetch_assoc()){
			
			$row_color = CastMysqlTable($row_color, "colours");
			
			array_push($colors, $row_color);
			
		}
		
		
		$return['data']['Colors'] = $colors;
		
		
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
					
					
					
					/*if($field=='street' && !empty($value)){
						$destination_location.=preg_replace('/\s/', '', $value);
					}
					if($field=='suburb' && !empty($value))
					{ 
					 $destination_location.=",".preg_replace('/\s/', '', $value);
					}
					if($field=='city' && !empty($value)){
						$destination_location.=",".preg_replace('/\s/', '', $value);
					}*/
					
					
					
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
			
			/*if(!empty($source_address) && !empty($destination_location)){
		$distance_api=file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?origins='.$source_address.',&destinations='.$destination_location.'&departure_time=now&key=xxxxxxxx');
		
	$output_distance= json_decode($distance_api);
$res_distance=$output_distance->rows[0]->elements[0]->distance->text;
$exp_distance=explode(" ",$res_distance);
$distance= round($exp_distance[0]);
if($distance == 0){ $distance=10;}
$field="distance";
//$agent_travel=($distance*$agnt_rate);
 array_push($updateFields, "$field = ". $mysqli->real_escape_string($distance));
	
		}*/
			
			
			
			$querySQL = "UPDATE location SET ". implode(', ', $updateFields). " WHERE locationid = $locationId";
			
			if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
			
		}
		
		
		
		
		# update margin values
		if(count($marginFields)>0){
			
			
			
			$queryCheck="SELECT COUNT(locationid) AS location_cnt FROM location_margins WHERE locationid=$locationId";
			
			if(!($query = $mysqli->query($queryCheck)))
			ThrowDBException($queryCheck, $mysqli->error);
			
			$countloc = $query->fetch_assoc();
			
			if($countloc['location_cnt']>=1){
				
				$querySQL = "UPDATE  location_margins SET ". implode(', ', $marginFields). " WHERE locationid = $locationId";
				
				if(!($query = $mysqli->query($querySQL)))
				ThrowDBException($querySQL, $mysqli->error);
				
			}
			else
			{
				
				$querySQL = "INSERT INTO location_margins(locationid)VALUES($locationId)";
				
				if(!($query = $mysqli->query($querySQL)))
				ThrowDBException($querySQL, $mysqli->error);
				
				$querySQL = "UPDATE  location_margins SET ". implode(', ', $marginFields). " WHERE locationid = $locationId";
				
				if(!($query = $mysqli->query($querySQL)))
				ThrowDBException($querySQL, $mysqli->error);
				
				
			}
			
			
		}
		
		
		$getlocaaddress="SELECT street,suburb,city,locationSearch FROM location WHERE locationid='$locationId'";
		
		if(!($query = $mysqli->query($getlocaaddress)))
		ThrowDBException($getlocaaddress, $mysqli->error);
		
		$destination = $query->fetch_assoc();
		
		if($destination['street']!=''){
			
			$dest_loc=preg_replace('/\s/', '', $destination['street']);
			
		}
		
		if($destination['suburb']!=''){
			
			$dest_loc.=",".preg_replace('/\s/', '', $destination['suburb']);
			
		}
		
		if($destination['city']!=''){
			
			$dest_loc.=",".preg_replace('/\s/', '', $destination['city']);
			
		}
		
		
		Updatedistance($source_address,$dest_loc,$locationId,$mysqli);
		
		Updatelatlong($destination['locationSearch'],$locationId,$mysqli);
		
		
		
		
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
		
		
		$panelCosts = CalcPanel($mysqli, $panelId, $calcParams,$locationId);
		
		$windowCosts = CalcWindow($mysqli, $windowId, $calcParams,$locationId);
		
		
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



function CalcPanel($mysqli, $panelId, $calcParams,$locationId) {
	
	$querySQL = "SELECT p.*, safety.safetyvalue, glasstype.typevalue, style.styledgvalue, style.styleevsvalue,style.IGUassemble,style.EVSassemble, cond.conditionvalue, astragal.astragalvalue FROM panel AS p JOIN paneloption_safety AS safety ON p.safetyid = safety.safetyid JOIN paneloption_glasstype AS glasstype ON p.glasstypeid = glasstype.glasstypeid JOIN paneloption_style AS style ON p.styleid = style.styleid JOIN paneloption_condition AS cond ON p.conditionid = cond.conditionid JOIN paneloption_astragal AS astragal ON p.astragalsid = astragal.astragalsid WHERE p.panelid = $panelId";
	
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
	
	
	# check margin exist
	$chk_margins="SELECT locationid FROM location_margins WHERE locationid='$locationId'";
	
	if(!($query = $mysqli->query($chk_margins)))
	ThrowDBException($chk_margins, $mysqli->error);
	
	if($query->num_rows == 0){
		
		
		$insert_margin="INSERT INTO location_margins(locationid)VALUES($locationId)";
		
		if(!($query = $mysqli->query($insert_margin)))
		ThrowDBException($insert_margin, $mysqli->error);
		
		
	}
	
	
	#getmargin
	$getmargin="SELECT * FROM location_margins WHERE locationid='$locationId'";
	
	if(!($query = $mysqli->query($getmargin)))
	ThrowDBException($getmargin, $mysqli->error);
	
	
	$location_margins = $query->fetch_assoc();
	
	$query->free();
	
	
	$getagent="SELECT agentid FROM location WHERE locationid='$locationId'";
	
	if(!($query = $mysqli->query($getagent)))
	ThrowDBException($getagent, $mysqli->error);
	
	
	$location_agent = $query->fetch_assoc();
	
	$query->free();
	
	
	
	$getagent="SELECT SGUrate,IGUx2rate,IGUx3rate FROM agent WHERE agentid='".$location_agent['agentid']."'";
	
	if(!($query = $mysqli->query($getagent)))
	ThrowDBException($getagent, $mysqli->error);
	
	$agent = $query->fetch_assoc();
	
	$query->free();
	
	
	$panDGLabour = (($panel['styledgvalue'] + $m2 + ($panel['conditionvalue'] * $lm) + ($panel['astragalvalue'] * 2)));
	
	$panEVSLabour = (($panel['styleevsvalue'] + $m2) + ($panel['conditionvalue'] * $lm * 0.3) + ($panel['astragalvalue'] * $panel['conditionvalue']));
	
	
	
	//G	lass
	$SGU_Glass = ((($agent['SGUrate'] + $panel['safetyvalue'] + $panel['typevalue']) * $m2) * $location_margins['igumargin']);
	
	$IGUx2_Glass = ((($agent['IGUx2rate'] + $panel['safetyvalue'] + $panel['typevalue']) * $m2) * $location_margins['igumargin']);
	
	$IGUx3_Glass = ((($agent['IGUx3rate'] + $panel['safetyvalue'] + $panel['typevalue']) * $m2) * $location_margins['igumargin']);
	
	$EVSx2_Glass = ((($agent['SGUrate']+($panel['safetyvalue'] * 0.5))* $m2)* $location_margins['evsmargin']);
	
	$EVSx3_Glass = ((($agent['IGUx2rate']+($panel['safetyvalue'] * 0.5))* $m2)* $location_margins['evsmargin']);
	
	//M	aterials
	$SGU_Materials = ((($calcParams['dgmaterials'] * $lm) + ($panel['IGUassemble']* $lm)) *$location_margins['productmargin']);
	
	$IGUx2_Materials =((($calcParams['dgmaterials'] * $lm) + ($panel['IGUassemble']* $lm)) *$location_margins['productmargin']);
	
	$IGUx3_Materials = ((($calcParams['dgmaterials'] * $lm) + ($panel['IGUassemble']* $lm)) *$location_margins['productmargin']);
	
	$EVSx2_Materials =((($calcParams['evsmaterials'] * $lm)+($panel['EVSassemble']* $lm))*$location_margins['evsmargin']);
	
	$EVSx3_Materials = ((($calcParams['evsmaterials'] * $lm)+($panel['EVSassemble']* $lm))*$location_margins['evsmargin']);
	
	//L	abour
	$SGU_Labour = ($panDGLabour*$location_margins['labourrate']);
	
	$IGUx2_Labour =($panDGLabour*$location_margins['labourrate']);
	
	$IGUx3_Labour =($panDGLabour*$location_margins['labourrate']);
	
	$EVSx2_Labour =($panEVSLabour*$location_margins['labourrate']);
	
	$EVSx3_Labour = ($panEVSLabour*$location_margins['labourrate']);
	
	//T	otals
	$SDG_Total = round(($SGU_Glass + $SGU_Materials + $SGU_Labour),2);
	
	$IGUx2_Total = round(($IGUx2_Glass + $IGUx2_Materials + $IGUx2_Labour),2);
	
	$IGUx3_Total = round(($IGUx3_Glass + $IGUx3_Materials + $IGUx3_Labour),2);
	
	$EVSx2_Total = round(($EVSx2_Glass + $EVSx2_Materials + $EVSx2_Labour),2);
	
	$EVSx3_Total = round(($EVSx3_Glass + $EVSx3_Materials + $EVSx3_Labour),2);
	
	
	$panSDG = $SDG_Total;
	
	$panMAXe = $IGUx2_Total;
	
	$panXCLe = $IGUx3_Total;
	
	$panEVSx2 = $EVSx2_Total;
	
	$panEVSx3= $EVSx3_Total;
	
	
	$querySQL = "UPDATE panel SET costsdg = $panSDG, costmaxe = $panMAXe, costxcle = $panXCLe, costevsx2 = $panEVSx2, costevsx3 = $panEVSx3, dglabour = $panDGLabour, evslabour = $panEVSLabour , SGU_Glass = $SGU_Glass, IGUx2_Glass = $IGUx2_Glass, IGUx3_Glass = $IGUx3_Glass, EVSx2_Glass = $EVSx2_Glass, EVSx3_Glass = $EVSx3_Glass,SGU_Materials = $SGU_Materials,IGUx2_Materials = $IGUx2_Materials, IGUx3_Materials = $IGUx3_Materials, EVSx2_Materials = $EVSx2_Materials,EVSx3_Materials = $EVSx3_Materials, SGU_Labour = $SGU_Labour, IGUx2_Labour = $IGUx2_Labour, IGUx3_Labour = $IGUx3_Labour, EVSx2_Labour = $EVSx2_Labour, EVSx3_Labour = $EVSx3_Labour WHERE panelid = $panelId";
	
	if(!($query = $mysqli->query($querySQL)))
	ThrowDBException($querySQL, $mysqli->error);
	
	
	return array('costsdg' => $panSDG, 'costmaxe' => $panMAXe, 'costxcle' => $panXCLe, 'costevsx2' => $panEVSx2, 'costevsx3' => $panEVSx3, 'dglabour' => $panDGLabour, 'evslabour' => $panEVSLabour);
	
}



function CalcWindow($mysqli, $windowId, $calcParams,$locationid) {
	
	
	# get total panel labour and style costs for this window
	$querySQL = "SELECT SUM(dglabour) AS dglabour, SUM(evslabour) AS evslabour, SUM(costsdg) AS sumcostsdg, SUM(costmaxe) AS sumcostmaxe, SUM(costxcle) AS sumcostxcle, SUM(costevsx2) AS sumcostevsx2, SUM(costevsx3) AS sumcostevsx3 FROM panel WHERE windowid = $windowId";
	
	
	error_log($querySQL);
	
	if(!($query = $mysqli->query($querySQL)))
	throw new Exception($mysqli->error);
	
	$row_labour = $query->fetch_assoc();
	
	$query->free();
	
	
	# calc window travel total
	
	
	$querySQL = "SELECT agentid,travel_status,distance,number_staff FROM location WHERE locationid='$locationid'";
	
	
	error_log($querySQL);
	
	if(!($query = $mysqli->query($querySQL)))
	throw new Exception($mysqli->error);
	
	$rowtravel = $query->fetch_assoc();
	
	$query->free();
	
	
	if($rowtravel['travel_status']==0){
		
		
		
		$travelSDG=0;
		
		$travelMAXe=0;
		
		$travelXCLe=0;
		
		$travelEVSx2=0;
		
		$travelEVSx3=0;
		
		
	}
	else{
		
		$queryMargins ="SELECT labourrate,travelrate FROM location_margins WHERE locationid='$locationid'";
		
		error_log($queryMargins);
		
		if(!($query = $mysqli->query($queryMargins)))
		throw new Exception($mysqli->error);
		
		$margins = $query->fetch_assoc();
		
		$query->free();
		
		
		$labourrate=$margins['labourrate'];
		
		$travelrate=$margins['travelrate'];
		
		
		
		$travelDaysEVS=($row_labour['evslabour']/(7*$rowtravel['number_staff']));
		
		
		$travelHoursEVS = ((($rowtravel['distance']*2)*$rowtravel['number_staff'])/90)*$travelDaysEVS;
		
		$travelEVSx2 = $travelEVSx3 =round((((($rowtravel['distance']*2)*$travelDaysEVS)*$travelrate)+($travelHoursEVS*$labourrate)),2);
		
		
		$travelDaysIGU=($row_labour['dglabour']/(5*$rowtravel['number_staff']));
		
		$travelHoursIGU = ((($rowtravel['distance']*2)*$rowtravel['number_staff'])/90)*$travelDaysIGU;
		
		$travelSDG=$travelMAXe=$travelXCLe =round((((($rowtravel['distance']*2)*$travelDaysIGU)*$travelrate)+($travelHoursIGU*$labourrate)),2);
		
		
		
		
		/*	$travelDaysEVS=($row_labour['evslabour']/(7*$rowtravel['number_staff']));
						$travelHoursEVS = ((($rowtravel['distance']*2)*$rowtravel['number_staff'])/60)*$travelDaysEVS;
						$travelEVSx2 = $travelEVSx3 =round((((($rowtravel['distance']*2)*$travelDaysEVS)*$travelrate)+($travelHoursEVS*$labourrate)),2);
					
						$travelDaysIGU=(($row_labour['dglabour']/7)*($rowtravel['number_staff']));
						$travelHoursIGU = ((($rowtravel['distance']*2)*$rowtravel['number_staff'])/60)*$travelDaysIGU;
						$travelSDG=$travelMAXe=$travelXCLe =round((((($rowtravel['distance']*2)*$travelDaysIGU)*$travelrate)+($travelHoursIGU*$labourrate)),2);*/
		
	}
	
	
	
	$querySQL="SELECT sum(cost) AS total_extras FROM `window_extras` WHERE windowid=$windowId";
	
	error_log($querySQL);
	
	if(!($query = $mysqli->query($querySQL)))
	ThrowDBException($querySQL, $mysqli->error);
	
	$extras = $query->fetch_assoc();
	
	$query->free();
	
	
	# finally calc window style costs
	$costSDG = $row_labour['sumcostsdg'] + $extras['total_extras'] + $travelSDG;
	
	$costMAXe = $row_labour['sumcostmaxe'] + $extras['total_extras'] + $travelMAXe;
	
	$costXCLe = $row_labour['sumcostxcle'] + $extras['total_extras'] + $travelXCLe;
	
	$costEVSx2 = $row_labour['sumcostevsx2'] + $extras['total_extras'] + $travelEVSx2;
	
	$costEVSx3 = $row_labour['sumcostevsx3'] + $extras['total_extras'] + $travelEVSx3;
	
	$querySQL = "UPDATE window SET costsdg = $costSDG, costmaxe = $costMAXe, costxcle = $costXCLe, costevsx2 = $costEVSx2, costevsx3 = $costEVSx3 WHERE windowid = $windowId";
	
	error_log($querySQL);
	
	if(!($query = $mysqli->query($querySQL)))
	ThrowDBException($querySQL, $mysqli->error);
	
	return array('roomid' => $extras['roomid'], 'costsdg' => $costSDG, 'costmaxe' => $costMAXe, 'costxcle' => $costXCLe, 'costevsx2' => $costEVSx2, 'costevsx3' => $costEVSx3);

	
	//return array('roomid' => $extras['roomid'], 'costsdg' => $costSDG, 'costmaxe' => $costMAXe, 'costxcle' => $costXCLe, 'costevsx2' => $costEVSx2, 'costevsx3' => $costEVSx3);
	
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
	
	//i	f($calcParams['distance']){
		
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
		CalcPanel($mysqli, $panelId, $calcParams,$locationId);
		
		
		# recalc windows
		foreach($windowIds as $windowId)
		CalcWindow($mysqli, $windowId, $calcParams,$locationId);
		
	}
	
	function Updatedistance($source_address,$destination_location,$locationId,$mysqli){
		
		$distance_api=file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?origins='.$source_address.',&destinations='.$destination_location.'&departure_time=now&key=xxxxxxx');
		
		
		$output_distance= json_decode($distance_api);
		
		$res_distance=$output_distance->rows[0]->elements[0]->distance->text;
		
		$exp_distance=explode(" ",$res_distance);
		
		$distance= round($exp_distance[0]);
		
		if($distance == 0){
			$distance=10;
		}
		
		$querySQL = "UPDATE location SET distance='$distance' WHERE locationid = $locationId";
		
		if(!($query = $mysqli->query($querySQL)))
		ThrowDBException($querySQL, $mysqli->error);
		
		
		return $distance;
		
		
	}
	
	function Updatelatlong($locationsearch,$locationId,$mysqli)
	{
		
		$location=str_replace(' ', '%20', $locationsearch);
		$res=file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address='".$location."'&key=xxxxxxxxx");
		
		$result_arr=json_decode($res);
		
		$lat=$result_arr->results[0]->geometry->location->lat;
		
		$lng=$result_arr->results[0]->geometry->location->lng;
		
		$querySQL = "UPDATE location SET latitude='$lat',longitude='$lng' WHERE locationid = $locationId";
		
		if(!($query = $mysqli->query($querySQL)))
		ThrowDBException($querySQL, $mysqli->error);
		
		
		return $lat;
		
	}
	