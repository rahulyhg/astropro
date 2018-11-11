<?php
  $months = array (0 => 'Choose month', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
  $my_error = "";

  $no_interps = False;          //set this to False when you want interpretations
  $no_house_interps = False;     //set this to False when you want transit planet in natal house interpretations

  // check if the form has been submitted
  if (isset($_POST['submitted']))
  {
    // get all variables from form - Person #1
    $name1 = safeEscapeString($_POST["name1"]);

    $month1 = safeEscapeString($_POST["month1"]);
    $day1 = safeEscapeString($_POST["day1"]);
    $year1 = safeEscapeString($_POST["year1"]);

    $hour1 = safeEscapeString($_POST["hour1"]);
    $minute1 = safeEscapeString($_POST["minute1"]);

    $timezone1 = safeEscapeString($_POST["timezone1"]);

    $long_deg1 = safeEscapeString($_POST["long_deg1"]);
    $long_min1 = safeEscapeString($_POST["long_min1"]);
    $ew1 = safeEscapeString($_POST["ew1"]);

    $lat_deg1 = safeEscapeString($_POST["lat_deg1"]);
    $lat_min1 = safeEscapeString($_POST["lat_min1"]);
    $ns1 = safeEscapeString($_POST["ns1"]);

    $start_month = safeEscapeString($_POST["start_month"]);
    $start_day = safeEscapeString($_POST["start_day"]);
    $start_year = safeEscapeString($_POST["start_year"]);

    // set cookie containing natal data here
    setcookie ('name', stripslashes($name1), time() + 60 * 60 * 24 * 30, '/', '', 0);

    setcookie ('month', $month1, time() + 60 * 60 * 24 * 30, '/', '', 0);
    setcookie ('day', $day1, time() + 60 * 60 * 24 * 30, '/', '', 0);
    setcookie ('year', $year1, time() + 60 * 60 * 24 * 30, '/', '', 0);

    setcookie ('hour', $hour1, time() + 60 * 60 * 24 * 30, '/', '', 0);
    setcookie ('minute', $minute1, time() + 60 * 60 * 24 * 30, '/', '', 0);

    setcookie ('timezone', $timezone1, time() + 60 * 60 * 24 * 30, '/', '', 0);

    setcookie ('long_deg', $long_deg1, time() + 60 * 60 * 24 * 30, '/', '', 0);
    setcookie ('long_min', $long_min1, time() + 60 * 60 * 24 * 30, '/', '', 0);
    setcookie ('ew', $ew1, time() + 60 * 60 * 24 * 30, '/', '', 0);

    setcookie ('lat_deg', $lat_deg1, time() + 60 * 60 * 24 * 30, '/', '', 0);
    setcookie ('lat_min', $lat_min1, time() + 60 * 60 * 24 * 30, '/', '', 0);
    setcookie ('ns', $ns1, time() + 60 * 60 * 24 * 30, '/', '', 0);

    include ('header.php');       //here because of setting cookies above

    include("validation_class.php");

    //error check
    $my_form = new Validate_fields;

    $my_form->check_4html = true;

    $my_form->add_text_field("Name #1", $name1, "text", "y", 40);

    $my_form->add_text_field("Month #1", $month1, "text", "y", 2);
    $my_form->add_text_field("Day #1", $day1, "text", "y", 2);
    $my_form->add_text_field("Year #1", $year1, "text", "y", 4);

    $my_form->add_text_field("Hour #1", $hour1, "text", "y", 2);
    $my_form->add_text_field("Minute #1", $minute1, "text", "y", 2);

    $my_form->add_text_field("Time zone #1", $timezone1, "text", "y", 4);

    $my_form->add_text_field("Longitude degree #1", $long_deg1, "text", "y", 3);
    $my_form->add_text_field("Longitude minute #1", $long_min1, "text", "y", 2);
    $my_form->add_text_field("Longitude E/W #1", $ew1, "text", "y", 2);

    $my_form->add_text_field("Latitude degree #1", $lat_deg1, "text", "y", 2);
    $my_form->add_text_field("Latitude minute #1", $lat_min1, "text", "y", 2);
    $my_form->add_text_field("Latitude N/S #1", $ns1, "text", "y", 2);

    $my_form->add_text_field("Start Month", $start_month, "text", "y", 2);
    $my_form->add_text_field("Start Day", $start_day, "text", "y", 2);
    $my_form->add_text_field("Start Year", $start_year, "text", "y", 4);

    // additional error checks on user-entered data
    if ($month1 != "" And $day1 != "" And $year1 != "")
    {
      if (!$date = checkdate($month1, $day1, $year1))
      {
        $my_error .= "The date of birth you entered is not valid.<br>";
      }
    }

    if (($year1 < 1800) Or ($year1 >= 2400))
    {
      $my_error .= "Birth year person #1 - please enter a year between 1800 and 2399.<br>";
    }

    if (($hour1 < 0) Or ($hour1 > 23))
    {
      $my_error .= "Birth hour must be between 0 and 23.<br>";
    }

    if (($minute1 < 0) Or ($minute1 > 59))
    {
      $my_error .= "Birth minute must be between 0 and 59.<br>";
    }

    if (($long_deg1 < 0) Or ($long_deg1 > 179))
    {
      $my_error .= "Longitude degrees must be between 0 and 179.<br>";
    }

    if (($long_min1 < 0) Or ($long_min1 > 59))
    {
      $my_error .= "Longitude minutes must be between 0 and 59.<br>";
    }

    if (($lat_deg1 < 0) Or ($lat_deg1 > 65))
    {
      $my_error .= "Latitude degrees must be between 0 and 65.<br>";
    }

    if (($lat_min1 < 0) Or ($lat_min1 > 59))
    {
      $my_error .= "Latitude minutes must be between 0 and 59.<br>";
    }

    if (($ew1 == '-1') And ($timezone1 > 2))
    {
      $my_error .= "You have marked West longitude but set an east time zone.<br>";
    }

    if (($ew1 == '1') And ($timezone1 < 0))
    {
      $my_error .= "You have marked East longitude but set a west time zone.<br>";
    }

    if ($start_month != "" And $start_day != "" And $start_year != "")
    {
      if (!$date = checkdate($start_month, $start_day, $start_year))
      {
        $my_error .= "The transit date you entered is not valid.<br>";
      }
    }

    if (($start_year < 1800) Or ($start_year >= 2400))
    {
      $my_error .= "Transit date - please enter a year between 1800 and 2399.<br>";
    }

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


    $validation_error = $my_form->validation();

    if ((!$validation_error) || ($my_error != ""))
    {
      $error = $my_form->create_msg();
      echo "<TABLE align='center' WIDTH='98%' BORDER='0' CELLSPACING='15' CELLPADDING='0'><tr><td><center><b>";
      echo "<font color='#ff0000' size=+2>Error! - The following error(s) occurred:</font><br>";

      if ($error)
      {
        echo $error . $my_error;
      }
      else
      {
        echo $error . "<br>" . $my_error;
      }

      echo "</font>";
      echo "<font color='#c020c0'";
      echo "<br><br>PLEASE RE-ENTER YOUR TIME ZONE DATA. THANK YOU.<br><br>";
      echo "</font>";
      echo "</b></center></td></tr></table>";
    }
    else
    {
      // no errors in filling out form, so process form
      $swephsrc = 'sweph';
      $sweph = 'sweph';

      putenv("PATH=$PATH:$swephsrc");

      $h_sys = "p";

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

      // adjust date and time for minus hour due to time zone taking the hour negative
      $utdatenow = strftime("%d.%m.%Y", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));
      $utnow = strftime("%H:%M:%S", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));

      exec ("swetest -edir$sweph -b$utdatenow -ut$utnow -p0123456789DAttt -eswe -house$my_longitude,$my_latitude,$h_sys -flsj -g, -head", $out);    //add a planet

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


      include("constants.php");     // this is here because we must rename the planet names


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


//get house positions of planets here - for natal person
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

//Transit calculations
      // Unset any variables not initialized elsewhere in the program
      unset($out,$longitude2,$speed2);

      // get all variables from form - Transits
      //get todays date and time
      $name2 = "Transits";
      $inmonth = $start_month;
      $inday = $start_day;
      $inyear = $start_year;

      $hour2 = gmdate("H");
      $minute2 = gmdate("i");
      $timezone2 = 0;

      $inhours = $hour2;
      $inmins = $minute2;
      $insecs = "0";

      $intz = $timezone2;

      // adjust date and time for minus hour due to time zone taking the hour negative
      $utdatenow = strftime("%d.%m.%Y", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));
      $utnow = strftime("%H:%M:%S", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));

      exec ("swetest -edir$sweph -b$utdatenow -ut$utnow -p0123456789DAttt -eswe -fls -g, -head", $out); //add a planet

      // Each line of output data from swetest is exploded into array $row, giving these elements:
      // 0 = longitude
      // 1 = speed
      // planets are index 0 - index (LAST_PLANET), house cusps are index (LAST_PLANET + 1) - (LAST_PLANET + 12)
      foreach ($out as $key => $line)
      {
        $row = explode(',',$line);
        $longitude2[$key] = $row[0];
        $speed2[$key] = $row[1];
      };

