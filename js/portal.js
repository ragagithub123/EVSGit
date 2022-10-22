$('#recalcualte').click(function(){
	
	location.reload();
	
});
$("#location").change(function () {
	$.ajax({
		type: "POST",
		url: "ajax/ajax_list_properties.php",
		data: { city: $('#location').val(), statusCode: 0, locationstatusid: $('#quote_status').val() },
		success: function (data) {
			//alert(data);
			$('#list-prop').hide();
			$('#result-prop').html(data);
		},
	});
});

function getwindowList(windowid,type){
	$('#oldwindowid').val(windowid);
	$('#movingtype').val(type);

	$('#movebefore').modal('show');


}
function selectwindow(id){

	$.ajax({
		type: "POST",
		url: "ajax/ajax_move_photos.php",
		data: { roomid: id, statusCode: 0},
		success: function (data) {
			//alert(data);
			
			$('#list-window-select').html(data);
		},
	});
}

function moveBeforephotos(){

	if($('#windowname').val()=="")
	alert("Please choose any room");
 else{

	$.ajax({
		type: "POST",
		url: "ajax/ajax_move_photos.php",
		data: { type:$('#movingtype').val(),roomid: $('#movephoto').val(), windowid:$('#windowname').val(),oldwindowid:$('#oldwindowid').val(),statusCode: 1},
		success: function (data) {
			location.reload();
			
		
		},
	});

 }

}

function moveWindow(windowid){

	$('#movewindowid').val(windowid);

	$('#moveWindow').modal('show');


}
function updatewindow(){
	if($('#roomnamemove').val()=="")
	alert("Please choose any room");
	else{

		$.ajax({
			type: "POST",
			url: "ajax/ajax_move_room.php",
			data: { windowid:$('#movewindowid').val(), roomid: $('#roomnamemove').val()},
			success: function (data) {
			location.reload();
			},
		});

	}

}

function editroom(id,roomname){
	
	$('#roomEdit').val(roomname);
	$('#roomidtxt').val(id);
	$('#EditRoom').modal('show');
}
function updateroom(){
	$.ajax({
		type: "POST",
		url: "ajax/ajax_update_room.php",
		data: { roomname: $('#roomEdit').val(), roomid: $('#roomidtxt').val()},
		success: function (data) {
		location.reload();
		},
	});
}

$("#downloaddata").click(function () {

	if ($('#search-text').val() == "") {
		$('#search-text').css('border-color', 'red');
		event.preventDefault();
	}

	else {

		$('.loader3').show();

		$.ajax({
			type: "POST",
			url: "ajax/ajax_report.php",
			data: { locationtext: $('#search-text').val(), list_value: $('#list').val(), Download: 1 },
			success: function (data) {

				$('.loader3').hide();

				var downloadLink = document.createElement("a");
				var fileData = ['\ufeff' + data];

				var blobObject = new Blob(fileData, {
					type: "text/csv;charset=utf-8;"
				});

				var url = URL.createObjectURL(blobObject);
				downloadLink.href = url;
				downloadLink.download = $('#search-text').val() + ".csv";

				/*
				 * Actually download CSV
				 */
				document.body.appendChild(downloadLink);
				downloadLink.click();
				document.body.removeChild(downloadLink);


			},
		});


	}




});


$("#downloadxml").click(function () {

	if ($('#search-text').val() == "") {
		$('#search-text').css('border-color', 'red');
		event.preventDefault();
	}

	else {

		$('.loader3').show();

		$.ajax({
			type: "POST",
			url: "ajax/ajax_xml.php",
			data: { locationtext: $('#search-text').val()},
			success: function (data) {

				//alert(data);

				$('.loader3').hide();

				var downloadLink = document.createElement("a");
				var fileData = ['\ufeff' + data];

				var blobObject = new Blob(fileData, {
					type: "text/xml;charset=utf-8;"
				});

				var url = URL.createObjectURL(blobObject);
				downloadLink.href = url;
				var filenames = ($('#search-text').val()).split(",");
				var street = filenames[1];
				var filenameStree = street.replace(/\s/g, '');
				var newfile = filenames[0]+filenameStree;
			
				downloadLink.download = newfile + ".xml";

				/*
				 * Actually download CSV
				 */
				document.body.appendChild(downloadLink);
				downloadLink.click();
				document.body.removeChild(downloadLink);


			},
		});


	}




});



$("#quote_status").change(function () {
	if ($('#location').val() == 'select') {
		var search_val = $('#search-hidden').val();
	}
	else {
		var search_val = "";
	}

	$.ajax({
		type: "POST",
		url: "ajax/ajax_status_properties.php",
		data: { jobstatus: $('#quote_status').val(), city: $('#location').val(), search_text: search_val },
		success: function (data) {
			$('#list-prop').hide();
			$('#result-prop').html(data);
		},
	});
});
$(document).ready(function () {
	$(".load-popup").click(function () {

		$('.loader3').show();
		var id = $(this).data("id");

		var base64 = getBase64Image(document.getElementById("backgroundImage" + id));
		document.getElementById('noimage').src = "data:image/jpg;base64," + base64;
		$('.modal-dialog').hide();
		$.ajax({
			type: "POST",
			url: "ajax/ajax_location_details.php",
			data: { locationid: id },
			success: function (data) {

				$('.modal-dialog').html();
				$('.loader3').hide();
				$('.modal-dialog').html(data);
				$('.modal-dialog').show();
				getBackgroundColor();

			},
		});

	});
});




function loadSearchProperty(id) {

	if (document.getElementById("backgroundImage" + id) != undefined) {

		var base64 = getBase64Image(document.getElementById("backgroundImage" + id));
		document.getElementById('noimage').src = "data:image/jpg;base64," + base64;


	}



	$.ajax({
		type: "POST",
		url: "ajax/ajax_location_details.php",
		data: { locationid: id },
		success: function (data) {
			$('.loader3').hide();
			$('#search-modal').html(data);

			$('#myModal').modal('show');
			getBackgroundColor();

		},
	});


}


$(document).ready(function () {
	$(".addprop").click(function () {

		$('#statusid').val($(this).data('id'));

	});
});
function Down_del(attachmentid, locationid) {

	swal({
		title: "Do you want to delete this file?",
		text: "Once deleted, you will not be able to recover this file!",
		icon: "warning",
		buttons: true,
		dangerMode: true,
	})
		.then((willDelete) => {

			if (willDelete) {
				//alert(attachmentid);
				//alert(locationid);
				$.ajax({
					type: "POST",
					url: "ajax/ajax_attachment.php",
					data: { attachmentid: attachmentid, locationid: locationid, status: 0 },
					success: function (data) {

						if (data == 0) {
							$('#spn_atatch').hide();
						}
						else {
							$('#spn_atatch').html(data);
						}
					},
				});



				/* swal("Poof! Your imaginary file has been deleted!", {
				   icon: "success",
				 });*/
			} /*else {
    
  }*/
		});

}


$(document).on("click", "#before-photo", function () {
	$('#windowid').val($(this).data('id'));


});


$(document).on("click", "#after-photo", function () {
	$('#windowid_after').val($(this).data('id'));


});





$(document).on("click", "#before-upload", function () {

	if ($('#beforimag').prop('files')[0] == undefined) {
		$('#span_error').html('Please Upload The Image');
	} else {
		var windowid = $('#windowid').val();
		var file_data = $('#beforimag').prop('files')[0];
		var form_data = new FormData();
		form_data.append('file', file_data);
		form_data.append('windowid', $('#windowid').val());
		form_data.append('status', 'before');
		$('.loader3').show();
		$.ajax({
			type: "POST",
			url: "ajax/ajax_image_upload.php",
			dataType: 'text',  // what to expect back from the PHP script, if anything
			cache: false,
			contentType: false,
			processData: false,

			data: form_data,
			success: function (data) {
				$("#div_befor_img" + windowid + " ul").append(data);

				$('.loader3').hide();

				$(".close").trigger("click");
			},
		});
	}



});


$(document).on("click", "#after-upload", function () {

	if ($('#afterimag').prop('files')[0] == undefined) {
		$('#span_error_after').html('Please Upload The Image');
	} else {
		var windowid = $('#windowid_after').val();
		var file_data = $('#afterimag').prop('files')[0];
		var form_data = new FormData();
		form_data.append('file', file_data);
		form_data.append('windowid', $('#windowid_after').val());
		form_data.append('status', 'after');
		$('.loader3').show();
		$.ajax({
			type: "POST",
			url: "ajax/ajax_image_upload.php",
			dataType: 'text',  // what to expect back from the PHP script, if anything
			cache: false,
			contentType: false,
			processData: false,

			data: form_data,
			success: function (data) {
				$("#div_after_img" + windowid + " ul").append(data);

				$('.loader3').hide();

				$(".close").trigger("click");
			},
		});
	}



});



