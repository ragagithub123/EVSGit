<?php ob_start();
session_start();
include('../includes/functions.php');
$get_selected_cnt=$db->joinquery("SELECT location.locationid,location.`quotesdg`,location.`quotemaxe`,location.`quotexcle`,location.`quoteevsx2`,location.`quoteevsx3` FROM location WHERE locationid='".$_POST['locationid']."'");

$array_location =mysqli_fetch_assoc($get_selected_cnt);

	?>
	

               <td></td>
               <td></td>
               <td></td>
               <td>
               <div class="wid65"></div>
                               
                               <div class="wid65"><input type="checkbox" id="quotesdg" <?php if($array_location['quotesdg'] == 1){?> checked="checked" <?php } ?> onchange="quotestatus(<?php echo $array_location['locationid'];?>,'quotesdg' )" 
                                
                                 ></div>
                               <div class="wid65"><input type="checkbox" id="quotemaxe" <?php if($array_location['quotemaxe'] == 1){?> checked="checked" <?php }  ?> onchange="quotestatus(<?php echo $array_location['locationid'];?>,'quotemaxe' )"></div>
                               <div class="wid65"><input type="checkbox" id="quotexcle" <?php if($array_location['quotexcle'] == 1){?> checked="checked" <?php }  ?> onchange="quotestatus(<?php echo $array_location['locationid'];?>,'quotexcle' )"></div>
                               <div class="wid65"><input type="checkbox" id="quoteevsx2" <?php if($array_location['quoteevsx2'] == 1){?> checked="checked" <?php }  ?> onchange="quotestatus(<?php echo $array_location['locationid'];?>,'quoteevsx2' )"></div>
                                <div class="wid65"><input type="checkbox" id="quoteevsx3"  <?php if($array_location['quoteevsx3'] == 1){?> checked="checked" <?php }  ?> onchange="quotestatus(<?php echo $array_location['locationid'];?>,'quoteevsx3' )"></div>
                  
                     </td>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td></td>
                                
