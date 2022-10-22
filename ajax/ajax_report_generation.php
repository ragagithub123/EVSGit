<?php ob_start();
session_start();
include('../includes/functions.php');

$weekstartdate = $_POST['startdate'];

$weekendate = $_POST['enddate'];

$panelavg =array();	
	$Locationid =array();

  $get_pjt = $db->joinquery("SELECT projectid FROM weekely_worksheet WHERE `agentid`='".$_SESSION['agentid']."' AND projectid IS NOT NULL AND dates BETWEEN '".$weekstartdate."' AND '".$weekendate."'");

  if(mysqli_num_rows($get_pjt)>0)

$gtelocation =$db->joinquery("SELECT location.locationid,location.projectid,location.unitnum,location.street,location.city,location.suburb,location.locationSearch,weekely_worksheet.* FROM location,weekely_worksheet WHERE location.locationid=weekely_worksheet.locationid AND location.projectid = weekely_worksheet.projectid AND weekely_worksheet.`agentid`='".$_SESSION['agentid']."' AND weekely_worksheet.dates BETWEEN '".$weekstartdate."' AND '".$weekendate."' GROUP BY weekely_worksheet.locationid");

else

$gtelocation =$db->joinquery("SELECT location.locationid,location.projectid,location.unitnum,location.street,location.city,location.suburb,location.locationSearch,weekely_worksheet.* FROM location,weekely_worksheet WHERE location.locationid=weekely_worksheet.locationid AND weekely_worksheet.`agentid`='".$_SESSION['agentid']."' AND weekely_worksheet.dates BETWEEN '".$weekstartdate."' AND '".$weekendate."' GROUP BY weekely_worksheet.locationid");



