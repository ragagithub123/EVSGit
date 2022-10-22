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
		
		
		# build sql query
		$querySQL = "SELECT b.frametypeid,b.`name`,b.`category` AS `catid`,b.`imageid`,c.`category` FROM  `paneloption_frametype` b,`famecategory` c WHERE  b.`category`=c.famecategoryid AND  b.".$params['data']['materialCategory']."=1";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
			$frames = array();
			while($row = $query->fetch_assoc()){
					if(file_exists($params['gFrameDir'].$row['imageid'].".png")){
			  $row['image']=$params['gFrameURL'].$row['imageid'].".png";
			}else
			{
				 $row['image']="";
			}
					$querySQL1 = "SELECT styleid,name as stylename FROM paneloption_style WHERE frametypeid=".$row['frametypeid']." ";
		if(!($query1 = $mysqli->query($querySQL1)))
			ThrowDBException($querySQL1, $mysqli->error);
			$styles=array();
			while($style=$query1->fetch_assoc()){
			/*$row['styleid']=$style['styleid'];
			$row['stylename']=$style['stylename'];*/
			  $styles[]=$style;
			}
			 $row['styles']=$styles;
							array_push($frames, $row);
			}
		$return['data'] = array('Frametypes'=>$frames);

			
	}
	catch(Exception $e) {
		$return['success'] = false;
		$return['errorcode'] = $e->getMessage();
	}
		
	return $return;
}

