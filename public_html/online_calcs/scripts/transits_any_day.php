<?php
  session_start();
  
  $months = array (0 => 'Choose month', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
  $my_error = "";

  require_once ('../../../mysqli_connect_online_calcs_db_MYSQLI.php');
  //require_once ('../../../my_functions_MYSQLI.php');

  // check if the form has been submitted
  if (isset($_POST['submitted']) Or isset($_POST['h_sys_submitted']))
  {
    // get all variables from form
    $h_sys = safeEscapeString($conn, $_POST["h_sys"]);
    $name = safeEscapeString($conn, $_POST["name"]);

    $month = safeEscapeString($conn, $_POST["month"]);
    $day = safeEscapeString($conn, $_POST["day"]);
    $year = safeEscapeString($conn, $_POST["year"]);

    $hour = safeEscapeString($conn, $_POST["hour"]);
    $minute = safeEscapeString($conn, $_POST["minute"]);

    $timezone = safeEscapeString($conn, $_POST["timezone"]);

    $long_deg = safeEscapeString($conn, $_POST["long_deg"]);
    $long_min = safeEscapeString($conn, $_POST["long_min"]);
    $ew = safeEscapeString($conn, $_POST["ew"]);

    $lat_deg = safeEscapeString($conn, $_POST["lat_deg"]);
    $lat_min = safeEscapeString($conn, $_POST["lat_min"]);
    $ns = safeEscapeString($conn, $_POST["ns"]);

    // set cookie containing transit data here
    setcookie ('name', stripslashes($name), time() + 60 * 60 * 24 * 30, '/', '', 0);

    setcookie ('month', $month, time() + 60 * 60 * 24 * 30, '/', '', 0);
    setcookie ('day', $day, time() + 60 * 60 * 24 * 30, '/', '', 0);
    setcookie ('year', $year, time() + 60 * 60 * 24 * 30, '/', '', 0);

    setcookie ('hour', $hour, time() + 60 * 60 * 24 * 30, '/', '', 0);
    setcookie ('minute', $minute, time() + 60 * 60 * 24 * 30, '/', '', 0);

    setcookie ('timezone', $timezone, time() + 60 * 60 * 24 * 30, '/', '', 0);

    setcookie ('long_deg', $long_deg, time() + 60 * 60 * 24 * 30, '/', '', 0);
    setcookie ('long_min', $long_min, time() + 60 * 60 * 24 * 30, '/', '', 0);
    setcookie ('ew', $ew, time() + 60 * 60 * 24 * 30, '/', '', 0);

    setcookie ('lat_deg', $lat_deg, time() + 60 * 60 * 24 * 30, '/', '', 0);
    setcookie ('lat_min', $lat_min, time() + 60 * 60 * 24 * 30, '/', '', 0);
    setcookie ('ns', $ns, time() + 60 * 60 * 24 * 30, '/', '', 0);

    include ('header_transit_any_day.html');        //here because of setting cookies above

    include("validation_class.php");

    //error check
    $my_form = new Validate_fields;

    $my_form->check_4html = true;

    $my_form->add_text_field("Name", $name, "text", "y", 40);

    $my_form->add_text_field("Month", $month, "text", "y", 2);
    $my_form->add_text_field("Day", $day, "text", "y", 2);
    $my_form->add_text_field("Year", $year, "text", "y", 4);

    $my_form->add_text_field("Hour", $hour, "text", "y", 2);
    $my_form->add_text_field("Minute", $minute, "text", "y", 2);

    $my_form->add_text_field("Time zone", $timezone, "text", "y", 17);

    $my_form->add_text_field("Longitude degree", $long_deg, "text", "y", 3);
    $my_form->add_text_field("Longitude minute", $long_min, "text", "y", 2);
    $my_form->add_text_field("Longitude E/W", $ew, "text", "y", 2);

    $my_form->add_text_field("Latitude degree", $lat_deg, "text", "y", 2);
    $my_form->add_text_field("Latitude minute", $lat_min, "text", "y", 2);
    $my_form->add_text_field("Latitude N/S", $ns, "text", "y", 2);

    // additional error checks on user-entered data
    if ($month == 0)
    {
      $my_error .= "Please enter a month.<br />";
    }

    if ($month != "" And $day != "" And $year != "")
    {
      if (!$date = checkdate(settype ($month, "integer"), settype ($day, "integer"), settype ($year, "integer")))
      {
        $my_error .= "The date of birth you entered is not valid.<br />";
      }
    }

    if (($year < 1200) Or ($year >= 2400))
    {
      $my_error .= "Please enter a year between 1200 and 2399.<br />";
    }

    if (($hour < 0) Or ($hour > 23))
    {
      $my_error .= "Birth hour must be between 0 and 23.<br />";
    }

    if (($minute < 0) Or ($minute > 59))
    {
      $my_error .= "Birth minute must be between 0 and 59.<br />";
    }

    if (($long_deg < 0) Or ($long_deg > 179))
    {
      $my_error .= "Longitude degrees must be between 0 and 179.<br />";
    }

    if (($long_min < 0) Or ($long_min > 59))
    {
      $my_error .= "Longitude minutes must be between 0 and 59.<br />";
    }

    if (($lat_deg < 0) Or ($lat_deg > 65))
    {
      $my_error .= "Latitude degrees must be between 0 and 65.<br />";
    }

    if (($lat_min < 0) Or ($lat_min > 59))
    {
      $my_error .= "Latitude minutes must be between 0 and 59.<br />";
    }

    if (($ew == '-1') And ($timezone > 2))
    {
      $my_error .= "You have marked West longitude but set an east time zone.<br />";
    }

    if (($ew == '1') And ($timezone < 0))
    {
      $my_error .= "You have marked East longitude but set a west time zone.<br />";
    }

    if ($ew < 0)
    {
      $ew_txt = "w";
    }
    else
    {
      $ew_txt = "e";
    }

    if ($ns > 0)
    {
      $ns_txt = "n";
    }
    else
    {
      $ns_txt = "s";
    }

    $validation_error = $my_form->validation();

    if ((!$validation_error) || ($my_error != ""))
    {
      $error = $my_form->create_msg();
      echo "<TABLE align='center' WIDTH='98%' BORDER='0' CELLSPACING='15' CELLPADDING='0'><tr><td><center><b>";
      echo "<font color='#ff0000' size=+2>Error! - The following error(s) occurred:</font><br />";

      if ($error)
      {
        echo $error . $my_error;
      }
      else
      {
        echo $error . "<br />" . $my_error;
      }

      echo "</font>";
      echo "<font color='#c020c0'";
      echo "<br />PLEASE RE-ENTER YOUR TIME ZONE DATA. THANK YOU.<br /><br />";
      echo "</font>";
      echo "</b></center></td></tr></table>";
    }
    else
    {
      // no errors in filling out form, so process form
      // calculate astronomic data
      $swephsrc = './sweph';    //sweph MUST be in a folder no less than at this level
      $sweph = './sweph';

      // Unset any variables not initialized elsewhere in the program
      unset($PATH,$out,$pl_name,$longitude1,$house_pos, $ruling_pl, $pl_hour);

      //assign data from database to local variables
      $inmonth = $month;
      $inday = $day;
      $inyear = $year;

      $inhours = $hour;
      $inmins = $minute;
      $insecs = "0";

      $intz = $timezone;

      $my_longitude = $ew * ($long_deg + ($long_min / 60));
      $my_latitude = $ns * ($lat_deg + ($lat_min / 60));

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

      putenv("PATH=$PATH:$swephsrc");

      // get LAST_PLANET planets and all house cusps
      if (strlen($h_sys) != 1)
      {
        $h_sys = "p";
      }

      exec ("swetest -edir$sweph -b$utdatenow -ut$utnow -p0123456789DAttt -eswe -house$my_longitude,$my_latitude,$h_sys -flsj -g, -head", $out);

      // Each line of output data from swetest is exploded into array $row, giving these elements:
      // 0 = longitude
      // 1 = speed
      // 2 = house position
      // planets are index 0 - index (LAST_PLANET), house cusps are index (LAST_PLANET + 1) - (LAST_PLANET + 12)
      foreach ($out as $key => $line)
      {
        $row = explode(',',$line);
        $longitude1[$key] = $row[0];
        $speed1[$key] = $row[1];
        $house_pos1[$key] = $row[2];
      };

      include("constants_eng.php");     // this is here because we must rename the planet names

      //calculate the Part of Fortune
      //is this a day chart or a night chart?
      if ($longitude1[LAST_PLANET + 1] > $longitude1[LAST_PLANET + 7])
      {
        if ($longitude1[0] <= $longitude1[LAST_PLANET + 1] And $longitude1[0] > $longitude1[LAST_PLANET + 7])
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
        if ($longitude1[0] > $longitude1[LAST_PLANET + 1] And $longitude1[0] <= $longitude1[LAST_PLANET + 7])
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
        $longitude1[SE_POF] = $longitude1[LAST_PLANET + 1] + $longitude1[1] - $longitude1[0];
      }
      else
      {
        $longitude1[SE_POF] = $longitude1[LAST_PLANET + 1] - $longitude1[1] + $longitude1[0];
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
      $longitude1[LAST_PLANET] = $longitude1[LAST_PLANET + 16];   //Asc = +13, MC = +14, RAMC = +15, Vertex = +16


//get house positions of planets here
      for ($x = 1; $x <= 12; $x++)
      {
        for ($y = 0; $y <= LAST_PLANET; $y++)
        {
          $pl = $longitude1[$y] + (1 / 36000);
          if ($x < 12 And $longitude1[$x + LAST_PLANET] > $longitude1[$x + LAST_PLANET + 1])
          {
            If (($pl >= $longitude1[$x + LAST_PLANET] And $pl < 360) Or ($pl < $longitude1[$x + LAST_PLANET + 1] And $pl >= 0))
            {
              $house_pos1[$y] = $x;
              continue;
            }
          }

          if ($x == 12 And ($longitude1[$x + LAST_PLANET] > $longitude1[LAST_PLANET + 1]))
          {
            if (($pl >= $longitude1[$x + LAST_PLANET] And $pl < 360) Or ($pl < $longitude1[LAST_PLANET + 1] And $pl >= 0))
            {
              $house_pos1[$y] = $x;
            }
            continue;
          }

          if (($pl >= $longitude1[$x + LAST_PLANET]) And ($pl < $longitude1[$x + LAST_PLANET + 1]) And ($x < 12))
          {
            $house_pos1[$y] = $x;
            continue;
          }

          if (($pl >= $longitude1[$x + LAST_PLANET]) And ($pl < $longitude1[LAST_PLANET + 1]) And ($x == 12))
          {
            $house_pos1[$y] = $x;
          }
        }
      }

//display transit data
      $secs = "0";
      if ($timezone < 0)
      {
        $tz = $timezone;
      }
      else
      {
        $tz = "+" . $timezone;
      }

      $restored_name = "Transits";

      echo "<center>";
//      echo "<FONT color='#ff0000' SIZE='5' FACE='Arial'><b>Name = $restored_name </b></font><br /><br />";
//      echo '<font size="2"><b>Born ' . strftime("%A, %B %d, %Y<br />%X (time zone = GMT $tz hours)</b></font><br />\n", mktime($hour, $minute, $secs, $month, $day, $year));
//      echo "<font size = '-1'><b>" . $long_deg . $ew_txt . $long_min . ", " . $lat_deg . $ns_txt . $lat_min . "</b></font><br /><br />";

      $line1 = $restored_name . " on " . strftime("%A, %B %d, %Y at %H:%M (time zone = GMT $tz hours)", mktime($hour, $minute, $secs, $month, $day, $year));
      $line1 = $line1 . " at " . $long_deg . $ew_txt . sprintf("%02d", $long_min) . " and " . $lat_deg . $ns_txt . sprintf("%02d", $lat_min);
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

        <input type="hidden" name="name" value="<?php echo $_POST['name']; ?>">
        <input type="hidden" name="month" value="<?php echo $_POST['month']; ?>">
        <input type="hidden" name="day" value="<?php echo $_POST['day']; ?>">
        <input type="hidden" name="year" value="<?php echo $_POST['year']; ?>">
        <input type="hidden" name="hour" value="<?php echo $_POST['hour']; ?>">
        <input type="hidden" name="minute" value="<?php echo $_POST['minute']; ?>">
        <input type="hidden" name="timezone" value="<?php echo $_POST['timezone']; ?>">
        <input type="hidden" name="long_deg" value="<?php echo $_POST['long_deg']; ?>">
        <input type="hidden" name="long_min" value="<?php echo $_POST['long_min']; ?>">
        <input type="hidden" name="ew" value="<?php echo $_POST['ew']; ?>">
        <input type="hidden" name="lat_deg" value="<?php echo $_POST['lat_deg']; ?>">
        <input type="hidden" name="lat_min" value="<?php echo $_POST['lat_min']; ?>">
        <input type="hidden" name="ns" value="<?php echo $_POST['ns']; ?>">

        <input type="hidden" name="h_sys_submitted" value="TRUE">
        <INPUT type="submit" name="submit" value="Go" align="middle" style="background-color:#66ff66;color:#000000;font-size:16px;font-weight:bold">
      </form>
<?php
      echo "</center>";

      $hr_ob = $hour;
      $min_ob = $minute;

      $ubt1 = 0;
      if (($hr_ob == 12) And ($min_ob == 0))
      {
        $ubt1 = 1;        // this person has an unknown birth time
      }

      $ubt2 = $ubt1;

      $rx1 = "";
      for ($i = 0; $i <= SE_TNODE; $i++)
      {
        if ($speed1[$i] < 0)
        {
          $rx1 .= "R";
        }
        else
        {
          $rx1 .= " ";
        }
      }

      $rx2 = $rx1;

      for ($i = 1; $i <= LAST_PLANET; $i++)
      {
        $hc1[$i] = $longitude1[LAST_PLANET + $i];
      }

// no need to urlencode unless perhaps magic quotes is ON (??)
      $_SESSION['tad_longitude1'] = $longitude1;
      $_SESSION['tad_hc1'] = $hc1;
      $_SESSION['tad_house_pos1'] = $house_pos1;
      
      $wheel_width = 640;
      $wheel_height = $wheel_width + 50;    //includes space at top of wheel for header

      echo "<center>";
      echo "<img border='0' src='natal_wheel_TAD.php?rx1=$rx1&l1=$line1' width='$wheel_width' height='$wheel_height'>";
      echo "<br /><br />";
      echo "<img border='0' src='natal_aspect_grid_TAD.php?rx1=$rx1&rx2=$rx2&ubt1=$ubt1&ubt2=$ubt2' width='705' height='450'>";
      echo "</center>";
      echo "<br /><br />";


//Moon aspectarian and void of course Moon
      include ('moon_aspects_any_day.php');


//Planetary hours
      echo "<center><font size='5'><strong><font face='Arial'>Planetary Hours</font></strong></font></center><br />";

      $starting_JD = gregoriantojd($month, $day, $year) + ($ew * 0.25);
      $result = exec ("swetest -edir$sweph -bj$starting_JD -eswe -geopos$my_longitude,$my_latitude,400 -rise");

      $first_colon = strpos($result, ":") + 1;
      $rise_hour = mid($result, $first_colon - 2, 2) + $timezone;
      if ($rise_hour >= 24)
      {
        $rise_hour = $rise_hour - 24;
      }
      $rise_minute = mid($result, $first_colon + 1, 2);

      $second_colon = strpos($result, ":", $first_colon + 1) + 1;
      $rise_seconds = mid($result, $second_colon + 1, 2);

      $third_colon = strpos($result, ":", $second_colon + 1) + 1;
      $set_hour = mid($result, $third_colon - 2, 2) + $timezone;
      if ($set_hour < 0)
      {
        $set_hour = $set_hour + 24;
      }
      $set_minute = mid($result, $third_colon + 1, 2);

      $fourth_colon = strpos($result, ":", $third_colon + 1) + 1;
      $set_seconds = mid($result, $fourth_colon + 1, 2);

      $day_of_week = jddayofweek($starting_JD + 0.5);   //0 = Sunday, 6 = Saturday

      $sunset_time = $set_hour * 3600 + $set_minute * 60 + $set_seconds;      //in numbers of seconds
      $sunrise_time = $rise_hour * 3600 + $rise_minute * 60 + $rise_seconds;    //in numbers of seconds

      $length_of_day = $sunset_time - $sunrise_time;
      $length_of_night = 86400 - $length_of_day;

      $day_interval = $length_of_day / 12;              //in seconds
      $night_interval = $length_of_night / 12;            //in seconds

      $time_now = $sunrise_time - $day_interval;
      $ruler_now = $day_of_week +2;
      for ($i = 0; $i < 12; $i++)
      {
        $time_now = $time_now + $day_interval;        //time of next planetary hour
        $ruler_now = $ruler_now - 2;
        if ($ruler_now < 0)
        {
          $ruler_now = $ruler_now + 7;
        }
        $ruling_pl[$i] = $pl_name[$ruler_now];
        $pl_hour[$i] = $time_now;
      }

      $time_now = $sunset_time - $night_interval;
      for ($i = 0; $i < 12; $i++)
      {
        $time_now = $time_now + $night_interval;      //time of next planetary hour
        $ruler_now = $ruler_now - 2;
        if ($ruler_now < 0)
        {
          $ruler_now = $ruler_now + 7;
        }
        $ruling_pl[$i + 12] = $pl_name[$ruler_now];
        $pl_hour[$i + 12] = $time_now;
      }

      echo '<center><table width="30%" cellpadding="0" cellspacing="0" border="0">',"\n";

      echo '<tr>';
      echo "<td><font color='#0000ff'><b> Planet </b></font></td>";
      echo "<td><font color='#0000ff'><b> Time </b></font></td>";
      echo "<td><font color='#0000ff'><b> Planet </b></font></td>";
      echo "<td><font color='#0000ff'><b> Time </b></font></td>";
      echo '</tr>';

      for ($i = 0; $i <= 11; $i++)
      {
        echo '<tr>';
        echo "<td>" . $ruling_pl[$i] . "</td>";
        echo "<td>" . strftime("%X", mktime(0, 0, $pl_hour[$i], $month, $day, $year)) . "</td>";
        echo "<td>" . $ruling_pl[$i + 12] . "</td>";
        echo "<td>" . strftime("%X", mktime(0, 0, $pl_hour[$i + 12], $month, $day, $year)) . "</td>";
        echo '</tr>';
      }

      echo '</table></center>',"\n";
      echo "<br /><br />";


//display transit data
      echo "<center><font size='5'><strong><font face='Arial'>Planet Positions</font></strong></font><br /></center>";

      echo '<center><table width="40%" cellpadding="0" cellspacing="0" border="0">';

      echo '<tr>';
      echo "<td><font color='#0000ff'><b> Planet </b></font></td>";
      echo "<td><font color='#0000ff'><b> Longitude </b></font></td>";
      if ($ubt1 == 1)
      {
        echo "<td>&nbsp;</td>";
      }
      else
      {
        echo "<td><font color='#0000ff'><b> House<br />position </b></font></td>";
      }
      echo '</tr>';

      if ($ubt1 == 1)
      {
        $a1 = SE_TNODE;
      }
      else
      {
        $a1 = LAST_PLANET;
      }

      for ($i = 0; $i <= $a1; $i++)
      {
        echo '<tr>';
        echo "<td>" . $pl_name[$i] . "</td>";
        echo "<td><font face='Courier New'>" . Convert_Longitude($longitude1[$i]) . " " . Mid($rx1, $i + 1, 1) . "</font></td>";
        if ($ubt1 == 1)
        {
          echo "<td>&nbsp;</td>";
        }
        else
        {
          $hse = floor($house_pos1[$i]);
          if ($hse < 10)
          {
            echo "<td>&nbsp;&nbsp;&nbsp;&nbsp; " . $hse . "</td>";
          }
          else
          {
            echo "<td>&nbsp;&nbsp;&nbsp;" . $hse . "</td>";
          }
        }
        echo '</tr>';
      }

      echo '<tr>';
      echo "<td> &nbsp </td>";
      echo "<td> &nbsp </td>";
      echo "<td> &nbsp </td>";
      echo "<td> &nbsp </td>";
      echo '</tr>';

      if ($ubt1 == 0)
      {
        echo '<tr>';
        echo "<td><font color='#0000ff'><b> House </b></font></td>";
        echo "<td><font color='#0000ff'><b> Longitude </b></font></td>";
        echo "<td> &nbsp </td>";
        echo '</tr>';

        for ($i = LAST_PLANET + 1; $i <= LAST_PLANET + 12; $i++)
        {
          echo '<tr>';
          if ($i == LAST_PLANET + 1)
          {
            echo "<td>Ascendant </td>";
          }
          elseif ($i == LAST_PLANET + 10)
          {
            echo "<td>MC (Midheaven) </td>";
          }
          else
          {
            echo "<td>House " . ($i - LAST_PLANET) . "</td>";
          }
          echo "<td><font face='Courier New'>" . Convert_Longitude($longitude1[$i]) . "</font></td>";
          echo "<td> &nbsp </td>";
          echo '</tr>';
        }
      }

      echo '</table></center>';
      echo "<br /><br />";


      // display transit data - aspect table
      echo "<center><font size='5'><strong><font face='Arial'>Planetary Aspects</font></strong></font><br /></center><br />";

      echo '<center><table width="40%" cellpadding="0" cellspacing="0" border="0">';

      echo '<tr>';
      echo "<td><font color='#0000ff'><b> Planet </b></font></td>";
      echo "<td><font color='#0000ff'><b> Aspect </b></font></td>";
      echo "<td><font color='#0000ff'><b> Planet </b></font></td>";
      echo "<td><font color='#0000ff'><b> Orb </b></font></td>";
      echo '</tr>';

      // include Ascendant and MC
      $longitude1[LAST_PLANET + 1] = $hc1[1];
      $longitude1[LAST_PLANET + 2] = $hc1[10];

      $pl_name[LAST_PLANET + 1] = "Ascendant";
      $pl_name[LAST_PLANET + 2] = "Midheaven";

      if ($ubt1 == 1)
      {
        $a1 = SE_TNODE;
      }
      else
      {
        $a1 = LAST_PLANET + 2;
      }

      for ($i = 0; $i <= $a1; $i++)
      {
        echo "<tr><td colspan='4'>&nbsp;</td></tr>";
        for ($j = 0; $j <= $a1; $j++)
        {
          $q = 0;
          $da = Abs($longitude1[$i] - $longitude1[$j]);

          if ($da > 180)
          {
            $da = 360 - $da;
          }

          // set orb - 8 if Sun or Moon, 6 if not Sun or Moon
          if ($i == SE_POF Or $j == SE_POF)
          {
            $orb = 2;
          }
          elseif ($i == SE_LILITH Or $j == SE_LILITH)
          {
            $orb = 2;
          }
          elseif ($i == SE_TNODE Or $j == SE_TNODE)
          {
            $orb = 2;
          }
          elseif ($i == SE_VERTEX Or $j == SE_VERTEX)
          {
            $orb = 2;
          }
          elseif ($i == SE_SUN Or $i == SE_MOON Or $j == SE_SUN Or $j == SE_MOON)
          {
            $orb = 3;
          }
          else
          {
            $orb = 3;
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

          if ($q > 0 And $i != $j)
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


      //display transit midpoints
      //get header first
      echo "<center><font size='+1' color='#0000ff'><b>TRANSIT MIDPOINTS</b></font></center><br />";

      //calculate various midpoints
      for ($i = 0; $i <= LAST_PLANET + 1; $i++)
      {
        for ($j = $i + 1; $j <= LAST_PLANET + 2; $j++)
        {
          $mp[$i][$j] = ($longitude1[$i] + $longitude1[$j]) / 2;

          //this finds the nearer midpoint, which may not be what is optimum
          $diff1 = $mp[$i][$j] - $longitude1[$i];
          $diff2 = $mp[$i][$j] - $longitude1[$j];

          if (abs($diff1) > 90 Or abs($diff2) > 90)
          {
            $mp[$i][$j] = $mp[$i][$j] + 180;
          }

          if ($mp[$i][$j] >= 360)
          {
            $mp[$i][$j] = $mp[$i][$j] - 360;
          }
        }
      }


      //list all midpoints
      //calculate various transit midpoints
      echo '<center><table width="80%" cellpadding="0" cellspacing="0" border="0">';

      $cols = 3;
      for ($i = 0; $i <= LAST_PLANET + 1; $i++)
      {
        for ($j = $i + 1; $j <= LAST_PLANET + 2; $j = $j + $cols)
        {
          echo "<tr>";

          if ($j <= LAST_PLANET + 2)
          {
            echo "<td>" . $pl_name[$i] . "/" . $pl_name[$j] . " = " . Convert_Longitude_no_secs($mp[$i][$j]) . "</td>";
          }
          else
          {
            echo "<td>&nbsp;</td>";
          }

          if ($j + 1 <= LAST_PLANET + 2)
          {
            echo "<td>" . $pl_name[$i] . "/" . $pl_name[$j + 1] . " = " . Convert_Longitude_no_secs($mp[$i][$j + 1]) . "</td>";
          }
          else
          {
            echo "<td>&nbsp;</td>";
          }

          if ($j + 2 <= LAST_PLANET + 2)
          {
            echo "<td>" . $pl_name[$i] . "/" . $pl_name[$j + 2] . " = " . Convert_Longitude_no_secs($mp[$i][$j + 2]) . "</td>";
          }
          else
          {
            echo "<td>&nbsp;</td>";
          }

          echo "</tr>";
        }

        echo "<tr><td colspan='$cols'>&nbsp;</td></tr>";
      }

      echo '</table></center><br /><br />';


      // update count
      $sql = "SELECT transits_any_date FROM reports";
      $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);
      $row = mysqli_fetch_array($result);
      $count = $row[transits_any_date] + 1;

      $sql = "UPDATE reports SET transits_any_date = '$count'";
      $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);


      include ('footer.html');
      exit();
    }
  }
  else
  {
    include ('header_transit_any_day.html');        //here because of cookies

    $name = "Transits";     //stripslashes($_COOKIE['name']);

    $month = $_COOKIE['month'];
    $day = $_COOKIE['day'];
    $year = $_COOKIE['year'];

    $hour = $_COOKIE['hour'];
    $minute = $_COOKIE['minute'];

    $timezone = $_COOKIE['timezone'];

    $long_deg = $_COOKIE["long_deg"];
    $long_min = $_COOKIE["long_min"];
    $ew = $_COOKIE["ew"];

    $lat_deg = $_COOKIE["lat_deg"];
    $lat_min = $_COOKIE["lat_min"];
    $ns = $_COOKIE["ns"];
  }

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="margin: 0px 20px;">
  <fieldset><legend><font size=5><b>Data entry for Transit Chart</b></font></legend>

  &nbsp;&nbsp;<font color="#ff0000"><b>All fields are required.</b></font><br />

  <table style="font-size:12px;">
    <TR>
      <TD>
        <P align="right">Name:</P>
      </TD>

      <TD>
        <INPUT size="40" name="name" value="<?php echo $name; ?>">
      </TD>
    </TR>

    <TR>
      <TD>
        <P align="right">Date:</P>
      </TD>

      <TD>
        <?php
        echo '<select name="month">';
        foreach ($months as $key => $value)
        {
          echo "<option value=\"$key\"";
          if ($key == $month)
          {
            echo ' selected="selected"';
          }
          echo ">$value</option>\n";
        }
        echo '</select>';
        ?>

        <INPUT size="2" maxlength="2" name="day" value="<?php echo $day; ?>">
        <b>,</b>&nbsp;
        <INPUT size="4" maxlength="4" name="year" value="<?php echo $year; ?>">
         <font color="#0000ff">
        (only years from 1900 through 2099 are valid)
        </font>
     </TD>
    </TR>

    <TR>
      <td valign="top"><P align="right">Time:</P></td>
      <TD>
        <INPUT maxlength="2" size="2" name="hour" value="<?php echo $hour; ?>">
        <b>:</b>
        <INPUT maxlength="2" size="2" name="minute" value="<?php echo $minute; ?>">

        <font color="#0000ff">
         (please give time in 24 hour format.)
        <br /><br />
        </font>
      </TD>
    </TR>

    <TR>
      <td valign="top">
        <P align="right"><font color="#ff0000">
        <b>IMPORTANT</b>
        </font></P>
      </td>

      <td>
        <font color="#ff0000">
        <b>NOTICE:</b>
        </font>
        <b>&nbsp;&nbsp;West longitudes are MINUS time zones.&nbsp;&nbsp;East longitudes are PLUS time zones.</b>
      </td>
    </TR>

    <TR>
      <td valign="top"><P align="right">Time zone:</P></td>

      <TD>
        <select name="timezone" size="1">
          <?php
          echo "<option value='' ";
          if ($timezone == ""){ echo " selected"; }
          echo "> Select Time Zone </option>";

          echo "<option value='-12' ";
          if ($timezone == "-12"){ echo " selected"; }
          echo ">GMT -12:00 hrs - IDLW</option>";

          echo "<option value='-11' ";
          if ($timezone == "-11"){ echo " selected"; }
          echo ">GMT -11:00 hrs - BET or NT</option>";

          echo "<option value='-10.5' ";
          if ($timezone == "-10.5"){ echo " selected"; }
          echo ">GMT -10:30 hrs - HST</option>";

          echo "<option value='-10' ";
          if ($timezone == "-10"){ echo " selected"; }
          echo ">GMT -10:00 hrs - AHST</option>";

          echo "<option value='-9.5' ";
          if ($timezone == "-9.5"){ echo " selected"; }
          echo ">GMT -09:30 hrs - HDT or HWT</option>";

          echo "<option value='-9' ";
          if ($timezone == "-9"){ echo " selected"; }
          echo ">GMT -09:00 hrs - YST or AHDT or AHWT</option>";

          echo "<option value='-8' ";
          if ($timezone == "-8"){ echo " selected"; }
          echo ">GMT -08:00 hrs - PST or YDT or YWT</option>";

          echo "<option value='-7' ";
          if ($timezone == "-7"){ echo " selected"; }
          echo ">GMT -07:00 hrs - MST or PDT or PWT</option>";

          echo "<option value='-6' ";
          if ($timezone == "-6"){ echo " selected"; }
          echo ">GMT -06:00 hrs - CST or MDT or MWT</option>";

          echo "<option value='-5' ";
          if ($timezone == "-5"){ echo " selected"; }
          echo ">GMT -05:00 hrs - EST or CDT or CWT</option>";

          echo "<option value='-4' ";
          if ($timezone == "-4"){ echo " selected"; }
          echo ">GMT -04:00 hrs - AST or EDT or EWT</option>";

          echo "<option value='-3.5' ";
          if ($timezone == "-3.5"){ echo " selected"; }
          echo ">GMT -03:30 hrs - NST</option>";

          echo "<option value='-3' ";
          if ($timezone == "-3"){ echo " selected"; }
          echo ">GMT -03:00 hrs - BZT2 or AWT</option>";

          echo "<option value='-2' ";
          if ($timezone == "-2"){ echo " selected"; }
          echo ">GMT -02:00 hrs - AT</option>";

          echo "<option value='-1' ";
          if ($timezone == "-1"){ echo " selected"; }
          echo ">GMT -01:00 hrs - WAT</option>";

          echo "<option value='0' ";
          if ($timezone == "0"){ echo " selected"; }
          echo ">Greenwich Mean Time - GMT or UT</option>";

          echo "<option value='1' ";
          if ($timezone == "1"){ echo " selected"; }
          echo ">GMT +01:00 hrs - CET or MET or BST</option>";

          echo "<option value='2' ";
          if ($timezone == "2"){ echo " selected"; }
          echo ">GMT +02:00 hrs - EET or CED or MED or BDST or BWT</option>";

          echo "<option value='3' ";
          if ($timezone == "3"){ echo " selected"; }
          echo ">GMT +03:00 hrs - BAT or EED</option>";

          echo "<option value='3.5' ";
          if ($timezone == "3.5"){ echo " selected"; }
          echo ">GMT +03:30 hrs - IT</option>";

          echo "<option value='4' ";
          if ($timezone == "4"){ echo " selected"; }
          echo ">GMT +04:00 hrs - USZ3</option>";

          echo "<option value='5' ";
          if ($timezone == "5"){ echo " selected"; }
          echo ">GMT +05:00 hrs - USZ4</option>";

          echo "<option value='5.5' ";
          if ($timezone == "5.5"){ echo " selected"; }
          echo ">GMT +05:30 hrs - IST</option>";

          echo "<option value='6' ";
          if ($timezone == "6"){ echo " selected"; }
          echo ">GMT +06:00 hrs - USZ5</option>";

          echo "<option value='6.5' ";
          if ($timezone == "6.5"){ echo " selected"; }
          echo ">GMT +06:30 hrs - NST</option>";

          echo "<option value='7' ";
          if ($timezone == "7"){ echo " selected"; }
          echo ">GMT +07:00 hrs - SST or USZ6</option>";

          echo "<option value='7.5' ";
          if ($timezone == "7.5"){ echo " selected"; }
          echo ">GMT +07:30 hrs - JT</option>";

          echo "<option value='8' ";
          if ($timezone == "8"){ echo " selected"; }
          echo ">GMT +08:00 hrs - AWST or CCT</option>";

          echo "<option value='8.5' ";
          if ($timezone == "8.5"){ echo " selected"; }
          echo ">GMT +08:30 hrs - MT</option>";

          echo "<option value='9' ";
          if ($timezone == "9"){ echo " selected"; }
          echo ">GMT +09:00 hrs - JST or AWDT</option>";

          echo "<option value='9.5' ";
          if ($timezone == "9.5"){ echo " selected"; }
          echo ">GMT +09:30 hrs - ACST or SAT or SAST</option>";

          echo "<option value='10' ";
          if ($timezone == "10"){ echo " selected"; }
          echo ">GMT +10:00 hrs - AEST or GST</option>";

          echo "<option value='10.5' ";
          if ($timezone == "10.5"){ echo " selected"; }
          echo ">GMT +10:30 hrs - ACDT or SDT or SAD</option>";

          echo "<option value='11' ";
          if ($timezone == "11"){ echo " selected"; }
          echo ">GMT +11:00 hrs - UZ10 or AEDT</option>";

          echo "<option value='11.5' ";
          if ($timezone == "11.5"){ echo " selected"; }
          echo ">GMT +11:30 hrs - NZ</option>";

          echo "<option value='12' ";
          if ($timezone == "12"){ echo " selected"; }
          echo ">GMT +12:00 hrs - NZT or IDLE</option>";

          echo "<option value='12.5' ";
          if ($timezone == "12.5"){ echo " selected"; }
          echo ">GMT +12:30 hrs - NZS</option>";

          echo "<option value='13' ";
          if ($timezone == "13"){ echo " selected"; }
          echo ">GMT +13:00 hrs - NZST</option>";
          ?>
        </select>

        <br />

        <font color="#0000ff">
        (example: Chicago is "GMT -06:00 hrs" (standard time), Paris is "GMT +01:00 hrs" (standard time).<br />
        Add 1 hour if Daylight Saving is in effect (select next time zone down in the list).
        <br /><br />
        </font>
      </TD>
    </TR>

    <TR>
      <td valign="top"><P align="right">Longitude:</P></td>
      <TD>
        <INPUT maxlength="3" size="3" name="long_deg" value="<?php echo $long_deg; ?>">
        <select name="ew">
          <?php
          if ($ew == "-1")
          {
            echo "<option value=''>Select </option>";
            echo "<option value='-1' selected>W </option>";
            echo "<option value='1'>E </option>";
          }
          elseif ($ew == "1")
          {
            echo "<option value=''>Select </option>";
            echo "<option value='-1'>W </option>";
            echo "<option value='1' selected>E </option>";
          }
          else
          {
            echo "<option value='' selected>Select</option>";
            echo "<option value='-1'>W </option>";
            echo "<option value='1'>E </option>";
          }
          ?>
        </select>

        <INPUT maxlength="2" size="2" name="long_min" value="<?php echo $long_min; ?>">
        <font color="#0000ff">
        (example: Chicago is 87 W 39, Sydney is 151 E 13)
        </font>
      </TD>
    </TR>

    <TR>
      <td valign="top"><P align="right">Latitude:</P></td>

      <TD>
        <INPUT maxlength="2" size="3" name="lat_deg" value="<?php echo $lat_deg; ?>">
        <select name="ns">
          <?php
          if ($ns == "1")
          {
            echo "<option value=''>Select </option>";
            echo "<option value='1' selected>N&nbsp;&nbsp;</option>";
            echo "<option value='-1'>S&nbsp;&nbsp;</option>";
          }
          elseif ($ns == "-1")
          {
            echo "<option value=''>Select </option>";
            echo "<option value='1'>N&nbsp;&nbsp;</option>";
            echo "<option value='-1' selected>S&nbsp;&nbsp;</option>";
          }
          else
          {
            echo "<option value='' selected>Select</option>";
            echo "<option value='1'>N&nbsp;&nbsp;</option>";
            echo "<option value='-1'>S&nbsp;&nbsp;</option>";
          }
          ?>
        </select>

        <INPUT maxlength="2" size="2" name="lat_min" value="<?php echo $lat_min; ?>">
        <font color="#0000ff">
        (example: Chicago is 41 N 51, Sydney is 33 S 52)
        </font>
        <br /><br />
      </TD>
    </TR>
  </table>

  <br />
  <center>
  <font color="#ff0000"><b>Most people mess up the time zone selection. Please make sure your selection is correct.</b></font><br /><br />
  <input type="hidden" name="submitted" value="TRUE">
  <INPUT type="submit" name="submit" value="Submit data (AFTER DOUBLE-CHECKING IT FOR ERRORS)" align="middle" style="background-color:#66ff66;color:#000000;font-size:16px;font-weight:bold">
  </center>

  <br />
  </fieldset>
</form>

<?php
include ('footer.html');


Function left($leftstring, $leftlength)
{
  return(substr($leftstring, 0, $leftlength));
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


Function safeEscapeString($conn, $string)
{
// replace HTML tags '<>' with '[]'
  $temp1 = str_replace("<", "[", $string);
  $temp2 = str_replace(">", "]", $temp1);

// but keep <br> or <br />
// turn <br> into <br /> so later it will be turned into ""
// using just <br> will add extra blank lines
  $temp1 = str_replace("[br]", "<br />", $temp2);
  $temp2 = str_replace("[br /]", "<br />", $temp1);

  if (get_magic_quotes_gpc())
  {
    return $temp2;
  }
  else
  {
    return mysqli_real_escape_string($conn, $temp2);
  }
}


Function Find_Specific_Report_Paragraph($phrase_to_look_for, $file)
{
  $string = "";
  $len = strlen($phrase_to_look_for);

  //put entire file contents into an array, line by line
  $file_array = file($file);

  // look through each line searching for $phrase_to_look_for
  for($i = 0; $i < count($file_array); $i++)
  {
    if (left(trim($file_array[$i]), $len) == $phrase_to_look_for)
    {
      $flag = 0;
      while (trim($file_array[$i]) != "*")
      {
        if ($flag == 0)
        {
          $string .= "<b>" . $file_array[$i] . "</b>";
        }
        else
        {
          $string .= $file_array[$i];
        }
        $flag = 1;
        $i++;
      }
      break;
    }
  }

  return $string;
}


Function Convert_Longitude_no_secs($longitude)
{
  $signs = array (0 => 'Ari', 'Tau', 'Gem', 'Can', 'Leo', 'Vir', 'Lib', 'Sco', 'Sag', 'Cap', 'Aqu', 'Pis');

  $sign_num = floor($longitude / 30);
  $pos_in_sign = $longitude - ($sign_num * 30);
  $deg = floor($pos_in_sign);
  $full_min = ($pos_in_sign - $deg) * 60;

  if ($deg < 10)
  {
    $deg = "0" . $deg;
  }

  $fmin = sprintf("%.0f", $full_min);
  if ($fmin < 10)
  {
    $fmin = "0" . $fmin;
  }

  return $deg . " " . $signs[$sign_num] . " " . $fmin;
}

?>
