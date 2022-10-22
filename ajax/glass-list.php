<?php
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
														
														if($row_panes['panels']['width'] >0)$row_panes['panels']['GlassX']= ($row_panes['panels']['width'] + $glassX);
													if($row_panes['panels']['height'] >0)$row_panes['panels']['GlassY']=($row_panes['panels']['height'] + $glassY);
															
														
					
					
					 $result_arr[]=$row_panes;
				}
			/*	if(!empty($result_arr))
				{
					 	$final_array=	sort_2d_desc($result_arr,'GlassX');

				}*/
					if(!empty($result_arr)){
					    foreach($result_arr as $row_panel){
										$i++;
										?>
          <tr>
           <th><?php echo $row_panel['ID'];?></th>
           <td><?php echo $row_panel['room_name'];?></td>
           <td><?php echo $row_panel['name']."[".$row_panel['panels']['panelnum']."]";?></td>
             <td><?php if($row_panel['panels']['styleid'] > 0 && file_exists($gPanelOptionsPhotoDir.$row_panel['panels']['styleid'].".png"))
																													{
																																	echo "<img src=\"". $gPanelOptionsPhotoURL.$row_panel['panels']['styleid'].".png?". time(). "\" class=\"img-responsive\" style=\"width: 50px; height; 50px;\">";
																													} ?></td>
                             <td><?php echo $row_panel['panels']['safty_name'];?></td>
                                   
                                   <td <?php if($row_panel['panels']['measurement']=='estimate'){?> style="color:#F00"<?php }?>><?php echo $row_panel['panels']['height'];?> </td>
                                   <td <?php if($row_panel['panels']['measurement']=='estimate'){?> style="color:#F00"<?php }?>><?php echo $row_panel['panels']['width'];?> </td>
                                   <td <?php if($row_panel['panels']['measurement']=='estimate'){?> style="color:#F00"<?php }?>><?php echo $row_panel['panels']['center'];?> </td>
                                   <?php
																																			if($row_panel['selected_product']=="EVSx3" || $row_panel['selected_product']=="EVSx2"){
																																				$profiletop=$row_panel['panels']['evsProfileTop'];
																																				$profilebottom=$row_panel['panels']['evsProfileBottom'];
																																				$leftprofile=$row_panel['panels']['evsProfileLeft'];
													                       $rightprofile=$row_panel['panels']['evsProfileRight'];
																																				$glassX=$row_panel['panels']['evsGlassX'];
																																				$glassY=$row_panel['panels']['evsGlassY'];
																																				$profileX=$row_panel['panels']['evsProfileX'];
																																				$profileY=$row_panel['panels']['evsProfileY'];
																																				
																																				}
																																				else{
																																				$profiletop=$row_panel['panels']['retroProfileTop'];
																																				$profilebottom=$row_panel['panels']['retroProfileBottom'];
																																				$leftprofile=$row_panel['panels']['retroProfileLeft'];
													                       $rightprofile=$row_panel['panels']['retroProfileRight'];
																																				$glassX=$row_panel['panels']['retroGlassX'];
																																				$glassY=$row_panel['panels']['retroGlassY'];
																																				$profileX=$row_panel['panels']['retroProfileX'];
																																				$profileY=$row_panel['panels']['retroProfileY'];
																																				
																																				}
																																				 if($glassX == NULL)$glassX=0;
																																					if($glassY == NULL)$glassY=0;
																																					if($profileX == NULL)$profileX=0;
																																					if($profileY == NULL)$profileY=0;
																																				if(($row_panel['panels']['center']) > ($row_panel['panels']['height'])){
                                     //$glassSizey=($row_panel['center'])+($row_panel['height']);
                                     //$profilesizey=($row_panel['center'])+($row_panel['height']);
																																					$glassSizey=($row_panel['panels']['center'])+$glassY;
                                     $profilesizey=($row_panel['panels']['center'])+$profileY;
                                     $m2=round(((($row_panel['panels']['width'] + $glassX)*($row_panel['panels']['center']))*0.000001),2);
                                     }
                                     else{
                                     if($row_panel['panels']['height']>0){
                                     $glassSizey=($row_panel['panels']['height']) + ($glassY);
                                     $profilesizey=($row_panel['panels']['height']) + ($profileY);
                                     }else{
                                       $glassSizey=0;
                                       $profilesizey=0;
                                     }
                                    
                                     $m2=round(((($row_panel['panels']['width'] + $glassX)*($row_panel['panels']['height'] + $glassY))*0.000001),2);
                                     }
                                     
																																				?>
                                    <td><?php echo $glassX;?></td>
                                    <td><?php if($row_panel['panels']['width'] >0){echo ($row_panel['panels']['width'] + $glassX);}?></td>
                                    <td><?php echo $glassY;?></td>
                                    <td><?php echo $glassSizey;?></td>
                                    <td><?php if($row_panel['panels']['width'] >0){ echo round(((($row_panel['panels']['width'] + $glassX)*($row_panel['panels']['height'] + $glassY))*0.000001),2);}?></td>
                                    <td>
                                    <?php
																																				if(file_exists($gProfilePhotoDir.$profiletop.".png"))
																																				{
																																					?>
                                      <span><a class="fs-gal" data-url="<?php echo $gProfilePhotoURL.$profiletop;?>.png" style="color:blue;"><?php echo $profiletop;?></a></span>
                                     <?php
																																				}
																																				else
																																				{?><span><?php echo $profiletop;?></span><?php
																																				}
																																				?>
                                    </td>
                                    <td><?php echo $profileX;?></td>
                                    <td><?php if($row_panel['panels']['width'] >0){echo ($row_panel['panels']['width'] + $profileX);}?></td>
                                    <td> <?php
																																				if(file_exists($gProfilePhotoDir.$profilebottom.".png"))
																																				{
																																					?>
                                      <span><a class="fs-gal" data-url="<?php echo $gProfilePhotoURL.$profilebottom;?>.png" style="color:blue;"><?php echo $profilebottom;?></a></span>
                                     <?php
																																				}
																																				else
																																				{?><span><?php echo $profilebottom;?></span><?php
																																				}
																																				?></td>
                                    <td><?php echo $profileX;?></td>
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
                     <td><?php echo $profileY;?></td>
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
                                    
                                    <td><?php echo $profileY;?></td>
                                    <td><?php echo $profilesizey;?></td>
                                   </tr>
                                   <?php
									}
					}
