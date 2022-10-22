<?php
	
function RedirectTo($mysqli, $url) {
	header("Location: ". $url);
	$mysqli->close();
	exit(0);
}


function NewDateStamp($year, $month, $day) {
  return sprintf("%04d%02d%02d", $year, $month, $day);
}


function GetTodayDateStamp() {
  return GetDateStamp(time());
}


function GetDateStamp($timeStamp) {
  $date = localtime($timeStamp, true);
  return sprintf("%04d%02d%02d", $date['tm_year']+1900, $date['tm_mon']+1, $date['tm_mday']);
}


function FormatDateStamp($dateStamp) {
  $year = intval(substr($dateStamp, 0, 4));
  $mon = intval(substr($dateStamp, 4, 2));
  $day = intval(substr($dateStamp, 6, 2));
  return sprintf("%02d/%02d/%4d", $day, $mon, $year);
}


function DateValid($year, $month, $day) {
  $monthDays = array(31,28,31,30,31,30,31,31,30,31,30,31);
  $leap = 0;

  # check for leap year
  if($year % 4 == 0) {
    if($year % 100 == 0) {
      if($year % 400 == 0) {
        $leap = 1;
      }
    }
    else {
      $leap = 1;
    }
  }
  if($leap)
    $monthDays[1] = 29;

  if($year <= 0)
    return false;
  if($month < 1 || $month > 12)
    return false;
  if($day < 1 || $day > $monthDays[$month-1])
    return false;

  return true;
}


function ValidEmailAddress($address) {
	if(!filter_var($address, FILTER_VALIDATE_EMAIL))
   	return false;
  else
    return true;
}


/* Simple email sender

To set a reply address supply extra headers:

Reply-to: reply@to.address

Sending one-part HTML:

$extraHeaders = "MIME-Version: 1.0\n";
$extraHeaders .= "Content-type: text/html; charset=iso-8859-1\n";
$message = "<HTML>sending html!</HTML>";
*/
function EmailSimple($to, $from, $subject, $message, $extraHeaders = "") {
  $headers = "From: $from\n";
  if($extraHeaders != "")
    $headers .= "$extraHeaders";
  return mail($to, $subject, $message, $headers);
}


function EmailSanitise($field) {
  $checkWords = array("bcc:", "cc:", "content-type", "mime-version", "from:", "reply-to",
                      "boundary=", "charset", "content-disposition", "content-type",
                      "content-transfer-encoding", "errors-to", "in-reply-to",
                      "message-id", "multipart/mixed", "multipart/alternative",
                      "multipart/related", "x-mailer", "x-sender", "x-uidl");

  # strip linefeeds
  $lineFeeds = array("\n", "\r", "%0A", "%0D", "%0a", "%0d");
  $field = str_replace($lineFeeds, " ", trim(stripslashes($field)));

  foreach($checkWords as $checkWord) {
    if(strstr(strtolower($field), $checkWord)) {
      echo "Security exception\n";
      exit(1);
    }
  }

  return $field;
}
