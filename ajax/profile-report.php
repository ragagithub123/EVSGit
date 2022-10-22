    <table class="table table-bordered table-striped fontstyle blue-border-table">
    <thead>
    <tr style="color:#fff; background:#565759;">
    <th>ID</th>
    <th colspan="2">Cut List</th>
    <th></th>
    <th colspan="3">Top</th>
    <th colspan="2">Bottom</th>
    <th colspan="2">Sides(Left)</th>
    <th colspan="2">Sides(Right)</th>
   
    </tr>
    </thead>
    <tbody>
    <tr>
    <th>#</th>
    <th>Room</th>
    <th>Window[Panel]</th>
    <th>Style</th>
    <th>Safety</th>
    <th>Profile</th>
    <th>(XT)</th>
    <th>Profile</th>
    <th>(XB)</th>
    <th>Profile</th>
    <th>(YL)</th>
   <th>Profile</th>
    <th>(YR)</th>
    </tr>
    </tbody>
   
 <?php
	  		 $i=0;
		
			if(count($postpanel)>0)
			{
				foreach($postpanel as $row_panes)
				{  
				      if($row_panes['selected_product']=="EVSx3" || $row_panes['selected_product']=="EVSx2")
										{
													$profileX=$row_panes['panels']['evsProfileX'];
												
													
											}
											else
											{
												     $profileX=$row_panes['panels']['retroProfileX'];
												}
													
														if($profileX == NULL)$profileX=0;
													
														
														if($row_panes['panels']['width'] >0)$row_panes['panels']['ProfileXASC']= ($row_panes['panels']['width'] + $profileX);
															
														$result_arr[]=$row_panes;
				}
			/*	if(!empty($result_arr))
				{
					 	$final_array=	sort_2d_desc($result_arr,'ProfileXASC');

				}*/
			 if(!empty($result_arr[0])){
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
													
													$profiletop=$row_panel['panels']['evsProfileTop'];
													$profilebottom=$row_panel['panels']['evsProfileBottom'];
													$leftprofile=$row_panel['panels']['evsProfileLeft'];
													$rightprofile=$row_panel['panels']['evsProfileRight'];
												 $profileX=$row_panel['panels']['evsProfileX'];
													$profileY=$row_panel['panels']['evsProfileY'];
												
													
													}
													else{
												
													$profiletop=$row_panel['panels']['retroProfileTop'];
													$profilebottom=$row_panel['panels']['retroProfileBottom'];
													$profilesides=$row_panel['panels']['retroProfileSides'];
													$profileX=$row_panel['panels']['retroProfileX'];
													$profileY=$row_panel['panels']['retroProfileY'];
													$leftprofile=$row_panel['panels']['retroProfileLeft'];
													$rightprofile=$row_panel['panels']['retroProfileRight'];
												
													
													}
														if($profileX == NULL)$profileX=0;
														if($profileY == NULL)$profileY=0;
														$arr_panno[]=$row_panel['panels']['panelnum'];
														$arr_profiletop[]=$profiletop;
														 if($row_panel['panels']['width']>0){
																$arr_width[]=$row_panel['panels']['width'] + $profileX;
															  if(($row_panel['panels']['center']) > ($row_panel['panels']['height'])){
															  $lm[]=round((((($row_panel['panels']['width'] + 72)+($row_panel['panels']['center'] + 72))*2)*0.001),2);
															  //$glassSizey=($row_panel['center'])+($row_panel['height']);
                // $profilesizey=($row_panel['center'])+($row_panel['height']);
																$glassSizey=($row_panel['panels']['center'])+$glassY;
                 $profilesizey=($row_panel['panels']['center'])+$profileY;
															}
															else{
																					if($row_panel['panels']['height']>0){
																					$glassSizey=($row_panel['panels']['height']) + ($glassY);
																					$profilesizey=($row_panel['panels']['height']) + ($profileY);
																					}else{
																							$glassSizey=0;
																							$profilesizey=0;
																					}
																					$lm[]=round((((($row_panel['panels']['width'] + 72)+($row_panel['panels']['height'] + 72))*2)*0.001),2);
																
															}
													}
															
															
														
														?>
             <td><?php if($row_panel['panels']['styleid'] > 0 && file_exists($gPanelOptionsPhotoDir.$row_panel['panels']['styleid'].".png"))
																													{
																																	echo "<img src=\"". $gPanelOptionsPhotoURL.$row_panel['panels']['styleid'].".png?". time(). "\" class=\"img-responsive\" style=\"width: 50px; height; 50px;\">";
																													} ?></td>
                             <td><?php echo $row_panel['panels']['safty_name'];?></td>
                              <td>
															<?php
               if(file_exists($gProfilePhotoDir.$profiletop.".png"))
               {
                ?>
                 <span><a class="fs-gal" data-url="<?php echo $gProfilePhotoURL.$profiletop;?>.png" style="color:blue;"><?php echo $profiletop;?></a></span>
                <?php
               }
               else
               {?><span ><?php echo $profiletop;?></span><?php
               }
               ?>
               </td>
                <td><?php if($row_panel['panels']['width'] >0){echo ($row_panel['panels']['width'] + $profileX);}?></td>
                <td> <?php
																		if(file_exists($gProfilePhotoDir.$profilebottom.".png"))
																		{
																			?>
																				<span><a class="fs-gal" data-url="<?php echo $gProfilePhotoURL.$profilebottom;?>.png" style="color:blue;"><?php echo $profilebottom;?></a></span>
																			<?php
																		}
																		else
																		{?><span ><?php echo $profilebottom;?></span><?php
																		}
																		?></td>
                   <!--<td><?php //echo $profileX;?></td>-->
                   <td><?php if($row_panel['panels']['width'] >0){echo ($row_panel['panels']['width'] + $profileX);}?></td>
                    <td> <?php
																					if(file_exists($gProfilePhotoDir.$leftprofile.".png"))
																					{
																						?>
																							<span><a class="fs-gal" data-url="<?php echo $gProfilePhotoURL.$leftprofile;?>.png" style="color:blue;"><?php echo $leftprofile;?></a></span>
																						<?php
																					}
																					else
																					{?><span ><?php echo $leftprofile;?></span><?php
																					}
																					?></td>
                   <td><?php echo $profilesizey;?></td>
                   
                   <td> <?php
																					if(file_exists($gProfilePhotoDir.$rightprofile.".png"))
																					{
																						?>
																							<span><a class="fs-gal" data-url="<?php echo $gProfilePhotoURL.$rightprofile;?>.png" style="color:blue;"><?php echo $rightprofile;?></a></span>
																						<?php
																					}
																					else
																					{?><span><?php echo $rightprofile;?></span><?php
																					}
																					?></td>
                  <!-- <td><?php //echo $profileY;?></td>  -->
                  <td><?php echo $profilesizey;?></td>
             </tr>
														<?php
													
							}
				}
			}
			echo '</table>';
			if(!empty($arr_width[0]))
			$measuremnt_arr = array_count_values($arr_width);
		 else
			 $measuremnt_arr =array();
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
																														
																																 echo '<li  style="color:#000">'.$arr_profiletop[$j].' &nbsp;&nbsp;'.$value.'@'.$key.'</li>';
																																	$j++;
																															}}
																															
																															 if(!empty($lm[0]))
																																{
																															?>
																																
                                <li style="color:#060; font-weight:bold;font-size:20px;">Profile Length Total : <?php echo array_sum($lm);?>m</li>
                                <?php } ?>
                                </ul>
                                
                            </div>
                        </div><!-- ./pro_inner_two -->
                    </div>

	
