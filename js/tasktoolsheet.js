
$(document).ready(function(){
	
	$(".maindata").change(function(){
		
		$.ajax({
          type: "POST",
          url: "ajax/ajax_tasktool_worksheet.php",
          data: { status: 'settings', field: $(this).attr("id"), locationid: $('#locationid').val(),value:$('#' + $(this).attr("id")).val() },
          success: function (data) {


          },
     });
		
		
});

$(".toolcheck").change(function(){
	
	if($(this).prop("checked") == true)
	
	var check = 1;
	
	else
	
	var check =0;
	
		$.ajax({
          type: "POST",
          url: "ajax/ajax_tasktool_worksheet.php",
          data: { status: 'tools', check: check,toolid:$(this).attr("data-tool"), tasktoolid: $(this).attr("data-id"), locationid: $('#locationid').val(),category:$(this).attr("data-cat"),type:$(this).attr("data-type") },
          success: function (data) {
											
									
        
          },
     });
		
		
});

$(".matcheck").change(function(){
	
	if($(this).prop("checked") == true)
	
	var check = 1;
	
	else
	
	var check =0;
	
		$.ajax({
          type: "POST",
          url: "ajax/ajax_tasktool_worksheet.php",
          data: { status: 'materials', check: check,materialid: $(this).attr("data-id"), locationid: $('#locationid').val(),category:$(this).attr("data-cat"),type:$(this).attr("data-type") },
          success: function (data) {
											
									
        
          },
     });
		
		
});
  
});

