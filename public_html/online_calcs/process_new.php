<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Astrology Calculations</title>
<meta name="description" content="Your description here">
<meta name="keywords" content="Your keywords here">

    <title><?php echo YOUR_URL; ?> login</title>
    <meta name="description" content="<?php echo YOUR_URL; ?> Website">


    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
       <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>


        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Exo+2:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

        <link rel="stylesheet" href="js/vendor/jquery.fancybox/jquery.fancybox.min.css">
        <link rel="stylesheet" href="js/vendor/swiper/css/swiper.min.css">

        <link rel="stylesheet" href="../templates/css/main.css">
    </head>

    
<?php

session_start();

include '../templates/modules/header.php';
include '../templates/modules/nav.php';
//  include 'constants.php';

require_once 'mysqli_connect_online_calcs_db_MYSQLI.php';
require_once 'my_functions_MYSQLI.php';

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
if (preg_match($pattern, $email) == 0) {

    echo '<div class="main main_steps">';
    echo '<div class="main__wrap">';
    echo '<div class="container">';
    echo '<div class="box">';
    echo error_message_header();
    echo "<center><br><b>The e-mail address you entered is not valid.<br><br>";
    echo post_back_message('Registration');
    echo "</b></center>";
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    include 'footer.html';
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

if (!$username) {
    $missing_text .= "Username<br>";
}

// if (!$password1)
// {
//   $missing_text .= "Password<br>";
// }

if ($missing_text != "") {
    echo '<div class="main main_steps">';
    echo '<div class="main__wrap">';
    echo '<div class="container">';
    echo '<div class="box">';
    echo '<div class="box__gray">';
    echo "<center><br><b>You did not fill in the form correctly.<br><br>The following items are either incorrect or missing:<br><br>";
    echo $missing_text . "<br><br>";
    echo post_back_message('Registration');
    echo "</b></center><br><br>";
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    include 'footer.html';
    exit();
}

$sql = "SELECT * FROM member_info WHERE username='$username'";
$result = mysqli_query($conn, $sql);

if ($result) {
    $num_rows1 = MYSQLI_NUM_rows($result);
} else {
    $num_rows1 = 0;
}

if ($num_rows1 > 0) {
    echo '<div class="main main_steps">';
    echo '<div class="main__wrap">';
    echo '<div class="container">';
    echo '<div class="box">';
    echo '<div class="box__gray">';
    echo "<center><br><b>That username is already taken.<br /><br />Please select another and try again.<br><br>";
    echo post_back_message('Sign up');
    echo "</b></center><br><br>";
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    include 'footer.html';
    exit();
}

$date_now = date("Y-m-d");

///// $crypt_pwd = md5($password1);

$sql = "INSERT INTO member_info (ID,username,email,orig_email,account_opened,last_login,last_transaction,day) VALUES ('','$username','$email','$email','$date_now','$date_now','$date_now','$day')";
$result = mysqli_query($conn, $sql);

$sql = "INSERT INTO birth_info (ID,entered_by,name,month,day,year,hour,minute,timezone,long_deg,long_min,ew,lat_deg,lat_min,ns,entry_date) VALUES ('','$username','$username','$month','$day','$year','$hour','$minute','$timezone','$long_deg','$long_min','$ew','$lat_deg','$lat_min','$ns','$date_now')";
$result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);

//the below is what needs changing according to individual situation - e-mail settings
$emailTo = EMAIL_ADDRESS;

$emailFrom = $email;
$emailSubject = YOUR_URL . " Registration Form Data";
$emailText = "This is the information submitted to " . YOUR_URL . ":\n\n";
//change the above to suit your situation

//here is the data to be submitted
$emailText .= "Username              = $username \n";
$emailText .= "E-mail address        = $email \n";
$emailText .= "Request date          = $date_now \n\n";

@mail($emailTo, $emailSubject, $emailText, "From: $email");

@mail(EMAIL_ADDRESS, $emailSubject, $emailText, "From: $email");

echo "<meta HTTP-EQUIV='REFRESH' content='0; url=../templates/now_registered.php'>";

exit();

function error_message_header()
{
    $msg = "<br><center><b>The following information you submitted to us was either incomplete or invalid:</b></center><br>";
    return ($msg);
}

?>


