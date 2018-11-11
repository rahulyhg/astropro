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

    include ('header_natal_midpoints.html');        //here because of setting cookies above

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






//put natal planets in $longitude2
      $name2 = "Natal planets are in inner wheel - natal midpoints are in outer wheel";

      for ($i = 0; $i <= LAST_PLANET + 12; $i++)

      {

        $longitude2[$i] = $longitude1[$i];

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
      $line1 = $name_without_slashes . ", born " . strftime("%A, %B %d, %Y at %H:%M (time zone = GMT $tz1 hours)", mktime($hour1, $minute1, $secs, $month1, $day1, $year1));
      $line1 = $line1 . " at " . $long_deg1 . $ew1_txt . sprintf("%02d", $long_min1) . " and " . $lat_deg1 . $ns1_txt . sprintf("%02d", $lat_min1);

      $line2 = $name2;

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


      $rx2 = "";

      for ($i = 0; $i <= SE_TNODE; $i++)
      {
        $rx2 .= " ";
      }

      // to make GET string shorter
      for ($i = 0; $i <= LAST_PLANET; $i++)
      {
        $L1[$i] = $longitude1[$i];
        $L2[$i] = $longitude2[$i];
      }

      for ($i = 1; $i <= 12; $i++)
      {
        $hc1[$i] = $longitude1[LAST_PLANET + $i];
      }

      for ($i = 1; $i <= 12; $i++)
      {
        $hc2[$i] = ($i - 1) * 30;
      }

      $hc2[13] = 0;

    $hc2[1] = $longitude2[LAST_PLANET + 1];
    $hc2[10] = $longitude2[LAST_PLANET + 10];

    $L2[LAST_PLANET + 1] = $longitude2[LAST_PLANET + 1];
    $L2[LAST_PLANET + 2] = $longitude2[LAST_PLANET + 10];



// no need to urlencode unless perhaps magic quotes is ON (??)
    $_SESSION['nmp_p1'] = $L1;
    $_SESSION['nmp_p2'] = $L2;
    $_SESSION['hc1'] = $hc1;
    $_SESSION['house_pos1'] = $house_pos1;
    $_SESSION['hc2'] = $hc2;
 

    //prepare all the single planets into an array for later use (sorting and listing)
    $all_bodies = array();
    $cnt = 0;
 
    for ($i = 0; $i <= LAST_PLANET; $i++)
    {
      $all_bodies[0][$cnt] = left($pl_name[$i], 3) . "&nbsp;&nbsp;&nbsp;&nbsp;";
      $all_bodies[1][$cnt] = $longitude1[$i];

      $cnt++;
    }

    $all_bodies[0][SE_POF] = "PoF&nbsp;&nbsp;&nbsp;&nbsp;";
    
    //include the Ascendant
    $all_bodies[0][$cnt] = "Asc&nbsp;&nbsp;&nbsp;&nbsp;";
    $all_bodies[1][$cnt] = $hc1[1];

    $cnt++;

    //include the Midheaven
    $all_bodies[0][$cnt] = "MC &nbsp;&nbsp;&nbsp;&nbsp;";
    $all_bodies[1][$cnt] = $hc1[10];

    $cnt++;


//calculate various natal midpoints
    for ($i = 0; $i <= LAST_PLANET + 1; $i++)
    {
      for ($j = 0; $j <= LAST_PLANET + 2; $j++)
      {
        $mp[$i][$j] = ($L2[$i] + $L2[$j]) / 2;

        //this finds the nearer midpoint, which may not be what is optimum
        $diff1 = $mp[$i][$j] - $L2[$i];
        $diff2 = $mp[$i][$j] - $L2[$j];

        if (abs($diff1) > 90 Or abs($diff2) > 90) { $mp[$i][$j] = $mp[$i][$j] + 180; }

        if ($mp[$i][$j] >= 360) { $mp[$i][$j] = $mp[$i][$j] - 360; }

        if ($j > $i)
        {
          $all_bodies[0][$cnt] = left($all_bodies[0][$i], 3) . "/" . left($all_bodies[0][$j], 3);

          $all_bodies[1][$cnt] = $mp[$i][$j];

          $cnt++;        
        }
      }
    }

    $all_bodies_count = $cnt - 1;     //this number is the highest index of the $all_bodies[] array
    
    $_SESSION['mp'] = $mp;

    $wheel_width = 800;
    $wheel_height = $wheel_width + 50;    //includes space at top of wheel for header

    echo "<center>";

    echo "<img border='0' src='nmp_wheel_2.php?rx1=$rx1&rx2=$rx2&ubt1=$ubt1&ubt2=$ubt2&l1=$line1&l2=$line2' width='$wheel_width' height='$wheel_height'>";
    echo "<br><br>";

    //31 October 2010
    $tempXX = $longitude1[LAST_PLANET + 2];
  
    $longitude1[LAST_PLANET + 2] = $hc1[10];
    
    $_SESSION['nmpL1'] = $longitude1;

    //30 degree line
    $L2_height = Detect_num_same_deg_planets_max($longitude1);
    $L2_height = ($L2_height + 1) * 50;

    echo "30-degree line<br>";
    echo "<img border='0' src='deg_line.php?rx1=$rx1' width='740' height='$L2_height'>";

    //90 degree line
    $L2_height = Detect_num_same_deg_planets_max_90($longitude1);
    $L2_height = 50 + ($L2_height * 18);

    echo "<br><br>90-degree line<br>";
    echo "<img border='0' src='deg_line_90.php?rx1=$rx1' width='740' height='$L2_height'>";

    //45 degree line
    $L2_height = Detect_num_same_deg_planets_max_45($longitude1);
    $L2_height = 50 + ($L2_height * 18);

    echo "<br><br><br><br>45-degree line<br>";
    echo "<img border='0' src='deg_line_45.php?rx1=$rx1' width='740' height='$L2_height'>";

    //midpoint tree (first 7 planets)
    $L2_height = Detect_max_num_of_midpoints(0, 6, $L2, $mp, LAST_PLANET, $ubt1);
    $L2_height = 50 + ($L2_height * 18);

    echo "<br><br><br><br>Midpoint trees (90-degree modulus)<br>";
    echo "<img border='0' src='midpoint_tree1.php?ubt1=$ubt1' width='920' height='$L2_height'>";
    
    //midpoint tree (second 7 planets)
    $L2_height = Detect_max_num_of_midpoints(7, LAST_PLANET + 2, $L2, $mp, LAST_PLANET, $ubt1);
    $L2_height = 50 + ($L2_height * 18);

    echo "<img border='0' src='midpoint_tree2.php?ubt1=$ubt1' width='920' height='$L2_height'>";


    $longitude1[LAST_PLANET + 2] = $tempXX;
    echo "<br><br><br></center>";


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


    $asp_name[1] = "Conjunction";
    $asp_name[2] = "Opposition";
    $asp_name[3] = "Trine";
    $asp_name[4] = "Square";
    $asp_name[5] = "Quincunx";
    $asp_name[6] = "Sextile";


// display aspect table - natal planet to midpoint
    echo '<center><table width="40%" cellpadding="0" cellspacing="0" border="0">';

    echo '<tr>';
    echo "<td><font color='#0000ff'><b> Natal Planet</b></font></td>";
    echo "<td><font color='#0000ff'><b> Hard Aspects </b></font></td>";
    echo "<td><font color='#0000ff'><b> Natal Midpoint</b></font></td>";
    echo "<td><font color='#0000ff'><b> Orb </b></font></td>";
    echo '</tr>';


    // include Ascendant and MC
    $L2[LAST_PLANET + 1] = $hc1[1];

    $L2[LAST_PLANET + 2] = $hc1[10];

    $pl_name[LAST_PLANET + 1] = "Ascendant";
    $pl_name[LAST_PLANET + 2] = "Midheaven";

    for ($k = 0; $k <= LAST_PLANET + 2; $k++)         //natal planet
    {
      if ($k == SE_LILITH Or $k == SE_POF Or $k == SE_VERTEX) { continue; }     //don't do every midpoint combination

      echo "<tr><td colspan='4'>&nbsp;</td></tr>";

      for ($i = 0; $i <= LAST_PLANET + 1; $i++)         //natal midpoint
      {
        if ($i == SE_LILITH Or $i == SE_POF Or $i == SE_VERTEX) { continue; }   //don't do every midpoint combination

        for ($j = $i + 1; $j <= LAST_PLANET + 2; $j++)      //natal midpoint
        {
          if ($j == SE_LILITH Or $j == SE_POF Or $j == SE_VERTEX) { continue; }   //don't do every midpoint combination

          if ($k == $i Or $k == $j) { continue; }       //don't allow the same planet to appear more than once
          
          if ($ubt1 == 1 And ($i > SE_TNODE Or $j > SE_TNODE Or $k > SE_TNODE)) { continue; }

          $q = 0;
          $da = Abs($mp[$i][$j] - $L2[$k]);

          if ($da > 180) { $da = 360 - $da; }

          $orb = 1.0001;

          // is there an aspect within orb?
          if ($da <= $orb)
          {
            $q = 1;
            $dax = $da;
          }
          elseif (($da <= (90 + $orb)) And ($da >= (90 - $orb)))
          {
            $q = 4;
            $dax = $da - 90;
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
            echo "<td>" . $pl_name[$k] . "</td>";
            echo "<td>" . $asp_name[$q] . "</td>";
            echo "<td>" . $pl_name[$i] . "/" . $pl_name[$j] . "</td>";
            echo "<td>" . sprintf("%.2f", abs($dax)) . "</td>";
            echo '</tr>';
          }
        }
      }
    }

    echo '</table></center>';
    echo "<br /><br />";


// display aspect table - midpoint to natal planet
    echo '<center><table width="40%" cellpadding="0" cellspacing="0" border="0">';

    echo '<tr>';
    echo "<td><font color='#0000ff'><b> Natal Midpoint</b></font></td>";
    echo "<td><font color='#0000ff'><b> Hard Aspects </b></font></td>";
    echo "<td><font color='#0000ff'><b> Natal Planet</b></font></td>";
    echo "<td><font color='#0000ff'><b> Orb </b></font></td>";
    echo '</tr>';

    $num_midpoint_aspects = 0;

    $nmp = array();     //to hold the midpoints that are actually making aspects to natal planets

    $nmp_already_processed = array();     //1 = we have already processed this midpoint

    for ($i = 0; $i <= LAST_PLANET + 1; $i++)         //natal midpoint
    {
      if ($i == SE_LILITH Or $i == SE_POF Or $i == SE_VERTEX) { continue; }     //don't do every midpoint combination

      echo "<tr><td colspan='4'>&nbsp;</td></tr>";

      for ($j = $i + 1; $j <= LAST_PLANET + 2; $j++)      //natal midpoint
      {
        if ($j == SE_LILITH Or $j == SE_POF Or $j == SE_VERTEX) { continue; }   //don't do every midpoint combination

        for ($k = 0; $k <= LAST_PLANET + 2; $k++)       //natal planet
        {
          if ($k == SE_LILITH Or $k == SE_POF Or $k == SE_VERTEX) { continue; }   //don't do every midpoint combination

          if ($k == $i Or $k == $j) { continue; }       //don't allow the same planet to appear more than once

          if ($ubt1 == 1 And ($i > SE_TNODE Or $j > SE_TNODE Or $k > SE_TNODE)) { continue; }

          $q = 0;
          $da = abs($mp[$i][$j] - $L2[$k]);

          if ($da > 180) { $da = 360 - $da; }

          $orb = 1.0001;

          // is there an aspect within orb?
          if ($da <= $orb)
          {
            $q = 1;
            $dax = $da;
          }
          elseif (($da <= (90 + $orb)) And ($da >= (90 - $orb)))
          {
            $q = 4;
            $dax = $da - 90;
          }
          elseif ($da >= (180 - $orb))
          {
            $q = 2;
            $dax = 180 - $da;
          }

          if ($q > 0)
          {
            // aspect exists
            if ($nmp_already_processed[$i][$j] == 0)
            {
              $nmp[0][$num_midpoint_aspects] = $i;
              $nmp[1][$num_midpoint_aspects] = $j;
              $nmp[2][$num_midpoint_aspects] = $mp[$i][$j];

              $nmp_already_processed[$i][$j] = 1;

              $num_midpoint_aspects++;        //save the midpoint data
            }
            
            echo '<tr>';
            echo "<td>" . $pl_name[$i] . "/" . $pl_name[$j] . "</td>";
            echo "<td>" . $asp_name[$q] . "</td>";
            echo "<td>" . $pl_name[$k] . "</td>";
            echo "<td>" . sprintf("%.2f", abs($dax)) . "</td>";
            echo '</tr>';
          }
        }
      }
    }


    //$num_midpoint_aspects--;        //get proper count of number of aspects

    $_SESSION['num_midpoint_aspects'] = $num_midpoint_aspects;

    $_SESSION['nmp'] = $nmp;
    
    echo '</table></center>';
    echo "<br /><br />";


//now find the house position of each natal midpoint that makes an aspect
    for ($x = 1; $x <= 12; $x++)
    {
      for ($y = 0; $y <= $num_midpoint_aspects; $y++)
      {
        $pl = $nmp[2][$y] + (1 / 36000);

        if ($x < 12 And $longitude2[$x + LAST_PLANET] > $longitude2[$x + LAST_PLANET + 1])
        {
          if (($pl >= $longitude2[$x + LAST_PLANET] And $pl < 360) Or ($pl < $longitude2[$x + LAST_PLANET + 1] And $pl >= 0))
          {
            $house_pos_nmp[$y] = $x;

            continue;
          }
        }

        if ($x == 12 And ($longitude2[$x + LAST_PLANET] > $longitude2[LAST_PLANET + 1]))
        {
          if (($pl >= $longitude2[$x + LAST_PLANET] And $pl < 360) Or ($pl < $longitude2[LAST_PLANET + 1] And $pl >= 0))
          {
            $house_pos_nmp[$y] = $x;
          }

          continue;
        }

        if (($pl >= $longitude2[$x + LAST_PLANET]) And ($pl < $longitude2[$x + LAST_PLANET + 1]) And ($x < 12))
        {
          $house_pos_nmp[$y] = $x;

          continue;
        }

        if (($pl >= $longitude2[$x + LAST_PLANET]) And ($pl < $longitude2[LAST_PLANET + 1]) And ($x == 12))
        {
          $house_pos_nmp[$y] = $x;
        }
      }
    }

    $_SESSION['house_pos_nmp'] = $house_pos_nmp;
  

//display all natal midpoints in their 0-90 deg format - and sorted
    $interval = ($all_bodies_count + 1) / 3;

    for ($i = 0; $i <= $all_bodies_count; $i++)
    {
      $all_bodies[1][$i] = Reduce_below_90($all_bodies[1][$i]);     //reduce all longitudes to 0 - 90 deg
    }
    
    array_multisort($all_bodies[1], SORT_NUMERIC, $all_bodies[0], SORT_REGULAR);
?>

<style type="text/css">
.td_special
{
  font-family: courier new;
  font-size: 12px;
}
</style>

<?php
    echo "<center><font size='+1' color='#0000ff'><b>NATAL MIDPOINT SORT (modulus = 90)</b></font></center><br />";
    echo '<center><table width="62%" cellpadding="0" cellspacing="0" border="0">';

    for ($i = 0; $i <= $interval - 1; $i++)
    {
      echo "<tr>";

      if ($i <= $all_bodies_count)
      {
        echo "<td class='td_special'>" . $all_bodies[0][$i] . "&nbsp;= " . Convert_Longitude_no_secs_no_signs($all_bodies[1][$i]) . "</td>";
      }
      else
      {
        echo "<td>&nbsp;</td>";
      }

      if ($i + 1 <= $all_bodies_count)

      {
        echo "<td class='td_special'>" . $all_bodies[0][$i + $interval] . "&nbsp;= " . Convert_Longitude_no_secs_no_signs($all_bodies[1][$i + $interval]) . "</td>";
      }
      else
      {
        echo "<td>&nbsp;</td>";
      }

      if ($i + 2 <= $all_bodies_count)
      {
        echo "<td class='td_special'>" . $all_bodies[0][$i + 2 * $interval] . "&nbsp;= " . Convert_Longitude_no_secs_no_signs($all_bodies[1][$i + 2 * $interval]) . "</td>";
      }
      else
      {
        echo "<td>&nbsp;</td>";
      }

      echo "</tr>";
    }

    echo '</table></center><br /><br />';

    echo '</font></td></tr>';
    echo '</table></center>';


//display all natal midpoints
    echo "<center><font size='+1' color='#0000ff'><b>NATAL MIDPOINTS</b></font></center><br />";
    echo '<center><table width="62%" cellpadding="0" cellspacing="0" border="0">';

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

    echo '</font></td></tr>';

    echo '</table></center>';


    // update count
    $sql = "SELECT natal_midpoints FROM reports";
    $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);
    $row = mysqli_fetch_array($result);
    $count = $row[natal_midpoints] + 1;

    $sql = "UPDATE reports SET natal_midpoints = '$count'";
    $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);


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


