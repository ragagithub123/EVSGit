function getBackgroundColor() {
	var colorThief = new BackgroundColorTheif();
	var rgb = colorThief.getBackGroundColor(document.getElementById("noimage"));
	//alert('background-color = '+rgb);
	console.log('background-color = '+rgb);
	document.getElementById("backgroud-image").style.backgroundColor = 'rgb(' + rgb[0] + ',' + rgb[1] + ',' + rgb[2] +')';
}
function getBase64Image(img) {
  var canvas = document.createElement("canvas");
  canvas.width = img.width;
  canvas.height = img.height;
  var ctx = canvas.getContext("2d");
  ctx.drawImage(img, 0, 0);
  var dataURL = canvas.toDataURL("image/png");
  return dataURL.replace(/^data:image\/(png|jpg);base64,/, "");
}




 