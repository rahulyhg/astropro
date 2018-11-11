<?php
  include ('../accesscontrol.php');

  if ($is_logged_in == False)
  {
    exit();
  }

  require_once ('../../../mysqli_connect_online_calcs_db_MYSQLI.php');
  require_once ('../../../my_functions_MYSQLI.php');

  $no_interps = False;        //set this to False when you want interpretations

  // check if the form has been submitted
  if (isset($_POST['submitted']))
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
    $name = $row['name'];

    $month = $row['month'];
    $day = $row['day'];
    $year = $row['year'];

    $hour = $row['hour'];
    $minute = $row['minute'];

    $timezone = $row['timezone'];

    $long_deg = $row['long_deg'];
    $long_min = $row['long_min'];
    $ew = $row['ew'];

    $lat_deg = $row['lat_deg'];
    $lat_min = $row['lat_min'];
    $ns = $row['ns'];

    include ('header_vocation.html');       //here because of setting cookies above

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
      unset($PATH,$out,$pl_name,$longitude1,$house_pos);

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
      $h_sys = "p";

      exec ("swetest -edir$sweph -b$utdatenow -ut$utnow -p0123456789DAmmm -eswe -house$my_longitude,$my_latitude,$h_sys -flsj -g, -head", $out);

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

//display natal data
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

      $line1 = $restored_name . ", born " . strftime("%A, %B %d, %Y at %H:%M (time zone = GMT $tz hours)", mktime($hour, $minute, $secs, $month, $day, $year));
      $line1 = $line1 . " at " . $long_deg . $ew_txt . sprintf("%02d", $long_min) . " and " . $lat_deg . $ns_txt . sprintf("%02d", $lat_min);

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
      $_SESSION['longitude1_VO'] = $longitude1;
      $_SESSION['hc1_VO'] = $hc1;
      $_SESSION['house_pos1_VO'] = $house_pos1;

      $wheel_width = 640;
      $wheel_height = $wheel_width + 50;    //includes space at top of wheel for header

      echo "<center>";
      echo "<img border='0' src='natal_wheel_VO.php?rx1=$rx1&l1=$line1' width='$wheel_width' height='$wheel_height'>";
      echo "<br><br>";
      echo "<img border='0' src='natal_aspect_grid_VO.php?rx1=$rx1&rx2=$rx2&ubt1=$ubt1&ubt2=$ubt2' width='705' height='450'>";
      echo "</center>";
      echo "<br>";