Function Reduce_below_45($longitude)
{
  $lng = $longitude;

  while ($lng >= 45)
  {
    $lng = $lng - 45;
  }

  return $lng;
}


Function Reduce_below_90($longitude)
{
  $lng = $longitude;

  while ($lng >= 90)
  {
    $lng = $lng - 90;
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

  if ($deg < 10) { $deg = "0" . $deg; }

  if ($min < 10) { $min = "0" . $min; }

  if ($full_sec < 10) { $full_sec = "0" . $full_sec; }

  return $deg . " " . $signs[$sign_num] . " " . $min . "' " . $full_sec . chr(34);
}


Function Convert_Longitude_no_secs($longitude)
{
  $signs = array (0 => 'Ari', 'Tau', 'Gem', 'Can', 'Leo', 'Vir', 'Lib', 'Sco', 'Sag', 'Cap', 'Aqu', 'Pis');

  $sign_num = floor($longitude / 30);
  $pos_in_sign = $longitude - ($sign_num * 30);
  $deg = floor($pos_in_sign);
  $full_min = ($pos_in_sign - $deg) * 60;

  if ($deg < 10) { $deg = "0" . $deg; }

  $fmin = sprintf("%.0f", $full_min);
  if ($fmin < 10) { $fmin = "0" . $fmin; }

  return $deg . " " . $signs[$sign_num] . " " . $fmin;
}


Function Convert_Longitude_no_secs_no_signs($longitude)
{
  $deg = floor($longitude);
  $full_min = ($longitude - $deg) * 60;

  if ($deg < 10) { $deg = "0" . $deg; }

  $fmin = sprintf("%.0f", $full_min);
  if ($fmin < 10) { $fmin = "0" . $fmin; }

//  return $deg . "d " . $fmin . "m";
  return $deg . " " . $fmin;
}


Function mid($midstring, $midstart, $midlength)
{
  return(substr($midstring, $midstart-1, $midlength));
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


Function Detect_num_same_deg_planets_max_45($longitude1)
{
  $deg_filled_idx = array();
  
  for ($i = 0; $i <= 45; $i++)
  {
    $deg_filled_idx[$i] = 0;      //initialize
  }
  
  $cnt = 0;
  for ($i = 0; $i <= LAST_PLANET + 2; $i++)
  {
    $pl_pos = floor(Reduce_below_45($longitude1[$i]));
    $deg_filled_idx[$pl_pos]++;
  }
  
  $mx = 0;
  for ($i = 0; $i <= 45; $i++)
  {
    if ($deg_filled_idx[$i] > $mx)
    {
      $mx = $deg_filled_idx[$i];
    }
  }

  return $mx;
}


Function Detect_num_same_deg_planets_max_90($longitude1)
{
  $deg_filled_idx = array();
  
  for ($i = 0; $i <= 90; $i++)
  {
    $deg_filled_idx[$i] = 0;      //initialize
  }
  
  $cnt = 0;
  for ($i = 0; $i <= LAST_PLANET + 2; $i++)
  {
    $pl_pos = floor(Reduce_below_90($longitude1[$i]));
    $deg_filled_idx[$pl_pos]++;
  }
  
  $mx = 0;
  for ($i = 0; $i <= 90; $i++)
  {
    if ($deg_filled_idx[$i] > $mx)
    {
      $mx = $deg_filled_idx[$i];
    }
  }

  return $mx;
}


Function Detect_max_num_of_midpoints($p1, $p2, $L2, $mp, $last_planet, $ubt1)
{
//define(SE_LILITH, 11);
//define(SE_TNODE, 12);
//define(SE_POF, 13);
//define(SE_VERTEX, 14);
//define(LAST_PLANET, 14);

  $mx = -99;
  
  for ($k = $p1; $k <= $p2; $k++)         // natal planet
  {
    $cnt = 0;
    
    if ($k == 11 Or $k == 13 Or $k == 14) { continue; }     // don't do every midpoint combination

    for ($i = 0; $i <= $last_planet + 1; $i++)          // natal midpoint
    {
      if ($i == 11 Or $i == 13 Or $i == 14) { continue; }   // don't do every midpoint combination

      for ($j = $i + 1; $j <= $last_planet + 2; $j++)     // natal midpoint
      {
        if ($j == 11 Or $j == 13 Or $j == 14) { continue; }   // don't do every midpoint combination
      
        if ($k == $i Or $k == $j) { continue; }         // don't allow the same planet to appear more than once
        
        if ($ubt1 == 1 And ($i > 12 Or $j > 12 Or $k > 12)) { continue; }

        $q = 0;
        $da = Abs($mp[$i][$j] - $L2[$k]);

        if ($da > 180) { $da = 360 - $da; }

        $orb = 1.0001;

        // is there an aspect within orb?
        if ($da <= $orb)
        {
          $cnt++;
        }
        elseif (($da <= (90 + $orb)) And ($da >= (90 - $orb)))
        {
          $cnt++;
        }
        elseif ($da >= (180 - $orb))
        {
          $cnt++;
        }
      }
    }
    
    if ($cnt > $mx) { $mx = $cnt; }
  }
  
  return $mx;
}

?>
