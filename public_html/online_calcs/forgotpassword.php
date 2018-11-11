<?php

include 'header.html';
include ('constants.php');
  
$background_color = BACKGROUND_COLOR;


require_once('../../mysqli_connect_online_calcs_db_MYSQLI.php');
require_once ('../../my_functions_MYSQLI.php');

if ($_POST['submitted'] == "new_pwd")
{
  echo "<TABLE align='center' WIDTH='98%' BORDER='0' CELLSPACING='15' CELLPADDING='0'>";
    echo "<tr>";
      echo "<td>";
        echo "<center>";
        echo "<strong><font color='0000ff' size='+3'>Password Notification</font></strong>";
        echo "</center>";
      echo "</td>";
    echo "</tr>";
  echo "</table>";

  $username = safeEscapeString($conn, $_POST["username"]);
  $email = safeEscapeString($conn, $_POST["email"]);

  if (!$username Or !$email)
  {
    echo "<center><br><b>Please tell us your username and e-mail address<br><br>";
    echo post_back_message('Forgot password');
    echo "</b></center><br><br>";
    include 'footer.html';
    exit();
  }

  $sql = "SELECT ID, username, email FROM member_info WHERE username='$username' And email='$email'";
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
    echo "<br><center><font size=5 color=#'000000'><b>We cannot find the username/email combination you entered.<br>Please re-enter your correct username/email.</font><br><br>";
    echo "<font size=3>";
    echo post_back_message('Forgot password');
    echo "</b></font></center><br><br>";
    include 'footer.html';
    exit();
  }

//find ID and e-mail address on file
  $id = $row['ID'];
  $email = $row['email'];

//generate new random password here
  $alphanum = array('a','b','c','d','e','f','g','h','i','j','k','m','n','o','p','q','r','s','t','u','v','x','y','z','A','B','C','D','E','F','G','H','^','J','K','M','N','P','Q','R','S','T','U','V','W','X','Y','Z','2','3','4','5','6','7','8','9');
  $chars = sizeof($alphanum);
  $a = time();
  mt_srand($a);
  for ($i=0; $i < 8; $i++)
  {
    $randnum = intval(mt_rand(0,56));
    $password .= $alphanum[$randnum];
  }

  $crypt_pwd = md5($password);

  $sql = "UPDATE member_info SET password='$crypt_pwd' WHERE ID='$id'";
  $result = @mysqli_query($conn, $sql) or die('Sorry, but I cannot complete your update - your password was NOT changed. If you like, please try again.');


  //the below is what needs changing according to individual situation - e-mail settings
  $emailTo = $email;
  $emailFrom = EMAIL_ADDRESS;
  $emailSubject = "Here is the information you requested";
  $emailText =  "Here is your new password:\n\n";
  //change the above to suit your situation

  //here is the data to be submitted
  $emailText .= "Password              = $password \n\n";

  @mail($emailTo, $emailSubject, $emailText, "From: $emailFrom");

  echo "<center><font FACE='Verdana' size='6' color='#8800FF'>A new password has been e-mailed to you.</font></center><br><br><br>";
  echo "<center><a href='signup_login.php'>Return to registration page</a></center><br>";

  include 'footer.html';
  exit();
}
else
{
  ?>
  <style type='text/css'>
  .pa_textbox
  {
    FONT-WEIGHT: 400; FONT-SIZE: 11px; COLOR: #000000; FONT-FAMILY: Verdana, Arial, sans-serif
  }
  </style>

  <div id="content">

  <TABLE bgcolor="<?php echo $background_color; ?>" align=center cellSpacing=0 cellPadding=3 border=0>
    <tr>
      <td>
        <FORM name="form3" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" target="_blank">
        <TABLE cellSpacing=0 cellPadding=5 border=0>
          <TR>
            <TD align='center' colspan='2'>
              <center>
              <h4>Forgot your password?<br><br>
              You may have a new password e-mailed to the<br>
              e-mail address you have on file with us.<br></h4>
              </center>
            </TD>
          </TR>

          <TR>
            <TD align='right'>
              <SPAN class='pa_textbox'>Username:</span>
            </TD>
            <TD align='left'>
              <INPUT class='pa_textbox' maxLength=16 size=16 name=username style="FONT-WEIGHT: 400; FONT-SIZE: 11px; COLOR: #000000; FONT-FAMILY: Verdana, Arial, sans-serif">
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
              <input type="hidden" name="submitted" value="new_pwd">
              <br><INPUT type=submit value='E-mail me a new random password' style="width: 330px; BORDER-TOP-WIDTH: 1px; FONT-WEIGHT: bold; BORDER-LEFT-WIDTH: 1px; FONT-SIZE: 11px; BORDER-LEFT-COLOR: #ff9eb9; BACKGROUND: #b70000 no-repeat 5px 3px; BORDER-BOTTOM-WIDTH: 1px; BORDER-BOTTOM-COLOR: #990049; COLOR: #ffffff; BORDER-TOP-COLOR: #ff9eb9; FONT-FAMILY: Verdana, Arial, sans-serif; BORDER-RIGHT-WIDTH: 1px; BORDER-RIGHT-COLOR: #990049">
            </TD>
          </TR>
        </TABLE>
        </FORM>
      </td>
    </tr>
  </table>

  <p>&nbsp;</p>

  </div>

  <?php
}

include 'footer.html';

?>
