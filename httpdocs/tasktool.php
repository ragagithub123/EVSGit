<?php
	
# includes
include("files/constants.php");
include("files/library/session.php");
include("files/library/formlib.php");
include("files/library/common.php");

if(isset($_REQUEST['cancel'])) {
	header("Location: ". $_SERVER['PHP_SELF']."?type=". $_REQUEST["type"]);
	exit(0);
}

# connect to database
$mysqli = new mysqli($gDBHost, $gDBUser, $gDBPassword, $gDBName);
if($mysqli->connect_errno)
	throw new Exception($mysqli->connect_error, $mysqli->connect_errno);
$mysqli->set_charset('UTF');

$adminId = SessionCheck($mysqli);
$category = array();	
$tools = array();
$tasktool = array();
$taskmat =array();
$query_frametype ="SELECT `famecategoryid`,`category` FROM  famecategory";
if(!($query = $mysqli->query($query_frametype)))
		throw new Exception($mysqli->error);
	while($row_frame = $query->fetch_assoc()){
		$query_retrocnt = "SELECT COUNT(`name`) AS retotoolcount FROM taskTools WHERE `categoryid`='".$row_frame['famecategoryid']."' AND `type`='retro'";	
		if(!($query_retotool = $mysqli->query($query_retrocnt)))
		throw new Exception($mysqli->error);
		$row_rerto = $query_retotool->fetch_array();
		$row_frame['retrocount'] = $row_rerto['retotoolcount'];
			$query_evscnt = "SELECT COUNT(`name`) AS evstoolcount FROM taskTools WHERE `categoryid`='".$row_frame['famecategoryid']."' AND `type`='evs'";	
		if(!($query_evstool = $mysqli->query($query_evscnt)))
		throw new Exception($mysqli->error);
		$row_evs = $query_evstool->fetch_array();
		$row_frame['evscount'] = $row_evs['evstoolcount'];
		$query_retrocnt = "SELECT COUNT(`name`) AS retomatcount FROM taskMaterials WHERE `categoryid`='".$row_frame['famecategoryid']."' AND `type`='retro'";	
		if(!($query_retotool = $mysqli->query($query_retrocnt)))
		throw new Exception($mysqli->error);
		$row_rerto = $query_retotool->fetch_array();
		$row_frame['retromatcount'] = $row_rerto['retomatcount'];
			$query_evscnt = "SELECT COUNT(`name`) AS evsmatcount FROM taskMaterials WHERE `categoryid`='".$row_frame['famecategoryid']."' AND `type`='evs'";	
		if(!($query_evstool = $mysqli->query($query_evscnt)))
		throw new Exception($mysqli->error);
		$row_evs = $query_evstool->fetch_array();
		$row_frame['evsmatcount'] = $row_evs['evsmatcount'];
		
		
		array_push($category, $row_frame);
		
	}
		
	$query->free();
	
	$query_tools ="SELECT * FROM  Tools";
if(!($query = $mysqli->query($query_tools)))
		throw new Exception($mysqli->error);
	while($row_tools = $query->fetch_assoc())
		array_push($tools, $row_tools);
		
	$query->free();
	
	if(isset($_REQUEST['type'])){
	
	$type = $_REQUEST['type'];
	
	$query_frame ="SELECT `category` FROM  famecategory WHERE famecategoryid='".$_REQUEST['catid']."'";
if(!($query = $mysqli->query($query_frame)))
		throw new Exception($mysqli->error);
	 $row_frame = $query->fetch_assoc();

	$query_tasktools ="SELECT * FROM taskTools WHERE type='".$type."' AND categoryid='".$_REQUEST['catid']."'";
if(!($query = $mysqli->query($query_tasktools)))
		throw new Exception($mysqli->error);
	while($row_tasktools = $query->fetch_assoc())
		array_push($tasktool, $row_tasktools);
		
	$query->free();
	
		$query_taskmat ="SELECT * FROM taskMaterials WHERE type='".$type."' AND categoryid='".$_REQUEST['catid']."'";
if(!($query = $mysqli->query($query_taskmat)))
		throw new Exception($mysqli->error);
	while($row_taskmat = $query->fetch_assoc())
		array_push($taskmat, $row_taskmat);
		
	$query->free();
	
		$pageContent = "files/templates/tasktooledit.htm";

	
	}

else{
	
	 	$pageContent = "files/templates/tasktool.htm";

	
}


$mysqli->close();
include("files/templates/templateadmin.htm");	
exit(0);