//add a planet - maybe some code needs to be put here


//get house positions of planets here - transit planets in natal houses
      for ($x = 1; $x <= 12; $x++)
      {
        for ($y = 0; $y <= LAST_PLANET; $y++)
        {
          $pl = $longitude2[$y] + (1 / 36000);
          if ($x < 12 And $longitude1[$x + LAST_PLANET] > $longitude1[$x + LAST_PLANET + 1])
          {
            If (($pl >= $longitude1[$x + LAST_PLANET] And $pl < 360) Or ($pl < $longitude1[$x + LAST_PLANET + 1] And $pl >= 0))
            {
              $house_pos2[$y] = $x;
              continue;
            }
          }

          if ($x == 12 And ($longitude1[$x + LAST_PLANET] > $longitude1[LAST_PLANET + 1]))
          {
            if (($pl >= $longitude1[$x + LAST_PLANET] And $pl < 360) Or ($pl < $longitude1[LAST_PLANET + 1] And $pl >= 0))
            {
              $house_pos2[$y] = $x;
            }
            continue;
          }

          if (($pl >= $longitude1[$x + LAST_PLANET]) And ($pl < $longitude1[$x + LAST_PLANET + 1]) And ($x < 12))
          {
            $house_pos2[$y] = $x;
            continue;
          }

          if (($pl >= $longitude1[$x + LAST_PLANET]) And ($pl < $longitude1[LAST_PLANET + 1]) And ($x == 12))
          {
            $house_pos2[$y] = $x;
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

      echo "<center>";
//      echo "<FONT color='#0000ff' SIZE='3' FACE='Arial'><b>$name_without_slashes</b><br>";
//      echo '<b>Born ' . strftime("%A, %B %d, %Y<br>%X (time zone = GMT $tz1 hours)</b><br>\n", mktime($hour1, $minute1, $secs, $month1, $day1, $year1));
//      echo "<b>" . $long_deg1 . $ew1_txt . $long_min1 . ", " . $lat_deg1 . $ns1_txt . $lat_min1 . "</b><br><br>";

//      echo "<b>$name2</b><br>";
//      echo '<b>On ' . strftime("%A, %B %d, %Y<br>%X (time zone = GMT)</b><br>\n", mktime($hour2, $minute2, $secs, $start_month, $start_day, $start_year));
//      echo "</font>";

      $line1 = $name_without_slashes . ", born " . strftime("%A, %B %d, %Y at %H:%M (time zone = GMT $tz1 hours)", mktime($hour1, $minute1, $secs, $month1, $day1, $year1));
      $line1 = $line1 . " at " . $long_deg1 . $ew1_txt . sprintf("%02d", $long_min1) . " and " . $lat_deg1 . $ns1_txt . sprintf("%02d", $lat_min1);

      $line2 = $name2 . " on " . strftime("%A, %B %d, %Y at %H:%M (time zone = GMT)", mktime($hour2, $minute2, $secs, $start_month, $start_day, $start_year));

      echo "</center>";

      $hr_ob1 = $hour1;
      $min_ob1 = $minute1;

      $ubt1 = 0;
      if (($hr_ob1 == 12) And ($min_ob1 == 0)) { $ubt1 = 1; }        // this person has an unknown birth time

      $hr_ob2 = $hour2;
      $min_ob2 = $minute2;

      $ubt2 = 1;    //always assume an unknown time

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

      $rx2 = "";
      for ($i = 0; $i <= SE_TNODE; $i++)
      {
        if ($speed2[$i] < 0)
        {
          $rx2 .= "R";
        }
        else
        {
          $rx2 .= " ";
        }
      }

      // to make GET string shorter
      for ($i = 0; $i <= LAST_PLANET; $i++)
      {
        $L1[$i] = $longitude1[$i];
        $L2[$i] = $longitude2[$i];
      }

      for ($i = 1; $i <= LAST_PLANET; $i++)
      {
        $hc1[$i] = $longitude1[LAST_PLANET + $i];
        $hc2[$i] = $longitude2[LAST_PLANET + $i];
      }

    echo "<br>";


// display all data - planets
    echo '<center><table width="65%" cellpadding="0" cellspacing="0" border="0">';

    echo '<tr>';
    echo "<td><font color='#0000ff'><b> Natal </b></font></td>";
    echo "<td><font color='#0000ff'><b> Longitude </b></font></td>";
    if ($ubt1 == 1)
    {
      echo "<td>&nbsp;</td>";
    }
    else
    {
      echo "<td><font color='#0000ff'><b> House<br>position </b></font></td>";
    }
    echo "<td><font color='#0000ff'><b> Transits </b></font></td>";
    echo "<td><font color='#0000ff'><b> Longitude </b></font></td>";
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
      echo "<td><font face='Courier New'>" . Convert_Longitude($L1[$i]) . " " . Mid($rx1, $i + 1, 1) . "</font></td>";
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

      if ($i <= LAST_PLANET - 2)
      {
        echo "<td>" . $pl_name[$i] . "</td>";
      }
      else
      {
        echo "<td> &nbsp </td>";
      }
      if ($i <= LAST_PLANET - 2)
      {
        echo "<td><font face='Courier New'>" . Convert_Longitude($L2[$i]) . " " . Mid($rx2, $i + 1, 1) . "</font></td>";
      }
      else
      {
        echo "<td> &nbsp </td>";
      }
      echo '</tr>';
    }

    echo '<tr>';
    echo "<td> &nbsp </td>";
    echo "<td> &nbsp </td>";
    echo "<td> &nbsp </td>";
    echo "<td> &nbsp </td>";
    echo '</tr>';

// display house cusps
    if ($ubt1 == 0)
    {
      echo '<tr>';
      echo "<td><font color='#0000ff'><b> House </b></font></td>";
      echo "<td><font color='#0000ff'><b> Longitude </b></font></td>";
      echo '</tr>';

      for ($i = 1; $i <= 12; $i++)
      {
        echo '<tr>';
        if ($i == 1)
        {
          echo "<td>Ascendant </td>";
        }
        elseif ($i == 10)
        {
          echo "<td>MC (Midheaven) </td>";
        }
        else
        {
          echo "<td>House " . ($i) . "</td>";
        }
        echo "<td><font face='Courier New'>" . Convert_Longitude($hc1[$i]) . "</font></td>";
        echo '</tr>';
      }
    }

    echo '</table></center>';
    echo "<br><br>";


// display transit data - aspect table
    $asp_name[1] = "Conjunction";
    $asp_name[2] = "Opposition";
    $asp_name[3] = "Trine";
    $asp_name[4] = "Square";
    $asp_name[5] = "Quincunx";
    $asp_name[6] = "Sextile";

    echo '<center><table width="40%" cellpadding="0" cellspacing="0" border="0">';

    echo '<tr>';
    echo "<td><font color='#0000ff'><b> Transits</b></font></td>";
    echo "<td><font color='#0000ff'><b> Aspect </b></font></td>";
    echo "<td><font color='#0000ff'><b> Natal Planet</b></font></td>";
    echo "<td><font color='#0000ff'><b> Orb </b></font></td>";
    echo '</tr>';

    // include Ascendant and MC
    $L1[LAST_PLANET + 1] = $hc1[1];
    $L1[LAST_PLANET + 2] = $hc1[10];

    $pl_name[LAST_PLANET + 1] = "Ascendant";
    $pl_name[LAST_PLANET + 2] = "Midheaven";

    for ($i = 0; $i <= LAST_PLANET - 2; $i++)
    {
      echo "<tr><td colspan='4'>&nbsp;</td></tr>";
      for ($j = 0; $j <= LAST_PLANET + 2; $j++)
      {
        $q = 0;
        $da = Abs($L2[$i] - $L1[$j]);

        if ($da > 180)
        {
          $da = 360 - $da;
        }

        $orb = 2;

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
    echo "<br><br>";

    if (EMAIL_enabled == True) { @mail(EMAIL, "Transits", ""); }


//display the transit chart report
    if ($no_interps == True)
    {
      include ('footer.html');
      exit();
    }
    else
    {
      echo '<center><table width="61.8%" cellpadding="0" cellspacing="0" border="0">';
      echo '<tr><td><font face="Verdana" size="3">';

      //display philosophy of astrology
      echo "<center><font size='+1' color='#0000ff'><b>TRANSIT PLANET ASPECTS TO NATAL PLANETS</b></font></center>";

      $file = "transit_files/aspect.txt";
      $fh = fopen($file, "r");
      $string = fread($fh, filesize($file));
      fclose($fh);

      $p_aspect_interp = nl2br($string);
      echo "<font size='2'>" . $p_aspect_interp . "</font>";


      // loop through each planet
      $days_back = 8;
      $days_ahead = 16;
      
      for ($i = 0; $i <= 12; $i++)      //transit planets - was 5 - ended with Jupiter
      {
        for ($j = 0; $j <= LAST_PLANET + 2; $j++)   //natal planets - was 9 - ended with Pluto
        {
          //if (($i == 1) Or ($j == 1 And $ubt1 == 1))
          if (($j == 1 Or $j == LAST_PLANET + 1 Or $j == LAST_PLANET + 2) And $ubt1 == 1)
          {
            //continue;     // do not allow Moon aspects for transit planets, or for natal planets if birth time is unknown
            continue;     // do not allow Moon aspects for natal planets if birth time is unknown
          }

          $da = Abs($L2[$i] - $L1[$j]);
          if ($da > 180) { $da = 360 - $da; }

          $orb = 2.0;       //orb = 2.0 degree

          // are planets within orb?
          $q = 1;
          if ($da <= $orb)
          {
            $q = 2;
          }
          elseif (($da <= 60 + $orb) And ($da >= 60 - $orb))
          {
            $q = 3;
          }
          elseif (($da <= 90 + $orb) And ($da >= 90 - $orb))
          {
            $q = 4;
          }
          elseif (($da <= 120 + $orb) And ($da >= 120 - $orb))
          {
            $q = 5;
          }
          elseif (($da <= 150 + $orb) And ($da >= 150 - $orb))
          {
            $q = 11;
          }
          elseif ($da >= 180 - $orb)
          {
            $q = 6;
          }

          if ($q > 1)
          {
            if ($q == 2)
            {
              $aspect = " Conjunct ";
              $angle = 0;
            }
            elseif ($q == 6)
            {
              $aspect = " Opposite ";
              $angle = 180;
            }
            elseif ($q == 3)
            {
              $aspect = " Sextile ";
              $angle = 60;
            }
            elseif ($q == 4)
            {
              $aspect = " Square ";
              $angle = 90;
            }
            elseif ($q == 5)
            {
              $aspect = " Trine ";
              $angle = 120;
            }
            elseif ($q == 11)
            {
              $aspect = " Quincunx ";
              $angle = 150;
            }

            $phrase_to_look_for = $pl_name[$i] . $aspect . $pl_name[$j];
            $file = "transit_files/" . strtolower($pl_name[$i]) . "_tr.txt";

            if (file_exists($file))
            {
              $string = Find_Specific_Report_Paragraph($phrase_to_look_for, $file, $pl_name[$i], $aspect, $pl_name[$j], $L2[$i], $speed2[$i]);
              $string = nl2br($string);
              echo "<font size='2'>" . $string . "</font>";

              if ($string != "")
              {
                $start_JD = gregoriantojd($start_month, $start_day, $start_year) - 0.5;   // find Julian day for specified date at midnight.
                $start_JD = $start_JD - $days_back;      // start a little early so we dont miss the day

                $p1_idx = $i;   // transit planet index into Swiss ephemeris

                $orb_rx = 2.0;

//                $Result_JD = array();

//                $orb_rx = 2.0;
//                if ($speed2[$i] < 0) { $orb_rx = -2.0; }

//                $n_planet = $longitude1[$j] - $orb_rx;
//                if ($n_planet < 0) { $n_planet = $n_planet + 360; }
//                $Result_JD[0] = Get_when_planet_is_at_certain_degree_TR($n_planet, $p1_idx, $pl_name, $start_JD, $start_JD + $days_ahead, $angle);

//                $n_planet = $longitude1[$j];
//                $Result_JD[1] = Get_when_planet_is_at_certain_degree_TR($n_planet, $p1_idx, $pl_name, $start_JD, $start_JD + $days_ahead, $angle);

//                $n_planet = $longitude1[$j] + $orb_rx;
//                if ($n_planet >= 360) { $n_planet = $n_planet - 360; }
//                $Result_JD[2] = Get_when_planet_is_at_certain_degree_TR($n_planet, $p1_idx, $pl_name, $start_JD, $start_JD + $days_ahead, $angle);

//-------------- start of bug fix ------------------

//the above works most of the time except when a planet is now direct, but was Rx 8 days ago, turns and goes back from where it came
//it may then miss the date and time where it is back to where it was in the past - example t. Venus trine n. Saturn
//Transit Venus Trine Saturn - Venus at 13 Cap 33' 24"
//This aspect starts on Sunday, January 26, 2014 at 16:27:59 GMT
//This aspect ends on Thursday, February 06, 2014 at 04:26:49 GMT
//the solution is to record ALL times when p1 is exact or -2 or +2 the n. planet
$cnt = 0;
$dt = array();

$n_planet = $L1[$j] - $orb_rx;
if ($n_planet < 0) { $n_planet = $n_planet + 360; }

$jd_result = Get_when_planet_is_at_certain_degree_TR($n_planet, $p1_idx, $pl_name, $start_JD, $start_JD + $days_ahead, $angle);

if ($jd_result > 0 )
{
  $dt[0][$cnt] = $jd_result;
  $dt[1][$cnt] = 0;
  $cnt++;
  $jd_result = Get_when_planet_is_at_certain_degree_TR($n_planet, $p1_idx, $pl_name, $jd_result + 0.1, $start_JD + $days_ahead, $angle);       //look for another aspect in this time period

  if ($jd_result > 0 )
  {
    $dt[0][$cnt] = $jd_result;
    $dt[1][$cnt] = 0;
    $cnt++;
  }
}

$n_planet = $L1[$j];
$jd_result = Get_when_planet_is_at_certain_degree_TR($n_planet, $p1_idx, $pl_name, $start_JD, $start_JD + $days_ahead, $angle);

if ($jd_result > 0 )
{
  $dt[0][$cnt] = $jd_result;
  $dt[1][$cnt] = 1;
  $cnt++;
  $jd_result = Get_when_planet_is_at_certain_degree_TR($n_planet, $p1_idx, $pl_name, $jd_result + 0.1, $start_JD + $days_ahead, $angle);       //look for another aspect in this time period

  if ($jd_result > 0 )
  {
    $dt[0][$cnt] = $jd_result;
    $dt[1][$cnt] = 1;
    $cnt++;
  }
}

$n_planet = $L1[$j] + $orb_rx;
if ($n_planet >= 360) { $n_planet = $n_planet - 360; }

$jd_result = Get_when_planet_is_at_certain_degree_TR($n_planet, $p1_idx, $pl_name, $start_JD, $start_JD + $days_ahead, $angle);

if ($jd_result > 0 )
{
  $dt[0][$cnt] = $jd_result;
  $dt[1][$cnt] = 0;
  $cnt++;
  $jd_result = Get_when_planet_is_at_certain_degree_TR($n_planet, $p1_idx, $pl_name, $jd_result + 0.1, $start_JD + $days_ahead, $angle);       //look for another aspect in this time period

  if ($jd_result > 0 )
  {
    $dt[0][$cnt] = $jd_result;
    $dt[1][$cnt] = 0;
    $cnt++;
  }
}

//sort the dates
//sort($dt, SORT_NUMERIC);
array_multisort($dt[0], SORT_NUMERIC, $dt[1], SORT_REGULAR);

if ($cnt == 0)
{
  $the_dt_1 = ConvertJDtoDateandTime_TR($start_JD, $timezone2);
  $the_dt_2 = ConvertJDtoDateandTime_TR($start_JD + $days_ahead, $timezone2);

  echo "<font size='2'>This aspect is active from " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_1[3], $the_dt_1[4], $the_dt_1[5], $the_dt_1[0], $the_dt_1[1], $the_dt_1[2])) . " GMT to " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_2[3], $the_dt_2[4], $the_dt_2[5], $the_dt_2[0], $the_dt_2[1], $the_dt_2[2])) . " GMT</font><br><br>";
}
elseif ($cnt == 1)
{
  if ($dt[1][0] == 1)
  {
    $the_dt_1 = ConvertJDtoDateandTime_TR($start_JD, $timezone2);
    $the_dt_2 = ConvertJDtoDateandTime_TR($start_JD + $days_ahead, $timezone2);

    echo "<font size='2'>This aspect is active from " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_1[3], $the_dt_1[4], $the_dt_1[5], $the_dt_1[0], $the_dt_1[1], $the_dt_1[2])) . " GMT to " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_2[3], $the_dt_2[4], $the_dt_2[5], $the_dt_2[0], $the_dt_2[1], $the_dt_2[2])) . " GMT</font><br><br>";

    $the_dt_1 = ConvertJDtoDateandTime_TR($dt[0][0], $timezone2);

    echo "<font size='2'>This aspect is exact on " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_1[3], $the_dt_1[4], $the_dt_1[5], $the_dt_1[0], $the_dt_1[1], $the_dt_1[2])) . " GMT</font><br><br>";
  }
  elseif ($dt[1][0] == 0)
  {
    $time_period_1 = $start_JD + $days_ahead - $dt[0][0];
    $time_period_2 = $dt[0][0] - $start_JD;
        
    if ($time_period_1 > $time_period_2)
    {
      //it's either this one . . .
      $the_dt_1 = ConvertJDtoDateandTime_TR($dt[0][0], $timezone2);
      $the_dt_2 = ConvertJDtoDateandTime_TR($start_JD + $days_ahead, $timezone2);

      echo "<font size='2'>This aspect is active from " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_1[3], $the_dt_1[4], $the_dt_1[5], $the_dt_1[0], $the_dt_1[1], $the_dt_1[2])) . " GMT to " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_2[3], $the_dt_2[4], $the_dt_2[5], $the_dt_2[0], $the_dt_2[1], $the_dt_2[2])) . " GMT</font><br><br>";
    }
    else
    {
      //or this one
      $the_dt_1 = ConvertJDtoDateandTime_TR($start_JD, $timezone2);
      $the_dt_2 = ConvertJDtoDateandTime_TR($dt[0][0], $timezone2);
    
      echo "<font size='2'>This aspect is active from " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_1[3], $the_dt_1[4], $the_dt_1[5], $the_dt_1[0], $the_dt_1[1], $the_dt_1[2])) . " GMT to " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_2[3], $the_dt_2[4], $the_dt_2[5], $the_dt_2[0], $the_dt_2[1], $the_dt_2[2])) . " GMT</font><br><br>";
    }
  }
}
elseif ($cnt == 2)
{
  //check for various conditions
  if ($dt[1][0] == 1 And $dt[1][1] == 0)
  {
    $the_dt_1 = ConvertJDtoDateandTime_TR($start_JD, $timezone2);
    $the_dt_2 = ConvertJDtoDateandTime_TR($dt[0][1], $timezone2);

    echo "<font size='2'>This aspect is active from " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_1[3], $the_dt_1[4], $the_dt_1[5], $the_dt_1[0], $the_dt_1[1], $the_dt_1[2])) . " GMT to " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_2[3], $the_dt_2[4], $the_dt_2[5], $the_dt_2[0], $the_dt_2[1], $the_dt_2[2])) . " GMT</font><br><br>";

    $the_dt_1 = ConvertJDtoDateandTime_TR($dt[0][0], $timezone2);

    echo "<font size='2'>This aspect is exact on " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_1[3], $the_dt_1[4], $the_dt_1[5], $the_dt_1[0], $the_dt_1[1], $the_dt_1[2])) . " GMT</font><br><br>";
  }
  elseif ($dt[1][0] == 0 And $dt[1][1] == 1)
  {
    $the_dt_1 = ConvertJDtoDateandTime_TR($dt[0][0], $timezone2);
    $the_dt_2 = ConvertJDtoDateandTime_TR($start_JD + $days_ahead, $timezone2);

    echo "<font size='2'>This aspect is active from " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_1[3], $the_dt_1[4], $the_dt_1[5], $the_dt_1[0], $the_dt_1[1], $the_dt_1[2])) . " GMT to " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_2[3], $the_dt_2[4], $the_dt_2[5], $the_dt_2[0], $the_dt_2[1], $the_dt_2[2])) . " GMT</font><br><br>";

    $the_dt_1 = ConvertJDtoDateandTime_TR($dt[0][1], $timezone2);

    echo "<font size='2'>This aspect is exact on " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_1[3], $the_dt_1[4], $the_dt_1[5], $the_dt_1[0], $the_dt_1[1], $the_dt_1[2])) . " GMT</font><br><br>";
  }
  elseif ($dt[1][0] == 1 And $dt[1][1] == 1)
  {
    $the_dt_1 = ConvertJDtoDateandTime_TR($start_JD, $timezone2);
    $the_dt_2 = ConvertJDtoDateandTime_TR($start_JD + $days_ahead, $timezone2);

    echo "<font size='2'>This aspect is active from " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_1[3], $the_dt_1[4], $the_dt_1[5], $the_dt_1[0], $the_dt_1[1], $the_dt_1[2])) . " GMT to " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_2[3], $the_dt_2[4], $the_dt_2[5], $the_dt_2[0], $the_dt_2[1], $the_dt_2[2])) . " GMT</font><br><br>";

    $the_dt_1 = ConvertJDtoDateandTime_TR($dt[0][0], $timezone2);

    echo "<font size='2'>This aspect is exact on " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_1[3], $the_dt_1[4], $the_dt_1[5], $the_dt_1[0], $the_dt_1[1], $the_dt_1[2])) . " GMT</font><br><br>";

    $the_dt_1 = ConvertJDtoDateandTime_TR($dt[0][1], $timezone2);

    echo "<font size='2'>This aspect is exact on " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_1[3], $the_dt_1[4], $the_dt_1[5], $the_dt_1[0], $the_dt_1[1], $the_dt_1[2])) . " GMT</font><br><br>";
  }
  else
  {
    $the_dt_1 = ConvertJDtoDateandTime_TR($dt[0][0], $timezone2);
    $the_dt_2 = ConvertJDtoDateandTime_TR($dt[0][1], $timezone2);

    echo "<font size='2'>This aspect is active from " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_1[3], $the_dt_1[4], $the_dt_1[5], $the_dt_1[0], $the_dt_1[1], $the_dt_1[2])) . " GMT to " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_2[3], $the_dt_2[4], $the_dt_2[5], $the_dt_2[0], $the_dt_2[1], $the_dt_2[2])) . " GMT</font><br><br>";
  }
}
elseif ($cnt == 3)
{
  $the_dt = ConvertJDtoDateandTime_TR($dt[0][0], $timezone2);
  echo "<font size='2'>This aspect starts on " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt[3], $the_dt[4], $the_dt[5], $the_dt[0], $the_dt[1], $the_dt[2])) . " GMT</font><br>";

  $the_dt = ConvertJDtoDateandTime_TR($dt[0][1], $timezone2);
  echo "<font size='2'>This aspect is exact on " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt[3], $the_dt[4], $the_dt[5], $the_dt[0], $the_dt[1], $the_dt[2])) . " GMT</font><br>";

  $the_dt = ConvertJDtoDateandTime_TR($dt[0][2], $timezone2);
  echo "<font size='2'>This aspect ends on " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt[3], $the_dt[4], $the_dt[5], $the_dt[0], $the_dt[1], $the_dt[2])) . " GMT</font><br><br><br>";
    
}

