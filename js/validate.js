function valid1()
{
	  var v=true;
			if($('#value').val()=="")
		{
			  v=false;
					$('#value').css('border-color', 'red');
				
		}
		else
		{
			 v=true;
		}
		return v;
}
function valid()
{
	 var v=true;
		if($('#address').val() == "")
		{
			 v=false;
				$('#address').css('border-color', 'red');
		}
		else if($('#customer').val() == "")
		{
			  v=false;
				 $('#customer').css('border-color', 'red');
					$('#address').css('border-color', '');
					$('#avgprice').css('border-color', '');
					$('#totalprice').css('border-color', '');
					$('#product').css('border-color', '');
					$('#datepicker').css('border-color', '');
		}
		else if($('#value').val() == "")
		{
			  v=false;
					$('#value').css('border-color', '');
				 $('#customer').css('border-color', '');
					$('#address').css('border-color', '');
					$('#avgprice').css('border-color', '');
					$('#totalprice').css('border-color', '');
					$('#product').css('border-color', '');
					$('#datepicker').css('border-color', '');
		}
		else if($('#avgprice').val() == "")
		{
			  v=false;
				 $('#avgprice').css('border-color', 'red');
					$('#customer').css('border-color', '');
					$('#address').css('border-color', '');
					$('#totalprice').css('border-color', '');
					$('#product').css('border-color', '');
					$('#datepicker').css('border-color', '');
		}
	/*	else if(!numeric( $('#avgprice').val()))
		{
			  v=false;
					$('#avgprice').val('');
				 $('#avgprice').css('border-color', 'red');
					 $('#customer').css('border-color', 'red');
					$('#address').css('border-color', '');
					$('#totalprice').css('border-color', '');
					$('#product').css('border-color', '');
					$('#datepicker').css('border-color', '');
		}*/
		else if($('#totalprice').val() == "")
		{
			  v=false;
				 $('#totalprice').css('border-color', 'red');
					 $('#avgprice').css('border-color', '');
					 $('#customer').css('border-color', '');
					$('#address').css('border-color', '');
					$('#product').css('border-color', '');
					$('#datepicker').css('border-color', '');
		}
		/*else if(!numeric( $('#totalprice').val()))
		{
			  v=false;
					$('#totalprice').val('');
				 $('#totalprice').css('border-color', 'red');
					$('#avgprice').css('border-color', 'red');
					 $('#customer').css('border-color', 'red');
					$('#address').css('border-color', '');
					$('#product').css('border-color', '');
					$('#datepicker').css('border-color', '');
		}*/
		else if($('#product').val() == "")
		{
			  v=false;
				 $('#product').css('border-color', 'red');
					$('#totalprice').css('border-color', '');
					$('#avgprice').css('border-color', '');
					 $('#customer').css('border-color', '');
					$('#address').css('border-color', '');
					$('#datepicker').css('border-color', '');
		}
		else if($('#datepicker').val() == "")
		{
			  v=false;
				 $('#datepicker').css('border-color', 'red');
					$('#product').css('border-color', '');
					$('#totalprice').css('border-color', '');
					$('#avgprice').css('border-color', '');
					 $('#customer').css('border-color', '');
					$('#address').css('border-color', '');
		}
		
		else
		{
			 v=true;
				
		}
		return v;
}

function numeric(num)
{
	var v=true;
	var vchar="0123456789";
	for(i=0;i<num.length;i++)
	{
		var c=num.charAt(i);
		if(vchar.indexOf(c)==-1)
		{
			v=false;
			break;
		}
	}
	return v;
}

function getdata(value)
{
	 $.ajax({
            type: "POST",
            url: "ajax_res.php",
            data: {cust_name:value},
            success: function (data) {
                $('#ajax-result').html(data);
            },
        });
}

function getlocation(value)
{
	 $.ajax({
            type: "POST",
            url: "ajax_location.php",
            data: {agent_id:value},
            success: function (data) {
                $('#ajax-location').html(data);
            },
        });
}
function getproperties()
{
	 
	if($('#location').val() == "select")
	{
		 $('#location').css('border-color', 'red');
	}
	if($('#location_status').val() == "select")
	{
		 $('#location_status').css('border-color', 'red');
	}
	else
	{
		 $('#location').css('border-color', '');
			$('#location_status').css('border-color', '');
		  	 $.ajax({
            type: "POST",
            url: "ajax_properties.php",
            data: {agentid:$('#agents').val(),city:$('#location').val(),status:$('#location_status').val()},
            success: function (data) {
												
                $('#ajax-properties').html(data);
            },
        });
								
							
								
								
								
	}
	
	
}


