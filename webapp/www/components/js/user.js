var qrcode = new QRCode(document.getElementById("qrcode"), {
    width:100,
    height:100
});

/*function makeCode(value) {		
	document.getElementById("text").style.visibility = "hidden"
	qrcode.makeCode(value);
}
*/

function makeCode(value) {		
	qrcode.makeCode(value);
}