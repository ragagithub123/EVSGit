<?php ob_start();
session_start();
include('../includes/functions.php');
	if(!empty($_FILES['file']['name']))
	{
			$image_info = getimagesize($_FILES["file"]["tmp_name"]);
			
		if(!empty($image_info[0])&& !empty($image_info[1]))
		{
				$image_data = array(
				'width'	 =>$image_info[0],
				'height'	 => $image_info[1],
				
			);
			
				$photoid = $db->ins_rec("photo", $image_data);	
			
				$temp = explode(".", $_FILES["file"]["name"]);
				$newfilename = $photoid.".jpg" ;
				move_uploaded_file($_FILES['file']['tmp_name'], $gPhotoDir."/".$newfilename);
				if($_POST['status']=='before'){
					 		$db->ins_rec('window_photo',array('photoid'=>$photoid,'windowid'=>$_POST['windowid']));

				}
				else{
					$db->ins_rec('window_after_photo',array('photoid'=>$photoid,'windowid'=>$_POST['windowid']));
				}
			
		}
	
			
				
}



?>
<li> <img src="<?php echo $gPhotoURL.$photoid;?>.jpg" class="fs-gal" data-url="<?php echo $gPhotoURL.$photoid;?>.jpg"> </li>
