// JavaScript Document

$("#maincategory").change(function () {

	$.ajax({
		type: "POST",
		url: "select-subsctegory.php",
		data: { selectid: $('#maincategory').val(), 'type': 1 },
		success: function (data) {

			var $select = $('#category');

			var $select1 = $('#frametypstyle');

			var json_obj = $.parseJSON(data)

			$('#category').empty();

			$('#frametypstyle').empty();

			for (var i in json_obj.subscat) {

				$select.append('<option value="' + json_obj.subscat[i].famecategoryid + '">' + json_obj.subscat[i].category + '</option>');
			}

			for (var i in json_obj.frametypes) {

				$select1.append('<option value="' + json_obj.frametypes[i].frametypeid + '">' + json_obj.frametypes[i].name + '</option>');
			}

		},
	});



});


$("#category").change(function () {


	$.ajax({
		type: "POST",
		url: "select-subsctegory.php",
		data: { selectid: $('#category').val(), 'type': 2 },
		success: function (data) {


			var $select1 = $('#frametypstyle');

			var json_obj = $.parseJSON(data)


			$('#frametypstyle').empty();



			for (var i in json_obj) {

				$select1.append('<option value="' + json_obj[i].frametypeid + '">' + json_obj[i].name + '</option>');
			}

		},
	});



});




$("#select_cat").change(function () {
	$.ajax({
		type: "POST",
		url: "select-frame.php",
		data: { selectid: $('#select_cat').val() },
		success: function (data) {
			$('#result').html(data);
		},
	});


});


$("#select_cat_style").change(function () {
	$.ajax({
		type: "POST",
		url: "select-style.php",
		data: { selectid: $('#select_cat_style').val() },
		success: function (data) {
			$('#result').html(data);
		},
	});


});




$("#copy-frame").click(function () {

	$.ajax({
		type: "POST",
		url: "copy-frame.php",
		data: { selectid: $(this).data('id'), frametypeid: $('#frametypeid').val() },
		success: function (data) {
			//alert(data);
			$('.v-box').append(data);
		},
	});
});

$("#unitname").change(function () {
	$.ajax({

		type: "POST",
		url: "get-unitname.php",
		data: { unitname: $('#unitname').val() },
		success: function (data) {

			$('#unittag').val(data);
		},
	});

});


$('#delframe').click(function () {
	var r = confirm("Do you want to delete?")

	if (r == true) {

		$.ajax({
			type: "POST",
			url: "deleteframe.php",
			data: { frameid: $('#frameid').val(), status: 'delframe' },
			success: function (data) {

				window.location.href = "paneloptions.php?type=frametype";


			},
		});

	}


});


function delcopy(frameid, styleid) {
	var r = confirm("Do you want to delete?")
	if (r == true) {

		$.ajax({
			type: "POST",
			url: "deleteframe.php",
			data: { frameid: frameid, styleid: styleid, status: 'delcopy' },
			success: function (data) {

				window.location.href = "paneloptions.php?type=frametype&id=" + frameid;


			},
		});

	}



}


