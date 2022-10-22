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
		$locationId = 0;
		if(isset($params['data']['locationid']) && intval($params['data']['locationid']) > 0)
			$locationId = intval($params['data']['locationid']);
		if($locationId == 0)
			throw new Exception("MissingArgument");
		
		$mysqli = $params['mysqli'];

		if(!AgentHasPermission($mysqli, $agentId, 'location', $locationId))
			throw new Exception("SecurityViolation");
		
		# create new room
		$querySQL = "INSERT INTO room (locationid) VALUES ($locationId)";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
		$roomId = $mysqli->insert_id;
		
		# now update new room
		$updateFields = array();
		if(isset($params['data']['fields']) && is_array($params['data']['fields'])) {
			foreach($params['data']['fields'] as $field => $value) {
				if(array_key_exists($field, $gDataDict['room'])) {
					if($gDataDict['room'][$field] == 's')
						array_push($updateFields, "$field = '". $mysqli->real_escape_string($value). "'");
					else
						array_push($updateFields, "$field = ". $mysqli->real_escape_string($value));
				}
			}
		}
		if(count($updateFields) == 0)
			throw new Exception("MissingArgument");

		# build update query
		$querySQL = "UPDATE room SET ". implode(', ', $updateFields). " WHERE roomid = $roomId";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
		
		$return['data'] = array('roomid' => $roomId);
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
		$roomId = 0;
		if(isset($params['data']['roomid']) && intval($params['data']['roomid']) > 0)
			$roomId = intval($params['data']['roomid']);
		if($roomId == 0)
			throw new Exception("MissingArgument");
		
		$mysqli = $params['mysqli'];
		
		if(!AgentHasPermission($mysqli, $agentId, 'room', $roomId))
			throw new Exception("SecurityViolation");
		
		$updateFields = array();
		if(isset($params['data']['fields']) && is_array($params['data']['fields'])) {
			foreach($params['data']['fields'] as $field => $value) {
				if(array_key_exists($field, $gDataDict['room'])) {
					if($gDataDict['room'][$field] == 's')
						array_push($updateFields, "$field = '". $mysqli->real_escape_string($value). "'");
					else
						array_push($updateFields, "$field = ". $mysqli->real_escape_string($value));
				}
			}
		}
		if(count($updateFields) == 0)
			throw new Exception("MissingArgument");

		# build update query
		$querySQL = "UPDATE room SET ". implode(', ', $updateFields). " WHERE roomid = $roomId";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
	}
	catch(Exception $e) {
		$return['success'] = false;
		$return['errorcode'] = $e->getMessage();
	}
		
	return $return;
}


function delete($queryString, $params) {
	$return = array('success' => true, 'errorcode' => '');
	
	try {
		# check access token 
		if(!($agentId = ValidAccessToken($params['accesstoken'])))
			throw new Exception("BadCredentials");

		# check arguments
		$roomId = 0;
		if(isset($params['data']['roomid']) && intval($params['data']['roomid']) > 0)
			$roomId = intval($params['data']['roomid']);
		if($roomId == 0)
			throw new Exception("MissingArgument");
		
		$mysqli = $params['mysqli'];
		
		if(!AgentHasPermission($mysqli, $agentId, 'room', $roomId))
			throw new Exception("SecurityViolation");

		# get windows
		$windowIds = array();
		$querySQL = "SELECT windowid FROM window WHERE roomid = $roomId";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
		while($row = $query->fetch_assoc())
			array_push($windowIds, $row['windowid']);
		$query->free();

		if(count($windowIds))
			DeleteWindows($mysqli, $params['photodir'], $windowIds);
		
		# delete room
		$querySQL = "DELETE FROM room WHERE roomid = $roomId";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
	}
	catch(Exception $e) {
		$return['success'] = false;
		$return['errorcode'] = $e->getMessage();
	}
		
	return $return;
}

