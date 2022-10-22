<?php
# includes
include("files/constants.php");
include("files/library/common.php");

$productNames = array(
	'sdg' => 'SGU',
	'maxe' => 'IGUX2',
	'xcle' => 'IGUx3',
	'evsx2' => 'EVSx2',
	'evsx3' => 'EVSx3'	
);

# connect to database
$mysqli = new mysqli($gDBHost, $gDBUser, $gDBPassword, $gDBName);
if($mysqli->connect_errno)
	throw new Exception($mysqli->connect_error, $mysqli->connect_errno);
$mysqli->set_charset('UTF');

# extract quote id
if(!isset($_REQUEST['id']) || $_REQUEST['id'] == '')
	die("Missing / invalid argument");
list($locationId, $hash) = explode('-', $_REQUEST['id']);
if($hash != hash('sha256', "$locationId".$gQuoteHashSecret))
	die("Missing / invalid argument");

# load location
$querySQL = "SELECT l.*, c.*, a.agentid,a.firstname AS afirstname, a.lastname AS alastname, a.unitnum AS aunitnum, a.street AS astreet, a.suburb AS asuburb, a.city AS acity, a.postcode AS apostcode, a.phone as aphone, a.email as aemail,a.businessname as abusinessname, c.email, c.phone FROM location AS l JOIN agent AS a ON l.agentid = a.agentid JOIN customer AS c on l.customerid = c.customerid WHERE l.locationid = ". intval($locationId);
#echo "$querySQL <p>";
if(!($query = $mysqli->query($querySQL)))
	throw new Exception($mysqli->error);
$location = $query->fetch_assoc();
$query->close();
if($location['locationid'] != $locationId)
	throw new Exception("Record not found");

# what product are being quoted on?
$locationProducts = array();
foreach(array('sdg', 'maxe', 'xcle', 'evsx2', 'evsx3') as $product) {
	if($location["quote$product"] == 1)
		array_push($locationProducts, $product);
}

# get staffs

$gettravel ="SELECT travel_status,distance,number_staff,agentid FROM location WHERE locationid=".intval($locationId)."";
if(!($query_travel = $mysqli->query($gettravel)))
	throw new Exception($mysqli->error);
$rowtravel = $query_travel->fetch_assoc();

# get margins

$queryMargins = "SELECT labourrate,travelrate FROM location_margins WHERE locationid=".intval($locationId)."";
if(!($query_margin = $mysqli->query($queryMargins)))
	throw new Exception($mysqli->error);
$margins = $query_margin->fetch_assoc();

#quote details

$agent_quoteSql="SELECT quotegreeting,quotedetails FROM agent WHERE agentid='".$rowtravel['agentid']."' AND quotegreeting!=''";
if(!($query_quote = $mysqli->query($agent_quoteSql)))
	throw new Exception($mysqli->error);
	if($query_quote->num_rows == 0){
		
		$query_quoteSQL="SELECT quotegreeting,quotedetails FROM params";
if(!($query_quote = $mysqli->query($query_quoteSQL)))
	throw new Exception($mysqli->error);

	}
$quoteDetails = $query_quote->fetch_assoc();
$query_quote->close();

#  prodcut images

$agent_imageSql="SELECT SGUimage,IGUx2image,IGUx3image,EVSx2image,EVSx3image,SGUurl,IGUx2url,IGUx3url,EVSx2url,EVSx3url FROM agent WHERE agentid='".$rowtravel['agentid']."'";
if(!($query_image = $mysqli->query($agent_imageSql)))
	throw new Exception($mysqli->error);
	
$imageDetails = $query_image->fetch_assoc();
$query_image->close();

if(empty($imageDetails['SGUimage']) || empty($imageDetails['IGUx2image']) || empty($imageDetails['IGUx3image']) || empty($imageDetails['EVSx2image']) || empty($imageDetails['EVSx3image'])){
	$sguimage=$maxeimage=$xcleimage=$evsx2image=$evsx3image="no-image.png";
}
else{
	 $sguimage=$imageDetails['SGUimage'];
		$maxeimage=$imageDetails['IGUx2image'];
		$xcleimage=$imageDetails['IGUx3image'];
		$evsx2image=$imageDetails['EVSx2image'];
		$evsx3image=$imageDetails['EVSx3image'];
		
}

