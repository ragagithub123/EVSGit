<?php ob_start();
session_start();
include('../includes/functions.php');

$getprop=$db->joinquery("SELECT panel.`width`,panel.`height`,panel.`center`,panel.materialCategory,panel.`measurement`,panel.styleid,panel.`safetyid`,panel.`glasstypeid`,panel.`conditionid`,panel.`astragalsid`,paneloption_style.name AS stylename,paneloption_style.styledgvalue,paneloption_style.styleevsvalue,paneloption_style.IGUassemble,paneloption_style.EVSassemble,paneloption_safety.name AS safetyname,paneloption_safety.safetyvalue,paneloption_glasstype.name AS glassname,paneloption_glasstype.typevalue,paneloption_condition.name AS conditionname,paneloption_condition.conditionvalue,paneloption_astragal.name AS astragalname,paneloption_astragal.astragalvalue FROM panel,paneloption_style,paneloption_safety,paneloption_condition,paneloption_astragal,paneloption_glasstype WHERE panel.glasstypeid=paneloption_glasstype.glasstypeid AND panel.safetyid=paneloption_safety.safetyid AND panel.styleid=paneloption_style.styleid AND panel.conditionid=paneloption_condition.conditionid AND panel.astragalsid=paneloption_astragal.astragalsid AND panel.`panelid`='".$_POST['panelid']."'");
$row_prop=mysqli_fetch_array($getprop);
$getsafty=$db->joinquery("SELECT * FROM paneloption_safety");
$getglass=$db->joinquery("SELECT * FROM paneloption_glasstype");
$getcondition=$db->joinquery("SELECT * FROM paneloption_condition");
$getastragal=$db->joinquery("SELECT * FROM paneloption_astragal");
$framecategory =$db->joinquery("SELECT DISTINCT(`materialCategory`),`material_tag` FROM `famecategory`");
$getMtaerisl = $db->joinquery("SELECT materialCategory FROM  famecategory WHERE material_tag='".$row_prop['materialCategory']."'");
$row_Mate = mysqli_fetch_array($getMtaerisl);

#	-----------------------------------------------------------Size code Start--------------------------------------------------
	# calc dimensions
  if($row_prop['width']==0){

    $m2 = 0;
    $lm=0;

  }else{

    if($row_prop['center'] > $row_prop['height']) {
      $m2 = (($row_prop['width']+24) * ($row_prop['center']+24)) * 0.000001;
      $lm = (($row_prop['width']+72) + ($row_prop['center']+72)) * 0.002;
    }
    else {
      $m2 = (($row_prop['width']+24) * ($row_prop['height']+24)) * 0.000001;
      $lm = (($row_prop['width']+72) + ($row_prop['height']+72)) * 0.002;
    }

    if($m2 < 0.3) # enforce minimum size
    $m2 = 0.3;
    

  }

#	-----------------------------------------------------------Size code End--------------------------------------------------


#	-----------------------------------------------------------new code start--------------------------------------------------
	# calc labour costs
	
	$panDGLabour = $row_prop['styledgvalue'] + $m2 + ($row_prop['conditionvalue'] * $lm) + ($row_prop['astragalvalue'] * 2);
	$panEVSLabour = ($row_prop['styleevsvalue'] + $m2) + ($row_prop['conditionvalue'] * $lm * 0.3) + ($row_prop['astragalvalue'] * $row_prop['conditionvalue']);
	

# get agent rates
// add in ,EVSx2rate,EVSx3rate
$getagentrate=$db->joinquery("SELECT SGUrate,IGUx2rate,IGUx3rate,EVSx2rate,EVSx3rate FROM agent WHERE agentid='".$_SESSION['agentid']."'");
$rowagent=mysqli_fetch_array($getagentrate);
# check location margins exist

$chk_margins=$db->joinquery("SELECT locationid FROM location_margins WHERE locationid='".$_POST['locationid']."'");
if(mysqli_num_rows($chk_margins) == 0){
		$db->joinquery("INSERT INTO location_margins(locationid)VALUES('".$_POST['locationid']."')");

}
# get margin
$getmargins=$db->joinquery("SELECT evsmargin,igumargin,productmargin,labourrate FROM location_margins WHERE locationid='".$_POST['locationid']."'");
$location_margins=mysqli_fetch_array($getmargins);

