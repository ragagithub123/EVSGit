$('.task_date').change(function () {
     var current_id = this.id;
     var id = current_id.split('-');
     var newdate = formatDate($('#' + current_id).val());
     var date = replacedateform($('#' + current_id).val());
     $('#' + id[3]).prop('checked', true);
     $('#' + id[1]).html(newdate);
     $.ajax({
          type: "POST",
          url: "ajax/ajax_worksheet.php",
          data: { status: 'datesettings', date: date, field: id[2], locationid: $('#locationid').val() },
          success: function (data) {



          },
     });

});
$("#quote_check").change(function () {

     if (document.getElementById('quote_check').checked == true) {

          date = $('#today_date').val();
     }
     else {

          date = '0000-00-00';
     }
     $('#td_quote').html(date);
     $.ajax({
          type: "POST",
          url: "ajax/ajax_worksheet.php",
          data: { status: 'datesettings', date: date, field: 'quoted_date', locationid: $('#locationid').val() },
          success: function (data) {

               //$("#quote_check").attr("disabled", true);

          },
     });
});


$("#measured_check").change(function () {

     if (document.getElementById('measured_check').checked == true) {

          date = $('#today_date').val();
     }
     else {

          date = '0000-00-00';
     }
     $('#td_measured').html(date);
     $.ajax({
          type: "POST",
          url: "ajax/ajax_worksheet.php",
          data: { status: 'datesettings', date: date, field: 'check_measured', locationid: $('#locationid').val() },
          success: function (data) {

               //$("#measured_check").attr("disabled", true);

          },
     });
});

$("#ordered_check").change(function () {
     if (document.getElementById('ordered_check').checked == true) {

          date = $('#today_date').val();
     }
     else {

          date = '0000-00-00';
     }
     $('#td_ordered').html(date);
     $.ajax({
          type: "POST",
          url: "ajax/ajax_worksheet.php",
          data: { status: 'datesettings', date: date, field: 'glass_ordered', locationid: $('#locationid').val() },
          success: function (data) {
               //$("#ordered_check").attr("disabled", true);

          },
     });
});

$("#received_check").change(function () {
     if (document.getElementById('received_check').checked == true) {

          date = $('#today_date').val();
     }
     else {

          date = '0000-00-00';
     }
     $('#td_received').html(date);
     $.ajax({
          type: "POST",
          url: "ajax/ajax_worksheet.php",
          data: { status: 'datesettings', date: date, field: 'glass_received', locationid: $('#locationid').val() },
          success: function (data) {
               //$("#received_check").attr("disabled", true);

          },
     });
});

$("#finished_check").change(function () {
     if (document.getElementById('finished_check').checked == true) {

          date = $('#today_date').val();
     }
     else {

          date = '0000-00-00';
     }
     $('#td_finished').html(date);
     $.ajax({
          type: "POST",
          url: "ajax/ajax_worksheet.php",
          data: { status: 'datesettings', date: date, field: 'job_finished', locationid: $('#locationid').val() },
          success: function (data) {
               //$("#finished_check").attr("disabled", true);

          },
     });
});

$("#job_check").change(function () {
     if (document.getElementById('job_check').checked == true) {

          date = $('#today_date').val();
     }
     else {

          date = '0000-00-00';
     }
     $('#td_jobcheck').html(date);
     $.ajax({
          type: "POST",
          url: "ajax/ajax_worksheet.php",
          data: { status: 'datesettings', date: date, field: 'job_checked', locationid: $('#locationid').val() },
          success: function (data) {
               //$("#job_check").attr("disabled", true);

          },
     });
});

$("#job_invoiced").change(function () {
     if (document.getElementById('job_invoiced').checked == true) {

          date = $('#today_date').val();
     }
     else {

          date = '0000-00-00';
     }
     $('#td_invoiced').html(date);
     $.ajax({
          type: "POST",
          url: "ajax/ajax_worksheet.php",
          data: { status: 'datesettings', date: date, field: 'job_invoiced', locationid: $('#locationid').val() },
          success: function (data) {
               //$("#job_invoiced").attr("disabled", true);

          },
     });
});



