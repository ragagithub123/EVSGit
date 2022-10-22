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
	header("Location: profiles.php");
	exit(0);
}

# connect to database
$mysqli = new mysqli($gDBHost, $gDBUser, $gDBPassword, $gDBName);
if($mysqli->connect_errno)
	throw new Exception($mysqli->connect_error, $mysqli->connect_errno);
$mysqli->set_charset('UTF');

# check session
$adminId = SessionCheck($mysqli);

# load agent if exists
$agent = null;
$agentId = 0;
if(intval($_GET['id']) > 0) {
	$profileId = intval($_GET['id']);
	$querySQL = "SELECT * FROM profiles WHERE profileid = ". $mysqli->real_escape_string($profileId);
	if(!($query = $mysqli->query($querySQL)))
		throw new Exception($mysqli->error);
	$profile = $query->fetch_assoc();
	$query->free();
	if($profile['profileid'] != $profileId)
		RedirectTo($mysqli, "profiles.php");
}


if(isset($_REQUEST["save"])) { # process form
	$errors = array();
	$updateFields = array();

	$requiredFields = array('profilename', 'profilecode','pricelm');
	foreach($requiredFields as $field) {
		if(!$_POST[$field])
			$errors[$field] = 'Required';
	}
	
	if(count($errors) == 0)	{ # no errors so save it
		if($profileId == null) { # create new profile
			$querySQL = "INSERT INTO profiles () VALUES ()";
		
			if(!($query = $mysqli->query($querySQL)))
				throw new Exception($mysqli->error);
			$profileId = $mysqli->insert_id;
		}
		
		$fieldNames = array("profilename","profilecode","pricelm");
		$date="'".date('Y-m-d H:i:s')."'";
		foreach($fieldNames as $field)
			array_push($updateFields, "$field = '". $mysqli->real_escape_string($_POST[$field]). "'");
  array_push($updateFields, "updated_at = ". $date);
		
		$querySQL = "UPDATE profiles SET ". implode(', ', $updateFields). " WHERE profileid = $profileId";
				if(!($query = $mysqli->query($querySQL)))
			throw new Exception($mysqli->error);

		# process image (if available)
		if($_FILES['image']['tmp_name'] != '') {
			if(!move_uploaded_file($_FILES['image']['tmp_name'], $gProfilePhotoDir.$_POST['profilecode'].".png"))
				throw new Exception("Unable to create ". $gProfilePhotoDir.$_POST['profilecode'].".png");
		}
		

		
		RedirectTo($mysqli, "profiles.php");
	}
	else
		$errorMessage = "<div class=\"alert alert-danger\" role=\"alert\">Please correct the indicated errors</div>";
}

# render page	
$form = new Form($_POST);
$pageContent = "files/templates/profile.htm";
include("files/templates/templateadmin.htm");

# done
$mysqli->close();
exit(0);



