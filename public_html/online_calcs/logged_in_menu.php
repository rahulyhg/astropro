
<?php

include ('header.html');
include 'nav.php';
include ('constants.php');           //nedded because of "../footer.html" statements
session_start();


// connect to the database and point to the proper database
require_once ('../../mysqli_connect_online_calcs_db_MYSQLI.php');
?>
<body>
<div class="container round_block">

  <div class="row">

    <div class="col-md-12">
		<?php echo "<h1><strong>Welcome, " . $username . ", to your private database</strong></h1>"; ?>
<?php

  $main_page = MAIN_PAGE;
  $view_records_page = VIEW_RECORDS_PAGE;
  
  echo "<p><strong>New users</strong>:</p>";
  echo "<p><a href='$main_page'>Click here</a> to enter birth details into your personal database</p>";
  echo "<p>Tip: You need to enter birth details to create your database and then generate your reports. <br>";
  echo "</p>";

  echo "<br><p><strong>Returning users:  </strong></p>";

  echo "<font size='+1'><a href='$view_records_page'>Click here</a> to view your personal database entries</a></font><br><br><br>";
  echo "<a href='$main_page'>Click here</a> to enter birth details into your personal database</a><br><br>";
  echo "<a href='changeemail.php'>Click here</a> to change your e-mail address</a><br><br>";
  echo "<a href='changepassword.php'>Click here</a> to change your password</a>";

  echo "<br><br>";

?>
    </div>
  </div>
</div>
<?php include 'footer.html';?>
</body>
