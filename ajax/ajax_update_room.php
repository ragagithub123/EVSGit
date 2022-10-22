<?php

ob_start();
session_start();
include('../includes/functions.php');
$db->joinquery("UPDATE room SET name='".$_POST['roomname']."' WHERE roomid='".$_POST['roomid']."'");
echo 'success';
?>