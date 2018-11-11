<?php
  session_start( );
  
  error_reporting(E_ALL);
ini_set('display_errors', 1);

require('http.php');
require('oauth_client.php');
require('config.php');


  //$crypt_pwd = md5($day);

  $sql = "SELECT email FROM users WHERE email='$email'";
  $result = @mysqli_query($conn, $sql);
  $num_rows1 = @MYSQLI_NUM_rows($result);

  $row = @mysqli_fetch_array($result);
  $email = $row['email'];

  // now analyze the results
  if ($num_rows1 != 1)
  {
    // cannot find valid user
    echo "<br><br>&nbsp;&nbsp;&nbsp;&nbsp;I cannot find you as a valid user. Please go back and re-enter your information.";
    exit();
  }
  else
  {
  $_SESSION['email'] = $row['email'];

    //update member_info table in database for this record
    //$date_now = date ("Y-m-d");
    //$sql = "UPDATE users SET last_login='$date_now' WHERE useremail='$useremail'";
    //$result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);

    //example from PHP 4 - $string = 'Porcupine Pie; Vanilla Soup';
    $hashy = $row['email'] . "; " . $my_hash_padding;
    $_SESSION['email_hash'] = md5($hashy);

  }
?>

<body>
<div class="container round_block">
  <div class="row">
    <div class="col-md-12">


      <?php  
       echo "<meta HTTP-EQUIV='REFRESH' content='0; url=scripts/natal_chart_report.php'>";

       exit();
      ?>




    </div>
  </div>
</div>
<?php include 'footer.html';?>
</body>
