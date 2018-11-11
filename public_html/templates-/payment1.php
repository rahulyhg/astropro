<?php
session_start();
include ('modules/head.php');
include ('modules/header.php');
include 'modules/nav.php';
?>



<div class="block_one">
<div class="container">
	<div class="wrapper">
		<div class="col-md-12">
    <div class="main-content">

 <form class="form-signin" name="form2" action="payment2.php" method="post">
 <div class="spacer-30"></div>
    <h1 class="h3 mb-3 font-weight-normal">Payment details:</h1>
    
    <div class="row">
        <div class="col-md-12">
        <label>Card number:</label>
    <?php
	echo "<input class='form-control' type='email' maxLength='16' size='17' name='email' VALUE='' placeholder='card number'>";
    ?>
     </div>

       <div class="col-md-12">
       <label>Expiration Date:</label>
	<?php
	echo "<input class='form-control' type='text' maxLength='12' size='17' name='username' VALUE=''  placeholder='expiration Date'>";
    ?>
    </div>

    <div class="col-md-12">
<label>CSV:</label>
<?php
	echo "<input class='form-control' type='text' maxLength='12' size='17' name='username' VALUE=''  placeholder='CSV'>";
    ?>
   
    
    <div class="spacer-30"></div>
    <input class='btn btn-lg btn-primary btn-block' type='submit' value='Continue'>
	<div class="spacer-30"></div>
    </div>
    </div>
</form>

</div>
</div>
</div>
</div>
<?php
include ('modules/footer.php');
?>

