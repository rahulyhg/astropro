<?php
  include ('../accesscontrol.php');

  if ($is_logged_in == False) { exit(); }

  include ('header_event.html');

?>

  <br>
  
  <font size='2'>
    Our significators are affected by close aspects from other planets, for better or for worse.<br><br>

    Conjunctions, trines, and sextiles show things happening easily; squares and oppositions show them happening with difficulty.<br>
  </font>

<?php
  include ('footer.html');
?>
