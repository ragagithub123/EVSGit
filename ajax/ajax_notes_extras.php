<?php ob_start();
session_start();
include('../includes/functions.php');
if($_POST['type']=="notes")
{
	 $db->upd_rec('window',array('notes'=>$_POST['value']),"windowid = '".$_POST['windowid']."'");

}
else
{
	 $db->upd_rec('window',array('extras'=>$_POST['value']),"windowid = '".$_POST['windowid']."'");

}
echo $_POST['value'];