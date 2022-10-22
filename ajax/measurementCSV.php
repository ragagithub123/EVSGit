<?php
 $filename ='measurement.csv';
	$delimiter = ",";
	$f = fopen('php://memory', 'w'); 

$fields = array('ID', 'Room', 'Window[Panel]','Style','Safety','Height','Width','Center','w','W','h','H','m2','Profile','w','W','Profile','w','W','Profile','h','H','Profile','h','H');

fputcsv($f, $fields, $delimiter); 

 
      
          foreach($postpanel as $row_panel){
											
									
																																								
																																									 $i++;
																																								
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
																																				$profilesides=$row_panel['panels']['retroProfileSides'];
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
                                   //  $glassSizey=($row_panel['center'])+($row_panel['height']);
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
																																					
																																				if($row_panel['panels']['width'] >0){
																																					
																																					$width = ($row_panel['panels']['width'] + $glassX);
																																					
																																						$profilewidth =$row_panel['panels']['width'] + $profileX;
																																					
																																				}
																																				
																																				else{
																																					
																																					 $width =0;
																																						
																																						$profilewidth =0;
																																				}
																																					
																																			
								$lineData = array($row_panel['ID'], $row_panel['room_name'], $row_panel['name']."[".$row_panel['panels']['panelnum']."]", $row_panel['stylename'], $row_panel['panels']['safty_name'], $row_panel['panels']['height'],$row_panel['panels']['width'],$row_panel['panels']['center'],$glassX,$width,$glassY,$glassSizey,$m2,$profiletop,$profileX,$profilewidth,$profilebottom,$profileX,$profilewidth,$leftprofile,$profileY,$profilesizey,$rightprofile,$profileY,$profilesizey); 
        fputcsv($f, $lineData, $delimiter);		
											
											}
											
											
													 fseek($f, 0); 

	// Set headers to download file rather than displayed 
	header('Content-Type: text/csv'); 
	header('Content-Disposition: attachment; filename="' . $filename . '";'); 

	// Output all remaining data on a file pointer 
	fpassthru($f); 

	exit();

  ?>
