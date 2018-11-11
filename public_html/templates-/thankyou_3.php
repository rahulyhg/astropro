<?php
  session_start( );
  include ('modules/head.php');
  include ('modules/header.php');
  include 'modules/nav.php';
?>


<div class="block_one">
<div class="container">
	<div class="wrapper">
		<div class="col-md-12">
    <div class="main-content">

 <form class="form-signin" name="form2" action="payment1.php" method="post">
 <div class="spacer-30"></div>
    <h1 class="h3 mb-3 font-weight-normal">Almost ready.</h1>
    <div class="spacer-30"></div>
    <h4 class="h4 mb-3 font-weight-normal">BUT WAIT...</h4>
    
    <div class="row">
        <div class="col-md-12">    
    
        
    <!-- Trigger the modal with a button -->
<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Life numbers</button>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <p>Some text will be here soon!</p>
      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-default" data-dismiss="modal"></button> -->
      </div>
    </div>

  </div>
</div>



    <div class="spacer-30"></div>
    <input class='btn btn-lg btn-primary btn-block' type='submit' value='Pay only $3'>
    <div class="spacer-30"></div>
    <a href="thankyou_4.php" class="h4 mb-3 font-weight-normal">No, I want to stay in the dark</h4>
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