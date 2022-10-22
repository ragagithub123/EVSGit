// JavaScript Document

$(document).on("click", ".report-dates", function () {

	$('.loader3').show();

	var startdate = $(this).attr("data-startdate");

	var enddate = $(this).attr("data-enddate");

	//alert(startdate);alert(enddate);
	$.ajax({
		type: "POST",
		url: "ajax/ajax_report_generation.php",
		data: { startdate: startdate, enddate: enddate },
		success: function (data) {

			$('.loader3').hide();

			$('.week-slider-single').removeClass('active');

			$('#disp' + enddate).addClass('active');

			$('#report-ajax').html(data);

		},
	});


});

$(document).on("click","#sheetaddlo",function(){
	
	if($('#agent-location').val()!=''){
		
		  	$.ajax({
		type: "POST",
		url: "ajax/ajax_staff_report_generation.php",
		data: { location: $('#agent-location').val(),agentid:$('#agentid').val(),weekenddate:$('#weekenddate').val(),status: 'addloc'},
		success: function (data) {	
		swal("Location Added");
		$("#totalli").before('<li><span>'+$('#agent-location').val()+'</span><div class="form-group"><input type="text" class="form-control form-box workshop_prephr spendhr" name="prephr[]" id="prep'+data+'" value="0"></div><div class="form-group"><input type="text" class="form-control form-box workshop_make spendhr" name="makehr[]" id="make'+data+'" value="0"></div><div class="form-group"><input type="text" class="form-control form-box workshop_install spendhr" name="installhr[]" id="install'+data+'" value="0"></div><div class="form-group"><input type="text" class="form-control form-box workshop_extra spendhr" name="extrahr[]" id="extra'+data+'" value="0"></div><input type="hidden" name="locationtrackid[]" value="'+data+'"/></li>');
		if($('#checkval').val()==0)
		$("#btnspn").html('<button class="btn btn-primary" id="trackupdate" value="updatetrack">Update</button>');
			},
	});
		
		
		}
	

	else{
		$('#agent-location').css('border-color', 'red');
		}
	
	
	});

$(document).on("click", ".staff-dates", function () {

	$('.loader3').show();

	var startdate = $(this).attr("data-staffstartdate");

	var enddate = $(this).attr("data-staffenddate");

	$.ajax({
		type: "POST",
		url: "ajax/ajax_staff_report_generation.php",
		data: { startdate: startdate, enddate: enddate, status: 'display' },
		success: function (data) {

			$('.loader3').hide();

			$('.week-slider-single').removeClass('active');

			$('#dispstaff' + enddate).addClass('active');



			$('#week-end-date').val(enddate);

			$('#staff-ajax').html(data);



		},
	});


});


$(document).on("change", ".staff-text", function () {
	var filedcnt = $(this).attr("data-count");
	var crnt_id = this.id;
	$.ajax({
		type: "POST",
		url: "ajax/ajax_staff_report_generation.php",
		data: { weekdate: $('#week-end-date').val(), filedname: $(this).attr("data-filedname"), filedvalue: $('#staffname' + filedcnt + '').val(), filedcnt: filedcnt, staffid: $(this).attr("data-staff"), status: 'staff_insert' },
		success: function (res) { 
			$('#' + crnt_id).attr('data-staff', parseInt(res));
			$('#anchor' + filedcnt).attr('data-staff', parseInt(res));
			$('#bonus_hrs' + filedcnt).attr('data-staff', parseInt(res));
			$('#non_bonus_hrs' + filedcnt).attr('data-staff', parseInt(res));
			$('#mon' + filedcnt).attr('data-staff', parseInt(res));
			$('#tue' + filedcnt).attr('data-staff', parseInt(res));
			$('#wed' + filedcnt).attr('data-staff', parseInt(res));
			$('#thu' + filedcnt).attr('data-staff', parseInt(res));
			$('#fri' + filedcnt).attr('data-staff', parseInt(res));
			$('#sat' + filedcnt).attr('data-staff', parseInt(res));
			$('#sun' + filedcnt).attr('data-staff', parseInt(res));
			$('#comments' + filedcnt).attr('data-staff', parseInt(res));



		},
	});
});

