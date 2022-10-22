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
foreach(array('sdg','maxe','xcle', 'evsx2','evsx3') as $product) {
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

$locationquote = "SELECT `agentId`,`quotedatestamp`,`quotegreeting`,`quotedetails`,`paintdetails`,`stepdetails`,`paintlock`,`stepslock`,`painting_price`,`installation_price` FROM `location` WHERE `locationid`=".intval($locationId)."";
if(!($query_locquote = $mysqli->query($locationquote)))
	throw new Exception($mysqli->error);
	
	$quoteDetails = $query_locquote->fetch_assoc();
	
	if($quoteDetails['quotegreeting']==''){
		
		$agent_quoteSql="SELECT quotedate,quotegreeting,quotedetails,paintdetails,`stepdetails`,`paintlock`,`steplock`,`SGUname`,`IGUx2name`,`IGUx3name`,`EVSx2name`,`EVSx3name` FROM agent WHERE agentid='".$rowtravel['agentid']."' AND quotegreeting!=''";
if(!($query_agnetquote = $mysqli->query($agent_quoteSql)))
	throw new Exception($mysqli->error);
	
	$quoteagentDetails = $query_agnetquote->fetch_assoc();
	
	if($quoteagentDetails['quotegreeting'] == ''){
		
		$query_quoteSQL="SELECT quotegreeting,quotedetails FROM params";
   if(!($query_defaultquote = $mysqli->query($query_quoteSQL)))
	  throw new Exception($mysqli->error);
			
			$quotedefaultDetails = $query_defaultquote->fetch_assoc();
			
			$quote_greeting = $quotedefaultDetails['quotegreeting'];
			$quote_details  = $quotedefaultDetails['quotedetails'];
			$paint_details  = "";
			$step_details   = "";
			$paintlock      = 0;
			$steplock       = 0;
			$quotedate      = "";
			$painting_price = 0;
      $installation_price = 0;

			$product_Names = array(
				'sdg' => 'SGU',
				'maxe' => 'IGUX2',
				'xcle' => 'IGUx3',
				'evsx2' => 'EVSx2',
				'evsx3' => 'EVSx3'
			);
		
	}
	
	else{
		
		    $quote_greeting = $quoteagentDetails['quotegreeting'];
			$quote_details  = $quoteagentDetails['quotedetails'];
			$paint_details  = $quoteagentDetails['paintdetails'];
			$step_details   = $quoteagentDetails['stepdetails'];
			$paintlock      = $quoteagentDetails['paintlock'];
			$steplock       = $quoteagentDetails['steplock'];
			$quotedate      = $quoteagentDetails['quotedate'];

			$product_Names = array(
				'sdg' => $quoteagentDetails['SGUname'],
				'maxe' => $quoteagentDetails['IGUx2name'],
				'xcle' => $quoteagentDetails['IGUx3name'],
				'evsx2' => $quoteagentDetails['EVSx2name'],
				'evsx3' => $quoteagentDetails['EVSx3name']
			);
		
		
	}

		
	}
	else{
		
		 $quote_greeting = $quoteDetails['quotegreeting'];
			$quote_details  = $quoteDetails['quotedetails'];
			$paint_details  = $quoteDetails['paintdetails'];
			$step_details   = $quoteDetails['stepdetails'];
			$paintlock      = $quoteDetails['paintlock'];
			$steplock       = $quoteDetails['stepslock'];
			$painting_price = $quoteDetails['painting_price'];
      $installation_price = $quoteDetails['installation_price'];
			
			$productSql="SELECT `SGUname`,`IGUx2name`,`IGUx3name`,`EVSx2name`,`EVSx3name` FROM agent WHERE agentid='".$quoteDetails['agentId']."' AND quotegreeting!=''";
if(!($row_pdtSql = $mysqli->query($productSql)))
	throw new Exception($mysqli->error);
	
	$RowPdt = $row_pdtSql->fetch_assoc();
	 $product_Names = array(
		'sdg' => $RowPdt['SGUname'],
		'maxe' => $RowPdt['IGUx2name'],
		'xcle' => $RowPdt['IGUx3name'],
		'evsx2' => $RowPdt['EVSx2name'],
		'evsx3' => $RowPdt['EVSx3name']
	);


			if(!empty($quoteDetails['quotedatestamp']))
			$quotedate      = date('d/m/Y',$quoteDetails['quotedatestamp']);
			else
			$quotedate      = "";
			
		
	}





#  prodcut images

$agent_imageSql="SELECT SGUimage,IGUx2image,IGUx3image,EVSx2image,EVSx3image,SGUurl,IGUx2url,IGUx3url,EVSx2url,EVSx3url FROM agent WHERE agentid='".$rowtravel['agentid']."'";
if(!($query_image = $mysqli->query($agent_imageSql)))
	throw new Exception($mysqli->error);
	
$imageDetails = $query_image->fetch_assoc();
$query_image->close();

	 if(!empty($imageDetails['SGUimage']))
	 $sguimage=$imageDetails['SGUimage'];
		else
		$sguimage ="no-image.png";
		if(!empty($imageDetails['IGUx2image']))
		$maxeimage=$imageDetails['IGUx2image'];
		else
		$maxeimage="no-image.png";
		if(!empty($imageDetails['IGUx3image']))
		$xcleimage=$imageDetails['IGUx3image'];
		else
		$xcleimage="no-image.png";
		if(!empty($imageDetails['EVSx2image']))
		$evsx2image=$imageDetails['EVSx2image'];
		else
		$evsx2image="no-image.png";
		if(!empty($imageDetails['EVSx3image']))
		$evsx3image=$imageDetails['EVSx3image'];
		else
		$evsx3image="no-image.png";
		

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
$greetings.=$quote_greeting;//$quoteDetails['quotegreeting'];
$quote_details=$quote_details;//$quoteDetails['quotedetails'];

# get rooms
$querySQL = "SELECT * FROM room WHERE locationid = ". intval($locationId). " ORDER BY name";
if(!($query = $mysqli->query($querySQL)))
	throw new Exception($mysqli->error);
$rooms = array();
while($room = $query->fetch_assoc())
	$rooms[$room['roomid']] = array('roomid' => $room['roomid'], 'name' => $room['name'], 'windows' => array());
$query->close();

# get room windows
$colors =array();
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

		$queryLabour = "SELECT SUM(dglabour) AS dglabour, SUM(evslabour) AS evslabour FROM panel WHERE windowid = '".$window['windowid']."'";
	
	if(!($querylabour = $mysqli->query($queryLabour)))
	throw new Exception($mysqli->error);
	
	 $row_labour = $querylabour->fetch_assoc();
		
		$roomWindow = array('labourhours'=>$row_labour,'windowid' => $window['windowid'], 'name' => $window['name'],'notes'=>$window['notes'], 'windowtypeid' => $window['windowtypeid'],'photos'=>$b_phots);
		foreach($locationProducts as $product) {
	
			$roomWindow["cost$product"] = $window["cost$product"];
			//$totals["cost$product"] = $totals["cost$product"] + $window["cost$product"]+$Travel["travel$product"]+$extravalue;
			$totals["cost$product"] = $totals["cost$product"] + $window["cost$product"];
		}
		
		$totals['windowcount'] = $totals['windowcount'] + 1;
		$totals['panelcount'] += $window['numpanels'];
		array_push($rooms[$room['roomid']]['windows'], $roomWindow);
		
		/*getpanelcolor*/
        $panel_paint_cost =array();
		$getcolor = "SELECT DISTINCT(colourid) FROM panel WHERE windowid='".$window['windowid']."'";
		if(!($color_window = $mysqli->query($getcolor)))
		throw new Exception($mysqli->error);
		
		while($rowcolor= $color_window->fetch_array())
		{

	    $sql_cntPanel = "SELECT count(`colourid`) AS panelcount FROM `panel` WHERE `windowid`='".$window['windowid']."' AND `colourid`='".$rowcolor['colourid']."'";

		if(!($cntPanel = $mysqli->query($sql_cntPanel)))
		throw new Exception($mysqli->error);
		$panelCount = $cntPanel->fetch_array();

		$sqlQuery = "SELECT hoursperpanel, costperpanel FROM `paint_specifications` WHERE `locationid`=". intval($locationId). " AND `selected_status`=1 AND colourid='".$rowcolor['colourid']."'";
        
        if(!($paintcost = $mysqli->query($sqlQuery)))
			throw new Exception($mysqli->error);
			
			while($paints_cost= $paintcost->fetch_array())
			{ 
            
				$panel_paint_cost[]= (($paints_cost['hoursperpanel']* $margins['labourrate'])+$paints_cost['costperpanel'])*$panelCount['panelcount'];

				
				$PrepareCost[$window['windowid']] = array_sum($panel_paint_cost);

				
				$Post[$rowcolor['colourid']][] =$panelCount['panelcount'];

				//$posts[] = $Post;

			}

			

            
			 $colors[]=$rowcolor['colourid'];
		}
	}
	$query->close();
	
	
	
}

