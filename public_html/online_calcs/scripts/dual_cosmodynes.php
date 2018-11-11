<?php
  include ('../accesscontrol.php');

  if ($is_logged_in == False) { exit(); }

  $no_interps = False;        //set this to False when you want interpretations

  include ('header_dual_c.html');

  echo "<center>";

  // connect to and point to the proper database
  require_once ('../../../mysqli_connect_online_calcs_db_MYSQLI.php');
  require_once ('../../../my_functions_MYSQLI.php');
  require ('dual_c_calcs.php');
  require ('dual_c_mrs.php');
  require ('dual_c_display_natal_data.php');
  require ('dual_c_display_results.php');

  // get ID1 and ID2
  $id[1] = safeEscapeString($conn, $_POST["id1"]);
  $id[2] = safeEscapeString($conn, $_POST["id2"]);

  $username = $_SESSION['username'];

  for ($xx = 1; $xx <= 2; $xx++)
  {
    //fetch all data for this record
    $sql = "SELECT * FROM birth_info WHERE ID='$id[$xx]' And entered_by='$username'";
    $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);
    $row = mysqli_fetch_array($result);
    $num_rows = MYSQLI_NUM_rows($result);

    if ($xx == 1)
    {
      $existing_name1 = $row['name'];
    }
    else
    {
      $existing_name2 = $row['name'];
    }

    if ($num_rows == 0)
    {
      echo "<FONT color='#ff0000' SIZE='5' FACE='Arial'><b>I could not find the record(s) you specified. Thank you.</b></font><br><br><br>";
      mysqli_close($conn);
      echo "</center>";
      include ('footer.html');
      exit();
    }
    else
    {
      //assign data from database to local variables
      $secs = "0";
      if ($row['timezone'] < 0)
      {
        $tz = $row['timezone'];
      }
      Else
      {
        $tz = "+" . $row['timezone'];
      }

      if ($xx == 1)
      {
        $header1 = '<b>Data for ' . strftime("%A, %B %d, %Y at %X (time zone = GMT $tz hours)</b><br><br><br>\n", mktime($row['hour'], $row['minute'], $secs, $row['month'], $row['day'], $row['year']));
        $line1 = $existing_name1 . ", born " . strftime("%A, %B %d, %Y at %X (time zone = GMT $tz hours)</b>", mktime($row['hour'], $row['minute'], $secs, $row['month'], $row['day'], $row['year']));

        CalculatePositions($id[1], $longitude, $declination, $house_pos);

        // assign data from database into arrays for display
        $pl_name1[0] = "Sun";
        $pl_name1[1] = "Moon";
        $pl_name1[2] = "Mercury";
        $pl_name1[3] = "Venus";
        $pl_name1[4] = "Mars";
        $pl_name1[5] = "Jupiter";
        $pl_name1[6] = "Saturn";
        $pl_name1[7] = "Uranus";
        $pl_name1[8] = "Neptune";
        $pl_name1[9] = "Pluto";
        $pl_name1[10] = "Ascendant";
        $pl_name1[11] = "House 2";
        $pl_name1[12] = "House 3";
        $pl_name1[13] = "House 4";
        $pl_name1[14] = "House 5";
        $pl_name1[15] = "House 6";
        $pl_name1[16] = "House 7";
        $pl_name1[17] = "House 8";
        $pl_name1[18] = "House 9";
        $pl_name1[19] = "MC (Midheaven)";
        $pl_name1[20] = "House 11";
        $pl_name1[21] = "House 12";

        for ($z = 0; $z <= 21; $z++)
        {
          $longitude1[$z] = $longitude[$z];
          $declination1[$z] = $declination[$z];
        }

        for ($z = 0; $z <= 9; $z++)
        {
          $house_pos1[$z] = $house_pos[$z];
        }

        $hob[1] = $row['hour'];
        $mob[1] = $row['minute'];
      }
      else
      {
        $header2 = '<b>Data for ' . strftime("%A, %B %d, %Y at %X (time zone = GMT $tz hours)</b><br><br><br>\n", mktime($row['hour'], $row['minute'], $secs, $row['month'], $row['day'], $row['year']));
        $line2 = $existing_name2 . ", born " . strftime("%A, %B %d, %Y at %X (time zone = GMT $tz hours)</b>", mktime($row['hour'], $row['minute'], $secs, $row['month'], $row['day'], $row['year']));

        // assign data from database into arrays for display
        for ($z = 0; $z <= 21; $z++)
        {
          $pl_name2[$z] = $pl_name1[$z];
        }

        CalculatePositions($id[2], $longitude, $declination, $house_pos);

        for ($z = 0; $z <= 21; $z++)
        {
          $longitude2[$z] = $longitude[$z];
          $declination2[$z] = $declination[$z];
        }

        for ($z = 0; $z <= 9; $z++)
        {
          $house_pos2[$z] = $house_pos[$z];
        }

        $hob[2] = $row['hour'];
        $mob[2] = $row['minute'];
      }
    }
  }

  $num_MRs = GetMutualReceptions($longitude1, $longitude2);
  $dynes = GetCosmodynes($longitude1, $declination1, $house_pos1, $longitude2, $declination2, $house_pos2, $hob, $mob);

  DisplayResults($dynes, $num_MRs);

  echo "<FONT color='#0000ff' SIZE='4' FACE='Arial'><b>For ID #$id[1] --- </b></font>";
  echo "<FONT color='#ff0000' SIZE='5' FACE='Arial'><b>$existing_name1 </b></font><br><br>";
  echo $header1;

  $hr_ob = $hob[1];
  $min_ob = $mob[1];

  $ubt1 = 0;
  if (($hr_ob == 12) And ($min_ob == 0))
  {
    $ubt1 = 1;        // this person has an unknown birth time
  }

  DisplayNatalData($pl_name1, $longitude1, $declination1, $house_pos1, $hr_ob, $min_ob);

  echo "<FONT color='#0000ff' SIZE='4' FACE='Arial'><b>For ID #$id[2] --- </b></font>";
  echo "<FONT color='#ff0000' SIZE='5' FACE='Arial'><b>$existing_name2 </b></font><br><br>";
  echo $header2;

  $hr_ob = $hob[2];
  $min_ob = $mob[2];

  $ubt2 = 0;
  if (($hr_ob == 12) And ($min_ob == 0))
  {
    $ubt2 = 1;        // this person has an unknown birth time
  }

  DisplayNatalData($pl_name2, $longitude2, $declination2, $house_pos2, $hr_ob, $min_ob);

  echo "</center>";

  include("constants_eng.php");     // this is here because we must rename the planet names


  // update count
  $sql = "SELECT dual_cosmodynes FROM reports";
  $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);
  $row = mysqli_fetch_array($result);
  $count = $row[dual_cosmodynes] + 1;

  $sql = "UPDATE reports SET dual_cosmodynes = '$count'";
  $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);


  // update count
  $sql = "SELECT dc_with_report FROM reports";
  $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);
  $row = mysqli_fetch_array($result);
  $count = $row[dc_with_report] + 1;

  $sql = "UPDATE reports SET dc_with_report = '$count'";
  $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);