//display natal data
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
        echo "<td><font color='#0000ff'><b> House<br>position </b></font></td>";
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


      // vocational analysis header
      echo '<center><table width="50%" cellpadding="0" cellspacing="0" border="0">';
      echo '<tr><td>';

     echo "Having read and studied Noel Tyl's book 'Vocations - The New Midheaven Extension Process' (ISBN 0-7387-0778-3 - http://www.noeltyl.com/menu.shtml), I can highly recommend it to anyone wishing to learn more about astrological vocational analysis. Mr. Tyl believes that most people who practice his techniques will be able to read the message of the stars within a couple of minutes. Okay. Yet I find that it is nice to have all the pertinent data all in one place so I don't miss something important - I can then concentrate on analysis. The below data is the result of this desire. It is all the necessary information for any chart - now get to work figuring out the best vocation(s) for the person involved.<br /><br />";

     echo "<a href='vocation_notes.zip'>Download my vocational analysis study notes</a><br /><br /><br />";
     
      // display vocational analysis data - Find_MC_sign_and_ruler
      $temp = floor($hc1[10] / 30) + 1;

      echo $sign_name[$temp] . " is the sign on the MC and its ruler is ";

      if ($temp == 1)
      {
        $t = "Mars";
        $MC_ruler_pnum = 4;
      }
      elseif ($temp == 2)
      {
        $t = "Venus";
        $MC_ruler_pnum = 3;
      }
      elseif ($temp == 3)
      {
        $t = "Mercury";
        $MC_ruler_pnum = 2;
      }
      elseif ($temp == 4)
      {
        $t = "Moon";
        $MC_ruler_pnum = 10;
      }
      elseif ($temp == 5)
      {
        $t = "Sun";
        $MC_ruler_pnum = 1;
      }
      elseif ($temp == 6)
      {
        $t = "Mercury";
        $MC_ruler_pnum = 2;
      }
      elseif ($temp == 7)
      {
        $t = "Venus";
        $MC_ruler_pnum = 3;
      }
      elseif ($temp == 8)
      {
        $t = "Pluto";
        $MC_ruler_pnum = 9;
      }
      elseif ($temp == 9)
      {
        $t = "Jupiter";
        $MC_ruler_pnum = 5;
      }
      elseif ($temp == 10)
      {
        $t = "Saturn";
        $MC_ruler_pnum = 6;
      }
      elseif ($temp == 11)
      {
        $t = "Uranus";
        $MC_ruler_pnum = 7;
      }
      elseif ($temp == 12)
      {
        $t = "Neptune";
        $MC_ruler_pnum = 8;
      }

      echo $t . "<br />";

      if ($temp == 3 Or $temp == 12 Or $temp == 9)
      {
        echo "(possibly more than 1 vocation indicated)<br /><br />";
      }
      else
      {
        echo "<br />";
      }


      // display vocational analysis data - Find_MC_ruler_aspects_to_natal_planets
      Find_MC_ruler_aspects_to_natal_planets($longitude1, $MC_ruler_pnum, $pl_name);
      

      // display vocational analysis data - Find_interception_in_10th_house
      //get how many signs are intercepted in each house
      for ($i = 1; $i <= 12; $i++)
      {
        if ($i != 12)
        {
          $temp7 = floor($hc1[$i] / 30) + 1;
          $temp8 = floor($hc1[$i + 1] / 30) + 1;
        }
        else
        {
            $temp7 = floor($hc1[12] / 30) + 1;
            $temp8 = floor($hc1[1] / 30) + 1;
        }
        
        if ($temp8 - $temp7 < 0)
        {
          $temp8 = $temp8 + 12;
        }
        
        if ($temp8 - $temp7 == 2)
        {
          $ih[$i] = $temp7 + 1;

          if ($ih[$i] > 12)
          {
            $ih[$i] = $ih[$i] - 12;
          }
        }
        else
        {
          $ih[$i] = 0;
        }
      }

      // value of $ih[10] is the sign intercepted in the 10th house
      if ($ih[10] != 0)
      {
        $temp = $ih[10];
        echo $sign_name[$temp] . " is intercepted in the 10th house and its ruler is ";

        if ($temp == 1)
        {
          $t = "Mars";
          $MC_ruler_pnum = 4;
        }
        elseif ($temp == 2)
        {
          $t = "Venus";
          $MC_ruler_pnum = 3;
        }
        elseif ($temp == 3)
        {
          $t = "Mercury";
          $MC_ruler_pnum = 2;
        }
        elseif ($temp == 4)
        {
          $t = "Moon";
          $MC_ruler_pnum = 10;
        }
        elseif ($temp == 5)
        {
          $t = "Sun";
          $MC_ruler_pnum = 1;
        }
        elseif ($temp == 6)
        {
          $t = "Mercury";
          $MC_ruler_pnum = 2;
        }
        elseif ($temp == 7)
        {
          $t = "Venus";
          $MC_ruler_pnum = 3;
        }
        elseif ($temp == 8)
        {
          $t = "Pluto";
          $MC_ruler_pnum = 9;
        }
        elseif ($temp == 9)
        {
          $t = "Jupiter";
          $MC_ruler_pnum = 5;
        }
        elseif ($temp == 10)
        {
          $t = "Saturn";
          $MC_ruler_pnum = 6;
        }
        elseif ($temp == 11)
        {
          $t = "Uranus";
          $MC_ruler_pnum = 7;
        }
        elseif ($temp == 12)
        {
          $t = "Neptune";
          $MC_ruler_pnum = 8;
        }

        echo $t . "<br /><br />";

        if ($temp == 3 Or $temp == 12 Or $temp == 9)
        {
          echo "<br />(possibly more than 1 vocation indicated)";
        }

        Find_MC_ruler_aspects_to_natal_planets($longitude1, $MC_ruler_pnum, $pl_name);
      }



      // display vocational analysis data - Planets_conjunct_the_MC
      $orb = 5;

      for ($i = 0; $i <= 9; $i++)
      {
        $da = abs($longitude1[$i] - $hc1[10]);
        if ($da > 180)
        {
          $da = 360 - $da;
        }

        if ($da <= $orb)
        {
          echo $pl_name[$i] . " is prominent because it is conjunct the MC.";
          echo "<br /><br />";

          $MC_ruler_pnum = $i;
          Find_MC_ruler_aspects_to_natal_planets($longitude1, $MC_ruler_pnum, $pl_name);
        }
      }


      // display vocational analysis data - Find_MC_aspects_to_natal_planets
      $pflag = False;
      for ($i = 0; $i <= 9; $i++)
      {
        $da = abs($hc1[10] - $longitude1[$i]);
        if ($da > 180)
        {
          $da = 360 - $da;
        }

        if ($da <= 7)
        {
          echo "MC is conjunct " . $pl_name[$i] . "<br />";
          $pflag = True;
        }
        elseif ($da <= 64 And $da >= 56)
        {
          echo "MC sextiles " . $pl_name[$i] . "<br />";
          $pflag = True;
        }
        elseif ($da <= 97 And $da >= 83)
        {
          echo "MC squares " . $pl_name[$i] . "<br />";
          $pflag = True;
        }
        elseif ($da <= 126 And $da >= 114)
        {
          echo "MC trines " . $pl_name[$i] . "<br />";
          $pflag = True;
        }
        elseif ($da >= 173)
        {
          echo "MC opposes " . $pl_name[$i] . "<br />";
          $pflag = True;
        }
      }

      if ($pflag == True)
      {
        echo "<br /><br />";
      }
      

      // display vocational analysis data - Find_final_dispositors
      $pflag = False;
      for ($i = 0; $i <= 9; $i++)
      {
        $temp = floor($longitude1[$i] / 30) + 1;

        if ($temp == 1 And $i == SE_MARS)
        {
          echo "Mars is in its own sign and is a final dispositor<br />";
          $pflag = True;
        }
        elseif (($temp == 2 Or $temp == 7) And $i == SE_VENUS)
        {
          echo "Venus is in its own sign and is a final dispositor<br />";
          $pflag = True;
        }
        elseif (($temp == 3 Or $temp == 6) And $i == SE_MERCURY)
        {
          echo "Mercury is in its own sign and is a final dispositor<br />";
          $pflag = True;
        }
        elseif ($temp == 4 And $i == SE_MOON)
        {
          echo "The Moon is in its own sign and is a final dispositor<br />";
          $pflag = True;
        }
        elseif ($temp == 5 And $i == SE_SUN)
        {
          echo "The Sun is in its own sign and is a final dispositor<br />";
          $pflag = True;
        }
        elseif ($temp == 9 And $i == SE_JUPITER)
        {
          echo "Jupiter is in its own sign and is a final dispositor<br />";
          $pflag = True;
        }
        elseif ($temp == 10 And $i == SE_SATURN)
        {
          echo "Saturn is in its own sign and is a final dispositor<br />";
          $pflag = True;
        }
        elseif ($temp == 11 And $i == SE_URANUS)
        {
          echo "Uranus is in its own sign and is a final dispositor<br />";
          $pflag = True;
        }
        elseif ($temp == 12 And $i == SE_NEPTUNE)
        {
          echo "Neptune is in its own sign and is a final dispositor<br />";
          $pflag = True;
        }
        elseif ($temp == 8 And $i == SE_PLUTO)
        {
          echo "Pluto is in its own sign and is a final dispositor<br />";
          $pflag = True;
        }
      }

      if ($pflag == True)
      {
        echo "<br /><br />";
      }


      // display vocational analysis data - Planets_in_mutual_reception
      $pflag = False;
      for ($i = 0; $i <= 9; $i++)
      {
        $temp = floor($longitude1[$i] / 30) + 1;

        if ($temp == 1) { $j = 4; }
        if ($temp == 2 Or $temp == 7) { $j = 3; }
        if ($temp == 3 Or $temp == 6) { $j = 2; }
        if ($temp == 4) { $j = 1; }
        if ($temp == 5) { $j = 0; }
        if ($temp == 8) { $j = 9; }
        if ($temp == 9) { $j = 5; }
        if ($temp == 10) { $j = 6; }
        if ($temp == 11) { $j = 7; }
        if ($temp == 12) { $j = 8; }
        
        $temp = floor($longitude1[$j] / 30) + 1;

        if ($i == 0 And $j > $i And $temp == 5)
        {
          echo $pl_name[$i] . " is in mutual reception with " . $pl_name[$j];
          check_for_aspect($i, $j, $longitude1);
          $pflag = True;
        }
        
        if ($i == 1 And $j > $i And $temp == 4)
        {
          echo $pl_name[$i] . " is in mutual reception with " . $pl_name[$j];
          check_for_aspect($i, $j, $longitude1);
          $pflag = True;
        }
        
        if ($i == 2 And $j > $i And ($temp == 3 Or $temp == 6))
        {
          echo $pl_name[$i] . " is in mutual reception with " . $pl_name[$j];
          check_for_aspect($i, $j, $longitude1);
          $pflag = True;
        }
        
        if ($i == 3 And $j > $i And ($temp == 2 Or $temp == 7))
        {
          echo $pl_name[$i] . " is in mutual reception with " . $pl_name[$j];
          check_for_aspect($i, $j, $longitude1);
          $pflag = True;
        }
        
        if ($i == 4 And $j > $i And $temp == 1)
        {
          echo $pl_name[$i] . " is in mutual reception with " . $pl_name[$j];
          check_for_aspect($i, $j, $longitude1);
          $pflag = True;
        }
        
        if ($i == 5 And $j > $i And $temp == 9)
        {
          echo $pl_name[$i] . " is in mutual reception with " . $pl_name[$j];
          check_for_aspect($i, $j, $longitude1);
          $pflag = True;
        }
        
        if ($i == 6 And $j > $i And $temp == 10)
        {
          echo $pl_name[$i] . " is in mutual reception with " . $pl_name[$j];
          check_for_aspect($i, $j, $longitude1);
          $pflag = True;
        }
        
        if ($i == 7 And $j > $i And $temp == 11)
        {
          echo $pl_name[$i] . " is in mutual reception with " . $pl_name[$j];
          check_for_aspect($i, $j, $longitude1);
          $pflag = True;
        }
        
        if ($i == 8 And $j > $i And $temp == 12)
        {
          echo $pl_name[$i] . " is in mutual reception with " . $pl_name[$j];
          check_for_aspect($i, $j, $longitude1);
          $pflag = True;
        }
        
        if ($i == 9 And $j > $i And $temp == 8)
        {
          echo $pl_name[$i] . " is in mutual reception with " . $pl_name[$j];
          check_for_aspect($i, $j, $longitude1);
          $pflag = True;
        }
      }
      
      if ($pflag == True)
      {
        echo "<br /><br />";
      }


      // display vocational analysis data - Find_oriental_planet
      $pflag = False;
      for ($i = 0; $i <= 9; $i++)
      {
        $sort[$i] = $longitude1[$i];
        $sortpos[$i] = $i;
      }

      for ($i = 0; $i <= 8; $i++)
      {
        for ($j = $i + 1; $j <= 9; $j++)
        {
          if ($sort[$j] > $sort[$i])
          {
            $temp = $sort[$i];
            $temp1 = $sortpos[$i];
            $sort[$i] = $sort[$j];
            $sortpos[$i] = $sortpos[$j];
            $sort[$j] = $temp;
            $sortpos[$j] = $temp1;
          }
        }
      }

      $sort[10] = $sort[1];
      $sortpos[10] = $sortpos[1];

      for ($i = 0; $i <= 9; $i++)
      {
        if ($sortpos[$i] == SE_SUN)
        {
          echo $pl_name[$sortpos[$i + 1]] . " is the oriental planet<br />";
          $pflag = True;
          break;
        }
      }

      if ($pflag == True)
      {
        echo "<br /><br />";
      }


      // display vocational analysis data - Find_quintiles
      // put the planets in the proper order for this feature - and add 3 "planets"
      for ($i = 0; $i <= 9; $i++)
      {
        $LL3[$i] = $longitude1[$i];
        $pname[$i] = $pl_name[$i];
      }
      
      $LL3[10] = $longitude1[SE_TNODE];
      $pname[10] = "Node";
      
      $LL3[11] = $hc1[1];
      $pname[11] = "Ascendant";

      $LL3[12] = $hc1[10];
      $pname[12] = "MC";

      $pflag = False;
      for ($i = 0; $i <= 11; $i++)
      {
        for ($j = $i + 1; $j <= 12; $j++)
        {
          $da = abs($LL3[$i] - $LL3[$j]);
          if ($da > 180)
          {
            $da = 360 - $da;
          }

          if ($da <= 74.5 And $da >= 69.5)
          {
            echo $pname[$i] . " is quintile " . $pname[$j] . "<br />";
            $pflag = True;
          }
        }
      }

      if ($pflag == True)
      {
        echo "<br /><br />";
      }


      // display vocational analysis data - Find_quindeciles
      $pflag = False;
      for ($i = 0; $i <= 11; $i++)
      {
        for ($j = $i + 1; $j <= 12; $j++)
        {
          $da = abs($LL3[$i] - $LL3[$j]);
          if ($da > 180)
          {
            $da = 360 - $da;
          }

          if ($da <= 167.5 And $da >= 162.5)
          {
            echo $pname[$i] . " is quindecile " . $pname[$j] . "<br />";
            $pflag = True;
          }
        }
      }

      if ($pflag == True)
      {
        echo "<br /><br />";
      }


      // display vocational analysis data - Find_peregrination
      $pflag = False;
      for ($i = 0; $i <= 9; $i++)
      {
        $peregrine = True;

        for ($j = 0; $j <= 9; $j++)
        {
          if ($i != $j)
          {
            $da = abs($longitude1[$i] - $longitude1[$j]);
            if ($da > 180)
            {
              $da = 360 - $da;
            }

            if ($da <= 7)
            {
              $peregrine = False;
            }
            elseif ($da <= 64 And $da >= 56)
            {
              $peregrine = False;
            }
            elseif ($da <= 97 And $da >= 83)
            {
              $peregrine = False;
            }
            elseif ($da <= 126 And $da >= 114)
            {
              $peregrine = False;
            }
            elseif ($da >= 173)
            {
              $peregrine = False;
            }
          }
        }

        if ($peregrine == True)
        {
          echo $pl_name[$i] . " is peregrine<br />";
          $pflag = True;
        }      
      }

      if ($pflag == True)
      {
        echo "<br /><br />";
      }


      // display vocational analysis data - Find_Moons_house
      $temp = floor($longitude1[1] / 30) + 1;

      echo "The Moon is in house number " . $house_pos1[1] . " - and in the sign (reigning need) of " . $sign_name[$temp] . "<br />";

      echo "<br /><br />";


      // display vocational analysis data - Find_planets_at_the_Aries_Point
      $pflag = False;
      for ($i = 0; $i <= 12; $i++)
      {
        if ($LL3[$i] <= 1.5 Or ($LL3[$i] >= 88.5 And $LL3[$i] < 91.5) Or ($LL3[$i] >= 178.5 And $LL3[$i] < 181.5) Or ($LL3[$i] >= 268.5 And $LL3[$i] < 271.5) Or $LL3[$i] >= 358.5)
        {
          echo $pname[$i] . " is at the Aries Point<br />";
          $pflag = True;
        }
      }

      if ($pflag == True)
      {
        echo "<br /><br />";
      }


      // display vocational analysis data - Find_stelliums
      // count the number of planets in each house
      $pflag = False;

      for ($i = 0; $i <= 9; $i++)
      {
        $temp = $house_pos1[$i];
        $nopih[$temp] = $nopih[$temp] + 1;
      }

      for ($i = 1; $i <= 12; $i++)
      {
        if ($nopih[$i] >= 4)
        {
          echo "House " . $i . " contains a stellium of planets<br />";
          $pflag = True;
        }
      }

      if ($pflag == True)
      {
        echo "<br /><br />";
      }


      // display vocational analysis data - Find_hits_to_Sun_Moon_midpoint
      // calculate all the natal midpoints
      for ($i = 0; $i <= 11; $i++)
      {
        for ($j = $i + 1; $j <= 12; $j++)
        {
          $mp[$i][$j] = ($LL3[$i] + $LL3[$j]) / 2;

          //this finds the nearer midpoint, which may not be what is optimum
          $diff1 = $mp[$i][$j] - $LL3[$i];
          $diff2 = $mp[$i][$j] - $LL3[$j];

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
      
      $pflag = False;
      for ($i = SE_MERCURY; $i <= 12; $i++)
      {
        $da = abs($LL3[$i] - $mp[SE_SUN][SE_MOON]);
        if ($da > 180)
        {
          $da = 360 - $da;
        }

        if ($da <= 2.5 Or ($da <= 92.5 And $da >= 87.5) Or ($da <= 167.5 And $da >= 162.5) Or $da >= 177.5)
        {
          echo $pname[$i] . " is configured with the Sun/Moon midpoint<br />";
          $pflag = True;
        }
      }

      if ($pflag == True)
      {
        echo "<br /><br />";
      }


      // display vocational analysis data - Find_hits_to_Jupiter_Pluto_midpoint
      $pflag = False;
      for ($i = 0; $i <= 12; $i++)
      {
        $da = abs($LL3[$i] - $mp[SE_JUPITER][SE_PLUTO]);
        if ($da > 180)
        {
          $da = 360 - $da;
        }

        if ($da <= 2.5 Or ($da <= 92.5 And $da >= 87.5) Or ($da <= 167.5 And $da >= 162.5) Or $da >= 177.5)
        {
          if ($i != SE_JUPITER And $i != SE_PLUTO)
          {
            echo $pname[$i] . " is configured with the Jupiter/Pluto midpoint<br />";
            $pflag = True;
          }
        }
      }

      if ($pflag == True)
      {
        echo "<br /><br />";
      }


      // display vocational analysis data - Find_all_dispositors
      for ($i = 0; $i <= 9; $i++)
      {
        $temp = floor($longitude1[$i] / 30) + 1;

        if ($temp == 1)
        {
          echo $pl_name[$i] . " is disposited by Mars<br />";
        }
        elseif ($temp == 2 Or $temp == 7)
        {
          echo $pl_name[$i] . " is disposited by Venus<br />";
        }
        elseif ($temp == 3 Or $temp == 6)
        {
          echo $pl_name[$i] . " is disposited by Mercury<br />";
        }
        elseif ($temp == 4)
        {
          echo $pl_name[$i] . " is disposited by Moon<br />";
        }
        elseif ($temp == 5)
        {
          echo $pl_name[$i] . " is disposited by Sun<br />";
        }
        elseif ($temp == 8)
        {
          echo $pl_name[$i] . " is disposited by Pluto<br />";
        }
        elseif ($temp == 9)
        {
          echo $pl_name[$i] . " is disposited by Jupiter<br />";
        }
        elseif ($temp == 10)
        {
          echo $pl_name[$i] . " is disposited by Saturn<br />";
        }
        elseif ($temp == 11)
        {
          echo $pl_name[$i] . " is disposited by Uranus<br />";
        }
        elseif ($temp == 12)
        {
          echo $pl_name[$i] . " is disposited by Neptune<br />";
        }
      }
      

      echo "<br /><br />";
      
      
      // display vocational analysis data - Find_AP_hits_to_natal_midpoints
      for ($i = 0; $i <= 11; $i++)
      {
        for ($j = $i + 1; $j <= 12; $j++)
        {
          $da = abs(0 - $mp[$i][$j]);
          if ($da > 180)
          {
            $da = 360 - $da;
          }

          if ($da <= 2.5 Or ($da <= 92.5 And $da >= 87.5) Or ($da <= 167.5 And $da >= 162.5) Or $da >= 177.5)
          {
            echo "AP is configured with the " . $pname[$i] . "/" . $pname[$j] . " midpoint<br />";
          }
        }
      }

      echo "<br />";

      // display vocational analysis data - Find_MC_hits_to_natal_midpoints
      for ($i = 0; $i <= 10; $i++)
      {
        for ($j = $i + 1; $j <= 11; $j++)     //exclude the MC
        {
          $da = abs($hc1[10] - $mp[$i][$j]);
          if ($da > 180)
          {
            $da = 360 - $da;
          }

          if ($da <= 2.5 Or ($da <= 92.5 And $da >= 87.5) Or ($da <= 167.5 And $da >= 162.5) Or $da >= 177.5)
          {
            echo "MC is configured with the " . $pname[$i] . "/" . $pname[$j] . " midpoint<br />";
          }
        }
      }

      echo "<br />";

      // display vocational analysis data - Find_Mercury_hits_to_natal_midpoints
      for ($i = 0; $i <= 11; $i++)
      {
        for ($j = $i + 1; $j <= 12; $j++)
        {
          if ($i != SE_MERCURY and $j != SE_MERCURY)    // do not allow a Mercury midpoint
          {
            $da = abs($longitude1[SE_MERCURY] - $mp[$i][$j]);
            if ($da > 180)
            {
              $da = 360 - $da;
            }

            if ($da <= 2.5 Or ($da <= 92.5 And $da >= 87.5) Or ($da <= 167.5 And $da >= 162.5) Or $da >= 177.5)
            {
              echo "Mercury is configured with the " . $pname[$i] . "/" . $pname[$j] . " midpoint<br />";
            }
          }
        }
      }


    echo '</td></tr>';
      echo '</table></center><br /><br />';
      //end of vocational analysis


      // display natal data - aspect table
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
            $orb = 3;
          }
          elseif ($i == SE_TNODE Or $j == SE_TNODE)
          {
            $orb = 3;
          }
          elseif ($i == SE_VERTEX Or $j == SE_VERTEX)
          {
            $orb = 3;
          }
          elseif ($i == SE_SUN Or $i == SE_MOON Or $j == SE_SUN Or $j == SE_MOON)
          {
            $orb = 8;
          }
          else
          {
            $orb = 6;
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



      $sql = "SELECT vocational_analysis FROM reports";
      $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);
      $row = mysqli_fetch_array($result);
      $count = $row[vocational_analysis] + 1;

      $sql = "UPDATE reports SET vocational_analysis = '$count'";
      $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);


