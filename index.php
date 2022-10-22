<?php 
include('templates/header.php');
?>
<body style="background-image:url('images/EVS_wallpaper.png'); background-size:cover">
      <div class="login ">
        <img src="images/logo.png">  
      <form method="post" action="" id="login">
        
        <div class="form-group">
            <input type="text" class="form-control" placeholder="Username" name="username" id="username">
        </div>
        <div class="form-group">
            <input type="password" class="form-control" placeholder="Password" name="password" id="password">
        </div>
        <div class="form-group">
            <div class="forg_pass">
               <!-- <a href="#">Forgot password</a>-->
            </div>
        </div>
        <div class="form-group">
            <input type="button" value="Sign in" class="btn sign_btn" value="submit" id="submit" style="outline:none">
        </div>
        <div class="form-group">
            <div class="new_cus" id="msg_succ" style="color:#F00;">
               <!-- New Customer? <a href="#">Sign up</a>-->
            </div>
        </div>
     </form>
    </div>
    
  <?php include('templates/footer.php');?>  

   