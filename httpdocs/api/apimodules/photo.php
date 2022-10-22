<?php

global $hazardFields;
$hazardFields = array('overheadpower', 'siteaccess', 'vegetation', 'heightaccess', 'childrenanimals', 'traffic', 'hazmat', 'weather', 'worksite');

function create($queryString, $params) {
	global $hazardFields;
	$return = array('success' => true, 'errorcode' => '');
	
	try {
		# check access token 
		if(!($agentId = ValidAccessToken($params['accesstoken'])))
			throw new Exception("BadCredentials");

		$mysqli = $params['mysqli'];

		# check arguments
		if(!isset($params['data']['photos']) || !is_array($params['data']['photos']))
			throw new Exception("MissingArgument");

		$windowId = 0;
		if(isset($params['data']['windowid'])) {
			$windowId = intval($params['data']['windowid']);
			if(!AgentHasPermission($mysqli, $agentId, 'window', $windowId))
				throw new Exception("SecurityViolation");				
		}
		
		$locationId = 0;
		if(isset($params['data']['locationid'])) {
			$locationId = intval($params['data']['locationid']);
			if(!AgentHasPermission($mysqli, $agentId, 'location', $locationId))
				throw new Exception("SecurityViolation");
		}
		if($locationId == 0)
			throw new Exception("MissingArgument");
		
		$hazardField = null;
		if(isset($params['data']['hazardfield'])) {
			if(!in_array($params['data']['hazardfield'], $hazardFields))
				throw new Exception("MissingArgument");
			else
				$hazardField = $params['data']['hazardfield'];
		}
		
		$photoIds = array();
		foreach($params['data']['photos'] as $photo) {
			# create photo record
			$querySQL = "INSERT INTO photo (width, height) VALUES (". intval($photo['width']). ", ". intval($photo['height']).")";
			if(!($query = $mysqli->query($querySQL)))
				ThrowDBException($querySQL, $mysqli->error);
			$photoId = $mysqli->insert_id;			
			
			# create photo file
			if(!($fh = fopen($params['photodir']."/".$photoId.".jpg", 'w')))
				throw new Exception('InternalError');
			if(fwrite($fh, base64_decode($photo['jpegbase64'])) === false)
				throw new Exception('InternalError '+$params['photodir']."/".$photoId.".jpg");
			fclose($fh);
			
			if($windowId > 0) # window photo
				$querySQL = "INSERT INTO window_photo (photoid, windowid) VALUES ($photoId, $windowId)";
			elseif($hazardField != null) # hazard photo
				$querySQL = "UPDATE location SET hs_". $mysqli->real_escape_string($hazardField). "_photoid = $photoId WHERE locationid = $locationId";
			else # location photo
				$querySQL = "UPDATE location SET photoid = $photoId WHERE locationid = $locationId";

			if(!($query = $mysqli->query($querySQL)))
				ThrowDBException($querySQL, $mysqli->error);

			array_push($photoIds, $photoId);
		}
		
		$return['photoids'] = $photoIds;
	}
	catch(Exception $e) {
		$return['success'] = false;
		$return['errorcode'] = $e->getMessage();
	}
		
	return $return;
}


function delete($queryString, $params) {
	global $hazardFields;
	
	$return = array('success' => true, 'errorcode' => '');
	
	try {
		# check access token 
		if(!($agentId = ValidAccessToken($params['accesstoken'])))
			throw new Exception("BadCredentials");

		$mysqli = $params['mysqli'];

		# check arguments
		$photoId = 0;
		if(isset($params['data']['photoid']) && intval($params['data']['photoid']) > 0)
			$photoId = intval($params['data']['photoid']);
		if($photoId == 0)
			throw new Exception("MissingArgument");

		$locationId = 0;
		if(isset($params['data']['locationid']) && intval($params['data']['locationid']) > 0)
			$locationId = intval($params['data']['locationid']);
		if($locationId == 0)
			throw new Exception("MissingArgument");
		if(!AgentHasPermission($mysqli, $agentId, 'location', $locationId))
			throw new Exception("SecurityViolation");					

		$windowId = 0;
		if(isset($params['data']['windowid'])) {
			$windowId = intval($params['data']['windowid']);
			if(!AgentHasPermission($mysqli, $agentId, 'window', $windowId))
				throw new Exception("SecurityViolation");				
		}
		
		$hazardField = null;
		if(isset($params['data']['hazardfield'])) {
			if(!in_array($params['data']['hazardfield'], $hazardFields))
				throw new Exception("MissingArgument");
			else
				$hazardField = $params['data']['hazardfield'];
		}

		if($windowId) { # window photo
			DeletePhotos($mysqli, $params['photodir'], array($photoId));
		}
		elseif($hazardField != '') { # hazard photo
			$querySQL = "UPDATE location SET hs_". $mysqli->real_escape_string($hazardField). "_photoid = 0 WHERE locationid = $locationId AND hs_". $mysqli->real_escape_string($hazardField). "_photoid = $photoId";
			if(!($query = $mysqli->query($querySQL)))
				ThrowDBException($querySQL, $mysqli->error);

			DeletePhotos($mysqli, $params['photodir'], array($photoId), array());
		}
		else { # location photo
			$querySQL = "UPDATE location SET photoid = 0 WHERE locationid = $locationId";
			if(!($query = $mysqli->query($querySQL)))
				ThrowDBException($querySQL, $mysqli->error);

			DeletePhotos($mysqli, $params['photodir'], array($photoId), array());
		}
	}
	catch(Exception $e) {
		$return['success'] = false;
		$return['errorcode'] = $e->getMessage();
	}
		
	return $return;
}
