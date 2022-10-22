// JavaScript Document

$("#team_user_role").change(function(){
	$.ajax({
            type: "POST",
            url: "ajax/ajax_team_access.php",
            data: {role:$('#team_user_role').val(),status:'role'},
            success: function (data) {
			$('#team_user_access').empty().append(data);

            },
        });
  
});

$(".edit-team").click(function(){

	$('#team_id').val($(this).data("id"));
	
	$.ajax({
            type: "POST",
            url: "ajax/ajax_team_access.php",
            data: {teamid:$(this).data("id"),status:'user'},
            success: function (data) {
			
			var obj = JSON.parse(data);
			
			$('#team_user_access').empty().append(obj.acess);

			$("div.id_100 select").val(obj.roleid);
	
			$('.profile-edit-member img').attr('src',obj.profile_pic);
		
			$('#team_first_name').val(obj.first_name);
			$('#team_last_name').val(obj.last_name);
			$('#team_user_name').val(obj.username);
			$('#team_user_phone').val(obj.phone);
			$('#team_user_email').val(obj.email);
			$('#team_user_addres1').val(obj.address1);
			$('#team_user_addres2').val(obj.address2);
			$('#team_user_induction').val(obj.induction);
			$('#team_user_tools').val(obj.tools);
			$('#team_user_uniform').val(obj.uniform);
			$('#team_user_ppe').val(obj.ppe);
		
		 if(obj.induction != null)
			$("#check_induction").prop("checked", true);
			if(obj.tools != null)
			$("#check_tools").prop("checked", true);
			if(obj.uniform != null)
				$("#check_uniform").prop("checked", true);
				if(obj.ppe != null)
				$("#check_ppe").prop("checked", true);
			
		
			$('#team_user_notes').val(obj.notes);
			
		
			$('#team_user_status').val(obj.status);
			$('#team_user_ird').val(obj.ird);
			$('#team_user_pass').val(obj.password);
			$('#spn-drv').html(obj.driving_licence);
			$('#spn-cv').html(obj.cv);	
			$('#spn-other').html(obj.other);								
											
											
	
            },
        });
  
});

$("#team_btn").click(function(){
	if($('#team_first_name').val() == ""){

		$('#team_first_name').css('border','red');

		$( "#team_first_name" ).focus();

	}
	
	else if($('#team_last_name').val() == ""){

		$('#team_last_name').css('border','red');

		$( "#team_last_name" ).focus();

	}
	
	else if($('#team_user_name').val() == ""){

		$('#team_user_name').css('border','red');

		$( "#team_user_name" ).focus();

	}

	else if($('#team_user_pass').val()==""){

		$('#team_user_pass').css('border','red');

		$( "#team_user_pass" ).focus();


	}
	
	else if($('#team_user_phone').val() == ""){

		$('#team_user_phone').css('border','red');

		$( "#team_user_phone" ).focus();

	}

	else if(!numeric_valid($('#team_user_phone').val())){

		$('#team_user_phone').css('border','red');

		$( "#team_user_phone" ).focus();

	}
	
	else if($('#team_user_email').val() == ""){

		$('#team_user_email').css('border','red');

		$( "#team_user_email" ).focus();

	}

	else if(!validate_Email($('#team_user_email').val())){

		$('#team_user_email').css('border','red');

		$( "#team_user_email" ).focus();


	}


	
	else if($('#team_user_ird').val() == ""){

		$('#team_user_ird').css('border','red');

		$( "#focus" ).focus();

	}

	else{

		$('#team-form').submit();
	}
	

});


$(".moreimages").click(function(){
	
	var count = parseInt($('#imagecount').val())+1;
	
	$('#imagecount').val(count);
	
	$('.quote-pages').append('<div class="quote-single-page"><h5>Page'+count+'</h5><img src="'+$(this).attr('data-url')+'"><input type="file" name="userfile'+count+'"></div>');
	
		click_image++;

	
});




function validate_Email(sEmail)
{
	var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    if (filter.test(sEmail)) {
        return true;
    }
    else {
        return false;
    }
}


function numeric_valid(num)
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




