<?php
include('../includes/functions.php');
$datetime=$_POST['datesend']." ".$_POST['timesend'];
$db->upd_rec('location',array($_POST['status']=>$datetime,'jobstatusid'=>$_POST['crnt_status']),"locationid = '".$_POST['locationid']."'");
echo $_POST['datesend']." ".$_POST['timesend']." ".$_POST['locationid'];
?>