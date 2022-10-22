<?php ob_start();
session_start();
include('../includes/functions.php');
$getcmmt=$db->joinquery("SELECT comments FROM location_comments WHERE commentid='".$_POST['commentid']."'");
$row_cmmts=mysqli_fetch_array($getcmmt);
echo $row_cmmts['comments'];
