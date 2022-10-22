<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--<meta http-equiv="refresh" content="180;url=logout.php" />-->
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>EVS</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- Google font -->
    <link href="https://fonts.googleapis.com/css?family=Lato:400,700&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <!-- Owl carousel -->
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">
    
    <!-- Custom style -->
    <link href="css/styles.css" rel="stylesheet">
    <link href="css/fs-gal.css" rel="stylesheet">
    <link rel="stylesheet" href="css/jquery.datetimepicker.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.css">
    <!--graph-->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <!--end graph-->

    <link href="css/bootstrap-select.css" rel="stylesheet">
<!--Graph piec chrt-->
  <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
    <!--<script type="text/javascript">
   function idleTimer() {
    var t;

    //window.onload = resetTimer;
    window.onmousemove = resetTimer; // catches mouse movements
    window.onmousedown = resetTimer; // catches mouse movements
    window.onclick = resetTimer;     // catches mouse clicks
    window.onscroll = resetTimer;    // catches scrolling
    window.onkeypress = resetTimer;  //catches keyboard actions

    function logout() {

								  var log_time= (Date.now() - 600000);

						if((localStorage.getItem("currentTime")) <= log_time){

        window.location.href = 'logout.php';
						 }//Adapt to actual logout script

    }

   function reload() {
          window.location = self.location.href;  //Reloads the current page
   }

   function resetTimer() {
        clearTimeout(t);
									var currentTime         = Date.now();
								localStorage.setItem("currentTime", currentTime);
						console.log(currentTime);
				//	console.log(log_time);
        t = setTimeout(logout, 600000);  // time is in milliseconds (1000 is 1 second)
    }
}

idleTimer();
</script>-->


<!-- Full calendar --->

<link href='/assets/demo-to-codepen.css' rel='stylesheet' />
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href='https://unpkg.com/fullcalendar@5.1.0/main.min.css' rel='stylesheet' />
<script src='https://unpkg.com/fullcalendar@5.1.0/main.min.js'></script>
<script src='/assets/demo-to-codepen.js'></script>



<!-- End calendar-->



  </head>

  <?php