//display the natal chart report
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
      echo "<center><font size='+1' color='#0000ff'><b>MY PHILOSOPHY OF ASTROLOGY</b></font></center>";

      $file = "natal_files/philo.txt";
      $fh = fopen($file, "r");
      $string = fread($fh, filesize($file));
      fclose($fh);

      $philo = nl2br($string);
      echo "<font size=2>" . $philo . "</font>";


      if ($ubt1 == 0)
      {
        //display rising sign interpretation
        //get header first
        echo "<center><font size='+1' color='#0000ff'><b>THE RISING SIGN OR ASCENDANT</b></font></center>";

        $file = "natal_files/ascsign.txt";
        $fh = fopen($file, "r");
        $string = fread($fh, filesize($file));
        fclose($fh);

        echo "<br>";
        echo "<font size=2>" . $string . "</font>";
        echo "<br><br><b>" . " YOUR ASCENDANT IS: <br><br>" . "</b>";

        $s_pos = floor($hc1[1] / 30) + 1;
        $phrase_to_look_for = $sign_name[$s_pos] . " rising";
        $file = "natal_files/rising.txt";
        $string = Find_Specific_Report_Paragraph($phrase_to_look_for, $file);
        $string = nl2br($string);

        echo "<font size=2>" . $string . "</font>";
      }


      //display planetary aspect interpretations
      //get header first
      echo "<center><font size='+1' color='#0000ff'><b>PLANETARY ASPECTS</b></font></center>";

      $file = "natal_files/aspect.txt";
      $fh = fopen($file, "r");
      $string = fread($fh, filesize($file));
      fclose($fh);

      $string = nl2br($string);
      $p_aspect_interp = $string;

      echo "<font size=2>" . $p_aspect_interp . "</font>";

      // get the individual power and harmony for each aspect
      include ('dyne_aspect_p_h.php');
      GetAspectPowerHarmony($longitude1, $house_pos1, $ubt1, $p_h, LAST_PLANET);

      $num_aspects = 0;
      $aspect_text = array();

      // loop through each planet
      for ($i = 0; $i <= LAST_PLANET + 1; $i++)     //was 8
      {
        for ($j = $i + 1; $j <= LAST_PLANET + 2; $j++)      //was 9
        {
          if (($i == 1 Or $i == SE_POF Or $i == SE_VERTEX Or $i == LAST_PLANET + 1 Or $i == LAST_PLANET + 2 Or $j == 1 Or $j == SE_POF Or $j == SE_VERTEX Or $j == LAST_PLANET + 1 Or $j == LAST_PLANET + 2) And $ubt1 == 1)
          {
            continue;     // do not allow Moon aspects or PoF or Vertex aspects if birth time is unknown
          }

          $da = Abs($longitude1[$i] - $longitude1[$j]);
          if ($da > 180)
          {
            $da = 360 - $da;
          }

          // set orb - 8 if Sun or Moon, 6 if not Sun or Moon
          if ($i == 0 Or $i == 1 Or $j == 0 Or $j == 1)
          {
            $orb = 8;
          }
          else
          {
            $orb = 6;
          }

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
          elseif ($da >= 180 - $orb)
          {
            $q = 6;
          }

          if ($q > 1)
          {
            if ($q == 2)
            {
              $aspect = " blending with ";
            }
            elseif ($q == 3 Or $q == 5)
            {
              $aspect = " harmonizing with ";
            }
            elseif ($q == 4 Or $q == 6)
            {
              $aspect = " discordant to ";
            }

            $phrase_to_look_for = $pl_name[$i] . $aspect . $pl_name[$j];
            $file = "natal_files/" . strtolower($pl_name[$i]) . ".txt";
            $string = Find_Specific_Report_Paragraph_ASPECTS($phrase_to_look_for, $file, $i, $j, $p_h);
            $string = nl2br($string);

            $aspect_text[0][$num_aspects] = $p_h[$i][$j];
            $aspect_text[1][$num_aspects] = $string;
        
            $num_aspects++;
          }
        }
      }

      //now sort the aspect interpretations according to power
      array_multisort($aspect_text[0], SORT_NUMERIC, SORT_DESC, $aspect_text[1], SORT_REGULAR);
  
      $p_aspect_interp = "";
      for ($i = 0; $i <= $num_aspects - 1; $i++)
      {
        $p_aspect_interp .= $aspect_text[1][$i];
      }

      echo "<font size='2'>" . $p_aspect_interp . "</font>";


      //display planet in sign interpretation
      //get header first
      echo "<center><font size='+1' color='#0000ff'><b>SIGN POSITIONS OF PLANETS</b></font></center>";

      $file = "natal_files/sign.txt";
      $fh = fopen($file, "r");
      $string = fread($fh, filesize($file));
      fclose($fh);

      $string = nl2br($string);
      $sign_interp = $string;

      // loop through each planet
      for ($i = 0; $i <= LAST_PLANET; $i++)     //was 6
      {
        $s_pos = floor($longitude1[$i] / 30) + 1;

        $deg = Reduce_below_30($longitude1[$i]);
        if ($ubt1 == 1 And $i == 1 And ($deg < 7.7 Or $deg > 22.3))
        {
          continue;     //if the Moon is too close to the beginning or the end of a sign, then do not include it
        }
        $phrase_to_look_for = $pl_name[$i] . " in";
        $file = "natal_files/sign_" . trim($s_pos) . ".txt";
        $string = Find_Specific_Report_Paragraph($phrase_to_look_for, $file);
        $string = nl2br($string);
        $sign_interp .= $string;
      }

      echo "<font size=2>" . $sign_interp . "</font>";


      if ($ubt1 == 0)
      {
        //display planet in house interpretation
        //get header first
        echo "<center><font size='+1' color='#0000ff'><b>HOUSE POSITIONS OF PLANETS</b></font></center>";

        $file = "natal_files/house.txt";
        $fh = fopen($file, "r");
        $string = fread($fh, filesize($file));
        fclose($fh);

        $string = nl2br($string);
        $house_interp = $string;

        // loop through each planet
        for ($i = 0; $i <= LAST_PLANET; $i++)       //was 9
        {
          $h_pos = floor($house_pos1[$i]);
          $phrase_to_look_for = $pl_name[$i] . " in";
          $file = "natal_files/house_" . trim($h_pos) . ".txt";
          $string = Find_Specific_Report_Paragraph($phrase_to_look_for, $file);
          $string = nl2br($string);
          $house_interp .= $string;
        }

        echo "<font size=2>" . $house_interp . "</font>";
      }


      echo "<center><font size='+1' color='#0000ff'><b>SABIAN SYMBOL POSITIONS OF PLANETS</b></font></center>";

      $file = "natal_files/sabian.txt";
      $fh = fopen($file, "r");
      $string = fread($fh, filesize($file));
      fclose($fh);

      $string = nl2br($string);
      $sign_interp = $string;

      // loop through each planet
      for ($i = 0; $i <= 9; $i++)
      {
        $s_pos = floor($longitude1[$i] / 30) + 1;

        $deg = floor(Reduce_below_30($longitude1[$i])) + 1;   //add 1 to degree
        if ($ubt1 == 1 And $i == 1)
        {
          continue;     //do not include the Moon for an unknown birth time
        }
        $phrase_to_look_for = trim($name_of_sign[$s_pos]) . " " . $deg;
        $file = "natal_files/sabian_" . trim($s_pos) . ".txt";

        $string = Find_Specific_Report_Paragraph($phrase_to_look_for, $file);
        $string = nl2br($string);
        $sign_interp .= $pl_name[$i] . " in " . $string;
      }

      //Ascendant
      if ($ubt1 == 0)
      {
        $s_pos = floor($longitude1[LAST_PLANET + 1] / 30) + 1;

        $deg = floor(Reduce_below_30($longitude1[LAST_PLANET + 1])) + 1;    //add 1 to degree
        $phrase_to_look_for = trim($name_of_sign[$s_pos]) . " " . $deg;
        $file = "natal_files/sabian_" . trim($s_pos) . ".txt";

        $string = Find_Specific_Report_Paragraph($phrase_to_look_for, $file);
        $string = nl2br($string);
        $sign_interp .= "Ascendant in " . $string;
      }

      //MC
      if ($ubt1 == 0)
      {
        $s_pos = floor($longitude1[LAST_PLANET + 10] / 30) + 1;

        $deg = floor(Reduce_below_30($longitude1[LAST_PLANET + 10])) + 1;   //add 1 to degree
        $phrase_to_look_for = trim($name_of_sign[$s_pos]) . " " . $deg;
        $file = "natal_files/sabian_" . trim($s_pos) . ".txt";

        $string = Find_Specific_Report_Paragraph($phrase_to_look_for, $file);
        $string = nl2br($string);
        $sign_interp .= "MC in " . $string;
      }

      echo "<font size=2>" . $sign_interp . "</font>";


      //display closing
      echo "<br><center><font size='+1' color='#0000ff'><b>CLOSING COMMENTS</b></font></center>";

      if ($ubt1 == 1)
      {
        $file = "natal_files/closing_unk.txt";
      }
      else
      {
        $file = "natal_files/closing.txt";
      }
      $fh = fopen($file, "r");
      $string = fread($fh, filesize($file));
      fclose($fh);

      $closing = nl2br($string);
      echo "<font size=2>" . $closing . "</font>";

      echo '</font></td></tr>';
      echo '</table></center>';
      echo "<br /><br />";

      include ('footer.html');
      exit();
      }
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


