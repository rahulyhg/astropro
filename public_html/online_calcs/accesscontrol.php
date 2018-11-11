<?php
  session_start();

  date_default_timezone_set('America/Denver');
  //date_default_timezone_set('America/Phoenix');

  $is_logged_in = false;

  if (isset($_SESSION['username']) && isset($_SESSION['username_hash']))
  {
    $hashy = $_SESSION['username'] . "; " . $my_hash_padding;
    $hash = md5($hashy);

    if ($hash == $_SESSION['username_hash'])
    {
      $is_logged_in = true;
    }
    else
    {
      $is_logged_in = false;
      unset($_SESSION['username']);
      unset($_SESSION['email']);
      unset($_SESSION['member_ID']);
      unset($_SESSION['username_hash']);
    }
  }

  if ($is_logged_in == false)
  {
    unset($_SESSION['username']);
    unset($_SESSION['email']);
    unset($_SESSION['member_ID']);
    unset($_SESSION['username_hash']);

    echo "<html>";
    echo "<head>";
    echo "</head>";

    echo "<body text='#000000' link='#0000FF' vlink='#ff0000'>";
    echo "</BODY>";
    echo "</HTML>";
  }
?>
