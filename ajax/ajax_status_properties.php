<?php ob_start();
session_start();
include('../includes/functions.php');
if(!empty($_SESSION['agentid']))
{
	  if($_POST['city']!='select')
{
	  if($_POST['jobstatus']=='All')
			{
				  $getprop=$db->joinquery("SELECT `locationid`,`unitnum`,`street`,`suburb`,`city` FROM location WHERE city='".$_POST['city']."' AND agentid=".$_SESSION['agentid']." AND (locationstatusid!='3' AND locationstatusid!='5')");
			}
			else
			{
				  $getprop=$db->joinquery("SELECT `locationid`,`unitnum`,`street`,`suburb`,`city` FROM location WHERE city='".$_POST['city']."' AND locationstatusid=".$_POST['jobstatus']." AND agentid=".$_SESSION['agentid']."");
			}
	 
}
else
{
	   if($_POST['jobstatus']=='All')
			{
				   if(!empty($_POST['search_text'])){
								
								$getprop=$db->joinquery("SELECT `locationid`,`unitnum`,`street`,`suburb`,`city` FROM location WHERE agentid=".$_SESSION['agentid']." AND (`street` LIKE '%".$_POST['search_text']."%' OR `suburb` LIKE '%".$_POST['search_text']."%' OR `city` LIKE '%".$_POST['search_text']."%' OR `unitnum` LIKE '%".$_POST['search_text']."%') AND (locationstatusid!='3' AND locationstatusid!='5')");
							}
							else
							{
								 $getprop=$db->joinquery("SELECT `locationid`,`unitnum`,`street`,`suburb`,`city` FROM location WHERE agentid=".$_SESSION['agentid']." AND (locationstatusid!='3' AND locationstatusid!='5')");
							}
				 
						 
				
				  
			}
			else
			{
				
				   if(!empty($_POST['search_text']))
							{
								  $getprop=$db->joinquery("SELECT `locationid`,`unitnum`,`street`,`suburb`,`city` FROM location WHERE locationstatusid=".$_POST['jobstatus']." AND (`street` LIKE '%".$_POST['search_text']."%' OR `suburb` LIKE '%".$_POST['search_text']."%' OR `city` LIKE '%".$_POST['search_text']."%' OR `unitnum` LIKE '%".$_POST['search_text']."%') AND agentid=".$_SESSION['agentid']."");
							}
							else
							{
								  $getprop=$db->joinquery("SELECT `locationid`,`unitnum`,`street`,`suburb`,`city` FROM location WHERE locationstatusid=".$_POST['jobstatus']." AND agentid=".$_SESSION['agentid']."");
							}
				  
			}
	 
}
	
				 echo ' <select class="form-control" id="list-prop" name="list-prop" onchange="getproprty(this.value)">
				<option value="select">Select Property</option>';
				if(mysql_num_rows($getprop)>0)
				{
						while($row_prop=mysqli_fetch_array($getprop))
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
}
else
{
	 header('Location:logout.php');
}