Function Find_MC_ruler_aspects_to_natal_planets($longitude1, $MC_ruler_pnum, $pl_name)
{
  $pflag = False;
  for ($i = 0; $i <= 9; $i++)
  {
    if ($i != $MC_ruler_pnum)
    {
      $da = abs($longitude1[$MC_ruler_pnum] - $longitude1[$i]);
      if ($da > 180)
      {
        $da = 360 - $da;
      }

      if ($da <= 7)
      {
        echo $pl_name[$MC_ruler_pnum] . " is conjunct " . $pl_name[$i] . "<br />";
        $pflag = True;
      }
      elseif ($da <= 64 And $da >= 56)
      {
        echo $pl_name[$MC_ruler_pnum] . " sextiles " . $pl_name[$i] . "<br />";
        $pflag = True;
      }
      elseif ($da <= 97 And $da >= 83)
      {
        echo $pl_name[$MC_ruler_pnum] . " squares " . $pl_name[$i] . "<br />";
        $pflag = True;
      }
      elseif ($da <= 126 And $da >= 114)
      {
        echo $pl_name[$MC_ruler_pnum] . " trines " . $pl_name[$i] . "<br />";
        $pflag = True;
      }
      elseif ($da >= 173)
      {
        echo $pl_name[$MC_ruler_pnum] . " opposes " . $pl_name[$i] . "<br />";
        $pflag = True;
      }
    }
  }

  if ($pflag == True)
  {
    echo "<br /><br />";
  }
}


