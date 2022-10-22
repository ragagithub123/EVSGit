<?php ob_start();
session_start();
include('../includes/functions.php');
$getprofile=$db->joinquery("SELECT `evsProfileTop`,`evsProfileRight`,`evsProfileLeft`,`evsProfileBottom`,`evsGlassX`,`evsGlassY`,`evsProfileX`,`evsProfileY`,`retroProfileTop`,`retroProfileBottom`,`retroProfileLeft`,`retroProfileRight`,`retroGlassX`,`retroGlassY`,`retroProfileX`,`retroProfileY` FROM `paneloption_style` WHERE styleid='".$_POST['styleid']."'");
$rowprofile=mysqli_fetch_array($getprofile);
if($_POST['option']=="left"){
	$evsprofile=$rowprofile['evsProfileLeft'];
	$retroprofile=$rowprofile['retroProfileLeft'];
	$evsprofilevalue=$rowprofile['evsProfileY'];
	$evsglassvalue=$rowprofile['evsGlassY'];
	$retroprofilevalue=$rowprofile['retroProfileY'];
	$retorglassvalue=$rowprofile['retroGlassY']
	?>
 <h4>Left Side <span class="close-popup">&times;</span></h4>
 
 <?php }else if($_POST['option']== "right"){
			$evsprofile=$rowprofile['evsProfileRight'];
	  $retroprofile=$rowprofile['retroProfileRight'];
			$evsprofilevalue=$rowprofile['evsProfileY'];
				$evsglassvalue=$rowprofile['evsGlassY'];
				$retroprofilevalue=$rowprofile['retroProfileY'];
				$retorglassvalue=$rowprofile['retroGlassY']
			?>
  <h4>Right Side <span class="close-popup">&times;</span></h4>
  <?php }else if($_POST['option']== "top"){
			$evsprofile=$rowprofile['evsProfileTop'];
	  $retroprofile=$rowprofile['retroProfileTop'];
			$evsprofilevalue=$rowprofile['evsProfileX'];
	$evsglassvalue=$rowprofile['evsGlassX'];
	$retroprofilevalue=$rowprofile['retroProfileX'];
	$retorglassvalue=$rowprofile['retroGlassX']
			?>
		  <h4>Top Side <span class="close-popup">&times;</span></h4>
  <?php }else if($_POST['option']== "bottom"){
			$evsprofile=$rowprofile['evsProfileBottom'];
	  $retroprofile=$rowprofile['retroProfileBottom'];
			$evsprofilevalue=$rowprofile['evsProfileX'];
	$evsglassvalue=$rowprofile['evsGlassX'];
	$retroprofilevalue=$rowprofile['retroProfileX'];
	$retorglassvalue=$rowprofile['retroGlassX']
			?>
   <h4>Bottom Side <span class="close-popup">&times;</span></h4>
<?php } ?>
 
                                         
                                         <div class="prd-popup-inner">
                                          <h5>EVS</h5>
                                             <div class="prd-inner-content">
                                              <div>
                                              <?php if(file_exists($gProfilePhotoDir.$evsprofile.".png"))?>
                                                  <img src="<?php echo $gProfilePhotoURL.$evsprofile.".png";?>">
                                                     <h5 class="redclr"><?php echo $evsprofile;?></h5>
                                                 </div>
                                                 <div>
                                                  <ul>
                                                      <li><span>Glass</span> <?php echo $evsglassvalue;?></li>
                                                         <li><span>Profile</span> <?php echo $evsprofilevalue;?></li>
                                                     </ul>
                                                 </div>
                                             </div>
                                         </div><!-- ./prd-popup-inner -->
                                         
                                         <div class="prd-popup-inner">
                                          <h5>RETRO</h5>
                                             <div class="prd-inner-content">
                                              <div>
                                                  <?php if(file_exists($gProfilePhotoDir.$retroprofile.".png"))?>
                                                  <img src="<?php echo $gProfilePhotoURL.$retroprofile.".png";?>">
                                                     <h5 class="redclr"><?php echo $retroprofile;?></h5>
                                                 </div>
                                                 <div>
                                                  <ul>
                                                      <li><span>Glass</span> <?php echo $retroprofilevalue;?></li>
                                                         <li><span>Profile</span> <?php echo $retorglassvalue;?></li>
                                                     </ul>
                                                 </div>
                                             </div>
                                         </div><!-- ./prd-popup-inner -->
