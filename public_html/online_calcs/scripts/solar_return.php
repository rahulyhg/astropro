<?php
  include ('../accesscontrol.php');

  if ($is_logged_in == False) { exit(); }

  require_once ('../../../mysqli_connect_online_calcs_db_MYSQLI.php');
  require_once ('../../../my_functions_MYSQLI.php');

  include("constants_eng.php");

  $months = array (0 => 'Choose month', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

  // check if the form has been submitted
  if (isset($_POST['submitted']) Or isset($_POST['h_sys_submitted']))
  {
    $id1 = safeEscapeString($conn, $_POST["id1"]);

    if (!is_numeric($id1))
    {
      echo "<center><br /><br />You have forgotten to make an entry. Please try again.</center>";
      include ('footer.html');
      exit();
    }

    $username = $_SESSION['username'];

    $sql = "SELECT * FROM birth_info WHERE ID='$id1' And entered_by='$username'";

    $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $num_records = MYSQLI_NUM_rows($result);

    if ($num_records != 1)
    {
      echo "<center><br /><br />I cannot find this person in the database. Please try again.</center>";
      include ('footer.html');
      exit();
    }

    // get all variables from database
    $h_sys = safeEscapeString($conn, $_POST["h_sys"]);
    $name1 = $row['name'];

    $month1 = $row['month'];
    $day1 = $row['day'];
    $year1 = $row['year'];

    $hour1 = $row['hour'];
    $minute1 = $row['minute'];

    $timezone1 = $row['timezone'];

    $long_deg1 = $row['long_deg'];
    $long_min1 = $row['long_min'];
    $ew1 = $row['ew'];

    $lat_deg1 = $row['lat_deg'];
    $lat_min1 = $row['lat_min'];
    $ns1 = $row['ns'];


    include ('header_solar_return.html');       //here because of setting cookies above


    if ($ew1 < 0)
    {
      $ew1_txt = "w";
    }
    else
    {
      $ew1_txt = "e";
    }

    if ($ns1 > 0)
    {
      $ns1_txt = "n";
    }
    else
    {
      $ns1_txt = "s";
    }


    // get all variables from form - solar return location
    $start_month = $month1;
    $start_day = $day1;
    $start_year = safeEscapeString($conn, $_POST["start_year"]);

    $timezone2 = safeEscapeString($conn, $_POST["timezone2"]);

    $long_deg2 = safeEscapeString($conn, $_POST["long_deg2"]);
    $long_min2 = safeEscapeString($conn, $_POST["long_min2"]);
    $ew2 = safeEscapeString($conn, $_POST["ew2"]);

    $lat_deg2 = safeEscapeString($conn, $_POST["lat_deg2"]);
    $lat_min2 = safeEscapeString($conn, $_POST["lat_min2"]);
    $ns2 = safeEscapeString($conn, $_POST["ns2"]);

    if ($ew2 < 0)
    {
      $ew2_txt = "w";
    }
    else
    {
      $ew2_txt = "e";
    }

    if ($ns2 > 0)
    {
      $ns2_txt = "n";
    }
    else
    {
      $ns2_txt = "s";
    }


    // additional error checks on user-entered data
    if ($start_year != "")
    {
      if (!$date = checkdate($start_month, $start_day, $start_year))
      {
        $my_error .= "The solar return date you entered is not valid.<br />";
      }
    }

    if (($start_year < 1200) Or ($start_year >= 2400))
    {
      $my_error .= "Solar return date - please enter a year between 1200 and 2399.<br />";
    }

    if (($long_deg2 < 0) Or ($long_deg2 > 179))
    {
      $my_error .= "Longitude degrees must be between 0 and 179.<br />";
    }

    if (($long_min2 < 0) Or ($long_min2 > 59))
    {
      $my_error .= "Longitude minutes must be between 0 and 59.<br />";
    }

    if (($lat_deg2 < 0) Or ($lat_deg2 > 65))
    {
      $my_error .= "Latitude degrees must be between 0 and 65.<br />";
    }

    if (($lat_min2 < 0) Or ($lat_min2 > 59))
    {
      $my_error .= "Latitude minutes must be between 0 and 59.<br />";
    }

    if (($ew2 == '-1') And ($timezone2 > 2))
    {
      $my_error .= "You have marked West longitude but set an east time zone.<br />";
    }

    if (($ew2 == '1') And ($timezone2 < 0))
    {
      $my_error .= "You have marked East longitude but set a west time zone.<br />";
    }


    if ($my_error != "")
    {
      echo "<TABLE align='center' WIDTH='98%' BORDER='0' CELLSPACING='15' CELLPADDING='0'><tr><td><center><b>";
      echo "<font color='#ff0000' size=+2>Error! - The following error(s) occurred:</font><br />";

      echo $my_error;

      echo "</font>";
      echo "</b></center></td></tr></table>";

      include ('footer.html');
      exit();
    }
    else
    {
      // no errors in filling out form, so process form
      $swephsrc = './sweph';    //sweph MUST be in a folder no less than at this level
      $sweph = './sweph';

      putenv("PATH=$PATH:$swephsrc");

      $last_planet_num = 14;        //add a planet

      if (strlen($h_sys) != 1)
      {
        $h_sys = "p";
      }

//Person 1 calculations
      // Unset any variables not initialized elsewhere in the program
      unset($PATH,$out,$pl_name,$longitude1,$house_pos1);

      $inmonth = $month1;
      $inday = $day1;
      $inyear = $year1;

      $inhours = $hour1;
      $inmins = $minute1;
      $insecs = "0";

      $intz = $timezone1;

      $my_longitude = $ew1 * ($long_deg1 + ($long_min1 / 60));
      $my_latitude = $ns1 * ($lat_deg1 + ($lat_min1 / 60));

//this is the old code that worked fine unless LMT was used as a time zone
//      if ($intz >= 0)
//      {
//        $whole = floor($intz);
//        $fraction = $intz - floor($intz);
//      }
//      else
//      {
//        $whole = ceil($intz);
//        $fraction = $intz - ceil($intz);
//      }

//      $inhours = $inhours - $whole;
//      $inmins = $inmins - ($fraction * 60);

//here is the new code that works using LMT
      $abs_tz = abs($intz);
      $the_hours = floor($abs_tz);
      $fraction_of_hour = $abs_tz - floor($abs_tz);
      $the_minutes = 60 * $fraction_of_hour;
      $whole_minutes = floor(60 * $fraction_of_hour);
      $fraction_of_minute = $the_minutes -$whole_minutes;
      $whole_seconds = round(60 * $fraction_of_minute);

      if ($intz >= 0)
      {
        $inhours = $inhours - $the_hours;
        $inmins = $inmins - $whole_minutes;
        $insecs =  $insecs - $whole_seconds;
      }
      else
      {
        $inhours = $inhours + $the_hours;
        $inmins = $inmins + $whole_minutes;
        $insecs =  $insecs + $whole_seconds;
      }
//end of modified code

      // adjust date and time for minus hour due to time zone taking the hour negative
      $utdatenow = strftime("%d.%m.%Y", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));
      $utnow = strftime("%H:%M:%S", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));

      exec ("swetest -edir$sweph -b$utdatenow -ut$utnow -p0123456789DAttt -eswe -house$my_longitude,$my_latitude,$h_sys -fPlsj -g, -head", $out);   //add a planet

      // Each line of output data from swetest is exploded into array $row, giving these elements:
      // 0 = planet name
      // 1 = longitude
      // 2 = speed
      // 3 = house position
      // planets are index 0 - index ($last_planet_num), house cusps are index ($last_planet_num + 1) - ($last_planet_num + 12)
      foreach ($out as $key => $line)
      {
        $row = explode(',',$line);
        $pl_name[$key] = $row[0];
        $longitude1[$key] = $row[1];
        $speed1[$key] = $row[2];
        $house_pos1[$key] = $row[3];
      };

      //calculate the Part of Fortune
      //is this a day chart or a night chart?
      if ($longitude1[$last_planet_num + 1] > $longitude1[$last_planet_num + 7])
      {
        if ($longitude1[0] <= $longitude1[$last_planet_num + 1] And $longitude1[0] > $longitude1[$last_planet_num + 7])
        {
          $day_chart = True;
        }
        else
        {
          $day_chart = False;
        }
      }
      else
      {
        if ($longitude1[0] > $longitude1[$last_planet_num + 1] And $longitude1[0] <= $longitude1[$last_planet_num + 7])
        {
          $day_chart = False;
        }
        else
        {
          $day_chart = True;
        }
      }

      if ($day_chart == True)
      {
        $longitude1[SE_POF] = $longitude1[$last_planet_num + 1] + $longitude1[1] - $longitude1[0];
      }
      else
      {
        $longitude1[SE_POF] = $longitude1[$last_planet_num + 1] - $longitude1[1] + $longitude1[0];
      }

      if ($longitude1[SE_POF] >= 360)
      {
        $longitude1[SE_POF] = $longitude1[SE_POF] - 360;
      }

      if ($longitude1[SE_POF] < 0)
      {
        $longitude1[SE_POF] = $longitude1[SE_POF] + 360;
      }

//add a planet - maybe some code needs to be put here

      //capture the Vertex longitude
      $longitude1[$last_planet_num] = $longitude1[$last_planet_num + 16];


//get house positions of planets here
      for ($x = 1; $x <= 12; $x++)
      {
        for ($y = 0; $y <= $last_planet_num; $y++)
        {
          $pl = $longitude1[$y] + (1 / 36000);
          if ($x < 12 And $longitude1[$x + $last_planet_num] > $longitude1[$x + $last_planet_num + 1])
          {
            If (($pl >= $longitude1[$x + $last_planet_num] And $pl < 360) Or ($pl < $longitude1[$x + $last_planet_num + 1] And $pl >= 0))
            {
              $house_pos1[$y] = $x;
              continue;
            }
          }

          if ($x == 12 And ($longitude1[$x + $last_planet_num] > $longitude1[$last_planet_num + 1]))
          {
            if (($pl >= $longitude1[$x + $last_planet_num] And $pl < 360) Or ($pl < $longitude1[$last_planet_num + 1] And $pl >= 0))
            {
              $house_pos1[$y] = $x;
            }
            continue;
          }

          if (($pl >= $longitude1[$x + $last_planet_num]) And ($pl < $longitude1[$x + $last_planet_num + 1]) And ($x < 12))
          {
            $house_pos1[$y] = $x;
            continue;
          }

          if (($pl >= $longitude1[$x + $last_planet_num]) And ($pl < $longitude1[$last_planet_num + 1]) And ($x == 12))
          {
            $house_pos1[$y] = $x;
          }
        }
      }


// solar return calculations
      // Unset any variables not initialized elsewhere in the program
      unset($out,$pl_name,$longitude2,$speed2);

      $name2 = "Solar return";

    // get solar return birthday
      $n_sun = $longitude1[0];

      $start_JD = gregoriantojd($month1, $day1, $start_year) - 0.5;       // find Julian day for specified date at midnight.
      $start_JD = $start_JD - 5;      // start a little early so we dont miss the birthday

      // find when Sun is at natal longitudinal position
      $p1_idx = 0;    // Sun index into Swiss ephemeris - for calculating new Sun position
      $Result_JD = Get_when_planet_is_at_certain_degree($n_sun, $p1_idx, $pl_name, $start_JD, $start_JD + 10, $timezone1);

      $intz2 = $timezone2;
      $my_longitude2 = $ew2 * ($long_deg2 + ($long_min2 / 60));
      $my_latitude2 = $ns2 * ($lat_deg2 + ($lat_min2 / 60));

//FOR DEBUG
//echo $Result_JD . "<br />";

      exec ("swetest -edir$sweph -bj$Result_JD -ut -p0123456789DAttt -eswe -house$my_longitude2,$my_latitude2,$h_sys -fPlsj -g, -head", $out);  //add a planet

      // Each line of output data from swetest is exploded into array $row, giving these elements:
      // 0 = planet name
      // 1 = longitude
      // 2 = speed
      // 3 = house position
      // planets are index 0 - index ($last_planet_num), house cusps are index ($last_planet_num + 1) - ($last_planet_num + 12)
      foreach ($out as $key => $line)
      {
        $row = explode(',',$line);
        $pl_name[$key] = $row[0];
        $longitude2[$key] = $row[1];
        $speed2[$key] = $row[2];
        $house_pos2[$key] = $row[3];
      };

      //calculate the solar return Part of Fortune
      //is this a day chart or a night chart?
      if ($longitude2[$last_planet_num + 1] > $longitude2[$last_planet_num + 7])
      {
        if ($longitude2[0] <= $longitude2[$last_planet_num + 1] And $longitude2[0] > $longitude2[$last_planet_num + 7])
        {
          $day_chart = True;
        }
        else
        {
          $day_chart = False;
        }
      }
      else
      {
        if ($longitude2[0] > $longitude2[$last_planet_num + 1] And $longitude2[0] <= $longitude2[$last_planet_num + 7])
        {
          $day_chart = False;
        }
        else
        {
          $day_chart = True;
        }
      }

      if ($day_chart == True)
      {
        $longitude2[SE_POF] = $longitude2[$last_planet_num + 1] + $longitude2[1] - $longitude2[0];
      }
      else
      {
        $longitude2[SE_POF] = $longitude2[$last_planet_num + 1] - $longitude2[1] + $longitude2[0];
      }

      if ($longitude2[SE_POF] >= 360)
      {
        $longitude2[SE_POF] = $longitude2[SE_POF] - 360;
      }

      if ($longitude2[SE_POF] < 0)
      {
        $longitude2[SE_POF] = $longitude2[SE_POF] + 360;
      }

//add a planet - maybe some code needs to be put here

      //capture the Vertex longitude
      $longitude2[$last_planet_num] = $longitude2[$last_planet_num + 16];


//get house positions of planets here
      for ($x = 1; $x <= 12; $x++)
      {
        for ($y = 0; $y <= $last_planet_num; $y++)
        {
          $pl = $longitude2[$y] + (1 / 36000);
          if ($x < 12 And $longitude2[$x + $last_planet_num] > $longitude2[$x + $last_planet_num + 1])
          {
            If (($pl >= $longitude2[$x + $last_planet_num] And $pl < 360) Or ($pl < $longitude2[$x + $last_planet_num + 1] And $pl >= 0))
            {
              $house_pos2[$y] = $x;
              continue;
            }
          }

          if ($x == 12 And ($longitude2[$x + $last_planet_num] > $longitude2[$last_planet_num + 1]))
          {
            if (($pl >= $longitude2[$x + $last_planet_num] And $pl < 360) Or ($pl < $longitude2[$last_planet_num + 1] And $pl >= 0))
            {
              $house_pos2[$y] = $x;
            }
            continue;
          }

          if (($pl >= $longitude2[$x + $last_planet_num]) And ($pl < $longitude2[$x + $last_planet_num + 1]) And ($x < 12))
          {
            $house_pos2[$y] = $x;
            continue;
          }

          if (($pl >= $longitude2[$x + $last_planet_num]) And ($pl < $longitude2[$last_planet_num + 1]) And ($x == 12))
          {
            $house_pos2[$y] = $x;
          }
        }
      }


//get house positions of planets here - person 2 planets in person 1 houses
      for ($x = 1; $x <= 12; $x++)
      {
        for ($y = 0; $y <= LAST_PLANET; $y++)
        {
          $pl = $longitude2[$y] + (1 / 36000);
          if ($x < 12 And $longitude1[$x + LAST_PLANET] > $longitude1[$x + LAST_PLANET + 1])
          {
            If (($pl >= $longitude1[$x + LAST_PLANET] And $pl < 360) Or ($pl < $longitude1[$x + LAST_PLANET + 1] And $pl >= 0))
            {
              $house_pos2_in_1[$y] = $x;
              continue;
            }
          }

          if ($x == 12 And ($longitude1[$x + LAST_PLANET] > $longitude1[LAST_PLANET + 1]))
          {
            if (($pl >= $longitude1[$x + LAST_PLANET] And $pl < 360) Or ($pl < $longitude1[LAST_PLANET + 1] And $pl >= 0))
            {
              $house_pos2_in_1[$y] = $x;
            }
            continue;
          }

          if (($pl >= $longitude1[$x + LAST_PLANET]) And ($pl < $longitude1[$x + LAST_PLANET + 1]) And ($x < 12))
          {
            $house_pos2_in_1[$y] = $x;
            continue;
          }

          if (($pl >= $longitude1[$x + LAST_PLANET]) And ($pl < $longitude1[LAST_PLANET + 1]) And ($x == 12))
          {
            $house_pos2_in_1[$y] = $x;
          }
        }
      }


//display natal data
      $secs = "0";
      if ($timezone1 < 0)
      {
        $tz1 = $timezone1;
      }
      else
      {
        $tz1 = "+" . $timezone1;
      }

      if ($timezone2 < 0)
      {
        $tz2 = $timezone2;
      }
      else
      {
        $tz2 = "+" . $timezone2;
      }

      $name_without_slashes = stripslashes($name1);

      $the_dt = ConvertJDtoDateandTime($Result_JD, $timezone2);

      echo "<center>";
//      echo "<FONT color='#0000ff' SIZE='3' FACE='Arial'><b>$name_without_slashes</b><br />";
//      echo '<b>Born ' . strftime("%A, %B %d, %Y<br />%X (time zone = GMT $tz1 hours)</b><br />\n", mktime($hour1, $minute1, $secs, $month1, $day1, $year1));
//      echo "<b>" . $long_deg1 . $ew1_txt . $long_min1 . ", " . $lat_deg1 . $ns1_txt . $lat_min1 . "</b><br /><br />";

//      echo "<b>$name2</b><br />";
//      echo '<b>' . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt[3], $the_dt[4], $the_dt[5], $the_dt[0], $the_dt[1], $the_dt[2])) . "<br />(time zone = GMT $tz2 hours)</b><br />\n";
//      echo "<b>" . sprintf("%.3f", $my_longitude2) . ", " . sprintf("%.3f", $my_latitude2) . "</b><br /><br />";
//      echo "</font>";

      $line1 = $name_without_slashes . ", born on " . strftime("%A, %B %d, %Y at %H:%M (time zone = GMT $tz1 hours)", mktime($hour1, $minute1, $secs, $month1, $day1, $year1));
      $line1 = $line1 . " at " . $long_deg1 . $ew1_txt . sprintf("%02d", $long_min1) . " and " . $lat_deg1 . $ns1_txt . sprintf("%02d", $lat_min1);

      $line2 = $name_without_slashes . " (born " . strftime("%b %d, %Y at %H:%M)", mktime($hour1, $minute1, $secs, $month1, $day1, $year1));
      $line2 = $line2 . " - SR on " . strftime("%A, %B %d, %Y at %H:%M (tz = GMT $tz2 hrs)", mktime($the_dt[3], $the_dt[4], $the_dt[5], $the_dt[0], $the_dt[1], $the_dt[2]));
      $line2 = $line2 . " at " . $long_deg2 . $ew2_txt . sprintf("%02d", $long_min2) . " and " . $lat_deg2 . $ns2_txt . sprintf("%02d", $lat_min2);

      $line3 = "Solar Return on " . strftime("%A, %B %d, %Y at %H:%M (time zone = GMT $tz2 hours)", mktime($the_dt[3], $the_dt[4], $the_dt[5], $the_dt[0], $the_dt[1], $the_dt[2]));
      $line3 = $line3 . " at " . $long_deg2 . $ew2_txt . sprintf("%02d", $long_min2) . " and " . $lat_deg2 . $ns2_txt . sprintf("%02d", $lat_min2);

?>
      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <select name="h_sys" size="1">
          <?php
          echo "<option value='p' ";
          if ($h_sys == "p"){ echo " selected"; }
          echo "> Placidus </option>";

          echo "<option value='k' ";
          if ($h_sys == "k"){ echo " selected"; }
          echo "> Koch </option>";

          echo "<option value='r' ";
          if ($h_sys == "r"){ echo " selected"; }
          echo "> Regiomontanus </option>";

          echo "<option value='c' ";
          if ($h_sys == "c"){ echo " selected"; }
          echo "> Campanus </option>";

          echo "<option value='b' ";
          if ($h_sys == "b"){ echo " selected"; }
          echo "> Alcabitus </option>";

          echo "<option value='o' ";
          if ($h_sys == "o"){ echo " selected"; }
          echo "> Porphyrius </option>";

          echo "<option value='m' ";
          if ($h_sys == "m"){ echo " selected"; }
          echo "> Morinus </option>";

          echo "<option value='a' ";
          if ($h_sys == "a"){ echo " selected"; }
          echo "> Equal house - Asc </option>";

          echo "<option value='t' ";
          if ($h_sys == "t"){ echo " selected"; }
          echo "> Topocentric </option>";

          echo "<option value='v' ";
          if ($h_sys == "v"){ echo " selected"; }
          echo "> Vehlow </option>";
          ?>
        </select>

        <input type="hidden" name="id1" value="<?php echo $_POST['id1']; ?>">
        <input type="hidden" name="name1" value="<?php echo stripslashes($_POST['name1']); ?>">
        <input type="hidden" name="month1" value="<?php echo $_POST['month1']; ?>">
        <input type="hidden" name="day1" value="<?php echo $_POST['day1']; ?>">
        <input type="hidden" name="year1" value="<?php echo $_POST['year1']; ?>">
        <input type="hidden" name="hour1" value="<?php echo $_POST['hour1']; ?>">
        <input type="hidden" name="minute1" value="<?php echo $_POST['minute1']; ?>">
        <input type="hidden" name="timezone1" value="<?php echo $_POST['timezone1']; ?>">
        <input type="hidden" name="long_deg1" value="<?php echo $_POST['long_deg1']; ?>">
        <input type="hidden" name="long_min1" value="<?php echo $_POST['long_min1']; ?>">
        <input type="hidden" name="ew1" value="<?php echo $_POST['ew1']; ?>">
        <input type="hidden" name="lat_deg1" value="<?php echo $_POST['lat_deg1']; ?>">
        <input type="hidden" name="lat_min1" value="<?php echo $_POST['lat_min1']; ?>">
        <input type="hidden" name="ns1" value="<?php echo $_POST['ns1']; ?>">

        <input type="hidden" name="start_year" value="<?php echo $_POST['start_year']; ?>">
        <input type="hidden" name="timezone2" value="<?php echo $_POST['timezone2']; ?>">
        <input type="hidden" name="long_deg2" value="<?php echo $_POST['long_deg2']; ?>">
        <input type="hidden" name="long_min2" value="<?php echo $_POST['long_min2']; ?>">
        <input type="hidden" name="ew2" value="<?php echo $_POST['ew2']; ?>">
        <input type="hidden" name="lat_deg2" value="<?php echo $_POST['lat_deg2']; ?>">
        <input type="hidden" name="lat_min2" value="<?php echo $_POST['lat_min2']; ?>">
        <input type="hidden" name="ns2" value="<?php echo $_POST['ns2']; ?>">

        <input type="hidden" name="h_sys_submitted" value="TRUE">
        <INPUT type="submit" name="submit" value="Go" align="middle" style="background-color:#66ff66;color:#000000;font-size:16px;font-weight:bold">
      </form>
<?php
      echo "</center>";

      $pl_name[0] = "Sun";
      $pl_name[1] = "Moon";
      $pl_name[2] = "Mercury";
      $pl_name[3] = "Venus";
      $pl_name[4] = "Mars";
      $pl_name[5] = "Jupiter";
      $pl_name[6] = "Saturn";
      $pl_name[7] = "Uranus";
      $pl_name[8] = "Neptune";
      $pl_name[9] = "Pluto";
      $pl_name[10] = "Chiron";
      $pl_name[11] = "Lilith";
      $pl_name[12] = "True Node";
      $pl_name[13] = "P. of Fortune";   //add a planet
      $pl_name[14] = "Vertex";

      $pl_name[$last_planet_num + 1] = "Ascendant";
      $pl_name[$last_planet_num + 2] = "House 2";
      $pl_name[$last_planet_num + 3] = "House 3";
      $pl_name[$last_planet_num + 4] = "House 4";
      $pl_name[$last_planet_num + 5] = "House 5";
      $pl_name[$last_planet_num + 6] = "House 6";
      $pl_name[$last_planet_num + 7] = "House 7";
      $pl_name[$last_planet_num + 8] = "House 8";
      $pl_name[$last_planet_num + 9] = "House 9";
      $pl_name[$last_planet_num + 10] = "MC (Midheaven)";
      $pl_name[$last_planet_num + 11] = "House 11";
      $pl_name[$last_planet_num + 12] = "House 12";

      $sign_name[1] = "Aries";
      $sign_name[2] = "Taurus";
      $sign_name[3] = "Gemini";
      $sign_name[4] = "Cancer";
      $sign_name[5] = "Leo";
      $sign_name[6] = "Virgo";
      $sign_name[7] = "Libra";
      $sign_name[8] = "Scorpio";
      $sign_name[9] = "Sagittarius";
      $sign_name[10] = "Capricorn";
      $sign_name[11] = "Aquarius";
      $sign_name[12] = "Pisces";

      $house_name[1] = "1st";
      $house_name[2] = "2nd";
      $house_name[3] = "3rd";
      $house_name[4] = "4th";
      $house_name[5] = "5th";
      $house_name[6] = "6th";
      $house_name[7] = "7th";
      $house_name[8] = "8th";
      $house_name[9] = "9th";
      $house_name[10] = "10th";
      $house_name[11] = "11th";
      $house_name[12] = "12th";

      $hr_ob1 = $hour1;
      $min_ob1 = $minute1;

      $unknown_time1 = 0;
      if (($hr_ob1 == 12) And ($min_ob1 == 0))
      {
        $unknown_time1 = 1;       // this person has an unknown birth time
      }

      $hr_ob2 = $hour2;
      $min_ob2 = $minute2;

      $unknown_time2 = 0;
      if (($hr_ob2 == 12) And ($min_ob2 == 0))
      {
        $unknown_time2 = 1;       // this person has an unknown birth time
      }

      $retrograde1 = "";
      for ($i = 0; $i <= SE_TNODE; $i++)
      {
        if ($speed1[$i] < 0)
        {
          $retrograde1 .= "R";
        }
        else
        {
          $retrograde1 .= " ";
        }
      }

      $retrograde2 = "";
      for ($i = 0; $i <= SE_TNODE; $i++)
      {
        if ($speed2[$i] < 0)
        {
          $retrograde2 .= "R";
        }
        else
        {
          $retrograde2 .= " ";
        }
      }

      // to make GET string shorter
      for ($i = 0; $i <= $last_planet_num; $i++)
      {
        $L1[$i] = $longitude1[$i];
        $L2[$i] = $longitude2[$i];
      }

      for ($i = 1; $i <= $last_planet_num; $i++)
      {
        $hc1[$i] = $longitude1[$last_planet_num + $i];
        $hc2[$i] = $longitude2[$last_planet_num + $i];
      }

    $L2[$last_planet_num + 1] = $longitude2[$last_planet_num + 1];
    $L2[$last_planet_num + 2] = $longitude2[$last_planet_num + 10];

    $rx1 = $retrograde1;
    $rx2 = $retrograde2;

    $ubt1 = 0;
    $ubt2 = 0;


    $_SESSION['longitude1_SR'] = $longitude1;
    $_SESSION['hc1_SR'] = $hc1;
    $_SESSION['house_pos1_SR'] = $house_pos1;

    $_SESSION['longitude2_SR'] = $longitude2;
    $_SESSION['hc2_SR'] = $hc2;
    $_SESSION['house_pos2_SR'] = $house_pos2;
    
    $_SESSION['house_pos2_in_1_SR'] = $house_pos2_in_1;


    $wheel_width = 640;
    $wheel_height = $wheel_width + 50;    //includes space at top of wheel for header

    echo "<center>";
    echo "<img border='0' src='natal_wheel_SR1.php?rx1=$rx1&l1=$line1' width='$wheel_width' height='$wheel_height'>";    //add a planet
    echo "<img border='0' src='natal_wheel_SR2.php?rx1=$rx2&l1=$line2' width='$wheel_width' height='$wheel_height'>";   //add a planet
    echo "<br /><br />";


    echo "<img border='0' src='aspect_grid_sr.php?rx1=$rx2&p0=$L2[0]&p1=$L2[1]&p2=$L2[2]&p3=$L2[3]&p4=$L2[4]&p5=$L2[5]&p6=$L2[6]&p7=$L2[7]&p8=$L2[8]&p9=$L2[9]&p10=$L2[10]&p11=$L2[11]&p12=$L2[12]&p13=$L2[13]&p14=$L2[14]&c1=$hc2[1]&c10=$hc2[10]' width='705' height='450'>";   //add a planet - 2 places
    echo "<br /><br />";


    $wheel_width = 800;
    $wheel_height = $wheel_width + 50;    //includes space at top of wheel for header

    echo "<img border='0' src='synastry_wheel_2_SR.php?rx1=$rx1&rx2=$rx2&ubt1=$ubt1&ubt2=$ubt2&l1=$line1&l2=$line3' width='$wheel_width' height='$wheel_height'>";
    echo "<br /><br />";

    echo "<img border='0' src='aspect_grid_natal_and_sr.php?rx1=$rx1&rx2=$rx2&p0=$L1[0]&p1=$L1[1]&p2=$L1[2]&p3=$L1[3]&p4=$L1[4]&p5=$L1[5]&p6=$L1[6]&p7=$L1[7]&p8=$L1[8]&p9=$L1[9]&p10=$L1[10]&p11=$L1[11]&p12=$L1[12]&p13=$L1[13]&p14=$L1[14]&c1=$hc1[1]&c10=$hc1[10]&p0a=$L2[0]&p1a=$L2[1]&p2a=$L2[2]&p3a=$L2[3]&p4a=$L2[4]&p5a=$L2[5]&p6a=$L2[6]&p7a=$L2[7]&p8a=$L2[8]&p9a=$L2[9]&p10a=$L2[10]&p11a=$L2[11]&p12a=$L2[12]&p13a=$L2[13]&p14a=$L2[14]&p15a=$L2[15]&p16a=$L2[16]' width='830' height='475'>"; //add a planet (2 places)
    echo "</center>";
    echo "<br /><br />";


// display solar return data - aspect table
    $asp_name[1] = "Conjunction";
    $asp_name[2] = "Opposition";
    $asp_name[3] = "Trine";
    $asp_name[4] = "Square";
    $asp_name[5] = "Quincunx";
    $asp_name[6] = "Sextile";

    echo '<center><table width="40%" cellpadding="0" cellspacing="0" border="0">';

    echo '<tr>';
    echo "<td><font color='#0000ff'><b> Solar Return Planet</b></font></td>";
    echo "<td><font color='#0000ff'><b> Aspect </b></font></td>";
    echo "<td><font color='#0000ff'><b> Natal</b></font></td>";
    echo "<td><font color='#0000ff'><b> Orb </b></font></td>";
    echo '</tr>';

    // include Ascendant and MC
    $L1[$last_planet_num + 1] = $hc1[1];
    $L1[$last_planet_num + 2] = $hc1[10];

    $pl_name[$last_planet_num + 1] = "Ascendant";
    $pl_name[$last_planet_num + 2] = "Midheaven";

    for ($i = 0; $i <= $last_planet_num + 2; $i++)
    {
      echo "<tr><td colspan='4'>&nbsp;</td></tr>";
      for ($j = 0; $j <= $last_planet_num + 2; $j++)
      {
        $q = 0;
        $da = Abs($L2[$i] - $L1[$j]);

        if ($da > 180)
        {
          $da = 360 - $da;
        }

        // set orb - 2 if Sun or Moon, 2 if not Sun or Moon
        if ($i == 0 Or $i == 1 Or $j == 0 Or $j == 1)
        {
          $orb = 1;
        }
        else
        {
          $orb = 1;
        }

        // is there an aspect within orb?
        if ($da <= $orb)
        {
          $q = 1;
          $dax = $da;
        }
        elseif (($da <= (60 + $orb)) And ($da >= (60 - $orb)))
        {
          $q = 6;
          $dax = $da - 60;
        }
        elseif (($da <= (90 + $orb)) And ($da >= (90 - $orb)))
        {
          $q = 4;
          $dax = $da - 90;
        }
        elseif (($da <= (120 + $orb)) And ($da >= (120 - $orb)))
        {
          $q = 3;
          $dax = $da - 120;
        }
        elseif (($da <= (150 + $orb)) And ($da >= (150 - $orb)))
        {
          $q = 5;
          $dax = $da - 150;
        }
        elseif ($da >= (180 - $orb))
        {
          $q = 2;
          $dax = 180 - $da;
        }

        if ($q > 0)
        {
          // aspect exists
          echo '<tr>';
          echo "<td>" . $pl_name[$i] . "</td>";
          echo "<td>" . $asp_name[$q] . "</td>";
          echo "<td>" . $pl_name[$j] . "</td>";
          echo "<td>" . sprintf("%.2f", abs($dax)) . "</td>";
          echo '</tr>';
        }
      }
    }

    echo '</table></center>';
    echo "<br /><br />";


    // update count
    $sql = "SELECT solar_returns FROM reports";
    $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);
    $row = mysqli_fetch_array($result);
    $count = $row[solar_returns] + 1;

    $sql = "UPDATE reports SET solar_returns = '$count'";
    $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);


    include ('footer.html');
    exit();
  }
}


