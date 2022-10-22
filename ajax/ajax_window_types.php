<?php ob_start();
session_start();
include('../includes/functions.php');
$exp =explode("@",$_POST['windo']);
$framecategory =$db->joinquery("SELECT DISTINCT(`materialCategory`),`material_tag` FROM `famecategory`");
$images = $db->joinquery("SELECT windowtypeid,name FROM  window_type WHERE numpanels=".$exp[1]."");
$curnt_frame = $db->joinquery("SELECT windowtypeid,trim(materialCategory) AS materialCategory FROM window WHERE windowid=".$exp[0]."");
$rowfrmae = mysqli_fetch_array($curnt_frame);
$string = str_replace(' ', '-', $rowfrmae['materialCategory']);
$existcat =preg_replace('/[^A-Za-z0-9\-]/', '', $string);
$getMtaerisl = $db->joinquery("SELECT materialCategory FROM  famecategory WHERE material_tag='".$existcat."'");
$row_Mate = mysqli_fetch_array($getMtaerisl);
?>

   <div class="dropdown">
             <select name="framcategory" id="framcategory" class="btn btn-default dropdown-toggle">
             
             <option value="<?php echo $existcat;?>"><?php echo $row_Mate['materialCategory'];?></option>
             
             <?php
             while($rowframes =mysqli_fetch_array($framecategory)){
														
														if($rowframes['materialCategory']!=$row_Mate['materialCategory']){
														
														echo '<option value="'.$rowframes['material_tag'].'" >'.$rowframes['materialCategory'].'</option>';
														}
														
             }
             ?>
             </select>
               
               
              </div>

              <h3><?php echo $exp[1];?> x Panels</h3>

              <div class="retro-info">
                <ul>
                <?php
																while($rowimages = mysqli_fetch_array($images)){
																?>
                  <li onclick="selectimage(<?php echo $rowimages['windowtypeid'];?>)"><img src="<?php echo $gwindowURL.$rowimages['windowtypeid'];?>.png"><p><?php echo $rowimages['name'];?></p></li>
                  
                  <?php } ?>
                </ul>
              </div>
              <input type="hidden" id="typeid" value="<?php echo $rowfrmae['windowtypeid'];?>"/>
              <button class="btn btn-primary update-retro-btn" onclick="Updatewindow(<?php echo $exp[0];?>)">Update</button>



