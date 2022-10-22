// JavaScript Document

$("#unit_name").change(function () {
	$.ajax({
		type: "POST",
		url: "ajax/ajax_get_tag.php",
		data: { unit_name: $('#unit_name').val() },
		success: function (data) {
			$('#unit_tag').val(data);
		},
	});
});



$("#update_settings").click(function () {
	if ($('#business_name').val() == '') {
		$('#business_name').css('border-color', 'red');
		$('#business_name').focus();
	}
	else if ($('#first_name').val() == '') {
		$('#first_name').css('border-color', 'red');
		$('#first_name').focus();
	}


	else if ($('#last_name').val() == '') {
		$('#last_name').css('border-color', 'red');
		$('#last_name').focus();
	}
	else if ($('#email').val() == '') {
		$('#email').css('border-color', 'red');
		$('#email').focus();

	}
	else if (!validateEmail($('#email').val())) {
		$('#email').css('border-color', 'red');
		$('#email').focus();
	}
	else if ($('#unit_number').val() == '') {
		$('#unit_number').css('border-color', 'red');
		$('#unit_number').focus();
	}
	else if (!numeric($('#unit_number').val())) {
		$('#unit_number').css('border-color', 'red');
		$('#unit_number').focus();
	}

	else if ($('#street').val() == '') {
		$('#street').css('border-color', 'red');
		$('#street').focus();
	}
	else if ($('#suburb').val() == '') {
		$('#suburb').css('border-color', 'red');
		$('#suburb').focus();
	}
	else if ($('#city').val() == '') {
		$('#city').css('border-color', 'red');
		$('#city').focus();
	}
	else if ($('#postcode').val() == '') {
		$('#postcode').css('border-color', 'red');
		$('#postcode').focus();
	}

	else if ($('#phone').val() == '') {
		$('#phone').css('border-color', 'red');
		$('#phone').focus();
	}
	else if (!floattest($('#phone').val())) {
		$('#phone').css('border-color', 'red');
		$('#phone').focus();
	}
	else {
		$('#form-settings').submit();
	}

});



$("#update-products").click(function () {
	if ($('#sgu_name').val() == '') {
		$('#sgu_name').css('border-color', 'red');
		$('#sgu_name').focus();
	}
	else if ($('#igux2_name').val() == '') {
		$('#igux2_name').css('border-color', 'red');
		$('#igux2_name').focus();
	}

	else if ($('#IGUx3_name').val() == '') {
		$('#IGUx3_name').css('border-color', 'red');
		$('#IGUx3_name').focus();
	}

	else if ($('#labourrate').val() == '') {
		$('#labourrate').css('border-color', 'red');
		$('#labourrate').focus();
	}
	else if (!floattest($('#labourrate').val())) {
		$('#labourrate').css('border-color', 'red');
		$('#labourrate').focus();
	}
	else if ($('#evsmargin').val() == '') {
		$('#evsmargin').css('border-color', 'red');
		$('#evsmargin').focus();
	}

	else if (!floattest($('#evsmargin').val())) {
		$('#evsmargin').css('border-color', 'red');
		$('#evsmargin').focus();
	}
	else if ($('#igumargin').val() == '') {
		$('#igumargin').css('border-color', 'red');
		$('#igumargin').focus();
	}
	else if (!floattest($('#igumargin').val())) {
		$('#igumargin').css('border-color', 'red');
		$('#igumargin').focus();
	}
	else if ($('#SGUrate').val() == '') {
		$('#SGUrate').css('border-color', 'red');
		$('#SGUrate').focus();
	}
	else if (!floattest($('#SGUrate').val())) {
		$('#SGUrate').css('border-color', 'red');
		$('#SGUrate').focus();
	}

	else if ($('#IGUx3rate').val() == '') {
		$('#IGUx3rate').css('border-color', 'red');
		$('#IGUx3rate').focus();
	}

	else if (!floattest($('#IGUx3rate').val())) {
		$('#IGUx3rate').css('border-color', 'red');
		$('#IGUx3rate').focus();
	}

	else if ($('#productmargin').val() == '') {
		$('#productmargin').css('border-color', 'red');
		$('#productmargin').focus();
	}

	else if (!floattest($('#productmargin').val())) {
		$('#productmargin').css('border-color', 'red');
		$('#productmargin').focus();
	}

	else if ($('#agenttravelrate').val() == '') {
		$('#agenttravelrate').css('border-color', 'red');
		$('#agenttravelrate').focus();
	}

	else if (!floattest($('#agenttravelrate').val())) {
		$('#agenttravelrate').css('border-color', 'red');
		$('#agenttravelrate').focus();
	}

	else {
		$('#form-rates').submit();
	}

});



$("#quote-update").click(function () {
	if ($('#quote_date').val() == '') {
		$('#quote_date').css('border-color', 'red');
		$('#quote_date').focus();
	}
	else if ($('#quotegreeting').val() == '') {
		$('#quotegreeting').css('border-color', 'red');
		$('#quotegreeting').focus();
	}
	else if ($('#quotedetails').val() == '') {
		$('#quotedetails').css('border-color', 'red');
		$('#quotedetails').focus();
	}

	else {
		$('#form-quotes').submit();
	}

});


