<?php
		$flag = 0;
		$getlayerCount = $db->joinquery("SELECT * FROM window_layers WHERE locationid='" . $Locationid . "'");
		if(mysqli_num_rows($getlayerCount) == 0)
		$flag = 1;
		 $i=0;
		
			if(count($postpanel)>0)
			{
							$filename ='GlassList.csv';
			$delimiter = ",";
			$f = fopen('php://memory', 'w'); 

$fields = array('ID', 'Room', 'Window[Panel]','Height','Width','m2','Name','Outside','Safety','mm','Inside','Safety','mm');

fputcsv($f, $fields, $delimiter); 
			
				
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
															$arr_width[]=$key;
															$arr_outsideprofile[]=$outsideprofile;
															$arr_outsidethickness[]=$outsidethickness;
															$arr_insideprofile[]=$insideprofile;
															$arr_insidethickness[]=$insidethickness;
															$arr_layer[]= $layername;
													
														
															}
															
															if($row_panel['panels']['width'] >0){$width =($row_panel['panels']['width'] + $glassX); $m2 =$m2;}
															else{$width=0;$m2=0;}
														
														$lineData = array($row_panel['ID'], $row_panel['room_name'], $row_panel['name']."[".$row_panel['panels']['panelnum']."]", $glassSizey, $width, $m2,$row_panel['panels']['layer'],$row_panel['panels']['outsideGlasstype'],$outersafety,$row_panel['panels']['outsideThickness'],$row_panel['panels']['insideGlasstype'],$innersafety,$row_panel['panels']['insideThickness']); 
                  fputcsv($f, $lineData, $delimiter); 
										 
													
							}
				}
				 
																																									
			}
			
			if($_POST['Download']){
				
				 fseek($f, 0); 

	// Set headers to download file rather than displayed 
	header('Content-Type: text/csv'); 
	header('Content-Disposition: attachment; filename="' . $filename . '";'); 

	// Output all remaining data on a file pointer 
	fpassthru($f); 

	exit();
				
				
			}
			
			
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
   
   