Function left($leftstring, $leftlength)
{
  return(substr($leftstring, 0, $leftlength));
}


Function right($rightstring, $rightlength)
{
  return(substr($rightstring, -$rightlength));
}

Function Reduce_below_30($longitude)
{
  $lng = $longitude;

  while ($lng >= 30)
  {
    $lng = $lng - 30;
  }

  return $lng;
}


Function mod360($x)
{
  return $x - (floor($x / 360) * 360);
}


Function Get_OB_Ecl($jd)
{
  // fetch mean obliquity
  $t = ($jd - 2451545) / 36525;

  $epsilon = 23.43929111;
  $epsilon = $epsilon - (46.815 * $t / 3600);
  $epsilon = $epsilon - (0.00059 * $t * $t / 3600);
  $epsilon = $epsilon + (0.001813 * $t * $t * $t / 3600);
  return $epsilon;
}


Function Sine($x)
{
  return sin($x * 3.1415926535 / 180);
}

Function Cosine($x)
{
  return cos($x * 3.1415926535 / 180);
}

Function r2d($x)
{
  return $x * 180 / 3.1415926535;
}

Function d2r($x)
{
  return $x * 3.1415926535 / 180;
}

Function Crunch($x)
{
  if ($x >= 0)
  {
    $y = $x - floor($x / 360) * 360;
  }
  else
  {
    $y = 360 + ($x - ((1 + floor($x / 360)) * 360));
  }

  return $y;
}