$(document).on("click", "#cmmt-btton", function () {
	if ($('#comments').val() == "") {
		$('#comments').css('border-color', 'red');
	}
	else {
		$('#comments').css('border-color', '');
		if ($('#commentid').val() == '') {
			var comment_id = 0;
		} else {
			var comment_id = $('#commentid').val();
		}
		//alert(comment_id);
		$.ajax({
			type: "POST",
			url: "ajax/ajax-comments.php",
			data: { comment: $('#comments').val(), locationid: $('#locationid').val(), comment_id: comment_id },
			success: function (data) {
				$('#comments').val('');
				var result = data.split('@');
				$('#cmmt_' + $('#locationid').val()).html();
				$('#cmmt_' + $('#locationid').val()).html(result[1]);
				$('.recent-comments').append(result[0]);
			},
		});
	}
});





$("#list-prop").change(function () {
	$('.loader3').show();
	$.ajax({
		type: "POST",
		url: "ajax/ajax_list_properties.php",
		data: { locationid: $('#list-prop').val(), statusCode: 1 },
		success: function (data) {
			$('.loader3').hide();
			$('#res_prop_detail').html(data);
		},
	});
});



$("#list").change(function () {
	if ($('#search-text').val() == "") {
		$('#search-text').css('border-color', 'red');
		//event.preventDefault();
	} else {
		$('.loader3').show();

		$.ajax({
			type: "POST",
			url: "ajax/ajax_report.php",
			data: { locationtext: $('#search-text').val(), list_value: $('#list').val() },
			success: function (data) {
				$('.loader3').hide();
				$('#ajax-report').html(data);
			},
		});
	}

});





$("#search").click(function () {

	var url = window.location.pathname;
	var page = url.substr(url.lastIndexOf('/') + 1);
	if ($('#search-text').val() == "") {
		$('#search-text').css('border-color', 'red');
		event.preventDefault();
	}
	else {
		$('.loader3').show();
		if (page == 'cutting-list.php') {
			$.ajax({
				type: "POST",
				url: "ajax/ajax_report.php",
				data: { locationtext: $('#search-text').val(), list_value: $('#list').val() },
				success: function (data) {

					$('.loader3').hide();
					$('#ajax-report').html(data);


				},
			});
		}
		else if (page == 'manage-portal.php') {
			$.ajax({
				type: "POST",
				url: "ajax/ajax_list_properties.php",
				data: { locationtext: $('#search-text').val(), statusCode: 1 },
				success: function (data) {
					$('.loader3').hide();
					$('#res_prop_detail').html(data);
				},
			});
		}
		else if (page == 'customer-portal.php') {

			$.ajax({
				type: "POST",
				url: "ajax/ajax_list_properties.php",
				data: { locationtext: $('#search-text').val(), statusCode: 'customer-portal' },
				success: function (data) {
					//$('.loader3').hide();
					loadSearchProperty(data);

				},
			});


		}

	}




});




function profile(value, panelid) {

	$.ajax({
		type: "POST",
		url: "ajax/ajax_update_profile.php",
		data: { profileid: value, panelid: panelid },
		success: function (data) {

			//$('#spn_profile'+panelid).html(data);
			swal({
				title: "Profile Updated!",
				//text: "You clicked the button!",
				icon: "success",
				button: "OK",
			});

		},
	});
}

function generatetime(cnt, locationid, status) {
	if (document.getElementById("chk_stat" + cnt).checked == true) {
		var today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth() + 1; //January is 0!
		var hour = today.getHours();
		var minutes = today.getMinutes();

		var yyyy = today.getFullYear();
		if (dd < 10) {
			dd = '0' + dd;
		}
		if (mm < 10) {
			mm = '0' + mm;
		}
		var today = dd + '-' + mm + '-' + yyyy;
		var datesend = yyyy + '-' + mm + '-' + dd;
		var timesend = hour + ":" + minutes;
		$('#hr_time' + cnt).html(hour + ":" + minutes + " " + today);
		$('#p_status' + cnt).css('color', 'black');
		$('#hr_time' + cnt).css('color', 'black');


	}
	else {

		$('#hr_time' + cnt).html("00:00 00-00-0000");
		var datesend = "00-00-0000";
		var timesend = "00:00";
		$('#p_status' + cnt).css('color', '');
		$('#hr_time' + cnt).css('color', '');
	}

	$.ajax({
		type: "POST",
		url: "ajax/ajax_update_status.php",
		data: { datesend: datesend, timesend: timesend, status: status, locationid: locationid, crnt_status: document.getElementById("chk_stat" + cnt).value },
		success: function (data) {

			// alert(data);		

		},
	});




}

function gethourlabour(cost, count, hour, product, windowid, travel, panelid, roomid) {


	if (cost == 0) {
		var total_price = (parseFloat($('#total_price').html()) - (parseFloat($('#cost_val' + count).html()))).toFixed(2);
		var total_hours = (parseFloat($('#total_hrs').html()) - (parseFloat($('#hour_val' + count).html()))).toFixed(2);
		var total_travel = (parseFloat($('#total_travel').html()) - (parseFloat($('#travel_val' + count).html()))).toFixed(2);

	}
	else {
		var new_price = parseFloat($('#total_price').html()) - (parseFloat($('#cost_val' + count).html()));
		var new_hour = parseFloat($('#total_hrs').html()) - (parseFloat($('#hour_val' + count).html()));
		var new_travel = parseFloat($('#total_travel').html()) - (parseFloat($('#travel_val' + count).html()));
		var total_price = (parseFloat(cost) + new_price).toFixed(2);
		var total_hours = (parseFloat(hour) + new_hour).toFixed(2);
		var total_travel = (parseFloat(travel) + new_travel).toFixed(2);

	}


	/*if(cost == 0)
	   {
				//var total_price=(parseFloat($('#total_price').html())- (parseFloat($('#cost_val'+count).html()))).toFixed(2);
				var total_hours=(parseFloat($('#total_hrs').html())- (parseFloat( $('#hour_val'+count).html()))).toFixed(2);
							   var res_price=((parseFloat($('#total_price').html()))- ((parseFloat($('#cost_val'+count).html()))+(parseFloat($('#travel_val'+count).html())))).toFixed(2); 
					   	
	   }
	   else
	   {
				 	
								   var new_price=((parseFloat($('#total_price').html()))- ((parseFloat($('#cost_val'+count).html()))+(parseFloat($('#travel_val'+count).html())))); 

					   var new_hour=parseFloat($('#total_hrs').html())-(parseFloat($('#hour_val'+count).html()));
							 var res_price=(parseFloat(cost)+new_price+travel).toFixed(2);
			  var total_hours=(parseFloat(hour)+new_hour).toFixed(2);
				   	
	   }*/
	//alert(total_price);
	$('#cost_val' + count).html(cost);
	$('#hour_val' + count).html(hour);
	$('#travel_val' + count).html(travel);
	$('#total_price').html(total_price);
	$('#total_hrs').html(total_hours);
	$('#total_travel').html(total_travel);
	$('#new_val' + count).val(cost + "@" + hour + "@" + product + "@" + windowid + "@" + panelid + "@" + roomid);
	var btns = new Array();
	btns.push('HOLD', 'SGU', 'IGUX2', 'IGUX3', 'EVSx2', 'EVSx3');
	for (i = 0; i < btns.length; i++) {
		$("#btn_" + btns[i] + count).css("background-color", "#ddd");
	}
	$("#btn_" + product + count).css("background-color", "#17a2b8");
}

