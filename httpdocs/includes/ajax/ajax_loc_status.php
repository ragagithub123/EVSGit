<?php
include('includes/functions.php');
$getstatus=$db->joinquery("SELECT * FROM location_status");
echo '<select name="location_status" id="location_status" style="width:200px;" onchange="getproperties('.$_POST['agent_id'].')">
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






