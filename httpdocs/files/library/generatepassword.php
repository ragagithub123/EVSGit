<?php
# generates a new password-salt pair - use to manually create a new admin user

include('passwordlib.php');

$password = substr(GenerateSalt(), 0, 8); # random 8 char password
$password = "admin";
$salt = GenerateSalt();
$passwordHash = HashPassword($password, $salt);

echo "<p>password: $password<br>";
echo "salt: $salt<br>";
echo "passwordHash: $passwordHash</p>";


