<?php

include('../files/constants.php');

# globals
global $modules;
$modules = array("session", "locations", "location", "room", "window", "panel", "prodlocation", "photo", "customer", "windowtypes", "windowoptions", "quote");

# prevent caching
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: ". gmdate("D, d M Y H:i:s"). " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0, false");
header("Pragma: no-cache");

# set timezone to UTC
date_default_timezone_set('UTC');

# connect to database
$mysqli = new mysqli($gDBHost, $gDBUser, $gDBPassword, $gDBName);
if($mysqli->connect_errno)
	throw new Exception($mysqli->connect_error, $mysqli->connect_errno);
$mysqli->set_charset('UTF');

try {
	$urlFields = explode("/", $_SERVER['QUERY_STRING']);
	
	# get version
	$version = $urlFields[0];
	array_shift($urlFields);
	if($version != "v1")
		throw new Exception('Invalid API version');

	# get module
	$module = $urlFields[0];
	array_shift($urlFields);
	if(!in_array($module, $modules) || !file_exists("apimodules/$module.php"))
		throw new Exception('Invalid or missing module');
	else
		require_once("apimodules/$module.php");


	# get verb
	$queryStringArgs = array();
	list($verb, $extension) = explode(".", $urlFields[0], 2);
	if(($ampPos = strpos($extension, '&')) !== false) {
		$queryString = substr($extension, $ampPos);
		$extension = substr($extension, 0, $ampPos);
		parse_str(urldecode($queryString), $queryStringArgs);
	}		
	array_shift($urlFields);	
	if(!function_exists($verb) || $extension != "json")
		throw new Exception('Invalid or missing verb: '.$verb);
	
	# get arguments
	$argJSON = file_get_contents('php://input');
	$postData = json_decode($argJSON, true);
	$postData['mysqli'] = $mysqli;
	$postData['sessionsecret'] = $gSessionSecret;
	$postData['photodir'] = $gPhotoDir;
	$postData['windowtypephotourl'] = $gWindowTypePhotoURL;
	$postData['gPanelOptionsPhotoURL'] = $gPanelOptionsPhotoURL;
	$postData['gPanelOptionsSaftyURL']=$gPanelOptionsSaftyURL;
	$postData['gPanelOptionsGlassURL']=$gPanelOptionsGlassURL;
		
	# call verb
	if(!($return = call_user_func($verb, $queryStringArgs, $postData)))
		throw new Exception('Error on calling verb');
	
	# send result
	header("Content-Type: application/json");
  header("HTTP/1.1 200");
	echo json_encode($return);	
}
catch (Exception $e) {
	header("Content-Type: application/json");
  header("HTTP/1.1 200");
	$return = array('success' => false, 'errormessage' => $e->getMessage());
	echo json_encode($return);	
}

# done
$mysqli->close();
exit(0);


# return agentId if token valid else false
function ValidAccessToken($token) {
	global $gSessionSecret;
	
	list($agentId, $hash) = explode('-', $token);
	if($hash == hash('sha256', $gSessionSecret.$agentId))
		return $agentId;
	else
		return false;
}

# Return UTC time in YYYYMMDDHHMMSS format	
function GetUTCTime() {
	return gmdate("YmdHis");
}

function GetTodayDateStamp() {
  return GetDateStamp(time());
}

function GetDateStamp($timeStamp) {
  $date = localtime($timeStamp, true);
  return sprintf("%04d%02d%02d", $date['tm_year']+1900, $date['tm_mon']+1, $date['tm_mday']);
}

