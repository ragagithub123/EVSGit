<?php
ob_start();
session_start();
include('includes/functions.php');
$alldates = array();
$location = $db->joinquery("SELECT location.locationid,location.`unitnum`,location.`street`,location.`suburb`,location.`city` FROM location WHERE locationid='".$_REQUEST['locationid']."'");
$row_details=mysqli_fetch_array($location);
$loc=$row_details['unitnum'].",".$row_details['street'];
if(!empty($row_details['suburb']))
{
$loc.=",".$row_details['suburb'];
}

$locationid = $_REQUEST['locationid'];

$getWeekend = $db->joinquery("SELECT DISTINCT(week_date) FROM staffTrack WHERE locationid='".$_REQUEST['locationid']."'");

while($row = mysqli_fetch_array($getWeekend)){

    $date_end = $row['week_date'];

    $date_start = date('Y-m-d', strtotime('-6 day', strtotime($date_end)));

    $dateranges = displayDates($date_start,$date_end);

    

    for($i=0;$i<count($dateranges);$i++){

        $timestamp = strtotime($dateranges[$i]);

        $getday = date('D', $timestamp);

        $Day = strtolower($getday);

    
  $type = $_REQUEST['type'];

  if($type == 'make')

  $color = '#09aced';

  else if($type == 'prep')

  $color = 'darkolivegreen';

  else if($type == 'install')

  $color = 'hotpink';

  else if($type == 'extra')

  $color = 'orange';
  
  
   if($type == 'all'){

    $getmake = $db->joinquery("SELECT agentStaffs.staff_name,staffTrack.stafftrackid,staffTrack.make, staffTrack.prep,staffTrack.install,staffTrack.extra FROM `agentStaffs`,`staffTrack` WHERE staffTrack.staff_id=agentStaffs.staff_id AND `week_date`='".$date_end."' AND staffTrack.locationid='".$_REQUEST['locationid']."' AND staffTrack.`agentid`='".$_SESSION['agentid']."' AND staffTrack.day='".$Day."' GROUP BY staffTrack.stafftrackid");
    if(mysqli_num_rows($getmake)>0){

        while($rowmake = mysqli_fetch_array($getmake)){
    
            if($rowmake['make']!='0.00')
            
            $alldates[] = array('title'=>$rowmake['staff_name'].":"." ".$rowmake['make']." hr",'start'=>$dateranges[$i],'end'=>$dateranges[$i],'id'=>$rowmake['stafftrackid'],'color'=>'#09aced');

            if($rowmake['prep']!='0.00')
    
            $alldates[] = array('title'=>$rowmake['staff_name'].":"." ".$rowmake['prep']." hr",'start'=>$dateranges[$i],'end'=>$dateranges[$i],'id'=>$rowmake['stafftrackid'],'color'=>'darkolivegreen');

            if($rowmake['install']!='0.00')
    
            $alldates[] = array('title'=>$rowmake['staff_name'].":"." ".$rowmake['install']." hr",'start'=>$dateranges[$i],'end'=>$dateranges[$i],'id'=>$rowmake['stafftrackid'],'color'=>'hotpink');

            if($rowmake['extra']!='0.00')
    
            $alldates[] = array('title'=>$rowmake['staff_name'].":"." ".$rowmake['extra']." hr",'start'=>$dateranges[$i],'end'=>$dateranges[$i],'id'=>$rowmake['stafftrackid'],'color'=>'orange');
    
        }
    }

   


   }

  
else{

    $gettype = $db->joinquery("SELECT agentStaffs.staff_name,staffTrack.stafftrackid,staffTrack.$type FROM `agentStaffs`,`staffTrack` WHERE staffTrack.staff_id=agentStaffs.staff_id AND `week_date`='".$date_end."' AND staffTrack.locationid='".$_REQUEST['locationid']."' AND staffTrack.$type!='0.00' AND staffTrack.`agentid`='".$_SESSION['agentid']."' AND staffTrack.day='".$Day."' GROUP BY staffTrack.stafftrackid");

    if(mysqli_num_rows($gettype)>0){

        while($rowtype = mysqli_fetch_array($gettype)){
    
    
            $alldates[] = array('title'=>$rowtype['staff_name'].":"." ".$rowtype[$type]." hr",'start'=>$dateranges[$i],'end'=>$dateranges[$i],'id'=>$rowtype['stafftrackid'],'color'=>$color);
    
           
        }
    }
}
  



}//for







    }



$result =json_encode($alldates); 

include('templates/header.php');
include('views/time-report.htm');
include('templates/footer.php');
function displayDates($date1, $date2, $format = 'Y-m-d' ) {
    $dates = array();
    $current = strtotime($date1);
    $date2 = strtotime($date2);
    $stepVal = '+1 day';
    while( $current <= $date2 ) {
       $dates[] = date($format, $current);
       
       $current = strtotime($stepVal, $current);
    }
    return $dates;
 }


?>