Function Convert_Longitude($longitude)
{
  $signs = array (0 => 'Ari', 'Tau', 'Gem', 'Can', 'Leo', 'Vir', 'Lib', 'Sco', 'Sag', 'Cap', 'Aqu', 'Pis');

  $sign_num = floor($longitude / 30);
  $pos_in_sign = $longitude - ($sign_num * 30);
  $deg = floor($pos_in_sign);
  $full_min = ($pos_in_sign - $deg) * 60;
  $min = floor($full_min);
  $full_sec = round(($full_min - $min) * 60);

  if ($deg < 10)
  {
    $deg = "0" . $deg;
  }

  if ($min < 10)
  {
    $min = "0" . $min;
  }

  if ($full_sec < 10)
  {
    $full_sec = "0" . $full_sec;
  }

  return $deg . " " . $signs[$sign_num] . " " . $min . "' " . $full_sec . chr(34);
}


Function mid($midstring, $midstart, $midlength)
{
  return(substr($midstring, $midstart-1, $midlength));
}


Function Get_when_planet_is_at_certain_degree($degr, $p1_idx, $pl_name, $starting_JD, $ending_JD, $current_tz)
{
//aspectarian for when a planet hits a certain degree of the zodiac
  global $num_moon_aspects, $moon_asp_details;

  for ($x = $starting_JD; $x <= $ending_JD; $x++)
  {
    $angle = 0;
    $Result_JD = Secant_Method_one_degree($x, $x + 1, 0.00007, 100, $angle, $p1_idx, $degr);

    $rnd_off = $Result_JD;
    if ($rnd_off >= $x And $rnd_off < $x + 1)
    {
      return $Result_JD;
    }
  }
}


