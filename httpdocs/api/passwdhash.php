<?php

require('apimodules/session.php');

$passwd = "letmein";
$passwdSalt = "salt";
echo hash('sha256', $passwdSalt.$passwd);


