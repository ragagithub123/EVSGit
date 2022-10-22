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

if($_POST['selectid']=='All'){
$querySQL = "SELECT paneloption_style.`styleid`,paneloption_style.`frametypeid`,paneloption_style.`name`,paneloption_style.`IGUassemble`,paneloption_style.EVSassemble,paneloption_style.styledgvalue,paneloption_style.styleevsvalue,famecategory.category AS famecategory FROM `paneloption_style`,famecategory WHERE paneloption_style.category=famecategory.famecategoryid ORDER BY paneloption_style.name ASC";
if(!($query = $mysqli->query($querySQL)))
throw new Exception($mysqli->error);
}

else{
$getframescat = "SELECT famecategoryid FROM famecategory WHERE material_tag='".$_POST['selectid']."'";
	if(!($query = $mysqli->query($getframescat)))
 throw new Exception($mysqli->error);
		while($cat = $query->fetch_array()){
			$catids[] = $cat['famecategoryid']; 
			}
		if(count($catids>1)) $cat_ids = join(',',$catids);
		 else $cat_ids = $catids[0];
			
			$querySQL = "SELECT paneloption_style.`styleid`,paneloption_style.`frametypeid`,paneloption_style.`name`,paneloption_style.`IGUassemble`,paneloption_style.EVSassemble,paneloption_style.styledgvalue,paneloption_style.styleevsvalue,famecategory.category AS famecategory FROM `paneloption_style`,famecategory WHERE paneloption_style.category=famecategory.famecategoryid AND paneloption_style.category IN($cat_ids) ORDER BY paneloption_style.name ASC";
if(!($query = $mysqli->query($querySQL)))
 throw new Exception($mysqli->error);

	}


?>
<table class="table table-striped">
<thead>
     <tr><th>Option</th><th>Icons</th><th>Category</th><th>Frametypeid</th><th>IGU Assemble</th><th>EVS Assemble</th><th>DG Value</th><th>EVS Value</th><th style="text-align: right; font-weight: normal;"><a href="paneloptions.php?type=style&id=add">Add</a></th></tr>
      <?php 
		
		while($option = $query->fetch_assoc()){
			
			$getimageid="SELECT imageid FROM paneloption_frametype WHERE frametypeid='".$option['frametypeid']."'";
		if(!($query_image = $mysqli->query($getimageid)))
		throw new Exception($mysqli->error);
		$row_imageid = $query_image->fetch_assoc();
		$option['imageid']=$row_imageid['imageid'];
					echo "<tr><td>". htmlspecialchars($option['name']). "</td>";
					 if($option['styleid'] > 0 && file_exists($gPanelOptionsPhotoDir.$option['styleid'].".png"))
          {
              echo "<td><img src=\"". $gPanelOptionsPhotoURL.$option['styleid'].".png?". time(). "\"  style=\"width: 50px; height; 50px;\"></td>";
          }
										 else if(file_exists($gPanelOptionsPhotoDir.$option['imageid'].".png")){
              
              echo "<td><img src=\"". $gPanelOptionsPhotoURL.$option['imageid'].".png?".time(). "\"  style=\"width: 50px; height; 50px;\"></td>";
              }
          else
          {
              echo "<td></td>";
          }
				   echo "<td>".$option['famecategory']."</td><td>".$option['frametypeid']."</td> <td>".$option['IGUassemble']."</td> <td>".$option['EVSassemble']."</td><td>".$option['styledgvalue']."</td> <td>".$option['styleevsvalue']."</td><td style=\"text-align: right;\"><a href=\"paneloptions.php?type=style&id=". $option['styleid']. "\">Edit</a></td></tr>\n";
			
		}?>
   
</thead>
</table>
