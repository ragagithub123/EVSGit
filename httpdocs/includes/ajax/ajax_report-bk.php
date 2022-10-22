<?php
include('includes/functions.php');
$getlocaid=$db->joinquery("SELECT locationid FROM location WHERE city='".$_POST['location_id']."'");
while($row=mysql_fetch_array($getlocaid))
{
	 $locid[]=$row['locationid'];
}
$loca_id=join(',',$locid);
//echo "SELECT room.roomid,room.name,window.windowid,window.roomid,window.windowtypeid,window_type.windowtypeid,window_type.name,window_window_option.windowoptionid,window_window_option.windowid,window_option.windowoptionid,window_option.name,window_option.value FROM room,window,window_type,window_window_option,window_option WHERE room.roomid=window.roomid AND window.windowtypeid=window_type.windowtypeid AND window.windowid=window_window_option.windowid AND window_window_option.windowoptionid=window_option.windowoptionid GROUP BY room.roomid";die();
//SELECT room.roomid,room.name as room_name,window_type.name as type_name,panel.width,panel.height,panel.windowid,window_option.value FROM room,window_type,window,panel,window_option,window_window_option WHERE room.roomid=window.roomid AND window.windowtypeid=window_type.windowtypeid AND panel.windowid=window.windowid AND window_window_option.windowoptionid=window_option.windowoptionid AND window_window_option.windowid=window.windowid GROUP BY room.roomid
//SELECT room.roomid,room.name as room_name,window_type.name ,panel.width,panel.height,panel.windowid,window_option.value FROM room,window_type,window,panel,window_option,window_window_option WHERE room.roomid=window.roomid AND window.windowtypeid=window_type.windowtypeid AND panel.windowid=window.windowid AND window_window_option.windowoptionid=window_option.windowoptionid AND window_window_option.windowid=window.windowid GROUP BY room.roomid
$excel_data = $db->joinquery("SELECT room.roomid,room.name as room_name,window_type.name ,panel.width,panel.height, panel.glasstypeid,paneloption_glasstype.name as type,paneloption_glasstype.typevalue,panel.windowid,window_option.value FROM room,window_type,window,panel,window_option,window_window_option,paneloption_glasstype WHERE room.roomid=window.roomid AND window.windowtypeid=window_type.windowtypeid AND panel.windowid=window.windowid AND window_window_option.windowoptionid=window_option.windowoptionid AND panel.glasstypeid=paneloption_glasstype.glasstypeid AND window_window_option.windowid=window.windowid AND room.locationid IN($loca_id)  GROUP BY room.roomid");
echo' <div class="container">
<table class="table" id="example" border="1 px solid">
    <thead>
     <tr>
<th>Room Name</th>
<th>Name</th>
<th>Measure Size</th>
<th>Glass/plexiglas sizes(+26)</th>
<th>m2((Glass/plexiglas sizes) * 0.000001)</th>
<th>Glass Type</th>
<th>Bead Sizes(+72)</th>
<th>Trim(glasss type value)</th>
<th>Y sides</th>
<th>Lm((Bead Sizes)* 2* 0.001)</th>
</tr>
    </thead>
    <tbody>';
    
if(mysql_num_rows($excel_data)>0)
	{
		while($excel_value = mysql_fetch_array( $excel_data))
		{
			
           echo' <tr>
            <td>'.$excel_value['room_name'].'</td>
            <td>'.$excel_value['name'].'</td>
            <td>'.$excel_value['width'].' &#10006 '.$excel_value['height'].' </td>
           
            </tr>';
           
           
		}
		
		
	}
      echo '</tr>
    </tbody>
  </table> 
  </div>
';



