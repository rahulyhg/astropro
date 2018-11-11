<?php
Function DisplayResults($dynes, $num_MRs)
{
  $total_harmony = $dynes[1] + ($num_MRs * 5);

  echo "<br />";
  echo "<font color='#ff0000' size='5' face='Arial'><b>RESULTS</b></font><br /><br />";

  echo '<table width="75%" cellpadding="0" cellspacing="0" border="0">';
    echo "<tr><td colspan='4'><hr></td></tr>";

    echo "<tr>";
      echo "<td>";
        ?>
          <div class="clock" >
            <!-- <img src="meter.png" class="clock_img" /> -->
            <div id="speedometer"></div>
          </div>

          <input type="hidden" id="update" value="start" />
          <input type="hidden" id="maxvalue" value="100" />
          <input type="hidden" id="rescale" value="rescale" />
          <input type="hidden" name="mode" id="incremental" checked="checked" />
          <input type="hidden" name="mode" id="random" />
          <input type="hidden" name="matchedScore" id="matchedScore" value="<?php echo $total_harmony; ?>" />
        <?php
      echo "</td>";

      echo "<td>";
        echo "<table align='center'>";
          echo "<tr>";
  echo "<td><font color='#0000ff'><b> Power </b></font></td>";
  echo "<td><font color='#0000ff'><b> Harmony </b></font></td>";
  echo "<td><font color='#0000ff'><b> Mutual<br>Receptions </b></font></td>";
          echo "</tr>";

          echo "<tr>";
  echo "<td>" . sprintf("%.2f", $dynes[0]) . "</td>";

            if ($total_harmony >= 9.8)
  {
              echo "<td><font size='+1' color='#009000'>" . sprintf("%.2f", ($total_harmony)) . "</font></td>";
  }
            elseif ($total_harmony < 0)
  {
              echo "<td><font size='+1' color='#ff0000'>" . sprintf("%.2f", ($total_harmony)) . "</font></td>";
  }
  else
  {
              echo "<td><font size='+1' color='#000000'>" . sprintf("%.2f", ($total_harmony)) . "</font></td>";
  }

  echo "<td>" . $num_MRs . "</td>";
          echo "<tr>";

          echo "<tr>";
            echo "<td colspan='3'>";
              echo "<br />";
  echo "An average HARMONY score is about +10. Negative scores (red) show discord between two people.";
              echo "<br /><br />";

              echo "Each mutual reception adds +5 harmony points, which is included in the harmony total.<br /><br />";
  echo "Learn more about cosmodynes <a href='http://www.astrowin.org/cosmodynes.php'>here</a>.";
            echo "</td>";
          echo "<tr>";
        echo "</table>";
      echo "</td>";
    echo '</tr>';

    echo "<tr><td colspan='4'><hr></td></tr>";
  echo '</table>';

  echo "<br /><br />";
}

?>