//print_r($Post);die();
# paint specifications
if(count($colors)>0){
	
	$rescolor = array_unique($colors);
	
	if(count($rescolor)>1)
	
	$joincolor =join(',',$rescolor);
	
	else
	
	$joincolor = $rescolor[0];
	
	
$paint_panelcnt=array();

$paint_price=array();

$getpaintsel = "SELECT paint_specifications.*,colours.colourname,colours.colorcode FROM `paint_specifications`,`colours` WHERE paint_specifications.colourid=colours.colourid AND paint_specifications.colourid IN($joincolor)AND paint_specifications.locationid='".intval($locationId)."' AND paint_specifications.selected_status='1'";

if(!($query_paint = $mysqli->query($getpaintsel)))
	throw new Exception($mysqli->error);
	$i=0;
while($rowpaint = $query_paint->fetch_assoc()){
	
	//$paint_panelcnt[]= $rowpaint['panelcount'];

	$rowpaint['panelcount']=array_sum($Post[$rowpaint['colourid']]);

	$paint_panelcnt[]= array_sum($Post[$rowpaint['colourid']]);
	
	$paint_price[] = $rowpaint['totalcost'];
	
	$paints[] = $rowpaint;

	$i++;
		
	}
	
if(count($paint_panelcnt)>0)
	
	$totalpanelPaint = array_sum($paint_panelcnt);
	
else	

$totalpanelPaint =0;

if(count($paint_price)>0)
	
	$totalpaint_price = array_sum($paint_price);
	
else	

$totalpaint_price =0;
	
	
}

# quote pages
$querySQL = "SELECT quote_pages.images,quote_pages.pages FROM `quotepage_location`,quote_pages WHERE quotepage_location.pageid=quote_pages.pageid AND quotepage_location.agentid='".$rowtravel['agentid']."' AND quotepage_location.locationid='".$locationId."'";
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
