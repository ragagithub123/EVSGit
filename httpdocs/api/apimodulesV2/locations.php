<?php

require 'datadict.php';

function get($queryString, $params) {
	$return = array('success' => true, 'errorcode' => '');
	
	try {
		# check access token 
		if(!($agentId = ValidAccessToken($params['accesstoken'])))
			throw new Exception("BadCredentials");
		
		$mysqli = $params['mysqli'];
		
		# statuses
		$querySQL = "SELECT * FROM jobstatus ORDER BY jobstatusid";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
		$locationStatuses = array();
		while($status = $query->fetch_assoc())
			array_push($locationStatuses, CastMysqlTable($status, 'jobstatus'));
		$query->free();
		
		# locations
		$querySQL = "SELECT l.*, ls.status,ls.location_type,ls.jobstatus FROM location AS l JOIN jobstatus AS ls ON l.jobstatusid = ls.jobstatusid AND l.jobstatusid!='7' WHERE agentid = $agentId ORDER BY locationid DESC";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
		$locations = array();
		while($location = $query->fetch_assoc()) {
			$location = CastMysqlTable($location, 'location');
			if($location['alarmStatus']==1 &&( $location['booking_date'] >= date('Y-m-d H:i:s')) && $location['booking_status']==1){
				$location['alarmStatus']=1;
			}
			else
			{
				 $location['alarmStatus']=0;
			}
			$sendLocation = array();
			
			foreach(array('locationid', 'unitnum', 'street', 'suburb', 'city', 'quotelocked', 'locationstatusid','jobstatusid', 'status','location_type','photoid','alarmStatus','alarm_Type') as $field)
			
				$sendLocation[$field] = $location[$field];
			array_push($locations, $sendLocation);
		}
		$query->free();		
		$getcat="SELECT DISTINCT(location_type) FROM jobstatus";
		if(!($query_cat = $mysqli->query($getcat)))
			ThrowDBException($query_cat, $mysqli->error);
			$categorySearch = array();
		while($type = $query_cat->fetch_assoc())
			array_push($categorySearch, CastMysqlTable($type, 'location_status_table'));
			
			#maxlocations
			
			$getmaxcount="SELECT maxlocations FROM agent WHERE agentid='$agentId'";
			if(!($query_max = $mysqli->query($getmaxcount)))
			ThrowDBException($query_max, $mysqli->error);
			$row_count = $query_max->fetch_assoc();
			 $max_count= $row_count['maxlocations'];
			
			$getloc="SELECT count(locationid) AS locationcount FROM location WHERE agentid='$agentId'";
		if(!($query_cnt = $mysqli->query($getloc)))
			ThrowDBException($query_cnt, $mysqli->error);
			$row_loc = $query_cnt->fetch_assoc();
			$loccount=$row_loc['locationcount'];
			if($loccount >= $max_count)
			$add_status=true;
			else
			$add_status=false;
			
				
		$return['data'] = array('locations' => $locations, 'statuslist' => $locationStatuses,'SearchCategory'=>$categorySearch,'LocationStatus'=>$add_status);
	}
	catch(Exception $e) {
		$return['success'] = false;
		$return['errorcode'] = $e->getMessage();
	}
		
	return $return;
}