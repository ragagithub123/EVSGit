<?php
include('includes/functions.php');
//SELECT room.name as room_name,window.windowid,window_type.name ,panel.width,panel.height, panel.glasstypeid,paneloption_glasstype.name as type,paneloption_glasstype.typevalue,panel.windowid,window_option.value FROM room,window_type,window,panel,window_option,window_window_option,paneloption_glasstype WHERE room.roomid=window.roomid AND window.windowtypeid=window_type.windowtypeid AND panel.windowid=window.windowid AND window_window_option.windowoptionid=window_option.windowoptionid AND panel.glasstypeid=paneloption_glasstype.glasstypeid AND window_window_option.windowid=window.windowid AND room.locationid ='11' GROUP BY panel.panelid
$excel_data = $db->joinquery("SELECT room.roomid,room.name as room_name,window_type.name ,panel.width,panel.height, panel.panelnum,panel.glasstypeid,paneloption_glasstype.name as type,paneloption_glasstype.typevalue,panel.windowid,window_option.value FROM room,window_type,window,panel,window_option,window_window_option,paneloption_glasstype WHERE room.roomid=window.roomid AND window.windowtypeid=window_type.windowtypeid AND panel.windowid=window.windowid AND window_window_option.windowoptionid=window_option.windowoptionid AND panel.glasstypeid=paneloption_glasstype.glasstypeid AND window_window_option.windowid=window.windowid AND room.locationid ='".$_POST['location_id']."' GROUP BY panel.panelid");
?>
<div class="container" style="margin-top:10px;">
  <table class="table" id="example" border="1 px solid">
    <thead>
     <tr>
     <th>Sl NO</th>
<th>Room Name</th>
<th>Name</th>
<th>Panelnum</th>
<th>Measure Size</th>
<th>Glass/plexiglas sizes(+26)</th>
<th>m2((Glass/plexiglas sizes) * 0.000001)</th>
<th>Glass Type</th>
<th>Bead Sizes(+72)</th>
<th>Trim(glasss type value)</th>
<th>Y sides</th>
<th>Lm((Bead Sizes)* 2* 0.001)</th>
<th>Total(m2+Lm)Meters</th>
</tr>
    </thead>
    <tbody>
     <?php
if(mysql_num_rows($excel_data)>0)
	{
		 $i=0;
		while($excel_value = mysql_fetch_array( $excel_data))
		{
			 $i++;
			?>
            <tr>
             <td><?php echo $i;?></td>
            <td><?php echo $excel_value['room_name'];?></td>
            <td><?php echo $excel_value['name'];?></td>
              <td><?php echo $excel_value['panelnum'];?></td>
            <td ><?php echo $excel_value['width'];?> &#10006 <?php echo $excel_value['height'];?> </td>
            <td><?php if($excel_value['width'] >0){echo ($excel_value['width'] + 26);?> &#10006 <?php echo ($excel_value['height'] + 26);}?></td>
             <td><?php if($excel_value['width'] >0){ echo round(((($excel_value['width'] + 26)*($excel_value['height'] + 26))*0.000001),2);}?></td>
             <td><?php echo $excel_value['type'];?></td>
              <td><?php if($excel_value['width'] >0){echo ($excel_value['width'] + 72);?> &#10006 <?php echo ($excel_value['height'] + 72);}?></td>
              <td><?php echo $excel_value['typevalue'];?></td>
              <td><?php if($excel_value['typevalue'] > 0){echo ($excel_value['height'] + 72)-($excel_value['typevalue']);}?></td>
              <td><?php if($excel_value['width'] > 0){echo round((((($excel_value['width'] + 72)+($excel_value['height'] + 72))*2)*0.001),2);}?></td>
              <td><?php echo (round(((($excel_value['width'] + 26)*($excel_value['height'] + 26))*0.000001),2))+(round((((($excel_value['width'] + 72)+($excel_value['height'] + 72))*2)*0.001),2));?></td>
            </tr>
            <?php
		}
		
		
	}
?>
      </tr>
    </tbody>
  </table>
  </div>

