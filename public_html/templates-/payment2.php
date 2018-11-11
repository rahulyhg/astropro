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

 <form class="form-signin" name="form2" action="../templates/natal_chart_report.php" method="post">
 <div class="spacer-30"></div>
    <h1 class="h3 mb-3 font-weight-normal">Success!</h1>
    
    
   
    
    <div class="spacer-30"></div>
    <input class='btn btn-lg btn-primary btn-block' type='submit' value='Get your report!'>
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


