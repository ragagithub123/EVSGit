$("#submit").click(function(){
  if($('#username').val()=="")
		{
			 $('#username').css('border-color', 'red');
		}
		else if($('#password').val()=="")
		{
			  $('#password').css('border-color', 'red');
		}
		else
		{
			   $.ajax({
            type: "POST",
            url: "ajax/login.php",
            data: {username:$('#username').val(),password:$('#password').val()},
            success: function (data) {
													
												if(data == "success")
												{
													 window.location.href="customer-portal.php";
												}
												else
												{
													 $('#msg_succ').html(data);
												}
            },
        });
		}
});