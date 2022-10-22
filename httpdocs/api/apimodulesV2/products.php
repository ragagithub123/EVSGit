<?php

require 'datadict.php';



function get($queryString, $params) {
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
		
		$mysqli = $params['mysqli'];
		
		if(!AgentHasPermission($mysqli, $agentId, 'location', $locationId))
			throw new Exception("SecurityViolation");
		

			
		# prodcuts
		$options = array();
  $flag=0;
		$querySQL = "SELECT * FROM products WHERE agentid='$agentId' ORDER BY `name` ASC";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
				if($query->num_rows == 0){
					$flag=1;
					
						$querySQL = "SELECT * FROM products WHERE agentid='1' ORDER BY `name` ASC";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
				}
					

		while($option = $query->fetch_assoc()) {
			$option = CastMysqlTable($option, 'products');
			if($flag==1){
				 $imageid=$option['productid'];
			}
			else{
				$imageid=$option['imageid'];
			}
			if(file_exists($params['gProductDir'].$imageid.".png")){
			$option['image']=$params['gProdcutURL'].$imageid.".png";
			}else
			{
				 $option['image']="";
			}
			array_push($options, $option);
		}
		$query->free();

		

		$return['data']['Products'] = $options;
		
	}
	catch(Exception $e) {
		$return['success'] = false;
		$return['errorcode'] = $e->getMessage();
	}
		
	return $return;
}


