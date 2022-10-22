<?php
	
# includes
include("files/constants.php");
include("files/library/session.php");
include("files/library/formlib.php");
include("files/library/common.php");

# connect to database
$mysqli = new mysqli($gDBHost, $gDBUser, $gDBPassword, $gDBName);
if($mysqli->connect_errno)
	throw new Exception($mysqli->connect_error, $mysqli->connect_errno);
$mysqli->set_charset('UTF');

$adminId = SessionCheck($mysqli);

# get list of params
$querySQL = "SELECT * FROM params WHERE paramid = 1";
if(!($query = $mysqli->query($querySQL)))
	throw new Exception($mysqli->error);
$params = $query->fetch_assoc();
$query->free();

$fieldNames = array('sdgglassrate' => 'SDG Glass Rate <span style="color:red">($p/m2)</span>', 'sguglassrate'=> 'SGU Glass Rate <span style="color:red">($p/m2)</span>','igux2glassrate'=>'IGUX2 Glass Rate <span style="color:red">($p/m2)</span>','igux3glassrate'=>'IGUX3 Glass Rate <span style="color:red">($p/m2)</span>','maxeglassrate' => 'MAXe Glass Rate<span style="color:red">($p/m2)</span>', 'xcleglassrate' => 'XCLe Glass Rate<span style="color:red">($p/m2)</span>', 'evsx2glassrate' => 'EVSx2 Glass Rate<span style="color:red">($p/m2)</span>', 'evsx3glassrate' => 'EVSx3 Glass Rate<span style="color:red">($p/m2)</span>', 'agentmargin' => 'Agent Margin <span style="color:red;">(%)</span>', 'labourrate' => 'Labour Rate <span style="color:red">($p/h)</span>', 'travelrate' => 'Travel Rate <span style="color:red">($p/km)</span>', 'dgmaterials' => 'DG Materials <span style="color:red">($p/lm)</span>', 'evsmaterials' => 'EVS Materials <span style="color:red">($p/lm)</span>');

if(isset($_REQUEST["save"])) { # process form
	$errors = array();
	$updateFields = array();

	foreach(array_keys($fieldNames) as $field) {
		if(!$_POST[$field])
			$errors[$field] = 'Required';
	}
	
	if(count($errors) == 0)	{ # no errors so save it
		foreach($fieldNames as $field => $label)
			array_push($updateFields, "$field = ". floatval($_POST[$field]));

		array_push($updateFields, "quotegreeting = '". $mysqli->real_escape_string($_POST['quotegreeting']). "'");
		array_push($updateFields, "quotedetails = '". $mysqli->real_escape_string($_POST['quotedetails']). "'");

		$querySQL = "UPDATE params SET ". implode(', ', $updateFields). " WHERE paramid = 1";
		if(!($query = $mysqli->query($querySQL)))
			throw new Exception($mysqli->error);

		$message = "<div class=\"alert alert-success\" role=\"alert\">Saved changes</div>";
	}
	else
		$message = "<div class=\"alert alert-danger\" role=\"alert\">Please correct the indicated errors</div>";
}

$mysqli->close();
$form = new Form($_POST);	
$pageContent = "files/templates/defaults.htm";
include("files/templates/templateadmin.htm");	
