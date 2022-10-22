<?php ob_start();
session_start();
include('../includes/functions.php');
$getpdt=$db->joinquery("SELECT * FROM products WHERE supplierid='".$_POST['supplierid']."'");
while($rowpdt=mysqli_fetch_array($getpdt)){
	$rowpdt['image']=$gSupplierProdcutsURL.$rowpdt['productid'].".png";
	$list[]=$rowpdt;
}
echo json_encode($list);
?>


