<?php
  include ('../header.html');
  include ('../constants.php');           //nedded because of "../footer.html" statements
?>

<br>

<table width='41%' align='center' style="font-size:12px;">
  <tr>
    <td align='center' style='font-family:Arial'>
      <b>Please fill out the below and click NEXT in order to get your latitude, longitude, and time zone data.</b>
      <br><br>
      <iframe src="http://www.astrotheme.fr/partenaires/atlas.php?partenaire=9999&lang=en" frameborder="0" width="440" height="350"></iframe>
    </td>
  </tr>

  <tr><td>&nbsp;</td></tr>
    
  <tr>
    <td align='center' style='font-family:Arial'>
      <b>When you have completed filling out the above form, you should see something that looks like the below. The important 
      information you want is the data in the "Latitude:", "Longitude:", and Time Difference:" lines. Copy this information 
      into the proper form and continue. For example, if the "Time Difference:" is shown as '6W00', then select 'GMT -06:00 hrs 
      - CST or MDT or MWT' in the 'Time zone' drop-down box.</b>
    </td>
  </tr>

  <tr><td>&nbsp;</td></tr>
    
  <tr>
    <td align='center'>
      <img src='../images/atlas_example.jpg'>
    </td>
  </tr>
</table>


<?php include '../footer.html'; ?>
