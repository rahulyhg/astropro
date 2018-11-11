<?php
  include ('../accesscontrol.php');

  if ($is_logged_in == False) { exit(); }

  include ('header_contest.html');

?>

  <br>
  
  <font size='2'>
    Our significators are affected by close aspects from other planets, for better or for worse. <b>The condition of the aspecting planet, not the nature of the aspect, shows if that aspect is good or bad.</b><br><br>

    We are concerned only with what strengthens or weakens the significators. A square from a good planet will be helpful; a trine from a bad one will be weakening. An opposition is always an affliction.<br><br>

    If a planet has essential dignity, then it is nice. If it is peregrine or, even worse, in its detriment or fall, then it is nasty. An aspect from Saturn in Capricorn is nice - an aspect from Jupiter in Virgo is nasty. A square from Mars in Scorpio will help our significator, while a trine from Venus in Aries will harm it.<br><br>
      
    Conjunctions, trines, and sextiles show things happening easily; squares and oppositions show them happening with difficulty.<br>
  </font>

<?php
  include ('footer.html');
?>