# get calcparams
$getcalcparams=$db->joinquery("SELECT * FROM params");
$calcParams=mysqli_fetch_array($getcalcparams);

	//G	lass
			$SGU_Glass = ((($rowagent['SGUrate'] + $row_prop['safetyvalue'] + $row_prop['typevalue']) * $m2) * $location_margins['igumargin']);
			$IGUx2_Glass = ((($rowagent['IGUx2rate'] + $row_prop['safetyvalue'] + $row_prop['typevalue']) * $m2) * $location_margins['igumargin']);
			$IGUx3_Glass = ((($rowagent['IGUx3rate'] + $row_prop['safetyvalue'] + $row_prop['typevalue']) * $m2) * $location_margins['igumargin']);
			// change to ,EVSx2rate,EVSx3rate
			$EVSx2_Glass = ((($rowagent['EVSx2rate']+($row_prop['safetyvalue'] * 0.5))* $m2)* $location_margins['evsmargin']);
	        $EVSx3_Glass = ((($rowagent['EVSx3rate']+($row_prop['safetyvalue'] * 0.5))* $m2)* $location_margins['evsmargin']);
			
	//Materials
	// take out $panel['IGUassemble'] and $panel['EVSassemble']
			$SGU_Materials = (($calcParams['dgmaterials'] * $lm) * $location_margins['igumargin']);
			$IGUx2_Materials =(($calcParams['dgmaterials'] * $lm) * $location_margins['igumargin']);
			$IGUx3_Materials = (($calcParams['dgmaterials'] * $lm) * $location_margins['igumargin']);
			$EVSx2_Materials =(($calcParams['evsmaterials'] * $lm) * $location_margins['evsmargin']);
			$EVSx3_Materials = (($calcParams['evsmaterials'] * $lm) * $location_margins['evsmargin']);
			
	//Labour
	// insert +$panel['IGUassemble'] and +$panel['EVSassemble'] and +$m2
			$SGU_Labour = (($panDGLabour + $m2 + $row_prop['IGUassemble']) * $location_margins['labourrate']);
			$IGUx2_Labour =(($panDGLabour + $m2 + $row_prop['IGUassemble']) * $location_margins['labourrate']);
			$IGUx3_Labour =(($panDGLabour + $m2 + $row_prop['IGUassemble']) * $location_margins['labourrate']);
			$EVSx2_Labour =(($panEVSLabour + $m2 + $row_prop['EVSassemble']) * $location_margins['labourrate']);
			$EVSx3_Labour = (($panEVSLabour + $m2 + $row_prop['EVSassemble']) * $location_margins['labourrate']);

	//Totals
  if($m2>0){

    $SGUTotal = $SGU_Glass + $SGU_Materials + $SGU_Labour;
    $IGUx2Total = $IGUx2_Glass + $IGUx2_Materials + $IGUx2_Labour;
    $IGUx3Total = $IGUx3_Glass + $IGUx3_Materials + $IGUx3_Labour;
    $EVSx2Total = $EVSx2_Glass + $EVSx2_Materials + $EVSx2_Labour;
    $EVSx3Total = $EVSx3_Glass + $EVSx3_Materials + $EVSx3_Labour;

  }else{

    $SGUTotal=0;
    $IGUx2Total=0;
    $IGUx3Total=0;
    $EVSx2Total=0;
    $EVSx3Total=0;

    $SGU_Labour =0;
    $IGUx2_Labour=0;
    $IGUx3_Labour=0;
    $EVSx2_Labour=0;
    $EVSx3_Labour=0;
    
  }