//-------------- end of bug fix ------------------


//                if ($Result_JD[2] < $Result_JD[0])
//                {
//                  $temp = $Result_JD[0];    //make sure that the earliest date is before the latest date
//                  $Result_JD[0] = $Result_JD[2];
//                  $Result_JD[0] = $temp;
//                }


//                if ($Result_JD[0] < $start_JD) { $Result_JD[0] = $start_JD; }
//                if ($Result_JD[2] > $start_JD + $days_ahead Or $Result_JD[2] == 0) { $Result_JD[2] = $start_JD + $days_ahead; }

  
//                if ($Result_JD[0] >= $start_JD And $Result_JD[0] <= $start_JD + $days_ahead)
//                {
//                  $the_dt = ConvertJDtoDateandTime_TR($Result_JD[0], $timezone2);
//                  echo "<font size='2'>This aspect starts on " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt[3], $the_dt[4], $the_dt[5], $the_dt[0], $the_dt[1], $the_dt[2])) . " GMT</font><br />";
//                }


//                if ($Result_JD[1] >= $start_JD And $Result_JD[1] <= $start_JD + $days_ahead)
//                {
//                  $the_dt = ConvertJDtoDateandTime_TR($Result_JD[1], $timezone2);
//                  echo "<font size='2'>This aspect is exact on " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt[3], $the_dt[4], $the_dt[5], $the_dt[0], $the_dt[1], $the_dt[2])) . " GMT</font><br />";
//                }


