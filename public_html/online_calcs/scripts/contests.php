<?php
  include ('../accesscontrol.php');

  if ($is_logged_in == False) { exit(); }

  require_once ('../../../mysqli_connect_online_calcs_db_MYSQLI.php');
  require_once ('../../../my_functions_MYSQLI.php');

// check if the form has been submitted
  if (isset($_POST['submitted']))
  {
    $id1 = safeEscapeString($conn, $_POST["id1"]);
    $r1h = intval(safeEscapeString($conn, $_POST["r1h"]));
    $r2h = intval(safeEscapeString($conn, $_POST["r2h"]));
    
    if ($r1h < 1 Or $r1h > 12) { $r1h = 1; }
    if ($r2h < 1 Or $r2h > 12) { $r2h = 7; }

    $r1_txt = $r1h . "-ruler";
    $r2_txt = $r2h . "-ruler";

    if (!is_numeric($id1))
    {
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
      echo "<center><br><br>I cannot find this person in the database. Please try again.</center>";
      include ('footer.html');
      exit();
    }


      // get all variables from database
    $name = $row['name'];

    $month = $row['month'];
    $day = $row['day'];
    $year = $row['year'];

    $hour = $row['hour'];
    $minute = $row['minute'];
    $second = $row['second'];     //added 19 Mar 2010

    $timezone = $row['timezone'];

    $long_deg = $row['long_deg'];
    $long_min = $row['long_min'];
    $ew = $row['ew'];

    $lat_deg = $row['lat_deg'];
    $lat_min = $row['lat_min'];
    $ns = $row['ns'];

    $cityName = $row['city'];       //added 13 Oct 2009
    $countryName = $row['country'];       //added 06 May 2010

    include ('header_contest.html');        //here because of setting cookies above

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

        if ($my_error != "")
    {

    }
    else
    {
      // no errors in filling out form, so process form
      // calculate astronomic data
      $swephsrc = './sweph';    //sweph MUST be in a folder no less than at this level
      $sweph = './sweph';

      // Unset any variables not initialized elsewhere in the program
      unset($PATH,$out,$pl_name,$lng1,$house_pos);

      //assign data from database to local variables
      $inmonth = $month;
      $inday = $day;
      $inyear = $year;

      $_SESSION['inyear'] = $inyear;

      $inhours = $hour;
      $inmins = $minute;
      $insecs = $second;    //modified 19 Mar 2010

      $intz = $timezone;

      $my_longitude = $ew * ($long_deg + ($long_min / 60));
      $my_latitude = $ns * ($lat_deg + ($lat_min / 60));


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
      $h_sys = "r";

      exec ("swetest -edir$sweph -b$utdatenow -ut$utnow -p0123456tt -eswe -house$my_longitude,$my_latitude,$h_sys -flsj -g, -head", $out);

      // Each line of output data from swetest is exploded into array $row, giving these elements:
      // 0 = longitude
      // 1 = speed
      // 2 = house position
      // planets are index 0 - index (LAST_PLANET), house cusps are index (LAST_PLANET + 1) - (LAST_PLANET + 12)
      foreach ($out as $key => $line)
      {
        $row = explode(',',$line);
        $lng1[$key] = $row[0];
        $speed1[$key] = $row[1];
        $house_pos1[$key] = $row[2];
      };


      include("contests_constants.php");      // this is here because we must rename the planet names


      //calculate the South Node
      $lng1[SE_TSNODE] = $lng1[SE_TNNODE] + 180;
      if ($lng1[SE_TSNODE] >= 360) { $lng1[SE_TSNODE] = $lng1[SE_TSNODE] - 360; }


//get house positions of planets here
      for ($x = 1; $x <= 12; $x++)
      {
        for ($y = SE_SUN; $y <= LAST_PLANET; $y++)
        {
          $pl = $lng1[$y] + (1 / 36000);

          if ($x < 12 And $lng1[$x + LAST_PLANET] > $lng1[$x + LAST_PLANET + 1])
          {
            if (($pl >= $lng1[$x + LAST_PLANET] And $pl < 360) Or ($pl < $lng1[$x + LAST_PLANET + 1] And $pl >= 0))
            {
              $house_pos1[$y] = $x;

              continue;
            }
          }

          if ($x == 12 And ($lng1[$x + LAST_PLANET] > $lng1[LAST_PLANET + 1]))
          {
            if (($pl >= $lng1[$x + LAST_PLANET] And $pl < 360) Or ($pl < $lng1[LAST_PLANET + 1] And $pl >= 0))
            {
              $house_pos1[$y] = $x;
            }

            continue;
          }

          if (($pl >= $lng1[$x + LAST_PLANET]) And ($pl < $lng1[$x + LAST_PLANET + 1]) And ($x < 12))
          {
            $house_pos1[$y] = $x;

            continue;
          }

          if (($pl >= $lng1[$x + LAST_PLANET]) And ($pl < $lng1[LAST_PLANET + 1]) And ($x == 12))
          {
            $house_pos1[$y] = $x;
          }
        }
      }


//get house positions of planet antiscias here
      $antiscia1 = array();
      
      for ($x = 1; $x <= 12; $x++)
      {
        for ($y = SE_SUN; $y <= LAST_PLANET; $y++)
        {
          $pl = Crunch(180 - ($lng1[$y] + (1 / 36000)));
          $antiscia1[$y] = $pl;

          if ($x < 12 And $lng1[$x + LAST_PLANET] > $lng1[$x + LAST_PLANET + 1])
          {
            if (($pl >= $lng1[$x + LAST_PLANET] And $pl < 360) Or ($pl < $lng1[$x + LAST_PLANET + 1] And $pl >= 0))
            {
              $house_pos1_antiscia[$y] = $x;

              continue;
            }
          }

          if ($x == 12 And ($lng1[$x + LAST_PLANET] > $lng1[LAST_PLANET + 1]))
          {
            if (($pl >= $lng1[$x + LAST_PLANET] And $pl < 360) Or ($pl < $lng1[LAST_PLANET + 1] And $pl >= 0))
            {
              $house_pos1_antiscia[$y] = $x;
            }

            continue;
          }

          if (($pl >= $lng1[$x + LAST_PLANET]) And ($pl < $lng1[$x + LAST_PLANET + 1]) And ($x < 12))
          {
            $house_pos1_antiscia[$y] = $x;

            continue;
          }

          if (($pl >= $lng1[$x + LAST_PLANET]) And ($pl < $lng1[LAST_PLANET + 1]) And ($x == 12))
          {
            $house_pos1_antiscia[$y] = $x;
          }
        }
      }


//done with calculations
      $secs = "0";
      if ($timezone < 0)
      {
        $tz = $timezone;
      }
      else
      {
        $tz = "+" . $timezone;
      }

      $restored_name = stripslashes($name);

      echo "<center>";

      $house_system_name = "Regiomontanus";

      $line1 = $restored_name . ", - " . strftime("%A, %B %d, %Y", mktime($hour, $minute, $secs, $month, $day, $year));
      $line2 = strftime("%H:%M (time zone = $tz hours)", mktime($hour, $minute, $secs, $month, $day, $year));

      if ($countryName != "")
      {
        $line3 = $long_deg . $ew_txt . $long_min . ", " . $lat_deg . $ns_txt . $lat_min . " - " . $cityName . ", " . $countryName;
      }
      else
      {
        $line3 = $long_deg . $ew_txt . $long_min . ", " . $lat_deg . $ns_txt . $lat_min . " - " . $cityName;
      }

      $line4 = "House system = " . $house_system_name;

      echo "</center>";


      $rx1 = "";
      for ($i = 0; $i <= LAST_PLANET; $i++)
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

      for ($i = 1; $i <= 12; $i++) { $hc1[$i] = $lng1[LAST_PLANET + $i]; }

      $hc1[13] = $hc1[1];

// no need to urlencode unless perhaps magic quotes is ON (??)
      $_SESSION['longitude1'] = $lng1;
      $_SESSION['antiscia1'] = $antiscia1;
      $_SESSION['speed1'] = $speed1;
      $_SESSION['rx1'] = $rx1;
      $_SESSION['hc1'] = $hc1;
      $_SESSION['house_pos1'] = $house_pos1;


      $total_width = 792;         //added 10 for left and right margins - was 780 - was 700
      $wheel_width = 640;
      $extra_width_for_graphic_data_table = $total_width - $wheel_width;

      //calculate the height of the graphic, considering chart wheel, graphic data table, and 3-deg line
      $wheel_height = 640;
      $extra_height_for_graphic_data_table = 240;


//calculate how high our table must be
      $y_top_margin = 64;
      $wheel_height = $wheel_height + $extra_height_for_graphic_data_table + $y_top_margin;


      echo "<center>";
      echo "<br>";

      echo "<img border='0' src='contest_wheel.php?rx1=$rx1&l1=$line1&l2=$line2&l3=$line3&l4=$line4&eh=$extra_height_for_graphic_data_table&ew=$extra_width_for_graphic_data_table&ytm=$y_top_margin'>";
      echo "<br>";
      echo "<img border='0' src='contest_wheel - small longitude.php?rx1=$rx1'>";
      echo "<img border='0' src='contest_wheel - small antiscia.php?rx1=$rx1'>";

      echo "<br>";


      //$hash_x = md5($line1 . $line2 . $line3);
      //$_SESSION['hash_x'] = $hash_x;

      //$URL = "./downloads/" . $hash_x . ".png";
      //echo "<a href='$URL'>Click here</a> to generate a URL to this chart<br><br>";
      //echo "<br>";

      echo "</center>";



      //here I want to determine the dignities and debilities of the planets
      //my $dignity_debility[] array will use the following ocdes: +5 = exaltation, +4 = rulership, +3 = triplicity, +2 = term, +1 = face, 0 = peregrine, -1 = detriment, -2 = fall
      $dignity_debility = array(0, 0, 0, 0, 0, 0, 0);
      $detriment = array(0, 0, 0, 0, 0, 0, 0);
      $fall = array(0, 0, 0, 0, 0, 0, 0);

      for ($i = SE_SUN; $i <= SE_SATURN; $i++)
      {
        $sign_pos = floor($lng1[$i] / 30) + 1;
    
        //find ruler of this planet's sign
        if ($i == SE_MARS And ($sign_pos == 1 Or $sign_pos == 8))
        {
          $dignity_debility[SE_MARS] = 4;
        }
        elseif ($i == SE_VENUS And ($sign_pos == 2 Or $sign_pos == 7))
        {
          $dignity_debility[SE_VENUS] = 4;
        }
        elseif ($i == SE_MERCURY And ($sign_pos == 3 Or $sign_pos == 6))
        {
          $dignity_debility[SE_MERCURY] = 4;
        }
        elseif ($i == SE_MOON And $sign_pos == 4)
        {
          $dignity_debility[SE_MOON] = 4;
        }
        elseif ($i == SE_SUN And $sign_pos == 5)
        {
          $dignity_debility[SE_SUN] = 4;
        }
        elseif ($i == SE_JUPITER And ($sign_pos == 9 Or $sign_pos == 12))
        {
          $dignity_debility[SE_JUPITER] = 4;
        }
        elseif ($i == SE_SATURN And ($sign_pos == 10 Or $sign_pos == 11))
        {
          $dignity_debility[SE_SATURN] = 4;
        }


        //find exaltation of this planet's sign
        if ($i == SE_SUN And $sign_pos == 1)
        {
          $dignity_debility[SE_SUN] = 5;
        }
        elseif ($i == SE_MOON And $sign_pos == 2)
        {
          $dignity_debility[SE_MOON] = 5;
        }
        elseif ($i == SE_JUPITER And $sign_pos == 4)
        {
          $dignity_debility[SE_JUPITER] = 5;
        }
        elseif ($i == SE_MERCURY And $sign_pos == 6)
        {
          $dignity_debility[SE_MERCURY] = 5;
        }
        elseif ($i == SE_SATURN And $sign_pos == 7)
        {
          $dignity_debility[SE_SATURN] = 5;
        }
        elseif ($i == SE_MARS And $sign_pos == 10)
        {
          $dignity_debility[SE_MARS] = 5;
        }
        elseif ($i == SE_VENUS And $sign_pos == 12)
        {
          $dignity_debility[SE_VENUS] = 5;
        }


        //find triplicity of this planet's sign
        if ($house_pos1[SE_SUN] < 7)
        {
          //night chart
          if ($i == SE_JUPITER And ($sign_pos == 1 Or $sign_pos == 5 Or $sign_pos == 9))
          {
            $dignity_debility[SE_JUPITER] = 3;
          }
          elseif ($i == SE_MOON And ($sign_pos == 2 Or $sign_pos == 6 Or $sign_pos == 10))
          {
            $dignity_debility[SE_MOON] = 3;
          }
          elseif ($i == SE_MERCURY And ($sign_pos == 3 Or $sign_pos == 7 Or $sign_pos == 11))
          {
            $dignity_debility[SE_MERCURY] = 3;
          }
          elseif ($i == SE_MARS And ($sign_pos == 4 Or $sign_pos == 8 Or $sign_pos == 12))
          {
            $dignity_debility[SE_MARS] = 3;
          }
        }
        else
        {
          //day chart
          if ($i == SE_SUN And ($sign_pos == 1 Or $sign_pos == 5 Or $sign_pos == 9))
          {
            $dignity_debility[SE_SUN] = 3;
          }
          elseif ($i == SE_VENUS And ($sign_pos == 2 Or $sign_pos == 6 Or $sign_pos == 10))
          {
            $dignity_debility[SE_VENUS] = 3;
          }
          elseif ($i == SE_SATURN And ($sign_pos == 3 Or $sign_pos == 7 Or $sign_pos == 11))
          {
            $dignity_debility[SE_SATURN] = 3;
          }
          elseif ($i == SE_MARS And ($sign_pos == 4 Or $sign_pos == 8 Or $sign_pos == 12))
          {
            $dignity_debility[SE_MARS] = 3;
          }
        }


        //find term of this planet's sign
        if ($i == SE_JUPITER And $lng1[$i] >= 0 And $lng1[$i] < 6) { $dignity_debility[SE_JUPITER] = 2; }
        if ($i == SE_VENUS And $lng1[$i] >= 6 And $lng1[$i] < 14) { $dignity_debility[SE_VENUS] = 2; }
        if ($i == SE_MERCURY And $lng1[$i] >= 14 And $lng1[$i] < 21) { $dignity_debility[SE_MERCURY] = 2; }
        if ($i == SE_MARS And $lng1[$i] >= 21 And $lng1[$i] < 26) { $dignity_debility[SE_MARS] = 2; }
        if ($i == SE_SATURN And $lng1[$i] >= 26 And $lng1[$i] < 30) { $dignity_debility[SE_SATURN] = 2; }
        if ($i == SE_VENUS And $lng1[$i] >= 30 And $lng1[$i] < 38) { $dignity_debility[SE_VENUS] = 2; }
        if ($i == SE_MERCURY And $lng1[$i] >= 38 And $lng1[$i] < 45) { $dignity_debility[SE_MERCURY] = 2; }
        if ($i == SE_JUPITER And $lng1[$i] >= 45 And $lng1[$i] < 52) { $dignity_debility[SE_JUPITER] = 2; }
        if ($i == SE_SATURN And $lng1[$i] >= 52 And $lng1[$i] < 56) { $dignity_debility[SE_SATURN] = 2; }
        if ($i == SE_MARS And $lng1[$i] >= 56 And $lng1[$i] < 60) { $dignity_debility[SE_MARS] = 2; }
        if ($i == SE_MERCURY And $lng1[$i] >= 60 And $lng1[$i] < 67) { $dignity_debility[SE_MERCURY] = 2; }
        if ($i == SE_JUPITER And $lng1[$i] >= 67 And $lng1[$i] < 74) { $dignity_debility[SE_JUPITER] = 2; }
        if ($i == SE_VENUS And $lng1[$i] >= 74 And $lng1[$i] < 81) { $dignity_debility[SE_VENUS] = 2; }
        if ($i == SE_SATURN And $lng1[$i] >= 81 And $lng1[$i] < 85) { $dignity_debility[SE_SATURN] = 2; }
        if ($i == SE_MARS And $lng1[$i] >= 85 And $lng1[$i] < 90) { $dignity_debility[SE_MARS] = 2; }
        if ($i == SE_MARS And $lng1[$i] >= 90 And $lng1[$i] < 96) { $dignity_debility[SE_MARS] = 2; }
        if ($i == SE_JUPITER And $lng1[$i] >= 96 And $lng1[$i] < 103) { $dignity_debility[SE_JUPITER] = 2; }
        if ($i == SE_MERCURY And $lng1[$i] >= 103 And $lng1[$i] < 110) { $dignity_debility[SE_MERCURY] = 2; }
        if ($i == SE_VENUS And $lng1[$i] >= 110 And $lng1[$i] < 117) { $dignity_debility[SE_VENUS] = 2; }
        if ($i == SE_SATURN And $lng1[$i] >= 117 And $lng1[$i] < 120) { $dignity_debility[SE_SATURN] = 2; }
        if ($i == SE_SATURN And $lng1[$i] >= 120 And $lng1[$i] < 126) { $dignity_debility[SE_SATURN] = 2; }
        if ($i == SE_MERCURY And $lng1[$i] >= 126 And $lng1[$i] < 133) { $dignity_debility[SE_MERCURY] = 2; }
        if ($i == SE_VENUS And $lng1[$i] >= 133 And $lng1[$i] < 139) { $dignity_debility[SE_VENUS] = 2; }
        if ($i == SE_JUPITER And $lng1[$i] >= 139 And $lng1[$i] < 145) { $dignity_debility[SE_JUPITER] = 2; }
        if ($i == SE_MARS And $lng1[$i] >= 145 And $lng1[$i] < 150) { $dignity_debility[SE_MARS] = 2; }
        if ($i == SE_MERCURY And $lng1[$i] >= 150 And $lng1[$i] < 157) { $dignity_debility[SE_MERCURY] = 2; }
        if ($i == SE_VENUS And $lng1[$i] >= 157 And $lng1[$i] < 163) { $dignity_debility[SE_VENUS] = 2; }
        if ($i == SE_JUPITER And $lng1[$i] >= 163 And $lng1[$i] < 168) { $dignity_debility[SE_JUPITER] = 2; }
        if ($i == SE_SATURN And $lng1[$i] >= 168 And $lng1[$i] < 174) { $dignity_debility[SE_SATURN] = 2; }
        if ($i == SE_MARS And $lng1[$i] >= 174 And $lng1[$i] < 180) { $dignity_debility[SE_MARS] = 2; }
        if ($i == SE_SATURN And $lng1[$i] >= 180 And $lng1[$i] < 186) { $dignity_debility[SE_SATURN] = 2; }
        if ($i == SE_VENUS And $lng1[$i] >= 186 And $lng1[$i] < 191) { $dignity_debility[SE_VENUS] = 2; }
        if ($i == SE_JUPITER And $lng1[$i] >= 191 And $lng1[$i] < 199) { $dignity_debility[SE_JUPITER] = 2; }
        if ($i == SE_MERCURY And $lng1[$i] >= 199 And $lng1[$i] < 204) { $dignity_debility[SE_MERCURY] = 2; }
        if ($i == SE_MARS And $lng1[$i] >= 204 And $lng1[$i] < 210) { $dignity_debility[SE_MARS] = 2; }
        if ($i == SE_MARS And $lng1[$i] >= 210 And $lng1[$i] < 216) { $dignity_debility[SE_MARS] = 2; }
        if ($i == SE_JUPITER And $lng1[$i] >= 216 And $lng1[$i] < 224) { $dignity_debility[SE_JUPITER] = 2; }
        if ($i == SE_VENUS And $lng1[$i] >= 224 And $lng1[$i] < 231) { $dignity_debility[SE_VENUS] = 2; }
        if ($i == SE_MERCURY And $lng1[$i] >= 231 And $lng1[$i] < 237) { $dignity_debility[SE_MERCURY] = 2; }
        if ($i == SE_SATURN And $lng1[$i] >= 237 And $lng1[$i] < 240) { $dignity_debility[SE_SATURN] = 2; }
        if ($i == SE_JUPITER And $lng1[$i] >= 240 And $lng1[$i] < 248) { $dignity_debility[SE_JUPITER] = 2; }
        if ($i == SE_VENUS And $lng1[$i] >= 248 And $lng1[$i] < 254) { $dignity_debility[SE_VENUS] = 2; }
        if ($i == SE_MERCURY And $lng1[$i] >= 254 And $lng1[$i] < 259) { $dignity_debility[SE_MERCURY] = 2; }
        if ($i == SE_SATURN And $lng1[$i] >= 259 And $lng1[$i] < 265) { $dignity_debility[SE_SATURN] = 2; }
        if ($i == SE_MARS And $lng1[$i] >= 265 And $lng1[$i] < 270) { $dignity_debility[SE_MARS] = 2; }
        if ($i == SE_VENUS And $lng1[$i] >= 270 And $lng1[$i] < 276) { $dignity_debility[SE_VENUS] = 2; }
        if ($i == SE_MERCURY And $lng1[$i] >= 276 And $lng1[$i] < 282) { $dignity_debility[SE_MERCURY] = 2; }
        if ($i == SE_JUPITER And $lng1[$i] >= 282 And $lng1[$i] < 289) { $dignity_debility[SE_JUPITER] = 2; }
        if ($i == SE_MARS And $lng1[$i] >= 289 And $lng1[$i] < 295) { $dignity_debility[SE_MARS] = 2; }
        if ($i == SE_SATURN And $lng1[$i] >= 295 And $lng1[$i] < 300) { $dignity_debility[SE_SATURN] = 2; }
        if ($i == SE_SATURN And $lng1[$i] >= 300 And $lng1[$i] < 306) { $dignity_debility[SE_SATURN] = 2; }
        if ($i == SE_MERCURY And $lng1[$i] >= 306 And $lng1[$i] < 312) { $dignity_debility[SE_MERCURY] = 2; }
        if ($i == SE_VENUS And $lng1[$i] >= 312 And $lng1[$i] < 320) { $dignity_debility[SE_VENUS] = 2; }
        if ($i == SE_JUPITER And $lng1[$i] >= 320 And $lng1[$i] < 325) { $dignity_debility[SE_JUPITER] = 2; }
        if ($i == SE_MARS And $lng1[$i] >= 325 And $lng1[$i] < 330) { $dignity_debility[SE_MARS] = 2; }
        if ($i == SE_VENUS And $lng1[$i] >= 330 And $lng1[$i] < 338) { $dignity_debility[SE_VENUS] = 2; }
        if ($i == SE_JUPITER And $lng1[$i] >= 338 And $lng1[$i] < 344) { $dignity_debility[SE_JUPITER] = 2; }
        if ($i == SE_MERCURY And $lng1[$i] >= 344 And $lng1[$i] < 350) { $dignity_debility[SE_MERCURY] = 2; }
        if ($i == SE_MARS And $lng1[$i] >= 350 And $lng1[$i] < 356) { $dignity_debility[SE_MARS] = 2; }
        if ($i == SE_SATURN And $lng1[$i] >= 356 And $lng1[$i] < 360) { $dignity_debility[SE_SATURN] = 2; }



        //find face of this planet's sign
        $t = $lng1[$i];

        while ($t >= 70) { $t = $t - 70; }

        if ($i == SE_MARS And $t >= 0 And $t < 10)
        {
          $dignity_debility[SE_MARS] = 1;
        }
        elseif ($i == SE_SUN And $t >= 10 And $t < 20)
        {
          $dignity_debility[SE_SUN] = 1;
        }
        elseif ($i == SE_VENUS And $t >= 20 And $t < 30)
        {
          $dignity_debility[SE_VENUS] = 1;
        }
        elseif ($i == SE_MERCURY And $t >= 30 And $t < 40)
        {
          $dignity_debility[SE_MERCURY] = 1;
        }
        elseif ($i == SE_MOON And $t >= 40 And $t < 50)
        {
          $dignity_debility[SE_MOON] = 1;
        }
        elseif ($i == SE_SATURN And $t >= 50 And $t < 60)
        {
          $dignity_debility[SE_SATURN] = 1;
        }
        elseif ($i == SE_JUPITER And $t >= 60 And $t < 70)
        {
          $dignity_debility[SE_JUPITER] = 1;
        }


        //find detriment of this planet's sign
      if ($i == SE_MARS And ($sign_pos == 7 Or $sign_pos == 2))
      {
        $detriment[SE_MARS] = 1;
      }
      elseif ($i == SE_VENUS And ($sign_pos == 8 Or $sign_pos == 1))
      {
        $detriment[SE_VENUS] = 1;
      }
      elseif ($i == SE_MERCURY And ($sign_pos == 9 Or $sign_pos == 12))
      {
        $detriment[SE_MERCURY] = 1;
      }
      elseif ($i == SE_MOON And $sign_pos == 10)
      {
        $detriment[SE_MOON] = 1;
      }
      elseif ($i == SE_SUN And $sign_pos == 11)
      {
        $detriment[SE_SUN] = 1;
      }
      elseif ($i == SE_JUPITER And ($sign_pos == 3 Or $sign_pos == 6))
      {
        $detriment[SE_JUPITER] = 1;
      }
      elseif ($i == SE_SATURN And ($sign_pos == 4 Or $sign_pos == 5))
      {
        $detriment[SE_SATURN] = 1;
      }


        //find fall of this planet's sign
      if ($i == SE_SUN And $sign_pos == 7)
      {
        $fall[SE_SUN] = 1;
      }
      elseif ($i == SE_MOON And $sign_pos == 8)
      {
        $fall[SE_MOON] = 1;
      }
      elseif ($i == SE_JUPITER And $sign_pos == 10)
      {
        $fall[SE_JUPITER] = 1;
      }
      elseif ($i == SE_MERCURY And $sign_pos == 12)
      {
        $fall[SE_MERCURY] = 1;
      }
      elseif ($i == SE_SATURN And $sign_pos == 1)
      {
        $fall[SE_SATURN] = 1;
      }
      elseif ($i == SE_MARS And $sign_pos == 4)
      {
        $fall[SE_MARS] = 1;
      }
      elseif ($i == SE_VENUS And $sign_pos == 6)
      {
        $fall[SE_VENUS] = 1;
      }
      }



      //find the two ruling planets of specified houses
      $rulers = array();                //[1] is ruler of $r1h and [2] is ruler $r2h
  
      //process the specified first house-ruler
      $sign_pos = floor($hc1[$r1h] / 30) + 1;

      if ($sign_pos == 1 Or $sign_pos == 8)
      {
        $rulers[1] = SE_MARS;
      }
      elseif ($sign_pos == 2 Or $sign_pos == 7)
      {
        $rulers[1] = SE_VENUS;
      }
      elseif ($sign_pos == 3 Or $sign_pos == 6)
      {
        $rulers[1] = SE_MERCURY;
      }
      elseif ($sign_pos == 4)
      {
        $rulers[1] = SE_MOON;
      }
      elseif ($sign_pos == 5)
      {
      $rulers[1] = SE_SUN;
      }
      elseif ($sign_pos == 9 Or $sign_pos == 12)
      {
      $rulers[1] = SE_JUPITER;
      }
      elseif ($sign_pos == 10)
      {
      $rulers[1] = SE_SATURN;
      }
      elseif ($sign_pos == 11)
      {
      $rulers[1] = SE_SATURN;
      }

      
      //process the specified second house-ruler
      $sign_pos = floor($hc1[$r2h] / 30) + 1;

      if ($sign_pos == 1 Or $sign_pos == 8)
      {
        $rulers[2] = SE_MARS;
      }
      elseif ($sign_pos == 2 Or $sign_pos == 7)
      {
        $rulers[2] = SE_VENUS;
      }
      elseif ($sign_pos == 3 Or $sign_pos == 6)
      {
        $rulers[2] = SE_MERCURY;
      }
      elseif ($sign_pos == 4)
      {
        $rulers[2] = SE_MOON;
      }
      elseif ($sign_pos == 5)
      {
      $rulers[2] = SE_SUN;
      }
      elseif ($sign_pos == 9 Or $sign_pos == 12)
      {
      $rulers[2] = SE_JUPITER;
      }
      elseif ($sign_pos == 10)
      {
      $rulers[2] = SE_SATURN;
      }
      elseif ($sign_pos == 11)
      {
      $rulers[2] = SE_SATURN;
      }


      //get the sign the rulers are in
      $rulers_signs = array();
      $rulers_signs[1] = floor($lng1[$rulers[1]] / 30) + 1;
      $rulers_signs[2] = floor($lng1[$rulers[2]] / 30) + 1;


      // contest analysis header
      echo '<center><table width="73%" cellpadding="0" cellspacing="0" border="0">';
      echo '<tr><td>';

      echo "<table width='100%'><tr>";
      echo "<td width='140px'><b>The Significators:</b></td>";
      echo "<td><font color='#0000ff'><b>" . $pl_name[$rulers[1]] . "</b></font> is the ruler of house " . $r1h . check_for_Moon($rulers[1]) . ".</td></tr>";
      echo "<tr><td width='140px'>&nbsp;</td>";
      echo "<td><font color='#0000ff'><b>" . $pl_name[$rulers[2]] . "</b></font> is the ruler of house " . $r2h . check_for_Moon($rulers[2]) . ".</td></tr></table>";
      

//IF THE MOON IS A SIGNIFICATOR, REMIND THE USER TO CHECK THE AMOUNT OF LIGHT THE MOON HAS.


      echo "<br><center><table width='100%'><tr>";
  
      for ($i = SE_SUN; $i <= SE_SATURN; $i++)
      {
        $t = "";
    
        if ($dignity_debility[$i] == 5) { $t .= bold_if_significator($t, $i, $rulers, $pl_name) . ", exalted "; }
        if ($dignity_debility[$i] == 4) { $t .= bold_if_significator($t, $i, $rulers, $pl_name) . ", own sign "; }
        if ($dignity_debility[$i] == 3) { $t .= bold_if_significator($t, $i, $rulers, $pl_name) . ", own triplicity "; }
        if ($dignity_debility[$i] == 2) { $t .= bold_if_significator($t, $i, $rulers, $pl_name) . ", own term "; }
        if ($dignity_debility[$i] == 1) { $t .= bold_if_significator($t, $i, $rulers, $pl_name) . ", own face "; }
        if ($dignity_debility[$i] == 0) { $t .= bold_if_significator($t, $i, $rulers, $pl_name) . ", peregrine "; }
        if ($detriment[$i] == 1) { $t .= bold_if_significator($t, $i, $rulers, $pl_name) . ", detriment "; }
        if ($fall[$i] == 1) { $t .= bold_if_significator($t, $i, $rulers, $pl_name) . ", fall "; }
    
        echo "<td>" . $t . "</td>"; 
    
        if ($i == SE_VENUS) { echo "</tr><tr>"; }
      }

      echo "<td>&nbsp;</td></table></center>";
      echo "<br><br>";
      

      //see if $rulers[1] is conjunct North Node
      $txt = "<b>Is either significator conjunct a Node?</b><br>";
      $flag = False;

      $orb = 3;                         //not sure what the orb should be here
      $da = abs($lng1[$rulers[1]] - $lng1[SE_TNNODE]);
      if ($da > 180) { $da = 360 - $da; }
      if ($da <= $orb)
      {
        $flag = True;
        $txt .= "<font color='#ff0000'><b>Important!</b></font> " . $r1_txt . " is conjunct North Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }
      elseif ($da <= $orb + 2)
      {
        $flag = True;
        $txt .= "Semi-important, yet not close enough to be a major testimony. " . $r1_txt . " is conjunct North Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }

      //see if $rulers[1] is conjunct South Node
      $da = abs($lng1[$rulers[1]] - $lng1[SE_TSNODE]);
      if ($da > 180) { $da = 360 - $da; }
      if ($da <= $orb)
      {
        $flag = True;
        $txt .= "<font color='#ff0000'><b>Important!</b></font> " . $r1_txt . " is conjunct South Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }
      elseif ($da <= $orb + 2)
      {
        $flag = True;
        $txt .= "Semi-important, yet not close enough to be a major testimony. " . $r1_txt . " is conjunct South Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }
      
      //see if $rulers[2] is conjunct North Node
      $da = abs($lng1[$rulers[2]] - $lng1[SE_TNNODE]);
      if ($da > 180) { $da = 360 - $da; }
      if ($da <= $orb)
      {
        $flag = True;
        $txt .= "<font color='#ff0000'><b>Important!</b></font> " . $r2_txt . " is conjunct North Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }
      elseif ($da <= $orb + 2)
      {
        $flag = True;
        $txt .= "Semi-important, yet not close enough to be a major testimony. " . $r2_txt . " is conjunct North Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }

      //see if $rulers[2] is conjunct South Node
      $da = abs($lng1[$rulers[2]] - $lng1[SE_TSNODE]);
      if ($da > 180) { $da = 360 - $da; }
      if ($da <= $orb)
      {
        $flag = True;
        $txt .= "<font color='#ff0000'><b>Important!</b></font> " . $r2_txt . " is conjunct South Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }
      elseif ($da <= $orb + 2)
      {
        $flag = True;
        $txt .= "Semi-important, yet not close enough to be a major testimony. " . $r2_txt . " is conjunct South Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }

      if ($flag == True) { echo $txt . "<br><br>"; }
      

      //display a note here about house positions
      ?><a href="house_position_help.php" onClick="return popup(this, 'help4')"><img src='balloon help.png' alt=''></a>&nbsp;<?php
      echo "<b>HOUSE PLACEMENT DECIDES MORE CONTEST HORARIES THAN ANY OTHER TESTIMONY</b><br><br>";
      

      //check the positions of all the planets to see if any of them are within 5 degrees of the next house
      echo "<br><b>Note: planets that change house positions:</b><br>";
      $revised_house_pos1 = array();
      
      for ($i = SE_SUN; $i <= LAST_PLANET; $i++)
      {
        $revised_house_pos1[$i] = $house_pos1[$i];
        
        if ($house_pos1[$i] == 12)
        {
          if (($hc1[1] - $lng1[$i] >= 0 And $hc1[1] - $lng1[$i] <= 5) Or $hc1[1] - $lng1[$i] <= -355)
          {
            $revised_house_pos1[$i] = 1;

            //FOR DEBUG
            echo $pl_name[$i] . " is really in the NEXT house - in house 1, not in house 12.<br>";
          }
        }
        else
        {
          for ($j = 1; $j <= 11; $j++)
          {
            if ($house_pos1[$i] == $j)
            {
              if (($hc1[$j + 1] - $lng1[$i] >= 0 And $hc1[$j + 1] - $lng1[$i] <= 5) Or $hc1[$j + 1] - $lng1[$i] <= -355)
              {
                if (floor($hc1[$j + 1] / 30) != floor($lng1[$i] / 30)) { break; }   //planet and cusp must be in the same sign
                
                $revised_house_pos1[$i] = $j + 1;

                //FOR DEBUG
                echo $pl_name[$i] . " is really in the NEXT house - in house " . $revised_house_pos1[$i] . ", not in house " . $house_pos1[$i] . ".<br>";
                
                break;
              }
            }
          }
        }
      }


      //check the positions of all the planet antiscias to see if any of them are within 5 degrees of the next house
      echo "<br><b>Note: planet antiscias that change house positions:</b><br>";
      $revised_house_pos1_antiscia = array();
      
      for ($i = SE_SUN; $i <= LAST_PLANET; $i++)
      {
        $revised_house_pos1_antiscia[$i] = $house_pos1_antiscia[$i];
        
        if ($house_pos1_antiscia[$i] == 12)
        {
          if (($hc1[1] - $antiscia1[$i] >= 0 And $hc1[1] - $antiscia1[$i] <= 5) Or $hc1[1] - $antiscia1[$i] <= -355)
          {
            $revised_house_pos1_antiscia[$i] = 1;

            //FOR DEBUG
            echo "Antiscia of " . $pl_name[$i] . " is really in the NEXT house - in house 1, not in house 12.<br>";
          }
        }
        else
        {
          for ($j = 1; $j <= 11; $j++)
          {
            if ($house_pos1_antiscia[$i] == $j)
            {
              if (($hc1[$j + 1] - $antiscia1[$i] >= 0 And $hc1[$j + 1] - $antiscia1[$i] <= 5) Or $hc1[$j + 1] - $antiscia1[$i] <= -355)
              {
                if (floor($hc1[$j + 1] / 30) != floor($antiscia1[$i] / 30)) { break; }       //planet and cusp must be in the same sign
                
                $revised_house_pos1_antiscia[$i] = $j + 1;

                //FOR DEBUG
                echo "Antiscia of " . $pl_name[$i] . " is really in the NEXT house - in house " . $revised_house_pos1_antiscia[$i] . ", not in house " . $house_pos1_antiscia[$i] . ".<br>";
                
                break;
              }
            }
          }
        }
      }


      //now list the planets in house $r1h
      echo "<br><br><b>Planets in house " . $r1h . " = </b>";
      for ($i = SE_SUN; $i <= LAST_PLANET; $i++)
      {
        if ($revised_house_pos1[$i] == $r1h)
        {
          if (floor($lng1[$i] / 30) != floor($hc1[$r1h] / 30))
          {
            echo $pl_name[$i] . " (weaker)&nbsp;&nbsp;&nbsp;";
          }
          else
          {
            echo $pl_name[$i] . "&nbsp;&nbsp;&nbsp;";
          }
        }
      }


      //now list the planets in house $r2h
      echo "<br><b>Planets in house " . $r2h . " = </b>";
      for ($i = SE_SUN; $i <= LAST_PLANET; $i++)
      {
        if ($revised_house_pos1[$i] == $r2h)
        {
          if (floor($lng1[$i] / 30) != floor($hc1[$r2h] / 30))
          {
            echo $pl_name[$i] . " (weaker)&nbsp;&nbsp;&nbsp;";
          }
          else
          {
            echo $pl_name[$i] . "&nbsp;&nbsp;&nbsp;";
          }
        }
      }


      //now list the planet antiscias in house $r1h
      echo "<br><br><b>Planet antiscias in house " . $r1h . " = </b>";
      for ($i = SE_SUN; $i <= LAST_PLANET; $i++)
      {
        if ($revised_house_pos1_antiscia[$i] == $r1h)
        {
          if (floor($antiscia1[$i] / 30) != floor($hc1[$r1h] / 30))
          {
            echo $pl_name[$i] . " (weaker)&nbsp;&nbsp;&nbsp;";
          }
          else
          {
            echo $pl_name[$i] . " &nbsp;&nbsp;&nbsp;";
          }
        }
      }


      //now list the planet antiscias in house $r2h
      echo "<br><b>Planet antiscias in house " . $r2h . " = </b>";
      for ($i = SE_SUN; $i <= LAST_PLANET; $i++)
      {
        if ($revised_house_pos1_antiscia[$i] == $r2h)
        {
          if (floor($antiscia1[$i] / 30) != floor($hc1[$r2h] / 30))
          {
            echo $pl_name[$i] . " (weaker)&nbsp;&nbsp;&nbsp;";
          }
          else
          {
            echo $pl_name[$i] . " &nbsp;&nbsp;&nbsp;";
          }
        }
      }


      //list in what house ruler $r1h is located
      echo "<br><br><br>";
      
      $hse = $revised_house_pos1[$rulers[1]];
      
      //check if Ruler $r1h is in same sign as the cusp on the house it is in
      if ($hse == 1 Or $hse == 10 Or ($hse == 4 And $r1h != 10) Or ($hse == 7 And $r1h != 1))
      {
        echo "<b>" . $r1_txt . "</b> is in house " . $hse . " (<font color='#008000'><b>this is strong</b></font> - closer to the cusp is better).<br>";
      }
      elseif ($hse == 6 Or $hse == 8 Or $hse == 12)
      {
        echo "<b>" . $r1_txt . "</b> is in house " . $hse . " (this is weak).<br>";
      }
      elseif (($hse == 7 And $r1h == 1) Or ($hse == 4 And $r1h == 10))
      {
        if ($revised_house_pos1[$rulers[1]] == $house_pos1[$rulers[1]])
        {
          echo "<b>" . $r1_txt . "</b> is in the house of the enemy (<font color='#ff0000'><b>this is not good at all</b></font>)<br>";
        }
        else
        {
          //but it must be within 2 or 3 degrees of the cusp
          $da = abs($lng1[$rulers[1]] - $hc1[7]);
          
          if ($rulers_signs[1] == floor($hc1[7] / 30) + 1 And ($da <= 2.75 Or $da >= 357.25))
          {
            if ($speed1[$rulers[1]] > 0)
            {
              echo "<b>" . $r1_txt . "</b> is in the house of the enemy (<font color='#008000'><b>BUT CONTROLS THE HOUSE!</b></font>)<br>";
            }
            else
            {
              echo "<b>" . $r1_txt . "</b> is in the house of the enemy (BUT CONTROLS THE HOUSE! - yet because it is not direct, it loses some power)<br>";
            }
          }
          else
          {
            echo "<b>" . $r1_txt . "</b> is in the house of the enemy (<font color='#008000'><b>BUT CONTROLS THE HOUSE!</b></font>)<br>";
          }
        }
      }
      else
      {
        echo "<b>" . $r1_txt . "</b> is in house " . $hse . " (this is neutral).<br>";
      }
      
      if (floor($lng1[$rulers[1]] / 30) != floor($hc1[$hse] / 30)) { echo "<b>" . $r1_txt . "</b> is NOT in same sign as cusp of present house (this really weakens the influence).<br>"; }
      

      //list in what house ruler $r2h is located
      echo "<br>";

      $hse = $revised_house_pos1[$rulers[2]];
      
      //check if Ruler $r2h is in same sign as the cusp on the house it is in
      if ($hse == 4 Or $hse == 7 Or ($hse == 10 And $r2h != 4) Or ($hse == 1 And $r2h != 7))
      {
        echo "<b>" . $r2_txt . "</b> is in house " . $hse . " (<font color='#008000'><b>this is strong</b></font> - closer to the cusp is better).<br>";
      }
      elseif ($hse == 6 Or $hse == 8 Or $hse == 12)
      {
        echo "<b>" . $r2_txt . "</b> is in house " . $hse . " (this is weak).<br>";
      }
      elseif (($hse == 1 And $r2h == 7) Or ($hse == 10 And $r2h == 4))
      {
        if ($revised_house_pos1[$rulers[2]] == $house_pos1[$rulers[2]])
        {
          echo "<b>" . $r2_txt . "</b> is in the house of the enemy (<font color='#ff0000'><b>this is not good at all</b></font>)<br>";
        }
        else
        {
          //but it must be within 2 or 3 degrees of the cusp
          $da = abs($lng1[$rulers[2]] - $hc1[1]);
          
          if ($rulers_signs[2] == floor($hc1[1] / 30) + 1 And ($da <= 2.75 Or $da >= 357.25))
          {
            if ($speed1[$rulers[2]] > 0)
            {
              echo "<b>" . $r2_txt . "</b> is in the house of the enemy (<font color='#008000'><b>BUT CONTROLS THE HOUSE!</b></font>)<br>";
            }
            else
            {
              echo "<b>" . $r2_txt . "</b> is in the house of the enemy (BUT CONTROLS THE HOUSE! - yet because it is not direct, it loses some power)<br>";
            }
          }
          else
          {
            echo "<b>" . $r2_txt . "</b> is in the house of the enemy (<font color='#008000'><b>BUT CONTROLS THE HOUSE!</b></font>)<br>";
          }
        }
      }
      else
      {
        echo "<b>" . $r2_txt . "</b> is in house " . $hse . " (this is neutral).<br>";
      }

      if (floor($lng1[$rulers[2]] / 30) != floor($hc1[$hse] / 30)) { echo "<b>" . $r2_txt . "</b> is NOT in same sign as cusp of present house (this really weakens the influence).<br>"; }


      //list in what house ruler $r1h antiscia is located
      echo "<br>";
      
      $hse = $revised_house_pos1_antiscia[$rulers[1]];

      //check if Ruler $r1h antiscia is in same sign as the cusp on the house it is in
      if ($hse == 1 Or $hse == 10 Or ($hse == 4 And $r1h != 10) Or ($hse == 7 And $r1h != 1))
      {
        echo "<b>" . $r1_txt . " antiscia</b> is in house " . $hse . " (<font color='#008000'><b>this is strong</b></font> - closer to the cusp is better).<br>";
      }
      elseif ($hse == 6 Or $hse == 8 Or $hse == 12)
      {
        echo "<b>" . $r1_txt . " antiscia</b> is in house " . $hse . " (this is weak).<br>";
      }
      elseif (($hse == 7 And $r1h == 1) Or ($hse == 4 And $r1h == 10))
      {
        if ($revised_house_pos1_antiscia[$rulers[1]] == $house_pos1_antiscia[$rulers[1]])
        {
          echo "<b>" . $r1_txt . " antiscia</b> is in the house of the enemy (<font color='#ff0000'><b>this is not good at all</b></font>)<br>";
        }
        else
        {
          //but it must be within 2 or 3 degrees of the cusp
          $da = abs($antiscia1[$rulers[1]] - $hc1[7]);
          
          if ($rulers_signs[1] == floor($hc1[7] / 30) + 1 And ($da <= 2.75 Or $da >= 357.25))
          {
            if ($speed1[$rulers[1]] > 0)
            {
              echo "<b>" . $r1_txt . " antiscia</b> is in the house of the enemy (<font color='#008000'><b>BUT CONTROLS THE HOUSE!</b></font>)<br>";
            }
            else
            {
              echo "<b>" . $r1_txt . " antiscia</b> is in the house of the enemy (BUT CONTROLS THE HOUSE! - yet because it is not direct, it loses some power)<br>";
            }
          }
          else
          {
            echo "<b>" . $r1_txt . " antiscia</b> is in the house of the enemy (<font color='#008000'><b>BUT CONTROLS THE HOUSE!</b></font>)<br>";
          }
        }
      }
      else
      {
        echo "<b>" . $r1_txt . " antiscia</b> is in house " . $hse . " (this is neutral).<br>";
      }

      if (floor($antiscia1[$rulers[1]] / 30) != floor($hc1[$hse] / 30)) { echo "<b>" . $r1_txt . " antiscia </b> is NOT in same sign as cusp of present house (this really weakens the influence).<br>"; }


      //list in what house ruler $r2h antiscia is located
      echo "<br>";

      $hse = $revised_house_pos1_antiscia[$rulers[2]];

      //check if Ruler $r2h antiscia is in same sign as the cusp on the house it is in
      if ($hse == 4 Or $hse == 7 Or ($hse == 10 And $r2h != 4) Or ($hse == 1 And $r2h != 7))
      {
        echo "<b>" . $r2_txt . " antiscia</b> is in house " . $hse . " (<font color='#008000'><b>this is strong</b></font> - closer to the cusp is better).<br>";
      }
      elseif ($hse == 6 Or $hse == 8 Or $hse == 12)
      {
        echo "<b>" . $r2_txt . " antiscia</b> is in house " . $hse . " (this is weak).<br>";
      }
      elseif (($hse == 1 And $r2h == 7) Or ($hse == 10 And $r2h == 4))
      {
        if ($revised_house_pos1_antiscia[$rulers[2]] == $house_pos1_antiscia[$rulers[2]])
        {
          echo "<b>" . $r2_txt . " antiscia</b> is in the house of the enemy (<font color='#ff0000'><b>this is not good at all</b></font>)<br>";
        }
        else
        {
          //but it must be within 2 or 3 degrees of the cusp
          $da = abs($antiscia1[$rulers[2]] - $hc1[1]);
          
          if ($rulers_signs[2] == floor($hc1[1] / 30) + 1 And ($da <= 2.75 Or $da >= 357.25))
          {
            if ($speed1[$rulers[2]] > 0)
            {
              echo "<b>" . $r2_txt . " antiscia</b> is in the house of the enemy (<font color='#008000'><b>BUT CONTROLS THE HOUSE!</b></font>)<br>";
            }
            else
            {
              echo "<b>" . $r2_txt . " antiscia</b> is in the house of the enemy (BUT CONTROLS THE HOUSE! - yet because it is not direct, it loses some power)<br>";
            }
          }
          else
          {
            echo "<b>" . $r2_txt . " antiscia</b> is in the house of the enemy (<font color='#008000'><b>BUT CONTROLS THE HOUSE!</b></font>)<br>";
          }
        }
      }
      else
      {
        echo "<b>" . $r2_txt . " antiscia</b> is in house " . $hse . " (this is neutral).<br>";
      }

      if (floor($antiscia1[$rulers[2]] / 30) != floor($hc1[$hse] / 30)) { echo "<b>" . $r2_txt . " antiscia </b> is NOT in same sign as cusp of present house (this really weakens the influence).<br>"; }


      //now check each significator and their accidental dignity/debility
      //combust
      echo "<br><br>";
      ?><a href="dignity_help.php" onClick="return popup(this, 'help2')"><img src='balloon help.png' alt=''></a>&nbsp;<?php
      echo "<b>Combust Planets = </b>";

      for ($i = SE_MOON; $i <= SE_SATURN; $i++)
      {
        if (floor($lng1[SE_SUN] / 30) == floor($lng1[$i] / 30) And abs($lng1[SE_SUN] - $lng1[$i]) >= .29166667 And abs($lng1[SE_SUN] - $lng1[$i]) <= 8.5)
        {
          if ($i == $rulers[1])
          {
            echo $pl_name[$i] . " (" . $r1_txt . " - if in own sign or exaltation, then no problem)&nbsp;&nbsp;&nbsp;";
          }
          elseif ($i == $rulers[2])
          {
            echo $pl_name[$i] . " (" . $r2_txt . " - if in own sign or exaltation, then no problem)&nbsp;&nbsp;&nbsp;";
          }
          else
          {
            echo $pl_name[$i] . "&nbsp;&nbsp;&nbsp;";
          }
        }
      }


//LOOK INTO THIS LATER - IT HAS TO BE ADDRESSED, BUT I'M NOT SURE WHAT THE BEST WAY IS.
//Reception with the Sun can also be important when judging combustion. A planet combust in its own sign or exaltation is not debilitated at all. That works much like a mutual reception between the planet and the Sun. A planet combust in its own detriment or fall is — if this is possible — debilitated even more seriously than is usual with combustion. It is afflicted by a Sun which, as this negative reception shows, hates it. Bad news indeed!'


      //cazimi
      echo "<br><br><b>Cazimi Planets = </b>";

      for ($i = SE_MOON; $i <= SE_SATURN; $i++)
      {
        if (abs($lng1[SE_SUN] - $lng1[$i]) >= 0 And abs($lng1[SE_SUN] - $lng1[$i]) <= .29166667)
        {
          if ($i == $rulers[1])
          {
            echo $pl_name[$i] . " (" . $r1_txt . " - excellent!)&nbsp;&nbsp;&nbsp;";
          }
          elseif ($i == $rulers[2])
          {
            echo $pl_name[$i] . " (" . $r2_txt . " - excellent!)&nbsp;&nbsp;&nbsp;";
          }
          else
          {
            echo $pl_name[$i] . "&nbsp;&nbsp;&nbsp;";
          }
        }
      }


      //under sunbeams
      echo "<br><br><b>Under Sunbeams Planets = </b>";

      for ($i = SE_MOON; $i <= SE_SATURN; $i++)
      {
        if (floor($lng1[SE_SUN] / 30) != floor($lng1[$i] / 30) And abs($lng1[SE_SUN] - $lng1[$i]) >= .29166667 And abs($lng1[SE_SUN] - $lng1[$i]) <= 8.5)
        {
          if ($i == $rulers[1])
          {
            echo $pl_name[$i] . " (" . $r1_txt . ")&nbsp;&nbsp;&nbsp;";
          }
          elseif ($i == $rulers[2])
          {
            echo $pl_name[$i] . " (" . $r2_txt . ")&nbsp;&nbsp;&nbsp;";
          }
          else
          {
            echo $pl_name[$i] . "&nbsp;&nbsp;&nbsp;";
          }
        }
        elseif (abs($lng1[SE_SUN] - $lng1[$i]) >= 8.5 And abs($lng1[SE_SUN] - $lng1[$i]) <= 17.5)
        {
          if ($i == $rulers[1])
          {
            echo $pl_name[$i] . " (" . $r1_txt . ")&nbsp;&nbsp;&nbsp;";
          }
          elseif ($i == $rulers[2])
          {
            echo $pl_name[$i] . " (" . $r2_txt . ")&nbsp;&nbsp;&nbsp;";
          }
          else
          {
            echo $pl_name[$i] . "&nbsp;&nbsp;&nbsp;";
          }
        }
      }
      

      //check if either ruler is in the Via Combusta
      for ($i = 1; $i <= 2; $i++)
      {
        $significator = $rulers[$i];
        
        if ($i == 1)
        {
          if ($lng1[$significator] >= 195 And $lng1[$significator] < 225) { echo "<br><br>" . $r1_txt . " is in the Via Combusta (not good)"; }
        }
        else
        {
          if ($lng1[$significator] >= 195 And $lng1[$significator] < 225) { echo "<br><br>" . $r2_txt . " is in the Via Combusta (not good)"; }
        }
      }


      //display a note here about receptions
      echo "<br><br><br>";
      ?><a href="reception_help.php" onClick="return popup(this, 'help3')"><img src='balloon help.png' alt=''></a>&nbsp;<?php
      echo "<b>Use the table above to check receptions between significators. Click on the icon for more info.</b>";
      

      //display the aspects here - do each significator's aspects
      echo "<br><br><br>";
      ?><a href="significator_aspects_help.php" onClick="return popup(this, 'help1')"><img src='balloon help.png' alt=''></a>&nbsp;<?php
      echo "<b>Significator's Aspects</b> (see top left for applying/separating and various planetary dignities/debilities):<br><br>";
      
      for ($i = 1; $i <= 2; $i++)
      {
        $significator = $rulers[$i];
        $sign_of_significator = floor($lng1[$significator] / 30) + 1;
        
        if ($i == 1)
        {
          echo "" . $r1_txt . ":<br>";
        }
        else
        {
          echo "<br>" . $r2_txt . "<br>";
        }
        
        for ($j = SE_SUN; $j <= SE_SATURN; $j++)
        {
          if ($j == $significator) { continue; }                  //don't let a significator aspect itself
          //if ($significator == $rulers[1] And $j == $rulers[2]) { continue; }   //we don't care if both significators aspect each other (BUT NOT WITH CERTAIN QUESTIONS)
          //if ($significator == $rulers[2] And $j == $rulers[1]) { continue; }   //we don't care if both significators aspect each other (BUT NOT WITH CERTAIN QUESTIONS)
          
          $sign_of_p2 = floor($lng1[$j] / 30) + 1;
          
          $sign_diff =  abs($sign_of_significator - $sign_of_p2);
          
          $q = 0;
          $da = abs($lng1[$significator] - $lng1[$j]);

          if ($da > 180) { $da = 360 - $da; }

          $orb = 5;

          // is there an aspect within orb?
          //BUT . . . any legitimate aspect must NOT be over the sign line
          if ($da <= $orb And $sign_diff == 0)
          {
            $q = 1;
            $da = $da;
          }
          elseif (($da <= (60 + $orb)) And ($da >= (60 - $orb)) And ($sign_diff == 2 Or $sign_diff == 10))
          {
            $q = 5;
            $da = $da - 60;
          }
          elseif (($da <= (90 + $orb)) And ($da >= (90 - $orb)) And ($sign_diff == 3 Or $sign_diff == 9))
          {
            $q = 4;
            $da = $da - 90;
          }
          elseif (($da <= (120 + $orb)) And ($da >= (120 - $orb)) And ($sign_diff == 4 Or $sign_diff == 8))
          {
            $q = 3;
            $da = $da - 120;
          }


          if ($q > 0)
          {
            if ($da > 3)
            {
              echo $pl_name[$significator] . " " . $asp_name[$q] . " " . $pl_name[$j] . "&nbsp;&nbsp;&nbsp;" . sprintf("%.2f", abs($da)) . " - only a mild effect<br>";
            }
            else
            {
              echo $pl_name[$significator] . " " . $asp_name[$q] . " " . $pl_name[$j] . "&nbsp;&nbsp;&nbsp;" . sprintf("%.2f", abs($da)) . "<br>";
            }
          }


          if ($j == SE_SUN) { $orb = 8; }           //allow up to an 8-degree orb for any Sun opposition, if the Sun is NOT the significator

          if ($da >= (180 - $orb) And $sign_diff == 6)
          {
            $q = 2;
            $da = 180 - $da;
            
            if ($da > 3 And $orb == 5)
            {
              echo $pl_name[$significator] . " " . $asp_name[$q] . " " . $pl_name[$j] . "&nbsp;&nbsp;&nbsp;" . sprintf("%.2f", abs($da)) . " - only a mild effect<br>";
            }
            else
            {
              echo $pl_name[$significator] . " " . $asp_name[$q] . " " . $pl_name[$j] . "&nbsp;&nbsp;&nbsp;" . sprintf("%.2f", abs($da)) . "<br>";
            }
          }
        }
      }


      echo "<br><br><b>House of Joy</b> (where a planet is happy - being in that house strengthens it a bit):<br><br>";

      for ($i = SE_SUN; $i <= SE_SATURN; $i++)
      {
        if ($i == SE_SUN And $revised_house_pos1[$i] == 9) { echo "The Sun is in the 9th house - the house of its joy.<br>"; }
        if ($i == SE_MOON And $revised_house_pos1[$i] == 3) { echo "The Moon is in the 3rd house - the house of its joy.<br>"; }
        if ($i == SE_MERCURY And $revised_house_pos1[$i] == 1) { echo "Mercury is in the 1st house - the house of its joy.<br>"; }
        if ($i == SE_VENUS And $revised_house_pos1[$i] == 5) { echo "Venus is in the 5th house - the house of its joy.<br>"; }
        if ($i == SE_MARS And $revised_house_pos1[$i] == 6) { echo "Mars is in the 6th house - the house of its joy.<br>"; }
        if ($i == SE_JUPITER And $revised_house_pos1[$i] == 11) { echo "Jupiter is in the 11th house - the house of its joy.<br>"; }
        if ($i == SE_SATURN And $revised_house_pos1[$i] == 12) { echo "Saturn is in the 12th house - the house of its joy.<br>"; }
      }


      echo "<br><br>Check the relative speeds of the planets in the table below the chartwheel. Remember, Saturn doesn't like going fast. A stationary planet is very weak.";
      
      echo "<br><br>Worth noting, but not of major importance - retrogradation will add to an accumulation of weakening testimonies, or take the shine off a strong one. It won't decide a contest on its own.";



//THIS TIES IN WITH THE ABOVE, BUT IT WILL TAKE SOME THOUGHT TO FIGURE OUT HOW TO BEST IMPLEMENT IT.
//What about receptions? Do they affect the aspects? Yes. Suppose your significator is the Moon, which is trined by Jupiter in Cancer. This is a nice aspect, because Jupiter is in its exaltation: it has lots of dignity. It is even nicer because Jupiter is ruled by the Moon. Or suppose your Moon is aspected by Venus in Scorpio. This is a nasty aspect, because Venus is in its detriment, and is even nastier because Venus is in the fall of the Moon.

//Suppose my significator, the Moon, is aspected by Mars in Taurus. What then?' 'It is aspected by a nasty Mars - in its own detriment - who likes it lots, because it is in the exaltation of the Moon. This will be significantly less harmful than an aspect from nasty Mars would otherwise have been. If it was aspected by Mars in Scorpio, the aspect is from a nice Mars (lots of dignity) who hates the Moon (in the Moon's fall). This will be significantly less beneficial than an aspect from nice Mars would otherwise have been. But still better than any aspect from nasty Mars.

//It would be a rare chart that was decided on an aspect alone. There would have to be a terrible paucity of testimony for that to happen. So we don't need to be too subtle in our judgement of any individual aspect. It will either help a bit or harm a bit. Remember that this discussion on receptions applies only to contest horaries. Elsewhere, they become far more significant. The context here is so limiting that they work only like this.



//HERE WE HAVE TO USE 2 ARRAYS, ONE FOR NICE PLANETS AND ONE FOR NASTY PLANETS AND USE THESE TWO ARRAYS TO DETERMINE NEGATIVE AND POSITIVE BESIEGEMENT.
//IT"S NOT JUST MARS AND SATURN THAT CAN BESIEGE EITHER WAY.
//BUT THE BESIEGING PLANETS SHOULD BE NO MORE THAN 10 DEGREES APART!!
      echo "<br><br><br><b>Besieged Planets - NEGATIVE:</b>";

      for ($i = 1; $i <= 2; $i++)
      {
        $significator = $rulers[$i];
        
        if (floor($lng1[SE_MARS] / 30) == floor($lng1[SE_SATURN] / 30))   //Mars and Saturn must be in the same sign
        {
          if ($significator != SE_MARS And $significator != SE_SATURN)                    //skip Mars and Saturn
          {
            if ($lng1[SE_SATURN] - $lng1[SE_MARS] >= 0)
            {
              if ($lng1[$significator] >= $lng1[SE_MARS] And $lng1[$significator] <= $lng1[SE_SATURN]) { $txt .= $pl_name[$significator] . "   "; }
            }
            else
            {
              if ($lng1[$significator] >= $lng1[SE_SATURN] And $lng1[$significator] <= $lng1[SE_MARS]) { $txt .= $pl_name[$significator] . "   "; }
            }
          }
        }
      }

      //imagettftext($im, 9, 0, $x_text_start, $start_of_y_axis + (15 * $line_cntr), $black, 'arial.ttf', $txt);
  


      echo "<br><br><b>Besieged Planets - POSITIVE:</b>";

      for ($i = 1; $i <= 2; $i++)
      {
        $significator = $rulers[$i];
        
        if (floor($lng1[SE_MARS] / 30) == floor($lng1[SE_SATURN] / 30))   //Mars and Saturn must be in the same sign
        {
          if ($significator != SE_MARS And $significator != SE_SATURN)                    //skip Mars and Saturn
          {
            if ($lng1[SE_SATURN] - $lng1[SE_MARS] >= 0)
            {
              if ($lng1[$significator] >= $lng1[SE_MARS] And $lng1[$significator] <= $lng1[SE_SATURN]) { $txt .= $pl_name[$significator] . "   "; }
            }
            else
            {
              if ($lng1[$significator] >= $lng1[SE_SATURN] And $lng1[$significator] <= $lng1[SE_MARS]) { $txt .= $pl_name[$significator] . "   "; }
            }
          }
        }
      }

      //imagettftext($im, 9, 0, $x_text_start, $start_of_y_axis + (15 * $line_cntr), $black, 'arial.ttf', $txt);


      echo "<br><br><br>Having read and studied John Frawley's book 'Sports Astrology' (ISBN 978 0953977420), I find that it is nice to have all the pertinent data all in one place so I don't miss something important - I can then concentrate on analysis. The above data is the result of this desire. It is all the necessary information for any chart - now get to work figuring out who's gonna win.<br><br><br>";

      
//A PLANET CAN ALSO BE BESIEGED BY THE RAYS. IF IT CASTS ITS ASPECT INTO THE NARROW SPACE BETWEEN TWO NASTY PLANETS, THIS IS WEAKENING. IF BETWEEN TWO NICE PLANETS, IT IS HELPFUL. THE PLANETS MUST BE VERY CLOSE TOGETHER FOR THIS TO BE SIGNIFICANT IN CONTEST HORARIES: NO MORE THAN TWO DEGREES APART AT MOST. EVEN AT IT STRONGEST, IT IS A WEAKER TESTIMONY THAN BODILY BESIEGEMENT.  


//FIXED STARS PLAY ONLY A SMALL PART IN HORARY, AND THERE ARE ONLY THREE WHOSE EFFECT UPON A PLANET IS WORTH NOTING IN THESE CHARTS. REGULUS IS BY FAR THE MOST IMPORTANT OF THESE. A SIGNIFICATOR AT 28 OR 29 LEO IS ON REGULUS, AND IS GREATLY STRENGTHENED. SPICA, AT 23 LIBRA, OFFERS SOME PROTECTION AGAINST DEFEAT, BUT CAN BE OVERRULED BY STRONG TESTIMONY. ALGOL, AT 26 TAURUS, IS A NEGATIVE OF MODERATE STRENGTH. ALLOW A DEGREE OR SO OF ORB EITHER WAY, BUT NO MORE THAN THAT.' 


//IF THE MOON IS ONE OF OUR MAIN SIGNIFICATORS — LET ME STRESS THIS: I MEAN ONLY IF THE MOON IS ONE OF OUR MAIN SIGNIFICATORS - THERE ARE SOME OTHER POINTS WE MUST CONSIDER: THINGS THAT DON'T APPLY TO ANY OTHER PLANET, BUT ARE IMPORTANT WHEN ASSESSING THE MOON'S CONDITION. FIRST IS THE AMOUNT OF LIGHT IT HAS.' 'HOW DO I TELL THAT, MASTER?' 'THE NEARER THE MOON IS TO FULL, THE MORE LIGHT IT HAS; THE NEARER IT IS TO NEW, THE LESS IT HAS. FOR THESE CHARTS, WE CAN SAY THAT IF IT IS MORE THAN 120 DEGREES FROM THE SUN IT HAS LOTS OF LIGHT, SO IT'S STRONG. IF IT IS LESS THAN 60 DEGREES FROM THE SUN, IT HAS LITTLE LIGHT, SO IT'S MODERATELY WEAK. LESS THAN 30 DEGREES AND IT'S VERY WEAK.' 'DOES IT MATTER IF IT'S GAINING OR LOSING LIGHT?' 'NOT IN THESE QUESTIONS. 

//WHAT ABOUT IT DIRECTLY OPPOSING THE SUN? YOU SAID THAT WAS A MAJOR AFFLICTION.' 'GOOD! THAT IS AS TRUE OF THE MOON AS OF ANY OTHER PLANET. EVEN THOUGH IT HAS LOTS OF LIGHT THEN, WITHIN 8 DEGREES OF OPPOSITION TO THE SUN IT IS BADLY AFFLICTED. 'AND THE VIA COMBUSTA?' 'YES, IF THE MOON IS IN THE VIA COMBUSTA, WHICH IS BETWEEN 15 LIBRA AND 15 SCORPIO, IT IS SERIOUSLY AFFLICTED. IN MOST CASES WE WOULD READ THIS AS A GENERAL WEAKENING, BUT THE CONNECTION BETWEEN THE VIA COMBUSTA AND THE ANCIENT TABOOS AROUND THE ISSUE OF BLOOD CAN GIVE IT A SPECIAL SIGNIFICANCE IN CHARTS FOR BOXING MATCHES. 'SO IF WE KNOW THAT THE BOXER SIGNIFIED BY THE MOON IN THE VIA COMBUSTA HAS A TENDENCY TO CUT UP...' 'EXACTLY. NOW REMEMBER: THESE POINTS ABOUT THE MOON ARE RELEVANT ONLY IF THE MOON IS ONE OF OUR MAIN SIGNIFICATORS.

//SO WHAT DOES IT MEAN IN A CONTEST HORARY?' 'IF THE MOON IS ONE OF THE MAIN SIGNIFICATORS, IT MEANS NOTHING AT ALL. IGNORE IT. IF THE MOON IS NOT ONE OF THE MAIN SIGNIFICATORS, IT IS A MINOR TESTIMONY THAT THINGS MAY NOT FALL OUT AS THE QUERENT WISHES. THAT'S ALL. IT CAN EASILY BE OVERRULED.' 'THE VOID MOON IS ONE THAT MAKES NO FURTHER ASPECTS BEFORE LEAVING ITS PRESENT SIGN?' 'YES. THE MOON CAN BE VOID AT OTHER TIMES, BUT THEY NEED NOT CONCERN US HERE.


//IN CONTEST CHARTS A PLANET IN ITS OWN EXALTATION IS ESPECIALLY STRONG. THIS IS THE ONE ESSENTIAL FACTOR THAT CAN BE WEIGHED AGAINST THE MAJOR ACCIDENTAL DIGNITIES. IF A PLANET EXALTS ITSELF, WHAT THAT PLANET SIGNIFIES WILL BE FULL OF CONFIDENCE. CONFIDENCE DOESN'T GUARANTEE VICTORY, OF COURSE, BUT IT DOES MAKE IT MORE LIKELY.

//SO IF MY PLANET IS IN ITS EXALTATION AND YOURS IS IN ITS OWN SIGN, MY PLANET IS MUCH STRONGER THAN YOURS.' 'A MOST UNLIKELY CIRCUMSTANCE, MY BOY, BUT YES, THAT WOULD BE SO.


//RECEPTIONS TOO HAVE A LIMITED APPLICATION IN CONTEST HORARIES.' 'SO IF MY PLANET IS IN A SIGN RULED BY YOUR PLANET, MY TEAM LIKES YOUR TEAM?' THAT BROUGHT HIM A SMART TAP ON THE HEAD. 'NO, BOY. WHO LIKES WHOM IS HARDLY RELEVANT IN THESE CHARTS! WHAT CONCERNS US IS WHO HAS POWER OVER WHOM. IF YOUR PLANET IS IN A SIGN RULED BY MY PLANET, YOUR TEAM IS IN MY TEAM'S POWER. TO SOME EXTENT. AS WITH ESSENTIAL DIGNITY, THIS CAN TIP A CHART THAT IS OTHERWISE EVEN, BUT IS NOT USUALLY A POWERFUL THING IN ITSELF - EXCEPT WHEN THAT RECEPTION IS BY EXALTATION.' 

//IF MY TEAM EXALTS YOUR TEAM WE THINK YOUR TEAM IS WONDERFUL.' 'THAT'S EXACTLY IT. YOU THINK YOU'RE DESTINED TO LOSE. THIS DOESN'T MEAN THAT YOU WILL LOSE, BUT IT DOES MAKE IT MORE LIKELY.' 'AND IF MY PLANET IS RULED BY YOUR PLANET...' 'IT WILL BE IN ITS OWN DETRIMENT. BE CAREFUL WITH THAT ONE. DON'T COUNT THIS AS TWO SEPARATE TESTIMONIES. IF LORD 1 IS IN A SIGN RULED BY LORD 7, IT WILL ALWAYS BE IN ITS DETRIMENT. OR VICE VERSA. WE CAN IGNORE THE RECEPTION AND JUST SAY IT'S DEBILITATED. SIMILARLY IF LORD 1 IS JUST INSIDE THE 7TH HOUSE (OR VICE VERSA): IT IS IN THE ENEMY'S HOUSE. IT WILL ALSO BE IN ITS DETRIMENT. THIS IS ONE TESTIMONY, NOT TWO.' 'WHAT ABOUT OTHER RECEPTIONS?' 'IF MY PLANET IS IN THE TRIPLICITY RULED BY YOUR PLANET, THAT IS A MINOR TESTIMONY IN YOUR FAVOUR. RECEPTION BY TERM, FACE, DETRIMENT AND FALL CAN BE IGNORED.

//AND WHAT ABOUT MUTUAL RECEPTION WITH OTHER PLANETS? THAT MUST BE HELPFUL.' 'NO, IT ISN'T. YOU'RE THINKING IN ABSTRACTIONS, TAKING A LITTLE FORMULA: MUTUAL RECEPTION = HELPFUL. DON'T. YOU MUST THINK ABOUT WHAT THESE THINGS MEAN. MUTUAL RECEPTION IS LIKE FRIENDSHIP: THE TWO PLANETS WANT TO HELP EACH OTHER. IN A COURT-CASE HORARY, WHICH HAS SOME SIMILARITIES TO A CONTEST HORARY, THAT MAY MAKE SENSE. IF MY PLANET IS IN MUTUAL RECEPTION WITH ANOTHER PLANET, THAT OTHER PLANET COULD SIGNIFY MY WITNESSES — SOMEBODY ELSE WHO IS HELPING ME. IN A CONTEST, THERE ISN'T ANYBODY ELSE: IT'S JUST ME AND THE ENEMY. M Y FRIEND MAY WISH ME WELL, BUT HE ISN'T GOING TO RUN ONTO THE FIELD AND SCORE A HOME RUN FOR ME.'

//SO IT IS IMPOSSIBLE TO FIX A NUMERICAL VALUE FOR ANY DIGNITY OR DEBILITY.

//SUPPOSE MY SIGNIFICATOR IS IN ITS OWN SIGN WHILE MY OPPONENT'S IS IN ITS DETRIMENT OR IS PEREGRINE: THAT WOULD GIVE MY TEAM AN ADVANTAGE. BUT IF MY OPPONENT IS ACCIDENTALLY MUCH STRONGER, HIS TEAM WILL STILL WIN, EVEN IF I HAVE THE ADVANTAGE ESSENTIALLY.


//HORARY CHECKLIST 
//Cast chart for time and place of a question.
//Regiomontanus houses. 
//Seven planets only. 
//Major aspects only. 
//Select the appropriate houses. 
//Check the condition of these houses. 
//Locate the house rulers. 
//Check: are they combust, under the sunbeams, cazimi, opposing the Sun? 
//house placement 
//are they conjunct the Nodes? 
//speed and direction 
//aspects 
//are they besieged? 
//are they on Regulus, Spica or Algol? 
//are they in their joy? 
//essential dignity/debility 
//receptions between the rulers: does one dominate the other? 
//if the Moon is one of the house rulers, how much light does she have? is she in the via combusta? 



  






    echo '</td></tr></table></center><br><br>';
      //end of contest analysis
    }
  }

  include ('footer.html');
  exit();


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

  if ($deg < 10) { $deg = "0" . $deg; }

  if ($min < 10) { $min = "0" . $min; }

  if ($full_sec < 10) { $full_sec = "0" . $full_sec; }

  //return $deg . " " . $signs[$sign_num] . " " . $min . "' " . $full_sec . chr(34);
  return $signs[$sign_num] . " " . $deg . "° " . $min . "' " . $full_sec . chr(34);
}


Function mid($midstring, $midstart, $midlength)
{
  return(substr($midstring, $midstart-1, $midlength));
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


Function bold_if_significator($t, $i, $rulers, $pl_name)
{
  if ($t != "") { return ""; }
  
  if ($i == $rulers[1] Or $i == $rulers[2])
  {
    $t = "<b>" . $pl_name[$i] . "</b>";
  }
  else
  {
    $t = $pl_name[$i];
  }
  
  return $t;
}

Function check_for_Moon($p_num)
{
  if ($p_num == 1) { return " (check the Moon for the amount of light it has - more is better)"; }
}

?>