//display the synastry report
  if ($no_interps == False)
  {
    require ('calc_dual_dyne_harmony.php');

    $dynes = Get_Dual_Cosmodyne_Harmony($longitude1, $declination1, $house_pos1, $longitude2, $declination2, $house_pos2, $hob, $mob);

    include ('synastry_report.php');
    Generate_synastry_report($existing_name1, $existing_name2, $line1, $line2, $pl_name1, $longitude1, $longitude2, $longitude1[10], $longitude2[10], $ubt1, $ubt2, $dynes);
  }


  include ('footer.html');
  exit();


Function CalculatePositions($last_id, &$longitude, &$declination, &$house_pos)
{
  require('../../../mysqli_connect_online_calcs_db_MYSQLI.php');

  $swephsrc = './sweph';    //sweph MUST be in a folder no less than at this level
  $sweph = './sweph';

  // Unset any variables not initialized elsewhere in the program
  unset($PATH,$out,$pl_name);

  //fetch all data for this record
  $sql = "SELECT * FROM birth_info WHERE ID='$last_id'";
  $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);
  $row = mysqli_fetch_array($result);
  $num_rows = MYSQLI_NUM_rows($result);

  //assign data from database to local variables
  $inmonth = $row['month'];
  $inday = $row['day'];
  $inyear = $row['year'];

  $inhours = $row['hour'];
  $inmins = $row['minute'];
  $insecs = "0";

  $intz = $row['timezone'];

  $my_longitude = $row['ew'] * ($row['long_deg'] + ($row['long_min'] / 60));
  $my_latitude = $row['ns'] * ($row['lat_deg'] + ($row['lat_min'] / 60));

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

  $utdatenow = strftime("%d.%m.%Y", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));
  $utnow = strftime("%H:%M:%S", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));

  putenv("PATH=$PATH:$swephsrc");

  // get 10 planets and all house cusps
  exec ("swetest -edir$sweph -b$utdatenow -ut$utnow -p0123456789 -eswe -house$my_longitude,$my_latitude, -fPldj -g, -head", $out);

  // Each line of output data from swetest is exploded into array $row, giving these elements:
  // 0 = planet name
  // 1 = longitude
  // 2 = declination
  // 3 = house position
  // planets are index 0 - index 9, house cusps are index 10 - 21
  foreach ($out as $key => $line)
  {
    $row = explode(',',$line);
    $pl_name[$key] = $row[0];
    $longitude[$key] = $row[1];
    $declination[$key] = $row[2];
    $house_pos[$key] = $row[3];
  };


  //I want this routine to change the data in the house position array, hence my use of "&$house_pos"
  for ($x = 1; $x <= 12; $x++)
  {
    for ($y = 0; $y <= 9; $y++)
    {
      $pl = $longitude[$y] + (1 / 36000);
      if ($x < 12 And $longitude[$x + 9] > $longitude[$x + 10])
      {
        If (($pl >= $longitude[$x + 9] And $pl < 360) Or ($pl < $longitude[$x + 10] And $pl >= 0))
        {
          $house_pos[$y] = $x;
          continue;
        }
      }

      if ($x == 12 And ($longitude[$x + 9] > $longitude[10]))
      {
        if (($pl >= $longitude[$x + 9] And $pl < 360) Or ($pl < $longitude[10] And $pl >= 0))
        {
          $house_pos[$y] = $x;
        }
        continue;
      }

      if (($pl >= $longitude[$x + 9]) And ($pl < $longitude[$x + 10]) And ($x < 12))
      {
        $house_pos[$y] = $x;
        continue;
      }

      if (($pl >= $longitude[$x + 9]) And ($pl < $longitude[10]) And ($x == 12))
      {
        $house_pos[$y] = $x;
      }
    }
  }
}

?>
