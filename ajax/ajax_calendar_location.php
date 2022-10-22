<?php ob_start();
session_start();
include('../includes/functions.php');
$location = $_POST['location'];
$loc_details =$db->joinquery("SELECT location.locationid,location.photoid,location.alarm_Type,location.booking_notes,location.`unitnum`,location.`street`,location.`suburb`,location.`city`,location.booking_date,location.booking_end_date,time(location.booking_date) AS booking_time ,customer.firstname,customer.lastname,customer.phone FROM `location`,`customer` WHERE location.customerid=customer.customerid AND location.`locationid`=".$location."");
$row_details =mysqli_fetch_array($loc_details);
$loc=$row_details['unitnum'].",".$row_details['street'];
	if(!empty($row_details['suburb']))
	{
			$loc.=",".$row_details['suburb'];
	}

?>

 <div class="retro-header">
   <div class="retro-date"><?php echo date('H:i',strtotime($row_details['booking_time']));?></div>
   <div class="retro-address"><?php echo $loc;?>
   		<?php
  			if($row_details['alarm_Type']=="Measure and Quote"){
         echo '<img src="images/Appointment_MeasureQuote.png" style="width:25px; height:25px" />';
        }else if($row_details['alarm_Type']=="Installation"){
         echo '<img src="images/Appointment_Install.png" style="width:25px; height:25px" />';
        }else if($row_details['alarm_Type']=="Inspect"){
         echo '<img src="images/Appointment_Inspect.png" style="width:25px; height:25px" />';
        }
			?>
  </div>
 </div>
 <div class="retro-body">
 <input type="hidden" id="locationid" value="<?php echo $row_details['locationid'];?>" />
   <img src="<?php echo $gPhotoURL.$row_details['photoid'].".jpg";?>">
   <p class="appointed-dropdown">Name: <?php echo $row_details['firstname']." ".$row_details['lastname'];?>
		 <a class="btn" id="edit-apptmnt-btn">Edit Appointment</a>
		 <div class="timepick_popup">
      <div class="form-group">
       StartDate:<input id="datetimepicker" type="text" class="form-control datetimepicker" value="<?php echo $row_details['booking_date'];?>">
         </div>
         <div class="form-group">
       EndDate:<input id="datetimepicker" type="text" class="form-control datetimepicker enddate" value="<?php echo $row_details['booking_end_date'];?>">
         </div>

         <div class="notes-note">
          <div class="form-group">
             <img src="images/Appointment_MeasureQuote.png" style="width:20px; height:20px; float:left;">
             <label class="cust_checkbox2">Measure and Quote
                   <input type="radio" name="alarm_type" value="Measure and Quote" <?php if($row_details['alarm_Type']=="Measure and Quote"){?> checked="checked" <?php } ?>>
                   <span class="checkmark alarm"></span>
                 </label>
              <!--<input type="radio" name="measure"> Measure and Quote-->
             </div>
             <div class="form-group" name="alarm_type">
             <img src="images/Appointment_Install.png" style="width:20px; height:20px; float:left;">
              <label class="cust_checkbox2">Installation
              	<input type="radio" name="alarm_type" value="Installation" <?php if($row_details['alarm_Type']=="Installation"){?> checked="checked" <?php } ?>>
                <span class="checkmark alarm"></span>
              </label>
              <!--<input type="radio" name="measure"> Installation-->
             </div>
	            <div class="form-group">
	            <img src="images/Appointment_Inspect.png" style="width:20px; height:20px; float:left;">
	            <label class="cust_checkbox2">Inspect

	                 <input type="radio" name="alarm_type" value="Inspect" <?php if($row_details['alarm_Type']=="Inspect"){?> checked="checked" <?php } ?>>
	                 <span class="checkmark alarm"></span>
	               </label>
              <!--<input type="radio" name="measure"> Installation-->
             </div>
             <div class="form-group">
           <label>Notes</label>
              <textarea class="form-control" rows="2" placeholder="Measure and Quote" id="booking_notes"><?php echo $row_details['booking_notes'];?> </textarea>
             </div>
         </div>
         <div class="datepick_btns">
						<a href="" id="save"> save <i class="fa fa-clock-o"></i></a>
					<!--	<a href="#" id="done">Done <i class="fa fa-check"></i></a>
						<a href="#" id="cancel">Remove</a>-->
         </div>
     </div>
	 </p>
   <p>Full address: <?php echo $loc;?></p>
   <p>Phone : <?php echo $row_details['phone'];?></p>
   <textarea class="form-control" rows="4" placeholder="Notes"><?php echo $row_details['booking_notes'];?></textarea>
   <br>
   <a href="customer-portal.php?id=<?php echo base64_encode($row_details['locationid']);?>" target="_blank" class="retro-submit-btn">Open Jobcard</a>

 </div>

 <script>
		$('#edit-apptmnt-btn').click(function(){
			//$(this).siblings('.timepick_popup').toggle();
			$('.timepick_popup').toggle();
		});
	
						
 </script>