$('#projectset').change(function(){

	$('.loader3').show();

	$('#projectid').val($('#projectset').val());
	

	$.ajax({
		type: "POST",
		url: "ajax/ajax_location_projects.php",
		data: { projectid: $('#projectset').val(),locationid:$('#locationid').val(),status:1},
		success: function (data) {
			
			var res = data.split("@");

			$('#edit-pjtname').val(res[0]);

			$('#edit-pjtdate').val(res[1]);

			location.reload();

		},
	});

});
$('#updatepjt').click(function(){



	if($('#edit-pjtname').val()==''){

		$('#edit-pjtname').css('border-color', 'red');

		$('#pjtdate').css('border-color', '');

	}

	else if($('#edit-pjtdate').val()==''){

		$('#edit-pjtdate').css('border-color', 'red');

		$('#edit-pjtname').css('border-color', '');


	}

	
	else{

		$('.loader3').show();

		var quotearr=[];

	$.each($("input[name='quotecheck[]']:checked"), function(){
		
		quotearr.push($(this).val());
	});

	if(quotearr.length>0)

	var checkdwindow = quotearr.toString();

	else

	var checkdwindow ="";
	
    $("#div_window").addClass("table-fade");
	$('.loader3').show();
	var info = new Array();
	$('input[name="update_arry[]"]').each(function () {

		info.push($(this).val());
	});
	
	var locationid = $('#locationid').val();
	
	$.ajax({
		type: "POST",
		url: "ajax/ajax_update_product.php",
		data: { data: info,locationid:locationid,projectid: $('#projectid').val(), pagestatus: $("input[name=page-status]").val(),pjtname:$('#edit-pjtname').val(),pjtdate: $('#edit-pjtdate').val(),windowchked:checkdwindow},
		success: function (data) {
		
		
           var $select = $('#projectset');
			
			getpanels(locationid);
			getpdtstatus(locationid);
			//alert(data);
			$('#load-result').hide();
			$('.loader3').hide();
			$("#div_window").removeClass("table-fade");
			$('#ajax-load-result').show();
			$('#ajax-load-result').html(data);
			$("#projectset option[value='"+ $('#ajx_pjtid').val()+"']").remove();
			$select.append('<option value="' + $('#ajx_pjtid').val()+ '" selected="selected">' + $('#ajx_pjtname').val() + '</option>');
			//var exp= $('#ajx_pjtname').val().split(" ");
			
			
			$('#edit-pjtname').val($('#ajx_edit_pjtname').val());
            $('#edit-pjtdate').val($('#ajx_pjtdate').val());
			swal({
				title: "Product Updated!",
				//text: "You clicked the button!",
				icon: "success",
				button: "OK",
			});

		},
	});


	}



})

function update(locationid) {

	if($('#pjtname').val()==''){

		$('#pjtname').css('border-color', 'red');

		$('#pjtdate').css('border-color', '');

	}

	else if($('#pjtdate').val()==''){

		$('#pjtdate').css('border-color', 'red');

		$('#pjtname').css('border-color', '');


	}

	
	else{

		var quotearr=[];

	$.each($("input[name='quotecheck[]']:checked"), function(){
		
		quotearr.push($(this).val());
	});

	if(quotearr.length>0)

	var checkdwindow = quotearr.toString();

	else

	var checkdwindow ="";
	
    $("#div_window").addClass("table-fade");
	$('.loader3').show();
	var info = new Array();
	$('input[name="update_arry[]"]').each(function () {

		info.push($(this).val());
	});
	
	//alert(JSON.stringify(info));
	$.ajax({
		type: "POST",
		url: "ajax/ajax_update_product.php",
		data: { data: info, locationid: locationid,projectid:0, pagestatus: $("input[name=page-status]").val(),pjtname:$('#pjtname').val(),pjtdate: $('#pjtdate').val(),windowchked:checkdwindow},
		success: function (data) {
		
           var $select = $('#projectset');
			
			getpanels(locationid);
			getpdtstatus(locationid);
			//alert(data);
			$('#load-result').hide();
			$('.loader3').hide();
			$("#div_window").removeClass("table-fade");
			$('#ajax-load-result').show();
			$('#ajax-load-result').html(data);
			$select.append('<option value="' + $('#ajx_pjtid').val()+ '" selected="selected">' + $('#ajx_pjtname').val() + '</option>');
			//var exp= $('#ajx_pjtname').val().split(" ");
			
			$('#edit-pjtname').val($('#ajx_edit_pjtname').val());
            $('#edit-pjtdate').val($('#ajx_pjtdate').val());
			$('#projectid').val( $('#ajx_pjtid').val());
			swal({
				title: "Product Updated!",
				//text: "You clicked the button!",
				icon: "success",
				button: "OK",
			});

		},
	});


	}

	

}

function getpanels(id) {
	$.ajax({
		type: "POST",
		url: "ajax/ajax_panel_count.php",
		data: { locationid: id },
		success: function (data) {
			$('#spn-panels').html(data);

		},
	});

}


function getpdtstatus(id) {
	$.ajax({
		type: "POST",
		url: "ajax/ajax_product_status.php",
		data: { locationid: id },
		success: function (data) {
			$('#spn-pdt-status').html(data);

		},
	});

}


function getproprty(value) {


	var url = window.location.pathname;
	var page = url.substr(url.lastIndexOf('/') + 1);
	if (page == "manage-portal.php") {
		$('.loader3').show();
		$.ajax({
			type: "POST",
			url: "ajax/ajax_list_properties.php",
			data: { locationid: value, statusCode: 1 },
			success: function (data) {
				$('.loader3').hide();
				$('#res_prop_detail').html(data);
			},
		});
	}
	else if (page == "cutting-list.php") {
		$('.loader3').show();
		if ($('#list-prop').val() == "Select Property" || $('#list-prop').val() == "") {
			$('#list-prop').css('border-color', 'red');
		}
		else {
			$.ajax({
				type: "POST",
				url: "ajax/ajax_report.php",
				data: { location_id: $('#list-prop').val(), list_value: $('#list').val() },
				success: function (data) {

					$('.loader3').hide();
					$('#ajax-report').html(data);


				},
			});
		}


	}


}



function updatenotes(windowid, type) {
	$.ajax({
		type: "POST",
		url: "ajax/ajax_notes_extras.php",
		data: { windowid: windowid, type: type, value: $('#windownotes' + windowid).val() },
		success: function (data) {

			$('#notes' + windowid).html(data);
			$(".close").trigger("click");
		},
	});


}
function updateextras(windowid, type) {
	$.ajax({
		type: "POST",
		url: "ajax/ajax_notes_extras.php",
		data: { windowid: windowid, type: type, value: $('#windowextras' + windowid).val() },
		success: function (data) {

			$('#extras' + windowid).html(data);
			$(".close").trigger("click");
		},
	});


}
function readURL(input) {

	if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function (e) {
			$('#userimage').attr('src', e.target.result);
		}

		reader.readAsDataURL(input.files[0]);
	}
}
function readURLBefore(input) {

	if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function (e) {
			$('#userimage_before').attr('src', e.target.result);
		}

		reader.readAsDataURL(input.files[0]);
	}
}

function readURLAfter(input) {

	if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function (e) {
			$('#userimage_after').attr('src', e.target.result);
		}

		reader.readAsDataURL(input.files[0]);
	}
}

function readURLEdit(input) {

	if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function (e) {
			$('#userimage-edit').attr('src', e.target.result);
		}

		reader.readAsDataURL(input.files[0]);
	}
}



function getwindownotes(windowid, notes) {
	$('#windownotes').val(notes);
	$('#windowid').val(windowid);
}

function updateproperty(locationid, customerid) {

	var file_data = $('#locimage-edit').prop('files')[0];
	var form_data = new FormData();
	form_data.append('file', file_data);
	form_data.append('unitnum', $('#unitnum-edit').val());
	form_data.append('locationid', locationid);
	form_data.append('customerid', customerid);
	//	form_data.append('loc_status',$("input[name='loc_status-edit']:checked").val());
	form_data.append('street', $('#street-edit').val());
	form_data.append('suburb', $('#suburb-edit').val());
	form_data.append('city', $('#city-edit').val());
	form_data.append('firstname', $('#firstname-edit').val());
	form_data.append('lastname', $('#lastname-edit').val());
	form_data.append('phone', $('#phone-edit').val());
	form_data.append('email', $('#email-edit').val());
	if ($('#unitnum-edit').val() == "") {
		$('#unitnum-edit').css('border-color', 'red');
	}
	else if ($('#street-edit').val() == "") {
		$('#street-edit').css('border-color', 'red');
	}
	else if ($('#suburb-edit').val() == "") {
		$('#suburb-edit').css('border-color', 'red');
	}
	else if ($('#city-edit').val() == "") {
		$('#city-edit').css('border-color', 'red');
	}
	else if ($('#firstname-edit').val() == "") {
		$('#firstname-edit').css('border-color', 'red');
	}
	else if ($('#lastname-edit').val() == "") {
		$('#lastname-edit').css('border-color', 'red');
	}
	else if (!validateEmail($('#email-edit').val())) {
		$('#email-edit').css('border-color', 'red');
	}
	else if (!numeric($('#phone-edit').val())) {
		$('#phone').css('border-color', 'red');
	}
	else {
		$('.loader3').show();
		$.ajax({
			type: "POST",
			url: "ajax/ajax_update_property.php",
			dataType: 'text',  // what to expect back from the PHP script, if anything
			cache: false,
			contentType: false,
			processData: false,

			data: form_data,
			success: function (data) {
				if ($('#loc-status').val() == 1) {
					location.reload();
				}
				else {
					$('#curnt-details').hide();
					$('#ajax-property').html(data);

					$('.loader3').hide();
					$(".close").trigger("click");
				}



			},
		});
	}

}
/*function getVal(panelid,styleid,img,stylename)
{
	//var text = $("select[name=styleop] option[value="+styleid+"]").text();
	var text=stylename;
	$('.bootstrap-select .filter-option').text(text);
	$('select[name=styleop]').val(styleid);
	document.getElementById('panelid').value=panelid;
	document.getElementById('styleid').value=styleid;
	document.getElementById('style-image').src=img;
	$("#styleop").val(styleid);
	$.ajax({
            type: "POST",
            url: "ajax/ajax_profile.php",
            data: {panelid:panelid,styleid:styleid,status:0},
            success: function (data) {
											
                $('#top_spn').html(data);
            },
        });
	
	
}*/

