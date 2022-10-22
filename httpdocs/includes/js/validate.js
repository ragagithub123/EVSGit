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
            url: "includes/ajax/ajax_location.php",
            data: {agent_id:value},
            success: function (data) {
                $('#ajax-location').html(data);
            },
        });
}
function getproperties(agentid)
{
	 
	if($('#location').val() == "select")
	{
		 $('#location').css('border-color', 'red');
	}
	
	else
	{
		  	 $.ajax({
            type: "POST",
            url: "includes/ajax/ajax_properties.php",
            data: {agentid:agentid,city:$('#location').val(),status:$('#location_status').val()},
            success: function (data) {
												
                $('#ajax-properties').html(data);
            },
        });
	}
	
}
function getstatus(agentid)
{
	 	$.ajax({
            type: "POST",
            url: "includes/ajax/ajax_loc_status.php",
            data: {agentid:agentid},
            success: function (data) {
												
                $('#ajax-status').html(data);
            },
        });
}
function getreport(value)
{
	
	 $.ajax({
            type: "POST",
            url: "includes/ajax/ajax_report.php",
            data: {location_id:value},
            success: function (data) {
												
                $('#ajax-report').html(data);
            },
        });
}