$(document).on("click", ".remove-bonus", function () {

	swal({
		title: "Do you want to delete this data?",
		text: "Once deleted, you will not be able to recover this data!",
		icon: "warning",
		buttons: true,
		dangerMode: true,
	})
		.then((willDelete) => {
			var staffid = $(this).attr("data-staff");
			var filedcnt = $(this).attr("data-filedcnt");
			$.ajax({
				type: "POST",
				url: "ajax/ajax_staff_report_generation.php",
				data: { staffid: staffid, weekdate: $('#week-end-date').val(), status: 'remove' },
				success: function (data) {
					$('#tr' + filedcnt + '').remove();
					var obj = JSON.parse(data);
					$('#hourly' + $('#week-end-date').val() + '').html(obj.hourlybonus_data);
					$('#non_bonous_total').html(obj.total_nonbonus_data);
					$('#bonous_total').html(obj.total_bonus_data);
					$('#hourly_bonus').html(obj.hourlybonus_data);
					$('#bonus' + $('#week-end-date').val() + '').html(obj.total_bonus_data);


				},
			});
		});


});
$(document).on("change", ".non_bonus_hrs", function () {
	var filedcnt = $(this).attr("data-count");

	$.ajax({
		type: "POST",
		url: "ajax/ajax_staff_report_generation.php",
		data: { weekdate: $('#week-end-date').val(), filedname: $(this).attr("data-filedname"), filedvalue: $('#non_bonus_hrs' + filedcnt + '').val(), filedcnt: $(this).attr("data-count"), staffid: $(this).attr("data-staff"), status: 'insert' },
		success: function (data) {
			var obj = JSON.parse(data);

			$('#bonus_hrs' + filedcnt + '').val(obj.bonustask);
			$('#non_bonous_total').html(obj.non_bonus);
			$('#bonous_total').html(obj.bonus_hr);
			$('#hourly_bonus').html(obj.hourlybonus);
			$('#hourly' + $('#week-end-date').val() + '').html(obj.hourlybonus);
			$('#bonus' + $('#week-end-date').val() + '').html(obj.bonus_hr);


		},
	});
});
$(document).on("change", ".mon", function () {
	var filedcnt = $(this).attr("data-count");

	$.ajax({
		type: "POST",
		url: "ajax/ajax_staff_report_generation.php",
		data: { weekdate: $('#week-end-date').val(), filedname: $(this).attr("data-filedname"), filedvalue: $('#mon' + filedcnt + '').val(), filedcnt: $(this).attr("data-count"), staffid: $(this).attr("data-staff"), status: 'insert' },
		success: function (data) {
			var obj = JSON.parse(data);
			$('#bonus_hrs' + filedcnt + '').val(obj.bonustask);
			$('#non_bonous_total').html(obj.non_bonus);
			$('#bonous_total').html(obj.bonus_hr);
			$('#hourly_bonus').html(obj.hourlybonus);
			$('#hourly' + $('#week-end-date').val() + '').html(obj.hourlybonus);
			$('#bonus' + $('#week-end-date').val() + '').html(obj.bonus_hr);

		},
	});
});

$(document).on("change", ".tue", function () {
	var filedcnt = $(this).attr("data-count");

	$.ajax({
		type: "POST",
		url: "ajax/ajax_staff_report_generation.php",
		data: { weekdate: $('#week-end-date').val(), filedname: $(this).attr("data-filedname"), filedvalue: $('#tue' + filedcnt + '').val(), filedcnt: $(this).attr("data-count"), staffid: $(this).attr("data-staff"), status: 'insert' },
		success: function (data) {
			var obj = JSON.parse(data);
			$('#bonus_hrs' + filedcnt + '').val(obj.bonustask);
			$('#non_bonous_total').html(obj.non_bonus);
			$('#bonous_total').html(obj.bonus_hr);
			$('#hourly_bonus').html(obj.hourlybonus);
			$('#hourly' + $('#week-end-date').val() + '').html(obj.hourlybonus);
			$('#bonus' + $('#week-end-date').val() + '').html(obj.bonus_hr);

		},
	});
});

