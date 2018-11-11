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
                            <div class="box__heading box__heading_blue">
                                <div>
                                    <span>Thank you for your purchase</span>
                                    <small>Your reports is on its way</small>
                                </div>
                                <!-- <a href="page_08.php" class="box__prev" title="prev"></a> -->
                            </div>

                            <div class="box__main">
                                <div class="box__title_purple">But wait!</div>
                                <div class="box__title_black">Imagine the power of being able <strong>to decode your past</strong></div>

                                <div class="box__row">
                                    <div class="box__row_image">
                                        <div class="modalInfo__elem">
                                            <div class="modalInfo__elem_title">Life Experts</div>
                                            <div class="modalInfo__elem_image">
                                                <img src="img/life_experts.png" class="img-fluid" alt="">
                                            </div>
                                            <a class="modalInfo__elem_button">Click to Preview</a>
                                        </div>
                                    </div>
                                    <div class="box__row_content">
                                        <p>A Life Experts report contains secret details about you that no one else knows. It will completely explain who you are and why. Get yours now.</p>
                                        <form class="form">
                                            <div class="form_group">
                                                <label class="form_checkbox">
                                                    <input type="checkbox" name="" checked>
                                                    <span>Yes! Please add this item to my order</span>
                                                </label>
                                            </div>
                                            <a href="page_09.php" class="btn btn_purple">Pay Only $3</a>
                                        </form>
                                    </div>
                                </div>

                                <div class="text-center">
                                    <a href="../online_calcs/scripts/natal_chart_report.php" class="btn btn_purple btn_long">Get horoscope now <i class="fa fa-caret-right"></i></a>
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
