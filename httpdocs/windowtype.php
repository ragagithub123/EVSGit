<?php
	
# includes
include("files/constants.php");
include("files/library/session.php");
include("files/library/formlib.php");
include("files/library/common.php");

# handle cancel
if(isset($_REQUEST['cancel'])) {
	header("Location: windowtypes.php");
	exit(0);
}

# connect to database
$mysqli = new mysqli($gDBHost, $gDBUser, $gDBPassword, $gDBName);
if($mysqli->connect_errno)
	throw new Exception($mysqli->connect_error, $mysqli->connect_errno);
$mysqli->set_charset('UTF');

# check session
$adminId = SessionCheck($mysqli);

# load window if exists
$windowTypeId = 0;
if(intval($_GET['id']) > 0) {
	$windowTypeId = intval($_GET['id']);
	$querySQL = "SELECT * FROM window_type WHERE windowtypeid = ". $mysqli->real_escape_string($windowTypeId);
	if(!($query = $mysqli->query($querySQL)))
		throw new Exception($mysqli->error);
	$window = $query->fetch_assoc();
	$query->free();
	if($window['windowtypeid'] != $windowTypeId)
		RedirectTo($mysqli, "windowtypes.php");
}


if(isset($_REQUEST["save"])) { # process form
	$errors = array();
	$updateFields = array();

	$requiredFields = array('name', 'numpanels');
	foreach($requiredFields as $field) {
		if(!$_POST[$field])
			$errors[$field] = 'Required';
	}
	
	if(count($errors) == 0)	{ # no errors so save it
		if($windowTypeId == 0) { # create new window
			$querySQL = "INSERT INTO window_type () VALUES ()";
			if(!($query = $mysqli->query($querySQL)))
				throw new Exception($mysqli->error);
			$windowTypeId = $mysqli->insert_id;
		}
		if(empty($_POST['NALU'])){
			 $NALU=0;
		}
		else{
			 $NALU=$_POST['NALU'];
		}
		if(empty($_POST['NTIM'])){
			 $NTIM=0;
		}
		else{
			 $NTIM=$_POST['NTIM'];
		}
		if(empty($_POST['RALU'])){
			 $RALU=0;
		}
		else{
			 $RALU=$_POST['RALU'];
		}
		if(empty($_POST['RTIM'])){
			 $RTIM=0;
		}
		else{
			 $RTIM=$_POST['RTIM'];
		}
		if(empty($_POST['RMET'])){
			 $RMET=0;
		}
		else{
			 $RMET=$_POST['RMET'];
		}
		
		
		$querySQL = "UPDATE window_type SET name = '". $mysqli->real_escape_string($_POST['name']). "', numpanels = ". intval($_POST['numpanels']). ",NALU=".$NALU.",NTIM=".$NTIM.",RALU=".$RALU.",RTIM=".$RTIM.",RMET=".$RMET." WHERE windowtypeid = ". $windowTypeId;
		if(!($query = $mysqli->query($querySQL)))
			throw new Exception($mysqli->error);
		
		# process image (if available)
		if($_FILES['image']['tmp_name'] != '') {
			if(!move_uploaded_file($_FILES['image']['tmp_name'], $gWindowTypePhotoDir.$windowTypeId.".png"))
				throw new Exception("Unable to create ". $gWindowTypePhotoDir.$windowTypeId.".png");
		}

		RedirectTo($mysqli, "windowtypes.php");
	}
	else
		$errorMessage = "<div class=\"alert alert-danger\" role=\"alert\">Please correct the indicated errors</div>";
}

# render page	
$form = new Form($_POST);

$pageContent = "files/templates/windowtype.htm";
include("files/templates/templateadmin.htm");

# done
$mysqli->close();
exit(0);



