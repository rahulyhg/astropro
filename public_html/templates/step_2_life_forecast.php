<?php
session_start();
include 'modules/head.php';
?>

<body>

<div class="page">

<?php
include 'modules/header.php';
?>

        <nav class="topnav">
            <div class="container">
                <ul>
                    <li><a href="/">Free Horoscope</a></li>
                    <li><a href="step_1_life_map.php"><span>Step 1.</span> Life map</a></li>
                    <li class="active"><a href="#"><span>Step 2.</span> Life Forecast</a></li>
                    <li><a href="step_3_life_numbers.php"><span>Step 3.</span> Life Numbers</a></li>
                    <li><a href="step_4_life_experts.php"><span>Step 4.</span> Life Experts</a></li>
                </ul>
            </div>
        </nav>

        <section class="service_about">
            <div class="container">
                <div class="heading">What My Future Holds?</div>
                <div class="subtitle">Unveil it gradually.</div>
                <div class="row">
                    <div class="row__left">

                        <div class="row__left_item">
                            <div class="service_elem">
                                <div class="service_elem__image">
                                    <img src="img/service_03.png" class="img-fluid" alt="">
                                </div>
                                <div class="service_elem__button">
                                    <a href="#" class="btn btn_yellow">Click to Preview</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row__right">
                        <div class="service_text">
                            <p>
                                See your future in the most unbelievable manner:
                            </p>
                            <ul>
                                <li>See things <strong>before they even come to pass</strong></li>
                                <li>Get <strong>instant comfort and clarity</strong></li>
                                <li>Avail <strong>12 months</strong> of daily forecasts</li>
                            </ul>
                            <div class="service_price">Only $19</div>
                            <div class="service_button">
                                <a href="page_09.php" class="btn btn_purple btn-lg">Order now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="block_quote">
            <div class="container">
                <div class="quote">
                    <div class="quote__image">
                        <img src="img/b04_img.png" class="img-fluid" alt="">
                    </div>
                    <div class="quote__text">“I must confess I was skeptical about getting a reading, but after an experience with AstroPro  I was blown away. Their insight and predictions into my life and how I can evolve was very beneficial. I would HIGHLY recommend them”.</div>
                    <div class="quote__author">Sue L.</div>
                </div>
            </div>
        </section>

        <section class="service_what">
            <div class="container">
                <h2>What Is My Life Map?</h2>

                <div class="row">
                    <div class="row__text">
                        <div class="service_what_text">
                            <p>Your Life Forecast is a 50+ page PDF guide that gives you a chance to see your future at this point.</p>
                            <p>In the light of few simple facts about you and 40,000 years of astrological data, it forecasts what you can anticipate during exact dates (1 to 7 day periods) for the subsequent 12 months of your life.</p>
                            <p>No need to worry about WHAT will happen and WHEN….? Get the answers inside the pages of your 100% customized Life Forecast report.</p>
                        </div>
                    </div>
                    <div class="row__image">
                        <div class="service_what_image">
                            <div class="service_what_image_inner">
                                <img src="img/service_b02_3.jpg" class="img-fluid" alt="">
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <section class="service_advantage">
            <div class="container">
                <h2>What You'll Discover</h2>
                <p>From your home and family, your work, finances, personal relationships, independence, your spiritual path, and so much more are explored in the most unimaginable level of precision.</p>
                <br/>
                <h3 class="text-center">Here's a tip-off of what your Life Forecast reveals to you:</h3>
                <ul>
                    <li>Your immediate <strong>financial outlook</strong> (even if your Life Map shows you're financially blessed or challenged overall, your life during your current personal year could prove contradictory)</li>
                    <li>The <strong>confrontations</strong> and obstacles you'll encounter, and how to deal with them, including deception, jealousy, rivalries, addictions and more.</li>
                    <li>CYour <strong>relationships:</strong> Love, family and community - discover specific insight for each according to their significance during this cycle of your life</li>
                    <li>And whatever you can think of</li>
                </ul>

                <div class="text-center">
                    <a href="page_09.php" class="btn btn_purple btn-lg">Order now</a>
                </div>
            </div>
        </section>

        <div class="service_reviews">
            <div class="container">

                <div class="content_mobile">
                    <div class="review_slider swiper-container">
                        <div class="swiper-wrapper">

                            <div class="swiper-slide">
                                <div class="mReview">
                                    <div class="mReview__image">
                                        <img src="img/user_05.png" class="img-fluid" alt="">
                                    </div>
                                    <div class="mReview__content">
                                        <p>"What I have read thus far appears to be precise and enticing. All that has happened so far has been spot on with the guide. It comes at a time when I am desperate for answers so I look forward to witnessing the continued accuracy. Thank you AstroPro."</p>
                                        <strong>Chris C.</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="swiper-slide">
                                <div class="mReview">
                                    <div class="mReview__image">
                                        <img src="img/user_04.png" class="img-fluid" alt="">
                                    </div>
                                    <div class="mReview__content">
                                        <p>"AstroPro guide explained how me being born under my sign guides me in daily life and interacting with people. Their guidance is helping me understand my dreams and my relationships and how can I make my life decisions. I knew that there was a comprehensive implication in how and why things were happening and AstroPro revealed it all to me."</p>
                                        <strong>Sandra H</strong>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- Add Pagination -->
                        <div class="swiper-pagination">
                        </div>
                    </div>
                </div>

                <div class="content_desktop">
                    <div class="dReview">
                        <div class="dReview__image">
                            <img src="img/user_05.png" class="img-fluid">
                        </div>
                        <p>"What I have read thus far appears to be precise and enticing. All that has happened so far has been spot on with the guide. It comes at a time when I am desperate for answers so I look forward to witnessing the continued accuracy. Thank you AstroPro."</p>
                        <strong>Chris C.</strong>
                    </div>

                    <div class="dReview">
                        <div class="dReview__image">
                            <img src="img/user_04.png" class="img-fluid">
                        </div>
                        <p>"AstroPro guide explained how me being born under my sign guides me in daily life and interacting with people. Their guidance is helping me understand my dreams and my relationships and how can I make my life decisions. I knew that there was a comprehensive implication in how and why things were happening and AstroPro revealed it all to me."</p>
                        <strong>Sandra H</strong>
                    </div>

                </div>

                <div class="reviewBlock">
                    <div class="reviewBlock__inner">
                        <div class="reviewBlock__title">Money Back <span>Guarantee</span></div>
                        <div class="reviewBlock__subtitle">Love it or it’s FREE</div>
                        <p>Order your Life Forecast today RISK-FREE. You can avail up to 90 days to absorb every shocking detail it reveals about you. Just follow the guidance to improve almost every aspect of your life.</p>
                        <p>If you're not completely satisfied and overwhelmed by our services, just request a refund within 90 days of purchase, and you'll get a prompt 100% refund - without any questions.</p>
                        <a href="page_09.php" class="btn btn_purple btn-lg">Order now</a>
                    </div>
                    <div class="reviewBlock__icon">
                        <img src="img/quality.png" class="img-fluid" alt="">
                    </div>
                </div>

            </div>
        </div>

        <section class="service_access">
            <div class="container">
                <h2>Your Life Forecast helps you answer questions, such as:</h2>
                <ol>
                    <li>Should you embark on new risks this year or lay low for now?</li>
                    <li>What impulsive changes might be appropriate for you?</li>
                    <li>Will blessings come easy for you, and in what facets of your life?</li>
                    <li>Is it now a time of self-government or will collaboration serve you best this year?</li>
                </ol>
                <div class="text-center">
                    <a href="page_09.php" class="btn btn_purple btn-lg">Order now</a>
                </div>

            </div>
        </section>

        <footer class="footer">

            <div class="container">

                <ul class="footer__logos">
                    <li>
                        <img src="img/footer__logo_01.png" class="img-fluid" alt="">
                    </li>
                    <li>
                        <img src="img/footer__logo_02.png" class="img-fluid" alt="">
                    </li>
                    <li>
                        <img src="img/footer__logo_04.png" class="img-fluid" alt="">
                    </li>
                    <li>
                        <img src="img/footer__logo_03.png" class="img-fluid" alt="">
                    </li>
                    <li>
                        <img src="img/footer__logo_05.png" class="img-fluid" alt="">
                    </li>
                </ul>

                <ul class="footer__progress">
                    <li>
                        <strong>345</strong>
                        <span>International<br/>Experts of Astrology</span>
                    </li>
                    <li>
                        <strong>823 123</strong>
                        <span>Clients of<br/>2017 Year</span>
                    </li>
                    <li>
                        <strong>16 324</strong>
                        <span>Reports Compiled<br/>This Montth</span>
                    </li>
                </ul>

                <a href="/" class="footer__logo">
                    <object type="image/svg+xml" data="img/logo.svg" class="img-fluid"></object>
                </a>

                <ul class="footer__nav">
                    <li><a href="/">Free Horoscope</a></li>
                    <li><a href="step_1_life_map.php">Life Map</a></li>
                    <li><a href="step_2_life_forecast.php">Life Forecast</a></li>
                    <li><a href="step_3_life_numbers.php">Life Numbers</a></li>
                    <li><a href="step_4_life_experts.php">Life Experts</a></li>
                </ul>

                <div class="footer__about">
                    <div class="footer__about_copy">Copyright 2016-2018. All rihts reserved</div>
                    <ul class="footer__about_nav">
                        <li><a href="#">Terms and Conditions</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">help@astropro.net</a></li>
                    </ul>
                </div>

            </div>

        </footer>


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