function getprop(value)
{
	
	  
		  	 $.ajax({
            type: "POST",
            url: "ajax_list_properties.php",
            data: {city:value,statusCode:0},
            success: function (data) {
												document.getElementById('list-prop').style.display="none";
												$('#result-prop').html(data);
            },
        });
								
							
	

}
function generatetime(cnt,locationid,status)
{
	  if(document.getElementById("chk_stat"+cnt).checked == true)
			{
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
							var datesend= yyyy + '-' + mm + '-' + dd;
							var timesend=hour+":"+minutes;
							$('#hr_time'+cnt).html(hour+":"+minutes+" "+today);
							$('#p_status'+cnt).css('font-weight','bold');
							$('#hr_time'+cnt).css('font-weight','bold');
			}
			else
			{
				 $('#hr_time'+cnt).html("00:00 00-00-0000");
					var datesend="00-00-0000";
					var timesend="00:00";
					$('#p_status'+cnt).css('font-weight','normal');
					$('#hr_time'+cnt).css('font-weight','normal');
			}
			
			 $.ajax({
            type: "POST",
            url: "ajax_update_status.php",
            data: {datesend:datesend,timesend:timesend,status:status,locationid:locationid},
            success: function (data) {
											 
												// alert(data);		
											
            },
        });

	 
	
	 
}
function update()
{
	  var info=new Array();
	$('input[name="update_arry[]"]').each(function(){
		
		 info.push($(this).val());
});
//alert(JSON.stringify(info));
  $.ajax({
            type: "POST",
            url: "ajax_update_product.php",
            data: {data:info},
            success: function (data) {
											 
												 //alert(data);		
											
            },
        });

}
function get_status_prop(value)
{
	 $.ajax({
            type: "POST",
            url: "ajax_status_properties.php",
            data: {jobstatus:value,city:$('#location').val()},
            success: function (data) {
													
												document.getElementById('list-prop').style.display="none";
												$('#result-prop').html(data);
            },
        });
								
}
function getproperty(value)
{
	  //alert(window.location.href );
	 //alert(this.href.substring(this.href.lastIndexOf('/') + 1));
	  $.ajax({
            type: "POST",
            url: "ajax_list_properties.php",
            data: {locationid:value,statusCode:1},
            success: function (data) {
									
												$('#res_prop_detail').html(data);
            },
        });
}

function getstatus(agentid)
{
	
	  
	 	$.ajax({
            type: "POST",
            url: "ajax_loc_status.php",
            data: {agentid:agentid},
            success: function (data) {
												
                $('#ajax-status').html(data);
            },
        });
}
/*function gethourlabour(cost,count,hour,product,windowid)
{
	//alert(windowid);
	//alert(hour);
	 if(cost == 0)
		{
			     var total_price=(parseFloat($('#total_price').html())- (parseFloat($('#cost_val'+count).html()))).toFixed(2);
			     var total_hours=(parseFloat($('#total_hrs').html())- (parseFloat( $('#hour_val'+count).html()))).toFixed(2);
							
		}
		else
		{
			   var new_price=parseFloat($('#total_price').html())-(parseFloat($('#cost_val'+count).html()));
						var new_hour=parseFloat($('#total_hrs').html())-(parseFloat($('#hour_val'+count).html()));
			  	var total_price=(parseFloat(cost)+new_price).toFixed(2);
			   var total_hours=(parseFloat(hour)+new_hour).toFixed(2);
					
		}
	  $('#cost_val'+count).html(cost);
		 $('#hour_val'+count).html(hour);
		 $('#total_price').html(total_price);
			$('#total_hrs').html(total_hours);
			$('#new_val'+count).val(cost+"@"+hour+"@"+product+"@"+windowid);
			var btns=new Array();
			btns.push('HOLD','SDG','MAXe','XCLe','EVSx2','EVSx3');
			for(i=0;i<btns.length;i++)
			{
				$("#btn_"+btns[i]+count).css("background-color", "#999");
			}
			$("#btn_"+product+count).css("background-color", "#17a2b8");
			
}*/

$("#list-prop").change(function(){

	
					if($('#list-prop').val()=='Select Property' || $('#list-prop').val()=='')
					{
						   
								
								 $('#list-prop').css('border-color', 'red');
									
					}
					else
					{
						  $('#list-prop').css('border-color', '#548da5');
						  $.ajax({
											
																				type: "POST",
																				url: "ajax/ajax_report.php",
																				data: {location_id:$('#list-prop').val(),list_value:$('#list').val()},
																				success: function (data) {
																					
																								$('#ajax-report').html(data);
																				},
																});
						 
					}
	
});
/*$("#list").change(function(){
	if($('#list-prop').val()=="Select Property" ||$('#list-prop').val()=="" )
	{
		  $('#list-prop').css('border-color', 'red');
	}
	else
	{
		 $('#list-prop').css('border-color', '');
		  $.ajax({
            type: "POST",
            url: "ajax/ajax_report.php",
            data: {location_id:$('#list-prop').val(),list_value:$('#list').val()},
            success: function (data) {
													
                $('#ajax-report').html(data);
            },
        });
	}
	
});*/

