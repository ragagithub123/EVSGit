<?php

require 'datadict.php';

function get($queryString, $params) {
	$return = array('success' => true, 'errorcode' => '');
	
	try {
		# check access token 
		if(!($agentId = ValidAccessToken($params['accesstoken'])))
			throw new Exception("BadCredentials");
		
		$mysqli = $params['mysqli'];
		$querySQL = "SELECT * FROM window_type ORDER BY name";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
		$windowTypes = array();
		while($windowType = $query->fetch_assoc()) {
			$tempWindowType = CastMysqlTable($windowType, 'windowtype');
			$tempWindowType['imageurl'] = $params['windowtypephotourl'].$windowType['windowtypeid'].".png";
			array_push($windowTypes, $tempWindowType);
		}
		$query->free();		
		
		$return['data'] = $windowTypes;
	}
	catch(Exception $e) {
		$return['success'] = false;
		$return['errorcode'] = $e->getMessage();
	}
		
	return $return;
}