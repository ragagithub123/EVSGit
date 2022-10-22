<?php ob_start();
session_start();
ini_set('memory_limit', '16M');
include('../includes/functions.php');
if (!empty($_FILES['file']['name'])) {
	$image_info = getimagesize($_FILES["file"]["tmp_name"]);
	if (!empty($image_info[0]) && !empty($image_info[1])) {
		$image_data = array(
			'width'	 => $image_info[0],
			'height'	 => $image_info[1],
		);
		$photoid = $db->ins_rec("photo", $image_data);
		$temp = explode(".", $_FILES["file"]["name"]);
		$newfilename = $photoid . ".jpg";
		move_uploaded_file($_FILES['file']['tmp_name'], $gPhotoDir . "/" . $newfilename);
		copy($gPhotoDir . "/" . $newfilename, $DPhotoDir . "/" . $newfilename);
		$img = 'assets/photos/' . $newfilename;
		$url = $gPhotoURL . $photoid . ".jpg";
	}
} else {
	$photoid = 0;
}
$customer_id = $db->ins_rec('customer', array('created' => gmdate("YmdHis"), 'agentid' => $_SESSION['agentid'], 'firstname' => $_POST['firstname'], 'lastname' => $_POST['lastname'], 'email' => $_POST['email'], 'phone' => $_POST['phone']));
$loc = $_POST['unitnum'] . "," . $_POST['street'];
if (!empty($_POST['suburb'])) {
	$loc .= "," . $_POST['suburb'];
}
$loc .= "," . $_POST['city'];
$newloc = $loc;
$location = str_replace(' ', '%20', $newloc);
$res = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address='" . $location . "'&key=AIzaSyD91uB2dG7Lyj4xOWJwxT-lq9uQ0FtwU0o");
$result_arr = json_decode($res);
$lat = $result_arr->results[0]->geometry->location->lat;
$lng = $result_arr->results[0]->geometry->location->lng;
$locationid = $db->ins_rec('location', array('unitnum' => $_POST['unitnum'], 'street' => $_POST['street'], 'suburb' => $_POST['suburb'], 'city' => $_POST['city'], 'latitude' => $lat, 'longitude' => $lng, 'notes' => $_POST['notes'], 'jobstatusid' => $_POST['statusid'], 'customerid' => $customer_id, 'agentid' => $_SESSION['agentid'], 'photoid' => $photoid, 'locationSearch' => $loc));
$customor = $_POST['firstname'] . $_POST['lastname'] . "," . $loc;
echo $_POST['notes'];
