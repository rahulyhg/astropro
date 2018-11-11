<?php

session_start();

require_once '../../mysqli_connect_online_calcs_db_MYSQLI.php';
require_once '../../my_functions_MYSQLI.php';

include 'modules/head.php';
?>

<body>

    <div class="page">

        <?php
include 'modules/header.php';

if(isset($_POST['username']))
$_SESSION['username']=$_POST['username'];
$name=$_SESSION['username'];
$username = $name;

if(isset($_POST['day']))
$_SESSION['day']=$_POST['day'];
$day = $_SESSION['day'];

if(isset($_POST['month']))
$_SESSION['month']=$_POST['month'];
$month = $_SESSION['month'];

if(isset($_POST['year']))
$_SESSION['year']=$_POST['year'];
$year = $_SESSION['year'];

if(isset($_POST['timezone']))
$_SESSION['timezone']=$_POST['timezone'];
$timezone = $_SESSION['timezone'];

if(isset($_POST['long_deg']))
$_SESSION['long_deg']=$_POST['long_deg'];
$long_deg = $_SESSION['long_deg'];

if(isset($_POST['long_min']))
$_SESSION['long_min']=$_POST['long_min'];
$long_min = $_SESSION['long_min'];

if(isset($_POST['ew']))
$_SESSION['ew']=$_POST['ew'];
$ew = $_SESSION['ew'];

if(isset($_POST['lat_min']))
$_SESSION['lat_min']=$_POST['lat_min'];
$lat_min = $_SESSION['lat_min'];

if(isset($_POST['lat_deg']))
$_SESSION['lat_deg']=$_POST['lat_deg'];
$lat_deg = $_SESSION['lat_deg'];

if(isset($_POST['ns']))
$_SESSION['ns']=$_POST['ns'];
$ns = $_SESSION['ns'];

if(isset($_POST['hour']))
$_SESSION['hour']=$_POST['hour'];
$hour = $_SESSION['hour'];

if(isset($_POST['minute']))
$_SESSION['minute']=$_POST['minute'];
$minute = $_SESSION['minute'];

if(isset($_POST['amorpm']))
$_SESSION['amorpm']=$_POST['amorpm'];
$amorpm = $_SESSION['amorpm'];

if(isset($_POST['email']))
$_SESSION['email']=$_POST['email'];
$email = $_SESSION['email'];

?>

        <?php
include 'modules/nav.php';
?>

            <div class="main main_steps">

                <div class="main__wrap">
                    <div class="container">
                        <div class="box">
                            <form class="form" name="form2" action="../online_calcs/login.php" method="post">
                                <div class="box__heading">
                                    <div>Your Info</div>
                                    <a href="page_04.php" class="box__prev" title="prev"></a>
                                </div>

                                <div class="box__gray">
                                    <div class="box__inner">
                                        <div class="form_group">
                                            <label class="form_label">Email*</label>
                                            <div class="formBox">
                                            
                                                <?php echo "<input disabled='disabled' class='formBox__control' type='email' name='email' VALUE='$email' placeholder='email' required>";
?>
                                                <i class="formBox__icon">
                                                    <svg class="ico-svg" viewBox="0 0 30 30" xmlns="http://www.w3.org/2000/svg">
                                                        <use xlink:href="img/sprite-icons.svg#icon_pencil" xmlns:xlink="http://www.w3.org/1999/xlink"></use>
                                                    </svg>
                                                </i>
                                            </div>
                                        </div>
                                        <div class="form_group">
                                            <label class="form_label">First Name*</label>
                                            <div class="formBox">
                                               
                                                <?php echo "<input disabled='disabled' class='formBox__control' type='text' name='username' VALUE='$username'  placeholder='First name' required>";
?>
                                                <i class="formBox__icon">
                                                    <svg class="ico-svg" viewBox="0 0 30 30" xmlns="http://www.w3.org/2000/svg">
                                                        <use xlink:href="img/sprite-icons.svg#icon_check" xmlns:xlink="http://www.w3.org/1999/xlink"></use>
                                                    </svg>
                                                </i>
                                            </div>
                                        </div>
                                        <div class="form_group">
                                            <label class="form_label">Birthday*</label>
                                            <div class="formBox">
                                            <ul class="form_date">
                                               <li>
                                                   <?php $monthName = date('F', mktime(0, 0, 0, $month, 10));
echo "<input disabled='disabled' class='formBox__control' type='' name='month' VALUE='$monthName' placeholder='month' required>";
?>
</li>
<li>
	<?php
echo "<input disabled='disabled' class='formBox__control' type='day' name='day' VALUE='$day' placeholder='day' required>";
?>
</li>
<li>
	<?php
echo "<input disabled='disabled' class='formBox__control' type='year' maxLength='16' size='17' name='year' VALUE='$year' placeholder='year' required>";
?>
</li>
                                        
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="box__content box__content_radius">
                                    <div class="box__inner">
                                        <div class="box__buttons">
                                            <button type="submit" class="btn btn_purple">Get My Horoscope <i class="fa fa-caret-right"></i></button>
                                            <div class="box__buttons_text">Your information is 100% secure and will not be shared.</div>
                                        </div>
                                    </div>
                                </div>

                            </form>

                            <div class="box__step">
                                <div class="box__step_value"><span>80%</span> complete</div>
                                <div class="box__step_progress"><span style="width: 80%;"></span></div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>


        <script src="js/vendor/modernizr-3.5.0.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-3.2.1.min.js"><\/script>')</script>
        <script src="js/vendor/svg4everybody.legacy.min.js"></script>
        <script src="js/vendor/jquery.fancybox/jquery.fancybox.min.js"></script>
        <script src="js/vendor/jquery.scrollbar/jquery.scrollbar.min.js"></script>
        <script src="js/vendor/swiper/js/swiper.min.js"></script>
        <script src="js/plugins.js"></script>
        <script src="js/main.js"></script>


    </body>
</html>