$(document).on("change", "#styleop", function () {
	//alert($('#styleop').val());
	$.ajax({
		type: "POST",
		url: "ajax/ajax_frame.php",
		data: { materialtype: $('#styleop').val() },

		success: function (data) {
			$('#id_frame').css('display', 'block');
			$('#id_style').css('display', 'none');
			var $select = $('#styleframe');
			var json_obj = $.parseJSON(data)
			$('#styleframe').empty();
			$select.append('<option value="select">Choose Your Frame</option>');
			for (var i in json_obj) {
				//alert(json_obj[i].image);
				$select.append('<option value="' + json_obj[i].frametypeid + '" data-thumbnail="' + json_obj[i].image + '">' + json_obj[i].name + '</option>');
			}

			$('.selectpicker').selectpicker('refresh');
			//alert(JSON.stringify(data));


		},
	});

});



$(document).on("change", "#styleframe", function () {
	$.ajax({
		type: "POST",
		url: "ajax/ajax_get_style.php",
		data: { frametypeid: $('#styleframe').val(), status: 0 },

		success: function (data) {
			$('#id_style').css('display', 'block');
			/* var result = data.split('@');
			if(result[1]==0){
				 $('#id_100').html(result[0]);
			}*/

			/*var option=$('#styleop').val();
			$("#styleop option[value="+option+"]").remove();
			$('#styleop').prepend(data); */
			var $select = $('#style_options');
			var json_obj = $.parseJSON(data);
			$('#style_options').empty();
			$select.append('<option value="select">Choose your Style</option>');
			for (var i in json_obj) {
				//alert(json_obj[i].image);
				$select.append('<option value="' + json_obj[i].styleid + '" data-thumbnail="' + json_obj[i].image + '">' + json_obj[i].name + '</option>');
			}

			$('.selectpicker').selectpicker('refresh');
			//alert(JSON.stringify(data));


		},
	});

});


$(document).on("click", "#panel-profile-update", function () {

	$.ajax({
		type: "POST",
		url: "ajax/ajax_style_update.php",
		data: { materialCategory: $('#panel-framcategory').val(), locationid: $('#locationid').val(), styleid: $('#styleop').val(), panelid: $('#panelid').val(), width: $('#width').val(), height: $('#height').val(), center: $('#center').val(), measurement: $("input[name='measurement']:checked").val(), safety: $('#drop-safty').val(), glasstype: $('#drop-glasstype').val(), condition: $('#drop-condition').val(), astragal: $('#drop-astragal').val(), frametypeid: $('#styleframe').val() },

		success: function (data) {
			
		

			var result = data.split('@');
			


			$('#span' + $('#panelid').val()).html(result[0]);
			$('#spn_width' + $('#panelid').val()).html(result[1]);
			$('#td_safty' + $('#panelid').val()).html(result[2]);
			$('#td_glass' + $('#panelid').val()).html(result[3]);
			$('#td_condition' + $('#panelid').val()).html(result[4]);
			$('#td_astragal' + $('#panelid').val()).html(result[5]);

			$(".close").trigger("click");
		},
	});

});


$(document).on("change", "#style_options", function () {
	$.ajax({
		type: "POST",
		url: "ajax/ajax_get_style.php",
		data: { styleid: $('#style_options').val(), panelid: $('#panelid').val(), astragal: $('#drop-astragal').val(), conditionid: $('#drop-condition').val(), glasstypeid: $('#drop-glasstype').val(), saftyid: $('#drop-safty').val(), width: $('#width').val(), height: $('#height').val(), center: $('#center').val(), status: 1 },

		success: function (data) {

			var result = data.split('@');
			$('#id_100').html(result[0]);
			document.getElementById('style-image').src = result[1];
			$('#id_frame').css('display', 'none');
			$('#id_style').css('display', 'none');
			$('#td_dglabour').html(result[2]);
			$('#td_evslabour').html(result[3]);
		},
	});

});



function getVal(locationid, panelid, styleid, img, stylename) {

	$('#id_frame').css('display', 'none');
	$('#id_style').css('display', 'none');
	//$('#styleop').append('<option value="'+styleid+'" selected="selected">'+stylename+'</option>');
	document.getElementById('panelid').value = panelid;
	document.getElementById('styleid').value = styleid;
	document.getElementById('style-image').src = img;
	$('#styleframe').empty();
	$.ajax({

		type: "POST",
		url: "ajax/ajax_profile.php",
		data: { locationid: locationid, panelid: panelid, styleid: styleid, status: 0 },
		success: function (data) {
			var result = data.split('@');
			$('#id_100').html(result[0]);
			$('#top_spn').html(result[1]);
			$('#div_material').html(result[2]);
		},
	});
}


function numeric(num) {
	var v = true;
	var vchar = "0123456789";
	for (i = 0; i < num.length; i++) {
		var c = num.charAt(i);
		if (vchar.indexOf(c) == -1) {
			v = false;
			break;
		}
	}
	return v;
}

function validateEmail(sEmail) {
	var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
	if (filter.test(sEmail)) {
		return true;
	}
	else {
		return false;
	}
}
/* ajax*/
$(document).on("click", ".edit-file", function () {
	$(this).siblings('input').trigger('click');
});



$(document).on("change", ".upload-file", function (files) {

	$('.loader3').show();
	var filePath = $(this).val();
	var id = $(this).attr('id');
	var file_data = $('.upload-file').prop('files')[0];

	var form_data = new FormData();
	form_data.append('file', file_data);
	form_data.append('attachmentid', id);
	form_data.append('status', '1');
	$.ajax({
		type: "POST",
		url: "ajax/ajax_attachment.php",
		dataType: 'text',  // what to expect back from the PHP script, if anything
		cache: false,
		contentType: false,
		processData: false,

		data: form_data,
		success: function (data) {



			$('.loader3').hide();


			$('#spn_atatch').html(data);

		},
	});

});



$(document).ready(function () {
	$("#add-prop").click(function () {
		//alert($('#unitnum').val());

		if ($('#unitnum').val() == "") {
			$('#unitnum').css('border-color', 'red');
		}
		else if ($('#street').val() == "") {
			$('#street').css('border-color', 'red');
		}
		else if ($('#suburb').val() == "") {
			$('#suburb').css('border-color', 'red');
		}
		else if ($('#city').val() == "") {
			$('#city').css('border-color', 'red');
		}
		else if ($('#firstname').val() == "") {
			$('#firstname').css('border-color', 'red');
		}
		else if ($('#lastname').val() == "") {
			$('#lastname').css('border-color', 'red');
		}
		else if (!validateEmail($('#email').val())) {
			$('#email').css('border-color', 'red');
		}
		else if (!numeric($('#phone').val())) {
			$('#phone').css('border-color', 'red');
		}
		else {

			$('.loader3').show();
			var file_data = $('#locimage').prop('files')[0];

			var form_data = new FormData();
			form_data.append('file', file_data);
			form_data.append('unitnum', $('#unitnum').val());
			form_data.append('loc_status', $("input[name='loc_status']:checked").val());
			form_data.append('street', $('#street').val());
			form_data.append('suburb', $('#suburb').val());
			form_data.append('city', $('#city').val());
			form_data.append('firstname', $('#firstname').val());
			form_data.append('lastname', $('#lastname').val());
			form_data.append('phone', $('#phone').val());
			form_data.append('email', $('#email').val());
			form_data.append('statusid', $('#statusid').val());
			form_data.append('notes', $('#notes').val());



			$.ajax({
				type: "POST",
				url: "ajax/ajax_add_property.php",
				dataType: 'text',  // what to expect back from the PHP script, if anything
				cache: false,
				contentType: false,
				processData: false,

				data: form_data,
				success: function (data) {
					location.reload();

					//alert(data);

					/*var cnt=$('#statusid').val();
	
					$('#ajax-span'+cnt).html(data);
				
$('.loader3').hide();
								$( ".close" ).trigger( "click" );*/

				},
			});
		}




	});
});