$(document).on("change", ".checkA", function () {

     var glass = [];
     var kit = [];
     var assemble = [];
     var prep = [];
     var install = [];
     var seals = [];

     var curent_id = $(this).attr("data-id");

     if (document.getElementById('checkA' + curent_id).checked == true) {

          date = $('#today_date').val();
										
									$.each($("input[name='check_glass']:checked"), function () {

          glass.push($(this).val());
     });
     $.each($("input[name='check_kit']:checked"), function () {

          kit.push($(this).val());
     });
     $.each($("input[name='check_assemble']:checked"), function () {

          assemble.push($(this).val());
     });
     $.each($("input[name='check_prep']:checked"), function () {

          prep.push($(this).val());
     });
     $.each($("input[name='check_install']:checked"), function () {

          install.push($(this).val());
     });
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
          url: "ajax/ajax_worksheet.php",
          data: { status: 'worksheet', week: curent_id, date: date, glass: (glass.length), kit: (kit.length), assemble: (assemble.length), seals: (seals.length), install: (install.length), prep: (prep.length), locationid: $('#locationid').val() },
          success: function (data) {



               var obj = JSON.parse(data);

               $('#glassA' + curent_id).html(obj.glass);
               $('#kitA' + curent_id).html(obj.kit);
               $('#assembleA' + curent_id).html(obj.assemble);
               $('#prepA' + curent_id).html(obj.prep);
               $('#installA' + curent_id).html(obj.install);
               $('#sealsA' + curent_id).html(obj.seals);
               $('#tot_glass').html(obj.totglass);
               $('#tot_kit').html(obj.totkit);
               $('#tot_assemble').html(obj.totassemble);
               $('#tot_prep').html(obj.totprep);
               $('#tot_install').html(obj.totinstall);
               $('#tot_seals').html(obj.totseals);
               $('#job_complete').html(obj.jobcomplete);

               //$("#checkA").attr("disabled", true);

          },
     });
});

function workflow(id, type, roomid, panelid) {
     if (document.getElementById(id).checked == true) {

          var checked = 1;

     }
     else {

          var checked = 0;
     }
     $.ajax({
          type: "POST",
          url: "ajax/ajax_worksheet.php",
          data: { status: 'workflow', field: type, roomid: roomid, panelid: panelid, locationid: $('#locationid').val(), checked: checked },
          success: function (data) {


          },
     });
}


