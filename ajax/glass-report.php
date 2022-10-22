    <table class="table table-bordered table-striped fontstyle blue-border-table">
    <thead>
    <tr style="color:#fff; background:#565759;">
    <th>ID</th>
    <th colspan="2">Cut List</th>
    <th colspan="3">Glass</th>
    <th colspan="8">Layer Configuration</th>
   
    </tr>
    </thead>
    <tbody>
    <tr>
    <th>#</th>
    <th>Room</th>
    <th>Window[Panel]</th>
    
    <th>Height</th>
    <th>Width</th>
    <th>m2</th>
    <th>Name</th>
    <th>Outside</th>
    <th>Safety</th>
    <th>mm</th>
    <th>Inside</th>
    <th>Safety</th>
    <th>mm</th>
    </tr>
    </tbody>
   
  <?php
		$flag = 0;
		$getlayerCount = $db->joinquery("SELECT * FROM window_layers WHERE locationid='" . $Locationid . "'");
		if(mysqli_num_rows($getlayerCount) == 0)
		$flag = 1;
		 $i=0;
		
			if(count($postpanel)>0)
			{
						
				foreach($postpanel as $row_panes)
				{  
				      if($row_panes['selected_product']=="EVSx3" || $row_panes['selected_product']=="EVSx2")
										{
													
													$glassX=$row_panes['panels']['evsGlassX'];
													$glassY=$row_panes['panels']['evsGlassY'];
												
												
													
											}
											else{
												
													$glassX=$row_panes['panels']['retroGlassX'];
													$glassY=$row_panes['panels']['retroGlassY'];
												
												
													
													}
														if($glassX == NULL)$glassX=0;
														if($glassY == NULL)$glassY=0;
														
														if($row_panes['panels']['width'] >0)$row_panes['GlassX']= ($row_panes['panels']['width'] + $glassX);
														
														 
															
															if($flag == 1)
													 
														$getlayer	= $db->joinquery("SELECT * FROM paneloption_layers WHERE glassType='".$row_panes['panels']['glasstypeid']."'");
														
														else
														
														$getlayer	= $db->joinquery("SELECT paneloption_layers.* FROM paneloption_layers,window_layers WHERE paneloption_layers.layersid=window_layers.layerid AND paneloption_layers.glassType=window_layers.glassid AND window_layers.glassid='" . $row_panes['panels']['glasstypeid'] . "' AND window_layers.locationid='" . $Locationid . "'");
									    
													$rowlayer =mysqli_fetch_array($getlayer);
													
													$row_panes['panels']['layer'] = $rowlayer['name'];
													
													$row_panes['panels']['outsideThickness'] = $rowlayer['outsideThickness'];
													
													$outsideglass = $db->joinquery("SELECT name FROM paneloption_glasstype WHERE glasstypeid='" . $rowlayer['outsideGlasstype'] . "'");

             $rowglassoutside = mysqli_fetch_array($outsideglass);

             $row_panes['panels']['outsideGlasstype'] = $rowglassoutside['name'];
													
													 $insideglass = $db->joinquery("SELECT name FROM paneloption_glasstype WHERE glasstypeid='" . $rowlayer['insideGlasstype'] . "'");

															$rowglassinside = mysqli_fetch_array($insideglass);

															$row_panes['panels']['insideGlasstype'] = $rowglassinside['name'];
															
																$row_panes['panels']['insideThickness'] = $rowlayer['insideThickness'];
														
													
					
					            $result_arr[]=$row_panes;
				}
			
				/*if(!empty($result_arr))
				{
					 	$final_array=	sort_2d_desc($result_arr,'GlassX');

				}*/
			//	print_r($final_array);die();
			

				if(!empty($result_arr))
				{
					   $area=array();
					    foreach($result_arr as $row_panel)
							{
								  $i++;
										
										
										?>
          <tr>
           <th><?php echo $row_panel['ID'];?></th>
           <td><?php echo $row_panel['room_name'];?></td>
           <td><?php echo $row_panel['name']."[".$row_panel['panels']['panelnum']."]";?></td>
          <?php
										if($row_panel['selected_product']=="EVSx3" || $row_panel['selected_product']=="EVSx2")
										{
													
													$glassX=$row_panel['panels']['evsGlassX'];
													$glassY=$row_panel['panels']['evsGlassY'];
													$outsideprofile=$row_panel['panels']['evsOutPanelType'];
											  $outsidethickness=$row_panel['panels']['evsOutPanelThickness'];
											  //$outsidethickness=$row_panel['panels']['outsideThickness'];
													$insideprofile=$row_panel['panels']['evsInPanelType'];
													$insidethickness=$row_panel['panels']['evsInPanelThickness'];
													$safetyarr = evsSafety($row_panel['panels']['safty_name']);
													$outersafety = $safetyarr[0];
													$innersafety = $safetyarr[1];
													$layername = $row_panel['panels']['layer'];
												
													
													}
													else{
												
													$glassX=$row_panel['panels']['retroGlassX'];
													$glassY=$row_panel['panels']['retroGlassY'];
													$outsideprofile=$row_panel['panels']['retroOutPanelType'];
												 $outsidethickness=$row_panel['panels']['retroOutPanelThickness'];
												 //$outsidethickness=$row_panel['panels']['outsideThickness'];
													$insideprofile=$row_panel['panels']['retroInPanelType'];
													$insidethickness=$row_panel['panels']['retroInPanelThickness'];
													$safetyarr = sguSafety($row_panel['panels']['safty_name']);
													$outersafety = $safetyarr[0];
													$innersafety = $safetyarr[1];
												$layername = $row_panel['panels']['layer'];
													
													}
														if($glassX == NULL)$glassX=0;
														if($glassY == NULL)$glassY=0;
														 if($row_panel['center'] > $row_panel['height']){
															//$glassSizey=($row_panel['center'])+($row_panel['height']);
															$glassSizey=($row_panel['panels']['center'])+$glassY;
															$m2=round(((($row_panel['panels']['width'] + $glassX)*($row_panel['panels']['center']))*0.000001),2);
															}
															else{
																							if($row_panel['panels']['height']>0){
																							$row_panel['GlassY']=($row_panel['panels']['height'] + $glassY);
																							$glassSizey=$row_panel['panels']['height'] + $glassY;
																					}
																							else{
																								$row_panes['GlassY']=0;
																									$glassSizey=0;
																							}
																	$m2=round(((($row_panel['panels']['width'] + $glassX)*($row_panel['panels']['height'] + $glassY))*0.000001),2);
															}
															
										    if($row_panel['panels']['width'] >0){
														 $area[]=	$m2;
														
															$key=($row_panel['panels']['height'] + $glassY).'&nbsp;&#10006;'.($row_panel['panels']['width'] + $glassX);
															$arr_width[]=$key."@".$layername;
															$arr_outsideprofile[]=$outsideprofile;
															$arr_outsidethickness[]=$outsidethickness;
															$arr_insideprofile[]=$insideprofile;
															$arr_insidethickness[]=$insidethickness;
															$arr_layer[]= $layername;
													
														
															}
															
															if($row_panel['panels']['width'] >0){$width =($row_panel['panels']['width'] + $glassX); $m2 =$m2;}
															else{$width=0;$m2=0;}
														?>
             
             <td><?php  echo $glassSizey;?></td>
             <td><?php if($row_panel['panels']['width'] >0){echo ($row_panel['panels']['width'] + $glassX);}?></td>
             <td><?php if($row_panel['panels']['width'] >0){ echo $m2;}?></td>
             <td><?php echo $row_panel['panels']['layer'];?></td>
             <td><?php echo $row_panel['panels']['outsideGlasstype'];?></td>
             <td><?php echo $outersafety;?></td>
             <td><?php echo $row_panel['panels']['outsideThickness'];?></td>
             <td><?php echo $row_panel['panels']['insideGlasstype'];?></td>
             <td><?php echo $innersafety;?></td>
             <td><?php echo $row_panel['panels']['insideThickness'];?></td>
            <!-- <td><?php //echo $outsideprofile;?></td>
              <td><?php //echo $outsidethickness;?></td>
              <td><?php //echo $row_panel['panels']['safty_name'];?></td>
               <td><?php //echo $insideprofile;?></td>
              <td><?php //echo $insidethickness;?></td>
              <td><?php //echo $row_panel['panels']['safty_name'];?></td>-->
             </tr>
														<?php
														
												
										 
													
							}
				}
				 
																																									
			}
			echo ' </table>';
			
			
			
			if(!empty($arr_width[0]))
			$measuremnt_arr = array_count_values($arr_width);
		 else
			 $measuremnt_arr =array();
				
			function evsSafety($Safety){
				
				if ($Safety == "Door"){
       $SafetyOutside = "Tuff";
       $SafetyInside  =   "No";
    }else if($Safety == "Window"){
      $SafetyOutside = "No";
      $SafetyInside  = "No";
    }else{
      $SafetyOutside = "No";
      $SafetyInside  =  "No";
    }
				
				return array($SafetyOutside,$SafetyInside);
				
			}
			function sguSafety($Safety){
				
				if ($Safety == "Door"){
       $SafetyOutside = "Tuff";
       $SafetyInside  =   "Tuff";
    }else if($Safety == "Window"){
      $SafetyOutside = "No";
      $SafetyInside  = "Tuff";
    }else{
      $SafetyOutside = "No";
      $SafetyInside  =  "No";
    }
				
					return array($SafetyOutside,$SafetyInside);
				
			}
		
				?>
   <div class="property_dtls">
                    	<div class="pro_inner_one">
                        	<h4 class="main_title" style="min-height:50px;">Job Summary</h4>
                            <div class="pro_inner_one_details">
                            	<div class="pro_inner_one_pic">
                                 <?php
																																				if($row_details['photoid'] !=0)
																																				{
																																					?>
                                     <img src="http://evsapp.nz/photos/<?php echo $row_details['photoid'];?>.jpg" class="img-responsive"> 
                                     <?php
                                     
																																				}?>
                                </div>
                                <div class="pro_inner_one_list">
                                	<ul>
                                    	<li>Property Address</li>
                                        <li><?php echo $loc;?></li>
                                        <li><?php echo $row_details['city'];?></li>
                                        </ul>
                                        <ul>
                                        <li><?php echo $row_details['afirstname']." ".$row_details['alastname'];?></li>
                                        <li><?php echo $row_details['aloc'];?></li>
                                         <li><?php echo $row_details['acity'];?></li>
                                        <li><?php echo $row_details['aphone'];?></li>
                                        <li><?php echo $row_details['aemail'];?></li>
                                    </ul>
                                 
                                </div>
                            </div><!-- ./pro_inner_one_details -->
                        </div><!-- ./pro_inner_one -->
                        
                        
                        <!-- Modal property-->
                        
                        <div id="myModalprop" class="modal fade" role="dialog">
                       <div class="modal-dialog">
                     
                         <!-- Modal content-->
                         <div class="modal-content">
                           <div class="modal-header">
                             <button type="button" class="close" data-dismiss="modal">&times;</button>
                             <h4 class="modal-title">URL Link</h4>
                           </div>
                           <div class="modal-body">
                            
                              <p>
                             <textarea id="quote-url" style="resize:none; width:100%" rows="5"><?php echo $quoteURL;?></textarea>
                             </p>
                           </div>
                           <div class="modal-footer">
                              <a href="<?php echo $quoteURL;?>" class="btn btn-default" target="_blank">View</a>
                             <button type="button" class="btn btn-default" id="copy-text">Copy</button>
                              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                           </div>
                         </div>
                     
                       </div>
               </div>
                        
      <!-- End Modal property-->                  
                        
                        
                        <div class="pro_inner_two">
                        	<div class="pro_inner_two_details" >
                            	<h4 class="main_title"><span></span> <a data-toggle="modal" data-target="#myModalprop" href="#" class="btn btn-info edit-btn">Link URL</a></h4>
                                <ul >
                               <?php 
																														
																															if(!empty($measuremnt_arr)){
																																	$j=0;
																															foreach($measuremnt_arr as $key => $value) {
																																
																																$keyval = explode('@',$key);
																														
																																 echo '<li  style="color:#000">('.$keyval[1].' ) &nbsp;&nbsp;'.$value.'@'.$keyval[0].'</li>';
																																	$j++;
																															}}
																													if(!empty($area[0])){
																															?>
																																
                                <li style="color:#060; font-weight:bold;font-size:20px;"> Total : <?php echo array_sum($area);?>m2</li><?php } ?>
                                </ul>
                                <ul >
                               <?php 
																														/*	if(!empty($measuremnt_arr)){
																																$j=0;
																															foreach($measuremnt_arr as $key => $value) {
																														
																																 echo '<li  style="color:#000">'.$arr_insideprofile[$j].'('.$arr_insidethickness[$j].' mm) &nbsp;&nbsp;'.$value.'@'.$key.'</li>';
																																	$j++;
																															}}
																																					
																																		
																																			if(!empty($area[0])){
																															
																															?>
																																 <li style="color:#060; font-weight:bold;font-size:20px;">Inside Total : <?php echo array_sum($area);?>m2</li><?php }*/ ?>
                                
                                </ul>
                            </div>
                        </div><!-- ./pro_inner_two -->
                    </div>
   
