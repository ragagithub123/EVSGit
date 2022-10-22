<?php
# includes
include("files/constants.php");
include("files/library/session.php");
include("files/library/formlib.php");
include("files/library/common.php");
if (isset($_REQUEST['cancel'])) {
	header("Location: " . $_SERVER['PHP_SELF'] . "?type=" . $_REQUEST["type"]);
	exit(0);
}
# connect to database
$mysqli = new mysqli($gDBHost, $gDBUser, $gDBPassword, $gDBName);
if ($mysqli->connect_errno)
	throw new Exception($mysqli->connect_error, $mysqli->connect_errno);
$mysqli->set_charset('UTF');
$adminId = SessionCheck($mysqli);
$optionTypes = array(
	'astragal' => array('typename' => 'Astragal', 'idfield' => 'astragalsid', 'valuefield' => 'astragalvalue'),
	'condition' => array('typename' => 'Condition', 'idfield' => 'conditionid', 'valuefield' => 'conditionvalue'),
	'glasstype' => array('typename' => 'Glass Type', 'idfield' => 'glasstypeid', 'valuefield' => 'typevalue'),
	'safety' => array('typename' => 'Safety Glass', 'idfield' => 'safetyid', 'valuefield' => 'safetyvalue'),
	'style' => array('typename' => 'Style', 'idfield' => 'styleid', 'valuefield' => 'astragalvalue'),
	'layers' => array('typename' => 'Layers', 'idfield' => 'layersid', 'valuefield' => ''),
	'frametype' => array('typename' => 'Frame Type', 'idfield' => 'frametypeid', 'valuefield' => ''),
	//'profile' => array('typename' => 'Profile Sets', 'idfield' => 'styleid', 'valuefield' => 'astragalvalue'),
);
$type = $_REQUEST["type"];
if (!array_key_exists($type, $optionTypes))
	throw new Exception("Missing / invalid argument");
