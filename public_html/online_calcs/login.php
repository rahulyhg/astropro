<?php
  session_start( );
  
  require_once('../../mysqli_connect_online_calcs_db_MYSQLI.php');
  require_once ('../../my_functions_MYSQLI.php');
  include '../../templates/modules/header.php';
  include '../../templates/modules/nav.php';

if(isset($_POST['username']))
$_SESSION['username']=$_POST['username'];
$name=$_SESSION['username'];
$username = $name;

if(isset($_POST['day']))
$_SESSION['day']=$_POST['day'];
$day = $_SESSION['day'];

if(isset($_POST['year']))
$_SESSION['year']=$_POST['year'];
$year = $_SESSION['year'];

  //$crypt_pwd = md5($day);

  $sql = "SELECT username FROM member_info WHERE username='$username' And day='$day'";
  $result = @mysqli_query($conn, $sql);
  $num_rows1 = @MYSQLI_NUM_rows($result);

  $row = @mysqli_fetch_array($result);
  $username = $row['username'];

  // now analyze the results
  if ($num_rows1 != 1)
  {
    // cannot find valid user
    echo "<br><br>&nbsp;&nbsp;&nbsp;&nbsp;I cannot find you as a valid user. Please go back and re-enter your information.";
    exit();
  }
  else
  {
  $_SESSION['username'] = $row['username'];

    //update member_info table in database for this record
    $date_now = date ("Y-m-d");
    $sql = "UPDATE member_info SET last_login='$date_now' WHERE username='$username'";
    $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);

    //example from PHP 4 - $string = 'Porcupine Pie; Vanilla Soup';
    $hashy = $row['username'] . "; " . $my_hash_padding;
    $_SESSION['username_hash'] = md5($hashy);

  }
?>

<body>
<div class="container round_block">
  <div class="row">
    <div class="col-md-12">


      <?php  
       echo "<meta HTTP-EQUIV='REFRESH' content='0; url=../templates/page_08.php'>";
       exit();
      ?>




    </div>
  </div>
</div>

</body>