function getreport(value)
{
	
	if($('#location').val() == "select")
	{
		 $('#location').css('border-color', 'red');
	}
	if($('#location_status').val() == "select")
	{
		 $('#location_status').css('border-color', 'red');
	}
	if($('#agents').val() == "select")
	{
		 $('#agents').css('border-color', 'red');
	}
		if($('#list-prop').val()=="Select Property" ||$('#list-prop').val()=="" )
	{
		  $('#list-prop').css('border-color', 'red');
	}
	else
	{
		
		  $.ajax({
            type: "POST",
            url: "ajax_report.php",
            data: {location_id:value},
            success: function (data) {
												
                $('#ajax-report').html(data);
            },
        });
	}
	
	 
}



function validglass(){
	$('#supplier-glass').css('border-color', '');
	$('#description-glass').css('border-color', '');
	$('#date-glass').css('border-color', '');
	$('#invoice-glass').css('border-color', '');
	$('#units-glass').css('border-color', '');
	$('#total-glass').css('border-color', '');

	if($('#supplier-glass').val()==""){

		$('#supplier-glass').css('border-color', 'red');
		
	}
	else if($('#description-glass').val()==""){

		$('#description-glass').css('border-color', 'red');
	}
	else if($('#date-glass').val()==""){

		$('#date-glass').css('border-color', 'red');
	}
	else if($('#invoice-glass').val()==""){

		$('#invoice-glass').css('border-color', 'red');
	}
	else if($('#units-glass').val()==""){

		$('#units-glass').css('border-color', 'red');
	}
	else if(($('#total-glass').val()=="")){

		$('#total-glass').css('border-color', 'red');
	}
	else{
		$('#invoiceform').submit();
	}
	

}
function validextra(){

	$('#supplier-extra').css('border-color', '');
	$('#description-extra').css('border-color', '');
	$('#date-extra').css('border-color', '');
	$('#invoice-extra').css('border-color', '');
	$('#units-extra').css('border-color', '');
	$('#total-extra').css('border-color', '');

	if($('#supplier-extra').val()==""){

		$('#supplier-extra').css('border-color', 'red');
		
	}
	else if($('#description-extra').val()==""){

		$('#description-extra').css('border-color', 'red');
	}
	else if($('#date-extra').val()==""){

		$('#date-extra').css('border-color', 'red');
	}
	else if($('#invoice-extra').val()==""){

		$('#invoice-extra').css('border-color', 'red');
	}
	else if($('#units-extra').val()==""){

		$('#units-extra').css('border-color', 'red');
	}
	else if(($('#total-extra').val()=="") ){

		$('#total-extra').css('border-color', 'red');
	}
	else{
		$('#invoiceformextra').submit();
	}
	


}

function updateglass(invoiceid){

	$('#supplier-glass'+invoiceid).css('border-color', '');
	$('#description-glass'+invoiceid).css('border-color', '');
	$('#date-glass'+invoiceid).css('border-color', '');
	$('#invoice-glass'+invoiceid).css('border-color', '');
	$('#units-glass'+invoiceid).css('border-color', '');
	$('#total-glass'+invoiceid).css('border-color', '');

	if($('#supplier-glass'+invoiceid).val()==""){

		$('#supplier-glass'+invoiceid).css('border-color', 'red');
		
	}
	else if($('#description-glass'+invoiceid).val()==""){

		$('#description-glass'+invoiceid).css('border-color', 'red');
	}
	else if($('#date-glass'+invoiceid).val()==""){

		$('#date-glass'+invoiceid).css('border-color', 'red');
	}
	else if($('#invoice-glass'+invoiceid).val()==""){

		$('#invoice-glass'+invoiceid).css('border-color', 'red');
	}
	else if($('#units-glass'+invoiceid).val()==""){

		$('#units-glass'+invoiceid).css('border-color', 'red');
	}
	else if(($('#total-glass'+invoiceid).val()=="")){

		$('#total-glass'+invoiceid).css('border-color', 'red');
	}
	else{
		$('#editinvoice'+invoiceid).submit();
	}
	


}

function ShowProfilepopup(type){

	$('#headTitle').html(type);

	$('#invicetype').val(type);

	$('#myModal').modal('show');



}


function isNumberKey(evt)
{
   var charCode = (evt.which) ? evt.which : evt.keyCode;
   if (charCode != 46 && charCode > 31 
	 && (charCode < 48 || charCode > 57))
	  return false;

   return true;
}