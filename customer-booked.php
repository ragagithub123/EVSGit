 <?php ob_start();
	session_start();
			include('includes/functions.php');
			if(!empty($_SESSION['agentid'])){
				
				$alldates = array();

				$getprop = $db->joinquery("SELECT locationid,locationSearch FROM location WHERE agentid='" . $_SESSION['agentid'] . "'");

					if (mysqli_num_rows($getprop) > 0)
					{

						while ($row_prop = mysqli_fetch_assoc($getprop))
						{

							$postLocation[] = $row_prop;

						}

					}

					$getStaffs = $db->joinquery("SELECT staff_id,staff_name FROM agentStaffs WHERE agentid='" . $_SESSION['agentid'] . "'");

					if (mysqli_num_rows($getStaffs) > 0)
					{

						while ($row_satff= mysqli_fetch_assoc($getStaffs))
						{

							$AllStaff[] = $row_satff;

						}

					}


				
								
				//$dates = $db->joinquery("SELECT date(booking_date) AS booking_date FROM location WHERE agentid=".$_SESSION['agentid']." AND date(booking_date)!='000-00-00'");
				//SELECT location.`unitnum`,location.`street`,location.`suburb`,location.`city`,date(location.booking_date) AS booking_date,date(location.booking_end_date) AS booking_end_date,time(location.booking_date) AS booking_time ,customer.firstname,customer.lastname,customer.phone FROM `location`,`customer`,`location_status_table` WHERE location.locationstatusid=location_status_table.locationstatusid AND location_status_table.status='QB' AND location.customerid=customer.customerid AND date(location.booking_date)!='000-00-00' AND location.`agentid`='7'
				
				$dates = $db->joinquery("SELECT location.locationid,location.alarm_Type,location.`unitnum`,location.`street`,location.`suburb`,location.`city`,date(location.booking_date) AS booking_date,date(location.booking_end_date) AS booking_end_date,time(location.booking_date) AS booking_time  FROM `location`, `location_status_table` WHERE location.jobstatusid = location_status_table.locationstatusid AND location_status_table.status!='QA' AND date(location.booking_date)!='000-00-00' AND location.`agentid`='".$_SESSION['agentid']."'");
				if(mysqli_num_rows($dates)>0){
					
					 while($row_dates =mysqli_fetch_array($dates)){
							
								$loc=$row_dates['unitnum'].",".$row_dates['street'];
									if(!empty($row_dates['suburb']))
									{
											$loc.=",".$row_dates['suburb'];
									}
							
							if($row_dates['alarm_Type'] == 'Measure and Quote')
							
							$alldates[] = array('title'=>date('H:i',strtotime($row_dates['booking_time']))." ".$loc,'start'=>$row_dates['booking_date'],'end'=>$row_dates['booking_date'],'id'=>$row_dates['locationid'],'color'=>'#03C');
							
							
								
							 if($row_dates['alarm_Type'] == 'Installation'){
									
									if($row_dates['booking_end_date']!='0000-00-00'){
										
										 	$stop_date = date('Y-m-d', strtotime($row_dates['booking_end_date'] . ' +1 day'));
									}
									else{
										
										 $stop_date = $row_dates['booking_date'];
										
									}
								
								
								$alldates[] = array('title'=>date('H:i',strtotime($row_dates['booking_time']))." ".$loc,'start'=>$row_dates['booking_date'],'end'=>$stop_date,'id'=>$row_dates['locationid'],'color'=>'#060');
								
								}
							
							 if($row_dates['alarm_Type'] == 'Inspect')
								
								
								$alldates[] = array('title'=>date('H:i',strtotime($row_dates['booking_time']))." ".$loc,'start'=>$row_dates['booking_date'],'end'=>$row_dates['booking_date'],'id'=>$row_dates['locationid'],'color'=>'#F00');
								
								
							else if($row_dates['alarm_Type'] == '')
								
								$alldates[] = array('title'=>date('H:i',strtotime($row_dates['booking_time']))." ".$loc,'start'=>$row_dates['booking_date'],'end'=>$row_dates['booking_date'],'id'=>$row_dates['locationid'],'color'=>'#03C');
							
				
								
							
						}
					
				}

		$getLeaves = $db->joinquery("SELECT date(StaffLeaves.`startdate`) AS startdateLeave,date(StaffLeaves.`enddate`) AS enddateLeave,StaffLeaves.leavetype,agentStaffs.staff_name FROM StaffLeaves,agentStaffs WHERE StaffLeaves.staffid= agentStaffs.staff_id AND StaffLeaves.agentId=agentStaffs.agentid AND StaffLeaves.agentId='".$_SESSION['agentid']."'");		
		if(mysqli_num_rows($getLeaves)>0){

		while($rowLeave = mysqli_fetch_array($getLeaves)){

			if($rowLeave['leavetype'] == 'HolidayLeave'){

				$alldates[] = array('title'=>$rowLeave['staff_name']."- ".$rowLeave['leavetype'],'start'=>$rowLeave['startdateLeave'],'end'=>$rowLeave['enddateLeave'],'id'=>'','color'=>'gold');
			}

			else if($rowLeave['leavetype'] == 'SickLeave'){

				$alldates[] = array('title'=>$rowLeave['staff_name']."- ".$rowLeave['leavetype'],'start'=>$rowLeave['startdateLeave'],'end'=>$rowLeave['enddateLeave'],'id'=>'','color'=>'orangered');
			}

			else if($rowLeave['leavetype'] == 'WorkTravel'){

				$alldates[] = array('title'=>$rowLeave['staff_name']."- ".$rowLeave['leavetype'],'start'=>$rowLeave['startdateLeave'],'end'=>$rowLeave['enddateLeave'],'id'=>'','color'=>'hotpink');
			}

			


		}	

			


		}	
	
				
				$result =json_encode($alldates); 
				
				include('templates/header.php');
                include('views/customer-booked.htm');
				include('templates/footer.php');
    
				
				}
else
{
	 header('Location:index.php');
}

function daterange($startdate,$enddate){ 
					
				$start = new DateTime($startdate);
			
				$stop_date = date('Y-m-d', strtotime($enddate . ' +1 day'));
				
				$end = new DateTime( $stop_date );
				
				$interval = new DateInterval('P1D');//one day interval
				
				$date_period = new DatePeriod($start, $interval ,$end);
				
				$dates_arr = array();
		
				foreach($date_period as $date){
					
					
				array_push($dates_arr,$date->format("Y-m-d"));
							
				}
   
			 return $dates_arr;
					
	}
?>