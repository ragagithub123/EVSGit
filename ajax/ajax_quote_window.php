<?php ob_start();session_start();

include('../includes/functions.php');

$db->joinquery("UPDATE window SET quote_status='".$_POST['value']."' WHERE windowid='".$_POST['windowid']."'");
echo 'success';

?>