Function Secant_Method_one_degree($earlier_jd, $later_jd, $e, $m, $angle, $p1_idx, $degr)
{
  for ($n = 1; $n <= $m; $n++)
  {
    //get positions of both planets on JD = later_jd and JD = earlier_jd
    $y1 = Get_1_Planet_geo($later_jd, $p1_idx);
    $y3 = Get_1_Planet_geo($earlier_jd, $p1_idx);

    //get distance from exact aspect for both planets on JD = later_jd
    $dayy = $degr - $y1;
    $da = abs($degr - $y1);
    if ($da > 180)
    {
      $da = 360 - $da;
    }
    $dist1 = $da - $angle;
    if ($dayy <= -180 Or ($dayy >= 0 And $dayy < 180))
    {
      $dist1 = -$dist1;
    }

    //get distance from exact aspect for both planets on JD = earlier_jd
    $dayy = $degr - $y3;
    $da = abs($degr - $y3);
    if ($da > 180)
    {
      $da = 360 - $da;
    }
    $dist2 = $da - $angle;
    if ($dayy <= -180 Or ($dayy >= 0 And $dayy < 180))
    {
      $dist2 = -$dist2;
    }

    if ($dist1 - $dist2 == 0)
    {
      $later_jd = ($later_jd + $earlier_jd) / 2;
      $d = 0;
    }
    else
    {
      $d = (($later_jd - $earlier_jd) / ($dist1 - $dist2)) * $dist1;
    }

    if (abs($dist1 - $dist2) > 20 And $n >= 2)
    {
      //keep from looping needlessly AND
      //protect against case where dist1 = -dist2, which gives false aspect
      //example 21 March 2006 - Moon 120 Mars - there is no trine, but an opposition
      $later_jd = 0;
      break;
    }

    if (abs($d) < $e)
    {
      break;
    }

    $earlier_jd = $later_jd;

    if (abs($d) >= 1.001)
    {
      //out of range - there is no aspect in this time frame (1 day)
      $later_jd = 0;
      break;
    }
    else
    {
      $later_jd = $later_jd - $d;
    }
  }

  if ($n > $m)
  {
    return 0;
  }
  else
  {
    return $later_jd;
  }
}


