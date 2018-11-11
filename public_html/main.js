var sbmtBtn = document.getElementById("SubmitButton");
sbmtBtn.disabled = true;

checkFormsValidity = function(){
var myforms = document.forms["myform"];
	if (myforms.checkValidity()) {
		sbmtBtn.disabled = false;
	} else {
		sbmtBtn.disabled = true;
	}
}
