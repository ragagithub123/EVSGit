<?php session_start();
/*if((time() - $_SESSION['timestamp']) > 600)
{
		header('Location:logout.php');
}
else{
	$_SESSION['timestamp']=time();
}*/

if(!isset($_SESSION['agentid'])){
		header('Location:index.php');}


    $link = $_SERVER['PHP_SELF'];
    $link_array = explode('/',$link);
    $page = end($link_array);
		if(isset($_POST['search'])){
		$value=$_POST['search-text'];
		}
		else if(isset($_REQUEST['id'])){
			$getsearchtext=$db->joinquery("SELECT locationSearch FROM location WHERE locationid='".base64_decode($_REQUEST['id'])."'");
			$row_text=mysqli_fetch_array($getsearchtext);
			$value=$row_text['locationSearch'];

		}
		else{
		 $value="";
		}

?>
<div class="evs_user">
                        	<ul>

                         <li class="search">
                         <!-- <form method="post" action="" id="search_form">-->
                         			<div class="search_head" style="display:flex;">

                            	<input type="text" name="search-text" id="search-text" list="list-location" class="form-control" value="<?php echo $value;?>" autocomplete="off" placeholder="Search your property"/>
                             <button id="search" value="search" name="search"><i class="glyphicon glyphicon-search"></i></button>
                             <datalist id="list-location">
                             <?php
																													for($i=0;$i<count($postLocation);$i++){
																														echo ' <option value="'.$postLocation[$i].'">';
																													}?>

  </datalist>
                            </div>
                           <!-- </form>-->
                         </li>
                         <?php

																									if($page == "customer-portal.php")
																									$redirect ="customer-portal.php";
																									else if($page == "manage-portal.php")
																									$redirect ="manage-portal.php";
																									else
																									$redirect ="customer-portal.php";
																									?>

                        <li><a href="<?php echo $redirect;?>" <?php if(($page == "customer-portal.php") || ($page == "manage-portal.php")){?>class="active"<?php } ?>>Customers</a></li>
                         <li><a href="customer-booked.php" <?php if($page == "customer-booked.php"){?>class="active"<?php } ?>>Bookings</a></li>
                            	<!--<li><a href="manage-portal.php" <?php //if($page == "manage-portal.php"){?>class="active"<?php //} ?>>Projects</a></li>-->

                        <li><a href="cutting-list.php" <?php if($page == "cutting-list.php"){?>class="active"<?php } ?>>Cutting Lists</a></li>

                         <li><a href="report.php" <?php if($page == "report.php"){?>class="active"<?php } ?>>Reports</a></li>

                               <!-- <li><a href="pipeline-graph.php" <?php //if($page == "pipeline-graph.php"){?>class="active"<?php //} ?>>Pipeline</a></li>-->

                               <li><a href="agent-settings.php" <?php if($page == "agent-settings.php"){?>class="active"<?php } ?>>Settings</a></li>

                           <li><a href="product-settings.php" <?php if($page == "product-settings.php"){?>class="active"<?php } ?>>Products</a></li>



                                <li class="dropdown">
                                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Account <span class="caret"></span></button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li><a href="logout.php"> Logout</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
