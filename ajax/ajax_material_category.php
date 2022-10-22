<?php ob_start();
session_start();
include('../includes/functions.php');

$getCat=$db->joinquery("SELECT category,famecategoryid FROM famecategory WHERE material_tag='".$_POST['materialCategory']."'");
while($row=mysqli_fetch_assoc($getCat)){
	
	$list[]=$row;
}
echo json_encode($list);