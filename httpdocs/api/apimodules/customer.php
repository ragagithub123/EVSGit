<?php

require 'datadict.php';

function create($queryString, $params) {
	global $gDataDict;
	
	$return = array('success' => true, 'errorcode' => '');
	
	try {
		# check access token 
		if(!($agentId = ValidAccessToken($params['accesstoken'])))
			throw new Exception("BadCredentials");
	
		$mysqli = $params['mysqli'];
		
		# create new customer
		$querySQL = "INSERT INTO customer (created, agentid) VALUES (". GetUTCTime(). ", $agentId)";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
		$customerId = $mysqli->insert_id;
		
		# now update new customer
		$updateFields = array();
		if(isset($params['data']['fields']) && is_array($params['data']['fields'])) {
			foreach($params['data']['fields'] as $field => $value) {
				if(array_key_exists($field, $gDataDict['customer'])) {
					if($gDataDict['customer'][$field] == 's')
						array_push($updateFields, "$field = '". $mysqli->real_escape_string($value). "'");
					else
						array_push($updateFields, "$field = ". $mysqli->real_escape_string($value));
				}
			}
		}
		if(count($updateFields) == 0)
			throw new Exception("MissingArgument");

		# build update query
		$querySQL = "UPDATE customer SET ". implode(', ', $updateFields). " WHERE customerid = $customerId";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
		
		$return['data'] = array('customerid' => $customerId);
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
		if(isset($params['data']['customerid']) && intval($params['data']['customerid']) > 0)
			$customerId = intval($params['data']['customerid']);
		if($customerId == 0)
			throw new Exception("MissingArgument");
		
		$mysqli = $params['mysqli'];

		if(!AgentHasPermission($mysqli, $agentId, 'customer', $customerId))
			throw new Exception("SecurityViolation");
		
		$updateFields = array();
		if(isset($params['data']['fields']) && is_array($params['data']['fields'])) {
			foreach($params['data']['fields'] as $field => $value) {
				if(array_key_exists($field, $gDataDict['customer'])) {
					if($gDataDict['customer'][$field] == 's')
						array_push($updateFields, "$field = '". $mysqli->real_escape_string($value). "'");
					else
						array_push($updateFields, "$field = ". $mysqli->real_escape_string($value));
				}
			}
		}
		if(count($updateFields) == 0)
			throw new Exception("MissingArgument");

		# build update query
		$querySQL = "UPDATE customer SET ". implode(', ', $updateFields). " WHERE customerid = $customerId";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
	}
	catch(Exception $e) {
		$return['success'] = false;
		$return['errorcode'] = $e->getMessage();
	}
		
	return $return;
}