function AgentHasPermission($mysqli, $agentId, $table, $tableKeyId) {
	switch($table) {
		case 'location':
			$querySQL = "SELECT l.agentid FROM location AS l JOIN agent AS a ON l.agentid = a.agentid WHERE l.locationid = ". intval($tableKeyId). " AND a.enabled = 1";
			break;
		case 'customer':
			$querySQL = "SELECT c.agentid FROM customer AS c JOIN agent AS a ON c.agentid = a.agentid WHERE customerid = ". intval($tableKeyId). " AND a.enabled = 1";
			break;
		case 'room':
			$querySQL = "SELECT l.agentid FROM room AS r JOIN location AS l ON r.locationid = l.locationid JOIN agent AS a ON l.agentid = a.agentid WHERE r.roomid = ". intval($tableKeyId). " AND a.enabled = 1";
			break;
		case 'window':
			$querySQL = "SELECT l.agentid FROM window AS w JOIN room AS r ON w.roomid = r.roomid JOIN location AS l ON r.locationid = l.locationid JOIN agent AS a ON l.agentid = a.agentid WHERE w.windowid = ". intval($tableKeyId). " AND a.enabled = 1";
			break;
		case 'panel':
			$querySQL = "SELECT l.agentid FROM panel AS p JOIN window AS w ON p.windowid = w.windowid JOIN room AS r ON w.roomid = r.roomid JOIN location AS l ON r.locationid = l.locationid JOIN agent AS a ON l.agentid = a.agentid WHERE p.panelid = ". intval($tableKeyId). " AND a.enabled = 1";
	}
	
	if(!($query = $mysqli->query($querySQL))) {
		error_log($querySQL. " ERROR: ". $mysqli->error, 0);
		throw new Exception('DatabaseError');			
	}
	$row = $query->fetch_assoc();
	$query->free();	
	if($row['agentid'] != $agentId)
		return false;
	
	return true;
}


function ThrowDBException($querySQL, $mysqlError) {
	error_log($querySQL. " ERROR: ". $mysqlError, 0);
	throw new Exception('DatabaseError');			
}


function DeletePanels($mysqli, $panelIds) {
	$querySQL = "DELETE FROM panel WHERE panelid IN (". implode(',', $panelIds). ")";
	if(!($query = $mysqli->query($querySQL)))
		ThrowDBException($querySQL, $mysqli->error);
}


function DeletePhotos($mysqli, $photoDir, $photoIds, $tables=array('photo', 'window_photo')) {
	foreach($photoIds as $photoId) {
		if(!@unlink($photoDir."$photoId.jpg"))
			throw new Exception("InternalError");
	}

	foreach($tables as $table) {
		$querySQL = "DELETE FROM $table WHERE photoid IN (". implode(',', $photoIds). ")";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
	}
}


function DeleteWindows($mysqli, $photoDir, $windowIds) {
	# delete panels
	$querySQL = "SELECT panelid FROM panel WHERE windowid IN (". implode(',', $windowIds). ")";
	if(!($query = $mysqli->query($querySQL)))
		ThrowDBException($querySQL, $mysqli->error);	
	$panelIds = array();
	while($panel = $query->fetch_assoc())
		array_push($panelIds, $panel['panelid']);
	$query->free();
	if(count($panelIds))
		DeletePanels($mysqli, $panelIds);
		
	# delete photos
	$querySQL = "SELECT photoid FROM window_photo WHERE windowid IN (". implode(',', $windowIds). ")";
	if(!($query = $mysqli->query($querySQL)))
		ThrowDBException($querySQL, $mysqli->error);
	$photoIds = array();
	while($photo = $query->fetch_assoc())
		array_push($photoIds, $photo['photoid']);
	$query->free();
	if(count($photoIds))
		DeletePhotos($mysqli, $photoDir, $photoIds);
	
	# delete windows
	$querySQL = "DELETE FROM window WHERE windowid IN (". implode(',', $windowIds). ")";
	if(!($query = $mysqli->query($querySQL)))
		ThrowDBException($querySQL, $mysqli->error);
}


/* Simple email sender

To set a reply address supply extra headers:

Reply-to: reply@to.address

Sending one-part HTML:

$extraHeaders = "MIME-Version: 1.0\n";
$extraHeaders .= "Content-type: text/html; charset=iso-8859-1\n";
$message = "<HTML>sending html!</HTML>";
*/
function EmailSimple($to, $from, $subject, $message, $extraHeaders = "") {
  $headers = "From: $from\n";
  if($extraHeaders != "")
    $headers .= "$extraHeaders";
  return mail($to, $subject, $message, $headers);
}
