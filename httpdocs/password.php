<?php

# includes
include("files/constants.php");
include("files/library/session.php");
include("files/library/formlib.php");
include("files/library/common.php");
include("files/library/passwordlib.php");

# connect to database
$mysqli = new mysqli($gDBHost, $gDBUser, $gDBPassword, $gDBName);
if($mysqli->connect_errno)
	throw new Exception($mysqli->connect_error, $mysqli->connect_errno);
$mysqli->set_charset('UTF');

# check session
$adminId = SessionCheck($mysqli);

$error = '';
if($_POST['submit'] != '') {
	if($_POST['current']) {
		
		# get user's password salt
		$querySQL = "SELECT * FROM administrator WHERE adminid = ". intval($adminId);
		if(!($query = $mysqli->query($querySQL)))
			throw new Exception($mysqli->error);
		$admin = $query->fetch_assoc();
		
		if($admin['adminid'] == $adminId) {
			# validate current password
			$currentPasswordHash = HashPassword($_POST['current'], $admin['passwordsalt']);

			if($admin['passwordhash'] != $currentPasswordHash)
				$error = "Current password missing or invalid";
			else {
				# validate new password
				if(!ValidPassword($_POST['password']))
					$error = "New password must be 8 - 40 characters including one number & uppercase letter";
				else {
					# compare password and verify
					if($_POST['password'] != $_POST['verify'])
						$error = "New password mismatch";
					else {
						# finally change the password
						$newPasswordHash = HashPassword($_POST['password'], $admin['passwordsalt']);
						$querySQL = "UPDATE administrator SET passwordhash = '$newPasswordHash' WHERE adminid = $adminId";
						if(!($query = $mysqli->query($querySQL)))
							throw new Exception($mysqli->error);
						$success = "<div class=\"alert alert-success\" role=\"alert\">Password changed</div>";
					}
				}
			}
		}
		else
			$error = "Current password missing or invalid";
	}
	else
		$error = "Current password missing or invalid";
}

# render page
if($error)
	$error = "<div class=\"alert alert-danger\" role=\"alert\">$error</div>";
$pageContent = 'files/templates/password.htm';
include('files/templates/templateadmin.htm');

# done
$mysqli->close();
