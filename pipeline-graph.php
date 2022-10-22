<?php
include('includes/functions.php');
include('templates/header.php');

?>

<body>

<section>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
            
                <div class="evs_head">
                    <div class="logo">
                        <img src="images/logo.png">
                    </div>
                    
                    <?php include('templates/menu.php');?>
                    
                </div><!-- ./evs_head -->
                
            </div>
        </div>
    </div>
</section>
    
<?php 
$sales_data = $db->sel_rec("sales_data", "*","type = 'pipeline'",'date_val','desc');

?>
<div class="container">
  <div class="col-sm-12 weekly-bar">
    <div class="col-sm-4 weekly-text">
      <span class="total-text"> Weekly</span>
      <span class="digits"><?php echo $pipe_total_weekly;?></span>
    </div>
    <div class="col-sm-4 weekly-ratio"> <span class="digits"><?php echo $average_ratio;?></span>  <span class="ratio">%</span></div>
    <div class="col-sm-4 weekily-prod"> <span class="digits"><?php echo $production_total_weekly;?></span> </div>
  </div>
  <div class="col-sm-12 graph-bar">
    <div id="container"  style="min-width: 310px; height: 400px; margin: 0 auto"></div>
  </div>
  <div class="col-sm-12 total-bar">
    <div class="col-sm-6 total-pipe">
      <span class="total-text"> Total</span>
      <span class="digits"> <?php echo $pipe_total_final;?></span>
    </div>

    <div class="col-sm-6 total-prod">
      <span class="total-text"> Total</span>
      <span class="digits"> <?php echo $producton_total;?> </span>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-12 form-bar">
      <div class="col-sm-6 form-pipe">
        <form action="" method="post" class="cont">
          <input type="hidden" name="action" value="insert_pipeline">
          <input type="text" name="pipeline"  maxlength="12" value="1" class="input-text qty" />
          <div class="button-container">
            <button class="cart-qty-plus" type="button" value="+">+</button>
            <button class="cart-qty-minus" type="button" value="-">-</button>
          </div>
          <div class="txts"><a data-toggle="modal" data-target="#myModal" href="#">Add New Sales</a><br> Sales</div>
          <div class="col-sm-12">
            <input class="btns blu" type="submit" name="save" value="Save">
          </div>
        </form>
      </div>
      <div class="col-sm-6 form-prod">
        <form action="" method="post" class="cont">
          <input type="hidden" name="action" value="insert_production">
          <input type="text" name="production" maxlength="12" value="1" class="input-text qty" />
          <div class="button-container">
            <button class="cart-qty-plus" type="button" value="+">+</button>
            <button class="cart-qty-minus" type="button" value="-">-</button>
          </div>
          <div class="txts"><a data-toggle="modal" data-target="#myModal1" href="#">Add Finished Sales </a><br> Units</div>
          <div class="col-sm-12">
            <input class="btns red" type="submit" name="save" value="Save">
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
 
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add new sales</h4>
      </div>
      <div class="modal-body">
        <p>
        <form method="post" action="" id="frmpipeline" onSubmit="return valid()">
        <input type="hidden" name="action" value="save-pipeline">
        <table class="table">
        <tr><td>Type</td><td><input type="text" name="type" id="type" value="pipeline" readonly class="form-control"></td></tr>
        <tr><td>Address</td><td><textarea name="address" id="address" rows="5" cols="15" style="resize:none" class="form-control"></textarea></td></tr>
        <tr><td>Customer</td><td><input type="text" name="customer" id="customer" class="form-control" autocomplete="off"></td></tr>
        <tr><td>Value</td><td><input type="text" name="value" id="value" class="form-control" autocomplete="off"></td></tr>
        <tr><td>AveragePrice</td><td><input type="text" name="avgprice" id="avgprice" class="form-control" autocomplete="off"></td></tr>
        <tr><td>TotalPrice</td><td><input type="text" name="totalprice" id="totalprice" class="form-control" autocomplete=""></td></tr>
        <tr><td>Product</td><td><input type="text" name="product" id="product" class="form-control" autocomplete="off"></td></tr>
        <tr><td>Date</td><td><input type="text"  id="datepicker"  name="datepicker" autocomplete="off" class="form-control"><span class="calnder"> <i class="far fa-calendar-alt"></i></span></td></tr>
        <tr><td></td><td><input type="submit" value="Save" class="btn btn-primary"></td></tr>
        </table>
        </form>
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<?php include("templates/footer.php");?>


<div id="myModal1" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add finished units</h4>
      </div>
      <div class="modal-body">
        <p>
        <form method="post" action=""  onSubmit="return valid1()">
        <input type="hidden" name="action" value="save-production">
        <table class="table">
        <tr><td>Type</td><td><input type="text" name="type" id="type" value="production" readonly class="form-control" ></td></tr>
        <tr><td>Customer</td><td><select name="customer" id="customer" onChange="getdata(this.value);" class="form-control"><option value="" >Please select customer</option>
        <?php if(mysqli_num_rows($sales_data)>0){while($row_cust=mysqli_fetch_array($sales_data))
								{
									  echo '<option value="'.$row_cust['id'].'">'.$row_cust['Customer'].'</option>';
								}}?>
        </select>
        </td></tr>
       
        </table>
         <span id="ajax-result"></span>
        </form>
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>



   

   