<?php ob_start();
session_start();
date_default_timezone_set("NZ");
include('../includes/functions.php');
$cmmt_date=date('Y-m-d H:i:s');
$exp_time=explode(" ",$cmmt_date);
if($_POST['comment_id']!=0){
	$db->joinquery("DELETE FROM location_comments WHERE commentid='".$_POST['comment_id']."'");
		$commentid=$db->ins_rec("location_comments", array('locationid'=>$_POST['locationid'],'userid'=>$_SESSION['agentid'],'comments'=>$_POST['comment'],'datetime'=>date('Y-m-d H:i:s')));

	//$db->joinquery("UPDATE location_comments SET comments='".$_POST['comment']."' WHERE commentid='".$_POST['comment_id']."'");
}
else{
	$commentid=$db->ins_rec("location_comments", array('locationid'=>$_POST['locationid'],'userid'=>$_SESSION['agentid'],'comments'=>$_POST['comment'],'datetime'=>date('Y-m-d H:i:s')));
}
$get_attach_count=$db->joinquery("SELECT COUNT(*) AS total_cnt FROM location_comments WHERE locationid='".$_POST['locationid']."'");
$row=mysqli_fetch_array($get_attach_count);


 echo '<div class="recent-comments-single" id="div'.$commentid.'">
                        	<div class="recent-comment-user-image">
                            	<img src="https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y">
                            </div>
                            <div class="recent-comment-txt">
                            	<p> '.nl2br($_POST['comment']).'</p>
                                <ul>
																																	<li><a id="edit-comment" data-id="'.$commentid.'" href="javascript:void(0)">EDIT</a></li>
                                	<li><i class="fa fa-calendar"></i> '.date('d-m-Y',strtotime($exp_time[0])).'</li>
                                    <li><i class="fa fa-clock-o"></i> '.date('h:i a',strtotime($exp_time[1]) ).'</li>
                                </ul>
                            </div>
                        </div>';
																								echo '@'.$row['total_cnt'];
?>



