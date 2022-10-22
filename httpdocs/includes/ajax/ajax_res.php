<?php
include('includes/functions.php');
$sales_data = $db->sel_rec("sales_data", "*","id = '".$_POST['cust_name']."'");
if(mysql_num_rows($sales_data)>0)
{
	 while($row_cst=mysql_fetch_array($sales_data))
		{
			 echo ' <table class="table"><tr><td>Address</td><td><textarea name="address" id="address" rows="5" cols="15" style="resize:none" class="form-control" readonly>'.$row_cst['Address'].'</textarea></td></tr>

        <tr><td>AveragePrice</td><td><input type="text" name="avgprice" id="avgprice" class="form-control" autocomplete="off" value="'.$row_cst['AvePrice'].'" readonly></td></tr>
        <tr><td>TotalPrice</td><td><input type="text" name="totalprice" id="totalprice" class="form-control" autocomplete="off" value="'.$row_cst['TotalPrice'].'" readonly></td></tr>
        <tr><td>Product</td><td><input type="text" name="product" id="product" class="form-control" autocomplete="off" value="'.$row_cst['Product'].'" readonly></td></tr>
        <tr><td>Date</td><td><input type="text"  id="datepicker"  name="datepicker" autocomplete="off" class="form-control" value="'.$row_cst['Date'].'" readonly><span class="calnder"> <i class="far fa-calendar-alt"></i></span></td></tr>
								<tr><td>Value</td><td><input type="text"  id="value"  name="value" autocomplete="off" class="form-control" ></td></tr>
        <tr><td></td><td><input type="submit" value="Save" class="btn btn-primary"></td></tr></table>';
		}
}

?>