$(document).on("change", ".add-file", function (files) {
	$('.loader3').show();
	var filePath = $(this).val();
	var id = $(this).data('id');

	var file_data = $('.add-file').prop('files')[0];
	var string = bytesToSize(file_data.size);
	var result = string.split(" ");
	var flag = 0;
	if (result[1] == "MB") {

		if (result[0] < 15)
			var flag = 0;
		else
			var flag = 1;
	}

	if (flag == 0) {

		var form_data = new FormData();
		form_data.append('file', file_data);
		form_data.append('locationid', id);
		form_data.append('status', '2');
		$.ajax({
			type: "POST",
			url: "ajax/ajax_attachment.php",
			dataType: 'text',  // what to expect back from the PHP script, if anything
			cache: false,
			contentType: false,
			processData: false,

			data: form_data,
			success: function (data) {

				$('.loader3').hide();
				if(data == false){
					swal("Your total uploaded capacity exceeded 10 GB!");  
				
				 }else{
	
					var result = data.split('@');
					$('.loader3').hide();
	
					$('#attach_' + result[2]).html(result[1]);
					$('#spn_atatch').html(result[0]);
	
				 }

			

			},
		});

	}

	else {
		$('.loader3').hide();
		$('#spn_atatch').html("file size cannot exceed 15 MB");
		$('#spn_atatch').css('color', 'red');
	}

});
/* for drag */

$(function () {
	$('div[id^="sort"]').sortable({
		connectWith: ".column-drag",
		receive: function (e, ui) {
			var status_id = $(ui.item).parent(".column-drag").data("status-id");
			var locationid = $(ui.item).data("task-id");
			//	alert(status_id);
			//alert(task_id);
			$.ajax({
				type: "POST",
				url: "ajax/ajax_drag_location.php",
				data: { status_id: status_id, locationid: locationid },
				success: function (data) {
					var result = data.split('@');
					$('#card-count' + result[1]).html(result[0]);
					$('#card-count' + status_id).html(result[2]);
					$('#total-panels' + status_id).html(result[3]);
					$('#selected-panels' + status_id).html(result[4]);
					$('#total-panels' + result[1]).html(result[5]);
					$('#selected-panels' + result[1]).html(result[6]);
					//alert(data);
				},
			});

		}

	}).disableSelection();
});

$(document).on("click", ".move-btn", function () {
	$(this).siblings('ul').toggle();
});

$(document).on("click", "#move_location_btn", function () {

	$(".move-loader").css("display", "block");
	$('#mov_load_loc').html($('#loc_quote_name' + $("input[name='enq']:checked").val()).val());
	$.ajax({
		type: "POST",
		url: "ajax/ajax_move_location.php",
		data: { status_id: $("input[name='enq']:checked").val(), locationid: $('#locationid').val(), status: 'move' },
		success: function (data) {

			location.reload();

		},
	});
	//alert($("input[name='enq']:checked").val());
});


$(document).on("click", ".delte-rad", function () {
	swal({
		title: "Do you want to delete this file?",
		text: "Once deleted, you will not be able to recover this location!",
		icon: "warning",
		buttons: true,
		dangerMode: true,
	})
		.then((willDelete) => {

			if (willDelete) {

				$.ajax({
					type: "POST",
					url: "ajax/ajax_move_location.php",
					data: { locationid: $("input[name='delete']:checked").val(), status: 'delete' },
					success: function (data) {

						location.reload();


					},
				});

			}
		});

});
$(document).on("click", ".rad-move", function () {
	if ($('#emailid').val() == "") {
		$('#emailid').css('border-color', 'red');
	} else {
		$('#emailid').css('border-color', '');
		//alert($('#emailid').val());
		//alert($("input[name='move']:checked").val());
		$.ajax({
			type: "POST",
			url: "ajax/ajax_move_location.php",
			data: { locationid: $("input[name='move']:checked").val(), status: 'move_account', email: $('#emailid').val() },
			success: function (data) {

				if (data == 0) {
					swal("Agent doesn't exist");
				} else {
					location.reload();
				}



			},
		});
	}
});


$(document).on("change", "#emailid", function () {
	if ($('#emailid').val() == "") {
		$('#emailid').css('border-color', 'red');
	} else {
		$('#emailid').css('border-color', '');
		$.ajax({
			type: "POST",
			url: "ajax/ajax_move_location.php",
			data: { locationid: $("input[name='move']:checked").val(), status: 'move_account', email: $('#emailid').val() },
			success: function (data) {
				if (data == 0) {
					swal("Agent doesn't exist");
				} else {
					location.reload();
				}


			},
		});
	}
});


$(document).on("click", "#datetimepicker", function () {
	var today = new Date();
	var date = today.getFullYear() + '/' + (today.getMonth() + 1) + '/' + today.getDate();
	var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
	var tp = $(this);
	//alert(tp);


	//$('#datetimepicker_mask').datetimepicker();
	$('#datetimepicker').datetimepicker();
	tp.datetimepicker({ value: date + " " + time });
	$('#datetimepicker1').datetimepicker();
	tp.datetimepicker1({
		datepicker: false,
		format: 'H:i',
		step: 5
	});



});


$(document).on("click", "#edit-location", function () {
	$.ajax({
		type: "POST",
		url: "ajax/ajax_move_location.php",
		data: { locationid: $('#locationid').val() },
		success: function (data) {
			if (data == 0) {
				swal("Agent doesn't exist");
			} else {
				location.reload();
			}


		},
	});
});





$(document).on("click", "#save", function () {

	if ($('#datetimepicker').val() == '000-00-00 00:00:00') {
		$("#datetimepicker").css("border-color", "red");
	}
	else if ($('.enddate').val() == '000-00-00 00:00:00') {
		$(".enddate").css("border-color", "red");
	}
	else {
		$(".move-loader").css("display", "block");
		$('#title_loader').html('Booking');
		$('#mov_load_loc').html('saving');
		//document.getElementById('booking_notes').focus();

		$.ajax({
			type: "POST",
			url: "ajax/ajax_move_location.php",
			data: { locationid: $('#locationid').val(), status: 'duedate', duedate: $('#datetimepicker').val(), enddate: $('.enddate').val(), alarm_type: $("input[name='alarm_type']:checked").val(), booking_notes: $('#booking_notes').val() },
			success: function (data) {
				if (data == 1) {

					location.reload();
				}
			},
		});
	}


});


$(document).on("click", "#cancel", function () {
	$.ajax({
		type: "POST",
		url: "ajax/ajax_move_location.php",
		data: { locationid: $('#locationid').val(), status: 'remove' },
		success: function (data) {
			if (data == 1) {

				location.reload();
			}
		},
	});
});



$(document).on("click", "#copy-text", function () {
	var copyText = document.getElementById('quote-url');
	copyText.select();
	document.execCommand("copy");
});


$(document).on("click", ".date-pick-popup a", function () {
	$(this).parent().children('.timepick_popup').toggle();


});

$(document).on("click", "#cancel", function () {
	$(this).parent().parent().hide();


});



$(document).on("click", "#edit-comment", function () {
	$('.loader3').show();
	var cmmtid = $(this).data('id');
	$('#commentid').val($(this).data('id'));
	$.ajax({
		type: "POST",
		url: "ajax/ajax_get_commnet.php",
		data: { commentid: $(this).data('id') },
		success: function (data) {
			$('.loader3').hide();
			$('#div' + cmmtid).remove();
			$('#comments').val(data);
			$('#comments').focus();
		},
	});

});

