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

if(!empty($_REQUEST['styleid'])){
	
	$guerycopy="SELECT `name`,`category` FROM `paneloption_style` WHERE styleid='".$_REQUEST['styleid']."'";
		if(!($querycopy = $mysqli->query($guerycopy)))
					throw new Exception($mysqli->error);
					$row_copy=$querycopy->fetch_assoc();
					$querySQL="INSERT INTO paneloption_frametype(name,category,imageid)VALUES('".$row_copy['name']."','".$row_copy['category']."','".$_REQUEST['styleid']."')";
					if(!($query = $mysqli->query($querySQL)))
					throw new Exception($mysqli->error);
				$frametypeid = $mysqli->insert_id;
				$queryUpdate="UPDATE paneloption_style SET frametypeid='$frametypeid' WHERE styleid='".$_REQUEST['styleid']."'";
					if(!($queryup = $mysqli->query($queryUpdate)))
				  throw new Exception($mysqli->error);
						header('Location:paneloptions.php?type=frametype');
				
	
}
else{
	
	   $styles=array();
				$queryStyleSql="SELECT `styleid`,`frametypeid`,`name`,`category` FROM `paneloption_style` ORDER BY name ASC";
				if(!($querystyle = $mysqli->query($queryStyleSql)))
					throw new Exception($mysqli->error);
				while($row = $querystyle->fetch_assoc()) {
					$getcategory="SELECT category FROM famecategory WHERE famecategoryid=".$row['category']."";
						if(!($query_cat = $mysqli->query($getcategory)))
						throw new Exception($mysqli->error);
						$row_cat = $query_cat->fetch_assoc();
						if(!empty($row_cat['category']))
						$row['category']=$row_cat['category'];
						else
						$row['category']="";
						$styles[]=$row;
				}
					
				$pageContent = "files/templates/panelcopy.htm";

	
}





$mysqli->close();
include("files/templates/templateadmin.htm");	
exit(0);
