<?php
# includes
include("files/constants.php");
include("files/library/session.php");
include("files/library/formlib.php");
include("files/library/common.php");

# connect to database
$mysqli = new mysqli($gDBHost, $gDBUser, $gDBPassword, $gDBName);
if($mysqli->connect_errno)
	throw new Exception($mysqli->connect_error, $mysqli->connect_errno);
$mysqli->set_charset('UTF');

$adminId = SessionCheck($mysqli);


if($_POST['savetype'] == "tool"){
	
		$savetool="INSERT INTO `taskTools`(`name`, `categoryid`, `type`, `toolid`) VALUES ('".$_POST['toolname']."','".$_POST['categroy']."','".$_POST['type']."','".$_POST['tool']."') ";
		if(!($query_save = $mysqli->query($savetool)))
		throw new Exception($mysqli->error);
		
		echo $_POST['tool'];

	
}

if($_POST['savetype'] == 'ediittool'){
	
$query_tool ="SELECT `name` FROM taskTools WHERE tasktoolid='".$_POST['toolid']."'";
if(!($query = $mysqli->query($query_tool)))
		throw new Exception($mysqli->error);
	
	while($row_tool = $query->fetch_array())
	
	echo $row_tool['name'];
		
	
}

if($_POST['savetype'] == 'updatetool'){
	
	$query_tool ="UPDATE taskTools SET name='".$_POST['tool']."' WHERE tasktoolid='".$_POST['toolid']."'";
if(!($query = $mysqli->query($query_tool)))
		throw new Exception($mysqli->error);
 echo 'success';
	
}
if($_POST['savetype'] == 'deletetool'){
	
	$query_tool ="DELETE FROM taskTools WHERE tasktoolid='".$_POST['toolid']."'";
if(!($query = $mysqli->query($query_tool)))
		throw new Exception($mysqli->error);
 echo 'success';
	
}


if($_POST['savetype']== "material"){
	
		$savemat="INSERT INTO `taskMaterials`(`name`,`categoryid`,`type`) VALUES ('".$_POST['materilname']."','".$_POST['categroy']."','".$_POST['type']."') ";
		if(!($query_mat = $mysqli->query($savemat)))
		throw new Exception($mysqli->error);
		
		echo $_POST['materilname'];

	
}

if($_POST['savetype'] =='materiallist'){
	
	$query_mat ="SELECT `name` FROM taskMaterials WHERE taskmaterialid='".$_POST['materialid']."'";
if(!($query = $mysqli->query($query_mat)))
		throw new Exception($mysqli->error);
	
	while($row_mat = $query->fetch_array())
	
	echo $row_mat['name'];


}

if($_POST['savetype'] =='updatematerial'){
	
		$query_mats ="UPDATE taskMaterials SET name='".$_POST['material']."' WHERE taskmaterialid='".$_POST['materialid']."'";
if(!($query = $mysqli->query($query_mats)))
		throw new Exception($mysqli->error);
 echo 'success';

	
}
if($_POST['savetype'] =='deletematerial'){
	
		$query_matdel ="DELETE FROM taskMaterials WHERE taskmaterialid='".$_POST['materialid']."'";
if(!($query = $mysqli->query($query_matdel)))
		throw new Exception($mysqli->error);
 echo 'success';
	
}

if($_POST['savetype'] == 'copytoolmat'){
	
	$mysqli->query("DELETE FROM taskTools WHERE categoryid='".$_POST['categroy']."' AND type='".$_POST['choosedtype']."'");
	
	$mysqli->query("DELETE FROM taskMaterials WHERE categoryid='".$_POST['categroy']."' AND type='".$_POST['choosedtype']."'");
	
	$query_copy ="SELECT * FROM taskTools WHERE categoryid ='".$_POST['cateid']."' AND type='".$_POST['type']."'";
	if(!($query = $mysqli->query($query_copy)))
		throw new Exception($mysqli->error);
	
	while($row_copy = $query->fetch_array()){
		
		$savetool="INSERT INTO `taskTools`(`name`, `categoryid`, `type`, `toolid`) VALUES ('".$row_copy['name']."','".$_POST['categroy']."','".$_POST['choosedtype']."','".$row_copy['toolid']."') ";
		if(!($query_save = $mysqli->query($savetool)))
		throw new Exception($mysqli->error);
		
	}
	
		$query_mat ="SELECT * FROM taskMaterials WHERE categoryid ='".$_POST['cateid']."' AND type='".$_POST['type']."'";
 if(!($query = $mysqli->query($query_mat)))
		throw new Exception($mysqli->error);
	
	while($row_mat = $query->fetch_array()){
		
				$savemat="INSERT INTO `taskMaterials`(`name`,`categoryid`,`type`) VALUES ('".$row_mat['name']."','".$_POST['categroy']."','".$_POST['choosedtype']."') ";
		if(!($query_mat = $mysqli->query($savemat)))
		throw new Exception($mysqli->error);

		
	}


echo 'success';

}

