<?php ob_start();
session_start();
include('../includes/functions.php');
$getlocid = $db->joinquery("SELECT locationid FROM location WHERE locationSearch='" . mysqli_escape_string($db->connection,$_POST['locationtext']) . "' AND agentid ='" . $_SESSION['agentid'] . "'");
$locid = mysqli_fetch_array($getlocid);
$Locationid = $locid['locationid'];
$quoteId = $Locationid . "-" . hash('sha256', $Locationid . $gQuoteHashSecret);
$quoteURL = $gWebsite . "/quote/$quoteId";
if ($_POST['statusCode'] == 'customer-portal') {
  echo $Locationid;
  exit;
} else if ($_POST['statusCode'] == 0) {
  if (!empty($_SESSION['agentid'])) {
    if ($_POST['locationstatusid'] == 'All') {
      $getprop = $db->joinquery("SELECT `locationid`,`unitnum`,`street`,`suburb`,`city` FROM location WHERE city='" . $_POST['city'] . "' AND agentid=" . $_SESSION['agentid'] . " AND (locationstatusid!='3' AND locationstatusid!='5')");
    } else {
      $getprop = $db->joinquery("SELECT `locationid`,`unitnum`,`street`,`suburb`,`city` FROM location WHERE city='" . $_POST['city'] . "' AND locationstatusid=" . $_POST['locationstatusid'] . " AND agentid=" . $_SESSION['agentid'] . "");
    }
    echo '<select class="form-control" id="list-prop" name="list-prop" onchange="getproprty(this.value)">
											<option value="select">Select Property</option>';
    if (mysqli_num_rows($getprop) > 0) {
      while ($row_prop = mysqli_fetch_array($getprop)) {
        $loc = $row_prop['unitnum'] . "," . $row_prop['street'];
        if (!empty($row_prop['suburb'])) {
          $loc .= "," . $row_prop['suburb'];
        }
        $loc .= "," . $row_prop['city'];
        echo '<option value=' . $row_prop['locationid'] . '>' . $loc . '</option>';
      }
    }
    echo '</select>';
  } else {
    header('Location:logout.php');
  }
} else if ($_POST['statusCode'] == 2) {
  if ($_POST['locationstatusid'] == 'All') {
    $getprop = $db->joinquery("SELECT `locationid`,`unitnum`,`street`,`suburb`,`city` FROM location WHERE `street` LIKE '%" . $_POST['search_text'] . "%' OR `suburb` LIKE '%" . $_POST['search_text'] . "%' OR `city` LIKE '%" . $_POST['search_text'] . "%' OR `unitnum` LIKE '%" . $_POST['search_text'] . "%' AND agentid=" . $_SESSION['agentid'] . " AND (locationstatusid!='3' AND locationstatusid!='5')");
  } else {
    $getprop = $db->joinquery("SELECT `locationid`,`unitnum`,`street`,`suburb`,`city` FROM location WHERE `street` LIKE '%" . $_POST['search_text'] . "%' OR `suburb` LIKE '%" . $_POST['search_text'] . "%' OR `city` LIKE '%" . $_POST['search_text'] . "%' OR `unitnum` LIKE '%" . $_POST['search_text'] . "%'AND locationstatusid=" . $_POST['locationstatusid'] . " AND agentid=" . $_SESSION['agentid'] . "");
  }
  echo '<select class="form-control" id="list-prop" name="list-prop" onchange="getproprty(this.value)">
											<option value="select">Select Property</option>';
  if (mysqli_num_rows($getprop) > 0) {
    while ($row_prop = mysqli_fetch_array($getprop)) {
      $loc = $row_prop['unitnum'] . "," . $row_prop['street'];
      if (!empty($row_prop['suburb'])) {
        $loc .= "," . $row_prop['suburb'];
      }
      $loc .= "," . $row_prop['city'];
      echo '<option value=' . $row_prop['locationid'] . '>' . $loc . '</option>';
    }
  }
  echo '</select>';
} else if ($_POST['statusCode'] == 1) {
  $getwindowoption = $db->joinquery("SELECT * FROM products WHERE agentid='" . $_SESSION['agentid'] . "'");
  if (mysqli_num_rows($getwindowoption) == 0) {
    $getwindowoption = $db->joinquery("SELECT * FROM products WHERE agentid='1'");
  }
  while ($window_op = mysqli_fetch_array($getwindowoption)) {
    if ($window_op['imageid'] == 0)
      $window_op['imageid'] = $window_op['productid'];
    $windowOption[] = $window_op;
  }
  $get_details = $db->joinquery("SELECT location.`unitnum`,location.`street`,location.`suburb`,location.`city`,`location`.locationstatusid,location.notes,location.photoid,location.`status1`,location.`status2`,location.status3,location.status4,location.status5,location.status6,location.status7,location.status8,location.status9,location.status10,location.status11,location.status12,location.status13,location.status14,location.status15,location.hs_overheadpower,location.hs_siteaccess_notes,location.hs_vegetation_notes,location.hs_heightaccess_notes,location.hs_heightaccess_photoid,location.hs_childrenanimals_notes,location.hs_traffic_notes,location.hs_weather_notes,location.hs_worksite_notes,location.distance,location.travel_rate,customer.customerid,customer.firstname,customer.lastname,customer.email,customer.phone,agent.labourrate,photo.width,photo.height FROM location LEFT JOIN photo ON location.photoid=photo.photoid ,agent,customer WHERE location.agentid=agent.agentid AND location.customerid=customer.customerid AND location.`locationid`=" . $Locationid . "");
  $get_status = $db->joinquery("SELECT * FROM location_status");
  $row_details = mysqli_fetch_array($get_details);
  $loc = $row_details['unitnum'] . "," . $row_details['street'];
  if (!empty($row_details['suburb'])) {
    $loc .= "," . $row_details['suburb'];
  }
  //$jobstatus=array('Enquiry: New','Enquiry: Reply','Quote: Booked','Quote: Follow up','Quote: Rejected','Quote: Go','Units: Materials','Units: Production','Units: Shipping','Install: Booked','Install: Underway','Install: Sign Off','Invoice','Archive');
  $jobstatus = $db->joinquery("SELECT * FROM jobstatus");
  //$get_window=$db->joinquery("SELECT room.`roomid`,room.`name` AS room_name,window.notes,window.extras,window.windowid,window.costsdg,window.costmaxe,window.costxcle,window.costevsx2,window.costevsx3,window.windowtypeid,window.status,window.`selected_product`,window.`selected_price`,window.`selected_hours`,window_type.name ,window_option.value AS extravalue,window_option.windowoptionid FROM room,window,window_type,window_window_option,window_option WHERE room.roomid=window.roomid AND window.windowtypeid=window_type.windowtypeid AND  window_window_option.windowid=window.windowid AND window_window_option.windowoptionid = window_option.windowoptionid AND room.locationid=".$Locationid." GROUP BY window.windowid ORDER BY room_name ASC");
  $get_window = $db->joinquery("SELECT room.`roomid`,room.`name` AS room_name,window.notes,window.extras,window.windowid,window.`costSGU`,window.`costIGUX2`,window.`costIGUX3`,window.costsdg,window.costmaxe,window.costxcle,window.costevsx2,window.costevsx3,window.windowtypeid,window.status,window.`selected_product`,window.`selected_price`,window.`selected_hours`,window_type.name FROM room,window,window_type WHERE window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid  AND room.locationid=" . $Locationid . " GROUP BY window.windowid ORDER BY room_name ASC");
  $get_totalpanel = $db->joinquery("SELECT sum(window_type.numpanels) AS total_panels FROM room,window,window_type WHERE window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid AND room.locationid='" . $Locationid . "'");
  $row_quote = mysqli_fetch_array($get_totalpanel);
  $get_selected_cnt = $db->joinquery("SELECT sum(window_type.numpanels) AS pdt_count FROM room,window,window_type WHERE window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid AND window.selected_product!='HOLD' AND room.locationid='" . $Locationid . "'");
  $row_selected = mysqli_fetch_array($get_selected_cnt);
?>
  <div class="property_dtls">
    <div class="pro_inner_one">
      <h4 class="main_title">Property Details <a data-toggle="modal" data-target="#myModalprop" href="#" class="btn-info cust-edit-btn">EDIT</a></h4>
      <span id="ajax-property"></span>
      <div class="pro_inner_one_details" id="curnt-details">
        <div class="pro_inner_one_pic">
          <?php
          if ($row_details['photoid'] != 0) {
          ?>
            <img src="<?php echo $gPhotoURL . $row_details['photoid']; ?>.jpg" class="img-responsive">
          <?php
          } ?>
        </div>
        <div class="pro_inner_one_list">
          <ul>
            <li>Property Address</li>
            <li><?php echo $loc; ?></li>
            <li><?php echo $row_details['city']; ?></li>
            <li><?php echo $row_details['firstname'] . " " . $row_details['lastname']; ?></li>
            <li><?php echo $row_details['phone']; ?></li>
            <li><?php echo $row_details['email']; ?></li>
          </ul>
          <b><a href="<?php echo $quoteURL; ?>" style="font-size:18px;" target="_blank">Preview Quote</a></b>
        </div>
      </div>
      <!-- ./pro_inner_one_details -->
    </div><!-- ./pro_inner_one -->
    <!-- Modal property-->
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
                  <th style="border:none">Image</th>
                  <td><?php
                      if ($row_details['photoid'] != 0) {
                      ?>
                      <img src="210.5.53.32/assets/<?php echo $row_details['photoid']; ?>.jpg" class="img-responsive" style="width:100px;height:100px" id="userimage-edit">
                    <?php
                      } else { ?> <img class="img-responsive" style="width:100px;height:100px" id="userimage-edit"> <?php } ?><input type="file" name="locimage" id="locimage-edit" onchange="readURLEdit(this)" />
                  </td>
                </tr>
                <tr>
                  <th>Unitnum</th>
                  <td><input type="text" name="unitnum" id="unitnum-edit" class="form-control" value="<?php echo $row_details['unitnum']; ?>" /></td>
                </tr>
                <tr>
                  <th>Street</th>
                  <td><input type="text" name="street" id="street-edit" class="form-control" value="<?php echo $row_details['street']; ?>" /></td>
                </tr>
                <tr>
                  <th>Suburb</th>
                  <td><input type="text" name="suburb" id="suburb-edit" class="form-control" value="<?php echo $row_details['suburb']; ?>" /></td>
                </tr>
                <tr>
                  <th>City</th>
                  <td><input type="text" name="city" id="city-edit" class="form-control" value="<?php echo $row_details['city']; ?>" /></td>
                </tr>
                <tr>
                  <th>Customer Firstname</th>
                  <td><input type="text" name="firstname" id="firstname-edit" class="form-control" value="<?php echo $row_details['firstname']; ?>" /></td>
                </tr>
                <tr>
                  <th>Customer Lastname</th>
                  <td><input type="text" name="lastname" id="lastname-edit" class="form-control" value="<?php echo $row_details['lastname']; ?>" /></td>
                </tr>
                <tr>
                  <th>Customer Phone</th>
                  <td><input type="text" name="phone" id="phone-edit" class="form-control" value="<?php echo $row_details['phone']; ?>" /></td>
                </tr>
                <tr>
                  <th>Customer Email</th>
                  <td><input type="text" name="email" id="email-edit" class="form-control" value="<?php echo $row_details['email']; ?>" /></td>
                </tr>
                <!-- <tr><th>Quote Status</th><td>
                              <?php
                              /*	while($row_status=mysqli_fetch_array($get_status))
																														{
																															 if($row_status['locationstatusid']==$row_details['locationstatusid'])
																															{
																																 echo $row_status['status'].'<input type="radio" name="loc_status-edit" value='.$row_status['locationstatusid'].' checked="checked"/>&nbsp;&nbsp;';
																															}
                               else
																															{
																																		 echo $row_status['status'].'<input type="radio" name="loc_status-edit" value='.$row_status['locationstatusid'].' />&nbsp;&nbsp;';
																															}
																														}*/
                              ?>
                              <td>
                              </tr>-->
                <tr>
                  <td colspan="2"><input type="button" value="UPDATE" class="btn btn-primary" style="margin-left:50px;" onclick="updateproperty(<?php echo $Locationid; ?>,<?php echo $row_details['customerid']; ?> )" /></td>
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
    <!-- End Modal property-->
    <div class="pro_inner_two">
      <div class="pro_inner_two_details">
        <h4 class="main_title"><span>Job Status</span> Time Date</h4>
        <ul>
          <?php
          $i = 0;
          while ($row_status = mysqli_fetch_array($jobstatus)) {
            $y = $i + 1;
            $status = 'status' . $y;
            if ($row_details[$status] != "0000-00-00 00:00:00") {
              $time_date = explode(' ', $row_details[$status]);
              $date = date('d-m-Y', strtotime($time_date[0]));
              $time = date("H:i", strtotime($time_date[1]));
            } else {
              $date = "00-00-000";
              $time = "00-00";
            }
          ?>
            <li>
              <label class="cust_checkbox" style="font-weight:bold"><b <?php if ($date != '00-00-000') { ?> style=" color:#000;" <?php } ?> id="p_status<?php echo $i; ?>"><?php echo $row_status['jobstatus']; ?></b><span class="sep" id="hr_time<?php echo $i; ?>" <?php if ($date != '00-00-000') { ?> style=" color:#000;" <?php } ?>><?php echo $time; ?> <?php echo $date; ?></span>
                <input type="radio" name="chk_stat" id="chk_stat<?php echo $i; ?>" onchange="generatetime(<?php echo $i; ?>,<?php echo $Locationid; ?>,'<?php echo $status; ?>')" <?php if ($date != '00-00-000') { ?> checked="checked" <?php } ?> value="<?php echo $row_status['jobstatusid']; ?>">
                <span class="checkmark"></span>
              </label>
            </li>
          <?php
            $i++;
          }
          ?>
        </ul>
      </div>
    </div><!-- ./pro_inner_two -->
  </div><!-- ./property_dtls -->
  </div><!-- ./col-lg-12 -->
  <div class="col-lg-12">
    <div class="health">
      <h4 class="main_title">Health and Safety</h4>
      <ul>
        <li>Overhead Power : <?php echo $row_details['hs_overheadpower']; ?></li>
        <li>Site Access : <?php echo $row_details['hs_siteaccess_notes']; ?></li>
        <li>Vegitation : <?php echo $row_details['hs_vegetation_notes']; ?></li>
        <li class="list_more">
          <span>Height Access : <?php echo $row_details['hs_heightaccess_notes']; ?></span>
          <span></span>
          <span><?php if ($row_details['hs_heightaccess_photoid'] != 0) { ?><span><img src="http://evsapp.nz/photos/<?php echo $row_details['hs_heightaccess_photoid']; ?>.jpg" class="img-responsive center-block"></span><?php } ?>
        </li>
        <li>Childern/Animals/Pests : <?php echo $row_details['hs_childrenanimals_notes']; ?></li>
        <li>Traffic/Pedestraints : <?php echo $row_details['hs_traffic_notes']; ?></li>
        <li>Dangerous Materials: </li>
        <li>Weather Exposure: <?php echo $row_details['hs_weather_notes']; ?></li>
        <li>Worksite: <?php echo $row_details['hs_worksite_notes']; ?></li>
      </ul>
    </div><!-- ./health -->
  </div><!-- ./col-lg-12 -->
  <div class="col-lg-12">
    <div class="measurev table_custom">
      <div class="table-responsive" id="div_window">
        <div class="loader3" style="display:none">
          <span></span>
          <span></span>
        </div>
        <table class="table cust-table cust-two-table">
          <thead>
            <tr>
              <th colspan="3">
                <div style="display:flex; justify-content:space-between;">
                  <h4 class="main_title">Window Sets</h4>
                  <h4 class="main_title"><img src="images/exclamation_small.png" style="width:25px; height:25px;">&nbsp<?php echo $row_quote['total_panels']; ?> panels quoted</h4>
                </div>
              </th>
              <th width="38%">
                <h4 class="main_title" id="spn-panels">&nbsp;Product Selected <img src="images/check_small.png" style="width:25px; height:25px;"> &nbsp; <?php echo $row_selected['pdt_count']; ?> panels selected</h4>
              </th>
              <th width="125">
                <h4 class="main_title">Quoted Price</h4>
              </th>
              <th width="90">
                <h4 class="main_title">Travel</h4>
              </th>
              <th width="90">
                <h4 class="main_title">Extra</h4>
              </th>
              <th width="80">
                <h4 class="main_title">Hours</h4>
              </th>
            </tr>
          </thead>
          <tbody>
            <?php
            if (mysqli_num_rows($get_window) > 0) {
              $i = 0;
              while ($row_window = mysqli_fetch_array($get_window)) {
                $getextra = $db->joinquery("SELECT sum(cost) AS extravalue FROM window_extras WHERE windowid=" . $row_window['windowid'] . "");
                if (mysqli_num_rows($getextra) == 0) {
                  $extravalue = 0;
                } else {
                  $row_extra = mysqli_fetch_array($getextra);
                  $extravalue = $row_extra['extravalue'];
                  if ($extravalue == NULL) {
                    $extravalue = 0;
                  }
                }
                $i++;
                //$total_price[]=$row_window['selected_price'];
                //	$total_hrs[]=$row_window['selected_hours'];
                $total_extra[] = $row_extra['extravalue'];
                $getlabour = $db->joinquery("SELECT panelid,SUM(dglabour) AS igulabour, SUM(evslabour) AS evslabour FROM panel WHERE windowid = " . $row_window['windowid'] . "");
                $row_labour = mysqli_fetch_array($getlabour);
                $gettravel = $db->joinquery("SELECT travel_status,distance,number_staff FROM location WHERE locationid='$Locationid'");
                $rowtravel = mysqli_fetch_array($gettravel);
                if ($rowtravel['travel_status'] == 0) {
                  $travelSGU = 0;
                  $travelIGUX2 = 0;
                  $travelIGUX3 = 0;
                  $travelEVSX3 = 0;
                  $travelEVSX2 = 0;
                } else {
                  $queryMargins = $db->joinquery("SELECT labourrate,travelrate FROM location_margins WHERE locationid='$Locationid'");
                  $margins = mysqli_fetch_array($queryMargins);
                  if (mysqli_num_rows($queryMargins) == 0) {
                    $queryMargins = $db->joinquery("SELECT labourrate,evsmargin,igumargin,productmargin,agenttravelrate as travelrate FROM agent WHERE agentid='" . $_SESSION['agentid'] . "'");
                    $margins = mysqli_fetch_array($queryMargins);
                    $db->joinquery("INSERT INTO location_margins(`locationid`,`evsmargin`,`igumargin`,`productmargin`,`labourrate`,`travelrate`)VALUES('$Locationid','" . $margins['evsmargin'] . "','" . $margins['igumargin'] . "','" . $margins['productmargin'] . "','" . $margins['labourrate'] . "','" . $margins['travelrate'] . "')");
                  }
                  $travelDaysEVS = ($row_labour['evslabour'] / (7 * $rowtravel['number_staff']));
                  $travelHoursEVS = ((($rowtravel['distance'] * 2) * $rowtravel['number_staff']) / 90) * $travelDaysEVS;
                  $travelEVSX2 = $travelEVSX3 = round((((($rowtravel['distance'] * 2) * $travelDaysEVS) * $margins['travelrate']) + ($travelHoursEVS * $margins['labourrate'])), 2);
                  $travelDaysIGU = ($row_labour['igulabour'] / (5 * $rowtravel['number_staff']));
                  $travelHoursIGU = ((($rowtravel['distance'] * 2) * $rowtravel['number_staff']) / 90) * $travelDaysIGU;
                  $travelSGU = $travelIGUX2 = $travelIGUX3 = round((((($rowtravel['distance'] * 2) * $travelDaysIGU) * $margins['travelrate']) + ($travelHoursIGU * $margins['labourrate'])), 2);
                  /*$travelDaysEVS=($row_labour['evslabour']/(7*$rowtravel['number_staff']));
																																						 $travelHoursEVS = ((($rowtravel['distance']*2)*$rowtravel['number_staff'])/60)*$travelDaysEVS;
																																						 $travelEVSX3 = $travelEVSX2 =round((((($rowtravel['distance']*2)*$travelDaysEVS)*$margins['travelrate'])+($travelHoursEVS*$margins['labourrate'])),2);
																																						 $travelDaysIGU=(($row_labour['igulabour']/7)*($rowtravel['number_staff']));
																																						 $travelHoursIGU = ((($rowtravel['distance']*2)*$rowtravel['number_staff'])/60)*$travelDaysIGU;
																																						 $travelSGU=$travelIGUX2=$travelIGUX3 =round((((($rowtravel['distance']*2)*$travelDaysIGU)*$margins['travelrate'])+($travelHoursIGU*$margins['labourrate'])),2);*/
                }
                if ($row_window['selected_product'] == "SGU" || $row_window['selected_product'] == "IGUX2" || $row_window['selected_product'] == "IGUx3" || $row_window['selected_product'] == "SDG" || $row_window['selected_product'] == "MAXe" || $row_window['selected_product'] == "XCLe") {
                  $total_travel_cost[] = round((((($rowtravel['distance'] * 2) * $travelDaysIGU) * $margins['travelrate']) + ($travelHoursIGU * $margins['labourrate'])), 2);;
                } else if ($row_window['selected_product'] == "EVSx2" || $row_window['selected_product'] == "EVSx3") {
                  $total_travel_cost[] = round((((($rowtravel['distance'] * 2) * $travelDaysEVS) * $margins['travelrate']) + ($travelHoursEVS * $margins['labourrate'])), 2);
                }
                /*	if($row_window['selected_product']=="SGU" || $row_window['selected_product']=="IGUX2" || $row_window['selected_product']=="IGUx3" || $row_window['selected_product']=="SDG"||$row_window['selected_product']=="MAXe" || $row_window['selected_product']=="XCLe"){
																																					$total_travel_cost[]=round((((($rowtravel['distance']*2)*$travelDaysIGU)*$margins['travelrate'])+($travelHoursIGU*$margins['labourrate'])),2);
																																				}
																																				else if($row_window['selected_product']=="EVSx2" || $row_window['selected_product']=="EVSx3"){
																																					 $total_travel_cost[]=round((((($rowtravel['distance']*2)*$travelDaysEVS)*$margins['travelrate'])+($travelHoursEVS*$margins['labourrate'])),2);
																																				}*/
            ?>
                <tr>
                  <td>
                    <?php echo $row_window['room_name']; ?> </td>
                  <td><?php
                      if ($row_window['windowtypeid'] != 0) {
                      ?>
                      <img src="http://evsapp.nz/assets/app/windowtypes/<?php echo $row_window['windowtypeid']; ?>.png" class="img-responsive" width="50" height="50">
                    <?php
                      } ?>
                  </td>
                  <td><?php echo $row_window['name']; ?></td>
                  <td>
                    <button type="button" class="btn" id="btn_HOLD<?php echo $i; ?>" onclick="gethourlabour(0,<?php echo $i; ?>,0,'HOLD',<?php echo $row_window['windowid']; ?>,0,<?php echo $row_labour['panelid']; ?>,<?php echo $row_window['roomid']; ?>)" <?php if ($row_window['selected_product'] == "HOLD") { ?>style="background-color:#17a2b8;" <?php } ?>>HOLD</button>
                    <button type="button" class="btn" id="btn_SGU<?php echo $i; ?>" onclick="gethourlabour(<?php echo $row_window['costsdg']; ?>,<?php echo $i; ?>,<?php echo $row_labour['igulabour']; ?>,'SGU',<?php echo $row_window['windowid']; ?>,<?php echo $travelSGU; ?>,<?php echo $row_labour['panelid']; ?>,<?php echo $row_window['roomid']; ?>)" <?php if ($row_window['selected_product'] == "SGU") { ?>style="background-color:#17a2b8;" <?php } ?>>SGU</button>
                    <button type="button" class="btn" id="btn_IGUX2<?php echo $i; ?>" onclick="gethourlabour(<?php echo $row_window['costmaxe']; ?>,<?php echo $i; ?>,<?php echo $row_labour['igulabour']; ?>,'IGUX2',<?php echo $row_window['windowid']; ?>,<?php echo $travelIGUX2; ?>,<?php echo $row_labour['panelid']; ?>,<?php echo $row_window['roomid']; ?>)" <?php if ($row_window['selected_product'] == "IGUX2") { ?>style="background-color:#17a2b8;" <?php } ?>>IGUX2</button>
                    <button type="button" class="btn" id="btn_IGUX3<?php echo $i; ?>" onclick="gethourlabour(<?php echo $row_window['costxcle']; ?>,<?php echo $i; ?>,<?php echo $row_labour['igulabour']; ?>,'IGUX3',<?php echo $row_window['windowid']; ?>,<?php echo $travelIGUX3; ?>,<?php echo $row_labour['panelid']; ?>,<?php echo $row_window['roomid']; ?>)" <?php if ($row_window['selected_product'] == "IGUX3") { ?>style="background-color:#17a2b8;" <?php } ?>>IGUX3</button>
                    <button type="button" class="btn" id="btn_EVSx2<?php echo $i; ?>" onclick="gethourlabour(<?php echo $row_window['costevsx2']; ?>,<?php echo $i; ?>,<?php echo $row_labour['evslabour']; ?>,'EVSx2',<?php echo $row_window['windowid']; ?>,<?php echo $travelEVSX2; ?>,<?php echo $row_labour['panelid']; ?>,<?php echo $row_window['roomid']; ?>)" <?php if ($row_window['selected_product'] == "EVSx2") { ?>style="background-color:#17a2b8;" <?php } ?>>EVSx2</button>
                    <button type="button" class="btn" id="btn_EVSx3<?php echo $i; ?>" onclick="gethourlabour(<?php echo $row_window['costevsx3']; ?>,<?php echo $i; ?>,<?php echo $row_labour['evslabour']; ?>,'EVSx3',<?php echo $row_window['windowid']; ?>,<?php echo $travelEVSX3; ?>,<?php echo $row_labour['panelid']; ?>,<?php echo $row_window['roomid']; ?>)" <?php if ($row_window['selected_product'] == "EVSx3") { ?>style="background-color:#17a2b8;" <?php } ?>>EVSx3</button>
                  </td>
                  <?php if ($row_window['selected_product'] == "SGU" || $row_window['selected_product'] == "SDG") {
                    $travel_value = $travelSGU;
                    $travel_amt[] = $travel_value;
                    $selected_price = $row_window['costsdg'];
                    $total_price[] = $selected_price;
                    $neval = $row_window['costsdg'] . "@" . $row_labour['igulabour'] . "@" . 'SGU' . "@" . $row_window['windowid'] . "@" . $row_window['panelid'] . "@" . $row_window['roomid'];
                    $selected_hr = $row_labour['igulabour'];
                    $total_hrs[] = $selected_hr;
                  } else if ($row_window['selected_product'] == "IGUX2" || $row_window['selected_product'] == "MAXe") {
                    $travel_value = $travelIGUX2;
                    $travel_amt[] = $travel_value;
                    $selected_price = $row_window['costmaxe'];
                    $total_price[] = $selected_price;
                    $neval = $row_window['costmaxe'] . "@" . $row_labour['igulabour'] . "@" . 'IGUX2' . "@" . $row_window['windowid'] . "@" . $row_window['panelid'] . "@" . $row_window['roomid'];
                    $selected_hr = $row_labour['igulabour'];
                    $total_hrs[] = $selected_hr;
                  } else if ($row_window['selected_product'] == "IGUX3" || $row_window['selected_product'] == "XCLe") {
                    $travel_value = $travelIGUX3;
                    $travel_amt[] = $travel_value;
                    $selected_price = $row_window['costxcle'];
                    $total_price[] = $selected_price;
                    $neval = $row_window['costxcle'] . "@" . $row_labour['igulabour'] . "@" . 'IGUX3' . "@" . $row_window['windowid'] . "@" . $row_window['panelid'] . "@" . $row_window['roomid'];
                    $selected_hr = $row_labour['igulabour'];
                    $total_hrs[] = $selected_hr;
                  } else if ($row_window['selected_product'] == "EVSx2") {
                    $travel_value = $travelEVSX2;
                    $selected_price = $row_window['costevsx2'];
                    $travel_amt[] = $travel_value;
                    $total_price[] = $selected_price;
                    $neval = $row_window['costevsx2'] . "@" . $row_window['evslabour'] . "@" . 'EVSx2' . "@" . $row_labour['windowid'] . "@" . $row_window['panelid'] . "@" . $row_window['roomid'];
                    $selected_hr = $row_labour['evslabour'];
                    $total_hrs[] = $selected_hr;
                  } else if ($row_window['selected_product'] == "EVSx3") {
                    $travel_value = $travelEVSX3;
                    $selected_price = $row_window['costevsx3'];
                    $travel_amt[] = $travel_value;
                    $total_price[] = $selected_price;
                    $neval = $row_window['costevsx3'] . "@" . $row_window['evslabour'] . "@" . 'EVSx3' . "@" . $row_labour['windowid'] . "@" . $row_window['panelid'] . "@" . $row_window['roomid'];
                    $selected_hr = $row_labour['evslabour'];
                    $total_hrs[] = $selected_hr;
                  } else {
                    $selected_price = 0;
                    $travel_value = 0;
                    $selected_hr = 0;
                  }
                  ?>
                  <td>$<span id='cost_val<?php echo $i; ?>'><?php echo $selected_price; ?></td>
                  <td>$<span id='travel_val<?php echo $i; ?>'><?php echo $travel_value; ?></span></td>
                  <td>$<span id='extra_val<?php echo $i; ?>'><?php echo $extravalue; ?></td>
                  <td><span id='hour_val<?php echo $i; ?>'><?php echo $selected_hr; ?></span></td>
                  <td><input type="hidden" name="update_arry[]" id="new_val<?php echo $i; ?>" value="<?php echo $neval; ?>" /></td>
                </tr>
            <?php
                $neval = "";
              }
            }
            /*		if(!empty($total_travel_cost)){
																															 $travel_amt=array_sum($total_travel_cost);
																														}else{
																															 $travel_amt=0;
																														}
																														if(!empty($total_price) &&!empty($total_extra)){
                              $total_amt=array_sum($total_price)+array_sum($total_extra)+$travel_amt;
                              }
																														else if(empty($total_extra)){
                              $total_amt=array_sum($total_price)+$travel_amt;
                              }
																														else{
                               $total_amt=array_sum($total_extra)+$travel_amt;
                              }*/
            if (count($total_price) == 0)
              $total_amt = 0.00;
            else
              $total_amt = array_sum($total_price);
            if (count($total_hrs) == 0)
              $total_hrs = 0;
            else
              $total_hrs = array_sum($total_hrs);
            if (count($travel_amt) == 0)
              $travel_amt = array_sum($travel_amt);
            else
              $travel_amt = array_sum($travel_amt);
            if (count($total_extra) == 0)
              $total_extra = 0;
            else
              $total_extra = array_sum($total_extra);
            ?>
            <tr class="table-total">
              <td colspan="4" align="center"><input type="button" class="btn btn-primary" id="updatebtn" value="Update" onclick="update(<?php echo $Locationid; ?>)" /></td>
              <td>$<span id="total_price"><?php echo $total_amt; ?></span></td>
              <td>$<span id="total_travel"><?php echo $travel_amt; ?></span></td>
              <td>$<span id="total_extra_spn"><?php echo $total_extra; ?></span></td>
              <td><span id="total_hrs"><?php echo $total_hrs; ?></span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div><!-- ./measure -->
  </div>
  <!-- ./col-lg-12 -->
  </div>
  </div>
  </section>
  <span id="ajax-load-result" style="display:none"></span>
  <span id="load-result">
    <?php
    mysqli_data_seek($get_window, 0);
    if (mysqli_num_rows($get_window) > 0) {
      while ($row_pann = mysqli_fetch_array($get_window)) {
        if ($row_pann['selected_product'] != 'HOLD') {
    ?>
          <section>
            <div class="container">
              <div class="row">
                <div class="col-lg-12">
                  <div class="panel_info_main">
                    <div class="window_sets">
                      <h4 class="main_title">Window Set</h4>
                      <p><?php echo $row_pann['room_name']; ?> <span><?php echo $row_pann['name']; ?></span></p>
                      <?php
                      if ($row_pann['windowtypeid'] != 0) {
                      ?>
                        <img src="http://evsapp.nz/assets/app/windowtypes/<?php echo $row_pann['windowtypeid']; ?>.png" class="img-responsive">
                      <?php
                      }
                      //$get_panel=$db->joinquery("SELECT panel.panelid,panel.width,panel.height,panel.measurement,panel.center,panel.panelnum,panel.profileid,panel.windowid,panel.`safetyid`,panel.`glasstypeid`,panel.`styleid`,panel.`conditionid`,panel.`astragalsid`,paneloption_style.`evsProfileTop`,paneloption_style.`evsProfileSides`,paneloption_style.`evsProfileBottom`,paneloption_style.`evsGlassX`,paneloption_style.`evsGlassY`,paneloption_style.`evsProfileX`,paneloption_style.`evsProfileY`,paneloption_style.`retroProfileTop`,paneloption_style.`retroProfileSides`,paneloption_style.`retroProfileBottom`,paneloption_style.`retroGlassX`,paneloption_style.`retroGlassY`,paneloption_style.`retroProfileX`,paneloption_style.`retroProfileY`,paneloption_astragal.name AS astragal_name,paneloption_condition.name AS condition_name,paneloption_safety.name AS safty_name,paneloption_glasstype.name AS galsstype_name FROM panel,paneloption_astragal,paneloption_safety,paneloption_style,paneloption_glasstype,paneloption_condition WHERE panel.safetyid=paneloption_safety.safetyid AND panel.glasstypeid=paneloption_glasstype.glasstypeid AND panel.`styleid`=paneloption_style.styleid AND panel.conditionid=paneloption_condition.conditionid AND panel.astragalsid=paneloption_astragal.astragalsid AND panel.windowid=".$row_pann['windowid']."");
                      $get_panel = $db->joinquery("SELECT panel.panelid,panel.width,panel.height,panel.center,panel.measurement,panel.panelnum,panel.profileid,panel.windowid,panel.`safetyid`,panel.`glasstypeid`,panel.`styleid`,panel.`conditionid`,panel.`astragalsid`,`paneloption_style`.name AS stylename,paneloption_style.`evsProfileTop`,paneloption_style.`evsProfileSides`,paneloption_style.`evsProfileBottom`,paneloption_style.`evsGlassX`,paneloption_style.`evsGlassY`,paneloption_style.`evsProfileX`,paneloption_style.`evsProfileY`,paneloption_style.`retroProfileTop`,paneloption_style.`retroProfileSides`,paneloption_style.`retroProfileBottom`,paneloption_style.`retroGlassX`,paneloption_style.`retroGlassY`,paneloption_style.`retroProfileX`,paneloption_style.`retroProfileY`,paneloption_style.evsProfileRight,paneloption_style.evsProfileLeft,paneloption_style.evsOutPanelThickness,paneloption_style.evsOutPanelType,paneloption_style.evsInPanelThickness,paneloption_style.evsInPanelType,paneloption_style.retroOutPanelThickness,paneloption_style.retroOutPanelType,paneloption_style.retroInPanelThickness,paneloption_style.retroInPanelType,paneloption_style.retroProfileLeft,paneloption_style.retroProfileRight,paneloption_astragal.name AS astragal_name,paneloption_condition.name AS condition_name,paneloption_safety.name AS safty_name,paneloption_glasstype.name AS galsstype_name,paneloption_glasstype.typevalue FROM panel,paneloption_astragal,paneloption_safety,paneloption_style,paneloption_glasstype,paneloption_condition WHERE 
panel.styleid=paneloption_style.styleid AND panel.safetyid=paneloption_safety.safetyid AND panel.astragalsid=paneloption_astragal.astragalsid AND panel.glasstypeid=paneloption_glasstype.glasstypeid AND panel.conditionid=paneloption_condition.conditionid AND panel.windowid=" . $row_pann['windowid'] . "");
                      ?>
                    </div>
                    <div class="window_info table_custom">
                      <div class="table-responsive">
                        <table class="table">
                          <thead>
                            <tr>
                              <th>
                                <h4 class="main_title">Panel Size</h4>
                              </th>
                              <!-- <th><h4 class="main_title">Profile</h4></th>-->
                              <th>
                                <h4 class="main_title">Style</h4>
                              </th>
                              <th>
                                <h4 class="main_title">Safety</h4>
                              </th>
                              <th>
                                <h4 class="main_title">Glass Type</h4>
                              </th>
                              <th>
                                <h4 class="main_title">Condition</h4>
                              </th>
                              <th>
                                <h4 class="main_title">Astrigals</h4>
                              </th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            if (mysqli_num_rows($get_panel) > 0) {
                              while ($row_panel = mysqli_fetch_array($get_panel)) {
                            ?>
                                <tr>
                                  <td class="flex-prop"><span>Panel <?php echo $row_panel['panelnum']; ?></span> <span id="spn_width<?php echo $row_panel['panelid']; ?>" style="display:initial"><?php echo $row_panel['width']; ?> x <?php echo $row_panel['height']; ?> x <?php echo $row_panel['center']; ?></span></td>
                                  <td class="cust_slct"><?php if ($row_panel['styleid'] > 0 && file_exists($gPanelOptionsPhotoDir . $row_panel['styleid'] . ".png")) {
                                                        ?>
                                      <a data-toggle='modal' data-target='#styleModalprop' href='#' onclick="getVal(<?php echo $Locationid; ?>,<?php echo $row_panel['panelid']; ?>,<?php echo $row_panel['styleid']; ?>,'<?php echo $gPanelOptionsPhotoURL . $row_panel['styleid']; ?>.png','<?php echo $row_panel['stylename']; ?>')"><span id='span<?php echo $row_panel['panelid']; ?>'><img src="<?php echo $gPanelOptionsPhotoURL . $row_panel['styleid']; ?>.png" class="img-responsive" style="width:50px; height:50px;"></span></a>
                                    <?php
                                                          //echo "<a data-toggle='modal' data-target='#styleModalprop' href='#' onclick='getVal(".$row_panel['panelid'].",".$row_panel['styleid'].",\'' ".$gPanelOptionsPhotoURL." '\')'><span id='span".$row_panel['panelid']."'><img src=\"". $gPanelOptionsPhotoURL.$row_panel['styleid'].".png?". time(). "\" class=\"img-responsive\" style=\"width: 50px; height; 50px;\"></span></a>";
                                                        } ?>
                                  </td>
                                  <td class="cust_slct" id="td_safty<?php echo $row_panel['panelid']; ?>"><?php echo $row_panel['safty_name']; ?></td>
                                  <td class="cust_slct" id="td_glass<?php echo $row_panel['panelid']; ?>"><?php echo $row_panel['galsstype_name']; ?></td>
                                  <td class="cust_slct" id="td_condition<?php echo $row_panel['panelid']; ?>"><?php echo $row_panel['condition_name']; ?></td>
                                  <td class="cust_slct" id="td_astragal<?php echo $row_panel['panelid']; ?>"><?php echo $row_panel['astragal_name']; ?></td>
                                </tr>
                            <?php
                              }
                            }
                            ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div><!-- ./col-lg-12 -->
                <div class="col-lg-12">
                  <div class="table-responsive">
                    <table class="table table-bordered table-striped fontstyle">
                      <thead>
                        <tr style="color:#fff; background:#565759;">
                          <th></th>
                          <th colspan="2">Cut List</th>
                          <th colspan="4">Measurements</th>
                          <th colspan="5">Glass</th>
                          <th colspan="3">Top</th>
                          <th colspan="3">Bottom</th>
                          <th colspan="3">Sides(Left)</th>
                          <th colspan="3">Sides(Right)</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <th>#</th>
                          <th>Window[Panel]</th>
                          <th>Style</th>
                          <th>Safety</th>
                          <th>Width</th>
                          <th>Height</th>
                          <th>Center</th>
                          <th>(+w)</th>
                          <th>W</th>
                          <th>(+h)</th>
                          <th>H</th>
                          <th>m2</th>
                          <th>Profile</th>
                          <th>(+w)</th>
                          <th>(W)</th>
                          <th>Profile</th>
                          <th>(+w)</th>
                          <th>(W)</th>
                          <th>Profile</th>
                          <th>(+h)</th>
                          <th>(H)</th>
                          <th>Profile</th>
                          <th>(+h)</th>
                          <th>(H)</th>
                        </tr>
                        <?php
                        mysqli_data_seek($get_panel, 0);
                        if (mysqli_num_rows($get_panel) > 0) {
                          $i = 0;
                          while ($row_panel = mysqli_fetch_array($get_panel)) {
                            $i++;
                        ?>
                            <tr>
                              <td><?php echo $i; ?></td>
                              <td><?php echo $row_pann['name'] . "[" . $row_panel['panelnum'] . "]"; ?></td>
                              <td><?php if ($row_panel['styleid'] > 0 && file_exists($gPanelOptionsPhotoDir . $row_panel['styleid'] . ".png")) {
                                    echo "<img src=\"" . $gPanelOptionsPhotoURL . $row_panel['styleid'] . ".png?" . time() . "\" class=\"img-responsive\" style=\"width: 50px; height; 50px;\">";
                                  } ?></td>
                              <td><?php echo $row_panel['safty_name']; ?></td>
                              <td <?php if ($row_panel['measurement'] == 'estimate') { ?> style="color:#F00" <?php } ?>><?php echo $row_panel['width']; ?> </td>
                              <td <?php if ($row_panel['measurement'] == 'estimate') { ?> style="color:#F00" <?php } ?>><?php echo $row_panel['height']; ?> </td>
                              <td <?php if ($row_panel['measurement'] == 'estimate') { ?> style="color:#F00" <?php } ?>><?php echo $row_panel['center']; ?> </td>
                              <?php
                              if ($row_pann['selected_product'] == "EVSx3" || $row_pann['selected_product'] == "EVSx2") {
                                $profiletop = $row_panel['evsProfileTop'];
                                $profilebottom = $row_panel['evsProfileBottom'];
                                $leftprofile = $row_panel['evsProfileLeft'];
                                $rightprofile = $row_panel['evsProfileRight'];
                                $glassX = $row_panel['evsGlassX'];
                                $glassY = $row_panel['evsGlassY'];
                                $profileX = $row_panel['evsProfileX'];
                                $profileY = $row_panel['evsProfileY'];
                              } else {
                                $profiletop = $row_panel['retroProfileTop'];
                                $profilebottom = $row_panel['retroProfileBottom'];
                                $profilesides = $row_panel['retroProfileSides'];
                                $leftprofile = $row_panel['retroProfileLeft'];
                                $rightprofile = $row_panel['retroProfileRight'];
                                $glassX = $row_panel['retroGlassX'];
                                $glassY = $row_panel['retroGlassY'];
                                $profileX = $row_panel['retroProfileX'];
                                $profileY = $row_panel['retroProfileY'];
                              }
                              if ($glassX == NULL) $glassX = 0;
                              if ($glassY == NULL) $glassY = 0;
                              if ($profileX == NULL) $profileX = 0;
                              if ($profileY == NULL) $profileY = 0;
                              ?>
                              <td><?php echo $glassX; ?></td>
                              <td><?php if ($row_panel['width'] > 0) {
                                    echo ($row_panel['width'] + $glassX);
                                  } ?></td>
                              <td><?php echo $glassY; ?></td>
                              <td><?php if ($row_panel['height'] > 0) {
                                    echo ($row_panel['height'] + $glassY);
                                  } ?></td>
                              <td><?php if ($row_panel['width'] > 0) {
                                    echo round(((($row_panel['width'] + $glassX) * ($row_panel['height'] + $glassY)) * 0.000001), 2);
                                  } ?></td>
                              <td>
                                <?php
                                if (file_exists($gProfilePhotoDir . $profiletop . ".png")) {
                                ?>
                                  <span><a class="fs-gal" data-url="<?php echo $gProfilePhotoURL . $profiletop; ?>.png" style="color:blue;"><?php echo $profiletop; ?></a></span>
                                <?php
                                } else { ?><span><?php echo $profiletop; ?></span><?php
                                                                                                                }
                                                                                                                  ?>
                              </td>
                              <td><?php echo $profileX; ?></td>
                              <td><?php if ($row_panel['width'] > 0) {
                                    echo ($row_panel['width'] + $profileX);
                                  } ?></td>
                              <td> <?php
                                    if (file_exists($gProfilePhotoDir . $profilebottom . ".png")) {
                                    ?>
                                  <span><a class="fs-gal" data-url="<?php echo $gProfilePhotoURL . $profilebottom; ?>.png" style="color:blue;"><?php echo $profilebottom; ?></a></span>
                                <?php
                                    } else { ?><span><?php echo $profilebottom; ?></span><?php
                                                                                                                  }
                                                                                                                    ?>
                              </td>
                              <td><?php echo $profileX; ?></td>
                              <td><?php if ($row_panel['width'] > 0) {
                                    echo ($row_panel['width'] + $profileX);
                                  } ?></td>
                              <td> <?php
                                    if (file_exists($gProfilePhotoDir . $leftprofile . ".png")) {
                                    ?>
                                  <span><a class="fs-gal" data-url="<?php echo $gProfilePhotoURL . $leftprofile; ?>.png" style="color:blue;"><?php echo $leftprofile; ?></a></span>
                                <?php
                                    } else { ?><span><?php echo $leftprofile; ?></span><?php
                                                                                  }
                                                                                    ?>
                              </td>
                              <td><?php echo $profileY; ?></td>
                              <td><?php if ($row_panel['height'] > 0) {
                                    echo ($row_panel['height'] + $profileY);
                                  } ?></td>
                              <td> <?php
                                    if (file_exists($gProfilePhotoDir . $rightprofile . ".png")) {
                                    ?>
                                  <span><a class="fs-gal" data-url="<?php echo $gProfilePhotoURL . $rightprofile; ?>.png" style="color:blue;"><?php echo $rightprofile; ?></a></span>
                                <?php
                                    } else { ?><span><?php echo $rightprofile; ?></span><?php
                                                                                    }
                                                                                      ?>
                              </td>
                              <td><?php echo $profileY; ?></td>
                              <td><?php if ($row_panel['height'] > 0) {
                                    echo ($row_panel['height'] + $profileY);
                                  } ?></td>
                            </tr>
                        <?php
                          } //whiile
                        } //if
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="col-lg-12">
                  <div class="photos" id="div_befor_img<?php echo $row_pann['windowid']; ?>">
                    <h4 class="main_title_2 main-title-flex">Before Photos <a class="plus-circle-blue" id="before-photo" data-id="<?php echo $row_pann['windowid']; ?>" data-toggle="modal" data-target="#myModalBefore" href="javascript:void(0)"><i class="fa fa-plus"></i></a></h4>
                    <ul>
                      <?php
                      $before_photos = $db->joinquery("SELECT photoid FROM window_photo WHERE windowid='" . $row_pann['windowid'] . "'");
                      while ($row_phot = mysqli_fetch_array($before_photos)) {
                        if ($row_phot['photoid'] > 0 && file_exists($gPhotoDir . $row_phot['photoid'] . ".png")) ?>
                        <li> <img src="<?php echo $gPhotoURL . $row_phot['photoid']; ?>.jpg" class="fs-gal" data-url="<?php echo $gPhotoURL . $row_phot['photoid']; ?>.jpg"> </li>
                      <?php
                      }
                      ?>
                    </ul>
                  </div><!-- ./photos -->
                </div><!-- ./col-lg-12 -->
                <div class="col-lg-12">
                  <div class="notes">
                    <h4 class="main_title_2">Notes <a data-toggle="modal" data-target="#myModal<?php echo $row_pann['windowid']; ?>" href="#" data-id="<?php echo $row_pann['windowid']; ?>" class="btn btn-primary" style="margin-left:100px;">EDIT</a></h4>
                    <p id="notes<?php echo $row_pann['windowid']; ?>"><?php echo $row_pann['notes']; ?></p>
                  </div>
                </div>
                <div class="col-lg-12">
                  <div class="notes">
                    <h4 class="main_title_2">Extras <a data-toggle="modal" data-target="#AddExtra" href="#" data-id="<?php echo $row_pann['windowid']; ?>" class="btn btn-primary" style="margin-left:100px;" id="anchor_extra_view">ADD</a></h4>
                    <div class="table-responsive">
                      <span id="view_<?php echo $row_pann['windowid']; ?>">
                        <?php
                        $get_extras = $db->joinquery("SELECT window_extras.*,products.* FROM window_extras,products WHERE window_extras.productid=products.productid AND window_extras.windowid='" . $row_pann['windowid'] . "'");
                        if (mysqli_num_rows($get_extras) > 0) { ?>
                          <table class="table">
                            <tr>
                              <td style="border:none"><input type="hidden" name="page-status" value="1" /></td>
                            </tr>
                            <?php
                            while ($row_extras = mysqli_fetch_array($get_extras)) {
                              //$hours=round(($row_extras['hours']*$row_extras['sizevalue']),2);
                              if ($row_extras['imageid'] == 0)
                                $row_extras['imageid'] = $row_extras['productid'];
                            ?>
                              <tr>
                                <?php
                                if (file_exists($gSupplierProdcutsDir . $row_extras['imageid'] . ".png")) {
                                  echo '<td><img src="' . $gSupplierProdcutsURL . $row_extras['imageid'] . '.png' . '" style="width:20px"></td>';
                                } else {
                                  echo '<td></td>';
                                }
                                ?>
                                <td><?php echo $row_extras['name']; ?></td>
                                <td>$<?php echo $row_extras['cost']; ?>p/<?php //echo $row_extras['unittag'];
                                                                        ?> </td>
                                <td><?php //echo $row_extras['sizevalue'];
                                    ?><?php echo $row_extras['unitname']; ?></td>
                                <td><?php echo $row_extras['hours']; ?>&nbsp;Hours</td>
                                <td><a href="<?php echo $row_extras['linkURL']; ?>">More Info</a></td>
                                <td><a href="javascript:void(0)" id="delete-extra" onClick="delview(<?php echo $row_extras['extraid']; ?>,<?php echo $row_extras['windowid']; ?> )"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                                <td><a data-toggle="modal" data-target="#myModalExtra" href="#" data-id="<?php echo $row_extras['extraid']; ?>" id="edit-view-manager"><i class="fa fa-edit" aria-hidden="true"></i></a></td>
                              </tr>
                            <?php
                            }
                            ?>
                          </table>
                        <?php } ?>
                      </span>
                    </div>
                  </div>
                </div>
                <!--<div class="col-lg-12">
                	<div class="notes">
                        <h4 class="main_title">Hazards</h4>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent at sapien est. Nunc ligula nibh, dictum a malesuada quis, venenatis nec tellus. Integer eget venenatis ligula. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Donec tellus neque, suscipit in augue quis, rhoncus tincidunt metus. Integer non hendrerit nisl.</p>
                    </div>
                </div>-->
                <div class="col-lg-12">
                  <div class="photos" id="div_after_img<?php echo $row_pann['windowid']; ?>">
                    <h4 class="main_title_2 main-title-flex">After Photos <a class="plus-circle-blue" id="after-photo" data-id="<?php echo $row_pann['windowid']; ?>" data-toggle="modal" data-target="#myModalAfter" href="javascript:void(0)"><i class="fa fa-plus"></i></a></h4>
                    <ul>
                      <?php
                      $after_photos = $db->joinquery("SELECT photoid FROM window_after_photo WHERE windowid='" . $row_pann['windowid'] . "'");
                      while ($row_phot_after = mysqli_fetch_array($after_photos)) {
                        if ($row_phot_after['photoid'] > 0 && file_exists($gPhotoDir . $row_phot_after['photoid'] . ".png")) ?>
                        <li> <img src="<?php echo $gPhotoURL . $row_phot_after['photoid']; ?>.jpg" class="fs-gal" data-url="<?php echo $gPhotoURL . $row_phot_after['photoid']; ?>.jpg"> </li>
                      <?php
                      }
                      ?>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </section>
        <?php
        } //if
        ?>
        <div id="myModal<?php echo $row_pann['windowid']; ?>" class="modal fade" role="dialog">
          <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add /Edit Window Notes</h4>
              </div>
              <div class="modal-body">
                <p>
                <table class="table">
                  <tr>
                    <td><textarea name="notes" id="windownotes<?php echo $row_pann['windowid']; ?>" rows="10" style="resize:none; width:100%"><?php echo $row_pann['notes']; ?></textarea></td>
                  </tr>
                  <tr>
                    <td><input type="button" value="UPDATE" class="btn btn-primary" style="margin-left:50px;" onclick="updatenotes(<?php echo $row_pann['windowid']; ?>,'notes')" /></td>
                  </tr>
                </table>
                </p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>
  </span>
<?php
      }
    }
?>
<?php
}
?>
<div id="myModalExtra" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit Extras</h4>
      </div>
      <div class="modal-body">
        <p id="extra-view-manager">
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<div id="AddExtraView" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Extras</h4>
      </div>
      <div class="modal-body">
        <table class="table">
          <tr>
            <th>Option</th>
            <td>
              <select name="product" id="product1" class="form-control">
                <?php foreach ($windowOption as $valop) {
                  echo '<option value=' . $valop['productid'] . '>' . $valop['name'] . '</option>';
                } ?>
              </select>
            </td>
          </tr>
          <tr>
            <th>Quantity</th>
            <td><input type="text" class="form-control" name="quantity1" id="quantity1"></td>
          </tr>
          <tr>
            <td><input type="hidden" id="extra-windowid1"></td>
            <td><input type="button" name="add" value="ADD" class="btn-primary" id="addextra"></td>
          </tr>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>