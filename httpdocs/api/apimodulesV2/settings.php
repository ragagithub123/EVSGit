<?php
require 'datadict.php';
	global $gDataDict;

function get($queryString, $params) {
	global $gDataDict;
		
	$return = array('success' => true, 'errorcode' => '');
	
	try {
		# check access token 
		if(!($agentId = ValidAccessToken($params['accesstoken'])))
			throw new Exception("BadCredentials");
    $mysqli = $params['mysqli'];
			$queryagent="SELECT * FROM agent WHERE agentid='$agentId'";
			
		if(!($query = $mysqli->query($queryagent)))
			ThrowDBException($queryagent, $mysqli->error);
		$agentaddress = $query->fetch_assoc();
		 if(file_exists($params['AgentDir'].$agentId.".png"))
          {
											  $agentaddress['brand_image']=$params['Agenturl'].$agentId.".png";
          }
          else
          {
             $agentaddress['brand_image']="";
          }
		$return['data'] = $agentaddress;
		
		
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
		if(isset($params['data']['password']) && $params['data']['password']!=''){
			$mysqli = $params['mysqli'];
			$queryagent="SELECT passwordhash,passwordsalt FROM agent WHERE agentid='$agentId'";
			if(!($query = $mysqli->query($queryagent)))
			ThrowDBException($queryagent, $mysqli->error);
		$row = $query->fetch_assoc();
			if($row['passwordhash'] != hash('sha256', $row['passwordsalt'].$params['data']['password'])){
				throw new Exception("Invalid Password");
			}
			else{
				    $updateFields=array();
				  if(isset($params['data']['fields']) && is_array($params['data']['fields'])) {
									foreach($params['data']['fields'] as $field => $value) {
										 if(array_key_exists($field, $gDataDict['agent'])) {
												
												 if($gDataDict['agent'][$field] == 's')
												
						       array_push($updateFields, "$field = '". $mysqli->real_escape_string($value). "'");
					        else
													 
						       array_push($updateFields, "$field = ". $mysqli->real_escape_string($value));
												
											}
									}
						}
						
							     if(count($updateFields) == 0)
			         throw new Exception("MissingArgument");
												# build update query
												$querySQL = "UPDATE agent SET ". implode(', ', $updateFields). " WHERE agentid = $agentId";
										  if(!($query = $mysqli->query($querySQL)))
											ThrowDBException($querySQL, $mysqli->error);	
												
												  
				 
			}
			
		}
		else{
			 			throw new Exception("MissingArgument");

		}
		
			
			    

		
		
	}
	catch(Exception $e) {
		$return['success'] = false;
		$return['errorcode'] = $e->getMessage();
	}
		
	return $return;
}