$(document).on("change", ".wed", function () {
	var filedcnt = $(this).attr("data-count");

	$.ajax({
		type: "POST",
		url: "ajax/ajax_staff_report_generation.php",
		data: { weekdate: $('#week-end-date').val(), filedname: $(this).attr("data-filedname"), filedvalue: $('#wed' + filedcnt + '').val(), filedcnt: $(this).attr("data-count"), staffid: $(this).attr("data-staff"), status: 'insert' },
		success: function (data) {
			var obj = JSON.parse(data);
			$('#bonus_hrs' + filedcnt + '').val(obj.bonustask);
			$('#non_bonous_total').html(obj.non_bonus);
			$('#bonous_total').html(obj.bonus_hr);
			$('#hourly_bonus').html(obj.hourlybonus);
			$('#hourly' + $('#week-end-date').val() + '').html(obj.hourlybonus);
			$('#bonus' + $('#week-end-date').val() + '').html(obj.bonus_hr);

		},
	});
});

$(document).on("change", ".thu", function () {
	var filedcnt = $(this).attr("data-count");

	$.ajax({
		type: "POST",
		url: "ajax/ajax_staff_report_generation.php",
		data: { weekdate: $('#week-end-date').val(), filedname: $(this).attr("data-filedname"), filedvalue: $('#thu' + filedcnt + '').val(), filedcnt: $(this).attr("data-count"), staffid: $(this).attr("data-staff"), status: 'insert' },
		success: function (data) {
			var obj = JSON.parse(data);
			$('#bonus_hrs' + filedcnt + '').val(obj.bonustask);
			$('#non_bonous_total').html(obj.non_bonus);
			$('#bonous_total').html(obj.bonus_hr);
			$('#hourly_bonus').html(obj.hourlybonus);
			$('#hourly' + $('#week-end-date').val() + '').html(obj.hourlybonus);
			$('#bonus' + $('#week-end-date').val() + '').html(obj.bonus_hr);

		},
	});
});
$(document).on("change", ".fri", function () {
	var filedcnt = $(this).attr("data-count");

	$.ajax({
		type: "POST",
		url: "ajax/ajax_staff_report_generation.php",
		data: { weekdate: $('#week-end-date').val(), filedname: $(this).attr("data-filedname"), filedvalue: $('#fri' + filedcnt + '').val(), filedcnt: $(this).attr("data-count"), staffid: $(this).attr("data-staff"), status: 'insert' },
		success: function (data) {
			var obj = JSON.parse(data);
			$('#bonus_hrs' + filedcnt + '').val(obj.bonustask);
			$('#non_bonous_total').html(obj.non_bonus);
			$('#bonous_total').html(obj.bonus_hr);
			$('#hourly_bonus').html(obj.hourlybonus);
			$('#hourly' + $('#week-end-date').val() + '').html(obj.hourlybonus);
			$('#bonus' + $('#week-end-date').val() + '').html(obj.bonus_hr);

		},
	});
});
$(document).on("change", ".sat", function () {
	var filedcnt = $(this).attr("data-count");

	$.ajax({
		type: "POST",
		url: "ajax/ajax_staff_report_generation.php",
		data: { weekdate: $('#week-end-date').val(), filedname: $(this).attr("data-filedname"), filedvalue: $('#sat' + filedcnt + '').val(), filedcnt: $(this).attr("data-count"), staffid: $(this).attr("data-staff"), status: 'insert' },
		success: function (data) {
			var obj = JSON.parse(data);
			$('#bonus_hrs' + filedcnt + '').val(obj.bonustask);
			$('#non_bonous_total').html(obj.non_bonus);
			$('#bonous_total').html(obj.bonus_hr);
			$('#hourly_bonus').html(obj.hourlybonus);
			$('#hourly' + $('#week-end-date').val() + '').html(obj.hourlybonus);
			$('#bonus' + $('#week-end-date').val() + '').html(obj.bonus_hr);

		},
	});
});
$(document).on("change", ".sun", function () {
	var filedcnt = $(this).attr("data-count");

	$.ajax({
		type: "POST",
		url: "ajax/ajax_staff_report_generation.php",
		data: { weekdate: $('#week-end-date').val(), filedname: $(this).attr("data-filedname"), filedvalue: $('#sun' + filedcnt + '').val(), filedcnt: $(this).attr("data-count"), staffid: $(this).attr("data-staff"), status: 'insert' },
		success: function (data) {
			var obj = JSON.parse(data);
			$('#bonus_hrs' + filedcnt + '').val(obj.bonustask);
			$('#non_bonous_total').html(obj.non_bonus);
			$('#bonous_total').html(obj.bonus_hr);
			$('#hourly_bonus').html(obj.hourlybonus);
			$('#hourly' + $('#week-end-date').val() + '').html(obj.hourlybonus);
			$('#bonus' + $('#week-end-date').val() + '').html(obj.bonus_hr);

		},
	});
});
$(document).on("change", ".staff-comment", function () {
	var filedcnt = $(this).attr("data-count");
	$.ajax({
		type: "POST",
		url: "ajax/ajax_staff_report_generation.php",
		data: { weekdate: $('#week-end-date').val(), filedname: $(this).attr("data-filedname"), filedvalue: $('#comments' + filedcnt + '').val(), filedcnt: $(this).attr("data-count"), staffid: $(this).attr("data-staff"), status: 'insert' },
		success: function (data) {



		},
	});
});

