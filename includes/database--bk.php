<?php
session_start();
ini_set('display_errors','0');
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'admin_evsapp');
define('DB_PASSWORD', '&Dl5w9m1');
define('DB_DATABASE', 'db.evsapp');

$root  = "http://".$_SERVER['HTTP_HOST'];
$root .= '/';
define('SITEURL', $root);

class Database
{
	function __construct() {
		session_start();
        $connection = mysql_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD) or die('Oops connection error -> ' . mysql_error());
        mysql_select_db(DB_DATABASE, $connection) or die('Database error -> ' . mysql_error());
    }
	
    public function ins_rec($tab,$array,$disp=false)	
	{	
		$array = Database::add_slashes($array);
				
		$qry = "insert into $tab set ";
		if (count($array) > 0)
		{
			foreach ($array as $k=>$v)
			{
				if($v != "")
				{
					$qry .= "`$k`='".$v."',";
				}
			}
		}		
		$qry=trim($qry,",");		
		if ($disp)
			echo $qry;		
		$err = mysql_query($qry);		
		if (!$err)
		{
			echo mysql_error()." - <b>".$qry."</b>";
			return false;
		}
		else
		{
			return mysql_insert_id();
		}
	}

    public function upd_rec($tab,$array,$where="1=1",$disp=false)	
	{	
		$array = Database::add_slashes($array);
		$qry = "update $tab set ";
		if (count($array) > 0)
		{
			foreach ($array as $k=>$v)
			{				
					$qry .= "$k='".$v."',";
			
			}
		}
			
		$qry=trim($qry,",")." where ".$where;
		if ($disp)
			echo $qry;
		
		$err = mysql_query($qry);		
		
		if (!$err)
		{
			echo mysql_error()." - <b>".$qry."</b>";
			return false;
		}
		else
			return true;
	}
	public function add_slashes($var)
	{
		if (is_array($var))
		{
			if (count($var) > 0)
			{
				foreach ($var as $k=>$v)	
				{
					$var[$k] = addslashes($v);
				}
			}
			return $var;
		}
		else
			return addslashes($var);
	}
	
	public function strip_slashes($var)
	{
		if (is_array($var))
		{
			if (count($var) > 0)
			{
				foreach ($var as $k=>$v)	
				{
					$var[$k] = stripslashes($v);
				}
			}
			return $var;
		}
		else
			return stripslashes($var);
	}
	
	public function is_domain_count($table,$field,$value,$disp=false)
	{
		$q = "select * from ".$table." where ".$field." = '".$value."'"; 
		if ($disp)
			echo ($q);
		$r = mysql_query($q);
		if(mysql_num_rows($r) > 0)
			return true;
		else
			return false;
	}
	//...............................check for users domain count [end]................
	
	public function is_dup_add($table,$field,$value,$disp=false)
	{
		$q = "select ".$field." from ".$table." where ".$field." = '".$value."'"; 
		if ($disp)
			echo ($q);
		$r = mysql_query($q);
		if(mysql_num_rows($r) > 0)
			return true;
		else
			return false;
	}
	//...............................check for duplication row in a table while adding new row [end]................
	
	//...............................check for duplication row in a table while updating any row [start]................
	public function is_dup_edit($table,$field,$value,$tableid,$id,$disp=false)
	{
		$q = "select ".$field." from ".$table." where ".$field." = '".$value."' and ".$tableid."!= '".$id."'"; 
		if ($disp)
			echo($q);
		$r = mysql_query($q);
		if(!$r)
			echo mysql_error();
		if(mysql_num_rows($r) > 0)
			return true;
		else
			return false;
	}	
	public function upload_image($files, $dir, $oldfile ,$prefix)
	{
		if(!is_dir ($dir))
		{
			mkdir($dir,0777);
			chmod($dir,0777);	
		}	
		if($oldfile != "" && is_file($dir.$oldfile))
		{
			unlink($dir.$oldfile);
		}		
		
		$image_name = preg_replace("/[^a-zA-Z.]+/", "", $files[name]);
		
		$filename = $prefix."".rand(0,999999999999)."-".$image_name;	
		//$filename = $prefix."".rand(0,999999999999)."-".$files[name];	
		if (is_file($dir.$filename))
			$filename = $prefix."".rand(0,999999999999)."-".rand(0,999999999999)."-".$image_name;
			//$filename = $prefix."".rand(0,999999999999)."-".rand(0,999999999999)."-".$files[name];
		if (@move_uploaded_file($files[tmp_name],$dir.$filename))
			return $filename;
		else
			return false;
	}
	public function sel_rec($tab,$fields="*",$where="1=1",$orderby="1",$order="desc",$limit="",$disp=false) 
	{	
		$qry="select $fields from $tab where $where order by $orderby $order $limit"; 	
			
		if($disp)
			echo $qry;		
		$res=mysql_query($qry);		
		if(!$res)	
			echo mysql_error()."-<b>".$qry."</b>";		
		if(mysql_num_rows($res)>0)
			return $res;
		else
			return false;		
	}
	
	
	

	public function sel_rec_asc($tab,$fields="*",$where="1=1",$orderby="1",$order="asc",$limit="",$disp=false) 
	{	
		$qry="select $fields from $tab where $where group by pid order by  $orderby $order $limit"; 	
			
		if($disp)
			echo $qry;		
		$res=mysql_query($qry);		
		if(!$res)	
			echo mysql_error()."-<b>".$qry."</b>";		
		if(mysql_num_rows($res)>0)
			return $res;
		else
			return false;		
	}
	public function joinquery($query)
	{
		 $res=mysql_query($query);		
		if(!$res)	
			echo mysql_error()."-<b>".$query."</b>";		
		if(mysql_num_rows($res)>0)
			return $res;
		else
			return false;		
	}
	public function sel_rec_desc($tab,$fields="*",$where="1=1",$orderby="1",$order="desc",$limit="",$disp=false) 
	{	
		$qry="select $fields from $tab where $where group by pid order by  $orderby $order $limit"; 	
			
		if($disp)
			echo $qry;		
		$res=mysql_query($qry);		
		if(!$res)	
			echo mysql_error()."-<b>".$qry."</b>";		
		if(mysql_num_rows($res)>0)
			return $res;
		else
			return false;		
	}
	
	public function sel_img_hea($tab,$field,$temp) 
	{	
		$qry="select id,image_name from $tab where $field LIKE '%$temp%' and Status = 'Y' ORDER BY id DESC"; 	
			
		if($disp)
			echo $qry;		
		$res=mysql_query($qry);		
		if(!$res)	
			echo mysql_error()."-<b>".$qry."</b>";		
		if(mysql_num_rows($res)>0)
			return $res;
		else
			return false;		
	}
	public function sel_img_hea_new($tab,$field,$temp,$exist_img) 
	{	
		$qry="select id,image_name from $tab where $field LIKE '%$temp%' and Status = 'Y' and image_name!='".$exist_img."' ORDER BY id DESC"; 	
			
		if($disp)
			echo $qry;		
		$res=mysql_query($qry);		
		if(!$res)	
			echo mysql_error()."-<b>".$qry."</b>";		
		if(mysql_num_rows($res)>0)
			return $res;
		else
			return false;		
	}
	
	public function sel_rec_group($tab,$id,$uid,$limit="") 
	{	
		$qry="select * from $tab where uid='$uid' GROUP BY $id $limit"; 	
			
		if($disp)
			echo $qry;		
		$res=mysql_query($qry);		
		if(!$res)	
			echo mysql_error()."-<b>".$qry."</b>";		
		if(mysql_num_rows($res)>0)
			return $res;
		else
			return false;		
	}
	
	public function sel_rec_query($qry,$disp=false) 
	{	
			
		if($disp)
			echo $qry;		
		$res=mysql_query($qry);		
		if(!$res)	
			echo mysql_error()."-<b>".$qry."</b>";		
		if(mysql_num_rows($res)>0)
			return $res;
		else
			return false;		
	}
	
	public function get_single_value($tab,$fields,$where="1=1",$orderby="1",$order="desc",$limit="",$disp=false)
	{
		$res = Database::sel_rec($tab,$fields,$where,$orderby,$order,$limit,$disp);
		if ($res)
		{
			$val = mysql_fetch_array($res);
			return Database::strip_slashes($val[$fields]);
		}
		else
			return false;
	}
	public function time_ago( $datefrom , $dateto=-1 )
	{
	   if($datefrom<=0) { return "A long time"; }
	   if($dateto==-1) { $dateto = time(); }
	   
	   $difference = $dateto - $datefrom;
	   
	   // Seconds
	   if($difference < 60)
	   {
		  $time_ago   = $difference . ' second' . ( $difference > 1 ? 's' : '' ).'';
	   }
	   
	   // Minutes
	   else if( $difference < 60*60 )
	   {
			 $ago_seconds   = $difference % 60;
			$ago_seconds   = ( ( $ago_seconds AND $ago_seconds > 1 ) ? ' and '.$ago_seconds.' seconds' : ( $ago_seconds == 1 ? ' and '.$ago_seconds.' second' : '' ) );
			$ago_minutes   = floor( $difference / 60 );
			$ago_minutes   = $ago_minutes . ' minute' . ( $ago_minutes > 1 ? 's' : '' ).'';
			$time_ago      = $ago_minutes.'';
	   }
	   
	   // Hours
	   else if ( $difference < 60*60*24 )
	   {
			 $ago_minutes   = round( $difference / 60 ) % 60 ;
		   $ago_minutes   = ( ( $ago_minutes AND $ago_minutes > 1 ) ? ' and ' . $ago_minutes . ' minutes' : ( $ago_minutes == 1 ? ' and ' . $ago_minutes .' minute' : '' ));
		   $ago_hours      = floor( $difference / ( 60 * 60 ) );
		   $ago_hours      = $ago_hours . ' hour'. ( $ago_hours > 1 ? 's' : '' );
		   $time_ago      = $ago_hours.'';
	   }
	   
	   // Days
	   else if ( $difference < 60*60*24*7 )
	   {
		  $ago_hours      = round( $difference / 3600 ) % 24 ;
		  $ago_hours      = ( ( $ago_hours AND $ago_hours > 1 ) ? ' and ' . $ago_hours . ' hours' : ( $ago_hours == 1 ? ' and ' . $ago_hours . ' hour' : '' ));
		  $ago_days      = floor( $difference / ( 3600 * 24 ) );
		  $ago_days      = $ago_days . ' day' . ($ago_days > 1 ? 's' : '' );
		  $time_ago      = $ago_days.'';
	   }
	   
	   // Weeks
	   else if ( $difference < 60*60*24*30 )
	   {
		  $ago_days      = round( $difference / ( 3600 * 24 ) ) % 7;
		  $ago_days      = ( ( $ago_days AND $ago_days > 1 ) ? ' and '.$ago_days.' days' : ( $ago_days == 1 ? ' and '.$ago_days.' day' : '' ));
		  $ago_weeks      = floor( $difference / ( 3600 * 24 * 7) );
		  $ago_weeks      = $ago_weeks . ' week'. ($ago_weeks > 1 ? 's' : '' );
		  $time_ago      = $ago_weeks.'';
	   }   
	   // Months
	   else if ( $difference < 60*60*24*365 )
	   {
		  $days_diff   = round( $difference / ( 60 * 60 * 24 ) );
		  $ago_days   = $days_diff %  30 ;
		  $ago_weeks   = round( $ago_days / 7 ) ;
		  $ago_weeks   = ( ( $ago_weeks AND $ago_weeks > 1 ) ? ' and '.$ago_weeks.' weeks' : ( $ago_weeks == 1 ? ' and '.$ago_weeks.' week' : '' ) );
		  $ago_months   = floor( $days_diff / 30 );
		  $ago_months   = $ago_months .' month'. ( $ago_months > 1 ? 's' : '' );
		  $time_ago   = $ago_months.'';
	   }   
	   // Years
	   else if ( $difference >= 60*60*24*365 )
	   {
		  $ago_months   = round( $difference / ( 60 * 60 * 24 * 30.5 ) ) % 12;
		  $ago_months   = ( ( $ago_months AND $ago_months > 1 ) ? ' and ' . $ago_months . ' months' : ( $ago_months == 1 ? ' and '.$ago_months.' month' : '' ) );
		  $ago_years   = floor( $difference / ( 60 * 60 * 24 * 365 ) );#30 * 12
		  $ago_years   = $ago_years . ' year'. ($ago_years > 1 ? 's' : '' ) ;
		  $time_ago   = $ago_years.'';
	   }   
	   return $time_ago;
	}
	public function single_row($tab,$fields="*",$where="1=1",$orderby="1",$order="desc",$limit="",$disp=false)
	{
		$res_single = Database::sel_rec($tab,$fields,$where,$orderby,$order,$limit,$disp);
		//echo mysql_num_rows($res_single); exit;
		if ($res_single != false && mysql_num_rows($res_single) > 0)
		{
			//echo "test"; exit;
			return Database::strip_slashes(mysql_fetch_array($res_single));
		}
		else
			return false;
	}
	public function SendHTMLMail($to,$subject,$mailcontent,$from1,$cc="")
	{
			$limite = "_parties_".md5 (uniqid (rand()));
			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
			if ($cc != "")
			{
				$headers .= "Cc: $cc\r\n";
			}
			$headers .= "From: $from1\r\n";
			$res=@mail($to,$subject,$mailcontent,$headers);
			return $res;
	}
	public function generate_password($len)
	{
		$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
		for($i=0; $i<$len; $i++) $r_str .= substr($chars,rand(0,strlen($chars)),1);
		return $r_str;
	}
	public function del_rec($tab,$where="1=1",$disp=false)
	 {
		$qry="delete from $tab where $where";
		if($disp)
			echo $qry;			
		$err=mysql_query($qry);
		if(!$err)
		{
			echo mysql_error()." - <b>".$qry."</b>";
			return false;
		}
		else
			return true;
	 }
	 
	 public function del_rec_slide($tab,$where)
	 {
		$qry="delete from $tab where $where";
		if($disp)
			echo $qry;			
		$err=mysql_query($qry);
		if(!$err)
		{
			echo mysql_error()." - <b>".$qry."</b>";
			return false;
		}
		else
			return true;
	 }
	public function del_file($path)
	 {
	 	if(is_file($path))
	 	{
			unlink($path);
			return true;
		}
		else
			return false;
	 }
	public function cut_str($str, $len, $suffix="...") {
		$s = substr($str, 0, $len);
		$cnt = 0;
		for ($i=0; $i<strlen($s); $i++)
			if (ord($s[$i]) > 127)
				$cnt++;
	
		$s = substr($s, 0, $len - ($cnt % 3));
	
		if (strlen($s) >= strlen($str))
			$suffix = "";
		return $s . $suffix;
	}
	public function encode($string,$key) {
	    $key = sha1($key);
	    $strLen = strlen($string);
	    $keyLen = strlen($key);
	    for ($i = 0; $i < $strLen; $i++) {
	        $ordStr = ord(substr($string,$i,1));
	        if ($j == $keyLen) { $j = 0; }
	        $ordKey = ord(substr($key,$j,1));
	        $j++;
	        $hash .= strrev(base_convert(dechex($ordStr + $ordKey),16,36));
	    }
	    return $hash;
	}
	
	public function decode($string,$key) {
	    $key = sha1($key);
	    $strLen = strlen($string);
	    $keyLen = strlen($key);
	    for ($i = 0; $i < $strLen; $i+=2) {
	        $ordStr = hexdec(base_convert(strrev(substr($string,$i,2)),36,16));
	        if ($j == $keyLen) { $j = 0; }
	        $ordKey = ord(substr($key,$j,1));
	        $j++;
	        $hash .= chr($ordStr - $ordKey);
	    }
	    return $hash;
	}
	
	public function make_time ($day, $hour, $min){
		$time = date('Y-m-d', strtotime($day));
		$time .= " ";
		$time .= $hour;
		$time .= ":";
		$time .= $min;
		return $time;
	}
	
	public function getTime ($time){
		$day = date('m/d/Y', strtotime($time));
		$hour = date('H', strtotime($time));
		$min = date('i', strtotime($time));
		$datetime = array(
							'day'	=> $day,
							'hour'	=> $hour,
							'min'	=> $min
						);
		return $datetime;
	}
	
