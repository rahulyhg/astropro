<?php
  include ('../accesscontrol.php');

  if ($is_logged_in == False) { exit(); }

  include ('header_event.html');

?>

  <br>
  
  <font size='2'>
    Check for house placement. We are looking at Lords 1, 4, 7 and 10. Are any of these 2-degrees or less to the cusp of any of these four houses?<br><br>

    A PLANET ON A CUSP CONTROLS THAT HOUSE - A PLANET INSIDE A CUSP IS CONTROLLED BY THAT HOUSE.<br><br>

    The difference between a planet in a house while in the sign of the house cusp, and in that house while in another sign is huge. Not only in contest horaries: in any astrological chart. A planet that is in that house but in a different sign to the cusp is much less affected by its placement there, and it affects that house much less.<br><br>
  </font>

<?php
  include ('footer.html');
?>
