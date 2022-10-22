<?php

require 'datadict.php';

function get($queryString, $params) {
	$return = array('success' => true, 'errorcode' => '');
	
	try {
		# check access token 
		if(!($agentId = ValidAccessToken($params['accesstoken'])))
			throw new Exception("BadCredentials");
		
		$mysqli = $params['mysqli'];
		
		# statuses
		$querySQL = "SELECT * FROM location_status ORDER BY locationstatusid";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
		$locationStatuses = array();
		while($status = $query->fetch_assoc())
			array_push($locationStatuses, CastMysqlTable($status, 'location_status'));
		$query->free();
		
		# locations
		$querySQL = "SELECT l.*, ls.status FROM location AS l JOIN location_status AS ls ON l.locationstatusid = ls.locationstatusid WHERE agentid = $agentId ORDER BY locationid DESC";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
		$locations = array();
		while($location = $query->fetch_assoc()) {
			$location = CastMysqlTable($location, 'location');
			$sendLocation = array();
			foreach(array('locationid', 'unitnum', 'street', 'suburb', 'city', 'quotelocked', 'locationstatusid', 'status','photoid') as $field)
				$sendLocation[$field] = $location[$field];
			array_push($locations, $sendLocation);
		}
		$query->free();		
		
		$return['data'] = array('locations' => $locations, 'statuslist' => $locationStatuses);
	}
	catch(Exception $e) {
		$return['success'] = false;
		$return['errorcode'] = $e->getMessage();
	}
		
	return $return;
}