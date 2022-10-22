<?php

require 'datadict.php';

function send($queryString, $params) {
	global $gDataDict;
	global $gQuoteHashSecret;
	global $gWebsite;
	global $gSignaturePhotoDir;
	global $gSignaturePhotoURL;
		
	$return = array('success' => true, 'errorcode' => '');

	date_default_timezone_set('Pacific/Auckland');
	
	try {
		# check access token 
		if(!($agentId = ValidAccessToken($params['accesstoken'])))
			throw new Exception("BadCredentials");

		# check arguments
		$locationId = 0;
		if(isset($params['data']['locationid']) && intval($params['data']['locationid']) > 0)
			$locationId = intval($params['data']['locationid']);
		if($locationId == 0)
			throw new Exception("MissingArgument");
		if(!isset($params['data']['preview']) && !isset($params['data']['email']))
			throw new Exception("MissingArgument");
		if(!isset($params['data']['products']) || !is_array($params['data']['products']))
			throw new Exception("MissingArgument");
		
		$mysqli = $params['mysqli'];

		if(!AgentHasPermission($mysqli, $agentId, 'location', $locationId))
			throw new Exception("SecurityViolation");


		# generate quote
		$querySQL = "SELECT l.*, a.email AS aemail, a.firstname AS afirstname, a.lastname AS alastname, a.phone AS aphone, c.firstname, c.lastname FROM location AS l JOIN agent AS a ON l.agentid = a.agentid JOIN customer AS c on l.customerid = c.customerid WHERE l.locationid = $locationId";
		if(!($query = $mysqli->query($querySQL)))
			ThrowDBException($querySQL, $mysqli->error);
		$location = $query->fetch_assoc();
		$query->free();		
		$quoteId = $locationId."-".hash('sha256', $locationId.$gQuoteHashSecret);
		$quoteURL = $gWebsite. "/quote/$quoteId";
		$return['data'] = array('quoteurl' => $quoteURL);
			
		# email quote
		if(!isset($params['data']['preview']) && $params['data']['preview'] != true) {
			$subject = "EVS Glazing Quotation";
			$extraHeaders = "MIME-Version: 1.0\n";
			$extraHeaders .= "Content-type: text/html; charset=iso-8859-1\n";
			$extraHeaders .= "cc: ". $location['aemail']. "\n";
			$message = "<html>";
		
			$message .= "<p><img src=\"$gWebsite/assets/app/logo-quote.png\"></p>\n";
		
			$address = array(htmlspecialchars($location['firstname'].' '.$location['lastname']));
			foreach(array($location['unitnum']. ' '. $location['street'], $location['suburb'], $location['city'], $location['postcode']) as $field) {
				if($field)
					array_push($address, $field);
			}
			$message .= "<br><p>". implode('<br>', $address). "</p><br>";
		
			$message .= "<p>Dear ". htmlspecialchars($location['firstname']). "</p>";
		
			foreach(explode("\n", wordwrap(str_replace("\n", "<br>", htmlspecialchars($params['data']['greeting'])), 80, "\n")) as $line)
				$message .= "$line\n";

			$message .= "<p>To view your quote please <a href=\"$quoteURL\">click here</a></p>\n";
			$message .= "<p>For more information about EVS Glazing products please <a href=\"{$gWebsite}/assets/app/EVS_Glazing_Info.pdf\">click here</a></p>\n";
			$message .= "<p>Kind regards</p>";
			
			if(file_exists($gSignaturePhotoDir."$agentId.png"))
				$message .= "<p><img src=\"". $gSignaturePhotoURL. "$agentId.png\" style=\"width: 80px; height: 80px;\"></p>";
		
			$message .= "<p><b>". htmlspecialchars($location['afirstname'].' '.$location['alastname']). "</b></p>";		
			$message .= "<p>". $location['aphone']. "<br>". $location['aemail']. "</p>";

			$message .= "<p><img src=\"$gWebsite/assets/app/logo-quote.png\" style=\"width: 120px;\"></p>\n";
			
			$message .= "</html>";

			EmailSimple($params['data']['email'], $location['aemail'], $subject, $message, $extraHeaders);
			
			# save product selection & quotedetails
			$productSelection = array(
				'quotesdg' => 0,
				'quotemaxe' => 0,
				'quotexcle' => 0,
				'quoteevsx2' => 0,
				'quoteevsx3' => 0,
			);
			$updateFields = array();
			foreach($params['data']['products'] as $field => $value)
				array_push($updateFields, "$field = ". intval($value));
			array_push($updateFields, "quotedatestamp = ". GetTodayDateStamp());
			array_push($updateFields, "quotegreeting = '". $mysqli->real_escape_string($params['data']['greeting']). "'");
			array_push($updateFields, "quotedetails = '". $mysqli->real_escape_string($params['data']['details']). "'");
			array_push($updateFields, "locationstatusid = 2"); # QS
			array_push($updateFields, "quotelocked = 1");
			$querySQL = "UPDATE location SET ". implode(', ', $updateFields). " WHERE locationid = $locationId";
			if(!($query = $mysqli->query($querySQL)))
				ThrowDBException($querySQL, $mysqli->error);			
		}
	}
	catch(Exception $e) {
		$return['success'] = false;
		$return['errorcode'] = $e->getMessage();
	}
		
	return $return;
}