#	-----------------------------------------------------------new code END--------------------------------------------------
if($_POST['status']==0){
/*$getCatgory=$db->joinquery("SELECT TRIM(window.materialCategory) AS matcategory FROM window,panel WHERE panel.windowid=window.windowid AND panel.panelid='".$_POST['panelid']."'");
$rowCategory=mysqli_fetch_array($getCatgory);
$cat=rtrim($rowCategory['matcategory']);*/
$cat = rtrim($row_prop['materialCategory']);	
$getCat=$db->joinquery("SELECT category,famecategoryid FROM famecategory WHERE material_tag='".$cat."'");
echo '<select name="styleop" id="styleop" class="form-control">
<option value="'.$row_prop['styleid'].'" selected="selected">'.$row_prop['stylename'].'</option>
<option value="'.$cat.'">All</option>';
while($rowCat=mysqli_fetch_array($getCat))
{
	 echo '<option value='.$rowCat['famecategoryid'].'>'.$rowCat['category'].'</option>';
}
echo '</select>'."@";
?>
<input type="hidden" name="locationid" id="locationid" value="<?php echo $_POST['locationid'];?>" />
<input type="hidden" name="panelid" id="panelid" value="<?php echo $_POST['panelid'];?>" />
<div class="row">
                              		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">

                                  <div class="form-group">
                                  		<label>Height</label>
                                  		<input type="text" class="form-control" name="height" id="height" value="<?php echo $row_prop['height'];?>">
                                  </div>
                                	
                                </div>
                                
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <div class="form-group">
                                  		<label>Width</label>
                                  		<input type="text" class="form-control" name="width" id="width" value="<?php echo $row_prop['width'];?>">
                                  </div>
                                </div>
                                
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                		<div class="form-group">
                                  		<label>Center</label>
                                  		<input type="text" class="form-control" name="center" id="center" value="<?php echo $row_prop['center'];?>">
                                  </div>
                                </div>
                                
                                <div class="col-lg-12">
                                		<div class="form-group">
                                  		<label class="cust_checkbox2" style="display:inline-block;">Actual
                                      <input type="radio" name="measurement" <?php if($row_prop['measurement']=='actual'){?> checked="checked" <?php } ?> value="actual">
                                      <span class="checkmark alarm"></span>
                                    </label>
                                    
                                    <label class="cust_checkbox2" style="display:inline-block;">Estimate
                                      <input type="radio" name="measurement" <?php if($row_prop['measurement']=='estimate'){?> checked="checked" <?php } ?> value="estimate">
                                      <span class="checkmark alarm"></span>
                                    </label>
                                  </div>
                                  
                                  <h4 style="color: #fff; text-align: center; padding: 5px 10px; background: #ccc;">Specifications</h4>
                                  <div class="table-responsive">
                                  		<table class="table">
                                    		<tbody>
                                      		<tr>
                                        		<td>Safety</td>
                                          <td id="spn_safty_name"><?php echo $row_prop['safetyname'];?></td>
                                          <td><?php if(file_exists($gPanelOptionsPhotoDir."saftyicons/".$row_prop['safetyid'].".png")){?>
                                          <img src="<?php echo $gPanelOptionsPhotoURL."saftyicons/".$row_prop['safetyid'].".png";?>" style="width:30px;" id="safty-image">
                                          <?php } ?>
                                          </td>
                                          <td id="spn_safty_val"><?php echo $row_prop['safetyvalue'];?></td>
                                          <td>
                                          		<select class="form-control" id="drop-safty">
                                            <option value="<?php echo $row_prop['safetyid'];?>"><?php echo $row_prop['safetyname'];?></option>
                                            <?php
																																												 while($row_safty=mysqli_fetch_array($getsafty)){
																																														if($row_prop['safetyid']!=$row_safty['safetyid']){
																																														echo '<option value="'.$row_safty['safetyid'].'">'.$row_safty['name'].'</option>';
																																														}
																																													}
																																												?>
                                            		
                                            </select>
                                          </td>
                                        </tr>
                                        
                                        <tr>
                                        		<td>Glasstype</td>
                                          <td id="spn_glass_name"><?php echo $row_prop['glassname'];?></td>
                                          <td><?php if($gPanelOptionsPhotoDir."glassicons/".$row_prop['glasstypeid'].".png"){?>
                                          <img src="<?php echo $gPanelOptionsPhotoURL."glassicons/".$row_prop['glasstypeid'].".png";?>" style="width:30px;" id="glass-image">
                                          <?php } ?>
                                          </td>
                                          <td id="spn_glass_value"><?php echo $row_prop['typevalue'];?></td>
                                          <td>
                                          		<select class="form-control" id="drop-glasstype">
                                            <option value="<?php echo $row_prop['glasstypeid'];?>"><?php echo $row_prop['glassname'];?></option>
                                            <?php
																																												 while($row_glass=mysqli_fetch_array($getglass)){
																																														if($row_prop['glasstypeid']!=$row_glass['glasstypeid']){
																																														echo '<option value="'.$row_glass['glasstypeid'].'">'.$row_glass['name'].'</option>';
																																														}
																																													}
																																												?>
                                            		
                                            </select>
                                          </td>
                                        </tr>
                                        
                                         <tr>
                                        		<td>Condition</td>
                                          <td id="spn_condition_name"><?php echo $row_prop['conditionname'];?></td>
                                          <td></td>
                                          
                                          <td id="spn_condition_value"><?php echo $row_prop['conditionvalue'];?></td>
                                          <td>
                                          		<select class="form-control" id="drop-condition">
                                            <option value="<?php echo $row_prop['conditionid'];?>"><?php echo $row_prop['conditionname'];?></option>
                                            <?php
																																												 while($row_condition=mysqli_fetch_array($getcondition)){
																																														if($row_prop['conditionid']!=$row_condition['conditionid']){
																																														echo '<option value="'.$row_condition['conditionid'].'">'.$row_condition['name'].'</option>';
																																														}
																																													}
																																												?>
                                            		
                                            </select>
                                          </td>
                                        </tr>
                                        <tr>
                                        		<td>Astragal</td>
                                          <td id="spn_astragl_name"><?php echo $row_prop['astragalname'];?></td>
                                          <td></td>
                                          
                                          <td id="spn_astragal_value"><?php echo $row_prop['astragalvalue'];?></td>
                                          <td>
                                          		<select class="form-control" id="drop-astragal">
                                            <option value="<?php echo $row_prop['astragalsid'];?>"><?php echo $row_prop['astragalname'];?></option>
                                            <?php
																																												 while($row_astragal=mysqli_fetch_array($getastragal)){
																																														if($row_prop['astragalsid']!=$row_astragal['astragalsid']){
																																														echo '<option value="'.$row_astragal['astragalsid'].'">'.$row_astragal['name'].'</option>';
																																														}
																																													}
																																												?>
                                            		
                                            </select>
                                          </td>
                                        </tr>
                                        <tr>
                                        <td>Total Dglabour</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td id="td_dglabour"><?php echo round($panDGLabour,2);?></td>
                                        </tr>
                                         <tr>
                                        <td>Total Evslabour</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td id="td_evslabour"><?php echo round($panEVSLabour,2);?></td>
                                        </tr>
                                        
                                        
                                        <tr></tr>
                                      </tbody>
                                    </table>
                                     <span id="quotation_pricing">
                                    <table class="table">
                                   
                                        <tr>
                                        <td></td>
                                        <td>Glass</td>
                                        <td>Materials</td>
                                        <td>Labour</td>
                                        
                                        <td>Total</td>
                                       
                                        </tr>
                                         <tr>
                                        <td>SGU</td>
                                        <td>$<?php echo round($SGU_Glass, 2);?></td>
                                        <td>$<?php echo round($SGU_Materials,2);?></td>
                                        <td>$<?php echo round($SGU_Labour,2);?></td>
                                        <td>$<?php echo round($SGUTotal,2);?></td>
                                       
                                        </tr>
                                        <tr>
                                        <td>IGUx2</td>
                                         <td>$<?php echo round($IGUx2_Glass,2);?></td>
                                        <td>$<?php echo round($IGUx2_Materials,2);?></td>
                                        <td>$<?php echo round($IGUx2_Labour,2);?></td>
                                        <td>$<?php echo round($IGUx2Total,2);?></td>
                                        
                                        </tr>
                                         <tr>
                                        <td>IGUx3</td>
                                         <td>$<?php echo round($IGUx3_Glass,2);?></td>
                                        <td>$<?php echo round($IGUx3_Materials,2);?></td>
                                        <td>$<?php echo round($IGUx3_Labour,2);?></td>
                                        <td>$<?php echo round($IGUx3Total,2);?></td>
                                        
                                        </tr>
                                         <tr>
                                        <td>EVSx2</td>
                                         <td>$<?php echo round($EVSx2_Glass,2);?></td>
                                        <td>$<?php echo round($EVSx2_Materials,2);?></td>
                                        <td>$<?php echo round($EVSx2_Labour,2);?></td>
                                        <td>$<?php echo round($EVSx2Total,2);?></td>
                                        
                                        </tr>
                                        <tr>
                                        <td>EVSx3</td>
                                         <td>$<?php echo round($EVSx3_Glass,2);?></td>
                                        <td>$<?php echo round($EVSx3_Materials,2);?></td>
                                        <td>$<?php echo round($EVSx3_Labour,2);?></td>
                                        <td>$<?php echo round($EVSx3Total,2);?></td>
                                        <td></td>
                                        </tr>
                                        
                                    </table>
                                    </span>
                                    <input type="button" class="btn btn-primary btn-block" id="panel-profile-update" value="Update" />
                                  
                           </div>
                           
                         </div>
                     
                       </div>
    <?php
			
			echo"@";?>
   
    <select name="framcategory" id="panel-framcategory" class="btn btn-default dropdown-toggle">
             
             <option value="<?php echo $row_prop['materialCategory'];?>"><?php echo $row_Mate['materialCategory'];?></option>
             
             <?php
             while($rowframes =mysqli_fetch_array($framecategory)){
														
														if($row_prop['materialCategory']!=$rowframes['material_tag']){
														
														echo '<option value="'.$rowframes['material_tag'].'" >'.$rowframes['materialCategory'].'</option>';
														}
														
             }
             ?>
             </select>
  
   <?php
}
?>
                                    






