
<?php
session_start();
include 'header.php';
include 'nav.php';
?>
	<div class="container">
		<div class="wrapper">
<div class="spacer-30"></div>
			<div class="col-md-12 form-signin">
			<div class="spacer-30"></div>
        <form class="form-signin" name="form2" action="login.php" method="post">
    <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
	<?php
	echo "<input class='form-control' type='text' maxLength='12' size='17' name='username' VALUE='$username'  placeholder='Login' required>";
	?>
    <label for="inputEmail" class="sr-only">Email address</label>
<div class="spacer-30"></div>
    <label for="inputPassword" class="sr-only">Password</label>
	<?php
	
	echo "<input class='form-control' type='day' maxLength='16' size='17' name='day' VALUE='$day' placeholder='day' required>";
  ?>
   <div class="spacer-30"></div>
  	<?php
	$monthName = date('F', mktime(0, 0, 0, $month, 10));
	echo "<input class='form-control' type='month' maxLength='16' size='17' name='month' VALUE='$monthName' placeholder='month' required>";
  ?>
  <div class="spacer-30"></div>
  	<?php
	
	echo "<input class='form-control' type='year' maxLength='16' size='17' name='year' VALUE='$year' placeholder='year' required>";
  ?>
    <div class="checkbox mb-3">
        <label>
            <input type="checkbox" value="remember-me"> Remember me
        </label>
    </div>
    <br><input class='btn btn-lg btn-primary btn-block' type='submit' value='Log me in'>
    <span class='pa_textbox'><A href="forgotpassword.php"><br><br>Forgot your password?</A></span>
</form>

        </div>
        <div class="spacer-30"></div>
       
    </div>
</div>
<?php include 'footer.html';?>

