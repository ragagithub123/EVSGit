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
		$windowId = 0;
		if(isset($params['data']['windowid']) && intval($params['data']['windowid']) > 0)
			$windowId = intval($params['data']['windowid']);
		if($windowId == 0)
			throw new Exception("MissingArgument");
			
		$mysqli = $params['mysqli'];
		
		if(!AgentHasPermission($mysqli, $agentId, 'window', $windowId))
			throw new Exception("SecurityViolation");		
		
		# create new panel
		$querySQL = "INSERT INTO panel (windowid) VALUES ($windowId)";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
		$panelId = $mysqli->insert_id;
		
		# now update new panel
		$updateFields = array();
		if(isset($params['data']['fields']) && is_array($params['data']['fields'])) {
			foreach($params['data']['fields'] as $field => $value) {
			
				
				if(array_key_exists($field, $gDataDict['panel'])) {
					if($gDataDict['panel'][$field] == 's')
						array_push($updateFields, "$field = '". $mysqli->real_escape_string($value). "'");
					else
						array_push($updateFields, "$field = ". $mysqli->real_escape_string($value));
				}
			}
		}
		
		if(count($updateFields) == 0)
			throw new Exception("MissingArgument");

		# build update query
		$querySQL = "UPDATE panel SET ". implode(', ', $updateFields). " WHERE panelid = $panelId";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
		
		$return['data'] = array('panelid' => $panelId);
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
		$panelId = 0;
		if(isset($params['data']['panelid']) && intval($params['data']['panelid']) > 0)
			$panelId = intval($params['data']['panelid']);
		if($panelId == 0)
			throw new Exception("MissingArgument");
		
		$mysqli = $params['mysqli'];

		if(!AgentHasPermission($mysqli, $agentId, 'panel', $panelId))
			throw new Exception("SecurityViolation");		
		
		$updateFields = array();
		if(isset($params['data']['fields']) && is_array($params['data']['fields'])) {
			foreach($params['data']['fields'] as $field => $value) {
				if(array_key_exists($field, $gDataDict['panel'])) {
						
					
					if($gDataDict['panel'][$field] == 's')
						array_push($updateFields, "$field = '". $mysqli->real_escape_string($value). "'");
					else
						array_push($updateFields, "$field = ". $mysqli->real_escape_string($value));
				}
			}
		}
		
		if(count($updateFields) == 0)
			throw new Exception("MissingArgument");

		# build update query
		$querySQL = "UPDATE panel SET ". implode(', ', $updateFields). " WHERE panelid = $panelId";
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
		$panelId = 0;
		if(isset($params['data']['panelid']) && intval($params['data']['panelid']) > 0)
			$panelId = intval($params['data']['panelid']);
		if($panelId == 0)
			throw new Exception("MissingArgument");
		
		$mysqli = $params['mysqli'];

		if(!AgentHasPermission($mysqli, $agentId, 'panel', $panelId))
			throw new Exception("SecurityViolation");		
		
		# delete panel
		DeletePanels($mysqli, array($panelId));
	}
	catch(Exception $e) {
		$return['success'] = false;
		$return['errorcode'] = $e->getMessage();
	}
		
	return $return;
}


