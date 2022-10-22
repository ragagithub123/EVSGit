<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
# includes
include("files/constants.php");
include("files/library/session.php");
include("files/library/formlib.php");
include("files/library/common.php");
include("files/library/passwordlib.php");
# handle cancel
if (isset($_REQUEST['cancel'])) {
	header("Location: agents.php");
	exit(0);
}
# connect to database
$mysqli = new mysqli($gDBHost, $gDBUser, $gDBPassword, $gDBName);
if ($mysqli->connect_errno)
	throw new Exception($mysqli->connect_error, $mysqli->connect_errno);
$mysqli->set_charset('UTF');
# check session
$adminId = SessionCheck($mysqli);
# load agent if exists
$agent = null;
$agentId = 0;
if (intval($_GET['id']) > 0) {
	$agentId = intval($_GET['id']);
	$querySQL = "SELECT * FROM agent WHERE agentid = " . $mysqli->real_escape_string($agentId);
	if (!($query = $mysqli->query($querySQL)))
		throw new Exception($mysqli->error);
	$agent = $query->fetch_assoc();
	$query->free();
	if ($agent['agentid'] != $agentId)
		RedirectTo($mysqli, "agents.php");
}
if (isset($_REQUEST["save"])) { # process form
	$errors = array();
	$updateFields = array();
	$requiredFields = array('firstname', 'lastname', 'businessname', 'unitnum', 'street', 'suburb', 'city');
	foreach ($requiredFields as $field) {
		if (!$_POST[$field])
			$errors[$field] = 'Required';
	}
	if (!ValidEmailAddress($_POST['email']))
		$errors['email'] = 'Missing/Invalid';
	if ($agentId == 0 || $_POST['password'])
		if (!ValidPassword($_POST['password']))
			$errors['password'] = 'New password must be 8 - 40 characters including one number & uppercase letter';
	if (count($errors) == 0) { # no errors so save it
		if ($agentId == null) { # create new agent
			$querySQL = "INSERT INTO agent () VALUES ()";
			if (!($query = $mysqli->query($querySQL)))
				throw new Exception($mysqli->error);
			$agentId = $mysqli->insert_id;
		}
		$newloc = $_POST['unitnum'] . "," . $_POST['street'] . "," . $_POST['suburb'] . "," . $_POST['city'];
		$location = str_replace(' ', '%20', $newloc);
		$res = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address='" . $location . "'&key=xxxxxxxxxx");
		$result_arr = json_decode($res);
		$lat = (isset($result_arr->results[0]->geometry->location->lat) && !empty($result_arr->results[0]->geometry->location->lat)) ? $result_arr->results[0]->geometry->location->lat : ' 0.00';
		$lng = (isset($result_arr->results[0]->geometry->location->lng) && !empty($result_arr->results[0]->geometry->location->lng)) ? $result_arr->results[0]->geometry->location->lng : '0.00';
		$fieldNames = array("businessname", "firstname", "lastname", "email", "unitnum", "street", "suburb", "city", "postcode", "phone", "status");
		foreach ($fieldNames as $field)
			array_push($updateFields, "$field = '" . $mysqli->real_escape_string($_POST[$field]) . "'");
		array_push($updateFields, "enabled = " . intval($_POST['enabled']));
		array_push($updateFields, "latitude = " . $lat);
		array_push($updateFields, "longitude = " . $lng);
		// if (empty($updateFields['labourrate']) || $updateFields['labourrate'] == NULL) $updateFields['labourrate'] = '75';
		if ($_POST['password']) {
			if ($agent == null) {
				$salt = rand(10000, 99999) . rand(10000, 99999) . rand(10000, 99999) . rand(10000, 99999);
				array_push($updateFields, "passwordsalt = '$salt'");
			} else
				$salt = $agent['passwordsalt'];
			array_push($updateFields, "passwordhash = '" . HashPassword($_POST['password'], $salt) . "'");
		}
		if ($_POST['labourrate'] == '') {
			array_push($updateFields, "labourrate = '75'");
		} else {
			array_push($updateFields, "labourrate = '" . $_POST['labourrate'] . "'");
		}
		if ($_POST['outoftownrate'] == '') {
			array_push($updateFields, "outoftownrate = '0.00'");
		} else {
			array_push($updateFields, "outoftownrate = '" . $_POST['outoftownrate'] . "'");
		}
		if ($_POST['milagerate'] == '') {
			array_push($updateFields, "milagerate = '0.00'");
		} else {
			array_push($updateFields, "milagerate = '" . $_POST['milagerate'] . "'");
		}
		if ($_POST['maxlocations'] == '') {
			array_push($updateFields, "maxlocations = '5'");
		} else {
			array_push($updateFields, "maxlocations = '" . $_POST['maxlocations'] . "'");
		}
		$querySQL = "UPDATE agent SET " . implode(', ', $updateFields) . " WHERE agentid = $agentId";
		// echo $querySQL;
		// die();
		if (!($query = $mysqli->query($querySQL)))
			throw new Exception($mysqli->error);
		# process image (if available)
		if ($_FILES['image']['tmp_name'] != '') {
			if (!move_uploaded_file($_FILES['image']['tmp_name'], $gSignaturePhotoDir . $agentId . ".png")) {
				throw new Exception("Unable to create " . $gSignaturePhotoDir . $agentId . ".png");
			}
		}
		if ($_FILES['logo']['tmp_name'] != '') {
			if (!move_uploaded_file($_FILES['logo']['tmp_name'], $gLogoPhotoDir . $agentId . ".png"))
				throw new Exception("Unable to create " . $gLogoPhotoDir . $agentId . ".png");
		}
		RedirectTo($mysqli, "agents.php");
	} else
		$errorMessage = "<div class=\"alert alert-danger\" role=\"alert\">Please correct the indicated errors</div>";
}
# render page	
$form = new Form($_POST);
$pageContent = "files/templates/agent.htm";
include("files/templates/templateadmin.htm");
# done
$mysqli->close();
exit(0);
