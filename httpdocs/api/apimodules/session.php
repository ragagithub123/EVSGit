<?php

function create($queryString, $params) {
	$return = array('success' => true, 'errorcode' => '');
	
	try {
		# check arguments
		if(!isset($params['data']['username']) || !isset($params['data']['password']))
			throw new Exception("MissingArgument");
		
		$mysqli = $params['mysqli'];
		
		$querySQL = "SELECT * FROM agent WHERE email = '". $mysqli->real_escape_string($params['data']['username']). "' AND enabled = 1";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
		$agent = $query->fetch_assoc();
		$query->free();
		
		if($agent['passwordhash'] != hash('sha256', $agent['passwordsalt'].$params['data']['password']))
			throw new Exception("BadCredentials");

		$accessToken = $agent['agentid'].'-'.hash('sha256', $params['sessionsecret'].$agent['agentid']);
		$return['data'] = array(
			'accesstoken' => $accessToken,
			'agentid' => $agent['agentid'],
			'firstname' => $agent['firstname'],
			'lastname' => $agent['lastname']
		);
	}
	catch(Exception $e) {
		$return['success'] = false;
		$return['errorcode'] = $e->getMessage();
	}
		
	return $return;
}
