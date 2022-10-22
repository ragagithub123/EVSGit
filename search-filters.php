 <?php ob_start();session_start();
	
																	
																	
																	if(isset($_SESSION['agentid']))
																	{
																		
																		
																		  if(isset($_REQUEST['id'])){
																					$get_re_loc=$db->joinquery("SELECT city FROM location WHERE locationid=".base64_decode($_REQUEST['id'])."");
																					$res_re_loc=mysqli_fetch_array($get_re_loc);
																					$req_array['city']=$res_re_loc['city'];
																					$get_req_prop=$db->joinquery("SELECT locationid,`unitnum`,`street`,`suburb`,`city` FROM location WHERE locationid=".base64_decode($_REQUEST['id'])."");
																					$res_re_prop=mysqli_fetch_array($get_req_prop);
																					$req_array['lcoationid']=base64_decode($_REQUEST['id']);
																					$loc_req=$res_re_prop['unitnum'].",".$res_re_prop['street'];
																									if(!empty($res_re_prop['suburb']))
																									{
																											$loc_req.=",".$res_re_prop['suburb'];
																									}
																									$loc_req.=",".$res_re_prop['city'];
																										$req_array['property']=$loc_req;
																									
																										
																				}
																		 	
                        
																		  	$getloc=$db->joinquery("SELECT DISTINCT(city) FROM location WHERE agentid=".$_SESSION['agentid']."");
																					if(mysqli_num_rows($getloc)>0)
																					{
																						 while($roloc=mysqli_fetch_array($getloc))
																							{
																									$postloc[]=$roloc;
																							}
																					}
																					
																	    $gtestatus=$db->joinquery("SELECT * FROM  location_status");
																					if(mysqli_num_rows($gtestatus)>0)
																					{
																						  while($row_status=mysqli_fetch_array($gtestatus))
																								{
																									 $poststatus[]=$row_status;
																								}
																					}
																					if(isset($_POST['search']))
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
																					// echo '<input type="hidden" id="search-hidden" value="'.$_POST['search-text'].'">';
																					//$getprop=$db->joinquery("SELECT `locationid`,`unitnum`,`street`,`suburb`,`city` FROM location WHERE (`street` LIKE '%".$_POST['search-text']."%' OR `suburb` LIKE '%".$_POST['search-text']."%' OR `city` LIKE '%".$_POST['search-text']."%' OR `unitnum` LIKE '%".$_POST['search-text']."%') AND agentid=".$_SESSION['agentid']."");
																				$getprop=$db->joinquery("SELECT * FROM location WHERE CONCAT(`unitnum`,`street`,`suburb`,`city`) LIKE '%".$searchtext."%'");

																				}
																				else
																				{
																					 if(isset($_REQUEST['id'])){
																					 $getprop=$db->joinquery("SELECT locationid,`unitnum`,`street`,`suburb`,`city` FROM location WHERE agentid=".$_SESSION['agentid']." AND (locationstatusid!='3' AND locationstatusid!='5') AND locationid!=".base64_decode($_REQUEST['id'])."");
																				  }else{
																							 $getprop=$db->joinquery("SELECT locationid,`unitnum`,`street`,`suburb`,`city` FROM location WHERE agentid=".$_SESSION['agentid']." AND (locationstatusid!='3' AND locationstatusid!='5')");
																							}
																							
																				}
																				if(mysqli_num_rows($getprop)>0)
																				{
																					  while($rowprop=mysqli_fetch_array($getprop))
																							{
																									$locprop[]=$rowprop;
																							}
																				}
																				
																				include('views/search-filter.htm');
																	    
																	}
																 else
																	{
																		  header('Location:index.php');
																				exit;
																				
																	}
																	?>
																	
                