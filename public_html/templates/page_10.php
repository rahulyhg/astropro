<?php
session_start();

include 'modules/head.php';
header('Refresh: 10; URL=../online_calcs/scripts/natal_chart_report.php');
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
                            <div class="box__heading box__heading_blue">
                                <span>Almost There!</span>
                            </div>

                            <div class="box__main">

                                <div class="box__main_image">
                                    <div class="modalInfo__elem">
                                        <div class="modalInfo__elem_title">Life Map</div>
                                        <div class="modalInfo__elem_image">
                                            <img src="img/service_image_one.jpg" class="img-fluid" alt="">
                                        </div>
                                    </div>
                                </div>
                                <div class="box__main_step">
                                    <strong>Step 2</strong>
                                    <span>(3-8 seconds)</span>
                                </div>

                                <ol class="box__main_stage">
                                <div class="progress">     
    <li class="progress-bar finished"><span>Processing Payment</span></li> 
  </div>  
  <div class="progress">   
                                    <li class="progress-bar finished"><span>Verifying Personal Info</span></li>
                                    </div>
                                    <div class="progress">   
                                    <li class="progress-bar finished"><span>Creating Report</span></li>
                                    </div>
                                    <div class="progress">   
                                    <li class="progress-bar finished"><span>Verifying Report</span></li>
                                    </div>
                                    <div class="progress">   
                                    <li class="progress-bar finished"><span>Sending Report</span></li>
                                    </div>
                                </ol>
                               
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
    $(function () {
  
  var prog = $(".progress-bar");
  
prog.eq(0).animate({width:"100%"}, 1500)
prog.eq(1).animate({width:"100%"}, 2500)
prog.eq(2).animate({width:"100%"}, 3500)
prog.eq(3).animate({width:"100%"}, 5000)
prog.eq(4).animate({width:"100%"}, 6500)

});
</script>

    </body>
</html>
