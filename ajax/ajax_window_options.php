<?php ob_start();
session_start();
include('../includes/functions.php');
$getvalue=$db->joinquery("SELECT value FROM window_option WHERE windowoptionid='".$_POST['optionid']."'");
$windowOption=mysqli_fetch_array($getvalue);

	?>
	 <select name="option_quantity" class="form-control">
         <?php 
								
         for($j=1;$j<21;$j++){
										$price=$windowOption['value']*$j;
         
         echo '<option>&#10006; &nbsp;'.$j.' &nbsp; $ &nbsp;'.$price.'</option>';
          }?>
        </select>