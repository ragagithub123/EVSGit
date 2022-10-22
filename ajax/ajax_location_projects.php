<?php ob_start();
session_start();
include ('../includes/functions.php');
$projectid = $_POST['projectid'];
if($_POST['status'] == 1){

    $db->joinquery("UPDATE location SET projectid='".$projectid."' WHERE locationid='".$_POST['locationid']."'");

    $getpjt = $db->joinquery("SELECT project_name,project_date FROM location_projects WHERE projectid='".$projectid."'");
   $rowpjt = mysqli_fetch_array($getpjt);
   echo $rowpjt['project_name']."@".$rowpjt['project_date'];

}

if($_POST['status'] == 2){

    $db->joinquery("UPDATE location_projects SET project_name='".$_POST['pjtname']."',project_date='".$_POST['pjtdate']."' WHERE projectid='".$projectid."'");

    $db->joinquery("UPDATE location SET projectid=".$projectid." WHERE locationid='".$_POST['locationid']."'");

}
