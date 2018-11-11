<?php

include 'accesscontrol.php';
include 'header.html';
include 'constants.php';
include 'nav.php';

$background_color = BACKGROUND_COLOR;

require_once('../../mysqli_connect_online_calcs_db_MYSQLI.php');
require_once ('../../my_functions_MYSQLI.php');

if ($_POST['submitted'] == "new_em")
{
  echo "<TABLE align='center' WIDTH='98%' BORDER='0' CELLSPACING='15' CELLPADDING='0'>";
    echo "<tr>";
      echo "<td>";
        echo "<center>";
        echo "<strong><font color='ff0000' size='+3'>E-mail Address Change</font></strong>";
        echo "</center>";
      echo "</td>";
    echo "</tr>";
  echo "</table>";

  $password = safeEscapeString($conn, $_POST["password"]);
  $email = safeEscapeString($conn, $_POST["email"]);

  if (!$password Or !$email)
  {
    echo "<center><br><b>Please tell us your password and e-mail address<br><br>";
    echo post_back_message('Change e-mail');
    echo "</b></center><br><br>";
    include 'footer.html';
    exit();
  }

  $pattern = '/.*@.*\..*/';
  if (preg_match($pattern, $email) == 0)
  {
    echo "<center><br><b>Your e-mail address is not valid.<br><br>";
    echo post_back_message('Change e-mail');
    echo "</b></center><br><br>";
    include 'footer.html';
    exit();
  }

  if (!validate_email($email))
  {
    echo "<center><br><b>Your e-mail address is not valid.<br><br>";
    echo post_back_message('Change e-mail');
    echo "</b></center><br><br>";
    include 'footer.html';
    exit();
  }

  $crypt_pwd = md5($password);

  $username = $_SESSION['username'];

  $sql = "SELECT ID FROM member_info WHERE username='$username' AND password='$crypt_pwd'";
  $result = mysqli_query($conn, $sql);
  $row = @mysqli_fetch_array($result);

  if ($result)
  {
    $num_rows1 = MYSQLI_NUM_rows($result);
  }
  else
  {
    $num_rows1 = 0;
  }

  if ($num_rows1 != 1)
  {
    echo "<br><center><font size=5 color=#'000000'><b>We cannot match your password with your e-mail address.<br>Please re-enter your correct password/email.</font><br><br>";
    echo "<font size=3>";
    echo post_back_message('Change e-mail');
    echo "</b></font></center><br><br>";
    include 'footer.html';
    exit();
  }

  $id = $row['ID'];

  $sql = "UPDATE member_info SET email='$email' WHERE ID='$id'";
  $result = @mysqli_query($conn, $sql) or die('Sorry, but I cannot complete your update - your e-mail address was NOT changed. If you like, please try again.');


  echo "Your e-mail address has been changed to<br>$email.<br><br><br>";

  echo "<link href='styles.css' rel='stylesheet' type='text/css' />";
  echo "<div id='content'>";
  include 'logged_in_menu.php';
  echo "</div>";
  include 'footer.html';
  exit();
}
else
{
  ?>

<body>
<div class="container round_block">
    <div class="row">
        <div class="col-md-6">

  <TABLE>
    <tr>
      <td>
        <FORM name="form3" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" target="_blank">
        <TABLE cellSpacing=0 cellPadding=5 border=0>
          <TR>
            <TD align='center' colspan='2'>
              <center>
              <h4>Change your e-mail address?</h4>
              </center>
            </TD>
          </TR>

          <TR>
            <TD align='right'>
              <SPAN class='pa_textbox'>Password:</span>
            </TD>
            <TD align='left'>
              <INPUT class='pa_textbox' type=password maxLength=16 size=16 name=password>
            </TD>
          </TR>

          <TR>
            <TD align='right'>
              <SPAN class='pa_textbox'>E-mail:</span>
            </TD>
            <TD align='left'>
              <INPUT class='pa_textbox' maxLength=40 size=40 name=email style="FONT-WEIGHT: 400; FONT-SIZE: 11px; COLOR: #000000; FONT-FAMILY: Verdana, Arial, sans-serif">
            </TD>
          </TR>

          <TR>
            <TD align='center' colspan='2'>
              <input type="hidden" name="submitted" value="new_em">
              <br><INPUT class='btn btn-primary' type=submit value='Change my e-mail address'>
            </TD>
          </TR>
        </TABLE>
        </FORM>
      </td>
    </tr>
  </table>

  <p>&nbsp;</p>

  </div>
    </div>
</div>
</body>

  <?php
}

include 'footer.html';

?>
