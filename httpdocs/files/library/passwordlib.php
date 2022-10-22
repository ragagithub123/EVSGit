<?php

function GenerateSalt() {
	$characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
  $salt = '';
  $max = strlen($characters) - 1;
  for($i=0; $i<20; $i++)
    $salt .= $characters[mt_rand(0, $max)];

	return $salt;
}

function HashPassword($password, $salt) {
	return hash('sha256', $salt.$password);
}

function ValidPassword($password) {
	if(strlen($password) < 8 || strlen($password) > 100) # check length
		return false;
	if(!preg_match("/[0-9]/", $password)) # check for at least one numeric
		return false;
	if(!preg_match("/[A-Z]/", $password)) # check for at least one uppercase
		return false;
		
	return true;
}