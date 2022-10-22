	$(document).on("change",".layer-composite",function(){
	
		var nameattr = $(this).attr('name');
		
		$('.loader3').show();
		
		var selectedoption = $(this).children("option:selected").val();
		var explode = selectedoption.split("@");
		var selectedlayerid = explode[0];
        var glasstype   =   $(this).attr("data-id");
	    var locationid = $(this).attr("data-location");
	    var layerid=[];
	    var glassid=[];
	    $('select[name="'+nameattr+'"] option:selected').each(function() {
		
		var value = $(this).val();
		
		var res = value.split("@");
		var windowid = res[0];
		var locationid = res[1];
  
		layerid.push(res[0]);

		glassid.push(res[1]);

	
 });
	
	var layerids = layerid.join();
	
	var  glassids = glassid.join();
	
	
	
	 $.ajax({
            type: "POST",
            url: "ajax/ajax_layer_listing.php",
            data: {layerids:layerids,glassids:glassids,locationid:locationid,selectedlayerid:selectedlayerid,status:1},
            success: function (data) {
				$('.loader3').hide();
				
				//prompt("Cpoy to clip board: Ctrl+C, Enter",data);
			var json_obj =JSON.parse(data);
			$('#icon'+glasstype+' img').attr('src', json_obj.icon);
		    //$('#icon'+glasstype).html(json_obj.icon);
			$('#outsidetype'+glasstype).html(json_obj.outsideGlasstype);
			$('#outsidethickness'+glasstype).html(json_obj.outsideThickness);
			$('#spacer'+glasstype).html(json_obj.spacer);
			$('#sapcerWidth'+glasstype).html(json_obj.sapcerWidth);
			$('#colorcode'+glasstype).css('background-color', '#'+json_obj.colorcode);
			$('#colorcode'+glasstype).html(json_obj.short_spacer);
			$('#insidetype'+glasstype).html(json_obj.insideGlasstype);
			$('#insidethickness'+glasstype).html(json_obj.insideThickness);
			swal("Layer section Updated");
		
            },
        });
	
});
/*$(document).on("click",".layerupdate-btn",function(){
//$('.layerupdate-btn').click(function(){
	
	$('.loader3').show();
	
	 var value = $(this).attr("data-id");
		var res = value.split("@");
		var windowid = res[0];
		var locationid = res[1];
	 var layerid=[]; 
  var glassid=[];
 
	$('select[name="layercomposite_'+windowid+'[]"] option:selected').each(function() {
  
	layerid.push($(this).val());
	
	
 });
	
	var layerids = layerid.join();
	
		//alert($('#glassids'+windowid).val());
	
	$.ajax({
            type: "POST",
            url: "ajax/ajax_layer_listing.php",
            data: {windowid:windowid,layerids:layerids,glassids:$('#glassids'+windowid).val(),locationid:locationid,status:2},
            success: function (data) {
											  
		$('.loader3').hide();
		
		swal("Layer section Updated");
		
            },
        });
	
	
	
	
});*/
