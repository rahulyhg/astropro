<?php
  include ('../accesscontrol.php');

  if ($is_logged_in == False) { exit(); }

  require_once ('../../../mysqli_connect_online_calcs_db_MYSQLI.php');
  require_once ('../../../my_functions_MYSQLI.php');

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


    include ('header_progressions_moon_declinations.html');       //here because of setting cookies above


    $ew1_txt = "e";
    if ($ew1 < 0) { $ew1_txt = "w"; }

    $ns1_txt = "s";
    if ($ns1 > 0) { $ns1_txt = "n"; }


    // get all variables from form - transit date
    $num_years = intval(safeEscapeString($conn, $_POST["num_years"]));

    $start_age = 0;

    if ($num_years < 1 Or $num_years > 99) { $num_years = 80; }


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

//Person 1 calculations
      // Unset any variables not initialized elsewhere in the program
      unset($PATH,$out,$pl_name,$longitude1);

      $inmonth = $month1;
      $inday = $day1;
      $inyear = $year1;

      $inhours = $hour1;
      $inmins = $minute1;
      $insecs = "0";

      $intz = $timezone1;

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

      exec ("swetest -edir$sweph -b$utdatenow -ut$utnow -p01 -eswe -fl -g, -head", $out);

      // Each line of output data from swetest is exploded into array $row, giving these elements:
      // 0 = longitude
      foreach ($out as $key => $line)
      {
        $row = explode(',',$line);
        $longitude1[$key] = $row[0];
      };


      include("constants_eng.php");          //this is here because we must rename the planet names


//Progressed planet calculations
      // Unset any variables not initialized elsewhere in the program
      unset($out,$declination2);

      $name2 = "Progressed Moon Declinations";

    // get progressed birthday
      $n_sun = $longitude1[0];

      $start_year = $year1 + $start_age;

      $birth_JD = gregoriantojd($month1, $day1, $year1) - 0.5;        // find Julian day for birth date at midnight.
      $start_JD = gregoriantojd($month1, $day1, $start_year) - 0.5;   // find Julian day for start of relationship at midnight.

      $birth_JD = $birth_JD + (($inhours + ($inmins / 60)) / 24);
      $start_JD = $start_JD + (($inhours + ($inmins / 60)) / 24);

      $days_alive = $start_JD - $birth_JD;
      $prog_time_to_add = $days_alive / 365.25;
      $jd_to_use = $birth_JD + $prog_time_to_add;

      //here we must loop through all the time periods we want to calculate
      $num_loops = $num_years * 6 + 2;            //e.g., 80 years is 80 days, so let's do calculations 6 times per day (every 4 hours)

      for ($i = 1; $i <= $num_loops; $i++)
      {
        $jd_here = $jd_to_use + (($i - 1) * 4 / 24);
        //$step_size = 1 / 6;
        //exec ("swetest -edir$sweph -bj$jd_here -ut -p1 -eswe -fd -n$num_loops -s$step_size -g, -head", $out);
        exec ("swetest -edir$sweph -bj$jd_here -ut -p1 -eswe -fd -g, -head", $out);
      }

      //now get all the calculate progressed Moon declinations into the declination2[] array
      foreach ($out as $key => $line)
      {
        $row = explode(',',$line);
        $declination2[$key] = $row[0];        //this is really declination
      };


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

      $name_without_slashes = stripslashes($name1);

      $line1 = $name_without_slashes . ", born " . strftime("%B %d, %Y at %H:%M (time zone = GMT $tz1 hours)", mktime($hour1, $minute1, $secs, $month1, $day1, $year1));
      $line1 = $line1 . " at " . $long_deg1 . $ew1_txt . sprintf("%02d", $long_min1) . " and " . $lat_deg1 . $ns1_txt . sprintf("%02d", $lat_min1);

      $_SESSION['declination2'] = $declination2;


      echo "<center>";
      echo "<img border='0' src='draw_graph.php?sy=$start_year&ny=$num_years&l1=$line1'>";
      echo "</center>";
      echo "<br>";


      include ('footer.html');
    }
  }

?>