$(document).on("change", "#drop-safty", function () {

	$.ajax({
		type: "POST",
		url: "ajax/ajax_get_paneloption.php",
		data: { saftyid: $('#drop-safty').val(), glasstypeid: $('#drop-glasstype').val(), conditionid: $('#drop-condition').val(), astragal: $('#drop-astragal').val(), width: $('#width').val(), height: $('#height').val(), center: $('#center').val(), styelid: $('#styleop').val(), panelid: $('#panelid').val(), locationid: $('#locationid').val(), option: 'safty' },
		success: function (data) {

			var result = data.split('@');

			$('#spn_safty_name').html(result[0]);
			$('#spn_safty_val').html(result[1]);
			$('#safty-image').attr('src', result[2]);

			$('#td_dglabour').html(result[3]);
			$('#td_evslabour').html(result[4]);

			$('#quotation_pricing').html(result[5]);

		},
	});

});

$(document).on("change", "#drop-glasstype", function () {

	$.ajax({
		type: "POST",
		url: "ajax/ajax_get_paneloption.php",
		data: { glasstypeid: $('#drop-glasstype').val(), saftyid: $('#drop-safty').val(), conditionid: $('#drop-condition').val(), astragal: $('#drop-astragal').val(), width: $('#width').val(), height: $('#height').val(), center: $('#center').val(), styelid: $('#styleop').val(), panelid: $('#panelid').val(), locationid: $('#locationid').val(), option: 'glasstype' },
		success: function (data) {
			var result = data.split('@');
			$('#spn_glass_name').html(result[0]);
			$('#spn_glass_value').html(result[1]);
			$('#glass-image').attr('src', result[2]);
			$('#td_dglabour').html(result[3]);
			$('#td_evslabour').html(result[4]);
			$('#quotation_pricing').html(result[5]);


		},
	});

});

$(document).on("change", "#drop-condition", function () {

	$.ajax({
		type: "POST",
		url: "ajax/ajax_get_paneloption.php",
		data: { conditionid: $('#drop-condition').val(), glasstypeid: $('#drop-glasstype').val(), saftyid: $('#drop-safty').val(), astragal: $('#drop-astragal').val(), width: $('#width').val(), height: $('#height').val(), center: $('#center').val(), styelid: $('#styleop').val(), panelid: $('#panelid').val(), locationid: $('#locationid').val(), option: 'condition' },
		success: function (data) {

			var result = data.split('@');
			$('#spn_condition_name').html(result[0]);
			$('#spn_condition_value').html(result[1]);
			$('#td_dglabour').html(result[2]);
			$('#td_evslabour').html(result[3]);
			$('#quotation_pricing').html(result[4]);

		},
	});

});

$(document).on("change", "#drop-astragal", function () {

	$.ajax({
		type: "POST",
		url: "ajax/ajax_get_paneloption.php",
		data: { astragal: $('#drop-astragal').val(), conditionid: $('#drop-condition').val(), glasstypeid: $('#drop-glasstype').val(), saftyid: $('#drop-safty').val(), width: $('#width').val(), height: $('#height').val(), center: $('#center').val(), styelid: $('#styleop').val(), panelid: $('#panelid').val(), locationid: $('#locationid').val(), option: 'astragal' },
		success: function (data) {

			var result = data.split('@');
			$('#spn_astragl_name').html(result[0]);
			$('#spn_astragal_value').html(result[1]);
			$('#td_dglabour').html(result[2]);
			$('#td_evslabour').html(result[3]);
			$('#quotation_pricing').html(result[4]);

		},
	});

});
$(document).on("keyup", "#width", function () {

	$.ajax({
		type: "POST",
		url: "ajax/ajax_get_paneloption.php",
		data: { saftyid: $('#drop-safty').val(), glasstypeid: $('#drop-glasstype').val(), conditionid: $('#drop-condition').val(), astragal: $('#drop-astragal').val(), width: $('#width').val(), height: $('#height').val(), center: $('#center').val(), styelid: $('#styleop').val(), panelid: $('#panelid').val(), locationid: $('#locationid').val(), option: 'width' },
		success: function (data) {

			var result = data.split('@');


			$('#td_dglabour').html(result[0]);
			$('#td_evslabour').html(result[1]);

			$('#quotation_pricing').html(result[2]);

		},
	});

});

$(document).on("keyup", "#height", function () {

	$.ajax({
		type: "POST",
		url: "ajax/ajax_get_paneloption.php",
		data: { saftyid: $('#drop-safty').val(), glasstypeid: $('#drop-glasstype').val(), conditionid: $('#drop-condition').val(), astragal: $('#drop-astragal').val(), width: $('#width').val(), height: $('#height').val(), center: $('#center').val(), styelid: $('#styleop').val(), panelid: $('#panelid').val(), locationid: $('#locationid').val(), option: 'height' },
		success: function (data) {

			var result = data.split('@');


			$('#td_dglabour').html(result[0]);
			$('#td_evslabour').html(result[1]);

			$('#quotation_pricing').html(result[2]);

		},
	});

});

$(document).on("keyup", "#center", function () {

	$.ajax({
		type: "POST",
		url: "ajax/ajax_get_paneloption.php",
		data: { saftyid: $('#drop-safty').val(), glasstypeid: $('#drop-glasstype').val(), conditionid: $('#drop-condition').val(), astragal: $('#drop-astragal').val(), width: $('#width').val(), height: $('#height').val(), center: $('#center').val(), styelid: $('#styleop').val(), panelid: $('#panelid').val(), locationid: $('#locationid').val(), option: 'center' },
		success: function (data) {

			var result = data.split('@');


			$('#td_dglabour').html(result[0]);
			$('#td_evslabour').html(result[1]);

			$('#quotation_pricing').html(result[2]);

		},
	});

});

$(document).on("click", "#done", function () {
	$.ajax({
		type: "POST",
		url: "ajax/ajax_move_location.php",
		data: { locationid: $('#locationid').val(), status: 'donedate' },
		success: function (data) {

			if (data == 1) {

				location.reload();
			}
		},
	});

});



$(document).on("click", ".pr-btn.top-btn", function () {

	$.ajax({
		type: "POST",
		url: "ajax/ajax_profile_options.php",
		data: { styleid: $('#styleop').val(), option: 'top' },
		success: function (data) {

			$('.prd-popup.top-popup').html(data);
		},
	});
	$('.prd-popup.top-popup').toggle('3000');

});

$(document).on("click", ".pr-btn.left-btn", function () {

	$.ajax({
		type: "POST",
		url: "ajax/ajax_profile_options.php",
		data: { styleid: $('#styleop').val(), option: 'left' },
		success: function (data) {
			$('.prd-popup.left-popup').html(data);
		},
	});

	$('.prd-popup.left-popup').toggle('3000');

});
$(document).on("click", ".pr-btn.right-btn", function () {

	$.ajax({
		type: "POST",
		url: "ajax/ajax_profile_options.php",
		data: { styleid: $('#styleop').val(), option: 'right' },
		success: function (data) {
			$('.prd-popup.right-popup').html(data);
		},
	});


	$('.prd-popup.right-popup').toggle('3000');
});
$(document).on("click", ".pr-btn.bottom-btn", function () {

	$.ajax({
		type: "POST",
		url: "ajax/ajax_profile_options.php",
		data: { styleid: $('#styleop').val(), option: 'bottom' },
		success: function (data) {
			$('.prd-popup.bottom-popup').html(data);
		},
	});


	$('.prd-popup.bottom-popup').toggle('3000');
});

$(document).on("click", ".close-popup", function () {

	$(this).parent().parent().toggle('3000');
});

function getWindowOptions(cnt) {

	$.ajax({
		type: "POST",
		url: "ajax/ajax_window_options.php",
		data: { optionid: $("#window-option" + cnt).val() },
		success: function (data) {
			$('#spn_qty' + cnt).html(data);
		},
	});
}

$("#recycle-button").click(function () {
	swal("You have reached your maximum!", "Please delete older projects or upgrade to add more locations!");

});


$(".quote-check").change(function () {


	if (this.checked) {


		$.ajax({
			type: "POST",
			url: "ajax/ajax_quote_window.php",
			data: { windowid: $(this).attr("data-id"), value: 1 },
			success: function (data) {

			},
		});



	}
	else {


		$.ajax({
			type: "POST",
			url: "ajax/ajax_quote_window.php",
			data: { windowid: $(this).attr("data-id"), value: 0 },
			success: function (data) {


			},
		});


	}



});


