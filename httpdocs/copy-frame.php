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
$querySQL = "SELECT * FROM paneloption_style WHERE `styleid` = '".$_POST['selectid']."'";
if(!($query = $mysqli->query($querySQL)))
 throw new Exception($mysqli->error);
	$stylevalue = $query->fetch_assoc();
	$frameSql="INSERT INTO paneloption_style(`frametypeid`,`name`,`styledgvalue`,`styleevsvalue`,`IGUlabour`,`EVSlabour`,`IGUassemble`,`EVSassemble`,`styleCategory`,`category`,`styleNotes`,`evsSpacer`,`retroSpacer`,`evsProfileTop`,`evsProfileSides`,`evsProfileRight`,`evsOutPanelThickness`,`evsOutPanelType`,`evsInPanelThickness`,`evsInPanelType`,`retroOutPanelThickness`,`retroOutPanelType`,`retroProfileLeft`,`retroProfileRight`,`evsProfileLeft`,`evsProfileBottom`,`evsGlassX`,`evsGlassY`,`evsProfileX`,`evsProfileY`,`retroProfileTop`,`retroProfileSides`,`retroProfileBottom`,`retroGlassX`,`retroGlassY`,`retroProfileX`,`retroProfileY`,`isdefault`)VALUES
('".$_POST['frametypeid']."','".$stylevalue['name']."','".$stylevalue['styledgvalue']."','".$stylevalue['styleevsvalue']."','".$stylevalue['IGUlabour']."','".$stylevalue['EVSlabour']."','".$stylevalue['IGUassemble']."','".$stylevalue['EVSassemble']."','".$stylevalue['styleCategory']."','".$stylevalue['category']."','".$stylevalue['styleNotes']."','".$stylevalue['evsSpacer']."','".$stylevalue['retroSpacer']."','".$stylevalue['evsProfileTop']."','".$stylevalue['evsProfileSides']."','".$stylevalue['evsProfileRight']."','".$stylevalue['evsOutPanelThickness']."','".$stylevalue['evsOutPanelType']."','".$stylevalue['evsInPanelThickness']."','".$stylevalue['evsInPanelType']."','".$stylevalue['retroOutPanelThickness']."','".$stylevalue['retroOutPanelType']."','".$stylevalue['retroProfileLeft']."','".$stylevalue['retroProfileRight']."','".$stylevalue['evsProfileLeft']."','".$stylevalue['evsProfileBottom']."','".$stylevalue['evsGlassX']."','".$stylevalue['evsGlassY']."','".$stylevalue['evsProfileX']."','".$stylevalue['evsProfileY']."','".$stylevalue['retroProfileTop']."','".$stylevalue['retroProfileSides']."','".$stylevalue['retroProfileBottom']."','".$stylevalue['retroGlassX']."','".$stylevalue['retroGlassY']."','".$stylevalue['retroProfileX']."','".$stylevalue['retroProfileY']."','".$stylevalue['isdefault']."')";
if(!($query = $mysqli->query($frameSql)))
 throw new Exception($mysqli->error);

					$insert_id=$mysqli->insert_id;

$querySQL ="SELECT styleid,name FROM paneloption_style WHERE styleid='$insert_id'";
if(!($query = $mysqli->query($querySQL)))
 throw new Exception($mysqli->error);
$new = $query->fetch_assoc();
  echo '<h5><a href="paneloptions.php?type=style&id='.$new['styleid'].'" target="_blank">'.$new['name'].'</a><a href="javascript:void(0)"><i class="fa fa-times delcopy" onclick="delcopy('.$_POST['frametypeid'].','.$new['styleid'].')"></i></a></h5>';


