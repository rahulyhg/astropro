<?php

session_start();

require_once('../../mysqli_connect_online_calcs_db_MYSQLI.php');
require_once ('../../my_functions_MYSQLI.php');
include ('modules/head.php');
  include ('modules/header.php');
  include 'modules/nav.php';
  //include ('constants.php');
?>

 <?php
$username = $_SESSION['username'];
$day = $_SESSION['day'];
$month = $_SESSION['month'];
$year = $_SESSION['year'];
$timezone = $_SESSION['timezone'];
$long_deg = $_SESSION['long_deg'];
$long_min = $_SESSION['long_min'];
$ew = $_SESSION['ew'];
$lat_min = $_SESSION['lat_min'];
$lat_deg = $_SESSION['lat_deg'];
$ns = $_SESSION['ns'];
$hour = $_SESSION['hour'];
$minute = $_SESSION['minute'];
$amorpm = $_SESSION['amorpm'];
$email = $_SESSION['email'];
 ?>


<div class="block_one">
<div class="container">
	<div class="wrapper">
		<div class="col-md-12">
        <div class="main-content">
 <form class="form-signin" name="form2" action="../online_calcs/login.php" method="post">
 <div class="spacer-30"></div>
    <h1 class="h3 mb-3 font-weight-normal">Your info:</h1>
    
    <div class="row">
        <div class="col-md-12">
        <label>Email:</label>
    <?php
	echo "<input class='form-control' type='email' maxLength='16' size='17' name='email' VALUE='$email' placeholder='email' required>";
    ?>
     </div>

       <div class="col-md-12">
       <label>First name:</label>
	<?php
	echo "<input class='form-control' type='text' maxLength='12' size='17' name='username' VALUE='$username'  placeholder='First name' required>";
    ?>
    </div>

    <div class="col-md-12">
<label>Birthday:</label>
<div class="row">
    <div class="col-md-6">
    <?php
	$monthName = date('F', mktime(0, 0, 0, $month, 10));
	echo "<input class='form-control' type='' maxLength='16' size='17' name='month' VALUE='$monthName' placeholder='month' required>";
    ?>
    </div>
   
    <div class="col-md-3">
	<?php
		echo "<input class='form-control' type='day' name='day' VALUE='$day' placeholder='day' required>";
    ?>
    </div>

    <div class="col-md-3">
  	<?php
	echo "<input class='form-control' type='year' maxLength='16' size='17' name='year' VALUE='$year' placeholder='year' required>";
    ?>
    </div>
    </div>
    
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
</div>
<?php
include ('modules/footer.php');
?>
