<?php

	include('includes/functions.php');
																	
																	if(isset($_SESSION['agentid']))
																	{
																		 	

																		  	$getloc=$db->joinquery("SELECT DISTINCT(city) FROM location WHERE agentid=".$_SESSION['agentid']."");
																	    $gtestatus=$db->joinquery("SELECT * FROM  location_status");
																					/*if(isset($_POST['search']))
																					{
																										$searchString = ',';
				
																								if( strpos($_POST['search-text'], $searchString) !== false ) {
																								
																													$search_text=	str_replace(","," ",$_POST['search-text']);
																								}
																								else
																								{
																											$search_text=	$_POST['search-text'];
																								}
																								
																										$searchtext=urldecode($search_text);
																										
																								$getprop=$db->joinquery("SELECT * FROM location WHERE CONCAT(`unitnum`,`street`,`suburb`,`city`) LIKE '%".$searchtext."%'");
				
																								}
																				else
																				{*/
																					 $getprop=$db->joinquery("SELECT locationid,`unitnum`,`street`,`suburb`,`city` FROM location WHERE agentid=".$_SESSION['agentid']." AND (locationstatusid!='3' AND locationstatusid!='5')");
																			//	}
																	    
																	}
																 else
																	{
																		  header('Location:index.php');
																				exit;
																				
																	}
																	?>
																	
                 <div class="form-group">
                        	<select class="form-control" name="location" id="location">
                            	<option value="select">Select Location</option>
                             <?php
																											if(mysqli_num_rows($getloc)>0)
																											{
																												 while($row_loc=mysqli_fetch_array($getloc))
																													{
																														  echo '<option value="'.$row_loc['city'].'">'.$row_loc['city'].'</option>';
																													}
																												 
																											}
																												?>
                            </select>
                        </div>
                        <div class="form-group">
                        	<select class="form-control" name="quote_status" id="quote_status">
                            <option value="All">All</option>
                             <?php
																											if(mysqli_num_rows($gtestatus)>0)
																											{
																												
																												 while($row_status=mysqli_fetch_array($gtestatus))
																													{
																														  echo '<option value="'.$row_status['locationstatusid'].'">'.$row_status['status'].'</option>';
																													}
																												 
																											}
																												?>
                            </select>
                        </div>
                        
                        <div class="form-group" id="result-prop">
                        	<select class="form-control" id="list-prop" name="list-prop">
                            	<option>Select Property</option>
                             <?php
																											if(mysqli_num_rows($getprop)>0)
																											{
																												 while($row_prop=mysqli_fetch_array($getprop))
																													{
																														  $loc=$row_prop['unitnum'].",".$row_prop['street'];
																																	if(!empty($row_prop['suburb']))
																																	{
																																			$loc.=",".$row_prop['suburb'];
																																	}
																																	$loc.=",".$row_prop['city'];
																														  echo '<option value="'.$row_prop['locationid'].'">'.$loc.'</option>';
																													}
																												 
																											}
																												?>
                            </select>
                        </div>