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
                            <form class="form">
                                <div class="box__heading box__heading_blue">
                                    Check Out
                                    <a href="page_08.php" class="box__prev"></a>
                                </div>

                                <div class="box__gray hide">
                                    <div class="box__inner">
                                        <div class="form_group">
                                            <label class="form_label">Credit Card Number*</label>
                                            <input type="text" name="ccn" class="form_control" value="" placeholder="1234 1234 1234 1234">
                                        </div>
                                        <div class="form_group">
                                            <ul class="form_row_three">
                                               <li>
                                                   <label class="form_label">Expiration Year*</label>
                                                   <input type="text" name="year" class="form_control" value="" placeholder="MM/YY">
                                               </li>
                                                <li>
                                                    <label class="form_label">CVC*</label>
                                                    <input type="text" name="ccn" class="form_control" value="" placeholder="XXX">
                                                </li>
                                                <li>
                                                    <label class="form_label">Postal Code</label>
                                                    <input type="text" name="ccn" class="form_control" value="" placeholder="XXXX">
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>



<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
      <div class="service_reviews">
            <div class="container">  
                <div class="reviewBlock">
                    <div class="reviewBlock__inner">
                        <div class="reviewBlock__title">Money Back <span>Guarantee</span></div>
                        <div class="reviewBlock__subtitle">Love it or it’s FREE</div>
                        <p>Order your Life Map today RISK-FREE. You can avail up to 90 days to absorb every shocking detail it reveals about you. Just follow the guidance to improve almost every aspect of your life.</p>
                        <p>If you're not completely satisfied and overwhelmed by our services, just request a refund within 90 days of purchase, and you'll get a prompt 100% refund - without any questions.</p>
                        <a href="#" class="btn btn_purple btn-lg">Order now</a>
                    </div>
                    <div class="reviewBlock__icon">
                        <img src="img/quality.png" class="img-fluid" alt="">
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>

  </div>
</div>


                       <div class="box__content box__content_radius">
                                    <div class="box__inner">
                                    <button type="button" style="cursor:pointer;border: none;background: none;margin: 0 auto;display: block;" class="box__buttons_title" data-toggle="modal" data-target="#myModal">100% moneY back guarantee</button>
                                        <div class="box__buttons">
                                            <a href="page_11.php" class="btn btn_purple">Pay $3 Via Credit Card <i class="fa fa-caret-right"></i></a>
                                            <div class="box__buttons_label">or</div>
                                            <ul class="box__buttons_pay">
                                                <li>
                                                    <a href="page_11.php" class="btn_pay">
                                                        <img src="img/btn_amazon.png" class="img-fluid" alt="">
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="page_11.php" class="btn_pay">
                                                        <img src="img/btn_paypal.png" class="img-fluid" alt="">
                                                    </a>
                                                </li>
                                            </ul>
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

    </body>
</html>
