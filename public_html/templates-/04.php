<?php
session_start();
include ('modules/head.php');
include ('modules/header.php');
include 'modules/nav.php';

?>

<body class="text-center">
<div class="block_one">
<div class="container">
	<div class="wrapper">
		<div class="col-md-12">

            <div class="main-content">
            <div class="page-header">
            <div class="header-title">
                <div>Where can we send your report?</div>
            </div>
            </div>
			<div class="content">
<div class="box2">
    <div class="field-label">
        My Email
    </div>

        <form name="form1" action="signup_login.php" method="post">
        <input class='form-control' id='email' type='email' maxLength='16' size='17' name='email' placeholder='email' required>
        <div class="spacer-30"></div>
        <input class='btn btn-lg btn-primary btn-block' type="submit" value="Continue">
        </form>
</div>

                <div class="box2">
                    <div class="flex-hr">
                       <div class="or-text">OR</div>
                </div>
                
					<a class="facebookbtn btn" href="fb_login.php">  <img src="images/facebook.png"><div>Continue with Facebook</div></a>
					
              
                    <a class="googlebtn btn" href="google_login.php"> <img src="images/google.png"><div>Continue with Google</div></a>

            </div>
		</div>
    </div>
	</div>
</div>
</div>
</div>
</body>

<?php
include ('modules/footer.php');
?>
<!-- <script>
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
</script> -->