if(mysqli_num_rows($gtelocation)>0)	{
	while($row_loc = mysqli_fetch_assoc($gtelocation)){
		
		if($row_loc['locationid']!=null){

      if(!empty($row_loc['projectid']) || ($row_loc['projectid']!=NULL)){

        $getproject = $db->joinquery("SELECT project_name,projectid,project_date FROM location_projects WHERE projectid='".$row_loc['projectid']."'");
  
        $rowPjt =  mysqli_fetch_array($getproject);
  
        $row_loc['projectid'] = $rowPjt['projectid'];
  
        $row_loc['project_date'] = date('d-m-Y',strtotime($rowPjt['project_date']));
        
        $row_loc['project_name'] =$rowPjt['project_name']." ".$row_loc['project_date'];
  
      }
		
		$Locationid[] = $row_loc['locationid'];
		
		$get_totalpanel = $db->joinquery("SELECT sum(window_type.numpanels) AS panelcount FROM room,window,window_type WHERE window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid AND window.selected_product!='HOLD' AND room.locationid='".$row_loc['locationid']."'");

  $row_quote = mysqli_fetch_array($get_totalpanel);
		

		if(empty($row_loc['locationSearch'])){

			$loc=$row_loc['unitnum'].",".$row_loc['street'];
		
			if(!empty($row_loc['suburb']))
						  
			$loc.=",".$row_loc['suburb'];

			$row_loc['locationSearch'] = $loc;

		}
		
		$row_loc['panelcount']= $row_quote['panelcount'];

  $glasscount[] = $row_loc['glass'];
		
		$kitcount[]  =  $row_loc['kit'];
		
		$sealscount[] = $row_loc['seals'];
		
		$installcount[] = $row_loc['install'];
		
		$prepcount[] =$row_loc['prep'];
		
		$assemblecount[] =$row_loc['assemble'];
		
		$jobcomplete[] = $row_loc['percentcomplete']; 
		

		$total_price = array();

		

		$panelcount[] = $row_quote['panelcount'];

		$get_window = $db->joinquery("SELECT window.windowid,window.selected_product,window.costsdg,window.costmaxe,window.costxcle,window.costevsx2,window.costevsx3 FROM room,window WHERE window.roomid=room.roomid AND window.selected_product!='HOLD' AND room.locationid=".$row_loc['locationid']." GROUP BY window.windowid ORDER BY room.name ASC");
		
		while($row_window = mysqli_fetch_array($get_window)){

			if($row_window['selected_product']=="SGU" || $row_window['selected_product']=="SDG"){

				$total_price[]= $row_window['costsdg'];

			}
			else if($row_window['selected_product']=="IGUX2" || $row_window['selected_product']=="MAXe"){

				$total_price[]= $row_window['costmaxe'];

			}

			else if($row_window['selected_product']=="IGUX3" || $row_window['selected_product']=="XCLe"){

				$total_price[]= $row_window['costxcle'];

			}

			else if($row_window['selected_product']=="EVSx2"){

				$total_price[]= $row_window['costevsx2'];


			}

			else if($row_window['selected_product']=="EVSx3"){

				$total_price[]= $row_window['costevsx3'];


			}


			
			
		}

		

		if(count($total_price)>0){

			$jobsum[] = array_sum($total_price);
			
			

			$row_loc['jobsum']  =  array_sum($total_price);
		}

		else{

			$row_loc['jobsum'] = 0;

		}

		  

				
				/* Formula :: jobGlassVal[i]=(((((100/jobPanels[i])*jobGlass[i])*percentGlass)*0.01)*jobSum[i])*/
				
				$totalcost = array_sum($total_price);
				
			$jobGlassVal = (((((100/$row_quote['panelcount'])*$row_loc['glass'])*0.25)*0.01)*$totalcost);
			
						
			$jobKitVal =  ((((((100/$row_quote['panelcount'])*$row_loc['kit'])*0.12)*0.01)*$totalcost));	
			
			
			$jobassembleVal = ((((((100/$row_quote['panelcount'])*$row_loc['assemble'])*0.13)*0.01)*$totalcost));
			
			
			$jobprepVal  =  ((((((100/$row_quote['panelcount'])*$row_loc['prep'])*0.25)*0.01)*$totalcost));
			
			
			$jobinstallVal =  ((((((100/$row_quote['panelcount'])*$row_loc['install'])*0.25)*0.01)*$totalcost));
			
			
			$jobsealsVal =  ((((((100/$row_quote['panelcount'])*$row_loc['seals'])*0.0)*0.01)*$totalcost));
			
			
				
			$panelavg[] = ($totalcost)/$row_quote['panelcount'];
				

		
		$jobglass[] = round($jobGlassVal,2);
		
		$jobkit[]  = round($jobKitVal,2);
		
		$jobassemble[]  = round($jobassembleVal,2);
			
		$jobprep[]  = round($jobprepVal,2);
		
		$jobinstall[] =round($jobinstallVal,2);
		
		$jobseals[] = round($jobsealsVal,2);
		 
		  $post[] =$row_loc;
				
	}

}

}
			
	


if(count($jobsum)>0)

$totalcost = array_sum($jobsum);

else

$totalcost =0;


$totalglass = array_sum($glasscount); 

$totalkitcount = array_sum($kitcount);

$totalprepcount = array_sum($prepcount);

$totalassemblecount = array_sum($assemblecount);

$totalinstallcount  = array_sum($installcount);

$totalsealscount  = array_sum($sealscount);

if(count($panelcount)>0)

$totalpanels = array_sum($panelcount);

else

$totalpanels =0;


$glasspercent = 25;

$kitpercent   = 12;

$assemblepercent = 13;

$installpercent  = 25;

$sealspercent   = 0;

$preppercent    =  25;


 
$datecurnt = date('Y-m-d');

/*$expdates = explode('-',$datecurnt);

$weekdates[]=getWeeks($expdates[1],$expdates[0]);*/




if(count($jobglass)>0)

$weekglass = array_sum($jobglass);
else
$weekglass =0;
if(count($jobkit)>0)

$weekkit =array_sum($jobkit);
else
$weekkit =0;
if(count($jobassemble)>0)

