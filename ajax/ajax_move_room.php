<?php

ob_start();
session_start();
include('../includes/functions.php');
$db->joinquery("UPDATE window SET roomid='".$_POST['roomid']."' WHERE windowid='".$_POST['windowid']."'");
echo 'success';
?>