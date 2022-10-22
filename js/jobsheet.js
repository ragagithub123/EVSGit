$('.job_head').change(function(){
	var current_id = this.id;
		
	 $.ajax({
          type: "POST",
          url: "ajax/ajax_job_worksheet.php",
          data: { status: 'settings', field: current_id, locationid: $('#locationid').val(),value:$('#' + current_id).val(),type:$('#worksheet_type').val() },
          success: function (data) {


          },
     });
});

$("#seals_btn_job").change(function () {

     var checked = $(this).is(':checked'); // Checkbox state
     var panels = [];
     var rooms = [];
     // Select all
     if (checked) {

          var checked_status = 1;

          $(".cls_seals").prop("checked", true);

          $.each($("input[name='check_seals']:checked"), function () {

               panels.push($(this).val());
               rooms.push($(this).attr("data-id"));

          });


     }
     else {

          var checked_status = 0;
          $.each($("input[name='check_seals']:checked"), function () {

               panels.push($(this).val());
               rooms.push($(this).attr("data-id"));

          });
          $(".cls_seals").prop("checked", false);


     }


     roomid = rooms.join();

     panelid = panels.join();

     $.ajax({
          type: "POST",
          url: "ajax/ajax_job_worksheet.php",
          data: { status: 'workflow_array', type:$('#worksheet_type').val(), roomid: roomid, panelid: panelid, locationid: $('#locationid').val(), checked: checked_status },
          success: function (data) {


          },
     });

});

function workflow_worksheet(id, type, roomid, panelid) {

     if (document.getElementById(id).checked == true) {

          var checked = 1;

     }
     else {

          var checked = 0;
     }
     $.ajax({
          type: "POST",
          url: "ajax/ajax_job_worksheet.php",
          data: { status: 'workflow', type: $('#worksheet_type').val(), roomid: roomid, panelid: panelid, locationid: $('#locationid').val(), checked: checked },
          success: function (data) {


          },
     });
}

$(document).on("change", ".sealsDate", function () {
	

     var seals = [];

     var curent_id = $(this).attr("data-id");

     if (document.getElementById('checkA' + curent_id).checked == true) {

          date = $('#today_date').val();
										
						
   
     $.each($("input[name='check_seals']:checked"), function () {

          seals.push($(this).val());
     });

     }

     else {

          date = '0000-00-00';
     }

    


     $('#td_A' + curent_id).html(date);


     $.ajax({
          type: "POST",
          url: "ajax/ajax_job_worksheet.php",
          data: { status: 'worksheet', week: curent_id, date: date, seals: (seals.length), locationid: $('#locationid').val(),type:$('#worksheet_type').val() },
          success: function (data) {

               

               var obj = JSON.parse(data);

              
               $('#sealsA' + curent_id).html(obj.seals);
              
               $('#tot_seals').html(obj.totseals);
              
               //$("#checkA").attr("disabled", true);

          },
     });
});

$(document).on("change", ".seals_Date", function () {
	

     var seals = [];
     var curent_id = $(this).attr("data-id");
     var newdate = formatDate($('#date_A' + curent_id).val());

     var date = replacedateform($('#date_A' + curent_id).val());


    
     $.each($("input[name='check_seals']:checked"), function () {

          seals.push($(this).val());
     });
     $('#checkA' + curent_id).prop('checked', true);
     $('#td_A' + curent_id).html(newdate);


    
     $('#sealsA' + curent_id).html(seals.length);
     $.ajax({
          type: "POST",
          url: "ajax/ajax_job_worksheet.php",
          data: { status: 'worksheet', week: curent_id, date: date, seals: (seals.length), locationid: $('#locationid').val(),type:$('#worksheet_type').val() },
          success: function (data) {

               var obj = JSON.parse(data);

              
               $('#sealsA' + curent_id).html(obj.seals);
              
               $('#tot_seals').html(obj.totseals);
             


          },
     });
});

var clickcnt_sheet = 0;
$('.jobsheetbtn').click(function () {

     clickcnt_sheet++;

     var cnt = parseInt($('#jcount').val()) + parseInt(clickcnt_sheet);


     $('#myTablejob tr:last').after('<tr> <td colspan="8"><div class="pull-right"><input type="checkbox" class="checkA" data-id="' + cnt + '" id="checkA' + cnt + '"></div></td> <td class="calendar-tble calendar-pos"><span id="td_A' + cnt + '">00-00-0000</span> <input type="date" id="date_A' + cnt + '" class="date_A" data-id="' + cnt + '"/> </td><td style="text-align: center;" id="sealsA' + cnt + '"></td></tr>');

});

$('#select_worksheet').change(function(){
	
	var querystring = window.location.search;
	
	
	if($('#select_worksheet').val() == 'job'){
		
		window.location.href="jobsheet.php"+querystring;
		
	}
	if($('#select_worksheet').val() == 'measure'){
		
		window.location.href="measuresheet.php"+querystring;
		
	}
		if($('#select_worksheet').val() == 'production'){
		
		window.location.href="worksheet.php"+querystring;
		
	}
		if($('#select_worksheet').val() == 'task'){
		
		window.location.href="tasktoolsheet.php"+querystring;
		
	}
	
});

function formatDate(input) {
     var datePart = input.match(/\d+/g),
          year = datePart[0] // get only two digits
     month = datePart[1], day = datePart[2];

     return day + '-' + month + '-' + year;
}
function replacedateform(input) {
     var datePart = input.match(/\d+/g),
          year = datePart[0] // get only two digits
     month = datePart[1], day = datePart[2];

     return year + '-' + month + '-' + day;

}