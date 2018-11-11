<!doctype html>
<html class="no-js" lang="">
<?php
session_start();

include 'modules/head.php';
?>
    <body>

        <div class="page">

            <?php
include 'modules/header.php';
?>

            <?php
include 'modules/nav.php';

?>


            <div class="main main_steps">

                <div class="main__wrap">
                    <div class="container">
                        <div class="box">
                            <form class="form" name="form1" action="page_04.php" method="post">
                                <div class="box__heading">
                                    <div>Customize Your Horoscope</div>
                                    <a href="page_02.php" class="box__prev" title="prev"></a>
                                </div>

                                <div class="box__gray">
                                    <div class="box__inner">
                                        <div class="form_group">
                                            <label class="form_label">Email*</label>
                                            <div class="formBox">
                                                <input value='<?php echo $_SESSION['email']; ?>' id='email' type='email' maxLength='16' size='17' name='email' placeholder='email' class='formBox__control' required />
                                                <!-- <i class="formBox__icon">
                                                    <svg class="ico-svg" viewBox="0 0 30 30" xmlns="http://www.w3.org/2000/svg">
                                                        <use xlink:href="img/sprite-icons.svg#icon_pencil" xmlns:xlink="http://www.w3.org/1999/xlink"></use>
                                                    </svg>
                                                </i> -->
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                        <div class="col-2" style="text-align: right;">   
                                        <input type="checkbox" id="termsChkbx " onchange="isChecked(this, 'sub1')" />
                                        </div>
                                        <div class="col-10"> 
                                        <div class="box__buttons_text">By joining I agree to AstroPro <a href="#">Terms & Conditions</a> and <a href="#">Privacy Policy</a> , and to receive AstroPro’s electronic communications.</div>
                                        </div>
                                        </div>                                
                                      </div>
                                </div>

                                <div class="box__content box__content_radius">
                                    <div class="box__inner">
                                        <div class="box__buttons">
                                            <button type="submit" name="submit" id="sub1" class="btn btn_purple" disabled="disabled">Continue <i class="fa fa-caret-right"></i></button>
                                            <div class="box__buttons_label">or</div>
                                            <button type="submit" class="btn btn_facebook">Continue with Facebook</button>
                                            <a href="google_login.php" type="submit" class="btn btn_google">Continue with Google</a>
                                        </div>
                                    </div>
                                </div>

                            </form>

                            <div class="box__step">
                                <div class="box__step_value"><span>60%</span> complete</div>
                                <div class="box__step_progress"><span style="width: 60%;"></span></div>
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

<script>
function isChecked(checkbox, sub1) {
    document.getElementById(sub1).disabled = !checkbox.checked;
}
</script>

    </body>


    
</html>