$weekassemble =array_sum($jobassemble);
else
$weekassemble=0;
if(count($jobprep)>0)

$weekprep =array_sum($jobprep);
else
$weekprep=0;
if(count($jobinstall)>0)

$weekinstall =array_sum($jobinstall);
else
$weekinstall=0;
if(count($jobseals)>0)

$weekseals =array_sum($jobseals);

else
$weekseals=0;


$totalproduction = $weekglass+$weekassemble+$weekinstall+$weekkit+$weekprep+$weekseals;

if(count($Locationid)>0){

$totalpanelavg = round(array_sum($panelavg)/count($Locationid),2);

$total_job_complete = round(array_sum($jobcomplete)/count($Locationid),2);



}


else{
	 $totalpanelavg =0;
  $total_job_complete =0;

}


?>
<div class="col-lg-12">
          <div class="report-table">
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th width="250">Customer</th>
                   
                    <th width="120">Total Value</th>
                    <th>% Complete</th>
                    <th>Total Panels</th>
                    <th>Glass Received</th>
                    <th>Kits Made</th>
                    <th>Panels Assembled</th>
                    <th>Panels Prepped</th>
                    <th>Panels Installed</th>
                    <th>Seals Installed</th>
                  </tr>
                </thead>
                <tbody>
                 <?php
                  $i=0;
                  foreach($post as $valpost){  $i++;?>
                 
                  <tr>
                 
                   <td><?php echo $valpost['locationSearch'];?>,<b style="color: #09aced;"><?php echo $valpost['project_name'];?></b></td>
                   <td>$<?php echo $valpost['jobsum'];?></td>
                   <td><?php echo ($valpost['percentcomplete']*100);?>%</td>
                   <td><?php echo $valpost['panelcount'];?></td>
                   <td><?php echo $valpost['glass'];?></td>
                   <td><?php echo $valpost['kit'];?></td>
                   <td><?php echo $valpost['assemble'];?></td>
                   <td><?php echo $valpost['prep'];?></td>
                    <td><?php echo $valpost['install'];?></td>
                   <td><?php echo $valpost['seals'];?></td>
                  </tr>
                  
                  <?php 
                  }
                  ?>
                
                </tbody>
                <tfoot>
                  <tr>
                    <td>Total $</td>
                    <td>$ <?php echo $totalcost;?></td>
                    <td><?php echo ($total_job_complete*100);?>%</td>
                    <td><?php echo $totalpanels;?></td>
                    <td><?php echo $totalglass;?></td>
                    <td><?php echo $totalkitcount;?></td>
                    <td><?php echo $totalassemblecount;?></td>
                    <td><?php echo $totalprepcount;?></td>
                    <td><?php echo $totalinstallcount;?></td>
                    <td><?php echo $totalsealscount;?></td>
                   
                  </tr>
                  <tr>
                    <td colspan="4">Ave $ </td>
                    <td class="bg-clr"><?php echo $glasspercent;?>%</td>
                    <td class="bg-clr"><?php echo $kitpercent;?>%</td>
                    <td class="bg-clr"><?php echo $assemblepercent;?>%</td>
                    <td class="bg-clr"><?php echo $preppercent;?>%</td>
                    <td class="bg-clr"><?php echo $installpercent;?>%</td>
                    <td class="bg-clr"><?php echo $sealspercent;?>%</td>
                  </tr>
                  <tr>
                    <td colspan="2">Production Value</td>
                    <td>$<?php echo $totalproduction;?></td>
                    <td>$<?php echo $totalpanelavg;?></td>
                    <td>$<?php echo $weekglass;?></td>
                    <td>$<?php echo $weekkit;?></td>
                    <td>$<?php echo $weekassemble;?></td>
                    <td>$<?php echo $weekprep;?></td>
                    <td>$<?php echo $weekinstall;?></td>
                    <td>$<?php echo $weekseals;?></td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>