if (isset($_REQUEST["id"])) {
	$form = new Form($_POST);
	if ($_REQUEST['id'] != 'add') {
		$id = intval($_REQUEST["id"]);
		$querySQL = "SELECT * FROM paneloption_$type WHERE " . $optionTypes[$type]['idfield'] . " = $id";
		if (!($query = $mysqli->query($querySQL)))
			throw new Exception($mysqli->error);
		$option = $query->fetch_assoc();
		$query->free();
		if ($type == "layers") {
			$getcolorcode = "SELECT colorcode,colourname FROM  sapcercolor WHERE colourid=" . $option['spacerColor'] . "";
			if (!($query_colorcode = $mysqli->query($getcolorcode)))
				throw new Exception($mysqli->error);
			$row_color = $query_colorcode->fetch_assoc();
			if (!empty($row_color['colorcode'])) {
				$option['color_code'] = $row_color['colorcode'];
				$option['colourname'] = $row_color['colourname'];
			} else
				$option['color_code'] = "000000";
			$option['colourname'] = 'bk(Black)';
		}
		if ($type == "style") {
			$getimageid = "SELECT imageid FROM paneloption_frametype WHERE frametypeid='" . $option['frametypeid'] . "'";
			if (!($query_image = $mysqli->query($getimageid)))
				throw new Exception($mysqli->error);
			$row_imageid = $query_image->fetch_assoc();
			$option['imageid'] = $row_imageid['imageid'];
		}
		if ($option[$optionTypes[$type]['idfield']] != $id)
			throw new Exception("Invalid argument");
		# frametype
		$query_frametype = "SELECT paneloption_style.`styleid`,paneloption_style.`name` FROM paneloption_style,paneloption_frametype WHERE paneloption_style.`frametypeid`=paneloption_frametype.frametypeid AND paneloption_frametype.frametypeid=" . $id . "";
		if (!($query = $mysqli->query($query_frametype)))
			throw new Exception($mysqli->error);
		$frametype = array();
		while ($row_frame = $query->fetch_assoc())
			array_push($frametype, $row_frame);
		$query->free();
	} else {
		$id = "add";
		$option = array('name' => '', 'value' => '', 'color_code' => '000000');
	}
	# get list of profiles
	$querySQLProfile = "SELECT * FROM profiles ORDER BY profilename";
	if (!($queryprofile = $mysqli->query($querySQLProfile)))
		throw new Exception($mysqli->error);
	while ($profiles = $queryprofile->fetch_assoc()) {
		$listprof[] = $profiles;
	}
	if (!empty($listprof)) {
		$option['profiles'] = $listprof;
	}
	# get list of glasstypes
	$querySQLPanelType = "SELECT * FROM paneloption_glasstype ORDER BY name";
	if (!($querypaneltype = $mysqli->query($querySQLPanelType)))
		throw new Exception($mysqli->error);
	while ($panel_types = $querypaneltype->fetch_assoc()) {
		$listtypes[] = $panel_types;
	}
	if (!empty($listtypes)) {
		$option['paneltypes'] = $listtypes;
	}
	# get list of brands
	$querySQLBrand = "SELECT brandid,name FROM brands ORDER BY name ASC";
	if (!($querybrand = $mysqli->query($querySQLBrand)))
		throw new Exception($mysqli->error);
	while ($row_brands = $querybrand->fetch_assoc()) {
		$brands[] = $row_brands;
	}
	#get colourcodes
	$querySqlColor = "SELECT * FROM colours";
	if (!($querycolor = $mysqli->query($querySqlColor)))
		throw new Exception($mysqli->error);
	while ($color_types = $querycolor->fetch_assoc()) {
		$colors[] = $color_types;
	}
	if (!empty($colors)) {
		$option['colors'] = $colors;
	}
	if (isset($_REQUEST['save'])) {
		$fields = array();
		$errors = array();
		if (trim($_REQUEST['name']) == '')
			$errors['name'] = 'Required';
		else
			array_push($fields, "name = '" . $mysqli->real_escape_string($_REQUEST['name']) . "'");
		if ($type == 'style') {
			/*if(!is_numeric($_REQUEST['IGUlabour']))
				$errors['IGUlabour'] = 'Invalid / Required';
			else
				array_push($fields, "IGUlabour = ". floatval($_REQUEST['IGUlabour']));
					if(!is_numeric($_REQUEST['IGUlabour']))
				$errors['EVSlabour'] = 'Invalid / Required';
			 else
				array_push($fields, "EVSlabour = ". floatval($_REQUEST['EVSlabour']));*/
			if (!is_numeric($_REQUEST['styledgvalue']))
				$errors['styledgvalue'] = 'Invalid / Required';
			else
				array_push($fields, "styledgvalue = " . floatval($_REQUEST['styledgvalue']));
			if (!is_numeric($_REQUEST['styleevsvalue']))
				$errors['styleevsvalue'] = 'Invalid / Required';
			else
				array_push($fields, "styleevsvalue = " . floatval($_REQUEST['styleevsvalue']));
			if (!is_numeric($_REQUEST['IGUassemble']))
				$errors['IGUassemble'] = 'Invalid / Required';
			else
				array_push($fields, "IGUassemble = " . floatval($_REQUEST['IGUassemble']));
			if (!is_numeric($_REQUEST['EVSassemble']))
				$errors['EVSassemble'] = 'Invalid / Required';
			else
				array_push($fields, "EVSassemble = " . floatval($_REQUEST['EVSassemble']));
			if (!is_numeric($_REQUEST['evsProfileX']))
				$errors['evsProfileX'] = 'Invalid / Required';
			else
				array_push($fields, "evsProfileX = " . floatval($_REQUEST['evsProfileX']));
			if (!is_numeric($_REQUEST['evsProfileY']))
				$errors['evsProfileY'] = 'Invalid / Required';
			else
				array_push($fields, "evsProfileY = " . floatval($_REQUEST['evsProfileY']));
			if (!is_numeric($_REQUEST['retroProfileX']))
				$errors['retroProfileX'] = 'Invalid / Required';
			else
				array_push($fields, "retroProfileX = " . floatval($_REQUEST['retroProfileX']));
			if (!is_numeric($_REQUEST['retroProfileY']))
				$errors['retroProfileY'] = 'Invalid / Required';
			else
				array_push($fields, "retroProfileY = " . floatval($_REQUEST['retroProfileY']));
			if (!is_numeric($_REQUEST['evsGlassX']))
				$errors['evsGlassX'] = 'Invalid / Required';
			else
				array_push($fields, "evsGlassX = " . floatval($_REQUEST['evsGlassX']));
			if (!is_numeric($_REQUEST['evsGlassY']))
				$errors['evsGlassY'] = 'Invalid / Required';
			else
				array_push($fields, "evsGlassY = " . floatval($_REQUEST['evsGlassY']));
			if (!is_numeric($_REQUEST['retroGlassX']))
				$errors['retroGlassX'] = 'Invalid / Required';
			else
				array_push($fields, "retroGlassX = " . floatval($_REQUEST['retroGlassX']));
			if (!is_numeric($_REQUEST['retroGlassY']))
				$errors['retroGlassY'] = 'Invalid / Required';
			else
				array_push($fields, "retroGlassY = " . floatval($_REQUEST['retroGlassY']));
			array_push($fields, "evsProfileTop = '" . $mysqli->real_escape_string($_REQUEST['evsProfileTop']) . "'");
			array_push($fields, "retroProfileTop = '" . $mysqli->real_escape_string($_REQUEST['retroProfileTop']) . "'");
			array_push($fields, "evsProfileBottom = '" . $mysqli->real_escape_string($_REQUEST['evsProfileBottom']) . "'");
			array_push($fields, "retroProfileBottom = '" . $mysqli->real_escape_string($_REQUEST['retroProfileBottom']) . "'");
			array_push($fields, "evsProfileRight = '" . $mysqli->real_escape_string($_REQUEST['evsProfileRight']) . "'");
			array_push($fields, "evsProfileLeft = '" . $mysqli->real_escape_string($_REQUEST['evsProfileLeft']) . "'");
			array_push($fields, "retroProfileRight = '" . $mysqli->real_escape_string($_REQUEST['retroProfileRight']) . "'");
			array_push($fields, "retroProfileLeft = '" . $mysqli->real_escape_string($_REQUEST['retroProfileLeft']) . "'");
			array_push($fields, "evsOutPanelType = '" . $mysqli->real_escape_string($_REQUEST['evsOutPanelType']) . "'");
			array_push($fields, "evsInPanelType = '" . $mysqli->real_escape_string($_REQUEST['evsInPanelType']) . "'");
			array_push($fields, "retroOutPanelType = '" . $mysqli->real_escape_string($_REQUEST['retroOutPanelType']) . "'");
			array_push($fields, "retroInPanelType = '" . $mysqli->real_escape_string($_REQUEST['retroInPanelType']) . "'");
			array_push($fields, "styleNotes = '" . $mysqli->real_escape_string($_REQUEST['styleNotes']) . "'");
			array_push($fields, "category = '" . $mysqli->real_escape_string($_REQUEST['category']) . "'");
		} else if ($type == 'frametype') {
			array_push($fields, "category = '" . $mysqli->real_escape_string($_REQUEST['category']) . "'");
			array_push($fields, "notes = '" . $mysqli->real_escape_string($_REQUEST['notes']) . "'");
			array_push($fields, "NALU = '" . $mysqli->real_escape_string($_REQUEST['NALU']) . "'");
			array_push($fields, "NTIM = '" . $mysqli->real_escape_string($_REQUEST['NTIM']) . "'");
			array_push($fields, "RALU = '" . $mysqli->real_escape_string($_REQUEST['RALU']) . "'");
			array_push($fields, "RTIM = '" . $mysqli->real_escape_string($_REQUEST['RTIM']) . "'");
			array_push($fields, "RMET = '" . $mysqli->real_escape_string($_REQUEST['RMET']) . "'");
		} else if ($type == 'layers') {
			if (!is_numeric($_REQUEST['outsideThickness']))
				$errors['outsideThickness'] = 'Invalid / Required';
			else
				array_push($fields, "outsideThickness = " . floatval($_REQUEST['outsideThickness']));
			if (!is_numeric($_REQUEST['insideThickness']))
				$errors['insideThickness'] = 'Invalid / Required';
			else
				array_push($fields, "insideThickness = " . floatval($_REQUEST['insideThickness']));
			if (!is_numeric($_REQUEST['weight']))
				$errors['weight'] = 'Invalid / Required';
			else
				array_push($fields, "weight = '" . floatval($_REQUEST['weight']) . "'");
			//array_push($fields, "category = '". $mysqli->real_escape_string($_REQUEST['category']). "'");
			//array_push($fields, "name = '". $mysqli->real_escape_string($_REQUEST['name']). "'");
			array_push($fields, "glassType = '" . $mysqli->real_escape_string($_REQUEST['glassType']) . "'");
			array_push($fields, "safetyType = '" . $mysqli->real_escape_string($_REQUEST['safetyType']) . "'");
			array_push($fields, "outsideGlasstype = '" . $mysqli->real_escape_string($_REQUEST['outsideGlasstype']) . "'");
			if (trim($_REQUEST['OutsideGlasscode']) == '')
			$errors['OutsideGlasscode'] = 'Required';
			else
			array_push($fields, "OutsideGlasscode = '" . $mysqli->real_escape_string($_REQUEST['OutsideGlasscode']) . "'");
			array_push($fields, "spacerColor = '" . $mysqli->real_escape_string($_REQUEST['spacerColor']) . "'");
			array_push($fields, "spacerMaterial = '" . $mysqli->real_escape_string($_REQUEST['spacerMaterial']) . "'");
			array_push($fields, "spacerDesiccate = '" . $mysqli->real_escape_string($_REQUEST['spacerDesiccate']) . "'");
			array_push($fields, "insideGlasstype = '" . $mysqli->real_escape_string($_REQUEST['insideGlasstype']) . "'");
			if (trim($_REQUEST['InsideGlasscode']) == '')
			$errors['InsideGlasscode'] = 'Required';
			else
			array_push($fields, "InsideGlasscode = '" . $mysqli->real_escape_string($_REQUEST['InsideGlasscode']) . "'");
			array_push($fields, "compositeThickness = '" . $mysqli->real_escape_string($_REQUEST['compositeThickness']) . "'");
			array_push($fields, "sapcerWidth = '" . $mysqli->real_escape_string($_REQUEST['sapcerWidth']) . "'");
			array_push($fields, "brand = '" . $mysqli->real_escape_string($_REQUEST['category']) . "'");
		} else {
			if (!is_numeric($_REQUEST['value']))
				$errors['value'] = 'Invalid / Required';
			else
			  if ($type == "safety") {
				array_push($fields, "urlLink = " . "'" . $_REQUEST['urlLink'] . "'");
			}
			array_push($fields, $optionTypes[$type]['valuefield'] . " = " . floatval($_REQUEST['value']));
		}
		if (count($errors) == 0) { # save it
			if ($_REQUEST['id'] == 'add') { # create new option
				$querySQL = "INSERT INTO paneloption_$type () VALUES ()";
				if (!($query = $mysqli->query($querySQL)))
					throw new Exception($mysqli->error);
				$id = $mysqli->insert_id;
			}
			if ($type == 'layers') {
				if (!empty($_FILES['image']['name'])) {
					$newfilename = $id . ".jpg";
					move_uploaded_file($_FILES['image']['tmp_name'], $gLayerDir . "/" . $newfilename);
					array_push($fields, "icon = '" . $mysqli->real_escape_string($newfilename) . "'");
				}
			}
			# update option
			array_push($fields, "isdefault = " . intval($_REQUEST['default']));
			$querySQL = "UPDATE paneloption_$type SET " . implode(",", $fields) . " WHERE " . $optionTypes[$type]['idfield'] . " = $id";
			if (!($query = $mysqli->query($querySQL)))
				throw new Exception($mysqli->error);
			# process image (if available)
			if ($_FILES['icon']['tmp_name'] != '') {
				if ($type == "style") {
					if (!move_uploaded_file($_FILES['icon']['tmp_name'], $gPanelOptionsPhotoDir . $id . ".png"))
						throw new Exception("Unable to create " . $gPanelOptionsPhotoDir . $id . ".png");
				} else if ($type == "glasstype") {
					if (!move_uploaded_file($_FILES['icon']['tmp_name'], $gPanelOptionsGlassDir . $id . ".png"))
						throw new Exception("Unable to create " . $gPanelOptionsGlassDir . $id . ".png");
				} else if ($type == "safety") {
					if (!move_uploaded_file($_FILES['icon']['tmp_name'], $gPanelOptionsSaftyDir . $id . ".png"))
						throw new Exception("Unable to create " . $gPanelOptionsSaftyDir . $id . ".png");
				}
			}
			# done
			header("Location: " . $_SERVER['PHP_SELF'] . "?type=$type");
			$mysqli->close();
			exit(0);
		} else
			$message = "<div class=\"alert alert-danger\" role=\"alert\">Please correct the indicated errors</div>";
	}
	# style category
	$queryCategory = "SELECT * FROM style_category ORDER BY category ASC";
	if (!($query = $mysqli->query($queryCategory)))
		throw new Exception($mysqli->error);
	$catgories = array();
	while ($catgory = $query->fetch_assoc())
		array_push($catgories, $catgory);
	$query->free();
	if ($type == 'style') {
		if ($id == 'add') {
			$queryfCategory = "SELECT * FROM famecategory WHERE material_tag = 'RALU'";
			$queryframetypes = "SELECT * FROM  paneloption_frametype WHERE category ='1'";
		} else {
			$queryget_maincat = "SELECT material_tag FROM famecategory WHERE famecategoryid ='" . $option['category'] . "'";
			if (!($query = $mysqli->query($queryget_maincat)))
				throw new Exception($mysqli->error);
			$maincat = $query->fetch_array();
			$option['main_cat'] = $maincat['material_tag'];
			$queryfCategory = "SELECT * FROM famecategory WHERE material_tag = '" . $maincat['material_tag'] . "'";
			$queryframetypes = "SELECT * FROM  paneloption_frametype WHERE category =" . $option['category'] . "";
		}
		$querydist = "SELECT DISTINCT(`materialCategory`),`material_tag` FROM `famecategory`";
		if (!($query = $mysqli->query($querydist)))
			throw new Exception($mysqli->error);
		$fdistcatgories = array();
		while ($fdistcatgory = $query->fetch_assoc())
			array_push($fdistcatgories, $fdistcatgory);
		$query->free();
		if (!($query = $mysqli->query($queryfCategory)))
			throw new Exception($mysqli->error);
		$fcatgories = array();
		while ($fcatgory = $query->fetch_assoc())
			array_push($fcatgories, $fcatgory);
		$query->free();
		if (!($query = $mysqli->query($queryframetypes)))
			throw new Exception($mysqli->error);
		$frametypescat = array();
		while ($framecatgory = $query->fetch_assoc())
			array_push($frametypescat, $framecatgory);
		$query->free();
	} else {
		# famecategory
		$queryfCategory = "SELECT * FROM famecategory ORDER BY category ASC";
		if (!($query = $mysqli->query($queryfCategory)))
			throw new Exception($mysqli->error);
		$fcatgories = array();
		while ($fcatgory = $query->fetch_assoc())
			array_push($fcatgories, $fcatgory);
		$query->free();
	}
	# safety
	$querySafety = "SELECT * FROM paneloption_safety ORDER BY name ASC";
	if (!($query = $mysqli->query($querySafety)))
		throw new Exception($mysqli->error);
	$safeties = array();
	while ($safety = $query->fetch_assoc())
		array_push($safeties, $safety);
	$query->free();
	# sapcercolor
	$querySpacer = "SELECT * FROM sapcercolor";
	if (!($query = $mysqli->query($querySpacer)))
		throw new Exception($mysqli->error);
	$sapcercolor = array();
	while ($sapcer_color = $query->fetch_assoc())
		array_push($sapcercolor, $sapcer_color);
	$query->free();
	$spacerMaterials = array('None', 'Timber', 'Aluminium', 'Warm Edge', 'Box Tape');
	$SpacerDesiccate = array('None', 'Bentonite', 'Molecular Sieve');
	$Composite      =  array('3mm', '4mm', '14mm', '16mm', '17mm', '18mm', '20mm');
	$sapcerwidth    =  array('6mm', '8mm', '9mm', '10mm', '12mm', '0mm');
	$pageContent = "files/templates/paneloptionedit.htm";
} else {
	$querySQL = "SELECT * FROM paneloption_$type ORDER BY name";
	if (!($query = $mysqli->query($querySQL)))
		throw new Exception($mysqli->error);
	$options = array();
	while ($option = $query->fetch_assoc()) {
		if ($type == "layers") {
			$getprofilecode = "SELECT name FROM paneloption_glasstype WHERE glasstypeid=" . $option['glassType'] . "";
			if (!($query_code = $mysqli->query($getprofilecode)))
				throw new Exception($mysqli->error);
			$row_code = $query_code->fetch_assoc();
			if (!empty($row_code['name']))
				$option['glassType'] = $row_code['name'];
			else
				$option['glassType'] = "";
			if ($option['brand'] != null) {
				$getbrand = "SELECT name FROM brands WHERE brandid=" . $option['brand'] . "";
				if (!($query_brand = $mysqli->query($getbrand)))
					throw new Exception($mysqli->error);
				$row_brand = $query_brand->fetch_assoc();
				if (!empty($row_brand['name']))
					$option['brand'] = $row_brand['name'];
				else
					$option['brand'] = "";
			} else {
				$option['brand'] = "";
			}
			$getoutsideGlasstype = "SELECT name FROM paneloption_glasstype WHERE glasstypeid=" . $option['outsideGlasstype'] . "";
			if (!($query_getoutsideGlasstype = $mysqli->query($getoutsideGlasstype)))
				throw new Exception($mysqli->error);
			$row_outsideGlasstype = $query_getoutsideGlasstype->fetch_assoc();
			if (!empty($row_outsideGlasstype['name']))
				$option['outsideGlasstype'] = $row_outsideGlasstype['name'];
			else
				$option['outsideGlasstype'] = "";
			$getinsideGlasstype = "SELECT name FROM paneloption_glasstype WHERE glasstypeid=" . $option['insideGlasstype'] . "";
			if (!($query_insideGlasstype = $mysqli->query($getinsideGlasstype)))
				throw new Exception($mysqli->error);
			$row_query_insideGlasstype = $query_insideGlasstype->fetch_assoc();
			if (!empty($row_query_insideGlasstype['name']))
				$option['insideGlasstype'] = $row_query_insideGlasstype['name'];
			else
				$option['insideGlasstype'] = "";
			$getcolorcode = "SELECT colorcode FROM sapcercolor WHERE colourid=" . $option['spacerColor'] . "";
			if (!($query_colorcode = $mysqli->query($getcolorcode)))
				throw new Exception($mysqli->error);
			$row_color = $query_colorcode->fetch_assoc();
			if (!empty($row_color['colorcode']))
				$option['colorcode'] = $row_color['colorcode'];
			else
				$option['colorcode'] = "";
			$getsafetyType = "SELECT name FROM paneloption_safety WHERE safetyid=" . $option['safetyType'] . "";
			if (!($query_safetyType = $mysqli->query($getsafetyType)))
				throw new Exception($mysqli->error);
			$row_safetyType = $query_safetyType->fetch_assoc();
			if (!empty($row_safetyType['name']))
				$option['safetyType'] = $row_safetyType['name'];
			else
				$option['safetyType'] = "";
		}
		if ($type == 'style') {
			$getimageid = "SELECT imageid FROM paneloption_frametype WHERE frametypeid='" . $option['frametypeid'] . "'";
			if (!($query_image = $mysqli->query($getimageid)))
				throw new Exception($mysqli->error);
			$row_imageid = $query_image->fetch_assoc();
			$option['imageid'] = $row_imageid['imageid'];
		}
		if ($type == 'frametype' ||  $type == 'style') {
			$getframecat = "SELECT category FROM famecategory WHERE famecategoryid=" . $option['category'] . "";
			if (!($query_fcat = $mysqli->query($getframecat)))
				throw new Exception($mysqli->error);
			$row_fcat = $query_fcat->fetch_assoc();
			if (!empty($row_fcat['category']))
				$option['famecategory'] = $row_fcat['category'];
			else
				$option['famecategory'] = "";
		}
		array_push($options, $option);
	}
	$query->free();
	$pageContent = "files/templates/paneloptionlist.htm";
}
$mysqli->close();
include("files/templates/templateadmin.htm");
exit(0);
