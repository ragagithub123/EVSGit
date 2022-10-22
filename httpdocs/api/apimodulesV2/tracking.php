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
		
		
		
		if(isset($params['data']['agentid']) && intval($params['data']['agentid']) > 0)
		$agentid = intval($params['data']['agentid']);
		
		
		if($agentid == 0)
		throw new Exception("MissingArgument");
		
		
		
		if(isset($params['data']['staffname']))
		$staffname = $params['data']['staffname'];
		
		
		if(empty($staffname))
		throw new Exception("MissingArgument");
		
		
		
		$mysqli = $params['mysqli'];
		
		
		if(!AgentHasPermission($mysqli, $agentId, 'location', $locationId))
		throw new Exception("SecurityViolation");
		
		
		# create new track
		if(isset($params['data']['trackingid'])){
			
			
			
			$trackingid=$params['data']['trackingid'];
			
			
			}
		
		
		else{
			
			
			$querySQL = "INSERT INTO TrakingWork (locationid,agentid) VALUES ($locationId,$agentid)";
			
			
			if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
			
			$trackingid = $mysqli->insert_id;

		}
		if(isset($params['data']['staffid'])){
			
				$staffid = $params['data']['staffid'];
			
		}
		else{
			
			$colorarr= array('#36391e','#16c07b','#afeb42','#6b4bab','#a513dd','#fbafc6','#4396b5','#4fdfaa','#fb6237','#9608f5','#b97778','#8a940b','#cc36ae','#fcaf0a','#7ca3ed','#4630fd','#b003e3','#32be4b','#9d779e','#cae2bc' );
			
			
			$date=date('Y-m-d');
			
			
			$querySQL = "SELECT color FROM staffs WHERE locationid='$locationId' AND DATE(created_at)='$date'";
			
			
			if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
			
			
			if($query->num_rows == 0){
				
				
				$colorname=$colorarr[0];
				
				
			}
			
			
			else{
				
				
				while($colrRow = $query->fetch_assoc()) {
					
					
					$color[]=$colrRow['color'];
					
					
				}
				
				
				$result_color_arr = array_diff($colorarr, $color);
				
				
				$colorname=array_pop($result_color_arr);
				
				
			}
			
			
			$querySQL = "INSERT INTO staffs (locationid,staffname,color) VALUES ($locationId,'$staffname','$colorname')";
			
			
			if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
			
			
			$staffid = $mysqli->insert_id;
			
			
			
		}
		
		# now update new panel
		$updateFields = array();
		
		
		if(isset($params['data']['fields']) && is_array($params['data']['fields'])) {
			
			
			foreach($params['data']['fields'] as $field => $value) {
				
				
				if(array_key_exists($field, $gDataDict['TrakingWork'])) {
					
							if($field == 'activity'){
								
										if($value == 1 ){
													
													$flag=1;
										}
										else{
												
												$flag =0;
										}
						
							}
								
								
						if($gDataDict['TrakingWork'][$field] == 's')
					
			
			 array_push($updateFields, "$field = '". $mysqli->real_escape_string($value). "'");
					
					else
					array_push($updateFields, "$field = ". $mysqli->real_escape_string($value));
					
					
				}
				
				
			}
			
			
			array_push($updateFields, "staffname = '". $staffname."'");
			
			array_push($updateFields, "staffid = ". $staffid);
		}
		
		
		if(count($updateFields) == 0)
		throw new Exception("MissingArgument");
	
		# build update query
		$querySQL = "UPDATE TrakingWork SET ". implode(', ', $updateFields). " WHERE trackingid = $trackingid";
		
		if(!($query = $mysqli->query($querySQL)))
		ThrowDBException($querySQL, $mysqli->error);
		
	 if($flag == 0 )
		{
			 $trackingid ="";
				
				$staffid="";
		}
	
		$return['data'] = array('trackingid' => $trackingid,'staffid'=>$staffid);
		
		
	}
	
	
	catch(Exception $e) {
		
		
		$return['success'] = false;
		
		
		$return['errorcode'] = $e->getMessage();
		
		
	}
	
	
	
	return $return;
	
	
}




