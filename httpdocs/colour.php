<?php
	
error_reporting(E_ERROR | E_WARNING | E_PARSE);
	
# includes
include("files/constants.php");
include("files/library/session.php");
include("files/library/formlib.php");
include("files/library/common.php");
include("files/library/passwordlib.php");

# handle cancel
if(isset($_REQUEST['cancel'])) {
	header("Location: colour.php");
	exit(0);
}

# connect to database
$mysqli = new mysqli($gDBHost, $gDBUser, $gDBPassword, $gDBName);
if($mysqli->connect_errno)
	throw new Exception($mysqli->connect_error, $mysqli->connect_errno);
$mysqli->set_charset('UTF');

# check session
$adminId = SessionCheck($mysqli);


$agent = null;
$agentId = 0;
if(intval($_GET['id']) > 0) {
	$colourid = intval($_GET['id']);
	$querySQL = "SELECT * FROM colours WHERE colourid = ". $mysqli->real_escape_string($colourid);
	if(!($query = $mysqli->query($querySQL)))
		throw new Exception($mysqli->error);
	$colr = $query->fetch_assoc();
	$query->free();
	if($colr['colourid'] != $colourid)
		RedirectTo($mysqli, "colour.php");
}


if(isset($_REQUEST["save"])) { # process form
	$errors = array();
	$updateFields = array();

	$requiredFields = array('colourname', 'colorcode');
	foreach($requiredFields as $field) {
		if(!$_POST[$field])
			$errors[$field] = 'Required';
	}
	
	if(count($errors) == 0)	{ # no errors so save it
		if($colourid == null) { # create new colour
			$querySQL = "INSERT INTO colours () VALUES ()";
		
			if(!($query = $mysqli->query($querySQL)))
				throw new Exception($mysqli->error);
			$colourid = $mysqli->insert_id;
		}
		
		$fieldNames = array("colourname","colorcode","swatchid");
		$date="'".date('Y-m-d H:i:s')."'";
		foreach($fieldNames as $field)
			array_push($updateFields, "$field = '". $mysqli->real_escape_string($_POST[$field]). "'");
  array_push($updateFields, "updated_at = ". $date);
		
		$querySQL = "UPDATE colours SET ". implode(', ', $updateFields). " WHERE colourid = $colourid";
				if(!($query = $mysqli->query($querySQL)))
			throw new Exception($mysqli->error);

	/*	# process image (if available)
		if($_FILES['image']['tmp_name'] != '') {
			if(!move_uploaded_file($_FILES['image']['tmp_name'], $gProfilePhotoDir.$_POST['profilecode'].".png"))
				throw new Exception("Unable to create ". $gProfilePhotoDir.$_POST['profilecode'].".png");
		}
		*/

		
		RedirectTo($mysqli, "colours.php");
	}
	else
		$errorMessage = "<div class=\"alert alert-danger\" role=\"alert\">Please correct the indicated errors</div>";
}

#get swatch category

$querySQL_swatch = "SELECT * FROM colours_swatch";
	if(!($query = $mysqli->query($querySQL_swatch)))
		throw new Exception($mysqli->error);
		while($row_swatch= $query->fetch_assoc()){
			$swatch[]=$row_swatch;
		}
	//$swatch = $querySQL_swatch->fetch_assoc();
	$query->free();

# render page	
$form = new Form($_POST);
$pageContent = "files/templates/colour.htm";
include("files/templates/templateadmin.htm");

# done
$mysqli->close();
exit(0);