Function check_for_aspect($i, $j, $longitude1)
{
  $q = 0;
  $da = abs($longitude1[$i] - $longitude1[$j]);
  if ($da > 180)
  {
    $da = 360 - $da;
  }

  if ($da <= 7)
  {
    $q = 1;
  }
  elseif ($da <= 64 And $da >= 56)
  {
    $q = 2;
  }
  elseif ($da <= 74.5 And $da >= 69.5)
  {
    $q = 13;
  }
  elseif ($da <= 97 And $da >= 83)
  {
    $q = 3;
  }
  elseif ($da <= 126 And $da >= 114)
  {
    $q = 4;
  }
  elseif ($da <= 167.5 And $da >= 162.5)
  {
    $q = 16;
  }
  elseif ($da >= 173)
  {
    $q = 5;
  }
  
  if ($q > 0)
  {
    echo " - these planets are in aspect (" . $q . ")<br />";
  }
  else
  {
    echo " - no aspect between them<br />";
  }
}


Function Find_Specific_Report_Paragraph_ASPECTS($phrase_to_look_for, $file, $x, $y, $p_h)
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
          if ($p_h[$y][$x] == 0)
          {
            $t = " (power = " . sprintf("%.2f", $p_h[$x][$y]) . " and this aspect is neutral)";
          }
          if ($p_h[$y][$x] > 0)
          {
            $t = " (power = " . sprintf("%.2f", $p_h[$x][$y]) . " and this aspect is harmonious = " . sprintf("%.2f", $p_h[$y][$x]) . ")";
          }
          if ($p_h[$y][$x] < 0)
          {
            $t = " (power = " . sprintf("%.2f", $p_h[$x][$y]) . " and this aspect is discordant = " . sprintf("%.2f", $p_h[$y][$x]) . ")";
          }

          $string .= "<b>" . left($file_array[$i], strlen($file_array[$i]) - 1) . "</b>" . $t . "\n";
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

?>
