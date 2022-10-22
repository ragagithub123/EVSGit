<?php ob_start();
session_start();
date_default_timezone_set("NZ");
include('../includes/functions.php');
$getdetails = $db->joinquery("SELECT location.`locationid`,location.`photoid`,location.`unitnum`,location.`street`,location.`suburb`,location.`city`,location.`jobstatusid`,location.`locationstatusid`,location.`booking_date`,location.`booking_end_date`,location.`booking_status`,location.`booking_notes`,location.`alarm_Type`,customer.customerid,customer.firstname,customer.lastname,customer.email,customer.phone FROM location,customer WHERE location.customerid=customer.customerid AND location.`locationid`=" . $_POST['locationid'] . "");
$row_cards = mysqli_fetch_array($getdetails);
$loc = $row_cards['unitnum'] . "," . $row_cards['street'];
if (!empty($row_cards['suburb'])) {
  $loc .= "," . $row_cards['suburb'];
}
$loc .= "," . $row_cards['city'];
$customor = $row_cards['firstname'] . $row_cards['lastname'] . "," . $loc;
$get_attachments = $db->joinquery("SELECT * FROM location_attachments WHERE locationid='" . $_POST['locationid'] . "' ORDER BY datetime DESC");
$get_comments = $db->joinquery("SELECT * FROM location_comments WHERE locationid='" . $_POST['locationid'] . "'");
$quoteId = $_POST['locationid'] . "-" . hash('sha256', $_POST['locationid'] . $gQuoteHashSecret);
$quoteURL = $gWebsite . "/quote/$quoteId";
$jobstatus = $db->joinquery("SELECT * FROM jobstatus");
$get_status = $db->joinquery("SELECT * FROM location_status");
$get_totalpanel = $db->joinquery("SELECT sum(window_type.numpanels) AS total_panels FROM room,window,window_type WHERE window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid AND room.locationid='" . $_POST['locationid'] . "'");
$row_quote = mysqli_fetch_array($get_totalpanel);
$get_selected_cnt = $db->joinquery("SELECT sum(window_type.numpanels) AS pdt_count FROM room,window,window_type WHERE window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid AND window.selected_product!='HOLD' AND room.locationid='" . $_POST['locationid'] . "'");
$row_selected = mysqli_fetch_array($get_selected_cnt);
if (empty($row_selected['pdt_count'])) {
  $total_selecteds = 0;
} else {
  $total_selecteds = $row_selected['pdt_count'];
}
if (empty($row_quote['total_panels'])) {
  $total_panesl = 0;
} else {
  $total_panesl = $row_quote['total_panels'];
}


