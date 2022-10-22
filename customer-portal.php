 <?php ob_start();
	session_start();
	include('includes/functions.php');
	include_once("colors.inc.php");
	if (!empty($_SESSION['agentid'])) {
		$arrContextOptions = array(
			"ssl" => array(
				"verify_peer" => false,
				"verify_peer_name" => false,
			),
		);
		//$getprop=$db->joinquery("SELECT locationSearch FROM location WHERE agentid='".$_SESSION['agentid']."' AND (locationstatusid!=3 AND locationstatusid!='5')");		
		$getprop = $db->joinquery("SELECT locationSearch FROM location WHERE agentid='" . $_SESSION['agentid'] . "' ");
		if (mysqli_num_rows($getprop) > 0) {
			while ($row_prop = mysqli_fetch_array($getprop)) {
				$postLocation[] = $row_prop['locationSearch'];
			}
		}
		$ex = new GetMostCommonColors();
		$num_results = 1;
		$delta =  24;
		$reduce_brightness =  1;
		$reduce_gradients = 1;
		$getstatus = $db->joinquery("SELECT * FROM jobstatus WHERE jobstatus!='Archive'");
		while ($row_status1 = mysqli_fetch_assoc($getstatus)) {
			$jobStatus[] = $row_status1;
		}
		array_swap($jobStatus, 5, 6); //ineterchange quote go and quote rejected
		array_swap($jobStatus, 7, 9); // interchange unit schedule and unit materials
		foreach ($jobStatus as $row_status) {
			//echo "SELECT location.locationid,location.photoid,location.`unitnum`,location.`street`,location.`suburb`,location.`city`,location.`status1`,location.`status2`,location.status3,location.status4,location.status5,location.status6,location.status7,location.status8,location.status9,location.status10,location.status11,location.status12,location.status13,location.status14,location.jobstatusid,location.booking_date,location.booking_status,location.alarm_Type,customer.customerid,customer.firstname,customer.lastname,customer.email,customer.phone FROM location,customer WHERE location.customerid=customer.customerid  AND location.`agentid`=".$_SESSION['agentid']." AND jobstatusid='".$row_status['jobstatusid']."'";
			//die();
			$getcards = $db->joinquery("SELECT location.locationid,location.photoid,location.`unitnum`,location.`street`,location.`suburb`,location.`city`,location.`status1`,location.`status2`,location.status3,location.status4,location.status5,location.status6,location.status7,location.status8,location.status9,location.status10,location.status11,location.status12,location.status13,location.status14,location.jobstatusid,location.booking_date,location.booking_status,location.alarm_Type,customer.customerid,customer.firstname,customer.lastname,customer.email,customer.phone FROM location,customer WHERE location.customerid=customer.customerid AND location.`agentid`=" . $_SESSION['agentid'] . " AND location.jobstatusid='" . $row_status['jobstatusid'] . "' ORDER BY location.locationid DESC");
			$row_status['total_cards'] = mysqli_num_rows($getcards);
			$array_panel_num = array();
			$array_selected_num = array();
			$get_total_panel = $db->joinquery("SELECT sum(window_type.numpanels) AS totalcardpanel FROM location,room,window,window_type WHERE location.agentid='" . $_SESSION['agentid'] . "' AND room.locationid=location.locationid AND (location.locationstatusid!=3 AND location.locationstatusid!='5') AND location.jobstatusid='" . $row_status['jobstatusid'] . "' AND window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid");
			$row_total_panel = mysqli_fetch_array($get_total_panel);
			$get_total_selected_cnt = $db->joinquery("SELECT sum(window_type.numpanels) AS totalcardselected FROM location,room,window,window_type WHERE location.agentid='" . $_SESSION['agentid'] . "' AND room.locationid=location.locationid AND (location.locationstatusid!=3 AND location.locationstatusid!='5') AND location.jobstatusid='" . $row_status['jobstatusid'] . "' AND window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid AND window.selected_product!='HOLD'");
			$row_total_selected_panel = mysqli_fetch_array($get_total_selected_cnt);
			if (empty($row_total_panel['totalcardpanel'])) {
				$row_status['totals_panels'] = 0;
			} else {
				$row_status['totals_panels'] = $row_total_panel['totalcardpanel'];
			}
			if (empty($row_total_selected_panel['totalcardselected'])) {
				$row_status['selected_panels'] = 0;
			} else {
				$row_status['selected_panels'] = $row_total_selected_panel['totalcardselected'];
			}
			while ($row_cards = mysqli_fetch_array($getcards)) {
				if (!(file_exists($DPhotoDir . "/" . $row_cards['photoid'] . ".jpg")) && $row_cards['photoid'] > 0 && (file_exists($gPhotoDir . "/" . $row_cards['photoid'] . ".jpg"))) {
					$img = 'assets/photos/' . $row_cards['photoid'] . ".jpg";
					$url = $gPhotoURL . $row_cards['photoid'] . ".jpg";
					file_put_contents($img, file_get_contents($url, false, stream_context_create($arrContextOptions)));
				} else {
					if ($row_cards['photoid'] > 0 && (file_exists($DPhotoDir . "/" . $row_cards['photoid'] . ".jpg"))) {
						$img = 'assets/photos/' . $row_cards['photoid'] . ".jpg";
					} else {
						$img = "images/no-location.png";
					}
				}
				if ($img != '') {
					$colors = $ex->Get_Color($img, $num_results, $reduce_brightness, $reduce_gradients, $delta);
					foreach ($colors as $hex => $count) {
						if ($count > 0) {
							$row_cards['background_color'] = "#" . $hex;
						}
					}
				} else {
					$row_cards['background_color'] = "";
				}
				$row_cards['location_image'] = $img;
				if ($row_cards['booking_date'] != "0000-00-00 00:00:00") {
					$time_date = date('d-m-Y H:i', strtotime($row_cards['booking_date']));
					$exp_time = explode(' ', $time_date);
					$row_cards['book_time'] = $exp_time[0];
					$row_cards['book_date'] = $exp_time[1];
				} else {
					$row_cards['book_time'] = "";
					$row_cards['book_date'] = "";
				}
				if ($row_cards['booking_date'] <= date('Y-m-d H:i:s')) {
					$row_cards['booking_status'] = 0;
				}
				$get_totalpanel = $db->joinquery("SELECT sum(window_type.numpanels) AS total_panels FROM room,window,window_type WHERE window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid AND room.locationid='" . $row_cards['locationid'] . "'");
				$row_quote = mysqli_fetch_array($get_totalpanel);
				$get_selected_cnt = $db->joinquery("SELECT sum(window_type.numpanels) AS pdt_count FROM room,window,window_type WHERE window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid AND window.selected_product!='HOLD' AND room.locationid='" . $row_cards['locationid'] . "'");
				$row_selected = mysqli_fetch_array($get_selected_cnt);
				if (empty($row_quote['total_panels'])) {
					$row_cards['quotedpanel'] = 0;
				} else {
					$row_cards['quotedpanel'] = $row_quote['total_panels'];
				}
				if (empty($row_selected['pdt_count'])) {
					$row_cards['selectedpanel'] = 0;
				} else {
					$row_cards['selectedpanel'] = $row_selected['pdt_count'];
				}
				$get_attach_count = $db->joinquery("SELECT COUNT(*) AS total_cnt FROM location_attachments WHERE locationid='" . $row_cards['locationid'] . "'");
				$row = mysqli_fetch_array($get_attach_count);
				$row_cards['total_cnt'] = $row['total_cnt'];
				$get_comments_count = $db->joinquery("SELECT COUNT(*) AS total_cmmnt FROM location_comments WHERE locationid='" . $row_cards['locationid'] . "'");
				$row_cmmt = mysqli_fetch_array($get_comments_count);
				$row_cards['cmmt_cnt'] = $row_cmmt['total_cmmnt'];
				$cards[] = $row_cards;
			}
			$row_status['cards'] = $cards;
			$post[] = $row_status;
		}
		$get_status = $db->joinquery("SELECT * FROM location_status");
		$row_details = mysqli_fetch_array($get_details);
		if (mysqli_num_rows($get_status) > 0) {
			while ($row_status = mysqli_fetch_array($get_status)) {
				$locstatus[] = $row_status;
			}
		}
		$checklocation = $db->joinquery("SELECT maxlocations FROM agent WHERE agentid='" . $_SESSION['agentid'] . "'");
		$row_location = mysqli_fetch_array($checklocation);
		$getlocationcount = $db->joinquery("SELECT COUNT(locationid)AS location_count FROM location WHERE agentid='" . $_SESSION['agentid'] . "'");
		$row_loc_cnt = mysqli_fetch_array($getlocationcount);
		if ($row_loc_cnt['location_count'] >= $row_location['maxlocations'])
			$recycle_button = true;
		else
			$recycle_button = false;
		include('templates/header.php');
		include('views/customer-interface.htm');
		include('templates/footer.php');
	} else {
		header('Location:index.php');
	}
	function file_get_contents_curl($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	function array_swap(&$array, $swap_a, $swap_b)
	{
		list($array[$swap_a], $array[$swap_b]) = array($array[$swap_b], $array[$swap_a]);
	}
	?>