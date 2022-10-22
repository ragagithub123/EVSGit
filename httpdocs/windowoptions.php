<?php
	
# includes
include("files/constants.php");
include("files/library/session.php");
include("files/library/formlib.php");
include("files/library/common.php");

if(isset($_REQUEST['cancel'])) {
	header("Location: ". $_SERVER['PHP_SELF']);
	exit(0);
}

# connect to database
$mysqli = new mysqli($gDBHost, $gDBUser, $gDBPassword, $gDBName);
if($mysqli->connect_errno)
	throw new Exception($mysqli->connect_error, $mysqli->connect_errno);
$mysqli->set_charset('UTF');

$adminId = SessionCheck($mysqli);

if(isset($_REQUEST["id"])) { 
	$form = new Form($_POST);	

	if($_REQUEST['id'] != 'add') {
		$id = intval($_REQUEST["id"]);
		$querySQL = "SELECT * FROM window_option WHERE windowoptionid = $id";
		if(!($query = $mysqli->query($querySQL)))
			throw new Exception($mysqli->error);
		$option = $query->fetch_assoc();
		$query->free();
		if($option['windowoptionid'] != $_REQUEST["id"])
			throw new Exception("Invalid argument");
	}
	else {
		$id = "add";
		$option = array('name' => '', 'value' => '');
	}

	if(isset($_REQUEST['save'])) {
		$fields = array();
		$errors = array();
		if(trim($_REQUEST['name']) == '')
			$errors['name'] = 'Required';
		else
			array_push($fields, "name = '". $mysqli->real_escape_string($_REQUEST['name']). "'");
		if(!is_numeric($_REQUEST['value']))
			$errors['value'] = 'Invalid / Required';
		else
			array_push($fields, "value = ". floatval($_REQUEST['value']));
			
				if(!is_numeric($_REQUEST['hours']))
			$errors['hours'] = 'Invalid / Required';
		else
			array_push($fields, "hours = ". floatval($_REQUEST['hours']));
				if(!is_numeric($_REQUEST['displayorder']))
			$errors['displayorder'] = 'Invalid / Required';
		else
			array_push($fields, "displayorder = ". floatval($_REQUEST['displayorder']));

		if(count($errors) == 0) { # save it
			if($_REQUEST['id'] == 'add') { # create new option
				$querySQL = "INSERT INTO window_option () VALUES ()";
				if(!($query = $mysqli->query($querySQL)))
					throw new Exception($mysqli->error);
				$id = $mysqli->insert_id;
			}
			
			
			

			# update option
			array_push($fields, "isdefault = ". intval($_REQUEST['default']));
			$querySQL = "UPDATE window_option SET ". implode(",", $fields). " WHERE windowoptionid = $id";
			if(!($query = $mysqli->query($querySQL)))
				throw new Exception($mysqli->error);
				
				
					# process image (if available)
		if($_FILES['image']['tmp_name'] != '') {
			if(!move_uploaded_file($_FILES['image']['tmp_name'], $gWindowOptionPhotoDir.$id.".png"))
				throw new Exception("Unable to create ". $gWindowOptionPhotoDir.$id.".png");
		}
			

			# done
			header("Location: ". $_SERVER['PHP_SELF']);
			$mysqli->close();
			exit(0);
		}
		else
			$message = "<div class=\"alert alert-danger\" role=\"alert\">Please correct the indicated errors</div>";			
	}

	$pageContent = "files/templates/windowoptionedit.htm";
}
else {
	$querySQL = "SELECT * FROM window_option ORDER BY name";
	if(!($query = $mysqli->query($querySQL)))
		throw new Exception($mysqli->error);
	$options = array();
	while($option = $query->fetch_assoc())
		array_push($options, $option);
	$query->free();
	
	$pageContent = "files/templates/windowoptionlist.htm";
}

$mysqli->close();
include("files/templates/templateadmin.htm");	
exit(0);
