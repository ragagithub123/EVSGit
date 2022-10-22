<?php

ob_start();
session_start();
include('../includes/functions.php');
if($_POST['statusCode']==0){

  $getwindows = $db->joinquery("SELECT window.windowid,window_type.name FROM window,window_type WHERE window.windowtypeid=window_type.windowtypeid AND window.roomid=".$_POST['roomid']."");
  ?>
   <select name="windowname" id="windowname" class="form-control">
                 
                 <?php
                 while($row=mysqli_fetch_assoc($getwindows)){?>
     
                 <option value="<?php echo $row['windowid'];?>"><?php echo $row['name'];?></option>
         <?php
                 }
                 
                 ?>
         </select>
 <?php        
 
}
if($_POST['statusCode']==1){
        if($_POST['type'] == 'before')
  $before_photos = $db->joinquery("UPDATE window_photo SET windowid='".$_POST['windowid']."' WHERE windowid='" . $_POST['oldwindowid'] . "'");

  else if($_POST['type']=='after')
  $after_photos = $db->joinquery("UPDATE window_after_photo SET windowid='".$_POST['windowid']."' WHERE windowid='" . $_POST['oldwindowid'] . "'");
  echo 'success';
}
?>