//                if ($Result_JD[2] >= $start_JD And $Result_JD[2] <= $start_JD + $days_ahead)
//                {
//                  $the_dt = ConvertJDtoDateandTime_TR($Result_JD[2], $timezone2);
//                  echo "<font size='2'>This aspect ends on " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt[3], $the_dt[4], $the_dt[5], $the_dt[0], $the_dt[1], $the_dt[2])) . " GMT</font><br /><br />";
//                }
              }
            }
          }
        }
      }

      if ($ubt1 == 0 And $no_house_interps == False)
      {
        //display planet in house interpretation
        //get header first
        echo "<br><center><font size='+1' color='#0000ff'><b>TRANSIT PLANETS IN NATAL HOUSES</b></font></center>";

        $file = "transit_files/house.txt";
        $fh = fopen($file, "r");
        $string = fread($fh, filesize($file));
        fclose($fh);

        $string = nl2br($string);
        $house_interp = $string;

        // loop through each planet
        for ($i = 0; $i <= 10; $i++)
        {
          $h_pos = $house_pos2[$i];
          $phrase_to_look_for = $pl_name[$i] . " in";
          $file = "transit_files/house_" . trim($h_pos) . ".txt";
          $string = Find_Specific_Report_Paragraph_HP($phrase_to_look_for, $file);
          $string = nl2br($string);
          $house_interp .= $string;
        }

        echo "<font size=2>" . $house_interp . "</font>";
      }

      //display closing
      $file = "transit_files/closing.txt";
      $fh = fopen($file, "r");
      $string = fread($fh, filesize($file));
      fclose($fh);

      $closing = nl2br($string);
      echo "<font size=2>" . $closing . "</font>";

      echo '</font></td></tr>';
      echo '</table></center>';
      echo "<br><br>";

      include ('footer.html');
      exit();
      }
    }
  }
  else
  {
    include ('header.php');       //here because of cookies

    $name1 = stripslashes($_COOKIE['name']);

    $month1 = $_COOKIE['month'];
    $day1 = $_COOKIE['day'];
    $year1 = $_COOKIE['year'];

    $hour1 = $_COOKIE['hour'];
    $minute1 = $_COOKIE['minute'];

    $timezone1 = $_COOKIE['timezone'];

    $long_deg1 = $_COOKIE["long_deg"];
    $long_min1 = $_COOKIE["long_min"];
    $ew1 = $_COOKIE["ew"];

    $lat_deg1 = $_COOKIE["lat_deg"];
    $lat_min1 = $_COOKIE["lat_min"];
    $ns1 = $_COOKIE["ns"];

    $start_month = strftime("%m", time());
    $start_day = strftime("%d", time());
    $start_year = strftime("%Y", time());
  }

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" target="_blank" style="margin: 0px 20px;">
  <fieldset><legend><font size='4'><b>Data entry for Transits - Enter your birth information</b></font></legend>

  &nbsp;&nbsp;<font color="#ff0000"><b>All fields are required.</b></font><br>

  <table style="font-size:12px;">
    <TR>
      <TD>
        <P align="right">Name:</P>
      </TD>

      <TD>
        <INPUT size="40" name="name1" value="<?php echo $name1; ?>">
      </TD>
    </TR>

    <TR>
      <TD>
        <P align="right">Birth date:</P>
      </TD>

      <TD>
        <?php
        echo '<select name="month1">';
        foreach ($months as $key => $value)
        {
          echo "<option value=\"$key\"";
          if ($key == $month1)
          {
            echo ' selected="selected"';
          }
          echo ">$value</option>\n";
        }
        echo '</select>';
        ?>

        <INPUT size="2" maxlength="2" name="day1" value="<?php echo $day1; ?>">
        <b>,</b>&nbsp;
        <INPUT size="4" maxlength="4" name="year1" value="<?php echo $year1; ?>">
         <font color="#0000ff">
        (only years from 1800 through 2399 are valid)
        </font>
      </TD>
    </TR>

    <TR>
      <td valign="top"><P align="right">Birth time:</P></td>
      <TD>
        <INPUT maxlength="2" size="2" name="hour1" value="<?php echo $hour1; ?>">
        <b>:</b>
        <INPUT maxlength="2" size="2" name="minute1" value="<?php echo $minute1; ?>">

        <br>

        <font color="#0000ff">
        (please give time of birth in <a href='time_24hr.html' target='_blank'>24 hour format</a>. If your birth time is unknown, please enter 12:00)<br>
        (if you were born EXACTLY at 12:00, then please enter 11:59 or 12:01 — 12:00 is reserved for unknown birth times only)
        <br><br>
        </font>
      </TD>
    </TR>

    <TR>
      <td valign="top">
        <P align="right">
        <b>&nbsp;</b>
        </P>
      </td>

      <td>
        <b>Please fill out the below and click NEXT in order to get your time zone (shown as 'Time Difference') data along with the longitude and latitude.<br>
        Once you get the proper values, then enter them into the 3 sections below (for example, if the 'Time Difference' is shown as '6W00',<br>
        then select 'GMT -06:00 hrs - CST or MDT or MWT' in the 'Time zone' drop-down box below).</b>
        <br>
        <iframe src="http://www.astrotheme.fr/partenaires/atlas.php?partenaire=9999&lang=en" frameborder="0" width="440" height="350"></iframe>
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
        <select name="timezone1" size="1">
          <?php
          echo "<option value='' ";
          if ($timezone1 == ""){ echo " selected"; }
          echo "> Select Time Zone </option>";

          echo "<option value='-12' ";
          if ($timezone1 == "-12"){ echo " selected"; }
          echo ">GMT -12:00 hrs - IDLW</option>";

          echo "<option value='-11' ";
          if ($timezone1 == "-11"){ echo " selected"; }
          echo ">GMT -11:00 hrs - BET or NT</option>";

          echo "<option value='-10.5' ";
          if ($timezone1 == "-10.5"){ echo " selected"; }
          echo ">GMT -10:30 hrs - HST</option>";

          echo "<option value='-10' ";
          if ($timezone1 == "-10"){ echo " selected"; }
          echo ">GMT -10:00 hrs - AHST</option>";

          echo "<option value='-9.5' ";
          if ($timezone1 == "-9.5"){ echo " selected"; }
          echo ">GMT -09:30 hrs - HDT or HWT</option>";

          echo "<option value='-9' ";
          if ($timezone1 == "-9"){ echo " selected"; }
          echo ">GMT -09:00 hrs - YST or AHDT or AHWT</option>";

          echo "<option value='-8' ";
          if ($timezone1 == "-8"){ echo " selected"; }
          echo ">GMT -08:00 hrs - PST or YDT or YWT</option>";

          echo "<option value='-7' ";
          if ($timezone1 == "-7"){ echo " selected"; }
          echo ">GMT -07:00 hrs - MST or PDT or PWT</option>";

          echo "<option value='-6' ";
          if ($timezone1 == "-6"){ echo " selected"; }
          echo ">GMT -06:00 hrs - CST or MDT or MWT</option>";

          echo "<option value='-5' ";
          if ($timezone1 == "-5"){ echo " selected"; }
          echo ">GMT -05:00 hrs - EST or CDT or CWT</option>";

          echo "<option value='-4' ";
          if ($timezone1 == "-4"){ echo " selected"; }
          echo ">GMT -04:00 hrs - AST or EDT or EWT</option>";

          echo "<option value='-3.5' ";
          if ($timezone1 == "-3.5"){ echo " selected"; }
          echo ">GMT -03:30 hrs - NST</option>";

          echo "<option value='-3' ";
          if ($timezone1 == "-3"){ echo " selected"; }
          echo ">GMT -03:00 hrs - BZT2 or AWT</option>";

          echo "<option value='-2' ";
          if ($timezone1 == "-2"){ echo " selected"; }
          echo ">GMT -02:00 hrs - AT</option>";

          echo "<option value='-1' ";
          if ($timezone1 == "-1"){ echo " selected"; }
          echo ">GMT -01:00 hrs - WAT</option>";

          echo "<option value='0' ";
          if ($timezone1 == "0"){ echo " selected"; }
          echo ">Greenwich Mean Time - GMT or UT</option>";

          echo "<option value='1' ";
          if ($timezone1 == "1"){ echo " selected"; }
          echo ">GMT +01:00 hrs - CET or MET or BST</option>";

          echo "<option value='2' ";
          if ($timezone1 == "2"){ echo " selected"; }
          echo ">GMT +02:00 hrs - EET or CED or MED or BDST or BWT</option>";

          echo "<option value='3' ";
          if ($timezone1 == "3"){ echo " selected"; }
          echo ">GMT +03:00 hrs - BAT or EED</option>";

          echo "<option value='3.5' ";
          if ($timezone1 == "3.5"){ echo " selected"; }
          echo ">GMT +03:30 hrs - IT</option>";

          echo "<option value='4' ";
          if ($timezone1 == "4"){ echo " selected"; }
          echo ">GMT +04:00 hrs - USZ3</option>";

          echo "<option value='5' ";
          if ($timezone1 == "5"){ echo " selected"; }
          echo ">GMT +05:00 hrs - USZ4</option>";

          echo "<option value='5.5' ";
          if ($timezone1 == "5.5"){ echo " selected"; }
          echo ">GMT +05:30 hrs - IST</option>";

          echo "<option value='6' ";
          if ($timezone1 == "6"){ echo " selected"; }
          echo ">GMT +06:00 hrs - USZ5</option>";

          echo "<option value='6.5' ";
          if ($timezone1 == "6.5"){ echo " selected"; }
          echo ">GMT +06:30 hrs - NST</option>";

          echo "<option value='7' ";
          if ($timezone1 == "7"){ echo " selected"; }
          echo ">GMT +07:00 hrs - SST or USZ6</option>";

          echo "<option value='7.5' ";
          if ($timezone1 == "7.5"){ echo " selected"; }
          echo ">GMT +07:30 hrs - JT</option>";

          echo "<option value='8' ";
          if ($timezone1 == "8"){ echo " selected"; }
          echo ">GMT +08:00 hrs - AWST or CCT</option>";

          echo "<option value='8.5' ";
          if ($timezone1 == "8.5"){ echo " selected"; }
          echo ">GMT +08:30 hrs - MT</option>";

          echo "<option value='9' ";
          if ($timezone1 == "9"){ echo " selected"; }
          echo ">GMT +09:00 hrs - JST or AWDT</option>";

          echo "<option value='9.5' ";
          if ($timezone1 == "9.5"){ echo " selected"; }
          echo ">GMT +09:30 hrs - ACST or SAT or SAST</option>";

          echo "<option value='10' ";
          if ($timezone1 == "10"){ echo " selected"; }
          echo ">GMT +10:00 hrs - AEST or GST</option>";

          echo "<option value='10.5' ";
          if ($timezone1 == "10.5"){ echo " selected"; }
          echo ">GMT +10:30 hrs - ACDT or SDT or SAD</option>";

          echo "<option value='11' ";
          if ($timezone1 == "11"){ echo " selected"; }
          echo ">GMT +11:00 hrs - UZ10 or AEDT</option>";

          echo "<option value='11.5' ";
          if ($timezone1 == "11.5"){ echo " selected"; }
          echo ">GMT +11:30 hrs - NZ</option>";

          echo "<option value='12' ";
          if ($timezone1 == "12"){ echo " selected"; }
          echo ">GMT +12:00 hrs - NZT or IDLE</option>";

          echo "<option value='12.5' ";
          if ($timezone1 == "12.5"){ echo " selected"; }
          echo ">GMT +12:30 hrs - NZS</option>";

          echo "<option value='13' ";
          if ($timezone1 == "13"){ echo " selected"; }
          echo ">GMT +13:00 hrs - NZST</option>";
          ?>
        </select>

        <br>

        <font color="#0000ff">
        (example: Chicago is "GMT -06:00 hrs" (standard time), Paris is "GMT +01:00 hrs" (standard time).<br>
        Add 1 hour if Daylight Saving was in effect when you were born (select next time zone down in the list).
        <br><br>
        </font>
      </TD>
    </TR>

    <TR>
      <td valign="top"><P align="right">Longitude:</P></td>
      <TD>
        <INPUT maxlength="3" size="3" name="long_deg1" value="<?php echo $long_deg1; ?>">
        <select name="ew1">
          <?php
          if ($ew1 == "-1")
          {
            echo "<option value='-1' selected>W </option>";
            echo "<option value='1'>E </option>";
          }
          elseif ($ew1 == "1")
          {
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

        <INPUT maxlength="2" size="2" name="long_min1" value="<?php echo $long_min1; ?>">
        <font color="#0000ff">
        (example: Chicago is 87 W 39, Sydney is 151 E 13)
        </font>
      </TD>
    </TR>

    <TR>
      <td valign="top"><P align="right">Latitude:</P></td>

      <TD>
        <INPUT maxlength="2" size="3" name="lat_deg1" value="<?php echo $lat_deg1; ?>">
        <select name="ns1">
          <?php
          if ($ns1 == "1")
          {
            echo "<option value='1' selected>N&nbsp;&nbsp;</option>";
            echo "<option value='-1'>S&nbsp;&nbsp;</option>";
          }
          elseif ($ns1 == "-1")
          {
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

        <INPUT maxlength="2" size="2" name="lat_min1" value="<?php echo $lat_min1; ?>">
        <font color="#0000ff">
        (example: Chicago is 41 N 51, Sydney is 33 S 52)
        </font>
        <br>
      </TD>
    </TR>
  </table>

  <br><hr><br>

  <table style="font-size:12px;">
    <TR>
      <TD>
        <P align="right"><b>Transit date:</b></P>
      </TD>

      <TD>
        <?php
        echo '<select name="start_month">';
        foreach ($months as $key => $value)
        {
          echo "<option value=\"$key\"";
          if ($key == $start_month)
          {
            echo ' selected="selected"';
          }
          echo ">$value</option>\n";
        }
        echo '</select>';
        ?>

        <INPUT size="2" maxlength="2" name="start_day" value="<?php echo $start_day; ?>">
        <b>,</b>&nbsp;
        <INPUT size="4" maxlength="4" name="start_year" value="<?php echo $start_year; ?>">
         <font color="#0000ff">
        (only years from 1800 through 2399 are valid)
        </font>
      </TD>
    </TR>
  </table>

  <center>
  <br><br>
  <font color="#ff0000"><b>Most people mess up the time zone selection. Please make sure your selection is correct.</b></font><br><br>
  <input type="hidden" name="submitted" value="TRUE">
  <INPUT type="submit" name="submit" value="Submit data (AFTER DOUBLE-CHECKING IT FOR ERRORS)" align="middle" style="background-color:#66ff66;color:#000000;font-size:16px;font-weight:bold">
  </center>

  <br>
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


Function safeEscapeString($string)
{
// replace HTML tags '<>' with '[]'
  $temp1 = str_replace("<", "[", $string);
  $temp2 = str_replace(">", "]", $temp1);

// but keep <br> or <br>
// turn <br> into <br> so later it will be turned into ""
// using just <br> will add extra blank lines
  $temp1 = str_replace("[br]", "<br>", $temp2);
  $temp2 = str_replace("[br /]", "<br>", $temp1);

  if (get_magic_quotes_gpc())
  {
    return $temp2;
  }
  else
  {
    return mysql_escape_string($temp2);
  }
}


Function Find_Specific_Report_Paragraph($phrase_to_look_for, $file, $pl1_name, $aspect, $pl2_name, $pl_pos, $pl_speed)
{
  $string = "";
  $len = strlen($phrase_to_look_for);

  if (!file_exists($file))
  {
    return "";
  }

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
          if ($pl_speed < 0)
          {
            //retrograde
            $string .= "<b>Transit " . $pl1_name . $aspect . $pl2_name . "</b> - " . $pl1_name . " (Rx) at " . Convert_Longitude($pl_pos) . "<br>";
          }
          else
          {
            $string .= "<b>Transit " . $pl1_name . $aspect . $pl2_name . "</b> - " . $pl1_name . " at " . Convert_Longitude($pl_pos) . "<br>";
          }
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


Function Find_Specific_Report_Paragraph_HP($phrase_to_look_for, $file)
{
  $string = "";
  $len = strlen($phrase_to_look_for);

  if (!file_exists($file))
  {
    return "";
  }

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
          $string .= "<b>Transit " . $file_array[$i] . "</b>";
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


Function Get_when_planet_is_at_certain_degree_TR($degr, $p1_idx, $pl_name, $starting_JD, $ending_JD, $angle)
{
//aspectarian for when a planet hits a certain degree of the zodiac
  for ($x = $starting_JD; $x <= $ending_JD; $x++)
  {
    //$angle = 0;
    $Result_JD = Secant_Method_one_degree_TR($x, $x + 1, 0.00007, 100, $angle, $p1_idx, $degr);

    $rnd_off = $Result_JD;
    if ($rnd_off >= $x And $rnd_off < $x + 1)
    {
      return $Result_JD;
    }
  }
}


Function Secant_Method_one_degree_TR($earlier_jd, $later_jd, $e, $m, $angle, $p1_idx, $degr)
{
  for ($n = 1; $n <= $m; $n++)
  {
    //get positions of both planets on JD = later_jd and JD = earlier_jd
    $y1 = Get_1_Planet_geo_TR($later_jd, $p1_idx);
    $y3 = Get_1_Planet_geo_TR($earlier_jd, $p1_idx);

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


Function Get_1_Planet_geo_TR($jd, $p_idx)
{
  //get longitude of planet indicated by $p_idx
  $swephsrc = 'sweph';
  $sweph = 'sweph';

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


Function ConvertJDtoDateandTime_TR($Result_JD, $current_tz)
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

?>
