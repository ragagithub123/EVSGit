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
		$roomId = 0;
		if(isset($params['data']['roomid']) && intval($params['data']['roomid']) > 0)
			$roomId = intval($params['data']['roomid']);
		if($roomId == 0)
			throw new Exception("MissingArgument");
		
		$mysqli = $params['mysqli'];

		if(!AgentHasPermission($mysqli, $agentId, 'room', $roomId))
			throw new Exception("SecurityViolation");
		
		# create new window
		$querySQL = "INSERT INTO window (roomid) VALUES ($roomId)";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
		$windowId = $mysqli->insert_id;
		
		# now update new window
		$updateFields = array();
		if(isset($params['data']['fields']) && is_array($params['data']['fields'])) {
			foreach($params['data']['fields'] as $field => $value) {
				if(array_key_exists($field, $gDataDict['window'])) {
					if($gDataDict['window'][$field] == 's')
						array_push($updateFields, "$field = '". $mysqli->real_escape_string($value). "'");
					else
						array_push($updateFields, "$field = ". $mysqli->real_escape_string($value));
				}
			}
		}
		if(count($updateFields) == 0)
			throw new Exception("MissingArgument");

		# build update query
		$querySQL = "UPDATE window SET ". implode(', ', $updateFields). " WHERE windowid = $windowId";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);

		# create window panels
		$querySQL = "SELECT * FROM window_type WHERE windowtypeid = ". intval($params['data']['fields']['windowtypeid']);
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
		$windowType = $query->fetch_assoc();
		for($i=1; $i<=$windowType['numpanels']; $i++) {
			$querySQL = "INSERT INTO panel (windowid, panelnum) VALUES ($windowId, $i)";
			if(!($query = $mysqli->query($querySQL)))
				ThrowDBException($querySQL, $mysqli->error);			
		}
		
		$return['data'] = array('windowid' => $windowId);
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
		$windowId = 0;
		if(isset($params['data']['windowid']) && intval($params['data']['windowid']) > 0)
			$windowId = intval($params['data']['windowid']);
		if($windowId == 0)
			throw new Exception("MissingArgument");
		
		$mysqli = $params['mysqli'];
		
		if(!AgentHasPermission($mysqli, $agentId, 'window', $windowId))
			throw new Exception("SecurityViolation");
		
		$updateFields = array();
		if(isset($params['data']['fields']) && is_array($params['data']['fields'])) {
			foreach($params['data']['fields'] as $field => $value) {
				if(array_key_exists($field, $gDataDict['window'])) {
					if($gDataDict['window'][$field] == 's')
						array_push($updateFields, "$field = '". $mysqli->real_escape_string($value). "'");
					else
						array_push($updateFields, "$field = ". $mysqli->real_escape_string($value));
				}
			}
		}
		if(count($updateFields) == 0)
			throw new Exception("MissingArgument");

		# build update query
		$querySQL = "UPDATE window SET ". implode(', ', $updateFields). " WHERE windowid = $windowId";
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
		$windowId = 0;
		if(isset($params['data']['windowid']) && intval($params['data']['windowid']) > 0)
			$windowId = intval($params['data']['windowid']);
		if($windowId == 0)
			throw new Exception("MissingArgument");
		
		$mysqli = $params['mysqli'];
		
		if(!AgentHasPermission($mysqli, $agentId, 'window', $windowId))
			throw new Exception("SecurityViolation");

		DeleteWindows($mysqli, $params['photodir'], array($windowId));
	}
	catch(Exception $e) {
		$return['success'] = false;
		$return['errorcode'] = $e->getMessage();
	}
		
	return $return;
}