Function Get_1_Planet_geo($jd, $p_idx)
{
  //get longitude of planet indicated by $p_idx
  $swephsrc = './sweph';    //sweph MUST be in a folder no less than at this level
  $sweph = './sweph';

  unset($out,$t_long);
  exec ("swetest -edir$sweph -bj$jd -ut -p$p_idx -eswe -fl -g, -head", $out);

  // Each line of output data from swetest is exploded into array $row, giving these elements:
  // 0 = longitude
  // 1 = speed
  foreach ($out as $key => $line)
  {
    $row = explode(',',$line);
    $t_long[$key] = $row[0];
  };

  return $t_long[0];
}


Function ConvertJDtoDateandTime($Result_JD, $current_tz)
{
  $the_dt = array();

  //returns date and time in local time, e.g. 9/3/2007 4:59 am
  //get calendar day - must adjust for the way the PHP function works by adding 0.5 days to the JD of interest
  $jd_to_use = $Result_JD + $current_tz / 24;

  $JDDate = jdtogregorian($jd_to_use + 0.5);

  $time_stamp = strtotime($JDDate);
  $the_dt[0] = strftime("%m", $time_stamp);
  $the_dt[1] = strftime("%d", $time_stamp);
  $the_dt[2] = strftime("%Y", $time_stamp);

  $fraction = $jd_to_use - floor($jd_to_use);

  $hh = $fraction * 24;

  if ($fraction >= 0.5)
  {
    $hh = $hh - 12;
  }
  else
  {
    $hh = $hh + 12;
  }

  $mm = $hh - floor($hh);
  $secs = floor(($mm * 60 - floor($mm * 60)) * 60);

  $the_dt[3] = floor($hh);
  $the_dt[4] = floor($mm * 60);
  $the_dt[5] = $secs;

  return $the_dt;
}


Function Find_Specific_Report_Paragraph($phrase_to_look_for, $file)
{
  $string = "";
  $len = strlen($phrase_to_look_for);

  //put entire file contents into an array, line by line
  $file_array = file($file);

  // look through each line searching for $phrase_to_look_for
  $flag = False;
  for($i = 0; $i < count($file_array); $i++)
  {
    if ($flag == True)
    {
      $string .= $file_array[$i];

      if (right(trim($file_array[$i]), 1) == "@")
      {
        $string[strrpos($string, '@')] = "";
        break;
      }
    }
    elseif (left(trim($file_array[$i]), $len) == $phrase_to_look_for)
    {
      $flag = True;
      //$string .= $file_array[$i];
      $string .= "<br />" . $phrase_to_look_for . "<br />";
    }
  }

  return $string;
}

?>
