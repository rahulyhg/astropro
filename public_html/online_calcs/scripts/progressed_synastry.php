<?php
  include ('../accesscontrol.php');

  if ($is_logged_in == False) { exit(); }

  require_once ('../../../mysqli_connect_online_calcs_db_MYSQLI.php');
  require_once ('../../../my_functions_MYSQLI.php');

  $months = array (0 => 'Choose month', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

  // check if the form has been submitted
  if (isset($_POST['submitted']) Or isset($_POST['h_sys_submitted']))
  {
    $id1 = safeEscapeString($conn, $_POST["id1"]);
    $id2 = safeEscapeString($conn, $_POST["id2"]);

    if (!is_numeric($id1) Or !is_numeric($id2))
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
      echo "<center><br /><br />I cannot find person #1 in the database. Please try again.</center>";
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


    $sql = "SELECT * FROM birth_info WHERE ID='$id2' And entered_by='$username'";

    $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $num_records = MYSQLI_NUM_rows($result);

    if ($num_records != 1)
    {
      echo "<center><br /><br />I cannot find person #2 in the database. Please try again.</center>";
      include ('footer.html');
      exit();
    }

    // get all variables from database
    $name2 = $row['name'];

    $month2 = $row['month'];
    $day2 = $row['day'];
    $year2 = $row['year'];

    $hour2 = $row['hour'];
    $minute2 = $row['minute'];

    $timezone2 = $row['timezone'];

    $long_deg2 = $row['long_deg'];
    $long_min2 = $row['long_min'];
    $ew2 = $row['ew'];

    $lat_deg2 = $row['lat_deg'];
    $lat_min2 = $row['lat_min'];
    $ns2 = $row['ns'];


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


    include ('header_progressed_synastry.html');        //here because of setting cookies above

    // get all variables from form - transit date
    $start_month = safeEscapeString($conn, $_POST["start_month"]);
    $start_day = safeEscapeString($conn, $_POST["start_day"]);
    $start_year = safeEscapeString($conn, $_POST["start_year"]);

    if ($start_day < 1 Or $start_day > 31)
    {
      $start_day = strftime("%d", time());
    }


    // additional error checks on user-entered data
    if ($start_month != "" And $start_day != "" And $start_year != "")
    {
      if (!$date = checkdate($start_month, $start_day, $start_year))
      {
        $my_error .= "The progressed date you entered is not valid.<br>";
      }
    }

    if (($start_year < 1200) Or ($start_year >= 2400))
    {
      $my_error .= "Progressed date - please enter a year between 1200 and 2399.<br>";
    }


    if ($my_error != "")
    {
      echo "<TABLE align='center' WIDTH='98%' BORDER='0' CELLSPACING='15' CELLPADDING='0'><tr><td><center><b>";
      echo "<font color='#ff0000' size=+2>Error! - The following error(s) occurred:</font><br>";

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


      include("constants_eng.php");     // this is here because we must rename the planet names


//calculate the Part of Fortune - person 1
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


//capture the Vertex longitude - person 1
      $longitude1[LAST_PLANET] = $longitude1[LAST_PLANET + 16];   //Asc = +13, MC = +14, RAMC = +15, Vertex = +16


//get house positions of planets here - person 1
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


//Progressed planet calculations - person 1
      // Unset any variables not initialized elsewhere in the program
      unset($out,$longitude1a,$speed1a);

      //$name2 = "Progressions";

    // get progressed birthday
      $n_mc = $house_pos1[10];
      $n_sun = $longitude1[0];
      $latitude = $my_latitude;

      $birth_JD = gregoriantojd($month1, $day1, $year1) - 0.5;          // find Julian day for birth date at midnight.
      $start_JD = gregoriantojd($start_month, $start_day, $start_year) - 0.5; // find Julian day for start of relationship at midnight.

      $birth_JD = $birth_JD + (($inhours + ($inmins / 60)) / 24);
      $start_JD = $start_JD + (($inhours + ($inmins / 60)) / 24);

      $days_alive = $start_JD - $birth_JD;
      $prog_time_to_add = $days_alive / 365.25;
      $jd_to_use = $birth_JD + $prog_time_to_add;

      exec ("swetest -edir$sweph -bj$jd_to_use -ut -p0123456789DAttt -eswe -fls -g, -head", $out);  //add a planet

      // Each line of output data from swetest is exploded into array $row, giving these elements:
      // 0 = longitude
      // 1 = speed
      // planets are index 0 - index (LAST_PLANET), house cusps are index (LAST_PLANET + 1) - (LAST_PLANET + 12)
      foreach ($out as $key => $line)
      {
        $row = explode(',',$line);
        $longitude1a[$key] = $row[0];
        $speed1a[$key] = $row[1];
      };

      $p_mc = Crunch($longitude1[LAST_PLANET + 10] + $longitude1a[0] - $longitude1[0]);
      $ob_deg = Get_OB_Ecl($jd_to_use);

      $p_RAMC = r2d(atan(Cosine($ob_deg) * Sine($p_mc) / Cosine($p_mc)));

      if (Cosine($p_mc) < 0)
      {
        $p_RAMC = $p_RAMC + 180;
      }

      if ($p_RAMC < 0)
      {
        $p_RAMC = $p_RAMC + 360;
      }

      $p_RAMC_r = d2r($p_RAMC);
      $ob_r = d2r($ob_deg);

      $sph = -Sine($p_RAMC) * Cosine($ob_deg) - tan(d2r($latitude)) * Sine($ob_deg);
      if ($sph == 0)
      {
        $a1 = 3.1415926535 / 2;
      }
      else
      {
        $a1 = atan(Cosine($p_RAMC) / ($sph));
      }

      if ($a1 < 0)
      {
        $a1 = $a1 + 3.1415926535;
      }

      if (Cosine($p_RAMC) < 0)
      {
        $a1 = $a1 + 3.1415926535;
      }

      $longitude1a[LAST_PLANET + 1] = Mod360(r2d($a1));
      $longitude1a[LAST_PLANET + 10] = $p_mc;


//get the progressed Part of Fortune
      if ($day_chart == True)
      {
        $longitude1a[SE_POF] = $longitude1a[LAST_PLANET + 1] + $longitude1a[1] - $longitude1a[0];
      }
      else
      {
        $longitude1a[SE_POF] = $longitude1a[LAST_PLANET + 1] - $longitude1a[1] + $longitude1a[0];
      }

      if ($longitude1a[SE_POF] >= 360)
      {
        $longitude1a[SE_POF] = $longitude1a[SE_POF] - 360;
      }

      if ($longitude1a[SE_POF] < 0)
      {
        $longitude1a[SE_POF] = $longitude1a[SE_POF] + 360;
      }


//Person 2 calculations
      // Unset any variables not initialized elsewhere in the program
      unset($PATH,$out,$longitude2,$house_pos2);

      $inmonth = $month2;
      $inday = $day2;
      $inyear = $year2;

      $inhours = $hour2;
      $inmins = $minute2;
      $insecs = "0";

      $intz = $timezone2;

      $my_longitude = $ew2 * ($long_deg2 + ($long_min2 / 60));
      $my_latitude = $ns2 * ($lat_deg2 + ($lat_min2 / 60));

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

      exec ("swetest -edir$sweph -b$utdatenow -ut$utnow -p0123456789DAttt -eswe -house$my_longitude,$my_latitude,$h_sys -flsj -g, -head", $out);    //add a planet

      // Each line of output data from swetest is exploded into array $row, giving these elements:
      // 0 = longitude
      // 1 = speed
      // 2 = house position
      // planets are index 0 - index (LAST_PLANET), house cusps are index (LAST_PLANET + 1) - (LAST_PLANET + 12)
      foreach ($out as $key => $line)
      {
        $row = explode(',',$line);
        $longitude2[$key] = $row[0];
        $speed2[$key] = $row[1];
        $house_pos2[$key] = $row[2];
      };


//calculate the Part of Fortune - person 2
      //is this a day chart or a night chart?
      if ($longitude2[LAST_PLANET + 1] > $longitude2[LAST_PLANET + 7])
      {
        if ($longitude2[0] <= $longitude2[LAST_PLANET + 1] And $longitude2[0] > $longitude2[LAST_PLANET + 7])
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
        if ($longitude2[0] > $longitude2[LAST_PLANET + 1] And $longitude2[0] <= $longitude2[LAST_PLANET + 7])
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
        $longitude2[SE_POF] = $longitude2[LAST_PLANET + 1] + $longitude2[1] - $longitude2[0];
      }
      else
      {
        $longitude2[SE_POF] = $longitude2[LAST_PLANET + 1] - $longitude2[1] + $longitude2[0];
      }

      if ($longitude2[SE_POF] >= 360)
      {
        $longitude2[SE_POF] = $longitude2[SE_POF] - 360;
      }

      if ($longitude2[SE_POF] < 0)
      {
        $longitude2[SE_POF] = $longitude2[SE_POF] + 360;
      }


//capture the Vertex longitude - person 2
      $longitude2[LAST_PLANET] = $longitude2[LAST_PLANET + 16];   //Asc = +13, MC = +14, RAMC = +15, Vertex = +16


//get house positions of planets here - person 2
      for ($x = 1; $x <= 12; $x++)
      {
        for ($y = 0; $y <= LAST_PLANET; $y++)
        {
          $pl = $longitude2[$y] + (1 / 36000);
          if ($x < 12 And $longitude2[$x + LAST_PLANET] > $longitude2[$x + LAST_PLANET + 1])
          {
            If (($pl >= $longitude2[$x + LAST_PLANET] And $pl < 360) Or ($pl < $longitude2[$x + LAST_PLANET + 1] And $pl >= 0))
            {
              $house_pos2[$y] = $x;
              continue;
            }
          }

          if ($x == 12 And ($longitude2[$x + LAST_PLANET] > $longitude2[LAST_PLANET + 1]))
          {
            if (($pl >= $longitude2[$x + LAST_PLANET] And $pl < 360) Or ($pl < $longitude2[LAST_PLANET + 1] And $pl >= 0))
            {
              $house_pos2[$y] = $x;
            }
            continue;
          }

          if (($pl >= $longitude2[$x + LAST_PLANET]) And ($pl < $longitude2[$x + LAST_PLANET + 1]) And ($x < 12))
          {
            $house_pos2[$y] = $x;
            continue;
          }

          if (($pl >= $longitude2[$x + LAST_PLANET]) And ($pl < $longitude2[LAST_PLANET + 1]) And ($x == 12))
          {
            $house_pos2[$y] = $x;
          }
        }
      }


//Progressed planet calculations - person 2
      // Unset any variables not initialized elsewhere in the program
      unset($out,$longitude2a,$speed2a);

      //$name2 = "Progressions";

    // get progressed birthday
      $n_mc = $house_pos2[10];
      $n_sun = $longitude2[0];
      $latitude = $my_latitude;

      $birth_JD = gregoriantojd($month2, $day2, $year2) - 0.5;          // find Julian day for birth date at midnight.
      $start_JD = gregoriantojd($start_month, $start_day, $start_year) - 0.5; // find Julian day for start of relationship at midnight.

      $birth_JD = $birth_JD + (($inhours + ($inmins / 60)) / 24);
      $start_JD = $start_JD + (($inhours + ($inmins / 60)) / 24);

      $days_alive = $start_JD - $birth_JD;
      $prog_time_to_add = $days_alive / 365.25;
      $jd_to_use = $birth_JD + $prog_time_to_add;

      exec ("swetest -edir$sweph -bj$jd_to_use -ut -p0123456789DAttt -eswe -fls -g, -head", $out);  //add a planet

      // Each line of output data from swetest is exploded into array $row, giving these elements:
      // 0 = longitude
      // 1 = speed
      // planets are index 0 - index (LAST_PLANET), house cusps are index (LAST_PLANET + 1) - (LAST_PLANET + 12)
      foreach ($out as $key => $line)
      {
        $row = explode(',',$line);
        $longitude2a[$key] = $row[0];
        $speed2a[$key] = $row[1];
      };

      $p_mc = Crunch($longitude2[LAST_PLANET + 10] + $longitude2a[0] - $longitude2[0]);
      $ob_deg = Get_OB_Ecl($jd_to_use);

      $p_RAMC = r2d(atan(Cosine($ob_deg) * Sine($p_mc) / Cosine($p_mc)));

      if (Cosine($p_mc) < 0)
      {
        $p_RAMC = $p_RAMC + 180;
      }

      if ($p_RAMC < 0)
      {
        $p_RAMC = $p_RAMC + 360;
      }

      $p_RAMC_r = d2r($p_RAMC);
      $ob_r = d2r($ob_deg);

      $a1 = atan(Cosine($p_RAMC) / (-Sine($p_RAMC) * Cosine($ob_deg) - tan(d2r($latitude)) * Sine($ob_deg)));

      if ($a1 < 0)
      {
        $a1 = $a1 + 3.1415926535;
      }

      if (Cosine($p_RAMC) < 0)
      {
        $a1 = $a1 + 3.1415926535;
      }

      $longitude2a[LAST_PLANET + 1] = Mod360(r2d($a1));
      $longitude2a[LAST_PLANET + 10] = $p_mc;


//get the progressed Part of Fortune
      if ($day_chart == True)
      {
        $longitude2a[SE_POF] = $longitude2a[LAST_PLANET + 1] + $longitude2a[1] - $longitude2a[0];
      }
      else
      {
        $longitude2a[SE_POF] = $longitude2a[LAST_PLANET + 1] - $longitude2a[1] + $longitude2a[0];
      }

      if ($longitude2a[SE_POF] >= 360)
      {
        $longitude2a[SE_POF] = $longitude2a[SE_POF] - 360;
      }

      if ($longitude2a[SE_POF] < 0)
      {
        $longitude2a[SE_POF] = $longitude2a[SE_POF] + 360;
      }


//get house positions of planets here - person 1 progressed planets in person 1 houses
      for ($x = 1; $x <= 12; $x++)
      {
        for ($y = 0; $y <= LAST_PLANET; $y++)
        {
          $pl = $longitude1a[$y] + (1 / 36000);
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


//get house positions of planets here - person 2 progressed planets in person 1 houses
      for ($x = 1; $x <= 12; $x++)
      {
        for ($y = 0; $y <= LAST_PLANET; $y++)
        {
          $pl = $longitude2a[$y] + (1 / 36000);
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
      $name2_without_slashes = stripslashes($name2);

      echo "<center>";

//      $line1 = $name_without_slashes . ", born " . strftime("%A, %B %d, %Y at %H:%M (time zone = GMT $tz1 hours)", mktime($hour1, $minute1, $secs, $month1, $day1, $year1));
//      $line1 = $line1 . " at " . $long_deg1 . $ew1_txt . $long_min1 . " and " . $lat_deg1 . $ns1_txt . $lat_min1;

//      $line2 = $name2 . " on " . strftime("%A, %B %d, %Y at %H:%M (time zone = GMT $tz1 hours)", mktime($hour1, $minute1, $secs, $start_month, $start_day, $start_year));

      $line1 = $name_without_slashes . ", born " . strftime("%A, %B %d, %Y at %H:%M (time zone = GMT $tz1 hours)", mktime($hour1, $minute1, $secs, $month1, $day1, $year1));
      $line1 = $line1 . " at " . $long_deg1 . $ew1_txt . sprintf("%02d", $long_min1) . " and " . $lat_deg1 . $ns1_txt . sprintf("%02d", $lat_min1);

      $line2 = $name2_without_slashes . ", born " . strftime("%A, %B %d, %Y at %H:%M (time zone = GMT $tz2 hours)", mktime($hour2, $minute2, $secs, $month2, $day2, $year2));
      $line2 = $line2 . " at " . $long_deg2 . $ew2_txt . sprintf("%02d", $long_min2) . " and " . $lat_deg2 . $ns2_txt . sprintf("%02d", $lat_min2);

    $line2 = $line2 . " - Progressions for " . strftime("%A, %B %d, %Y", mktime($hour1, $minute1, $secs, $start_month, $start_day, $start_year));

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

        <input type="hidden" name="id2" value="<?php echo $_POST['id2']; ?>">
        <input type="hidden" name="name2" value="<?php echo stripslashes($_POST['name2']); ?>">
        <input type="hidden" name="month2" value="<?php echo $_POST['month2']; ?>">
        <input type="hidden" name="day2" value="<?php echo $_POST['day2']; ?>">
        <input type="hidden" name="year2" value="<?php echo $_POST['year2']; ?>">
        <input type="hidden" name="hour2" value="<?php echo $_POST['hour2']; ?>">
        <input type="hidden" name="minute2" value="<?php echo $_POST['minute2']; ?>">
        <input type="hidden" name="timezone2" value="<?php echo $_POST['timezone2']; ?>">
        <input type="hidden" name="long_deg2" value="<?php echo $_POST['long_deg2']; ?>">
        <input type="hidden" name="long_min2" value="<?php echo $_POST['long_min2']; ?>">
        <input type="hidden" name="ew2" value="<?php echo $_POST['ew2']; ?>">
        <input type="hidden" name="lat_deg2" value="<?php echo $_POST['lat_deg2']; ?>">
        <input type="hidden" name="lat_min2" value="<?php echo $_POST['lat_min2']; ?>">
        <input type="hidden" name="ns2" value="<?php echo $_POST['ns2']; ?>">

        <input type="hidden" name="start_month" value="<?php echo $_POST['start_month']; ?>">
        <input type="hidden" name="start_day" value="<?php echo $_POST['start_day']; ?>">
        <input type="hidden" name="start_year" value="<?php echo $_POST['start_year']; ?>">

        <input type="hidden" name="h_sys_submitted" value="TRUE">
        <INPUT type="submit" name="submit" value="Go" align="middle" style="background-color:#66ff66;color:#000000;font-size:16px;font-weight:bold">
      </form>
<?php
      echo "</center>";

      $hr_ob1 = $hour1;
      $min_ob1 = $minute1;

      $ubt1 = 0;
      if (($hr_ob1 == 12) And ($min_ob1 == 0))
      {
        $ubt1 = 1;        // person #1 has an unknown birth time
      }


      $hr_ob2 = $hour2;
      $min_ob2 = $minute2;

      $ubt2 = 0;
      if (($hr_ob2 == 12) And ($min_ob2 == 0))
      {
        $ubt2 = 1;        // person #2 has an unknown birth time
      }


      $rx1 = "";
      for ($i = 0; $i <= SE_TNODE; $i++)
      {
        if ($speed1a[$i] < 0)
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
        if ($speed2a[$i] < 0)
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
        $L1[$i] = $longitude1a[$i];
        $L2[$i] = $longitude2a[$i];
      }

      for ($i = 1; $i <= LAST_PLANET; $i++)
      {
        $hc1[$i] = $longitude1[LAST_PLANET + $i];
      }


      for ($i = 1; $i <= 12; $i++)
      {
        $hc2[$i] = $longitude2[LAST_PLANET + $i];
      }

      $hc2[13] = 0;


    $ASC_2 = $longitude2[LAST_PLANET + 1];
    $MC_2 = $longitude2[LAST_PLANET + 10];

    $hc2[1] = $longitude2a[LAST_PLANET + 1];
    $hc2[10] = $longitude2a[LAST_PLANET + 10];


  $L1[LAST_PLANET + 1] = $longitude1a[LAST_PLANET + 1];
  $L1[LAST_PLANET + 2] = $longitude1a[LAST_PLANET + 10];

  $L2[LAST_PLANET + 1] = $longitude2a[LAST_PLANET + 1];
  $L2[LAST_PLANET + 2] = $longitude2a[LAST_PLANET + 10];


// no need to urlencode unless perhaps magic quotes is ON (??)
    $_SESSION['prog_syn_p1'] = $L1;
    $_SESSION['prog_syn_p2'] = $L2;

    $_SESSION['hc1'] = $hc1;
    $_SESSION['house_pos1'] = $house_pos1;
    $_SESSION['hc2'] = $hc2;
    $_SESSION['house_pos2_in_1'] = $house_pos2_in_1;


    $wheel_width = 800;
    $wheel_height = $wheel_width + 50;    //includes space at top of wheel for header


    echo "<center>";
    echo "<img border='0' src='prog_synastry_wheel.php?rx1=$rx1&rx2=$rx2&ubt1=$ubt1&ubt2=$ubt2&l1=$line1&l2=$line2' width='$wheel_width' height='$wheel_height'>";
    echo "<br><br>";
    echo "<img border='0' src='prog_synastry_aspect_grid.php?rx1=$rx1&rx2=$rx2&ubt1=$ubt1&ubt2=$ubt2' width='830' height='475'>";
    echo "</center>";
    echo "<br>";


// display all data - planets
    echo '<center><table width="65%" cellpadding="0" cellspacing="0" border="0">';

    echo '<tr>';
    echo "<td><font color='#0000ff'><b> Progressed </b></font></td>";
    echo "<td><font color='#0000ff'><b> Longitude </b></font></td>";
    if ($ubt1 == 1)
    {
      echo "<td>&nbsp;</td>";
    }
    else
    {
      echo "<td><font color='#0000ff'><b> House<br>position </b></font></td>";
    }
    echo "<td><font color='#0000ff'><b> Progressed </b></font></td>";
    echo "<td><font color='#0000ff'><b> Longitude </b></font></td>";
    if ($ubt2 == 1)
    {
      echo "<td>&nbsp;</td>";
    }
    else
    {
      echo "<td><font color='#0000ff'><b> House<br>position </b></font></td>";
    }
    echo '</tr>';

    if ($ubt1 == 1 Or $ubt2 == 1)
    {
      $a1 = SE_TNODE;
    }
    else
    {
      $a1 = LAST_PLANET - 1;
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

      echo "<td>" . $pl_name[$i] . "</td>";
      echo "<td><font face='Courier New'>" . Convert_Longitude($L2[$i]) . " " . Mid($rx2, $i + 1, 1) . "</font></td>";
      if ($ubt1 == 1)
      {
        echo "<td>&nbsp;</td>";
      }
      else
      {
        $hse = floor($house_pos2_in_1[$i]);
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


// display person #1 house cusps
    if ($ubt1 == 0)
    {
      echo '<tr>';
      echo "<td><font color='#0000ff'><b> Natal 1</b></font></td>";
      echo "<td><font color='#0000ff'><b> Longitude </b></font></td>";
    echo "<td>&nbsp;</td>";
      echo "<td><font color='#0000ff'><b> Natal 2</b></font></td>";
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
    echo "<td>&nbsp;</td>";

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

        if ($i == 1)
        {
          echo "<td><font face='Courier New'>" . Convert_Longitude($ASC_2) . "</font></td>";
    }
        elseif ($i == 10)
    {
      echo "<td><font face='Courier New'>" . Convert_Longitude($MC_2) . "</font></td>";
    }
    else
    {
          echo "<td><font face='Courier New'>" . Convert_Longitude($hc2[$i]) . "</font></td>";
      }
        echo '</tr>';
      }
    }

    echo '</table></center>';
    echo "<br /><br />";


// display progressed planet data - aspect table
    $asp_name[1] = "Conjunction";
    $asp_name[2] = "Opposition";
    $asp_name[3] = "Trine";
    $asp_name[4] = "Square";
    $asp_name[5] = "Quincunx";
    $asp_name[6] = "Sextile";

    echo '<center><table width="40%" cellpadding="0" cellspacing="0" border="0">';

    echo '<tr>';
    echo "<td><font color='#0000ff'><b> Progressed Planet 2</b></font></td>";
    echo "<td><font color='#0000ff'><b> Aspect </b></font></td>";
    echo "<td><font color='#0000ff'><b> Progressed Planet 1</b></font></td>";
    echo "<td><font color='#0000ff'><b> Orb </b></font></td>";
    echo '</tr>';

    // include Ascendant and MC
    $L1[LAST_PLANET + 1] = $hc1[1];
    $L1[LAST_PLANET + 2] = $hc1[10];

    $pl_name[LAST_PLANET + 1] = "Ascendant";
    $pl_name[LAST_PLANET + 2] = "Midheaven";

    for ($i = 0; $i <= LAST_PLANET + 2; $i++)
    {
      echo "<tr><td colspan='4'>&nbsp;</td></tr>";
      for ($j = 0; $j <= LAST_PLANET + 2; $j++)
      {
        if ($ubt1 == 1 And ($i > SE_TNODE Or $j > SE_TNODE))
        {
          continue;
        }

        $q = 0;
        $da = Abs($L2[$i] - $L1[$j]);

        if ($da > 180)
        {
          $da = 360 - $da;
        }

        $orb = 1;

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

        if ($q > 0 And $i != SE_VERTEX)
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

    include ('footer.html');
    exit();
  }
}


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

?>
