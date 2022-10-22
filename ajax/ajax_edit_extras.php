<?php ob_start();
session_start();
include('../includes/functions.php');

if(($_POST['status'] ==2) || ($_POST['status']==4) ){
		$getlocationid=$db->joinquery("SELECT room.locationid,window_extras.windowid FROM window_extras,window,room WHERE window_extras.windowid=window.windowid AND window.roomid=room.roomid AND window_extras.extraid='".$_POST['extraid']."'");
	  $row_locid=mysqli_fetch_array($getlocationid);
			$locationid=$row_locid['locationid'];
			$windowid=$row_locid['windowid'];

}

if(($_POST['status'] ==3)){
	  $getlocationid=$db->joinquery("	SELECT room.locationid FROM window,room WHERE window.roomid=room.roomid AND window.windowid='".$_POST['windowid']."'");
	  $row_locid=mysqli_fetch_array($getlocationid);
			$locationid=$row_locid['locationid'];
			$windowid=$_POST['windowid'];
}


 if($_POST['status']==2){
	
	$db->joinquery("UPDATE window_extras SET quantity='".$_POST['quantity']."',cost='".$_POST['cost']."' WHERE extraid='".$_POST['extraid']."'");
	
}
if($_POST['status']==3){

	$db->joinquery("INSERT INTO window_extras(windowid,productid,quantity,cost)VALUES('".$_POST['windowid']."','".$_POST['product']."','".$_POST['quantity']."','".$_POST['cost']."')");
}
if($_POST['status']==4){
	$db->joinquery("DELETE FROM window_extras WHERE extraid='".$_POST['extraid']."'");
}

# Calculate Cost

# get total panel labour and style costs for this window
		$querySQL=$db->joinquery("SELECT SUM(dglabour) AS dglabour, SUM(evslabour) AS evslabour, SUM(costsdg) AS sumcostsdg, SUM(costmaxe) AS sumcostmaxe, SUM(costxcle) AS sumcostxcle, SUM(costevsx2) AS sumcostevsx2, SUM(costevsx3) AS sumcostevsx3 FROM panel WHERE windowid = '$windowid'");
		$row_labour=mysqli_fetch_array($querySQL);
	
	
	# calc window travel total
	
	
	$querySQL1 = $db->joinquery("SELECT agentid,travel_status,distance,number_staff FROM location WHERE locationid='$locationid'");

	$rowtravel =mysqli_fetch_array($querySQL1);
	
		if($rowtravel['travel_status']==0){
		
					
					$travelSDG=0;
					$travelMAXe=0;
					$travelXCLe=0;
					$travelEVSx2=0;
					$travelEVSx3=0;
					
				}else{
					$queryMargins =$db->joinquery("SELECT labourrate,travelrate FROM location_margins WHERE locationid='$locationid'");
				
					$margins = mysqli_fetch_array($queryMargins);
					$labourrate=$margins['labourrate'];
					$travelrate=$margins['travelrate'];
					$travelDaysEVS = ($row_labour['evslabour'] / (7 * $rowtravel['number_staff']));
					$travelHoursEVS = ((($rowtravel['distance'] * 2) * $rowtravel['number_staff']) / 90) * $travelDaysEVS;
					$travelEVSx2 = $travelEVSx3 = round((((($rowtravel['distance'] * 2) * $travelDaysEVS) * $travelrate) + ($travelHoursEVS * $labourrate)), 2);
					$travelDaysIGU = ($row_labour['dglabour'] / (5 * $rowtravel['number_staff']));
					$travelHoursIGU = ((($rowtravel['distance'] * 2) * $rowtravel['number_staff']) / 90) * $travelDaysIGU;
					$travelSDG = $travelMAXe = $travelXCLe = round((((($rowtravel['distance'] * 2) * $travelDaysIGU) * $travelrate) + ($travelHoursIGU * $labourrate)), 2);
					
		
					/*$travelDaysEVS=($row_labour['evslabour']/(7*$rowtravel['number_staff']));
				
						$travelHoursEVS = ((($rowtravel['distance']*2)*$rowtravel['number_staff'])/90)*$travelDaysEVS;
						$travelEVSx2 = $travelEVSx3 =round((((($rowtravel['distance']*2)*$travelDaysEVS)*$travelrate)+($travelHoursEVS*$labourrate)),2);
																									
						$travelDaysIGU=($row_labour['dglabour']/(5*$rowtravel['number_staff']));
						$travelHoursIGU = ((($rowtravel['distance']*2)*$rowtravel['number_staff'])/90)*$travelDaysIGU;
				    $travelSDG=$travelMAXe=$travelXCLe =round((((($rowtravel['distance']*2)*$travelDaysIGU)*$travelrate)+($travelHoursIGU*$labourrate)),2);*/
			
				
			}
	
	$queryExtras=$db->joinquery("SELECT sum(cost) AS total_extras FROM `window_extras` WHERE windowid='$windowid'");
	$extras = mysqli_fetch_array($queryExtras);
  //#### New Changes ####14-10-2022///
	if($row_labour['sumcostsdg']==0 || $row_labour['sumcostsdg']==0.0)
	$travelSDG=0;
	if($row_labour['sumcostmaxe']==0 || $row_labour['sumcostmaxe']==0.0)
	$travelMAXe=0;
	if($row_labour['sumcostxcle']==0 || $row_labour['sumcostxcle']==0.0)
	$travelXCLe=0;
	if($row_labour['sumcostevsx2']==0 || $row_labour['sumcostevsx2']==0.0)
	$travelEVSx2=0;
	if($row_labour['sumcostevsx3']==0 || $row_labour['sumcostevsx3']==0.0)
	$travelEVSx3=0;
	//#### End New Changes ####14-10-2022///
	# finally calc window style costs

	$costSDG = $row_labour['sumcostsdg'] + $extras['total_extras'] + $travelSDG;
	$costMAXe = $row_labour['sumcostmaxe'] + $extras['total_extras'] + $travelMAXe;
	$costXCLe = $row_labour['sumcostxcle'] + $extras['total_extras'] + $travelXCLe;
	$costEVSx2 = $row_labour['sumcostevsx2'] + $extras['total_extras'] + $travelEVSx2;
	$costEVSx3 = $row_labour['sumcostevsx3'] + $extras['total_extras'] + $travelEVSx3;
	$db->joinquery("UPDATE window SET costsdg = $costSDG, costmaxe = $costMAXe, costxcle = $costXCLe, costevsx2 = $costEVSx2, costevsx3 = $costEVSx3 WHERE windowid = '$windowid'");
