<!doctype html>
<html class="no-js" lang="">
<?php
include ('modules/head.php');
?>
    <body>

        <div class="page">

            <?php
            include ('modules/header.php');
            ?>

            <?php
            include ('modules/nav.php');
            ?>

            <div class="main main_steps">

                <div class="main__wrap">
                    <div class="container">
                        <div class="box">
                            <div class="box__heading">
                                <div>Select Your Sign</div>
                                <a href="page_01.php" class="box__prev"></a>
                            </div>
                            <div class="box__content box__content_radius">
                                <ul class="zodiacList">
                                    <li>
                                        <label class="zodiac">
                                            <input type="radio" name="zodiac" value="" onclick="window.location='page_03.php';">
                                            <span class="zodiac__item">
                                                    <span class="zodiac__icon zodiac_01"></span>
                                                    <span class="zodiac__name">Capricorn</span>
                                                    <span class="zodiac__subname">12/22 - 1/19</span>
                                                </span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="zodiac">
                                            <input type="radio" name="zodiac" value="" onclick="window.location='page_03.php';">
                                            <span class="zodiac__item">
                                                    <span class="zodiac__icon zodiac_02"></span>
                                                    <span class="zodiac__name">Aquarius</span>
                                                    <span class="zodiac__subname">1/20 - 2/18</span>
                                                </span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="zodiac">
                                            <input type="radio" name="zodiac" value="" onclick="window.location='page_03.php';">
                                            <span class="zodiac__item">
                                                    <span class="zodiac__icon zodiac_03"></span>
                                                    <span class="zodiac__name">Pisces</span>
                                                    <span class="zodiac__subname">2/19 - 3/20</span>
                                                </span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="zodiac">
                                            <input type="radio" name="zodiac" value="" onclick="window.location='page_03.php';">
                                            <span class="zodiac__item">
                                                    <span class="zodiac__icon zodiac_04"></span>
                                                    <span class="zodiac__name">Aries</span>
                                                    <span class="zodiac__subname">3/21 - 4/20</span>
                                                </span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="zodiac">
                                            <input type="radio" name="zodiac" value="" onclick="window.location='page_03.php';">
                                            <span class="zodiac__item">
                                                    <span class="zodiac__icon zodiac_05"></span>
                                                    <span class="zodiac__name">Taurus</span>
                                                    <span class="zodiac__subname">4/21 - 5/19</span>
                                                </span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="zodiac">
                                            <input type="radio" name="zodiac" value="" onclick="window.location='page_03.php';">
                                            <span class="zodiac__item">
                                                    <span class="zodiac__icon zodiac_06"></span>
                                                    <span class="zodiac__name">Gemini</span>
                                                    <span class="zodiac__subname">5/20 - 6/21</span>
                                                </span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="zodiac">
                                            <input type="radio" name="zodiac" value="" onclick="window.location='page_03.php';">
                                            <span class="zodiac__item">
                                                    <span class="zodiac__icon zodiac_07"></span>
                                                    <span class="zodiac__name">Cancer</span>
                                                    <span class="zodiac__subname">6/22 - 7/22</span>
                                                </span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="zodiac">
                                            <input type="radio" name="zodiac" value="" onclick="window.location='page_03.php';">
                                            <span class="zodiac__item">
                                                    <span class="zodiac__icon zodiac_08"></span>
                                                    <span class="zodiac__name">Leo</span>
                                                    <span class="zodiac__subname">7/23 - 8/22</span>
                                                </span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="zodiac">
                                            <input type="radio" name="zodiac" value="" onclick="window.location='page_03.php';">
                                            <span class="zodiac__item">
                                                    <span class="zodiac__icon zodiac_09"></span>
                                                    <span class="zodiac__name">Virgo</span>
                                                    <span class="zodiac__subname">8/23 - 9/22</span>
                                                </span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="zodiac">
                                            <input type="radio" name="zodiac" value="" onclick="window.location='page_03.php';">
                                            <span class="zodiac__item">
                                                    <span class="zodiac__icon zodiac_10"></span>
                                                    <span class="zodiac__name">Libra</span>
                                                    <span class="zodiac__subname">9/23 - 10/23</span>
                                                </span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="zodiac">
                                            <input type="radio" name="zodiac" value="" onclick="window.location='page_03.php';">
                                            <span class="zodiac__item">
                                                    <span class="zodiac__icon zodiac_11"></span>
                                                    <span class="zodiac_name">Scorpio</span>
                                                    <span class="zodiac__subname">10/24 - 11/21</span>
                                                </span>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="zodiac">
                                            <input type="radio" name="zodiac" value="" onclick="window.location='page_03.php';">
                                            <span class="zodiac__item">
                                                    <span class="zodiac__icon zodiac_12"></span>
                                                    <span class="zodiac_name">Sagittarius</span>
                                                    <span class="zodiac__subname">11/22 - 12/21</span>
                                                </span>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                            <div class="box__step">
                                <div class="box__step_value"><span>50%</span> complete</div>
                                <div class="box__step_progress"><span style="width: 50%;"></span></div>
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