$prodcutimages=array('sdg'=>$sguimage,'maxe'=>$maxeimage,'xcle'=>$xcleimage,'evsx2'=>$evsx2image,'evsx3'=>$evsx3image);
$prodcuturl=array('sdg'=>$imageDetails['SGUurl'],'maxe'=>$imageDetails['IGUx2url'],'xcle'=>$imageDetails['IGUx3url'],'evsx2'=>$imageDetails['EVSx2url'],'evsx3'=>$imageDetails['EVSx3url']);
	
$customerAddress = array();

foreach(array($location['firstname'].' '.$location['lastname'], $location['unitnum']. " ". $location['street'], $location['suburb'], $location['city'].' '.$location['postcode'], $location['email'], $location['phone']) as $field) {
	if($field != '')
		array_push($customerAddress, htmlspecialchars($field));	
}

$agentAddress = array();
foreach(array($location['abusinessname'],$location['aunitnum']. " ". $location['astreet'], $location['asuburb'], $location['acity'].' '. $location['apostcode'],$location['aemail'],"Phone : ".$location['aphone']) as $field) {
	if($field != '')
		array_push($agentAddress, htmlspecialchars($field));	
}

$greetings="Dear ".$location['firstname']."<br>";
$greetings.=$quoteDetails['quotegreeting'];
$quote_details=$quoteDetails['quotedetails'];

# get rooms
$querySQL = "SELECT * FROM room WHERE locationid = ". intval($locationId). " ORDER BY name";
if(!($query = $mysqli->query($querySQL)))
	throw new Exception($mysqli->error);
$rooms = array();
while($room = $query->fetch_assoc())
	$rooms[$room['roomid']] = array('roomid' => $room['roomid'], 'name' => $room['name'], 'windows' => array());
$query->close();

# get room windows
$totals = array('windowcount' => 0, 'panelcount' => 0, 'costsdg' => 0, 'costmaxe' => 0, 'costxcle' => 0, 'costevsx2' => 0, 'costevsx3' => 0);
foreach($rooms as $room) {
	//$querySQL = "SELECT * FROM window AS w JOIN window_type AS wt ON w.windowtypeid = wt.windowtypeid WHERE w.selected_product!='HOLD' AND roomid = ". intval($room['roomid']). " ORDER BY wt.name";
	$querySQL = "SELECT * FROM window AS w JOIN window_type AS wt ON w.windowtypeid = wt.windowtypeid WHERE roomid = ". intval($room['roomid']). " AND w.quote_status = '1' ORDER BY wt.name";
	if(!($query = $mysqli->query($querySQL)))
		throw new Exception($mysqli->error);
	while($window = $query->fetch_assoc()) {
		
$before_photos="SELECT photoid FROM window_photo WHERE windowid='".$window['windowid']."'";
		if(!($photos_window = $mysqli->query($before_photos)))
		throw new Exception($mysqli->error);
		$b_phots=array();
		while($photos= $photos_window->fetch_assoc())
		{
			 $b_phots[]=$photos;
		}
		
		$roomWindow = array('windowid' => $window['windowid'], 'name' => $window['name'],'notes'=>$window['notes'], 'windowtypeid' => $window['windowtypeid'],'photos'=>$b_phots);
		foreach($locationProducts as $product) {
	
			$roomWindow["cost$product"] = $window["cost$product"];
			//$totals["cost$product"] = $totals["cost$product"] + $window["cost$product"]+$Travel["travel$product"]+$extravalue;
			$totals["cost$product"] = $totals["cost$product"] + $window["cost$product"];
		}
		
		$totals['windowcount'] = $totals['windowcount'] + 1;
		$totals['panelcount'] += $window['numpanels'];
		array_push($rooms[$room['roomid']]['windows'], $roomWindow);
	}
	$query->close();
}

# quote pages
$querySQL = "SELECT * FROM quote_pages WHERE agentid='".$rowtravel['agentid']."'";
if(!($query = $mysqli->query($querySQL)))
	throw new Exception($mysqli->error);
$images = array();
while($rowquote = $query->fetch_assoc()){

$images[] = $rowquote;
	//$pages[] = $gquotepagePhotoURL.$images['image'];
}
	
$query->close();



//print_r($pages);die();
# render page
include('files/templates/quote.htm');

# done
$mysqli->close();