$(document).on("change", ".date_A", function () {

     var glass = [];
     var kit = [];
     var assemble = [];
     var prep = [];
     var install = [];
     var seals = [];
     var curent_id = $(this).attr("data-id");
     var newdate = formatDate($('#date_A' + curent_id).val());

     var date = replacedateform($('#date_A' + curent_id).val());


     $.each($("input[name='check_glass']:checked"), function () {

          glass.push($(this).val());
     });
     $.each($("input[name='check_kit']:checked"), function () {

          kit.push($(this).val());
     });
     $.each($("input[name='check_assemble']:checked"), function () {

          assemble.push($(this).val());
     });
     $.each($("input[name='check_prep']:checked"), function () {

          prep.push($(this).val());
     });
     $.each($("input[name='check_install']:checked"), function () {

          install.push($(this).val());
     });
     $.each($("input[name='check_seals']:checked"), function () {

          seals.push($(this).val());
     });
     $('#checkA' + curent_id).prop('checked', true);
     $('#td_A' + curent_id).html(newdate);


     $('#glassA' + curent_id).html(glass.length);
     $('#kitA' + curent_id).html(kit.length);
     $('#assembleA' + curent_id).html(assemble.length);
     $('#prepA' + curent_id).html(prep.length);
     $('#installA' + curent_id).html(install.length);
     $('#sealsA' + curent_id).html(seals.length);
     $.ajax({
          type: "POST",
          url: "ajax/ajax_worksheet.php",
          data: { status: 'worksheet', week: curent_id, date: date, glass: (glass.length), kit: (kit.length), assemble: (assemble.length), seals: (seals.length), install: (install.length), prep: (prep.length), locationid: $('#locationid').val() },
          success: function (data) {

               var obj = JSON.parse(data);

               $('#glassA' + curent_id).html(obj.glass);
               $('#kitA' + curent_id).html(obj.kit);
               $('#assembleA' + curent_id).html(obj.assemble);
               $('#prepA' + curent_id).html(obj.prep);
               $('#installA' + curent_id).html(obj.install);
               $('#sealsA' + curent_id).html(obj.seals);
               $('#tot_glass').html(obj.totglass);
               $('#tot_kit').html(obj.totkit);
               $('#tot_assemble').html(obj.totassemble);
               $('#tot_prep').html(obj.totprep);
               $('#tot_install').html(obj.totinstall);
               $('#tot_seals').html(obj.totseals);
               $('#job_complete').html(obj.jobcomplete);


          },
     });
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
$("#glass_btn").change(function () {

     var checked = $(this).is(':checked'); // Checkbox state
     var panels = [];
     var rooms = [];
     // Select all
     if (checked) {

          var checked_status = 1;

          $(".cls_glass").prop("checked", true);

          $.each($("input[name='check_glass']:checked"), function () {

               panels.push($(this).val());
               rooms.push($(this).attr("data-id"));

          });


     }
     else {

          var checked_status = 0;
          $.each($("input[name='check_glass']:checked"), function () {

               panels.push($(this).val());
               rooms.push($(this).attr("data-id"));

          });
          $(".cls_glass").prop("checked", false);


     }


     roomid = rooms.join();

     panelid = panels.join();


     $.ajax({
          type: "POST",
          url: "ajax/ajax_worksheet.php",
          data: { status: 'workflow_array', field: 'glass', roomid: roomid, panelid: panelid, locationid: $('#locationid').val(), checked: checked_status },
          success: function (data) {


          },
     });

});


$("#kit_btn").change(function () {

     var checked = $(this).is(':checked'); // Checkbox state
     var panels = [];
     var rooms = [];
     // Select all
     if (checked) {

          var checked_status = 1;

          $(".cls_kit").prop("checked", true);

          $.each($("input[name='check_kit']:checked"), function () {

               panels.push($(this).val());
               rooms.push($(this).attr("data-id"));

          });


     }
     else {

          var checked_status = 0;
          $.each($("input[name='check_kit']:checked"), function () {

               panels.push($(this).val());
               rooms.push($(this).attr("data-id"));

          });
          $(".cls_kit").prop("checked", false);


     }


     roomid = rooms.join();

     panelid = panels.join();


     $.ajax({
          type: "POST",
          url: "ajax/ajax_worksheet.php",
          data: { status: 'workflow_array', field: 'kit', roomid: roomid, panelid: panelid, locationid: $('#locationid').val(), checked: checked_status },
          success: function (data) {


          },
     });

});


$("#assemble_btn").change(function () {

     var checked = $(this).is(':checked'); // Checkbox state
     var panels = [];
     var rooms = [];
     // Select all
     if (checked) {

          var checked_status = 1;

          $(".cls_assemble").prop("checked", true);

          $.each($("input[name='check_assemble']:checked"), function () {

               panels.push($(this).val());
               rooms.push($(this).attr("data-id"));

          });


     }
     else {

          var checked_status = 0;
          $.each($("input[name='check_assemble']:checked"), function () {

               panels.push($(this).val());
               rooms.push($(this).attr("data-id"));

          });
          $(".cls_assemble").prop("checked", false);


     }


     roomid = rooms.join();

     panelid = panels.join();


     $.ajax({
          type: "POST",
          url: "ajax/ajax_worksheet.php",
          data: { status: 'workflow_array', field: 'assemble', roomid: roomid, panelid: panelid, locationid: $('#locationid').val(), checked: checked_status },
          success: function (data) {


          },
     });

});

$("#prep_btn").change(function () {

     var checked = $(this).is(':checked'); // Checkbox state
     var panels = [];
     var rooms = [];
     // Select all
     if (checked) {

          var checked_status = 1;

          $(".cls_prep").prop("checked", true);

          $.each($("input[name='check_prep']:checked"), function () {

               panels.push($(this).val());
               rooms.push($(this).attr("data-id"));

          });


     }
     else {

          var checked_status = 0;
          $.each($("input[name='check_prep']:checked"), function () {

               panels.push($(this).val());
               rooms.push($(this).attr("data-id"));

          });
          $(".cls_prep").prop("checked", false);


     }


     roomid = rooms.join();

     panelid = panels.join();


     $.ajax({
          type: "POST",
          url: "ajax/ajax_worksheet.php",
          data: { status: 'workflow_array', field: 'prep', roomid: roomid, panelid: panelid, locationid: $('#locationid').val(), checked: checked_status },
          success: function (data) {


          },
     });

});


$("#install_btn").change(function () {

     var checked = $(this).is(':checked'); // Checkbox state
     var panels = [];
     var rooms = [];
     // Select all
     if (checked) {

          var checked_status = 1;

          $(".cls_install").prop("checked", true);

          $.each($("input[name='check_install']:checked"), function () {

               panels.push($(this).val());
               rooms.push($(this).attr("data-id"));

          });


     }
     else {

          var checked_status = 0;
          $.each($("input[name='check_install']:checked"), function () {

               panels.push($(this).val());
               rooms.push($(this).attr("data-id"));

          });
          $(".cls_install").prop("checked", false);


     }


     roomid = rooms.join();

     panelid = panels.join();


     $.ajax({
          type: "POST",
          url: "ajax/ajax_worksheet.php",
          data: { status: 'workflow_array', field: 'install', roomid: roomid, panelid: panelid, locationid: $('#locationid').val(), checked: checked_status },
          success: function (data) {


          },
     });

});



$("#seals_btn").change(function () {

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
          url: "ajax/ajax_worksheet.php",
          data: { status: 'workflow_array', field: 'seals', roomid: roomid, panelid: panelid, locationid: $('#locationid').val(), checked: checked_status },
          success: function (data) {


          },
     });

});
var clickcnt = 0;
$('.add-btn-table').click(function () {

     clickcnt++;

     var cnt = parseInt($('#count').val()) + parseInt(clickcnt);


     $('#myTable tr:last').after('<tr> <td colspan="8"><div class="pull-right"><input type="checkbox" class="checkA" data-id="' + cnt + '" id="checkA' + cnt + '"></div></td> <td class="calendar-tble calendar-pos"><span id="td_A' + cnt + '">00-00-0000</span> <input type="date" id="date_A' + cnt + '" class="date_A" data-id="' + cnt + '"/> </td><td style="text-align: center;" id="glassA' + cnt + '"></td><td style="text-align: center;" id="kitA' + cnt + '"></td><td style="text-align: center;" id="assembleA' + cnt + '"></td><td style="text-align: center;" id="prepA' + cnt + '"></td> <td style="text-align: center;" id="installA' + cnt + '"></td><td style="text-align: center;" id="sealsA' + cnt + '"></td></tr>');

});





