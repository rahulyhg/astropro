<?php
  include ('../accesscontrol.php');

  if ($is_logged_in == False)
  {
    echo "You are not yet logged in.";
    exit();
  }

    include ('../header.html');
    include '../nav2.php';
  // connect to the database and point to the proper database
  require_once ('../../../mysqli_connect_online_calcs_db_MYSQLI.php');
  require_once ('../../../my_functions_MYSQLI.php');

  $PHP_SELF = "entry_update.php";

  $username = $_SESSION['username'];

  if (isset($_POST['submit_update']) == True)
  {
    // process form
    // get ID and other information
    $id = safeEscapeString($conn, $_POST["idx"]);
    $name = safeEscapeString($conn, $_POST["name"]);
    
    $name = str_replace(" ", "_", $name);
    $name = str_replace(chr(39), "~", $name);
    
    $original_name = safeEscapeString($conn, $_POST["original_name"]);
    $sex = safeEscapeString($conn, $_POST["sex"]);

    $month = intval(safeEscapeString($conn, $_POST["month"]));
    $day = intval(safeEscapeString($conn, $_POST["day"]));
    $year = intval(safeEscapeString($conn, $_POST["year"]));

    $hour = intval(safeEscapeString($conn, $_POST["hour"]));
    $minute = intval(safeEscapeString($conn, $_POST["minute"]));

    $timezone = safeEscapeString($conn, $_POST["timezone"]);

    $long_deg = intval(safeEscapeString($conn, $_POST["long_deg"]));
    $long_min = intval(safeEscapeString($conn, $_POST["long_min"]));
    $ew = safeEscapeString($conn, $_POST["ew"]);

    $lat_deg = intval(safeEscapeString($conn, $_POST["lat_deg"]));
    $lat_min = intval(safeEscapeString($conn, $_POST["lat_min"]));
    $ns = safeEscapeString($conn, $_POST["ns"]);


    if (strtolower($ew) == "w") { $ew = -1; }
    if (strtolower($ew) == "e") { $ew = 1; }
    
    if (strtolower($ns) == "n") { $ns = 1; }
    if (strtolower($ns) == "s") { $ns = -1; }


    //check for more errors here
    $err = "";

    if (strtolower($sex) != "m" And strtolower($sex) != "f") { $err .= "Please enter either 'm' for male or 'f' for female.<br>"; }
    if (abs($ew) != 1) { $err .= "Please enter either '-1' for west longitude or '1' for east longitude.<br>"; }
    if (abs($ns) != 1) { $err .= "Please enter either '1' for north latitude or '-1' for south latitude.<br>"; }
    
    if ($month < 1) { $err .= "Please enter a valid month from 1 to 12.<br>"; }
    if ($month > 12) { $err .= "Please enter a valid month from 1 to 12.<br>"; }
    
    if ($day < 1) { $err .= "Please enter a valid day from 1 to 31.<br>"; }
    if ($day > 31) { $err .= "Please enter a valid day from 1 to 31.<br>"; }

    if ($hour > 23) { $err .= "Please enter a valid hour from 0 to 23.<br>"; }
    if ($minute > 59) { $err .= "Please enter a valid minute from 0 to 59.<br>"; }

    if ($err == "")
    {
      //delete this name from the 'scores' table
      $sql = "DELETE FROM scores WHERE entered_by='$username' And name_f='$original_name'";
      $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);

      //delete this name from the 'scores' table
      $sql = "DELETE FROM scores WHERE entered_by='$username' And name_m='$original_name'";
      $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);

      //update birth_info table in database for this record
      $sql = "UPDATE birth_info SET
        name='$name',
        sex='$sex',
        month='$month',
        day='$day',
        year='$year',
        hour='$hour',
        minute='$minute',
        timezone='$timezone',
        long_deg='$long_deg',
        long_min='$long_min',
        ew='$ew',
        lat_deg='$lat_deg',
        lat_min='$lat_min',
        ns='$ns' WHERE ID='$id' And entered_by='$username'";

      $result_1 = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);

      if ($result_1)
      {
        echo "<center><br /><FONT color='#ff4fff' SIZE='6' FACE='Arial'><b>Thank you! Record updated.</b></font></center>";
      }
      else
      {
        echo "<center><br /><FONT color='#ff0000' SIZE='7' FACE='Arial'><b>Update FAILED</b></font></center>";
      }

      echo "<br /><br />";

      echo "<center><a href='view_records.php'>Return to your database home page</a></center><br /><br />";
      
      include ('../footer.html');
      
      exit();
    }
  }

    // not a submitted form - get ID if this is not an error in input
  if (isset($_POST['submit_update']) == False) { $id = safeEscapeString($conn, $_GET["ID"]); }

  //fetch current data for this ID # from the 'birth_info' database
  $sql = "SELECT * FROM birth_info WHERE ID='$id' And entered_by='$username'";
  $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);
  $row = mysqli_fetch_array($result);

  $existing_name = $row['name'];
  $original_name = $row['name'];
  $existing_sex = $row['sex'];
  $existing_month = $row['month'];
  $existing_day = $row['day'];
  $existing_year = $row['year'];
  $existing_hour = $row['hour'];
  $existing_minute = $row['minute'];
  $existing_timezone = $row['timezone'];
  $existing_long_deg = $row['long_deg'];
  $existing_long_min = $row['long_min'];
  $existing_ew = $row['ew'];
  $existing_lat_deg = $row['lat_deg'];
  $existing_lat_min = $row['lat_min'];
  $existing_ns = $row['ns'];

  ?>