var clickcnt = 0;
$(document).on("click", ".staff-add", function () {

	clickcnt++;

	var cnt = parseInt($('#field_count').val()) + parseInt(clickcnt);


	$('#staff-table').append('<tr id="tr' + cnt + '"><td><div class="staff-name"><input type="text" class="form-control staff-text" data-filedname="staff_name" id="staffname' + cnt + '" data-count="' + cnt + '" data-staff="0"> <a href="javascript:void(0)" class="remove-bonus" data-staff="0" data-filedcnt="' + cnt + '" id="anchor' + cnt + '"><i class="fa fa-times"></i></a></div></td><td><input type="text" class="form-control bonus_hrs" data-filedname="bonus_hrs" id="bonus_hrs' + cnt + '" data-count="' + cnt + '" readonly="readonly"></td><td><input type="text" class="form-control non_bonus_hrs" data-filedname="non_bonus_hrs" id="non_bonus_hrs' + cnt + '" data-count="' + cnt + '"></td><td><input type="text" class="form-control mon" data-filedname="mon" id="mon' + cnt + '" data-count="' + cnt + '"></td><td><input type="text" class="form-control tue" data-filedname="tue" id="tue' + cnt + '" data-count="' + cnt + '"></td><td><input type="text" class="form-control wed" data-filedname="wed" id="wed' + cnt + '" data-count="' + cnt + '"></td><td><input type="text" class="form-control thu" data-filedname="thu" id="thu' + cnt + '" data-count="' + cnt + '"></td><td><input type="text" class="form-control fri" data-filedname="fri"id="fri' + cnt + '" data-count="' + cnt + '"></td><td><input type="text" class="form-control sat" data-filedname="sat" id="sat' + cnt + '" data-count="' + cnt + '"></td><td><input type="text" class="form-control sun" data-filedname="sun" id="sun' + cnt + '" data-count="' + cnt + '"></td><td><input type="text" class="form-control staff-comment" data-filedname="comments" id="comments' + cnt + '" data-count="' + cnt + '"></td></tr>');

});

