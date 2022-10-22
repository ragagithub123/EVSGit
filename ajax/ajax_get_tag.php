<?php ob_start();
session_start();
include('../includes/functions.php');
$gettag=$db->joinquery("SELECT unitTag FROM Units WHERE unitName='".$_POST['unit_name']."'");
$rowtag=mysqli_fetch_array($gettag);
echo $rowtag['unitTag'];
?>


