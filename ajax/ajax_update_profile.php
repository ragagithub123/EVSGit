<?php
include('../includes/functions.php');
$db->upd_rec('panel',array('profileid' =>$_POST['profileid']),"panelid = '".$_POST['panelid']."'");
echo 'success';
/*$getprof=$db->sel_rec('profiles','profilename',"profileid = '".$_POST['profileid']."'");
$row_profile=mysqli_fetch_array($getprof);
echo $row_profile['profilename'];*/
?>