?>
<div class="modal-content">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Location</h4>
  </div>
  <div class="modal-body">
    <div class="drag-pop-detail">
      <?php
      if ($row_cards['photoid'] != 0 && file_exists($DPhotoDir . "/" . $row_cards['photoid'] . ".jpg")) { ?><img src="<?php echo $DPhotoURL . $row_cards['photoid'] . ".jpg"; ?>" id="backgroud-image"><?php } else { ?>
        <img src="images/no-location.png" id="backgroud-image"><?php } ?>
      <!--<h4><?php //echo $customor;
              ?></h4>-->
      <input type="hidden" id="locationid" value="<?php echo $_POST['locationid']; ?>" />
      <div class="drag-pop-info">
        <div class="user-addrss">
          <h4><?php echo $customor; ?></h4>
          <p><?php echo $customor; ?></p>
          <p><?php echo $row_cards['phone']; ?></p>
          <p><?php echo $row_cards['email']; ?></p>

          <div class="notes-up">
            <div class="tile-inner-content tin_two" style="padding:0; margin-bottom: 12px;">
              <ul>
                <?php $time_date = date('d-m-Y H:i', strtotime($row_cards['booking_date']));
                $exp_time = explode(' ', $time_date);
                if ($row_cards['booking_date'] <= date('Y-m-d H:i:s')) {
                  $row_cards['booking_status'] = 0;
                }





                if (($row_cards['booking_status'] == 1) && $row_cards['booking_date'] != "0000-00-00 00:00:00") {


                ?>

                  <li>
                    <?php
                    if ($row_cards['alarm_Type'] == "Measure and Quote") {
                      echo '<img src="images/Appointment_MeasureQuote.png" style="width:25px; height:25px" />';
                    } else if ($row_cards['alarm_Type'] == "Installation") {
                      echo '<img src="images/Appointment_Install.png" style="width:25px; height:25px" />';
                    } else if ($row_cards['alarm_Type'] == "Inspect") {
                      echo '<img src="images/Appointment_Inspect.png" style="width:25px; height:25px" />';
                    }
                    ?>
                    <h6 class="waiting"><?php echo $exp_time[1]; ?> <span><?php echo $exp_time[0]; ?></span> <i class="fa fa-clock-o"></i></h6>
                  </li><?php } else if ($row_cards['booking_date'] != "0000-00-00 00:00:00") {

                        ?>
                  <li>
                    <?php

                        if ($row_cards['alarm_Type'] == "Measure and Quote") {
                          echo '<img src="images/Appointment_MeasureQuote.png" style="width:20px; height:20px" />';
                        } else if ($row_cards['alarm_Type'] == "Installation") {
                          echo '<img src="images/Appointment_Install.png" style="width:20px; height:20px" />';
                        } else if ($row_cards['alarm_Type'] == "Inspect") {
                          echo '<img src="images/Appointment_Inspect.png" style="width:20px; height:20px" />';
                        } ?>


                    <h6><?php echo $exp_time[1]; ?> <span><?php echo $exp_time[0]; ?></span> <i class="fa fa-check-circle-o"></i></h6>
                  </li><?php } ?>
                <li><img src="images/exclamation_small.png" style="width:20px; height:20px;"><?php echo $total_panesl; ?></li>

                <li><img src="images/check_small.png" style="width:20px; height:20px;"><?php echo $total_selecteds; ?></li>

              </ul>
            </div>
            <div class="notes-prgph"><?php echo $row_cards['booking_notes']; ?></div>

          </div>

        </div>

        <div class="card-list-menu">
          <ul class="card-list-o">
            <li><a href="#" class="btn-info cust-edit-btn" id="edit-btn-popup" data-toggle="modal" data-target="#myModalprop"><i class="fa fa-pencil"></i>EDIT</a></li>
            <!-- <li> <a id="datetimepicker" href="javascript:void(0)">
                            	<i class="fa fa-clock-o"></i> Due Date</a>
                            </li>-->

            <li class="date-pick-popup">
              <a href="javascript:void(0)"><i class="fa fa-clock-o"></i> Due Date</a>
              <div class="timepick_popup">
                <div class="form-group">
                  StartDate:<input id="datetimepicker" type="text" class="form-control datetimepicker" value="<?php echo $row_cards['booking_date']; ?>">
                </div>
                <div class="form-group">
                  EndDate:<input id="datetimepicker" type="text" class="form-control datetimepicker enddate" value="<?php echo $row_cards['booking_end_date']; ?>">
                </div>

                <div class="notes-note">
                  <div class="form-group">
                    <img src="images/Appointment_MeasureQuote.png" style="width:20px; height:20px; float:left;" />
                    <label class="cust_checkbox2">Measure and Quote

                      <input type="radio" name="alarm_type" value="Measure and Quote" <?php if ($row_cards['alarm_Type'] == "Measure and Quote") { ?> checked="checked" <?php } ?>>
                      <span class="checkmark alarm"></span>
                    </label>
                    <!--<input type="radio" name="measure"> Measure and Quote-->
                  </div>
                  <div class="form-group" name="alarm_type">
                    <img src="images/Appointment_Install.png" style="width:20px; height:20px; float:left;" />
                    <label class="cust_checkbox2">Installation

                      <input type="radio" name="alarm_type" value="Installation" <?php if ($row_cards['alarm_Type'] == "Installation") { ?> checked="checked" <?php } ?>>
                      <span class="checkmark alarm"></span>
                    </label>
                    <!--<input type="radio" name="measure"> Installation-->
                  </div>
                  <div class="form-group">
                    <img src="images/Appointment_Inspect.png" style="width:20px; height:20px; float:left;" />
                    <label class="cust_checkbox2">Inspect

                      <input type="radio" name="alarm_type" value="Inspect" <?php if ($row_cards['alarm_Type'] == "Inspect") { ?> checked="checked" <?php } ?>>
                      <span class="checkmark alarm"></span>
                    </label>
                    <!--<input type="radio" name="measure"> Installation-->
                  </div>
                  <div class="form-group">
                    <label>Notes</label>
                    <textarea class="form-control" rows="2" placeholder="Measure and Quote" id="booking_notes"> <?php echo $row_cards['booking_notes']; ?></textarea>
                  </div>
                </div>


                <div class="datepick_btns">

                  <a href="" id="save"> save <i class="fa fa-clock-o"></i></a>

                  <a href="#" id="done">Done <i class="fa fa-check"></i></a>
                  <a href="#" id="cancel">Remove</a>



                </div>
              </div>
            </li>


            <li><a href="manage-portal.php?id=<?php echo base64_encode($_POST['locationid']); ?>" target="_blank"><i class="fa fa-file-text-o"></i> Project</a></li>
            <li><a href="worksheet.php?id=<?php echo base64_encode($_POST['locationid']); ?>" target="_blank"><i class="fa fa-file-text-o"></i> Worksheet</a></li>
            <li><a href="cutting-list.php?id=<?php echo base64_encode($_POST['locationid']); ?>" target="_blank"><i class="fa fa-scissors"></i> Cut List</a></li>
            <li><a href="<?php echo $quoteURL; ?>" target="_blank"><i class="fa fa-quote-left"></i> Quote</a></li>

            <li class="edit-file" style="cursor:pointer;"><a>
                <form method="post" class="data-upload" enctype="multipart/form-data">
                  <i class="fa fa-paperclip edit-file">Attach</i>
                  <input type="file" class="add-file" style="display:none" data-id="<?php echo $_POST['locationid']; ?>">
                </form>
              </a>
            </li>





            <li><a href="#" class="move-btn"><i class="fa fa-arrows"></i> Move</a>
              <ul>
                <?php while ($row_status = mysqli_fetch_array($jobstatus)) {

                ?>
                  <li><input type="radio" name="enq" value="<?php echo $row_status['jobstatusid']; ?>" <?php if ($row_cards['jobstatusid'] == $row_status['jobstatusid']) { ?> checked="checked" <?php } ?> class="rad-status"> <span><?php echo $row_status['jobstatus']; ?></span></li>
                <?php } ?>
            </li>
            <li> <input type="radio" name="delete" value="<?php echo $_POST['locationid']; ?>" class="delte-rad"> <span>Delete</span></li>
            <li> <input type="radio" name="move" value="<?php echo $_POST['locationid']; ?>" class="rad-move"> <span>Move Account</span></li>
            <li class="email-plc"> <input type="text" class="form-control" placeholder="Email" id="emailid" /></li>
            <li class="email-plc"> <input type="button" value="Move" class="btn btn-block btn-primary" id="move_location_btn" /></li>
          </ul>
          </li>
          <li><a href="#"><i class="fa fa-print"></i> Invoice</a></li>
          <li><a href="summary.php?id=<?php echo base64_encode($_POST['locationid']); ?>" target="_blank"><i class="glyphicon glyphicon-search"></i> Summary</a></li>
          <li><a href="tracking-view.php?id=<?php echo base64_encode($_POST['locationid']); ?>" target="_blank"><i class="fa fa-map-marker"></i> Tracker</a></li>
          </ul>
        </div>
      </div>

      <!--<div class="user-addrss">
                	<p><?php //echo $customor;
                      ?></p>
                    <p><?php //echo $row_cards['phone'];
                        ?></p>
                    <p><?php //echo $row_cards['email'];
                        ?></p>
                </div>-->

      <!--<div class="drag-inner-sections">
                	<h4>Add to Card</h4>
                	<ul class="card-list-o">
                        <li><a href="#">Project Manager</a></li>
                        <li><a href="#">Cut List</a></li>
                        <li><a href="#">Invoice</a></li>
                        <li><i class="fa fa-clock-o"></i> Due Date</li>
                        <form method="post" class="data-upload" enctype="multipart/form-data">
                            <li class="edit-file"><i class="fa fa-paperclip edit-file"></i> Attachment</li>
																									
																												<input type="file" class="add-file" style="display:none" data-id="<?php //echo $_POST['locationid'];
                                                                                                                          ?>">
																										
																												</form>
                      
                       
                    </ul>
                    <div class="attachment-file">
                    	
                    </div>
                </div>-->

      <div class="drag-inner-sections">


        <h4><i class="fa fa-paperclip"></i> Attachments</h4>
        <span id="spn_atatch">
          <?php
          if (mysqli_num_rows($get_attachments) > 0) {
            while ($row_attach = mysqli_fetch_array($get_attachments)) {
              $attch_date = explode(" ", $row_attach['datetime']);
              echo '<div class="attchment-single">
                    	<!--<div class="attch_icon"><i class="fa fa-file-pdf-o"></i></div>-->
                        <div class="attch_details">';
              if (strlen($row_attach['attachment']) > 30) {
                $explode_file = explode('.', $row_attach['attachment']);
                $attach_file = substr($explode_file[0], 0, 20);
                $download_file = $attach_file . "..." . $explode_file[1];
              } else {
                $download_file = $row_attach['attachment'];
              }

              $folder = $gAttachmentDir."/".$_SESSION['agentid']."/".$row_attach['attachment'];
              if(!file_exists($folder))
               $openurl = "assets/attachments/" . $row_attach['attachment'];
               else
               $openurl ="assets/attachments/".$_SESSION['agentid']."/".$row_attach['attachment'];

             ?>

              <a href="<?php echo $openurl; ?>" target="_blank"><?php echo $download_file; ?></a></h5>


          <?php
              echo '<h6> Added on &nbsp;' . date('M d', strtotime($row_attach['datetime'])) . '&nbsp; at ' . date('g:i a', strtotime($attch_date[1])) . '</h6>
                        </div>
                        <div class="attch_btns">
																								<a href="javascript:void(0)" onclick="Down_del(' . $row_attach['attachmentid'] . ',' . $row_attach['locationid'] . ');">Delete</a>
                        <!--	<form method="post" class="data-upload" enctype="multipart/form-data">
                           <a href="javascript:void(0)" class="edit-file" data-id="' . $row_attach['attachmentid'] . '">Edit</a>
																									 <input type="hidden" class="MyTset">
																												<input type="file" class="upload-file" id="upload-file' . $row_attach['attachmentid'] . '">
																										
																												</form>-->
                        </div>
                    </div>';
            }
          }
          ?>

        </span>

      </div>

      <div class="drag-inner-sections">
        <h4><i class="fa fa-comment-o"></i>Recent Comments</h4>
        <div class="recent-comments">
          <input type="hidden" id="commentid" />
          <?php
          if (mysqli_num_rows($get_comments) > 0) {
            while ($row_cmmts = mysqli_fetch_array($get_comments)) {
              $cmmt_date = explode(" ", $row_cmmts['datetime']);
              echo '<div class="recent-comments-single" id="div' . $row_cmmts['commentid'] . '">
                        	<div class="recent-comment-user-image">
                            	<img src="https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y">
                            </div>
                            <div class="recent-comment-txt">
                            	<p> ' . nl2br($row_cmmts['comments']) . '</p>
                                <ul>
																																<li><a id="edit-comment" data-id="' . $row_cmmts['commentid'] . '" href="javascript:void(0)">EDIT</a></li>
                                	<li><i class="fa fa-calendar"></i> ' . date('d-m-Y', strtotime($cmmt_date[0])) . '</li>
                                    <li><i class="fa fa-clock-o"></i> ' . date('h:i a', strtotime($cmmt_date[1])) . '</li>
                                </ul>
                            </div>
                        </div>';
            }
          }
          ?>





        </div>
      </div>

      <div class="drag-inner-sections">
        <h4><i class="fa fa-comment-o"></i>Add Comments</h4>
        <div class="comment-single">
          <div class="form-group">

            <textarea class="form-control" rows="10" placeholder="Write a comment..." id="comments"></textarea>
          </div>
          <input type="button" class="btn btn-success" id="cmmt-btton" value="submit">
        </div>
      </div>
    </div>


  </div>