function quotestatus(locationid, quote) {

	if (document.getElementById(quote).checked == true) {

		$.ajax({
			type: "POST",
			url: "ajax/ajax_quote_product.php",
			data: { locationid: locationid, value: 1, product: quote },
			success: function (data) {
				//alert(data)									

			},
		});


	}
	else {

		$.ajax({
			type: "POST",
			url: "ajax/ajax_quote_product.php",
			data: { locationid: locationid, value: 0, product: quote },
			success: function (data) {
				//alert(data);									

			},
		});


	}
}

/* 15-06-2020*/

$(document).on("click", ".window-images", function () {


	var id = $(this).data("id");
	$.ajax({
		type: "POST",
		url: "ajax/ajax_window_types.php",
		data: { windo: id },
		success: function (data) {
			$('#popup-retro').html(data);


		},
	});



});


/*29-09-2020*/

$(document).on("keyup", ".hoursperpanel", function () {
	
	
	var currentid = $(this).attr("data-colourid");

	var time = parseFloat($('#hoursperpanel' + currentid).val()) * parseFloat(($('#cntpans' + currentid).html()));

	$('#time' + currentid).html(time);
	

if($('#costtd' + currentid).html() == '')

var valcost = 0;

else

var valcost = $('#costtd' + currentid).html();

	var totalprice = (time * parseFloat($(this).attr("data-labourrate"))) + parseFloat(valcost);

	$('#totaltd' + currentid).html(totalprice);
	
	var timesarr = [];
	
	if($("td").hasClass("testtimes1")){
		
		$(".testtimes1").each(function(index, elem){
    timesarr.push($(this).text());
 });
	}
	
	else{
		
		 $(".testtimes").each(function(index, elem){
    timesarr.push($(this).text());
});

		
	}
	

	
if(timesarr.length >0){
	var timesum = timesarr.reduce(function (a, b) {
		return parseFloat(a) + parseFloat(b);
	}, 0);
}
else var timesum =0;

var costsarr = [];

if($("td span").hasClass("testcost1")){
	
	
	$(".testcost1").each(function(index, elem){
    costsarr.push($(this).text());
});
	
	
}

else{
	
	$(".testcost").each(function(index, elem){
    costsarr.push($(this).text());
});
	
}



if(costsarr.length >0){
	var costsum = costsarr.reduce(function (a, b) {
		return parseFloat(a) + parseFloat(b);
	}, 0);
}

else var costsum =0;

var totalsarr = [];

if($("td span").hasClass("testtotal1")){
	
	$(".testtotal1").each(function(index, elem){
	
    totalsarr.push($(this).text());
});
	
}

else{
	
	  $(".testtotal").each(function(index, elem){
	
    totalsarr.push($(this).text());
});
	
}



if(totalsarr.length >0){
	var totalssum = totalsarr.reduce(function (a, b) {
		return parseFloat(a) + parseFloat(b);
	}, 0);
}

else  var totalssum =0;


$('#total_timecount').html(timesum);

$('#total_costcount').html("$" + costsum);

$('#total_totalscount').html("$" + totalssum);


	
	$.ajax({
		type: "POST",
		url: "ajax/ajax_painting.php",
		data: { agentid : $(this).attr("data-crntuser"),locationid: $(this).attr("data-locationid"), colourid: $(this).attr("data-colourid"),pans:$('#cntpans'+ currentid).html(), hoursper: $('#hoursperpanel' + currentid).val(),times:time, costper: $('#costperpanel' + currentid).val(),cost:$('#costtd' + currentid).html(),totalcost:totalprice,status: 1 },
		success: function (data) {
			
		
		
			

		},
	});

	
});

$(document).on("keyup", ".costperpanel", function () {
	

	var currentid = $(this).attr("data-colourid");

	var cost = parseFloat($('#cntpans' + currentid).html()) * parseFloat(($('#costperpanel' + currentid).val()));

	$('#costtd' + currentid).html(cost);

	var totalprice = parseFloat(($('#time' + currentid).html()) * parseFloat($(this).attr("data-labourrate"))) + cost;

	$('#totaltd' + currentid).html(totalprice);
	
var timesarr = [];
	
	if($("td").hasClass("testtimes1")){
		
		$(".testtimes1").each(function(index, elem){
    timesarr.push($(this).text());
 });
	}
	
	else{
		
		 $(".testtimes").each(function(index, elem){
    timesarr.push($(this).text());
});

		
	}
	

	
if(timesarr.length >0){
	var timesum = timesarr.reduce(function (a, b) {
		return parseFloat(a) + parseFloat(b);
	}, 0);
}
else var timesum =0;

var costsarr = [];

if($("td span").hasClass("testcost1")){
	
	$(".testcost1").each(function(index, elem){
    costsarr.push($(this).text());
});
	
	
}

else{
	
	$(".testcost").each(function(index, elem){
    costsarr.push($(this).text());
});
	
}



if(costsarr.length >0){
	var costsum = costsarr.reduce(function (a, b) {
		return parseFloat(a) + parseFloat(b);
	}, 0);
}

else var costsum =0;

var totalsarr = [];

if($("td span").hasClass("testtotal1")){
	
	$(".testtotal1").each(function(index, elem){
	
    totalsarr.push($(this).text());
});
	
}

else{
	
	  $(".testtotal").each(function(index, elem){
	
    totalsarr.push($(this).text());
});
	
}



if(totalsarr.length >0){
	var totalssum = totalsarr.reduce(function (a, b) {
		return parseFloat(a) + parseFloat(b);
	}, 0);
}

else  var totalssum =0;



$('#total_timecount').html(timesum);

$('#total_costcount').html("$" + costsum);

$('#total_totalscount').html("$"+totalssum);

	$.ajax({
		type: "POST",
		url: "ajax/ajax_painting.php",
		data: {agentid : $(this).attr("data-crntuser"),locationid: $(this).attr("data-locationid"), colourid: $(this).attr("data-colourid"), pans:$('#cntpans'+ currentid).html(),hoursper: $('#hoursperpanel' + currentid).val(),times:$('#time' + currentid).html(), costper: $('#costperpanel' + currentid).val(),cost:cost,totalcost:totalprice, status: 1 },
		success: function (data) {

		},
	});



});

$(document).on("change", ".radpaint", function () {

	var currentid = $(this).attr("data-colourid");

	if (document.getElementById('paintrad' + currentid).checked == true) {

		var check_status = 1;
		
		if($("td").hasClass("ajax_paintextra")){
			
		jQuery('#cntpans'+currentid).addClass('testcountpanels1');
		jQuery('#time'+currentid).addClass('testtimes1');
		jQuery('#costtd'+currentid).addClass('testcost1');
		jQuery('#totaltd'+currentid).addClass('testtotal1');
			
		}
		else{
			
			 jQuery('#cntpans'+currentid).addClass('testcountpanels');
		jQuery('#time'+currentid).addClass('testtimes');
		jQuery('#costtd'+currentid).addClass('testcost');
		jQuery('#totaltd'+currentid).addClass('testtotal');
			
		}
		
		
		
		

	}

	else {

		var check_status = 0;

		document.getElementById('paintrad' + currentid).checked = false;
		
		if($("td").hasClass("ajax_paintextra")){
			
			jQuery('#cntpans'+currentid).removeClass('testcountpanels1');
		jQuery('#time'+currentid).removeClass('testtimes1');
		jQuery('#costtd'+currentid).removeClass('testcost1');
		jQuery('#totaltd'+currentid).removeClass('testtotal1');
			
		}
		
		else{
			
			 jQuery('#cntpans'+currentid).removeClass('testcountpanels');
		jQuery('#time'+currentid).removeClass('testtimes');
		jQuery('#costtd'+currentid).removeClass('testcost');
		jQuery('#totaltd'+currentid).removeClass('testtotal');
			
		}
		
		 


	}
	
	
	var panarr = [];
	
	if($("td").hasClass("testcountpanels1")){
		
		$(".testcountpanels1").each(function(index, elem){
    panarr.push($(this).text());
 });
	}
	
	else{
		
		 $(".testcountpanels").each(function(index, elem){
    panarr.push($(this).text());
});

		
	}
	
	if(panarr.length >0){
	var pansum = panarr.reduce(function (a, b) {
		return parseFloat(a) + parseFloat(b);
	}, 0);
}
else var pansum =0;
	
	
	var timesarr = [];
	
	if($("td").hasClass("testtimes1")){
		
		$(".testtimes1").each(function(index, elem){
    timesarr.push($(this).text());
 });
	}
	
	else{
		
		 $(".testtimes").each(function(index, elem){
    timesarr.push($(this).text());
});

		
	}
	

	
if(timesarr.length >0){
	var timesum = timesarr.reduce(function (a, b) {
		return parseFloat(a) + parseFloat(b);
	}, 0);
}
else var timesum =0;

var costsarr = [];

if($("td span").hasClass("testcost1")){
	
	
	$(".testcost1").each(function(index, elem){
    costsarr.push($(this).text());
});
	
	
}

else{
	
	$(".testcost").each(function(index, elem){
    costsarr.push($(this).text());
});
	
}



if(costsarr.length >0){
	var costsum = costsarr.reduce(function (a, b) {
		return parseFloat(a) + parseFloat(b);
	}, 0);
}

else var costsum =0;

var totalsarr = [];

if($("td span").hasClass("testtotal1")){
	
	$(".testtotal1").each(function(index, elem){
	
    totalsarr.push($(this).text());
});
	
}

else{
	
	  $(".testtotal").each(function(index, elem){
	
    totalsarr.push($(this).text());
});
	
}



if(totalsarr.length >0){
	var totalssum = totalsarr.reduce(function (a, b) {
		return parseFloat(a) + parseFloat(b);
	}, 0);
}

else  var totalssum =0;


$('#total_panelscount').html(pansum);

$('#total_timecount').html(timesum);

$('#total_costcount').html("$" + costsum);

$('#total_totalscount').html("$" + totalssum);
	

	$.ajax({
		type: "POST",
		url: "ajax/ajax_painting.php",
		data: { locationid: $(this).attr("data-locationid"), colourid: $(this).attr("data-colourid"), check_status: check_status, status: 2 },
		success: function (data) {


		},
	});



	
});