$(document).on("click", ".track", function () {

	$('.loader3').show();

	var locidarr = [];
	$("input:hidden.locidclas").each(function() {
		locidarr.push($(this).val());
	});
		//alert(lst_to.toString());
	
	$('#dispday').html(getnameday($(this).attr("data-trackfiledname")));
	var datname = $(this).attr("data-trackfiledname");
	var cntid = $(this).attr("data-trackcount");
	var nameid = datname+cntid;
	$('#workshopexisthr').val($('#'+nameid).val());
	$('#trackday').val($(this).attr("data-trackfiledname"));

	$('#trackstaffid').val($(this).attr("data-trackstaff"));

	$('#filedtrackcnt').val($(this).attr("data-trackcount"));

	$('#staffreportid').val($(this).attr("data-staffreportid"));
	$.ajax({
		type: "POST",
		url: "ajax/ajax_staff_report_generation.php",
		data: { staffreportid: $(this).attr("data-staffreportid"), trackday: $(this).attr("data-trackfiledname"), filedtrackcnt: $(this).attr("data-trackcount"), trackstaffid: $(this).attr("data-trackstaff"), enddate: $('#weekenddate').val(), status: 'disptrack' },
		success: function (data) { 
			
			var datas = JSON.parse(data);

			if ((datas.details).length > 0) {
				$.each(datas.details, function (i, res) {

					var index = locidarr.indexOf(res.locationid);
					if (index > -1) {
						locidarr.splice(index, 1);
					}
					
					
						$('#prep' + res.locationid).css({ "background-color": "" });
						
						$('#make' + res.locationid).css({ "background-color": "" });
							
						$('#install' + res.locationid).css({ "background-color": "" });
						
						$('#extra' + res.locationid).css({ "background-color": "" });

					$('#prep' + res.locationid).val(res.prep);
					
				 $('#make' + res.locationid).val(res.make);
					
					$('#install' + res.locationid).val(res.install);
					
					$('#extra' + res.locationid).val(res.extra);

					if (res.prep != 0 && res.prep != 0.00)

						$('#prep' + res.locationid).css({ "background-color": "#96faf8" });
						
					
						
					if(res.make != 0 || res.make != 0.00)	
					
					$('#make' + res.locationid).css({ "background-color": "#96faf8" });
					
					if(res.install != 0 && res.install != 0.00)	
					
					$('#install' + res.locationid).css({ "background-color": "#96faf8" });
					
					if(res.extra != 0 && res.extra != 0.00)	
					
					$('#extra' + res.locationid).css({ "background-color": "#96faf8" });
     
  
				});

                 
	
				  for (let k = 0; k < (locidarr.length); k++){
					
					$('#prep' + locidarr[k]).val(0.00);
					
				   $('#make' + locidarr[k]).val(0.00);
					
					$('#install' + locidarr[k]).val(0.00);
					
					$('#extra' + locidarr[k]).val(0.00);

					$('#prep' + locidarr[k]).css({ "background-color": "" });
						
					$('#make' + locidarr[k]).css({ "background-color": "" });
							
					$('#install' + locidarr[k]).css({ "background-color": "" });
						
					$('#extra' + locidarr[k]).css({ "background-color": "" });


				}
				
				

			}

			else {
			
			  $('input[name="prephr[]"]').val(0.00); 

               $('input[name="makehr[]"]').val(0.00);
					
				 $('input[name="installhr[]"]').val(0.00);
					
					$('input[name="extrahr[]"]').val(0.00);
						
					$('input[name="prephr[]"]').css({ "background-color": "" });
					
					$('input[name="makehr[]"]').css({ "background-color": "" });
						
				$('input[name="installhr[]"]').css({ "background-color": "" });
							
					$('input[name="extrahr[]"]').css({ "background-color": "" });
			
				$('.workshop').css({ "background-color": "" });

				$('.workshop').val(0);
				
			
			}

	   
					

			$('#worktotal').val(datas.workshop_hr);
			$('#workshophr').val(datas.balancehr);

		},
	});

	$('.loader3').hide();


});

