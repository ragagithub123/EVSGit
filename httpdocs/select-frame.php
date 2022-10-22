<?php
	
# includes
include("files/constants.php");
include("files/library/session.php");
include("files/library/formlib.php");
include("files/library/common.php");

if(isset($_REQUEST['cancel'])) {
	header("Location: ". $_SERVER['PHP_SELF']."?type=". $_REQUEST["type"]);
	exit(0);
}

# connect to database
$mysqli = new mysqli($gDBHost, $gDBUser, $gDBPassword, $gDBName);
if($mysqli->connect_errno)
	throw new Exception($mysqli->connect_error, $mysqli->connect_errno);
$mysqli->set_charset('UTF');

$adminId = SessionCheck($mysqli);
if($_POST['selectid']=='All')
$querySQL = "SELECT paneloption_frametype.`frametypeid`, paneloption_frametype.`name`, paneloption_frametype.`imageid`, famecategory.category FROM `paneloption_frametype`,`famecategory` WHERE paneloption_frametype.category=famecategory.famecategoryid";
else
$querySQL = "SELECT paneloption_frametype.`frametypeid`, paneloption_frametype.`name`, paneloption_frametype.`imageid`, famecategory.category FROM `paneloption_frametype`,`famecategory` WHERE paneloption_frametype.category=famecategory.famecategoryid AND paneloption_frametype.".$_POST['selectid']."=1";
if(!($query = $mysqli->query($querySQL)))
 throw new Exception($mysqli->error);
?>
<table class="table table-striped">
<thead>
     <tr><th colspan=2>Name</th><th>Icon</th><th>Category</th><th></th><th style="text-align: right; font-weight: normal;"><a href="panel-copy.php">Add</a></th></tr>
     
    <?php 
		
		while($option = $query->fetch_assoc()){
					echo "<tr><td>". htmlspecialchars($option['name']). "</td><td></td>";
					 if($option['imageid'] > 0 && file_exists($gPanelOptionsPhotoDir.$option['imageid'].".png"))
          {
              echo "<td><img src=\"". $gPanelOptionsPhotoURL.$option['imageid'].".png?". time(). "\" class=\"img-responsive\" style=\"width: 50px; height; 50px;\"></td>";
          }
          else
          {
              echo "<td></td>";
          }
				    echo "<td>".$option['category']."</td><td>$default</td>  <td style=\"text-align: right;\"><a href=\"paneloptions.php?type=frametype&id=". $option['frametypeid']. "\">Edit</a></td></tr>\n";
			
		}?>
</thead>
</table>
