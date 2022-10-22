
$(document).ready(function(){
  $(".addtoll").click(function(){
			
			$('#tool-id').val($(this).attr("data-id"));
			
			
  });
		$(".tasktool").click(function(){
			$('#tasktoolid').val($(this).attr("data-id"));
			$.ajax({
            type: "POST",
            url: "savetasktool.php",
            data: {toolid:$(this).attr("data-id"),savetype:'ediittool'},
            success: function (data) {
												
												$('#texttool').val(data);
		
            },
        });
			
			
			
  });
		
		$("#updatetool").click(function(){
			
			if($('#texttool').val() == '')
			
			$('#texttool').css('border','#F00');
			
			else{
			
			$.ajax({
            type: "POST",
            url: "savetasktool.php",
            data: {toolid:$('#tasktoolid').val(),tool:$('#texttool').val(),savetype:'updatetool'},
            success: function (data) {
											location.reload();
		
            },
        });
			
			}
			
  });
		
		$("#deletetool").click(function(){
			
			$.ajax({
            type: "POST",
            url: "savetasktool.php",
            data: {toolid:$('#tasktoolid').val(),savetype:'deletetool'},
            success: function (data) {
											 location.reload();
		
            },
        });
			
			
			
  });
		
			$(".materiallink").click(function(){
				
				$('#materialid').val($(this).attr("data-id"));
			
			$.ajax({
            type: "POST",
            url: "savetasktool.php",
            data: {materialid:$(this).attr("data-id"),savetype:'materiallist'},
            success: function (data) {
												$('#textmaterial').val(data);
		
            },
        });
			
			
			
  });
		
		$("#updatemat").click(function(){
			
			if($('#textmaterial').val() =='')
			
			$('#textmaterial').css('border','#F00');
			
			else{
				
			$.ajax({
            type: "POST",
            url: "savetasktool.php",
            data: {materialid:$('#materialid').val(),material:$('#textmaterial').val(),savetype:'updatematerial'},
            success: function (data) {
											  location.reload();
		
            },
        });
			
			}
			
  });
		
		$("#deletemat").click(function(){
				
			$.ajax({
            type: "POST",
            url: "savetasktool.php",
            data: {materialid:$('#materialid').val(),savetype:'deletematerial'},
            success: function (data) {
											  location.reload();
		
            },
        });
			
			
			
  });
		
		$("#copybtn").click(function(){
				
			$.ajax({
            type: "POST",
            url: "savetasktool.php",
            data: {cateid:$('#categorycopy').val(),categroy:$('#category').val(),type:$("input[name='chooseradio']:checked").val(),choosedtype:$('#type').val(),savetype:'copytoolmat'},
            success: function (data) {
											  location.reload();
		
            },
        });
			
			
			
  });
		
		copybtn
		
		
		
});
$(document).ready(function(){
  $("#btntool").click(function(){
			
			if($('#text-tool').val()==''){
			
			$('#text-tool').css('border','#F00');
			
			alert($('#category').val());
			alert($('#type').val());
			alert($('#tool-id').val());
			}
			
			else{
			$.ajax({
            type: "POST",
            url: "savetasktool.php",
            data: {categroy:$('#category').val(),tool:$('#tool-id').val(),type:$('#type').val(),toolname:$('#text-tool').val(),savetype:'tool'},
            success: function (data) {
													location.reload();
		
            },
        });
			}
			
  });
});

$(document).ready(function(){
  $("#btnmaterial").click(function(){
			
			if($('#text-material').val()=='')
			
			$('#text-material').css('border','#F00');
			
			else{
			
			$.ajax({
            type: "POST",
            url: "savetasktool.php",
            data: {categroy:$('#category').val(),type:$('#type').val(),materilname:$('#text-material').val(),savetype:'material'},
            success: function (data) {
													location.reload();
		
            },
        });
			}
			
  });
		
		
	
});


