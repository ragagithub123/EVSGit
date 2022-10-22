<?php ob_start();
session_start();
include('../includes/functions.php');
$db->joinquery("UPDATE window SET materialCategory='".$_POST['frame']."',windowtypeid='".$_POST['typeid']."' WHERE windowid='".$_POST['windo']."'");
echo '<img src="'.$gwindowURL.$_POST['typeid'].".png".'">';