$(document).on("click", ".paintanchor", function () {

	var colourid = $(this).attr("data-colourid");

	var conditionid = $(this).attr("data-conditionid");

	$('#optpanelid').val($(this).attr("data-panelid"));

	$('#optwindowid').val($(this).attr("data-windowid"));
	$.ajax({
		type: "POST",
		url: "ajax/ajax_painting.php",
		data: { status: 3 },
		success: function (data) {

			var $select = $('#paintcondition');

			var $select1 = $('#selectcondition');

			var json_obj = JSON.parse(data);


			$('#paintcondition').empty();

			$('#selectcondition').empty();

			if ((json_obj.colours).length > 0) {

				$.each(json_obj.colours, function (i, res) {

					if (res.colourid == colourid) {

						$select.append('<option data-content="' + res.content + '" value="' + res.colourid + '" selected="selected">' + res.colourname + '</option>');
					}

					else {

						$select.append('<option data-content="' + res.content + '" value="' + res.colourid + '">' + res.colourname + '</option>');
					}

				});

				$('.selectpicker').selectpicker('refresh');

			}

			if ((json_obj.conditions).length > 0) {

				$.each(json_obj.conditions, function (i, res) {

					if (res.conditionid == conditionid) {

						$select1.append('<option  value="' + res.conditionid + '" selected="selected">' + res.name + '</option>');
					}

					else {

						$select1.append('<option  value="' + res.conditionid + '">' + res.name + '</option>');
					}

				});

				$('.selectpicker').selectpicker('refresh');

			}







		},
	});



});
$(document).on("click", "#optbtn", function () {

	$.ajax({
		type: "POST",
		url: "ajax/ajax_painting.php",
		data: { colourid: $('#paintcondition').val(), condition: $('#selectcondition').val(), option: $('input[name="radoptions"]:checked').val(), locationid: $('#locationid').val(), panelid: $('#optpanelid').val(), windowid: $('#optwindowid').val(), status: 4 },
		success: function (data) {

			location.reload();

		},
	});

});

/* 16-06-2020*/

function selectimage(typeid) {

	$('#typeid').val(typeid);

}

function Updatewindow(windowid) {


	$.ajax({
		type: "POST",
		url: "ajax/ajax_update_window_types.php",
		data: { windo: windowid, frame: $('#framcategory').val(), typeid: $('#typeid').val() },
		success: function (data) {

			$('#windowtype' + windowid).html(data);

			$(".close").trigger("click");

		},
	});

}

function bytesToSize(bytes) {
	var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
	if (bytes == 0) return '0 Byte';
	var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
	return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
}

$('#pdfgen').click(function(){
	var utc = new Date().toJSON().slice(0,10).replace(/-/g,'/'); 
	var dates = utc.split("/");

var today = dates[0] + '-' + dates[1] + '-' + dates[2]; 
		swal({
		title: "Do you want add a new quote with an updated date:"+today,
		icon: "warning",
		buttons: true,
		dangerMode: true,
	})
		.then((willDelete) => {if(willDelete){
			
		$('.loader3').show();
		$('#pdf_gen').val(1);

	
document.getElementById("quotedate").value = today;

$('#form-quotes').submit();
	
			
			
			
			}
			
			
		
			})
	
	
		
	
	
	});
$(document).on("change", "#panel-framcategory", function () {


	$.ajax({
		type: "POST",
		url: "ajax/ajax_material_category.php",
		data: { materialCategory: $('#panel-framcategory').val() },

		success: function (data) {

			var $select = $('#styleop');
			var json_obj = $.parseJSON(data)
			$('#styleop').empty();
			$select.append('<option value="select">Choose Your Style</option>');
			for (var i in json_obj) {

				$select.append('<option value="' + json_obj[i].famecategoryid + '">' + json_obj[i].category + '</option>');
			}
			$select.append('<option value="' + $('#panel-framcategory').val() + '">All</option>');

		},
	});

});



$(document).on("click", "#addnewbook", function () {

	

	if(($('input[name="typrad"]:checked').val())=="staff"){

		var status = "leave";

		var locationid = $('#staff').val();

		if($('#staff').val() ==""){

			$('#staff').css("border-color", "red");
			$(".b_startdate").css("border-color", "");
			$(".b_enddate").css("border-color", "");
	
		}
		else if (($('.b_startdate').val() == '000-00-00 00:00:00')||($('.b_startdate').val()=='')) {
			$(".b_startdate").css("border-color", "red");
			$(".b_enddate").css("border-color", "");
		}
		else if ($('.b_enddate').val() == '000-00-00 00:00:00' ||($('.b_enddate').val()=='')) {
			$(".b_enddate").css("border-color", "red");
			$(".b_startdate").css("border-color", "");
		}

		else{

			$.ajax({
				type: "POST",
				url: "ajax/ajax_move_location.php",
				data: { locationid: locationid, status: status, startdate: $('.b_startdate').val(), enddate: $('.b_enddate').val(), alarm_type: $("input[name='leave_type']:checked").val(), booking_notes: $('#new_booking_notes').val() },
				success: function (data) {
					if (data == 1) {
		
						location.reload();
					}
				},
			});
		
		
		
		}
		

		


	}

	else if(($('input[name="typrad"]:checked').val())=="book"){

		var status = "newbooking";
        var locationid = $('#propertty').val();

		if($('#propertty').val() ==""){

			$('#propertty').css("border-color", "red");
			$(".b_startdate").css("border-color", "");
			$(".b_enddate").css("border-color", "");
	
		}
		else if (($('.b_startdate').val() == '000-00-00 00:00:00')||($('.b_startdate').val()=='')) {
			$(".b_startdate").css("border-color", "red");
			$(".b_enddate").css("border-color", "");
		}
		else if ($('.b_enddate').val() == '000-00-00 00:00:00' ||($('.b_enddate').val()=='')) {
			$(".b_enddate").css("border-color", "red");
			$(".b_startdate").css("border-color", "");
		}

		else{

			$.ajax({
				type: "POST",
				url: "ajax/ajax_move_location.php",
				data: { locationid: locationid, status: status, startdate: $('.b_startdate').val(), enddate: $('.b_enddate').val(), alarm_type: $("input[name='book_type']:checked").val(), booking_notes: $('#new_booking_notes').val() },
				success: function (data) {
					if (data == 1) {
		
						location.reload();
					}
				},
			});
		
		
		
		}
		
		

	}

	


});	

$(document).on("change", ".typrad", function () {

	if(($('input[name="typrad"]:checked').val())=="staff"){

		$('#leave_staff').show();

		$('#book_span').hide();

		$('#locspan').hide();

		$('#staffspan').show();
}else{

	$('#leave_staff').hide();

	$('#book_span').show();

	$('#locspan').show();

	$('#staffspan').hide();

}
});