</div>



<div id="myModalprop" class="modal fade" role="dialog">

  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit Property Details</h4>
      </div>
      <div class="modal-body">
        <p>

        <form method="post" action="" enctype="multipart/form-data">
          <table class="table">
            <tr>
              <th>Image</th>
              <td>
                <?php if ($row_cards['photoid'] != 0 && file_exists($gPhotoDir . "/" . $row_cards['photoid'] . ".jpg")) {
                ?>
                  <img src="<?php echo $gPhotoURL . $row_cards['photoid'] . ".jpg"; ?>" class="img-responsive" style="width:100px;height:100px" id="userimage-edit">

                <?php

                } else { ?> <img class="img-responsive" style="width:100px;height:100px" id="userimage-edit"> <?php } ?><input type="file" name="locimage" id="locimage-edit" onchange="readURLEdit(this)" />
              </td>
            </tr>
            <tr>
              <th>Unitnum</th>
              <td><input type="text" name="unitnum" id="unitnum-edit" class="form-control" value="<?php echo $row_cards['unitnum']; ?>" /></td>
            </tr>
            <tr>
              <th>Street</th>
              <td><input type="text" name="street" id="street-edit" class="form-control" value="<?php echo $row_cards['street']; ?>" /></td>
            </tr>
            <tr>
              <th>Suburb</th>
              <td><input type="text" name="suburb" id="suburb-edit" class="form-control" value="<?php echo $row_cards['suburb']; ?>" /></td>
            </tr>
            <tr>
              <th>City</th>
              <td><input type="text" name="city" id="city-edit" class="form-control" value="<?php echo $row_cards['city']; ?>" /></td>
            </tr>
            <tr>
              <th>Customer Firstname</th>
              <td><input type="text" name="firstname" id="firstname-edit" class="form-control" value="<?php echo $row_cards['firstname']; ?>" /></td>
            </tr>
            <tr>
              <th>Customer Lastname</th>
              <td><input type="text" name="lastname" id="lastname-edit" class="form-control" value="<?php echo $row_cards['lastname']; ?>" /></td>
            </tr>
            <tr>
              <th>Customer Phone</th>
              <td><input type="text" name="phone" id="phone-edit" class="form-control" value="<?php echo $row_cards['phone']; ?>" /></td>
            </tr>
            <tr>
              <th>Customer Email</th>
              <td><input type="text" name="email" id="email-edit" class="form-control" value="<?php echo $row_cards['email']; ?>" /></td>
            </tr>

            <tr>
              <td style="border:none"><input type="hidden" id="loc-status" value="1" /></td>
            </tr>
            <tr>
              <td colspan="2"><input type="button" value="UPDATE" class="btn btn-primary" style="margin-left:50px;" onclick="updateproperty(<?php echo $_POST['locationid']; ?>,<?php echo $row_cards['customerid']; ?> )" />
              </td>
            </tr>
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