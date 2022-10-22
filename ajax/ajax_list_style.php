<?php ob_start();
session_start();
include('../includes/functions.php');
$getstyles=$db->joinquery("SELECT styleid,name FROM paneloption_style");
while($row_style=mysqli_fetch_assoc($getstyles))
{
	 $post[]=$row_style;
}
echo json_encode($post);
?>
