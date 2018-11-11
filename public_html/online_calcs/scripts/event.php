<?php
  include ('../accesscontrol.php');

  if ($is_logged_in == False) { exit(); }

  require_once ('../../../mysqli_connect_online_calcs_db_MYSQLI.php');
  require_once ('../../../my_functions_MYSQLI.php');

// check if the form has been submitted
  if (isset($_POST['submitted']))
  {
    $id1 = safeEscapeString($conn, $_POST["id1"]);

    $houses_to_check = array();

    $houses_to_check[1] = 1;
    $houses_to_check[2] = 7;
    $houses_to_check[3] = 10;
    $houses_to_check[4] = 4;


    $house_ruler_text = array();

    $house_ruler_text[1] = $houses_to_check[1] . "-ruler";
    $house_ruler_text[2] = $houses_to_check[2] . "-ruler";
    $house_ruler_text[3] = $houses_to_check[3] . "-ruler";
    $house_ruler_text[4] = $houses_to_check[4] . "-ruler";
    $house_ruler_text[5] = "Part of Fortune dispositor";


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

    include ('header_event.html');        //here because of setting cookies above

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
      $h_sys = "p";

      exec ("swetest -edir$sweph -b$utdatenow -ut$utnow -p0123456789tttt -eswe -house$my_longitude,$my_latitude,$h_sys -flsj -g, -head", $out);

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


      include("event_constants.php");      // this is here because we must rename the planet names


      //calculate the South Node
      $lng1[SE_TSNODE] = $lng1[SE_TNNODE] + 180;
      if ($lng1[SE_TSNODE] >= 360) { $lng1[SE_TSNODE] = $lng1[SE_TSNODE] - 360; }

      //calculate the Part of Fortune
      $lng1[SE_POF] = $lng1[LAST_PLANET + 1] + $lng1[SE_MOON] - $lng1[SE_SUN];
      if ($lng1[SE_POF] >= 360) { $lng1[SE_POF] = $lng1[SE_POF] - 360; }
      if ($lng1[SE_POF] < 0) { $lng1[SE_POF] = $lng1[SE_POF] + 360; }

      //calculate the antiscion of the Part of Fortune
      $lng1[SE_SP_POF] = Crunch(180 - $lng1[SE_POF]);


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


//get house positions of planet antiscia here
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

      $house_system_name = "Placidus";

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
          if ($i == SE_POF Or $i == SE_SP_POF)
          {
            $rx1 .= " ";
          }
          else
          {
            $rx1 .= "R";
          }
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
      $extra_height_for_graphic_data_table = 240 + 100;


//calculate how high our table must be
      $y_top_margin = 64;
      $wheel_height = $wheel_height + $extra_height_for_graphic_data_table + $y_top_margin;


      echo "<center>";
      echo "<br>";

      echo "<img border='0' src='event_wheel.php?rx1=$rx1&l1=$line1&l2=$line2&l3=$line3&l4=$line4&eh=$extra_height_for_graphic_data_table&ew=$extra_width_for_graphic_data_table&ytm=$y_top_margin'>";
      echo "<br>";


      //display 30-degree line
      $tempXX = $lng1[LAST_PLANET + 2];

      $lng1[LAST_PLANET + 2] = $hc1[10];
      $_SESSION['deg_line_event_longitude1'] = $lng1;

      $L2_height = Detect_num_same_deg_planets_max($lng1);
      $L2_height = ($L2_height + 1) * 50;

      echo "30-degree line (red planets indicate Rx)<br>";
      echo "<img border='0' src='deg_line_event.php?rx1=$rx1' width='740' height='$L2_height'>";

      $lng1[LAST_PLANET + 2] = $tempXX;

      echo "<br><br>";


      //display 2 small wheels
      echo "<img border='0' src='event_wheel - small longitude.php?rx1=$rx1'>";
      echo "<img border='0' src='event_wheel - small antiscia.php?rx1=$rx1'>";

      echo "<br>";

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



      //find the dispositore of the Moon
      $sign_pos = floor($lng1[SE_MOON] / 30) + 1;

      if ($sign_pos == 1 Or $sign_pos == 8)
      {
        $moon_dispositor = SE_MARS;
      }
      elseif ($sign_pos == 2 Or $sign_pos == 7)
      {
        $moon_dispositor = SE_VENUS;
      }
      elseif ($sign_pos == 3 Or $sign_pos == 6)
      {
        $moon_dispositor = SE_MERCURY;
      }
      elseif ($sign_pos == 4)
      {
        $moon_dispositor = SE_MOON;
      }
      elseif ($sign_pos == 5)
      {
        $moon_dispositor = SE_SUN;
      }
      elseif ($sign_pos == 9 Or $sign_pos == 12)
      {
        $moon_dispositor = SE_JUPITER;
      }
      elseif ($sign_pos == 10)
      {
        $moon_dispositor = SE_SATURN;
      }
      elseif ($sign_pos == 11)
      {
        $moon_dispositor = SE_SATURN;
      }


      //find the two ruling planets of specified houses
      $rulers = array();                //[1] is ruler of $houses_to_check[1], [2] is ruler $houses_to_check[2], [3] is ruler $houses_to_check[3], [4] is ruler $houses_to_check[4]

      //process the specified first house-ruler
      $sign_pos = floor($hc1[$houses_to_check[1]] / 30) + 1;

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
      $sign_pos = floor($hc1[$houses_to_check[2]] / 30) + 1;

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


      if ($rulers[1] == SE_MOON And $moon_dispositor != SE_SATURN) { $rulers[1] = $moon_dispositor; }

      if ($rulers[2] == SE_MOON And $moon_dispositor != SE_SATURN) { $rulers[2] = $moon_dispositor; }


      //process the 10th house ruler
      $sign_pos = floor($hc1[$houses_to_check[3]] / 30) + 1;

      if ($sign_pos == 1 Or $sign_pos == 8)
      {
        $rulers[3] = SE_MARS;
      }
      elseif ($sign_pos == 2 Or $sign_pos == 7)
      {
        $rulers[3] = SE_VENUS;
      }
      elseif ($sign_pos == 3 Or $sign_pos == 6)
      {
        $rulers[3] = SE_MERCURY;
      }
      elseif ($sign_pos == 4)
      {
        $rulers[3] = SE_MOON;
      }
      elseif ($sign_pos == 5)
      {
        $rulers[3] = SE_SUN;
      }
      elseif ($sign_pos == 9 Or $sign_pos == 12)
      {
        $rulers[3] = SE_JUPITER;
      }
      elseif ($sign_pos == 10)
      {
        $rulers[3] = SE_SATURN;
      }
      elseif ($sign_pos == 11)
      {
        $rulers[3] = SE_SATURN;
      }


      //process the 4th house ruler
      $sign_pos = floor($hc1[$houses_to_check[4]] / 30) + 1;

      if ($sign_pos == 1 Or $sign_pos == 8)
      {
        $rulers[4] = SE_MARS;
      }
      elseif ($sign_pos == 2 Or $sign_pos == 7)
      {
        $rulers[4] = SE_VENUS;
      }
      elseif ($sign_pos == 3 Or $sign_pos == 6)
      {
        $rulers[4] = SE_MERCURY;
      }
      elseif ($sign_pos == 4)
      {
        $rulers[4] = SE_MOON;
      }
      elseif ($sign_pos == 5)
      {
        $rulers[4] = SE_SUN;
      }
      elseif ($sign_pos == 9 Or $sign_pos == 12)
      {
        $rulers[4] = SE_JUPITER;
      }
      elseif ($sign_pos == 10)
      {
        $rulers[4] = SE_SATURN;
      }
      elseif ($sign_pos == 11)
      {
        $rulers[4] = SE_SATURN;
      }


      //process the dispositor of the Part of Fortune
      $sign_pos = floor($lng1[SE_POF] / 30) + 1;

      if ($sign_pos == 1 Or $sign_pos == 8)
      {
        $rulers[5] = SE_MARS;
      }
      elseif ($sign_pos == 2 Or $sign_pos == 7)
      {
        $rulers[5] = SE_VENUS;
      }
      elseif ($sign_pos == 3 Or $sign_pos == 6)
      {
        $rulers[5] = SE_MERCURY;
      }
      elseif ($sign_pos == 4)
      {
        $rulers[5] = SE_MOON;
      }
      elseif ($sign_pos == 5)
      {
        $rulers[5] = SE_SUN;
      }
      elseif ($sign_pos == 9 Or $sign_pos == 12)
      {
        $rulers[5] = SE_JUPITER;
      }
      elseif ($sign_pos == 10)
      {
        $rulers[5] = SE_SATURN;
      }
      elseif ($sign_pos == 11)
      {
        $rulers[5] = SE_SATURN;
      }


      //get the sign the rulers are in
      $rulers_signs = array();
      $rulers_signs[1] = floor($lng1[$rulers[1]] / 30) + 1;
      $rulers_signs[2] = floor($lng1[$rulers[2]] / 30) + 1;
      $rulers_signs[3] = floor($lng1[$rulers[3]] / 30) + 1;
      $rulers_signs[4] = floor($lng1[$rulers[4]] / 30) + 1;
      $rulers_signs[5] = floor($lng1[$rulers[5]] / 30) + 1;


      //tell the user if Cancer rises or sets
      echo "<center>";

      $sign_pos = intval(floor($hc1[1] / 30) + 1);

      if ($sign_pos == 4 And $moon_dispositor != SE_SATURN)
      {
        echo "<font color='#ff0000'><b>Cancer is rising, so I have made 1-ruler the Moon's dispositor</b></font><br><br>";
      }

      $sign_pos = floor($hc1[7] / 30) + 1;

      if ($sign_pos == 4 And $moon_dispositor != SE_SATURN)
      {
        echo "<font color='#ff0000'><b>Cancer is setting, so I have made 7-ruler the Moon's dispositor</b></font><br><br>";
      }

      echo "</center>";


      // event analysis header
      echo '<center><table width="73%" cellpadding="0" cellspacing="0" border="0">';
      echo '<tr><td>';

      echo "<table width='100%'><tr>";
      echo "<td width='140px'><b>The Significators:</b></td>";
      echo "<td><font color='#0000ff'><b>" . $pl_name[$rulers[1]] . "</b></font> is the ruler of house " . $houses_to_check[1] . check_for_Moon($rulers[1]) . ".</td></tr>";
      echo "<tr><td width='140px'>&nbsp;</td>";
      echo "<td><font color='#0000ff'><b>" . $pl_name[$rulers[2]] . "</b></font> is the ruler of house " . $houses_to_check[2] . check_for_Moon($rulers[2]) . ".</td></tr>";

      echo "<tr><td width='140px'>&nbsp;</td>";
      echo "<td><font color='#0000ff'><b>" . $pl_name[$rulers[3]] . "</b></font> is the ruler of house " . $houses_to_check[3] . check_for_Moon($rulers[3]) . ".</td></tr>";
      echo "<tr><td width='140px'>&nbsp;</td>";
      echo "<td><font color='#0000ff'><b>" . $pl_name[$rulers[4]] . "</b></font> is the ruler of house " . $houses_to_check[4] . check_for_Moon($rulers[4]) . ".</td></tr>";

      echo "<tr><td width='140px'>&nbsp;</td>";
      echo "<td><font color='#0000ff'><b>" . $pl_name[$rulers[5]] . "</b></font> disposits the Part of Fortune" . check_for_Moon($rulers[5]) . ".</td></tr></table>";


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
      $txt = "<b>Is either significator conjunct a Node?</b><br><br>";
      $flag = False;

      $orb = 3;                         //not sure what the orb should be here
      $da = abs($lng1[$rulers[1]] - $lng1[SE_TNNODE]);
      if ($da > 180) { $da = 360 - $da; }
      if ($da <= $orb)
      {
        $flag = True;
        $txt .= "<font color='#ff0000'><b>Important!</b></font> " . $house_ruler_text[1] . " is conjunct North Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }
      elseif ($da <= $orb + 2)
      {
        $flag = True;
        $txt .= "Semi-important, yet not close enough to be a major testimony: " . $house_ruler_text[1] . " is conjunct North Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }


      //see if $rulers[1] is conjunct South Node
      $da = abs($lng1[$rulers[1]] - $lng1[SE_TSNODE]);
      if ($da > 180) { $da = 360 - $da; }
      if ($da <= $orb)
      {
        $flag = True;
        $txt .= "<font color='#ff0000'><b>Important!</b></font> " . $house_ruler_text[1] . " is conjunct South Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }
      elseif ($da <= $orb + 2)
      {
        $flag = True;
        $txt .= "Semi-important, yet not close enough to be a major testimony: " . $house_ruler_text[1] . " is conjunct South Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }


      //see if $rulers[2] is conjunct North Node
      $da = abs($lng1[$rulers[2]] - $lng1[SE_TNNODE]);
      if ($da > 180) { $da = 360 - $da; }
      if ($da <= $orb)
      {
        $flag = True;
        $txt .= "<font color='#ff0000'><b>Important!</b></font> " . $house_ruler_text[2] . " is conjunct North Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }
      elseif ($da <= $orb + 2)
      {
        $flag = True;
        $txt .= "Semi-important, yet not close enough to be a major testimony: " . $house_ruler_text[2] . " is conjunct North Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }

      //see if $rulers[2] is conjunct South Node
      $da = abs($lng1[$rulers[2]] - $lng1[SE_TSNODE]);
      if ($da > 180) { $da = 360 - $da; }
      if ($da <= $orb)
      {
        $flag = True;
        $txt .= "<font color='#ff0000'><b>Important!</b></font> " . $house_ruler_text[2] . " is conjunct South Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }
      elseif ($da <= $orb + 2)
      {
        $flag = True;
        $txt .= "Semi-important, yet not close enough to be a major testimony: " . $house_ruler_text[2] . " is conjunct South Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }


      //see if $rulers[3] is conjunct North Node
      $da = abs($lng1[$rulers[3]] - $lng1[SE_TNNODE]);
      if ($da > 180) { $da = 360 - $da; }
      if ($da <= $orb)
      {
        $flag = True;
        $txt .= "<font color='#ff0000'><b>Important!</b></font> " . $house_ruler_text[3] . " is conjunct North Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }
      elseif ($da <= $orb + 2)
      {
        $flag = True;
        $txt .= "Semi-important, yet not close enough to be a major testimony: " . $house_ruler_text[3] . " is conjunct North Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }


      //see if $rulers[3] is conjunct South Node
      $da = abs($lng1[$rulers[3]] - $lng1[SE_TSNODE]);
      if ($da > 180) { $da = 360 - $da; }
      if ($da <= $orb)
      {
        $flag = True;
        $txt .= "<font color='#ff0000'><b>Important!</b></font> " . $house_ruler_text[3] . " is conjunct South Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }
      elseif ($da <= $orb + 2)
      {
        $flag = True;
        $txt .= "Semi-important, yet not close enough to be a major testimony: " . $house_ruler_text[3] . " is conjunct South Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }


      //see if $rulers[4] is conjunct North Node
      $da = abs($lng1[$rulers[4]] - $lng1[SE_TNNODE]);
      if ($da > 180) { $da = 360 - $da; }
      if ($da <= $orb)
      {
        $flag = True;
        $txt .= "<font color='#ff0000'><b>Important!</b></font> " . $house_ruler_text[4] . " is conjunct North Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }
      elseif ($da <= $orb + 2)
      {
        $flag = True;
        $txt .= "Semi-important, yet not close enough to be a major testimony: " . $house_ruler_text[4] . " is conjunct North Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }

      //see if $rulers[4] is conjunct South Node
      $da = abs($lng1[$rulers[4]] - $lng1[SE_TSNODE]);
      if ($da > 180) { $da = 360 - $da; }
      if ($da <= $orb)
      {
        $flag = True;
        $txt .= "<font color='#ff0000'><b>Important!</b></font> " . $house_ruler_text[4] . " is conjunct South Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }
      elseif ($da <= $orb + 2)
      {
        $flag = True;
        $txt .= "Semi-important, yet not close enough to be a major testimony: " . $house_ruler_text[4] . " is conjunct South Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }


      //see if $rulers[5] (dispositor of the Part of Fortune) is conjunct North Node
      $da = abs($lng1[$rulers[5]] - $lng1[SE_TNNODE]);
      if ($da > 180) { $da = 360 - $da; }
      if ($da <= $orb)
      {
        $flag = True;
        $txt .= "<font color='#ff0000'><b>Important!</b></font> " . $house_ruler_text[5] . " is conjunct North Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }
      elseif ($da <= $orb + 2)
      {
        $flag = True;
        $txt .= "Semi-important, yet not close enough to be a major testimony: " . $house_ruler_text[5] . " is conjunct North Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }

      //see if $rulers[5] (dispositor of the Part of Fortune) is conjunct South Node
      $da = abs($lng1[$rulers[5]] - $lng1[SE_TSNODE]);
      if ($da > 180) { $da = 360 - $da; }
      if ($da <= $orb)
      {
        $flag = True;
        $txt .= "<font color='#ff0000'><b>Important!</b></font> " . $house_ruler_text[5] . " is conjunct South Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }
      elseif ($da <= $orb + 2)
      {
        $flag = True;
        $txt .= "Semi-important, yet not close enough to be a major testimony: " . $house_ruler_text[5] . " is conjunct South Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }


      //see if $rulers[1]'s antiscion is conjunct North Node
      $da = abs($antiscia1[$rulers[1]] - $lng1[SE_TNNODE]);
      if ($da > 180) { $da = 360 - $da; }
      if ($da <= $orb)
      {
        $flag = True;
        $txt .= "<font color='#ff0000'><b>Important!</b></font> antiscion of " . $house_ruler_text[1] . " is conjunct North Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }
      elseif ($da <= $orb + 2)
      {
        $flag = True;
        $txt .= "Semi-important, yet not close enough to be a major testimony: antiscion of " . $house_ruler_text[1] . " is conjunct North Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }


      //see if $rulers[1]'s antiscion is conjunct South Node
      $da = abs($antiscia1[$rulers[1]] - $lng1[SE_TSNODE]);
      if ($da > 180) { $da = 360 - $da; }
      if ($da <= $orb)
      {
        $flag = True;
        $txt .= "<font color='#ff0000'><b>Important!</b></font> antiscion of " . $house_ruler_text[1] . " is conjunct South Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }
      elseif ($da <= $orb + 2)
      {
        $flag = True;
        $txt .= "Semi-important, yet not close enough to be a major testimony: antiscion of " . $house_ruler_text[1] . " is conjunct South Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }


      //see if $rulers[2]'s antiscion is conjunct North Node
      $da = abs($antiscia1[$rulers[2]] - $lng1[SE_TNNODE]);
      if ($da > 180) { $da = 360 - $da; }
      if ($da <= $orb)
      {
        $flag = True;
        $txt .= "<font color='#ff0000'><b>Important!</b></font> antiscion of " . $house_ruler_text[2] . " is conjunct North Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }
      elseif ($da <= $orb + 2)
      {
        $flag = True;
        $txt .= "Semi-important, yet not close enough to be a major testimony: antiscion of " . $house_ruler_text[2] . " is conjunct North Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }

      //see if $rulers[2]'s antiscion is conjunct South Node
      $da = abs($antiscia1[$rulers[2]] - $lng1[SE_TSNODE]);
      if ($da > 180) { $da = 360 - $da; }
      if ($da <= $orb)
      {
        $flag = True;
        $txt .= "<font color='#ff0000'><b>Important!</b></font> antiscion of " . $house_ruler_text[2] . " is conjunct South Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }
      elseif ($da <= $orb + 2)
      {
        $flag = True;
        $txt .= "Semi-important, yet not close enough to be a major testimony: antiscion of " . $house_ruler_text[2] . " is conjunct South Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }


      //see if $rulers[3]'s antiscion is conjunct North Node
      $da = abs($antiscia1[$rulers[3]] - $lng1[SE_TNNODE]);
      if ($da > 180) { $da = 360 - $da; }
      if ($da <= $orb)
      {
        $flag = True;
        $txt .= "<font color='#ff0000'><b>Important!</b></font> antiscion of " . $house_ruler_text[3] . " is conjunct North Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }
      elseif ($da <= $orb + 2)
      {
        $flag = True;
        $txt .= "Semi-important, yet not close enough to be a major testimony: antiscion of " . $house_ruler_text[3] . " is conjunct North Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }


      //see if $rulers[3]'s antiscion is conjunct South Node
      $da = abs($antiscia1[$rulers[3]] - $lng1[SE_TSNODE]);
      if ($da > 180) { $da = 360 - $da; }
      if ($da <= $orb)
      {
        $flag = True;
        $txt .= "<font color='#ff0000'><b>Important!</b></font> antiscion of " . $house_ruler_text[3] . " is conjunct South Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }
      elseif ($da <= $orb + 2)
      {
        $flag = True;
        $txt .= "Semi-important, yet not close enough to be a major testimony: antiscion of " . $house_ruler_text[3] . " is conjunct South Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }


      //see if $rulers[4]'s antiscion is conjunct North Node
      $da = abs($antiscia1[$rulers[4]] - $lng1[SE_TNNODE]);
      if ($da > 180) { $da = 360 - $da; }
      if ($da <= $orb)
      {
        $flag = True;
        $txt .= "<font color='#ff0000'><b>Important!</b></font> antiscion of " . $house_ruler_text[4] . " is conjunct North Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }
      elseif ($da <= $orb + 2)
      {
        $flag = True;
        $txt .= "Semi-important, yet not close enough to be a major testimony: antiscion of " . $house_ruler_text[4] . " is conjunct North Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }

      //see if $rulers[4]'s antiscion is conjunct South Node
      $da = abs($antiscia1[$rulers[4]] - $lng1[SE_TSNODE]);
      if ($da > 180) { $da = 360 - $da; }
      if ($da <= $orb)
      {
        $flag = True;
        $txt .= "<font color='#ff0000'><b>Important!</b></font> antiscion of " . $house_ruler_text[4] . " is conjunct South Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }
      elseif ($da <= $orb + 2)
      {
        $flag = True;
        $txt .= "Semi-important, yet not close enough to be a major testimony: antiscion of " . $house_ruler_text[4] . " is conjunct South Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }


      //see if $rulers[5]'s antiscion (dispositor of the Part of Fortune) is conjunct North Node
      $da = abs($antiscia1[$rulers[5]] - $lng1[SE_TNNODE]);
      if ($da > 180) { $da = 360 - $da; }
      if ($da <= $orb)
      {
        $flag = True;
        $txt .= "<font color='#ff0000'><b>Important!</b></font> antiscion of " . $house_ruler_text[5] . " is conjunct North Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }
      elseif ($da <= $orb + 2)
      {
        $flag = True;
        $txt .= "Semi-important, yet not close enough to be a major testimony: antiscion of " . $house_ruler_text[5] . " is conjunct North Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }

      //see if $rulers[5]'s antiscion (dispositor of the Part of Fortune) is conjunct South Node
      $da = abs($antiscia1[$rulers[5]] - $lng1[SE_TSNODE]);
      if ($da > 180) { $da = 360 - $da; }
      if ($da <= $orb)
      {
        $flag = True;
        $txt .= "<font color='#ff0000'><b>Important!</b></font> antiscion of " . $house_ruler_text[5] . " is conjunct South Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }
      elseif ($da <= $orb + 2)
      {
        $flag = True;
        $txt .= "Semi-important, yet not close enough to be a major testimony: antiscion of " . $house_ruler_text[5] . " is conjunct South Node - distance apart is " . sprintf("%.2f", $da) . "<br>";
      }

      if ($flag == True) { echo $txt . "<br><br>"; }


      //display a note here about house positions
      $text_h = "";         //header text
      $text_c = "";         //content text

      $text_h .= "<a href='house_position_help_event.php' onClick=" . chr(34) . "return popup(this, 'help4')" . chr(34) . "><img src='balloon help.png' alt=''></a>&nbsp";
      $text_h .= "<b><b>HOUSE PLACEMENTS</b><br><br>";


      //check all 4 rulers of the angular houses proximity to each of the 4 angles
      //is X-ruler within 2 degrees of any angle?
      for ($i = 1; $i <= 4; $i++)
      {
        $da1 = abs($lng1[$rulers[$i]] - $hc1[1]);
        $da2 = abs($lng1[$rulers[$i]] - $hc1[7]);
        $da3 = abs($lng1[$rulers[$i]] - $hc1[10]);
        $da4 = abs($lng1[$rulers[$i]] - $hc1[4]);

        if ($da1 <= 2.00 Or $da1 >= 358.00)
        {
          $text_c .= "<b>" . $house_ruler_text[$i] . "</b> is conjunct the 1st house cusp.<br>";
        }
        elseif ($da2 <= 2.00 Or $da2 >= 358.00)
        {
          $text_c .= "<b>" . $house_ruler_text[$i] . "</b> is conjunct the 7th house cusp.<br>";
        }
        elseif ($da3 <= 2.00 Or $da3 >= 358.00)
        {
          $text_c .= "<b>" . $house_ruler_text[$i] . "</b> is conjunct the 10th house cusp.<br>";
        }
        elseif ($da4 <= 2.00 Or $da4 >= 358.00)
        {
          $text_c .= "<b>" . $house_ruler_text[$i] . "</b> is conjunct the 4th house cusp.<br>";
        }
      }


      //is the Moon within 2 degrees of 1st, 4th, 7th, or 10th house cusp?
      $da1 = abs($lng1[SE_MOON] - $hc1[1]);
      $da2 = abs($lng1[SE_MOON] - $hc1[7]);
      $da3 = abs($lng1[SE_MOON] - $hc1[10]);
      $da4 = abs($lng1[SE_MOON] - $hc1[4]);

      if ($da1 <= 2.00 Or $da1 >= 358.00)
      {
        $text_c .= "<b>The Moon </b> is on the 1st house cusp (<font color='#008000'><b>victory goes to the favorite</b></font>)<br>";
      }
      elseif ($da2 <= 2.00 Or $da2 >= 358.00)
      {
        $text_c .= "<b>The Moon </b> is on the 7th house cusp (<font color='#008000'><b>victory goes to the underdog</b></font>)<br>";
      }
      elseif ($da3 <= 2.00 Or $da3 >= 358.00)
      {
        $text_c .= "<b>The Moon </b> is on the 10th house cusp (<font color='#008000'><b>victory goes to the favorite</b></font>)<br>";
      }
      elseif ($da4 <= 2.00 Or $da4 >= 358.00)
      {
        $text_c .= "<b>The Moon </b> is on the 4th house cusp (<font color='#008000'><b>victory goes to the underdog</b></font>)<br>";
      }


      if ($text_c != "") { $text_c .= "<br>"; }

      //check all 4 ruler's antiscion of the angular houses proximity to each of the 4 angles
      //is X-ruler antiscion within 2 degrees of any angle?
      for ($i = 1; $i <= 4; $i++)
      {
        $da1 = abs($antiscia1[$rulers[$i]] - $hc1[1]);
        $da2 = abs($antiscia1[$rulers[$i]] - $hc1[7]);
        $da3 = abs($antiscia1[$rulers[$i]] - $hc1[10]);
        $da4 = abs($antiscia1[$rulers[$i]] - $hc1[4]);

        if ($da1 <= 2.00 Or $da1 >= 358.00)
        {
          $text_c .= "<b>" . $house_ruler_text[$i] . " antiscion</b> is conjunct the 1st house cusp.<br>";
        }
        elseif ($da2 <= 2.00 Or $da2 >= 358.00)
        {
          $text_c .= "<b>" . $house_ruler_text[$i] . " antiscion</b> is conjunct the 7th house cusp.<br>";
        }
        elseif ($da3 <= 2.00 Or $da3 >= 358.00)
        {
          $text_c .= "<b>" . $house_ruler_text[$i] . " antiscion</b> is conjunct the 10th house cusp.<br>";
        }
        elseif ($da4 <= 2.00 Or $da4 >= 358.00)
        {
          $text_c .= "<b>" . $house_ruler_text[$i] . " antiscion</b> is conjunct the 4th house cusp.<br>";
        }
      }


      //is the Moon's antiscion within 2 degrees of 1st, 4th, 7th, or 10th house cusp?
      $da1 = abs($antiscia1[SE_MOON] - $hc1[1]);
      $da2 = abs($antiscia1[SE_MOON] - $hc1[7]);
      $da3 = abs($antiscia1[SE_MOON] - $hc1[10]);
      $da4 = abs($antiscia1[SE_MOON] - $hc1[4]);

      if ($da1 <= 2.00 Or $da1 >= 358.00)
      {
        $text_c .= "<b>The Moon's antiscion </b> is on the 1st house cusp (<font color='#008000'><b>victory goes to the favorite</b></font>)<br>";
      }
      elseif ($da2 <= 2.00 Or $da2 >= 358.00)
      {
        $text_c .= "<b>The Moon's antiscion </b> is on the 7th house cusp (<font color='#008000'><b>victory goes to the underdog</b></font>)<br>";
      }
      elseif ($da3 <= 2.00 Or $da3 >= 358.00)
      {
        $text_c .= "<b>The Moon's antiscion </b> is on the 10th house cusp (<font color='#008000'><b>victory goes to the favorite</b></font>)<br>";
      }
      elseif ($da4 <= 2.00 Or $da4 >= 358.00)
      {
        $text_c .= "<b>The Moon's antiscion </b> is on the 4th house cusp (<font color='#008000'><b>victory goes to the underdog</b></font>)<br>";
      }


      //is the Part of Fortune's antiscion within 2 degrees of 1st, 4th, 7th, or 10th house cusp?
      $da1 = abs($antiscia1[SE_POF] - $hc1[1]);
      $da2 = abs($antiscia1[SE_POF] - $hc1[7]);
      $da3 = abs($antiscia1[SE_POF] - $hc1[10]);
      $da4 = abs($antiscia1[SE_POF] - $hc1[4]);

      if ($da1 <= 2.00 Or $da1 >= 358.00)
      {
        $text_c .= "<b>The Part of Fortune's antiscion </b> is on the 1st house cusp (<font color='#008000'><b>victory goes to the favorite</b></font>)<br>";
      }
      elseif ($da2 <= 2.00 Or $da2 >= 358.00)
      {
        $text_c .= "<b>The Part of Fortune's antiscion </b> is on the 7th house cusp (<font color='#008000'><b>victory goes to the underdog</b></font>)<br>";
      }
      elseif ($da3 <= 2.00 Or $da3 >= 358.00)
      {
        $text_c .= "<b>The Part of Fortune's antiscion </b> is on the 10th house cusp (<font color='#008000'><b>victory goes to the favorite</b></font>)<br>";
      }
      elseif ($da4 <= 2.00 Or $da4 >= 358.00)
      {
        $text_c .= "<b>The Part of Fortune's antiscion </b> is on the 4th house cusp (<font color='#008000'><b>victory goes to the underdog</b></font>)<br>";
      }

      if ($text_c != "") { echo $text_h . $text_c . "<br>"; }


      //now check each significator and their accidental dignity/debility
      //combust
      $text_h = "";         //header text
      $text_c = "";         //content text

      $text_h .= "<br>";
      $text_h .= "<a href='combust_event_help.php' onClick=" . chr(34) . "return popup(this, 'help2')" . chr(34) . "><img src='balloon help.png' alt=''></a>&nbsp";
      $text_h .= "<b>Combust Planets (combustion is destructive) = </b>";

      for ($i = SE_MOON; $i <= SE_POF; $i++)
      {
        if (floor($lng1[SE_SUN] / 30) == floor($lng1[$i] / 30) And abs($lng1[SE_SUN] - $lng1[$i]) >= 0 And abs($lng1[SE_SUN] - $lng1[$i]) <= 2.0)
        {
          if ($i == $rulers[1])
          {
            $text_c .= $pl_name[$i] . " (" . $house_ruler_text[1] . ")&nbsp;&nbsp;&nbsp;";
          }
          elseif ($i == $rulers[2])
          {
            $text_c .= $pl_name[$i] . " (" . $house_ruler_text[2] . ")&nbsp;&nbsp;&nbsp;";
          }
          elseif ($i == $rulers[3])
          {
            $text_c .= $pl_name[$i] . " (" . $house_ruler_text[3] . ")&nbsp;&nbsp;&nbsp;";
          }
          elseif ($i == $rulers[4])
          {
            $text_c .= $pl_name[$i] . " (" . $house_ruler_text[4] . ")&nbsp;&nbsp;&nbsp;";
          }
          elseif ($i == $rulers[5])
          {
            $text_c .= $pl_name[$i] . " (" . $house_ruler_text[5] . ")&nbsp;&nbsp;&nbsp;";
          }
          else
          {
            $text_c .= $pl_name[$i] . "&nbsp;&nbsp;&nbsp;";
          }
        }
      }

      if ($text_c != "") { echo $text_h . $text_c . "<br><br>"; }


      //display the Moon aspects here - aspects to longitudes
      $text_h = "";         //header text
      $text_c = "";         //content text

      $text_h .= "<br>";
      $text_h .= "<a href='significator_aspects_help_EVENT.php' onClick=" . chr(34) . "return popup(this, 'help1')" . chr(34) . "><img src='balloon help.png' alt=''></a>&nbsp";
      $text_h .= "<b>Moon's Aspects</b> (see top left for applying/separating):<br>";

      for ($i = 1; $i <= 1; $i++)
      {
        $significator = SE_MOON;
        $sign_of_significator = floor($lng1[SE_MOON] / 30) + 1;

        $text_h .= "(we have our 4 main significators - whichever of these is the Moon's last aspect over its range wins)<br><br>";

        for ($j = SE_SUN; $j <= SE_TSNODE; $j++)
        {
          if ($j == $significator) { continue; }                  //don't let a significator aspect itself

          $sign_of_p2 = floor($lng1[$j] / 30) + 1;

          $sign_diff =  abs($sign_of_significator - $sign_of_p2);

          $q = 0;
          $da = abs($lng1[$significator] - $lng1[$j]);

          if ($da > 180) { $da = 360 - $da; }

          $orb = 5;          //the absolute maximum limit of motion

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
          elseif ($da >= (180 - $orb) And $sign_diff == 6)
          {
            $q = 2;
            $da = 180 - $da;
          }


          if ($q > 0 and ($j == $rulers[1] Or $j == $rulers[2] Or $j == $rulers[3] Or $j == $rulers[4] Or $j == $rulers[5] Or $j == SE_POF))
          {
            if ($q == 1)
            {
              $text_c .= $pl_name[$significator] . " " . $asp_name[$q] . " " . $pl_name[$j] . "&nbsp;&nbsp;&nbsp;" . sprintf("%.2f", abs($da)) . " - conjunction - treat this as if it were the final aspect<br>";
            }
            else
            {
              $text_c .= $pl_name[$significator] . " " . $asp_name[$q] . " " . $pl_name[$j] . "&nbsp;&nbsp;&nbsp;" . sprintf("%.2f", abs($da)) . "<br>";
            }
          }
        }
      }


      if ($text_c != "") { $text_c .= "<br>"; }


      //Moon longitude to planet antiscion aspects here
      for ($i = 1; $i <= 1; $i++)
      {
        $significator = SE_MOON;
        $sign_of_significator = floor($lng1[SE_MOON] / 30) + 1;

        for ($j = SE_SUN; $j <= SE_TSNODE; $j++)
        {
          if ($j == $significator) { continue; }                  //don't let a significator aspect itself

          $sign_of_p2 = floor($antiscia1[$j] / 30) + 1;

          $sign_diff =  abs($sign_of_significator - $sign_of_p2);

          $q = 0;
          $da = abs($lng1[$significator] - $antiscia1[$j]);

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
          elseif ($da >= (180 - $orb) And $sign_diff == 6)
          {
            $q = 2;
            $da = 180 - $da;
          }


          if ($q > 0 and ($j == $rulers[1] Or $j == $rulers[2] Or $j == $rulers[3] Or $j == $rulers[4] Or $j == $rulers[5] Or $j == SE_POF))
          {
            $text_c .= $pl_name[$significator] . " " . $asp_name[$q] . " antiscion of " . $pl_name[$j] . "&nbsp;&nbsp;&nbsp;" . sprintf("%.2f", abs($da)) . "<br>";
          }
        }
      }

      if ($text_c != "") { echo $text_h . $text_c . "<br>"; }


      //display the Part of Fortune aspects here - aspects to longitudes
      $text_h = "";         //header text
      $text_c = "";         //content text

      $text_h .= "<br>";
      $text_h .= "<a href='significator_aspects_help_EVENT.php' onClick=" . chr(34) . "return popup(this, 'help3')" . chr(34) . "><img src='balloon help.png' alt=''></a>&nbsp";
      $text_h .= "<b>Aspects to Part of Fortune and its antiscion (which are final, <font color='#ff0000'>IF there is no frustration or prohibition</font>):</b><br><br>";

      for ($i = SE_SUN; $i <= SE_PLUTO; $i++)
      {
        $significator = $i;
        $sign_of_significator = floor($lng1[$i] / 30) + 1;

        for ($j = SE_POF; $j <= SE_POF; $j++)
        {
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
          elseif ($da >= (180 - $orb) And $sign_diff == 6)
          {
            $q = 2;
            $da = 180 - $da;
          }


          if ($q > 0 and ($i == SE_MOON Or $i == $rulers[1] Or $i == $rulers[2] Or $i == $rulers[3] Or $i == $rulers[4]))
          {
            if ($i == SE_MOON)
            {
              if ($q == 1 Or $q == 3 Or $q == 5)
              {
                $text_c .= $pl_name[$significator] . " " . $asp_name[$q] . " " . $pl_name[$j] . "&nbsp;&nbsp;&nbsp;" . sprintf("%.2f", abs($da)) . " - favors the favorite.<br>";
              }
              else
              {
                $text_c .= $pl_name[$significator] . " " . $asp_name[$q] . " " . $pl_name[$j] . "&nbsp;&nbsp;&nbsp;" . sprintf("%.2f", abs($da)) . " - favors the underdog.<br>";
              }
            }
            else
            {
              if ($i == $rulers[1] and $q == 1)
              {
                $text_c .= $pl_name[$significator] . " " . $asp_name[$q] . " " . $pl_name[$j] . "&nbsp;&nbsp;&nbsp;" . sprintf("%.2f", abs($da)) . " - favors the favorite.<br>";
              }
              elseif ($i == $rulers[1] and $q == 2)
              {
                $text_c .= $pl_name[$significator] . " " . $asp_name[$q] . " " . $pl_name[$j] . "&nbsp;&nbsp;&nbsp;" . sprintf("%.2f", abs($da)) . " - favors the underdog.<br>";
              }
              elseif ($i == $rulers[2] and $q == 1)
              {
                $text_c .= $pl_name[$significator] . " " . $asp_name[$q] . " " . $pl_name[$j] . "&nbsp;&nbsp;&nbsp;" . sprintf("%.2f", abs($da)) . " - favors the underdog.<br>";
              }
              elseif ($i == $rulers[2] and $q == 2)
              {
                $text_c .= $pl_name[$significator] . " " . $asp_name[$q] . " " . $pl_name[$j] . "&nbsp;&nbsp;&nbsp;" . sprintf("%.2f", abs($da)) . " - favors the underdog.<br>";
              }
              elseif ($i == $rulers[1] Or $i == $rulers[2])
              {
              }
              else
              {
                $text_c .= $pl_name[$significator] . " " . $asp_name[$q] . " " . $pl_name[$j] . "&nbsp;&nbsp;&nbsp;" . sprintf("%.2f", abs($da)) . "<br>";
              }
            }
          }
        }
      }


      if ($text_c != "") { $text_c .= "<br>"; }


      //planet longitude to Part of Fortune antiscion aspects here
      for ($i = SE_SUN; $i <= SE_PLUTO; $i++)
      {
        $significator = $i;
        $sign_of_significator = floor($lng1[$i] / 30) + 1;

        for ($j = SE_POF; $j <= SE_POF; $j++)
        {
          $sign_of_p2 = floor($antiscia1[$j] / 30) + 1;

          $sign_diff =  abs($sign_of_significator - $sign_of_p2);

          $q = 0;
          $da = abs($lng1[$significator] - $antiscia1[$j]);

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
          elseif ($da >= (180 - $orb) And $sign_diff == 6)
          {
            $q = 2;
            $da = 180 - $da;
          }


          if ($q > 0 and ($i == SE_MOON Or $i == $rulers[1] Or $i == $rulers[2] Or $i == $rulers[3] Or $i == $rulers[4]))
          {
            if ($i == SE_MOON)
            {
              if ($q == 1 Or $q == 3 Or $q == 5)
              {
                $text_c .= $pl_name[$significator] . " " . $asp_name[$q] . " antiscion of " . $pl_name[$j] . "&nbsp;&nbsp;&nbsp;" . sprintf("%.2f", abs($da)) . " - favors the favorite.<br>";
              }
              else
              {
                $text_c .= $pl_name[$significator] . " " . $asp_name[$q] . " antiscion of " . $pl_name[$j] . "&nbsp;&nbsp;&nbsp;" . sprintf("%.2f", abs($da)) . " - favors the underdog.<br>";
              }
            }
            else
            {
              if ($i == $rulers[1] and $q == 1)
              {
                $text_c .= $pl_name[$significator] . " " . $asp_name[$q] . " antiscion of " . $pl_name[$j] . "&nbsp;&nbsp;&nbsp;" . sprintf("%.2f", abs($da)) . " - favors the favorite.<br>";
              }
              elseif ($i == $rulers[1] and $q == 2)
              {
                $text_c .= $pl_name[$significator] . " " . $asp_name[$q] . " antiscion of " . $pl_name[$j] . "&nbsp;&nbsp;&nbsp;" . sprintf("%.2f", abs($da)) . " - favors the underdog.<br>";
              }
              elseif ($i == $rulers[7] and $q == 1)
              {
                $text_c .= $pl_name[$significator] . " " . $asp_name[$q] . " antiscion of " . $pl_name[$j] . "&nbsp;&nbsp;&nbsp;" . sprintf("%.2f", abs($da)) . " - favors the underdog.<br>";
              }
              elseif ($i == $rulers[7] and $q == 2)
              {
                $text_c .= $pl_name[$significator] . " " . $asp_name[$q] . " antiscion of " . $pl_name[$j] . "&nbsp;&nbsp;&nbsp;" . sprintf("%.2f", abs($da)) . " - favors the favorite.<br>";
              }
              elseif ($i == $rulers[1] Or $i == $rulers[2])
              {
              }
              else
              {
                $text_c .= $pl_name[$significator] . " " . $asp_name[$q] . " antiscion of " . $pl_name[$j] . "&nbsp;&nbsp;&nbsp;" . sprintf("%.2f", abs($da)) . "<br>";
              }
            }
          }
        }
      }


      //display any conjunction or opposition from dispositor of Part of Fortune to the Part of Fortune
      for ($i = $rulers[5]; $i <= $rulers[5]; $i++)
      {
        if ($i == $rulers[1] Or $i == $rulers[2] Or $i == SE_MOON) { break; }

        $significator = $i;
        $sign_of_significator = floor($lng1[$i] / 30) + 1;

        for ($j = SE_POF; $j <= SE_POF; $j++)
        {
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
          elseif ($da >= (180 - $orb) And $sign_diff == 6)
          {
            $q = 2;
            $da = 180 - $da;
          }


          if ($q == 1)
          {
            $text_c .= $pl_name[$significator] . " (dispositor of Part of Fortune) " . $asp_name[$q] . " " . $pl_name[$j] . "&nbsp;&nbsp;&nbsp;" . sprintf("%.2f", abs($da)) . " - favors the favorite.<br>";
          }
          elseif ($q == 2)
          {
            $text_c .= $pl_name[$significator] . " (dispositor of Part of Fortune) " . $asp_name[$q] . " " . $pl_name[$j] . "&nbsp;&nbsp;&nbsp;" . sprintf("%.2f", abs($da)) . " - favors the underdog.<br>";
          }
        }
      }


      if ($text_c != "") { $text_c .= "<br>"; }


      //display any conjunction or opposition from dispositor of Part of Fortune to the Part of Fortune's antiscion
      for ($i = $rulers[5]; $i <= $rulers[5]; $i++)
      {
        if ($i == $rulers[1] Or $i == $rulers[2] Or $i == SE_MOON) { break; }

        $significator = $i;
        $sign_of_significator = floor($lng1[$i] / 30) + 1;

        for ($j = SE_POF; $j <= SE_POF; $j++)
        {
          $sign_of_p2 = floor($antiscia1[$j] / 30) + 1;

          $sign_diff =  abs($sign_of_significator - $sign_of_p2);

          $q = 0;
          $da = abs($lng1[$significator] - $antiscia1[$j]);

          if ($da > 180) { $da = 360 - $da; }

          $orb = 5;

          // is there an aspect within orb?
          //BUT . . . any legitimate aspect must NOT be over the sign line
          if ($da <= $orb And $sign_diff == 0)
          {
            $q = 1;
            $da = $da;
          }
          elseif ($da >= (180 - $orb) And $sign_diff == 6)
          {
            $q = 2;
            $da = 180 - $da;
          }


          if ($q == 1)
          {
            $text_c .= $pl_name[$significator] . " (dispositor of Part of Fortune) " . $asp_name[$q] . " antiscion of " . $pl_name[$j] . "&nbsp;&nbsp;&nbsp;" . sprintf("%.2f", abs($da)) . " - favors the favorite.<br>";
          }
          elseif ($q == 2)
          {
            $text_c .= $pl_name[$significator] . " (dispositor of Part of Fortune) " . $asp_name[$q] . " antiscion of " . $pl_name[$j] . "&nbsp;&nbsp;&nbsp;" . sprintf("%.2f", abs($da)) . " - favors the underdog.<br>";
          }
        }
      }


      //now see if the Part of Fortune is within 2 degrees of the Nodes - conjunctions only
      if ($text_c != "") { $text_c .= "<br>"; }

      $da = abs($lng1[SE_POF] - $lng1[SE_TNNODE]);
      $da22 = abs($lng1[SE_POF] - $lng1[SE_TSNODE]);

      if ($da <= 2.00 Or $da >= 358.00)
      {
        $text_c .= "The Part of Fortune is conjunct the North Node - this favors the favorite.<br>";
      }
      elseif ($da22 <= 2.00 Or $da22 >= 358.00)
      {
        $text_c .= "The Part of Fortune is conjunct the South Node - this favors the underdog.<br>";
      }

      if ($text_c != "") { echo $text_h . $text_c; }


      //now see what Pluto is up to - allow a 2.00-degree orb (for outer planets)
      $text_h = "";         //header text
      $text_c = "";         //content text

      $text_h .= "<br>";
      $text_h .= "<a href='outer_planets_help_EVENT.php' onClick=" . chr(34) . "return popup(this, 'help5')" . chr(34) . "><img src='balloon help.png' alt=''></a>&nbsp";
      $text_h .= "<b>Aspects involving outer planets (check planet Rx motion):</b><br><br>";

      $orb = 2.00;

      $da1 = abs($lng1[SE_PLUTO] - $hc1[1]);
      $da2 = abs($lng1[SE_PLUTO] - $hc1[2]);
      $da3 = abs($lng1[SE_PLUTO] - $hc1[10]);
      $da4 = abs($lng1[SE_PLUTO] - $hc1[7]);
      $da5 = abs($lng1[SE_PLUTO] - $hc1[4]);

      if ($da1 <= $orb Or $da1 >= (360 - $orb)) { $text_c .= "Pluto is conjunct the 1st house - favors the underdog.<br><br>"; }
      if ($da2 <= $orb Or $da2 >= (360 - $orb)) { $text_c .= "Pluto is conjunct the 2nd house - favors the underdog.<br><br>"; }
      if ($da3 <= $orb Or $da3 >= (360 - $orb)) { $text_c .= "Pluto is conjunct the 10th house - favors the underdog.<br><br>"; }

      if ($da4 <= $orb Or $da4 >= (360 - $orb)) { $text_c .= "Pluto is conjunct the 7th house - this might favor the favorite.<br><br>"; }
      if ($da5 <= $orb Or $da5 >= (360 - $orb)) { $text_c .= "Pluto is conjunct the 4th house - this might favor the favorite.<br><br>"; }

      $da6 = abs($lng1[SE_PLUTO] - $lng1[SE_POF]);

      if ($da6 <= $orb Or $da6 >= (360 - $orb)) { $text_c .= "Pluto is conjunct the Part of Fortune - favors the underdog.<br><br>"; }
      if ($da6 >= (180 - $orb) And $da6 <= (180 + $orb)) { $text_c .= "Pluto is opposite the Part of Fortune - favors the underdog.<br><br>"; }

      $da7 = abs($lng1[SE_PLUTO] - $antiscia1[SE_POF]);

      if ($da7 <= $orb Or $da7 >= (360 - $orb)) { $text_c .= "Pluto is conjunct the Part of Fortune's antiscion - favors the underdog.<br><br>"; }
      if ($da7 >= (180 - $orb) And $da7 <= (180 + $orb)) { $text_c .= "Pluto is opposite the Part of Fortune's antiscion - favors the underdog.<br><br>"; }

      $da8 = abs($lng1[SE_PLUTO] - $lng1[$rulers[5]]);

      if ($da8 <= $orb Or $da8 >= (360 - $orb)) { $text_c .= "Pluto is conjunct the Part of Fortune's dispositor - favors the underdog.<br><br>"; }
      if ($da8 >= (180 - $orb) And $da8 <= (180 + $orb)) { $text_c .= "Pluto is opposite the Part of Fortune's dispositor - favors the underdog.<br><br>"; }


      //now see what Uranus is up to - allow a 2.00-degree orb (for outer planets)
      $da9 = abs($lng1[SE_URANUS] - $hc1[10]);
      if ($da9 <= $orb Or $da9 >= (360 - $orb)) { $text_c .= "Uranus is conjunct the 10th house - favors the favorite.<br><br>"; }

      $da10 = abs($lng1[SE_URANUS] - $lng1[SE_POF]);
      if ($da10 <= $orb Or $da10 >= (360 - $orb)) { $text_c .= "Uranus is conjunct the Part of Fortune - favors the favorite.<br><br>"; }
      if ($da10 >= (180 - $orb) And $da10 <= (180 + $orb)) { $text_c .= "Uranus is opposite the Part of Fortune - favors the underdog.<br><br>"; }


      //now see what Saturn is up to - allow a 1.75-degree orb (for outer planets) - BUT ONLY IF SATURN IS NOT 1-RULER and NOT 2-RULER
      if (SE_SATURN != $rulers[1] And SE_SATURN != $rulers[2])
      {
        $da11 = abs($lng1[SE_SATURN] - $hc1[1]);
        $da12 = abs($lng1[SE_SATURN] - $hc1[2]);
        $da13 = abs($lng1[SE_SATURN] - $hc1[10]);
        $da14 = abs($lng1[SE_SATURN] - $hc1[7]);
        $da15 = abs($lng1[SE_SATURN] - $hc1[4]);

        if ($da11 <= $orb Or $da11 >= (360 - $orb)) { $text_c .= "Saturn is conjunct the 1st house - favors the underdog.<br><br>"; }
        if ($da12 <= $orb Or $da12 >= (360 - $orb)) { $text_c .= "Saturn is conjunct the 2nd house - favors the underdog.<br><br>"; }
        if ($da13 <= $orb Or $da13 >= (360 - $orb)) { $text_c .= "Saturn is conjunct the 10th house - favors the underdog.<br><br>"; }

        if ($da14 <= $orb Or $da14 >= (360 - $orb)) { $text_c .= "Saturn is conjunct the 7th house - this might favor the favorite.<br><br>"; }
        if ($da15 <= $orb Or $da15 >= (360 - $orb)) { $text_c .= "Saturn is conjunct the 4th house - this might favor the favorite.<br><br>"; }

        $da16 = abs($lng1[SE_SATURN] - $lng1[SE_POF]);

        if ($da16 <= $orb Or $da16 >= (360 - $orb)) { $text_c .= "Saturn is conjunct the Part of Fortune - favors the underdog.<br><br>"; }
        if ($da16 >= (180 - $orb) And $da16 <= (180 + $orb)) { $text_c .= "Saturn is opposite the Part of Fortune - favors the underdog.<br><br>"; }

        $da17 = abs($lng1[SE_SATURN] - $antiscia1[SE_POF]);

        if ($da17 <= $orb Or $da17 >= (360 - $orb)) { $text_c .= "Saturn is conjunct the Part of Fortune's antiscion - favors the underdog.<br><br>"; }
        if ($da17 >= (180 - $orb) And $da17 <= (180 + $orb)) { $text_c .= "Saturn is opposite the Part of Fortune's antiscion - favors the underdog.<br><br>"; }

        $da18 = abs($lng1[SE_SATURN] - $lng1[$rulers[5]]);

        if ($da18 <= $orb Or $da18 >= (360 - $orb)) { $text_c .= "Saturn is conjunct the Part of Fortune's dispositor - favors the underdog.<br><br>"; }
        if ($da18 >= (180 - $orb) And $da18 <= (180 + $orb)) { $text_c .= "Saturn is opposite the Part of Fortune's dispositor - favors the underdog.<br><br>"; }
      }

      if ($text_c != "") { echo $text_h . $text_c . "<br>"; }


      //display the aspects here - do each significator's aspects
      echo "<hr style='height:3px; background-color:#ff0000; border:0;'><br>";
      ?><a href="significator_aspects_help_EVENT.php" onClick="return popup(this, 'help6')"><img src='balloon help.png' alt=''></a>&nbsp;<?php
      echo "<b>Significator's Aspects</b> (see top left for applying/separating):<br><br>";

      for ($i = 1; $i <= 5; $i++)
      {
        $significator = $rulers[$i];
        $sign_of_significator = floor($lng1[$significator] / 30) + 1;

        if ($i == 1)
        {
          echo "" . $house_ruler_text[1] . ":<br>";
        }
        elseif ($i == 2)
        {
          echo "<br>" . $house_ruler_text[2] . "<br>";
        }
        elseif ($i == 3)
        {
          echo "<br>" . $house_ruler_text[3] . "<br>";
        }
        elseif ($i == 4)
        {
          echo "<br>" . $house_ruler_text[4] . "<br>";
        }
        elseif ($i == 5)
        {
          echo "<br>" . $house_ruler_text[5] . "<br>";
        }

        for ($j = SE_SUN; $j <= SE_PLUTO; $j++)
        {
          if ($j == $significator) { continue; }                  //don't let a significator aspect itself

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


      echo "<br><hr style='height:3px; background-color:#ff0000; border:0;'><br>Having read and studied John Frawley's book 'Sports Astrology' (ISBN 978 0953977420), I find that it is nice to have all the pertinent data all in one place so I don't miss something important - I can then concentrate on analysis. The above data is the result of this desire. It is all the necessary information for any chart - now get to work figuring out who's gonna win.<br><br><br>";
    echo '</td></tr></table></center><br><br>';
      //end of event analysis
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

  if ($i == $rulers[1] Or $i == $rulers[2] Or $i == $rulers[3] Or $i == $rulers[4] Or $i == $rulers[5])
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
  return "";

  if ($p_num == 1) { return " (check the Moon for the amount of light it has - more is better)"; }
}


Function Detect_num_same_deg_planets_max($longitude1)
{
  $deg_filled_idx = array();

  for ($i = 0; $i <= 30; $i++)
  {
    $deg_filled_idx[$i] = 0;      //initialize
  }

  $cnt = 0;
  for ($i = 0; $i <= LAST_PLANET + 2; $i++)
  {
    $pl_pos = floor(Reduce_below_30($longitude1[$i]));
    $deg_filled_idx[$pl_pos]++;
  }

  $mx = 0;
  for ($i = 0; $i <= 30; $i++)
  {
    if ($deg_filled_idx[$i] > $mx)
    {
      $mx = $deg_filled_idx[$i];
    }
  }

  return $mx;
}

?>