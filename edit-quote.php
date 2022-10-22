<?php ob_start();
session_start();
//echo ini_get('max_execution_time'); die();
include('includes/functions.php');
if (!empty($_SESSION['agentid'])) {
	 
	$msg = "";
	$quoteurl = $gquotepagePhotoURL;
	$quoteId = $_POST['locationid'] . "-" . hash('sha256', $_POST['locationid'] . $gQuoteHashSecret);
	
	$quote_URL = $gWebsite . "/quote/$quoteId";
	if (isset($_POST['quote-update'])) {
		$timestamp = strtotime($_POST['quotedate']);
		//painting price	
		if ($_POST['painting_estimate_price'] == 1)
			$painting_price = 1;
		else
			$painting_price = 0;
		//installation price
		if ($_POST['installation_estimate_price'] == 1)
			$installation_price = 1;
		else
			$installation_price = 0;
		if ($_POST['paintlock'] == 1)
			$paintlock = 1;
		else
			$paintlock = 0;
		if ($_POST['stepslock'] == 1)
			$stepslock = 1;
		else
			$stepslock = 0;

		// echo htmlspecialchars($_POST['quotegreeting']);
		// echo mysqli_real_escape_string($mysqli, $_POST['quotegreeting']); 
		// die();
		// print_r("UPDATE location SET quotedatestamp ='" . $timestamp . "',quotegreeting='" . mysqli_real_escape_string($_POST['quotegreeting']) . "',quotedetails='" . mysqli_real_escape_string($_POST['quotedetails']) . "',paintdetails='" . mysqli_real_escape_string($_POST['paintdetails']) . "',stepdetails='" . $_POST['steps'] . "',paintlock='" . $paintlock . "',stepslock='" . $stepslock . "',painting_price='" . $painting_price . "',installation_price='" . $installation_price . "' WHERE locationid='" . $_POST['locationid'] . "'"); die();
		$db->joinquery("UPDATE location SET quotedatestamp ='" . $timestamp . "',quotegreeting='" . mysqli_real_escape_string($db->connection, $_POST['quotegreeting']) . "',quotedetails='" . mysqli_real_escape_string($db->connection, $_POST['quotedetails']) . "',paintdetails='" . mysqli_real_escape_string($db->connection, $_POST['paintdetails']) . "',stepdetails='" . $_POST['steps'] . "',paintlock='" . $paintlock . "',stepslock='" . $stepslock . "',painting_price='" . $painting_price . "',installation_price='" . $installation_price . "' WHERE locationid='" . $_POST['locationid'] . "'");
		if (count($_POST['pagecheck']) > 0) {
			$chk_cnt = $db->joinquery("SELECT * FROM quotepage_location WHERE locationid='" . $_POST['locationid'] . "'");
			if (mysqli_num_rows($chk_cnt) > 0)
				$db->joinquery("DELETE FROM quotepage_location WHERE locationid='" . $_POST['locationid'] . "'");
		}
		for ($i = 0; $i < count($_POST['pagecheck']); $i++) {
			$db->joinquery("INSERT INTO quotepage_location(pageid,locationid,agentid)VALUES('" . $_POST['pagecheck'][$i] . "','" . $_POST['locationid'] . "','" . $_SESSION['agentid'] . "')");
		}
		$Locationid = $_POST['locationid'];
		$msg =  "Quote updated successfully";
	}
	if (isset($_REQUEST['Id'])) {
		$Locationid = base64_decode($_REQUEST['Id']);
	}
	$openurl = "";
	$quote_pdf_url = "";
	$locdeatils = $db->joinquery("SELECT `unitnum`,`street`,`suburb`,`city`,`quotedatestamp`,`quotegreeting`,`quotedetails`,`paintdetails`,`stepdetails`,`paintlock`,`stepslock`,`quotelocked` FROM `location` WHERE `locationid`=" . $Locationid . "");
	$rowdetails = mysqli_fetch_array($locdeatils);
	$quote_lock  = $rowdetails['quotelocked'];
	$getatatchment = $db->joinquery("SELECT `attachment` FROM `location_attachments` WHERE `locationid`=" . $Locationid . " ORDER BY attachmentid DESC LIMIT 0,1");
	if (mysqli_num_rows($getatatchment) > 0) {
		$row_attach = mysqli_fetch_array($getatatchment);
		$folder = $gAttachmentDir.$_SESSION['agentid']."/".$row_attach['attachment'];
    if(file_exists($folder)){

			$quote_pdf_url = $siteURL . "/assets/attachments/" . $_SESSION['agentid']."/".$row_attach['attachment'];
			$openurl = "assets/attachments/" . $_SESSION['agentid']."/".$row_attach['attachment'];
		}
		else{

			$quote_pdf_url = $siteURL . "/assets/attachments/".$row_attach['attachment'];
			$openurl = "assets/attachments/".$row_attach['attachment'];

		}
		

	
	}
	$pdfname = 'Quote_';
	if ($rowdetails['unitnum'] != '')
		$pdfname .= trim($rowdetails['unitnum'], " ") . "_";
	if ($rowdetails['street'] != '')
		$pdfname .= trim($rowdetails['street'], " ") . "_";
	if ($rowdetails['suburb'] != '')
		$pdfname .= trim($rowdetails['suburb'], " ") . "_";
	if ($rowdetails['city'] != '')
		$pdfname .= trim($rowdetails['city'], " ") . "_";
	$pdfname .= date('Y-m-d_H_i_sa') . ".pdf";
	if ($rowdetails['quotegreeting'] == "") {
		$agentquote = $db->joinquery("SELECT quotedate,quotegreeting,quotedetails,`paintdetails`,`stepdetails`,`paintlock`,`steplock` FROM agent WHERE agentid='" . $_SESSION['agentid'] . "'");
		$rowquote   =  mysqli_fetch_array($agentquote);
		if ($rowquote['quotegreeting'] == "") {
			$defaultquote = $db->joinquery("SELECT quotegreeting,quotedetails FROM params");
			$rowdefault   = mysqli_fetch_array($defaultquote);
			$quotegreeting = $rowdefault['quotegreeting'];
			$quotedetails  = $rowdefault['quotedetails'];
			$paintdetails  = "";
			$quotedate     = "";
			$stepdetails   = "";
			$paintlock     = 0;
			$steplock      = 0;
		} else {
			$quotegreeting = $rowquote['quotegreeting'];
			$quotedetails  = $rowquote['quotedetails'];
			$paintdetails  = $rowquote['paintdetails'];
			$stepdetails  = $rowquote['stepdetails'];
			$paintlock     = $rowquote['paintlock'];
			$steplock      = $rowquote['steplock'];
			if ($rowquote['quotedate'] != null)
				$quotedate     = date('Y-m-d', strtotime($rowquote['quotedate']));
			else
				$quotedate = "";
		}
	} else {
		$quotegreeting = $rowdetails['quotegreeting'];
		$quotedetails  = $rowdetails['quotedetails'];
		$paintdetails  = $rowdetails['paintdetails'];
		$stepdetails  = $rowdetails['stepdetails'];
		$paintlock     = $rowdetails['paintlock'];
		$steplock      = $rowdetails['stepslock'];
		if ($rowdetails['quotedatestamp'] != '' && $rowdetails['quotedatestamp'] != 0)
			$quotedate     = date('Y-m-d', $rowdetails['quotedatestamp']);
		else
			$quotedate = "";
	}
	$quotepages = $db->joinquery("SELECT * FROM quote_pages WHERE agentid='" . $_SESSION['agentid'] . "'");
	if (mysqli_num_rows($quotepages) > 0) {
		while ($rowquote = mysqli_fetch_assoc($quotepages)) {
			$images[] = $rowquote;
		}
	}
	$quote_selectpages = $db->joinquery("SELECT pageid FROM quotepage_location WHERE agentid='" . $_SESSION['agentid'] . "' AND locationid='" . $Locationid . "'");
	if (mysqli_num_rows($quote_selectpages) > 0) {
		while ($rowselquote = mysqli_fetch_array($quote_selectpages)) {
			$selquote[] = $rowselquote['pageid'];
		}
	} else
		$selquote = array();
	if (isset($_POST['quote_unlock'])) {
		$db->joinquery("UPDATE location SET quotelocked=1 WHERE locationid='" . $_POST['locationid'] . "'");
		$quote_lock = 1;
		$msg = "Quote Unlocked";
	}
	if (isset($_POST['quote_lockquote'])) {
		$db->joinquery("UPDATE location SET quotelocked=0 WHERE locationid='" . $_POST['locationid'] . "'");
		$quote_lock = 0;
		$msg = "Quote Locked";
	}
	if ($_POST['pdf_gen'] == 1) {

		

		$folder = $gAttachmentDir.$_SESSION['agentid']."/";
    if(!file_exists($folder))
    mkdir($gAttachmentDir."/" . $_SESSION['agentid'], 0777);
		$foldersize=foldersize($folder);
    $foldercapacity= format_size($foldersize);	
    $exp_size = explode(" ",$foldercapacity);
		if(($exp_size[1]=="GB")&&($exp_size[0]>10)){

			$msg ="Pdf Cannot generated. Your Total folder capacity exceeds 10GB!!!";
		

		}

		else{

			$timestamp = strtotime($_POST['quotedate']);
			require 'pdfcrowd.php';
			try {
				// create the API client instance
				$client = new \Pdfcrowd\HtmlToPdfClient("EVSGlazing", "XXXXXXXXXXX");
				$quoteId = $_POST['locationid'] . "-" . hash('sha256', $_POST['locationid'] . $gQuoteHashSecret);
				$quote_URL = $gWebsite . "quote/$quoteId";
			//	echo $quote_URL;die();
				$fileNama  = "assets/attachments/". $_SESSION['agentid'] ."/". $pdfname;
				// run the conversion and write the result to a file
				$client->convertUrlToFile($quote_URL, $fileNama);
				$db->joinquery("INSERT INTO location_attachments(locationid,attachment,agentid)VALUES(" . $_POST['locationid'] . ",'" . $pdfname . "'," . $_SESSION['agentid'] . ")");
				$db->joinquery("UPDATE location SET quotedatestamp ='" . $timestamp . "',quotelocked=0 WHERE locationid='" . $_POST['locationid'] . "'");
				header("Location: " . $fileNama . "");
			} catch (\Pdfcrowd\Error $why) {
				// report the error
				error_log("Pdfcrowd Error: {$why}\n");
				// rethrow or handle the exception
				throw $why;
			}
			$msg =  "Pdf generated successfully";
			
		}

	
	}
	include('templates/header.php');
	include('views/edit-quote.htm');
	include('templates/footer.php');
} else {
	header('Location:index.php');
}

function foldersize($path) {
	$total_size = 0;
	$files = scandir($path);

	foreach($files as $t) {
		if (is_dir(rtrim($path, '/') . '/' . $t)) {
			if ($t<>"." && $t<>"..") {
					$size = foldersize(rtrim($path, '/') . '/' . $t);

					$total_size += $size;
			}
		} else {
			$size = filesize(rtrim($path, '/') . '/' . $t);
			$total_size += $size;
		}
	}
	return $total_size;
}

function format_size($size) {
	$mod = 1024;
	$units = explode(' ','B KB MB GB TB PB');
	for ($i = 0; $size > $mod; $i++) {
		$size /= $mod;
	}

	return round($size, 2) . ' ' . $units[$i];
}