#end calucllation


$get_extras1=$db->joinquery("SELECT window_extras.*,products.* FROM window_extras,products WHERE window_extras.productid=products.productid AND window_extras.windowid='".$_POST['windowid']."' ORDER BY window_extras.extraid DESC");

if($_POST['pagestatus']==1)
$popupid="myModalExtra";
else
$popupid="myModalExtraView";

	?>
 <table class="table">
  <tr><td style="border:none;"><input type="hidden" name="page-status" value="<?php echo $_POST['pagestatus'];?>"/>
  <input type="hidden" id="list-windowid" value="<?php echo $_POST['windowid'];?>"/></td></tr>
        
        <?php
											while($row_extras1=mysqli_fetch_array($get_extras1)){
													if($row_extras1['imageid']==0)
								    $row_extras1['imageid']=$row_extras1['productid'];
												//$hours=round(($row_extras1['hours']*$row_extras1['sizevalue']),2);
												?>
											 
                             
                              <tr>
                              <?php
                              if(file_exists($gSupplierProdcutsDir.$row_extras1['imageid'].".png")){
                                         echo '<td><img src="'.$gSupplierProdcutsURL.$row_extras1['imageid'].'.png'.'" style="width:20px"></td>';
                                         }
                                         else{
                                          echo '<td></td>';
                                         }
                                         ?>
                              <td><?php echo $row_extras1['name'];?></td>
                               <td>$<?php echo $row_extras1['cost'];?><?php //echo $row_extras1['unittag'];?> </td>
                               <!-- <td><?php //echo $row_extras1['sizevalue'];?>&#10006;<?php //echo $row_extras1['unitname'];?></td>-->
                               <td><?php echo $row_extras1['unitname'];?></td>
                                <td><?php echo $row_extras1['hours'];?>&nbsp;Hours</td>
                                <td><a href="<?php echo $row_extras1['linkURL'];?>">More Info</a></td>
              	<td><a href="javascript:void(0)" id="delete-extra" onclick="delview(<?php echo $row_extras1['extraid'];?>,<?php echo $row_extras1['windowid'];?> )"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                <td><a data-toggle="modal" data-target="#<?php echo $popupid;?>" href="#" data-id="<?php echo $row_extras1['extraid'];?>" id="edit-view-manager"><i class="fa fa-edit" aria-hidden="true"></i></a></td>
												</tr>
											<?php	
											}
      
                          ?>
     
          
        </table>

                