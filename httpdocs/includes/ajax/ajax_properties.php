<?php
include('includes/functions.php');
if($_POST['status'] != 'All')
{
	 $getprop=$db->joinquery("SELECT `locationid`,`unitnum`,`street`,`suburb`,`city` FROM location WHERE city='".$_POST['city']."' AND agentid='".$_POST['agentid']."' AND locationstatusid='".$_POST['status']."'");
}
else
{
	 $getprop=$db->joinquery("SELECT `locationid`,`unitnum`,`street`,`suburb`,`city` FROM location WHERE city='".$_POST['city']."' AND agentid='".$_POST['agentid']."'");
}

echo '<select name="properties" id="properties" style="width:200px;" onchange="getreport(this.value)">
<option value="select">Please Select Properties</option>';
if(mysql_num_rows($getprop)>0)
{
	 while($row_prop=mysql_fetch_array($getprop))
		{
			 $loc=$row_prop['unitnum'].",".$row_prop['street'];
			 if(!empty($row_prop['suburb']))
				{
					 $loc.=",".$row_prop['suburb'];
				}
				$loc.=",".$row_prop['city'];
			 echo '<option value='.$row_prop['locationid'].'>'.$loc.'</option>';
		}
	 
}
echo '</select>';





