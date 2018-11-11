<?php

session_start();
include ('modules/head.php');
include ('modules/header.php');
include 'modules/nav.php';

  require_once('../../mysqli_connect_online_calcs_db_MYSQLI.php');
  require_once ('../../my_functions_MYSQLI.php');

  $username = safeEscapeString($conn, $_POST["username"]);
  $email = safeEscapeString($conn, $_POST["email"]);
  $month = safeEscapeString($conn, $_POST["month"]);
  $day = safeEscapeString($conn, $_POST["day"]);
  $year = safeEscapeString($conn, $_POST["year"]);
  $timezone = safeEscapeString($conn, $_POST["timezone"]);
  $long_deg = safeEscapeString($conn, $_POST["long_deg"]);
  $long_min = safeEscapeString($conn, $_POST["long_min"]);
  $ew = safeEscapeString($conn, $_POST["ew"]);
  $lat_min = safeEscapeString($conn, $_POST["lat_min"]);
  $lat_deg = safeEscapeString($conn, $_POST["lat_deg"]);
  $ns = safeEscapeString($conn, $_POST["ns"]);
  $hour = safeEscapeString($conn, $_POST["hour"]);
  $minute = safeEscapeString($conn, $_POST["minute"]);
  $amorpm = safeEscapeString($conn, $_POST["amorpm"]);

  $_SESSION['username'] = $username;
  $_SESSION['day'] = $day;
  $_SESSION['year'] = $year;
  $_SESSION['month'] = $month;
  $_SESSION['timezone'] = $timezone;
  $_SESSION['long_deg'] = $long_deg;
  $_SESSION['long_min'] = $long_min;
  $_SESSION['ew'] = $ew;
  $_SESSION['lat_min'] = $lat_min;
  $_SESSION['lat_deg'] = $lat_deg;
  $_SESSION['ns'] = $ns;
  $_SESSION['hour'] = $hour;
  $_SESSION['minute'] = $minute;
  $_SESSION['amorpm'] = $amorpm;
  $_SESSION['email'] = $email;

  $pattern = '/.*@.*\..*/';
  if (preg_match($pattern, $email) == 0)
  {        
    echo '<div class="main main_steps">';
    echo '<div class="main__wrap">';
    echo '<div class="container">';
    echo '<div class="box">';     
    echo '<div class="box__heading">';
    echo error_message_header();
    echo "<center>The e-mail address you entered is not valid.";
    echo '</div>';
    echo '<div class="box__gray">'; 
    echo '<div class="box__inner">'; 
    echo post_back_message('Registration');
    echo "</center>";
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

    exit();
  }

  // if ($password1 != $password2)
  // {
  //   echo error_message_header();
  //   echo "<center><br><b>Your passwords do not match.<br><br>";
  //   echo post_back_message('Registration');
  //   echo "</b></center>";
  //   include 'footer.html';
  //   exit();
  // }

   $missing_text = "";

  if (!$username)
  {
     $missing_text .= "Username<br>";
   }

  // if (!$password1)
  // {
  //   $missing_text .= "Password<br>";
  // }


  if ($missing_text != "")
  {
    echo '<div class="main main_steps">';
    echo '<div class="main__wrap">';
    echo '<div class="container">';
    echo '<div class="box">'; 
    echo '<div class="box__heading">'; 
    echo "<center>You did not fill in the form correctly.<br><br>The following items are either incorrect or missing:";
    echo '</div>';
    echo '<div class="box__gray">'; 
    echo '<div class="box__inner">'; 
    echo $missing_text . "<br><br>";
    echo post_back_message('Registration');
    echo "</center><br><br>";
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    exit();
  }

  $sql = "SELECT * FROM member_info WHERE username='$username'";
  $result = mysqli_query($conn, $sql);

  if ($result)
  {
    $num_rows1 = MYSQLI_NUM_rows($result);
  }
  else
  {
    $num_rows1 = 0;
  }

  if ($num_rows1 > 0)
  {
    echo '<div class="main main_steps">';
    echo '<div class="main__wrap">';
    echo '<div class="container">';
    echo '<div class="box">'; 
    echo '<div class="box__heading">'; 
    echo "<center>That username is already taken.<br /><br />Please select another and try again.";
    echo '</div>';
    echo '<div class="box__gray">'; 
    echo '<div class="box__inner">'; 
    echo post_back_message('Sign up');
    echo "</center><br><br>";
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    exit();
  }

  $date_now = date ("Y-m-d");

 ///// $crypt_pwd = md5($password1);

  $sql = "INSERT INTO member_info (ID,username,email,orig_email,account_opened,last_login,last_transaction,day) VALUES ('','$username','$email','$email','$date_now','$date_now','$date_now','$day')";
  $result = mysqli_query($conn, $sql);

  $sql = "INSERT INTO birth_info (ID,entered_by,name,month,day,year,hour,minute,timezone,long_deg,long_min,ew,lat_deg,lat_min,ns,entry_date) VALUES ('','$username','$username','$month','$day','$year','$hour','$minute','$timezone','$long_deg','$long_min','$ew','$lat_deg','$lat_min','$ns','$date_now')";
  $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);

//the below is what needs changing according to individual situation - e-mail settings
  $emailTo = EMAIL_ADDRESS;

  $emailFrom =  $email;
  $emailSubject = YOUR_URL . " Registration Form Data";
  $emailText =  "This is the information submitted to " . YOUR_URL . ":\n\n";
//change the above to suit your situation

//here is the data to be submitted
  $emailText .= "Username              = $username \n";
  $emailText .= "E-mail address        = $email \n";
  $emailText .= "Request date          = $date_now \n\n";


  @mail($emailTo, $emailSubject, $emailText, "From: $email");

  @mail(EMAIL_ADDRESS, $emailSubject, $emailText, "From: $email");

  echo "<meta HTTP-EQUIV='REFRESH' content='0; url=page_05.php'>";

  exit();


Function error_message_header()
{
  $msg = "<br><center><b>The following information you submitted to us was either incomplete or invalid:</b></center><br>";
  return($msg);
}

?>

