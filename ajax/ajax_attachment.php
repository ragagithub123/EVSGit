
<?php ob_start();
session_start();
include('../includes/functions.php'); 
$flag = true;
if($_POST['status']==0)
{
	 $get_attach=$db->joinquery("SELECT attachment FROM  location_attachments WHERE attachmentid=".$_POST['attachmentid']."");
		$row_atatch=mysqli_fetch_array($get_attach);
	  //echo getattachment($_POST['attachmentid']);die();
	 unlink($gAttachmentDir.$row_atatch['attachment']); 
	 $db->del_rec("location_attachments","attachmentid='".$_POST['attachmentid']."'");
		$locationid=$_POST['locationid'];
}
else if($_POST['status'] ==1)
{ 
		  if(!empty($_FILES['file']['name']))
				{
					$temp = explode(".", $_FILES["file"]["name"]);
							$newfilename = $temp[0].time().".".$temp[1];
							move_uploaded_file($_FILES['file']['tmp_name'], $gAttachmentDir."/".$newfilename);
							$db->upd_rec('location_attachments',array('attachment'=>$newfilename),"attachmentid = '".$_POST['attachmentid']."'");
								$gte_loc=$db->joinquery('SELECT locationid FROM location_attachments WHERE attachmentid='.$_POST['attachmentid'].'');
							$row_loc=mysqli_fetch_array($gte_loc);
							$locationid=$row_loc['locationid'];
							
				}

}
else if($_POST['status']==2)
{ 
	 if(!empty($_FILES['file']['name']))
				{
					$flag =true;
					 $old_file=preg_replace('/\s/', '', $_FILES["file"]["name"]);
					  $temp = explode(".", $old_file);
						$folder = $gAttachmentDir.$_SESSION['agentid']."/";
					
						$foldersize=foldersize($folder);
						$foldercapacity= format_size($foldersize);
					  $exp_size = explode(" ",$foldercapacity);
				     if(($exp_size[1]=="GB") &&($exp_size[0]>10)){
					
							$flag = false;
						}
					
						if($flag == true){

							if(!file_exists($folder))
						  mkdir($gAttachmentDir."/" . $_SESSION['agentid'], 0777);
							$newfolder = $gAttachmentDir."/" . $_SESSION['agentid'];
							$newfilename = $temp[0].time().".".$temp[1];
							move_uploaded_file($_FILES['file']['tmp_name'], $newfolder."/".$newfilename);
							$db->ins_rec('location_attachments',array('attachment'=>$newfilename,'locationid'=>$_POST['locationid'],'agentid'=>$_SESSION['agentid']));
							
							$locationid=$_POST['locationid'];

						}
						else{

						echo $flag;exit();

						}

						
						
							
				}
}
	

		$get_attachments=$db->sel_rec("location_attachments", "*","locationid = '".$locationid."'",'datetime','desc');
		 if(mysqli_num_rows($get_attachments)>0){
																	  while($row_attach=mysqli_fetch_array($get_attachments))
																			{
																				 $attch_date=explode(" ",$row_attach['datetime']);
																				 $folder = $gAttachmentDir."/".$_SESSION['agentid']."/".$row_attach['attachment'];
																				 if(!file_exists($folder))
																				 $openurl = $gDownlaodurl . $row_attach['attachment'];
																				 else
																				 $openurl =$gDownlaodurl.$_SESSION['agentid']."/".$row_attach['attachment'];
																				 echo '<div class="attchment-single">
                    	<!--<div class="attch_icon"><i class="fa fa-file-pdf-o"></i></div>-->
                        <div class="attch_details">
                        	<h5><a href='.$openurl.' download ='.$row_attach['attachment'].'>'.$row_attach['attachment'].'</a></h5>
                            <h6> Added on &nbsp;'.date('M d',strtotime($row_attach['datetime'])).'&nbsp; at '.date('g:i a',strtotime($attch_date[1]) ).'</h6>
                        </div>
                        <div class="attch_btns">
																								<a href="javascript:void(0)" onclick="Down_del('.$row_attach['attachmentid'].','.$locationid.');">Delete</a>
                        	
                          <!-- 	<form method="post" class="data-upload" enctype="multipart/form-data">
                           <a href="javascript:void(0)" class="edit-file" >Edit</a>
																									
																												<input type="file" class="upload-file" id="'.$row_attach['attachmentid'].'">
																										
																												</form>-->
                        </div>
                    </div>';
																			}}else{
																				 echo 0;
																				  
																				}
						if($_POST['status']==2){														
						echo '@'.	mysqli_num_rows($get_attachments)."@".$locationid;}

	

						function foldersize($path) {
							$total_size = 0;
							$files = scandir($path);
						
							foreach($files as $t) {
								if (is_dir(rtrim($path, '/') . '/' . $t)) {
									if ($t<>"." && $t<>"..") {
											$size = foldersize(rtrim($path, '/') . '/' . $t);
						
											$total_size += $size;
									}
								} else {
									$size = filesize(rtrim($path, '/') . '/' . $t);
									$total_size += $size;
								}
							}
							return $total_size;
						}
						
						function format_size($size) {
							$mod = 1024;
							$units = explode(' ','B KB MB GB TB PB');
							for ($i = 0; $size > $mod; $i++) {
								$size /= $mod;
							}
						
							return round($size, 2) . ' ' . $units[$i];
						}
						
																	
?>



