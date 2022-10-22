 <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="js/bootstrap-select.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.js"></script>
    <script src="js/background-color-theif.js"	type="text/javascript"></script>
    <script src="js/demo.js"></script>
    <script src="js/worksheet.js"></script>
    <script src="js/validate.js"></script>
    <script src="js/portal.js"></script>
    <script src="js/extras.js"></script>
    <script src="js/login.js"></script>
     <script src="js/team.js"></script>
     <script src="js/layer.js"></script>
      <script src="js/report.js"></script>
 <script src="js/jobsheet.js"></script>
 <script src="js/tasktoolsheet.js"></script>
    <script src="js/fs-gal.js"></script>
    <script src="js/jquery.datetimepicker.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

     <!-- graph-->

     <script src="https://code.highcharts.com/highcharts.js"></script>
     <script src="https://code.highcharts.com/modules/exporting.js"></script>
     <script src="https://code.highcharts.com/modules/export-data.js"></script>
     <!--<script src="https://code.jquery.com/jquery-1.12.4.js"></script>-->
     <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

     <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
     <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>

     <script type="text/javascript">
      $(document).ready(function() {
        $('#example').DataTable();
      });
     </script>
     <script>
        $( function() {
          $( "#datepicker").datepicker();
        });
     </script>


    <!-- <script>
  		$( ".column-drag" ).sortable({
  		  connectWith: ".column-drag",
  		  handle: ".portlet-header",
  		  cancel: ".portlet-toggle",
  		  start: function (event, ui) {
  			ui.item.addClass('tilt');
  		  },
  		  stop: function (event, ui) {
  			ui.item.removeClass('tilt');
  		  }
  		});
  	</script>-->

    <script>
      var incrementPlus;
      var incrementMinus;

      var buttonPlus  = $(".cart-qty-plus");
      var buttonMinus = $(".cart-qty-minus");

      var incrementPlus = buttonPlus.click(function() {
      	var $n = $(this)
      		.parent(".button-container")
      		.parent(".cont")
      		.find(".qty");
      	$n.val(Number($n.val())+1 );
      });

      var incrementMinus = buttonMinus.click(function() {
      		var $n = $(this)
      		.parent(".button-container")
      		.parent(".cont")
      		.find(".qty");
      	var amount = Number($n.val());
      	if (amount > 0) {
      		$n.val(amount-1);
      	}
      });
    </script>
<script>
Highcharts.chart('container', {
    chart: {
        type: 'line'
    },
    title: {
        text: 'Monthly Ratio'
    },
    subtitle: {
    },
    xAxis: {
       categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']

    },
    yAxis: {
        title: {
            text: 'Value'
        }
    },
    plotOptions: {
        line: {
            dataLabels: {
                enabled: true
            },
        },
		 series: {
            lineWidth: 1

        }
    },
    series: [{
        name: 'Pipeline',
		color: '#0098DB',
        data: [<?php echo $pipleline_arr;?>]
    }, {
        name: 'Production',
		color: '#ED3334',
        data: [<?php echo $production_arr;?>]
    }]
});
</script>
<style>
.highcharts-data-label text {
	font-weight: bold !important;
	font-size:16px !important;
}
</style>


     <!-- end graph-->


     <script>
    $('#datetimepicker_mask').datetimepicker({
        mask:'9999/19/39 29:59',
    });
    $('#datetimepicker').datetimepicker();
				 $('#datetimepickerend').datetimepicker();
    $('#datetimepicker').datetimepicker({value:'2019/04/15 05:06'});
    $('#datetimepicker1').datetimepicker({
					alert();
        datepicker:false,
        format:'H:i
',
        step:5
    });
    $('#datetimepicker2').datetimepicker({
        timepicker:false,
        format:'d/m/Y',
        formatDate:'Y/m/d',
        minDate:'-1970/01/02', // yesterday is minimum date
        maxDate:'+1970/01/02' // and tommorow is maximum date calendar
    });
    $('#datetimepicker3').datetimepicker({
        inline:true
    });
    $('#datetimepicker4').datetimepicker();
    $('#open').click(function(){
        $('#datetimepicker4').datetimepicker('show');
    });
    $('#close').click(function(){
        $('#datetimepicker4').datetimepicker('hide');
    });
    $('#datetimepicker5').datetimepicker({
        datepicker:false,
        allowTimes:['12:00','13:00','15:00','17:00','17:05','17:20','19:00','20:00']
    });
    $('#datetimepicker6').datetimepicker();
    $('#destroy').click(function(){
        if( $('#datetimepicker6').data('xdsoft_datetimepicker') ){
            $('#datetimepicker6').datetimepicker('destroy');
            this.value = 'create';
        }else{
            $('#datetimepicker6').datetimepicker();
            this.value = 'destroy';
        }
    });
    </script>

    <script>
      $('.click-tab').click(function(){
        $('#edit-team-member').trigger('click');
        });
    </script>



    <!-- Owl carousel -->
    <script src="js/owl.carousel.js"></script>
    <script>

      $('#week-slider').owlCarousel({

        margin: 3,
        nav: true,
        dots: false,
        loop: false,
        autoplay: false,
        autoHeight:true,
        responsiveClass: true,
        responsive: {
            0:{
                items:3,
            },
            600:{
                items:4,
            },
            1000:{
                items:7,
            }
        }
      });

	 <?php

	$link = $_SERVER['PHP_SELF'];
    $link_array = explode('/',$link);
    $page = end($link_array);
    if($page == 'report.php'){?>

	$( document ).ready(function() {

     $('#week-slider').trigger('to.owl.carousel', <?php echo $weeknumber;?>);
	 $('#week-slider2').trigger('to.owl.carousel', <?php echo $weeknumber;?>);
     $('#week-slider4').trigger('to.owl.carousel', <?php echo $weeknumber;?>);
});


    </script>

<?php } ?>

    <script>
      $('#week-slider2').owlCarousel({
        margin: 3,
        nav: true,
        dots: false,
        loop: false,
        autoplay: false,
        autoHeight:true,
        responsiveClass: true,
        responsive: {
            0:{
                items:3,
            },
            600:{
                items:4,
            },
            1000:{
                items:7,
            }
        }
      });
      $('#week-slider4').owlCarousel({
        margin: 3,
        nav: true,
        dots: false,
        loop: false,
        autoplay: false,
        autoHeight:true,
        responsiveClass: true,
        responsive: {
            0:{
                items:3,
            },
            600:{
                items:4,
            },
            1000:{
                items:7,
            }
        }
      });
    </script>

  </body>
</html>
