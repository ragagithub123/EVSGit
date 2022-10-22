<?php

require 'datadict.php';

function set($queryString, $params) {
	global $gDataDict;
	
	$return = array('success' => true, 'errorcode' => '');
	
	try {
		# check access token 
		if(!($agentId = ValidAccessToken($params['accesstoken'])))
			throw new Exception("BadCredentials");

		# check arguments
		$windowId = 0;
		if(isset($params['data']['windowid']) && intval($params['data']['windowid']) > 0)
			$windowId = intval($params['data']['windowid']);
		if($windowId == 0)
			throw new Exception("MissingArgument");
		
		$mysqli = $params['mysqli'];
		
		if(!AgentHasPermission($mysqli, $agentId, 'window', $windowId))
			throw new Exception("SecurityViolation");
		
		# delete all options for this window
		$querySQL = "DELETE FROM window_extras WHERE windowid = $windowId";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
			
		# reinsert all options (if any)
		if(isset($params['data']['options']) && is_array($params['data']['options'])) {
			$options = array();
			foreach($params['data']['options'] as $option) {
				array_push($options, '('. $windowId. ','. $option['windowoptionid']. ','. $option['quantity']. ','. $option['cost']. ')');
			}
			$querySQL = "INSERT INTO window_extras (windowid, productid, quantity,cost) VALUES ". implode(',', $options);
			if(!($query = $mysqli->query($querySQL)))
				ThrowDBException($querySQL, $mysqli->error);
		}
	}
	catch(Exception $e) {
		$return['success'] = false;
		$return['errorcode'] = $e->getMessage();
	}
		
	return $return;
}

function delete($queryString, $params) {
	global $gDataDict;
	
	$return = array('success' => true, 'errorcode' => '');
	
	try {
		# check access token 
		if(!($agentId = ValidAccessToken($params['accesstoken'])))
			throw new Exception("BadCredentials");

		# check arguments
		$windowId = 0;
		if(isset($params['data']['windowoptionid']) && intval($params['data']['windowoptionid']) > 0)
			$windowoptionid = intval($params['data']['windowoptionid']);
		if($windowoptionid == 0)
			throw new Exception("MissingArgument");
		
		$mysqli = $params['mysqli'];
		# get windowid
		$querySQL = "SELECT windowid FROM window_extras WHERE extraid = $windowoptionid";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
			
			$extras = $query->fetch_assoc();
		
		if(!AgentHasPermission($mysqli, $agentId, 'window', $extras['windowid']))
			throw new Exception("SecurityViolation");
		
		# delete all options for this window
		$querySQL = "DELETE FROM window_extras WHERE extraid = $windowoptionid";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
	
	}
	catch(Exception $e) {
		$return['success'] = false;
		$return['errorcode'] = $e->getMessage();
	}
		
	return $return;
}

function edit($queryString, $params) {
	global $gDataDict;
	
	$return = array('success' => true, 'errorcode' => '');
	
	try {
		# check access token 
		if(!($agentId = ValidAccessToken($params['accesstoken'])))
			throw new Exception("BadCredentials");

		# check arguments
		$windowId = 0;
		if(isset($params['data']['windowoptionid']) && intval($params['data']['windowoptionid']) > 0)
			$windowoptionid = intval($params['data']['windowoptionid']);
		if($windowoptionid == 0)
			throw new Exception("MissingArgument");
		
		$mysqli = $params['mysqli'];
		# get windowid
		$querySQL = "SELECT windowid FROM window_extras WHERE extraid = $windowoptionid";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
			
			$extras = $query->fetch_assoc();
		
		if(!AgentHasPermission($mysqli, $agentId, 'window', $extras['windowid']))
			throw new Exception("SecurityViolation");
		
		# delete all options for this window
		$querySQL = "UPDATE window_extras SET quantity=".$params['data']['quantity'].",cost=".$params['data']['cost']." WHERE extraid = $windowoptionid";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
	
	}
	catch(Exception $e) {
		$return['success'] = false;
		$return['errorcode'] = $e->getMessage();
	}
		
	return $return;
}

