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
                        <div class="box box_small">
                            <div class="box__heading box__heading_line">Your Order Is Complete</div>
                            <div class="box__content box__content_radius box__content_end">
                                <div class="box__subtitle">Read your report now</div>

                                <div class="box__image">
                                    <div class="modalInfo__elem">
                                        <div class="modalInfo__elem_title">Life Map</div>
                                        <div class="modalInfo__elem_image">
                                            <img src="img/free_horoscope.png" class="img-fluid" alt="">
                                        </div>
                                        <a href="../online_calcs/scripts/natal_chart_report.php" class="modalInfo__elem_button">Click to Read</a>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <a href="page_07.php" class="btn btn_purple btn_long">Continue <i class="fa fa-caret-right"></i></a>
                                </div>

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
