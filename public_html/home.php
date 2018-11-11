<?php
/*
 * @author Shahrukh Khan
 * @website http://www.thesoftwareguy.in
 * @facebbok https://www.facebook.com/Thesoftwareguy7
 * @twitter https://twitter.com/thesoftwareguy7
 * @googleplus https://plus.google.com/+thesoftwareguyIn
 */
require_once 'config.php';
if (!isset($_SESSION["user_id"]) && $_SESSION["user_id"] == "") {
  // user already logged in the site
  header("location:" . SITE_URL);
}
include './header.php';
?>
<div class="container col-sm-12">
   <?php if ($_SESSION["e_msg"] <> "") { ?>
    <div class="alert alert-dismissable alert-danger">
      <button data-dismiss="alert" class="close" type="button">x</button>
      <p><?php echo $_SESSION["e_msg"]; ?></p>
    </div>
  <?php } ?>
  <div class="margin10"></div>
  <?php if ($_SESSION["new_user"] == "yes") { ?>
    <h2>Thank you <?php echo $_SESSION["name"] ?>, for registering with us!!!</h2>
	<h5>Your email id is: <span style="text-decoration:underline;"><?php echo $_SESSION["email"]; ?></span></h5>
  <?php } else { ?>
    <h2>Welcome back <?php echo $_SESSION["name"] ?>!!!</h2>
	<h5>Your email id is: <span style="text-decoration:underline;"><?php echo $_SESSION["email"]; ?></span></h5>
  <?php } ?>
  <div class="margin20"></div>
  <div class="col-sm-3">
    <a class="btn btn-block btn-social btn-google-plus" href="<?php echo LOGOUT_URL; ?>">
      <i class="fa fa-google-plus"></i> Logout
    </a>
  </div>
</div>
<?php
include './footer.php';
// unset if after it display the error.
$_SESSION["e_msg"] = "";
?>