# Send Pushnotification
	public function sendPushNotification($deviceToken, $certFile, $certPass, $push_method, $alert, $badge, $sound, $custom_arrays) 
	 { 
	
	     $deviceToken = str_replace(" ", "", $deviceToken); 
	     $deviceToken = pack('H*', $deviceToken); 
	     $tmp = array(); 
	     if($alert) 
	     { 
	  		$tmp['alert'] = $alert; 
	     } 
	     if($badge) 
	     { 
	  		$tmp['badge'] = $badge; 
	     } 
	     if($sound) 
	     { 
	  		$tmp['sound'] = $sound; 
	     } 
	     $body['aps'] = $tmp; 
	     
	     foreach ($custom_arrays as $key => $value) {
	     	$body[$key] = $value; 
	     }
	     $ctx = stream_context_create(); 
	     stream_context_set_option($ctx, 'ssl', 'local_cert', $certFile); 
	     stream_context_set_option($ctx, 'ssl', 'passphrase', $certPass); 
	     
	     if ( $push_method == 'develop' )
	      	$ssl_gateway_url = 'ssl://gateway.sandbox.push.apple.com: 2195';
	     else if ( $push_method == 'live' )
	      	$ssl_gateway_url = 'ssl://gateway.push.apple.com: 2195';
	      
	     if(isset($certFile) && isset($ssl_gateway_url)) 
	     {
	      	$fp = stream_socket_client($ssl_gateway_url, $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx); 
	     }
	     if(!$fp) 
	     { 
	   		print "Connection failed $err $errstr\n"; 
	   		return FALSE; 
	     } 
	     $payload = json_encode($body); 
	     $msg = chr(0).chr(0).chr(32).$deviceToken.chr(0).chr(strlen($payload)).$payload; 
	     fwrite($fp, $msg); 
	     fclose($fp);      
	
	     
	     return TRUE;
	 }
	 
	 

	 
}

class Encryption {
    var $skey = "Doctor App!"; // you can change it

    public  function safe_b64encode($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }

    public function safe_b64decode($string) {
        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    public  function encode($value){ 
        if(!$value){return false;}
        $text = $value;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->skey, $text, MCRYPT_MODE_ECB, $iv);
        return trim($this->safe_b64encode($crypttext)); 
    }

    public function decode($value){
        if(!$value){return false;}
        $crypttext = $this->safe_b64decode($value); 
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->skey, $crypttext, MCRYPT_MODE_ECB, $iv);
        return trim($decrypttext);
    }
}