$("#btn_settings").click(function () {
	if ($('#product_name').val() == '') {
		$('#product_name').css('border-color', 'red');
		$('#product_name').focus();
	}
	else if ($('#short_name').val() == '') {
		$('#short_name').css('border-color', 'red');
		$('#short_name').focus();
	}


	else if ($('#code').val() == '') {
		$('#code').css('border-color', 'red');
		$('#code').focus();
	}

	/*else if($('#rrp').val()==''){
		$('#rrp').css('border-color','red');
		$('#rrp').focus();
	}
		else if(!floattest($('#rrp').val())){
		$('#rrp').css('border-color','red');
		$('#rrp').focus();
	}*/
	else if ($('#hours').val() == '') {
		$('#hours').css('border-color', 'red');
		$('#hours').focus();
	}
	else if (!floattest($('#hours').val())) {
		$('#hours').css('border-color', 'red');
		$('#hours').focus();
	}
	/*	else if($('#unit_name').val()==''){
		$('#unit_name').css('border-color','red');
		$('#unit_name').focus();
	}
		else if($('#unit_tag').val()==''){
		$('#unit_tag').css('border-color','red');
		$('#unit_tag').focus();
	}*/

	else if ($('#ws_value').val() == '') {
		$('#ws_value').css('border-color', 'red');
		$('#ws_value').focus();
	}

	else if (!floattest($('#ws_value').val())) {
		$('#ws_value').css('border-color', 'red');
		$('#ws_value').focus();
	}

	/*else if($('#cost_vlaue').val()==''){
	$('#cost_vlaue').css('border-color','red');
	$('#cost_vlaue').focus();
}
	
else if(!floattest($('#cost_vlaue').val())){
	$('#cost_vlaue').css('border-color','red');
	$('#cost_vlaue').focus();
}
*/


	else {
		$('#form-product').submit();
	}

});





$(document).on("click", "#anchor_extra_view", function () {
	var id = $(this).data("id");
	$('#extra-windowid').val(id);
});


$(document).on("change", "#extrapdt", function () {
	$.ajax({
		type: "POST",
		url: "ajax/ajax_view_produts.php",
		data: { productid: $('#extrapdt').val(), windowid: $('#extra-windowid').val() },
		success: function (data) {
			var res = data.split('@');
			$('#res_produt').html(res[1]);
			//	$( ".close" ).trigger( "click" );

		},
	});


});

$("#supplierlist").change(function () {

	$.ajax({
		type: "POST",
		url: "ajax/ajax_list_produts.php",
		data: { supplierid: $('#supplierlist').val() },
		success: function (data) {

			$('#list_produt').css('display', 'block');
			var $select = $('#select_produt');
			var json_obj = $.parseJSON(data)
			$('#select_produt').empty();
			//$select.append('<option value="select">Choose Your Products</option>');
			for (var i in json_obj) {
				//alert(json_obj[i].image);
				$select.append('<option value="' + json_obj[i].productid + '" data-thumbnail="' + json_obj[i].image + '">' + json_obj[i].name + '</option>');
			}

			$('.selectpicker').selectpicker('refresh');

			$('#btn_add').css('display', 'block');
			//alert(JSON.stringify(data));




		},
	});

});


$(document).on("keyup", "#quantity_add", function () {
	var total = ((parseFloat($('#labourtotal_add').val()) + parseFloat($('#prodcutcost_add').val())) * parseFloat($('#quantity_add').val())).toFixed(2);
	if (isNaN(total) == true) {
		total = 0;
	}
	$('#total_extra_add').html(total);
	$('#total_cost_add').val(total);
});




$(document).on("keyup", "#quantity", function () {

	var total = ((parseFloat($('#labourtotal').val()) + parseFloat($('#prodcutcost').val())) * parseFloat($('#quantity').val())).toFixed(2);
	if (isNaN(total) == true) {
		total = 0;
	}
	$('#total_extra').html(total);
	$('#total_cost').val(total);
});
$(document).on("click", "#add-extra-button", function () {
	$.ajax({
		type: "POST",
		url: "ajax/ajax_edit_extras.php",
		data: { product: $('#extrapdt').val(), windowid: $('#extra-windowid').val(), cost: $('#total_cost_add').val(), quantity: $('#quantity_add').val(), status: 3, pagestatus: $("input[name=page-status]").val() },
		success: function (data) {
			//alert(data);
			$('#view_' + $('#extra-windowid').val()).html(data);
			$(".close").trigger("click");

		},
	});

});


$(document).on("click", "#update-extra-button", function () {
//	alert($("#list-windowid").val());
	$.ajax({
		type: "POST",
		url: "ajax/ajax_edit_extras.php",
		data: { extraid: $('#extraid').val(), cost: $('#total_extra').html(), quantity: $('#quantity').val(), windowid: $('#windowid_up').val(), status: 2, pagestatus: $("input[name=page-status]").val() },
		success: function (data) {
			$('#view_' + $("#list-windowid").val()).html(data);
			$(".close").trigger("click");

		},
	});

});




$(document).on("click", "#edit-view-manager", function () {

	var id = $(this).data("id");

	$.ajax({
		type: "POST",
		url: "ajax/ajax_view_produts.php",
		data: { extraid: id, status: 1 },
		success: function (data) {
			var res = data.split('@');
			$('#list-windowid').val(res[0]);
			$('#extra-view-manager').html(res[1]);

		},
	});


});


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

function floattest(value) {
	if (value.match(/^-?\d*(\.\d+)?$/)) {
		return true
	}
	else {
		return false;
	}
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

function delview(extraid, windowid) {


	swal({
		title: "Do you want to delete ",
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
					url: "ajax/ajax_edit_extras.php",
					data: { extraid: extraid, windowid: windowid, pagestatus: $("input[name=page-status]").val(), status: 4 },
					success: function (data) {
						//alert(data);

						$('#view_' + windowid).html(data);

					},
				});




			}
		});


}
