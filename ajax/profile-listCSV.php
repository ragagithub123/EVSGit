<?php
		$filename ='ProfileList.csv';
			$delimiter = ",";
			$f = fopen('php://memory', 'w'); 

$fields = array('ID', 'Room', 'Window[Panel]','Style','Safety','Profile','XT','Profile','XB','Profile','YL','Profile','YR');

fputcsv($f, $fields, $delimiter); 
	
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
															if($row_panel['panels']['width'] >0){$widh =  ($row_panel['panels']['width'] + $profileX);}
															
															else {$widh =0;}
															
											
														$lineData = array($row_panel['ID'], $row_panel['room_name'], $row_panel['name']."[".$row_panel['panels']['panelnum']."]", $row_panel['panels']['stylename'],$row_panel['panels']['safty_name'],$profiletop,$widh, $profilebottom,$widh,$leftprofile,$profilesizey,$rightprofile,$profilesizey); 
                  fputcsv($f, $lineData, $delimiter); 
													
							}
				}
			}
			if(!empty($arr_width[0]))
			$measuremnt_arr = array_count_values($arr_width);
		 else
			 $measuremnt_arr =array();
				
				
				 fseek($f, 0); 

	// Set headers to download file rather than displayed 
	header('Content-Type: text/csv'); 
	header('Content-Disposition: attachment; filename="' . $filename . '";'); 

	// Output all remaining data on a file pointer 
	fpassthru($f); 

	exit();
			?>
   

	