<div class="container round_block">
    <div class="row">
        <div class="col-md-12">
  <form method="POST" action="<?php echo $PHP_SELF?>">
    <input type="hidden" name="idx" value="<?php echo $id ?>">

    <table style="font-size:12px;">
      <TR>
        <TD><P align="right">&nbsp;</P></TD>
        <TD><FONT color='#0000ff' SIZE='4' FACE='Arial'><b>For ID #<?php echo $id; ?> </b></font></TD>
      </TR>

      <?php
        if ($err != "")
        {
          echo "<TR>";
            echo "<TD><P align='right'>&nbsp;</P></TD>";

            echo "<TD>";
              echo "<img border='0' src='error2.gif'><img border='0' src='error2.gif'><img border='0' src='error2.gif'>";
      
              echo "<font color='#ff0000'><b> - THERE ARE ONE OR MORE ERRORS WHICH YOU NEED TO CORRECT BEFORE CONTINUING. THEY ARE:<br><br>";
              echo $err . "<br>";
              echo "</b></font>";
          }
          
          echo "</TD>";
        echo "</TR>";
      ?>

      <TR>
        <TD><P align="right">Name: </P></TD>
        <TD><INPUT size="40" name="name" value="<?php echo $existing_name; ?>"></TD>
      </TR>

      <TR>
        <TD><P align="right">Sex: </P></TD>
        <TD><INPUT size="3" name="sex" value="<?php echo $existing_sex; ?>">&nbsp;&nbsp;&nbsp;Enter m or f.</TD>
      </TR>

      <TR>
        <TD><P align="right">Month: </P></TD>
        <TD><INPUT size="3" name="month" value="<?php echo $existing_month; ?>"></TD>
      </TR>

      <TR>
        <TD><P align="right">Day: </P></TD>
        <TD><INPUT size="3" name="day" value="<?php echo $existing_day; ?>"></TD>
      </TR>

      <TR>
        <TD><P align="right">Year: </P></TD>
        <TD><INPUT size="4" name="year" value="<?php echo $existing_year; ?>"></TD>
      </TR>

      <TR>
        <TD><P align="right">Hour: </P></TD>
        <TD><INPUT size="3" name="hour" value="<?php echo $existing_hour; ?>">&nbsp;&nbsp;&nbsp;Please enter as 24-hour time, so 5 pm is entered as 17. Do not enter a value greater than 23. Enter 0 for midnight.</TD>
      </TR>

      <TR>
        <TD><P align="right">Minute: </P></TD>
        <TD><INPUT size="3" name="minute" value="<?php echo $existing_minute; ?>">&nbsp;&nbsp;&nbsp;This value should NOT be greater than 59.</TD>
      </TR>

      <TR>
        <TD><P align="right">Time zone: </P></TD>
        <TD><INPUT size="5" name="timezone" value="<?php echo $existing_timezone; ?>">&nbsp;&nbsp;&nbsp;In general, West longitude (like Chicago) is a minus time zone and East longitude (like Berlin) is a plus time zone. GMT = 0.</TD>
      </TR>

      <TR>
        <TD><P align="right">Long deg: </P></TD>
        <TD><INPUT size="3" name="long_deg" value="<?php echo $existing_long_deg; ?>">&nbsp;&nbsp;&nbsp;This value should NOT be greater than 180.</TD>
      </TR>

      <TR>
        <TD><P align="right">Long min: </P></TD>
        <TD><INPUT size="3" name="long_min" value="<?php echo $existing_long_min; ?>">&nbsp;&nbsp;&nbsp;This value should NOT be greater than 59.</TD>
      </TR>

      <TR>
        <TD><P align="right">E/W: </P></TD>
        <TD><INPUT size="3" name="ew" value="<?php echo $existing_ew; ?>">&nbsp;&nbsp;&nbsp;West longitude (like Chicago or New York) is -1 and East longitude (like Berlin) is 1.</TD>
      </TR>

      <TR>
        <TD><P align="right">Lat deg: </P></TD>
        <TD><INPUT size="3" name="lat_deg" value="<?php echo $existing_lat_deg; ?>">&nbsp;&nbsp;&nbsp;This value should NOT be greater than 65.</TD>
      </TR>

      <TR>
        <TD><P align="right">Lat min: </P></TD>
        <TD><INPUT size="3" name="lat_min" value="<?php echo $existing_lat_min; ?>">&nbsp;&nbsp;&nbsp;This value should NOT be greater than 59.</TD>
      </TR>

      <TR>
        <TD><P align="right">N/S: </P></TD>
        <TD><INPUT size="3" name="ns" value="<?php echo $existing_ns; ?>">&nbsp;&nbsp;&nbsp;North latitude is 1 and South latitude is -1.</TD>
      </TR>

      <TR>
        <TD><P align="right">&nbsp;</P></TD>
        <input type="hidden" name="original_name" value="<?php echo $original_name; ?>">
        <TD><input type="submit" name="submit_update" value="Submit" align="middle" style="background-color:#00ff00;color:#000000;font-size:16px;font-weight:bold"></TD>
      </TR>
    </table>
  </form>
        </div>
    </div>
</div>
  <?php

  include ('../footer.html');
?>
