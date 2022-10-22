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
		$querySQL = "DELETE FROM window_window_option WHERE windowid = $windowId";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
			
		# reinsert all options (if any)
		if(isset($params['data']['options']) && is_array($params['data']['options'])) {
			$options = array();
			foreach($params['data']['options'] as $option) {
				array_push($options, '('. $windowId. ','. $option['windowoptionid']. ','. $option['quantity']. ')');
			}
			
			$querySQL = "INSERT INTO window_window_option (windowid, windowoptionid, quantity) VALUES ". implode(',', $options);
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