$(document).on("click", "#trackupdate", function () {

	$('.loader3').show();

	var loc = new Array();

	if($('#disp_status').val()==0){

		$("input[name='locationtrackids[]']").each(function () {
			loc.push($(this).val());
		});


	}

	else{

		$("input[name='locationtrackid[]']").each(function () {
			loc.push($(this).val());
		});
	}

	

	var prephr = new Array();
	$("input[name='prephr[]']").each(function () {
		prephr.push($(this).val());
	});
	
	
	var makehr = new Array();
	$("input[name='makehr[]']").each(function () {
		makehr.push($(this).val());
	});
	
	var installhr = new Array();
	$("input[name='installhr[]']").each(function () {
		installhr.push($(this).val());
	});
	
	var extrahr = new Array();
	$("input[name='extrahr[]']").each(function () {
		extrahr.push($(this).val());
	});
	
	

	var day = $('#trackday').val();
	var cnt = $('#filedtrackcnt').val();
	var outputid = day + cnt;
	$.ajax({
		type: "POST",
		url: "ajax/ajax_staff_report_generation.php",
		data: {prephr:prephr,makehr:makehr,installhr:installhr,extrahr:extrahr,staffreportid: $('#staffreportid').val(), trackday: $('#trackday').val(), filedtrackcnt: $('#filedtrackcnt').val(), trackstaffid: $('#trackstaffid').val(), enddate: $('#weekenddate').val(), locationid: loc,status: 'track' },
		success: function (data) {

			//alert(data);

			var res = data.split("@");



			$('.loader3').hide();

			//$('#' + outputid).val(res[0]);

			$('#' + outputid).css({ "background-color": "#96faf8" });

			$('#bonus_hrs' + cnt).val(res[1]);

			$('#bonous_total').html(res[2]);

			$('#hourly_bonus').html(res[3]);
			
			
			$('#bonus' + $('#weekenddate').val()).html(res[2]);

			$('#hourly' + $('#weekenddate').val()).html(res[3]);

			$(".close").trigger("click");

		},
	});


});

/*$(document).on("keyup", ".workshop_prephr", function () {

	var prephr = new Array();
	$("input[name='prephr[]']").each(function () {
		prephr.push($(this).val());
	});
	var sum = prephr.reduce(function (a, b) {
		return parseFloat(a) + parseFloat(b);
	}, 0);

	if($('#workshophr').val()!=0 ){
		
		var balance = parseFloat($('#workshopexisthr').val()) - (parseFloat(sum)+parseFloat($('#maketotal').val())+parseFloat($('#installtotal').val())+parseFloat($('#extratotal').val()));
		$('#workshophr').val(balance);
		}


	$('#preptotal').val(sum);
});*/


$(document).on("keyup", ".spendhr", function () {

	var makehr = new Array();
	
	
	$("input[name='makehr[]']").each(function () {
		if($(this).val()!='')
		makehr.push($(this).val());
	});
	
	var summake = makehr.reduce(function (a, b) { 
		return parseFloat(a) + parseFloat(b);
	}, 0);
  
	
	var prephr = new Array();
	$("input[name='prephr[]']").each(function () {
		if($(this).val()!='')
		prephr.push($(this).val());
	}); 
	var sumprep = prephr.reduce(function (a, b) {
	
		return parseFloat(a) + parseFloat(b);
	}, 0);
	
	
		var installhr = new Array();
	$("input[name='installhr[]']").each(function () {
		if($(this).val()!='')
		installhr.push($(this).val());
	});
	var suminstall = installhr.reduce(function (a, b) {
		return parseFloat(a) + parseFloat(b);
	}, 0);
	
	
	var extrahr = new Array();
	$("input[name='extrahr[]']").each(function () {
		if($(this).val()!='')
		extrahr.push($(this).val());
	});
	var sumextra = extrahr.reduce(function (a, b) {
		return parseFloat(a) + parseFloat(b);
	}, 0);
	

	
	if(($('#workshophr').val())>0){
	
	var totalhrs = parseFloat(summake)+parseFloat(sumprep)+parseFloat(suminstall)+parseFloat(sumextra);
		
		var balance = parseFloat($('#workshopexisthr').val()) - totalhrs;
			$('#workshophr').val(balance);
		}


	//$('#worktotal').val(totalhrs);
});

function getnameday(day){
	
	if(day == 'mon')
	
	var dayname = 'Monday';
	
		else if(day == 'tue')
	
	var dayname = 'Tuesday';
	
	else if(day == 'wed')
	
	var dayname = 'Wednesday';
	
	else if(day == 'thu')
	
	var dayname = 'Thursday';
	
	else if(day == 'fri')
	
	var dayname = 'Friday';
	
		else if(day == 'sat')
	
	var dayname = 'Saturday';
	
	else if(day == 'sun')
	
	var dayname = 'Sunday';
	
	return dayname;
	}

