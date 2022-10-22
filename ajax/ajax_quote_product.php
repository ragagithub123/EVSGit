<?php ob_start();session_start();

include('../includes/functions.php');

$quote = $_POST['product'];

$sql ="UPDATE location SET $quote='".$_POST['value']."' WHERE locationid='".$_POST['locationid']."'";

$db->joinquery($sql);
echo $sql;

?>