function copywindow($queryString, $params){

	
	
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

			
		
		# copy new window

		$querySQL = "SELECT * FROM window WHERE windowid = ". intval($params['data']['fields']['windowid']);

		

		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);

			$windowdetails = $query->fetch_assoc();

					
			$querySQL= "INSERT INTO `window`(`roomid`,`windowtypeid`, `notes`, `extras`, `costSGU`, `costIGUX2`, `costIGUX3`, `costsdg`, `costmaxe`, `costxcle`, `costevsx2`, `costevsx3`, `status`, `selected_product`, `selected_price`, `selected_hours`, `materialCategory`)  
			
			VALUES ($roomId,'".$windowdetails['windowtypeid']."','".$windowdetails['notes']."','".$windowdetails['extras']."','".$windowdetails['costSGU']."','".$windowdetails['costIGUX2']."','".$windowdetails['costIGUX3']."'
			,'".$windowdetails['costsdg']."','".$windowdetails['costmaxe']."','".$windowdetails['costxcle']."','".$windowdetails['costevsx2']."','".$windowdetails['costevsx3']."','".$windowdetails['status']."'
			,'".$windowdetails['selected_product']."','".$windowdetails['selected_price']."','".$windowdetails['selected_hours']."','".$windowdetails['materialCategory']."')";

	
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
		
		 	$windowId = $mysqli->insert_id;
		

		
		# now create panel

		$querySQL = "SELECT * FROM panel WHERE windowid = ". intval($params['data']['fields']['windowid']);

		
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
		
			while($paneldetails = $query->fetch_assoc()){

			
            $querySQL="INSERT INTO panel(`windowid`,`panelnum`, `width`, `height`, `center`, `measurement`, `safetyid`, `colourid`, `glasstypeid`, `styleid`, `conditionid`, `astragalsid`, `frametypeid`, `costsdg`, `costmaxe`, `costxcle`, `costevsx2`, `costevsx3`, `costSGU`, `costIGUX2`, `costIGUX3`, `igulabour`, `dglabour`, `evslabour`, `SGU_Glass`, `IGUx2_Glass`, `IGUx3_Glass`, `EVSx2_Glass`, `EVSx3_Glass`, `SGU_Materials`, `IGUx2_Materials`, `IGUx3_Materials`, `EVSx2_Materials`, `EVSx3_Materials`, `SGU_Labour`, `IGUx2_Labour`, `IGUx3_Labour`, `EVSx2_Labour`, `EVSx3_Labour`, `profileid`)
			 values($windowId,'".$paneldetails['panelnum']."','".$paneldetails['width']."','".$paneldetails['height']."','".$paneldetails['center']."'
			 ,'".$paneldetails['measurement']."','".$paneldetails['safetyid']."','".$paneldetails['colourid']."','".$paneldetails['glasstypeid']."','".$paneldetails['styleid']."'
			 ,'".$paneldetails['conditionid']."','".$paneldetails['astragalsid']."','".$paneldetails['frametypeid']."','".$paneldetails['costsdg']."','".$paneldetails['costmaxe']."'
			 ,'".$paneldetails['costxcle']."','".$paneldetails['costevsx2']."','".$paneldetails['costevsx3']."','".$paneldetails['costSGU']."'
			 ,'".$paneldetails['costIGUX2']."','".$paneldetails['costIGUX3']."','".$paneldetails['igulabour']."','".$paneldetails['dglabour']."','".$paneldetails['evslabour']."'
			 ,'".$paneldetails['SGU_Glass']."','".$paneldetails['IGUx2_Glass']."','".$paneldetails['IGUx3_Glass']."','".$paneldetails['EVSx2_Glass']."','".$paneldetails['EVSx3_Glass']."'
			 ,'".$paneldetails['SGU_Materials']."','".$paneldetails['IGUx2_Materials']."','".$paneldetails['IGUx3_Materials']."','".$paneldetails['EVSx2_Materials']."','".$paneldetails['EVSx3_Materials']."'
			 ,'".$paneldetails['SGU_Labour']."','".$paneldetails['IGUx2_Labour']."','".$paneldetails['IGUx3_Labour']."','".$paneldetails['EVSx2_Labour']."','".$paneldetails['EVSx3_Labour']."','".$paneldetails['profileid']."')";

			$mysqli->query($querySQL);

		}
		
		
		
		
		$return['data'] = array('windowid' => $windowId);
	}
	catch(Exception $e) {
		$return['success'] = false;
		$return['errorcode'] = $e->getMessage();
	}
		
	return $return;

}
