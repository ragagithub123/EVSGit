<?php
include('functions.php');
$getlocation=$db->joinquery("SELECT DISTINCT(city) FROM location WHERE agentid='".$_POST['agent_id']."'");
$getstatus=$db->joinquery("SELECT * FROM location_status");
echo '<select name="location" id="location" style="width:200px;">
<option value="select">Please Select Location</option>';
if(mysql_num_rows($getlocation)>0)
{
	 while($row_loc=mysql_fetch_array($getlocation))
		{
			 echo '<option value='.$row_loc['city'].'>'.$row_loc['city'].'</option>';
		}
	 
}
echo '</select>';
echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select name="location_status" id="location_status" style="width:200px;" onchange="getproperties('.$_POST['agent_id'].')">
<option value="select">Please Select Location Status</option>';
if(mysql_num_rows($getstatus)>0)
{
	 echo '<option value="All">All</option>';
	 while($row_loc_status=mysql_fetch_array($getstatus))
		{
			 echo '<option value='.$row_loc_status['locationstatusid'].'>'.$row_loc_status['status'].'</option>';
		}
	 
}
echo '</select>';






