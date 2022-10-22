<?php ob_start();
session_start();
include('../includes/functions.php');
if($_POST['status']==1){
	$getpdt=$db->joinquery("SELECT window_extras.*,products.* FROM window_extras,products WHERE window_extras.productid=products.productid AND window_extras.extraid='".$_POST['extraid']."'");
	$pdtcostid="prodcutcost";
	$quantityid="quantity";
	$hoursid="hours";
	$costid="total_cost";
	$labourid="labourtotal";
	$total_extraid="total_extra";
	$getlocationid=$db->joinquery("SELECT room.locationid,window_extras.windowid FROM window_extras,window,room WHERE window_extras.windowid=window.windowid AND window.roomid=room.roomid AND window_extras.extraid='".$_POST['extraid']."'");
	  $row_locid=mysqli_fetch_array($getlocationid);
	$locationid=$row_locid['locationid'];
	
}
else
{
	 $getpdt=$db->joinquery("SELECT * FROM products WHERE productid='".$_POST['productid']."'");
		$pdtcostid="prodcutcost_add";
		$quantityid="quantity_add";
		$hoursid="hours_add";
		$costid="total_cost_add";
		$labourid="labourtotal_add";
		$total_extraid="total_extra_add";
		$getlocationid=$db->joinquery("SELECT room.locationid FROM window,room WHERE window.roomid=room.roomid AND window.windowid='".$_POST['windowid']."'");
	  $row_locid=mysqli_fetch_array($getlocationid);
			$locationid=$row_locid['locationid'];

}
$rowpdt=mysqli_fetch_array($getpdt);
$list_windowid=$rowpdt['windowid'];
if($rowpdt['imageid']==0)
$rowpdt['imageid']=$rowpdt['productid'];
	
	if($_POST['status']==1){
			$getlabourrate=$db->joinquery("SELECT location_margins.locationid,location_margins.labourrate,location_margins.productmargin FROM window,room,location_margins WHERE window.roomid=room.roomid AND room.locationid=location_margins.locationid AND window.windowid='".$rowpdt['windowid']."'");
	   $rowlabourrate=mysqli_fetch_array($getlabourrate);

	}
	else{
		 	$getlabourrate=$db->joinquery("SELECT location_margins.locationid,location_margins.labourrate,location_margins.productmargin FROM window,room,location_margins WHERE window.roomid=room.roomid AND room.locationid=location_margins.locationid AND window.windowid='".$_POST['windowid']."'");
	   $rowlabourrate=mysqli_fetch_array($getlabourrate);

	}
	if(mysqli_num_rows($getlabourrate)==0){
		 $getlabourrate=$db->joinquery("SELECT labourrate,evsmargin,igumargin,productmargin,agenttravelrate as travelrate FROM agent WHERE agentid='".$_SESSION['agentid']."'");
		 $rowlabourrate=mysqli_fetch_array($getlabourrate);
		$db->joinquery("INSERT INTO location_margins(`locationid`,`evsmargin`,`igumargin`,`productmargin`,`labourrate`,`travelrate`)VALUES('$locationid','".$rowlabourrate['evsmargin']."','".$rowlabourrate['igumargin']."','".$rowlabourrate['productmargin']."','".$rowlabourrate['labourrate']."','".$rowlabourrate['travelrate']."')");
	}
	#producttotal=product rate=WSvalue* agent->productmargin
	$cost=$rowlabourrate['productmargin'] * $rowpdt['wsvalue'];
	
	# Labourtotal=(agent/labourrate*product/hours) 
 # Total=(Labourtotal + producttotal)*quantity
	$Labourtotal=($rowlabourrate['labourrate']*$rowpdt['hours']);

if($_POST['status']==1){
	$Total=$rowpdt['cost'];
 $quantity=$rowpdt['quantity'];
}
else{
	   $Total=($Labourtotal + $cost)*1;
				$quantity=1;

}
if($_POST['status']==1){
	$list_windowid=$list_windowid;
}
else{
	$list_windowid="";
}
echo $list_windowid."@";
?>
<table class="table borderless vertical-middle">
<tr>
<td></td>
 <td><?php if(file_exists($gWindowOptionProductsDir.$rowpdt['imageid'].".png")){?>
 <img src="<?php echo $gWindowOptionProductsURL.$rowpdt['imageid'].".png";?>" style="width:50px;" id="safty-image"><?php }?></td>
</tr>
<tr>
 <td><?php echo $rowpdt['description'];?></td>
</tr>
<tr>
<td>Unitcost</td>
<td>
	<div class="table-inp-flx">
		<span>$</span>
 	<input type="text" class="form-control no-inp" name="productcost" id="<?php echo $pdtcostid;?>" value="<?php echo $cost;?>" readonly="readonly" style="width:60px"/>
 </div>
</td>
</tr>
<tr>
<td></td>
 <td><a href="<?php echo $rowpdt['linkURL'];?>" target="_blank">More info</a></td>
</tr>
<tr>
<td>Quantity</td>
 <td>
 	<div class="table-inp-flx">
  	<input type="text" class="form-control" name="quantity" id="<?php echo $quantityid;?>" value="<?php echo $quantity;?>" autofocus="autofocus" style="width:60px"/>&nbsp;
			<span><?php echo $rowpdt['unittag'];?></span> 
  </div>
 </td>
</tr>

<tr>
<td>Hours</td>
 <td>
 	<div class="table-inp-flx">
  	<input type="text" class="form-control no-inp" name="hours" id="<?php echo $hoursid;?>" value="<?php echo $rowpdt['hours'];?>" readonly="readonly" style="width:60px"/> &nbsp;
   <span>h</span>
  </div>
 </td>
</tr>
<tr>
<td>Labour Total</td>
 <td>
 	<div class="table-inp-flx">
  	<span>$</span>
   <input type="text" class="form-control no-inp" name="labourtotal" id="<?php echo $labourid;?>" value="<?php echo $Labourtotal;?>" readonly="readonly" style="width:60px"/>
 	</div>
 </td>
</tr>
<tr>
<td><input type="hidden" id="<?php echo $costid;?>" value="<?php echo $Total;?>"/></td>
<td><input type="hidden" id="labour_add" value="<?php echo $rowlabourrate['labourrate'];?>" /></td>

</tr>
<tr>
 <td>Total</td> 
 <td>
 <span>$</span><span id="<?php echo $total_extraid;?>"><?php echo $Total;?></span>
 </td>
</tr>
<?php
 if($_POST['status']==1){
		echo '<tr>
<td><input type="hidden" id="extraid" value="'.$_POST['extraid'].'">
<input type="hidden" id="windowid_up" value="'.$rowpdt['windowid'].'">


</td>
 <td><input type="button" name="add" value="UPDATE" id="update-extra-button" class="btn-primary" /></td>
</tr>';

	}
	else{
?>
<tr>
<td></td>
 <td><input type="button" name="add" value="ADD" id="add-extra-button" class="btn-primary" /></td>
</tr>
<?php } ?>
</table>

