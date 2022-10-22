<?php
include('includes/functions.php');
$getlocation=$db->joinquery("SELECT DISTINCT(city) FROM location WHERE agentid='".$_POST['agent_id']."'");
echo '<select name="location" id="location" style="width:27%;" onchange="getproperties('.$_POST['agent_id'].')">
<option value="select">Please Select Location</option>';
if(mysql_num_rows($getlocation)>0)
{
	 while($row_loc=mysql_fetch_array($getlocation))
		{
			 echo '<option value='.$row_loc['city'].'>'.$row_loc['city'].'</option>';
		}
	 
}
echo '</select>';





