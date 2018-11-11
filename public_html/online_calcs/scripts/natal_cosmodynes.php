<?php
  include ('../accesscontrol.php');

  if ($is_logged_in == False)
  {
    exit();
  }


  // connect to and point to the proper database
  require_once ('../../../mysqli_connect_online_calcs_db_MYSQLI.php');
  require_once ('../../../my_functions_MYSQLI.php');

if (isset($_POST['submitted']))
{
  echo "<center>";

  // get ID1
  $last_id = safeEscapeString($conn, $_POST["id1"]);

  if (!is_numeric($last_id))
  {
    echo "<center><br><br>You have forgotten to make an entry. Please try again.</center>";
    include ('footer.html');
    exit();
  }


  $username = $_SESSION['username'];

  $sql = "SELECT * FROM birth_info WHERE ID='$last_id' And entered_by='$username'";

  $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);
  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
  $num_records = MYSQLI_NUM_rows($result);

  if ($num_records != 1)
  {
    echo "<center><br><br>I cannot find this person in the database. Please try again.</center>";
    include ('footer.html');
    exit();
  }


  $swephsrc = './sweph';    //sweph MUST be in a folder no less than at this level
  $sweph = './sweph';

  // Unset any variables not initialized elsewhere in the program
  unset($PATH,$out,$pl_name,$longitude,$declination,$house_pos);

  //fetch all data for this record
  $sql = "SELECT * FROM birth_info WHERE ID='$last_id'";
  $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);
  $row = mysqli_fetch_array($result);
  $num_rows = MYSQLI_NUM_rows($result);

  //assign data from database to local variables
  $existing_name1 = $row['name'];
  
  $inmonth = $row['month'];
  $inday = $row['day'];
  $inyear = $row['year'];

  $inhours = $row['hour'];
  $inmins = $row['minute'];
  $insecs = "0";

  $intz = $row['timezone'];

  include ('header_natal_c.html');

  $my_longitude = $row['ew'] * ($row['long_deg'] + ($row['long_min'] / 60));
  $my_latitude = $row['ns'] * ($row['lat_deg'] + ($row['lat_min'] / 60));

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

  $header1 = '<b>Data for ' . strftime("%A, %B %d, %Y at %X (time zone = GMT $intz hours)</b><br><br><br>\n", mktime($row['hour'], $row['minute'], $secs, $row['month'], $row['day'], $row['year']));

  // adjust date and time for minus hour due to time zone taking the hour negative
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

  GetNatalHousePositions($longitude, $house_pos);


//calculate natal cosmodynes
  unset($MRs);
  $num_MRs = GetMutualReceptions_natal($longitude, $MRs);

// make sure the Ascendant and MC dont get into the picture
  $MRs[10] = 0;
  $MRs[11] = 0;
//done

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
  $pl_name[10] = "Ascendant";
  $pl_name[11] = "House 2";
  $pl_name[12] = "House 3";
  $pl_name[13] = "House 4";
  $pl_name[14] = "House 5";
  $pl_name[15] = "House 6";
  $pl_name[16] = "House 7";
  $pl_name[17] = "House 8";
  $pl_name[18] = "House 9";
  $pl_name[19] = "MC (Midheaven)";
  $pl_name[20] = "House 11";
  $pl_name[21] = "House 12";

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

  $house_name[1] = "House 1";
  $house_name[2] = "House 2";
  $house_name[3] = "House 3";
  $house_name[4] = "House 4";
  $house_name[5] = "House 5";
  $house_name[6] = "House 6";
  $house_name[7] = "House 7";
  $house_name[8] = "House 8";
  $house_name[9] = "House 9";
  $house_name[10] = "House 10";
  $house_name[11] = "House 11";
  $house_name[12] = "House 12";


  $result = GetNatalCosmodynes($pl_name, $longitude, $declination, $house_pos, $unknown_bt, $MRs, $name, $planet_power, $planet_harmony, $sign_power, $sign_harmony, $house_power, $house_harmony, $totals);


//get strongest planet, sign, and house AND most harmonious planet, sign, and house
  $pp = -500;
  $ph = -500;
  $pd = 1000;

  $sp = -500;
  $sh = -500;
  $sd = 1000;

  $hp = -500;
  $hh = -500;
  $hd = 1000;

  for ($y = 1; $y <= 12; $y++)
  {
    if ($planet_power[$y - 1] > $pp)
    {
      $pp = $planet_power[$y - 1];
      $xpp = $y - 1;
    }

    if ($planet_harmony[$y - 1] > $ph)
    {
      $ph = $planet_harmony[$y - 1];
      $xph = $y - 1;
    }

    if ($planet_harmony[$y - 1] < $pd)
    {
      $pd = $planet_harmony[$y - 1];
      $xpd = $y - 1;
    }

    if ($sign_power[$y] > $sp)
    {
      $sp = $sign_power[$y];
      $xsp = $y;
    }

    if ($sign_harmony[$y] > $sh)
    {
      $sh = $sign_harmony[$y];
      $xsh = $y;
    }

    if ($sign_harmony[$y] < $sd)
    {
      $sd = $sign_harmony[$y];
      $xsd = $y;
    }

    if ($house_power[$y] > $hp)
    {
      $hp = $house_power[$y];
      $xhp = $y;
    }

    if ($house_harmony[$y] > $hh)
    {
      $hh = $house_harmony[$y];
      $xhh = $y;
    }

    if ($house_harmony[$y] < $hd)
    {
      $hd = $house_harmony[$y];
      $xhd = $y;
    }
  }


//display the results - first reduce to 2 decimal places
  echo "<FONT color='#0000ff' SIZE='4' FACE='Arial'><b>For ID #$last_id --- </b></font>";
  echo "<FONT color='#ff0000' SIZE='5' FACE='Arial'><b>$existing_name1 </b></font><br><br>";
  echo $header1;

  for ($xx = 0; $xx <= 11; $xx++)
  {
    $planet_power[$xx] = sprintf('%.2f', $planet_power[$xx]);
    $planet_harmony[$xx] = sprintf('%.2f', $planet_harmony[$xx]);
    $L1[$xx] = Convert_Longitude($longitude[$xx]);

    if ($xx <= 6)
    {
      $totals[$xx - 1] = sprintf('%.2f', $totals[$xx - 1]);
    }
  }

  $L1[10] = Convert_Longitude($longitude[10]);
  $L1[11] = Convert_Longitude($longitude[19]);

  $pl_name[11] = "Midheaven";

  echo "<br>";

  echo "<table align='center' style='font-family: verdana; font-size: 8pt; color: 000066;' cellspacing='1' cellpadding='5'>";
    echo "<tr bgcolor='bebeee'>";
    echo "<td align='center'><b>Planet</b></td>";
    echo "<td align='center'><b>Power</b></td>";
    echo "<td align='center'><b>Harmony</b></td>";
    echo "<td align='center'><b>Longitude</b></td>";
    echo "<td align='center'><b>House Position</b></td>";
  echo "</tr>";

  for ($xx = 0; $xx <= 11; $xx++)
  {
    echo "<tr bgcolor='#ffffff'>";
    echo "<td>$pl_name[$xx]</td>";
    echo "<td align='center'>$planet_power[$xx]</td>";
    echo "<td align='center'>$planet_harmony[$xx]</td>";
    echo "<td>$L1[$xx]</td>";

    if ($xx < 10)
    {
      echo "<td align='center'>$house_pos[$xx]</td>";
    }
    else
    {
      echo "<td>&nbsp;</td>";
    }

    echo "</tr>";
  }

    echo "<tr>";
    echo "<td>------</td>";
    echo "<td align='center'>------</td>";
    echo "<td align='center'>------</td>";
    echo "<td>&nbsp;</td>";
    echo "<td>&nbsp;</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>TOTALS</td>";
    echo "<td align='center'>$totals[0]</td>";
    echo "<td align='center'>$totals[1]</td>";
    echo "<td>&nbsp;</td>";
    echo "<td>&nbsp;</td>";
    echo "</tr>";
  echo "</table>";

  echo "<br><br>";


  for ($xx = 1; $xx <= 12; $xx++)
  {
    $sign_power[$xx] = sprintf('%.2f', $sign_power[$xx]);
    $sign_harmony[$xx] = sprintf('%.2f', $sign_harmony[$xx]);

    $house_power[$xx] = sprintf('%.2f', $house_power[$xx]);
    $house_harmony[$xx] = sprintf('%.2f', $house_harmony[$xx]);
    $HC1[$xx] = Convert_Longitude($longitude[$xx + 9]);
  }


  echo "<table align='center' style='font-family: verdana; font-size: 8pt; color: 000066;' cellspacing='1' cellpadding='5'>";
    echo "<tr bgcolor='bebeee'>";
    echo "<td align='center'><b>Sign</b></td>";
    echo "<td align='center'><b>Power</b></td>";
    echo "<td align='center'><b>Harmony</b></td>";
  echo "</tr>";

  for ($xx = 1; $xx <= 12; $xx++)
  {
    echo "<tr bgcolor='#ffffff'>";
    echo "<td>$sign_name[$xx]</td>";
    echo "<td align='center'>$sign_power[$xx]</td>";
    echo "<td align='center'>$sign_harmony[$xx]</td>";
    echo "</tr>";
  }

    echo "<tr>";
    echo "<td>------</td>";
    echo "<td align='center'>------</td>";
    echo "<td align='center'>------</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>TOTALS</td>";
    echo "<td align='center'>$totals[2]</td>";
    echo "<td align='center'>$totals[3]</td>";
    echo "</tr>";
  echo "</table>";

  echo "<br><br>";

  echo "<table align='center' style='font-family: verdana; font-size: 8pt; color: 000066;' cellspacing='1' cellpadding='5'>";
    echo "<tr bgcolor='bebeee'>";
    echo "<td align='center'><b>House</b></td>";
    echo "<td align='center'><b>Power</b></td>";
    echo "<td align='center'><b>Harmony</b></td>";
    echo "<td align='center'><b>Longitude</b></td>";
  echo "</tr>";

  for ($xx = 1; $xx <= 12; $xx++)
  {
    echo "<tr bgcolor='#ffffff'>";
    echo "<td align='center'>$xx</td>";
    echo "<td align='center'>$house_power[$xx]</td>";
    echo "<td align='center'>$house_harmony[$xx]</td>";
    echo "<td>$HC1[$xx]</td>";
    echo "</tr>";
  }

    echo "<tr>";
    echo "<td>------</td>";
    echo "<td align='center'>------</td>";
    echo "<td align='center'>------</td>";
    echo "<td>&nbsp;</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>TOTALS</td>";
    echo "<td align='center'>$totals[4]</td>";
    echo "<td align='center'>$totals[5]</td>";
    echo "<td>&nbsp;</td>";
    echo "</tr>";
  echo "</table>";

  echo "</center>";


  // update count
  $sql = "SELECT natal_cosmodynes FROM reports";
  $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);
  $row = mysqli_fetch_array($result);
  $count = $row[natal_cosmodynes] + 1;

  $sql = "UPDATE reports SET natal_cosmodynes = '$count'";
  $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);


  include ('footer.html');
  exit();
}

include ('footer.html');
exit();


Function GetNatalHousePositions($longitude, &$house_pos)
{
// I want this routine to change the data in the house position array, hence my use of "&$house_pos"
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


Function GetMutualReceptions_natal($longitude, &$MRs)
{
  $num_MRs = 0;

  for ($y = 0; $y <= 9; $y++)
  {

    $sy = floor($longitude[$y] / 30) + 1;
    for ($x = 0; $x <= 9; $x++)
    {
      // get sign of each planet
      $sx = floor($longitude[$x] / 30) + 1;

      // look for all mutual receptions
      if ($y == 0 And ($sy == 4 Or $sy == 2) And $x == 1 And ($sx == 1 Or $sx == 5))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 0 And ($sy == 3 Or $sy == 11) And $x == 7 And ($sx == 1 Or $sx == 5))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 0 And ($sy == 9 Or $sy == 12) And $x == 8 And ($sx == 1 Or $sx == 5))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 0 And ($sy == 5 Or $sy == 8) And $x == 9 And ($sx == 1 Or $sx == 5))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 0 And ($sy == 3 Or $sy == 6 Or $sy == 11) And $x == 2 And ($sx == 1 Or $sx == 5))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 0 And ($sy == 2 Or $sy == 7 Or $sy == 12) And $x == 3 And ($sx == 1 Or $sx == 5))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 0 And ($sy == 1 Or $sy == 8 Or $sy == 10) And $x == 4 And ($sx == 1 Or $sx == 5))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 0 And ($sy == 4 Or $sy == 9 Or $sy == 12) And $x == 5 And ($sx == 1 Or $sx == 5))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 0 And ($sy == 7 Or $sy == 10 Or $sy == 11) And $x == 6 And ($sx == 1 Or $sx == 5))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }


      if ($y == 1 And ($sy == 3 Or $sy == 11) And $x == 7 And ($sx == 2 Or $sx == 4))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 1 And ($sy == 9 Or $sy == 12) And $x == 8 And ($sx == 2 Or $sx == 4))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 1 And ($sy == 5 Or $sy == 8) And $x == 9 And ($sx == 2 Or $sx == 4))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 1 And ($sy == 3 Or $sy == 6 Or $sy == 11) And $x == 2 And ($sx == 2 Or $sx == 4))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 1 And ($sy == 2 Or $sy == 7 Or $sy == 12) And $x == 3 And ($sx == 2 Or $sx == 4))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 1 And ($sy == 1 Or $sy == 8 Or $sy == 10) And $x == 4 And ($sx == 2 Or $sx == 4))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 1 And ($sy == 4 Or $sy == 9 Or $sy == 12) And $x == 5 And ($sx == 2 Or $sx == 4))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 1 And ($sy == 7 Or $sy == 10 Or $sy == 11) And $x == 6 And ($sx == 2 Or $sx == 4))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }


      if ($y == 2 And ($sy == 3 Or $sy == 11) And $x == 7 And ($sx == 3 Or $sx == 6 Or $sx == 11))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 2 And ($sy == 9 Or $sy == 12) And $x == 8 And ($sx == 3 Or $sx == 6 Or $sx == 11))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 2 And ($sy == 5 Or $sy == 8) And $x == 9 And ($sx == 3 Or $sx == 6 Or $sx == 11))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 2 And ($sy == 2 Or $sy == 7 Or $sy == 12) And $x == 3 And ($sx == 3 Or $sx == 6 Or $sx == 11))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 2 And ($sy == 1 Or $sy == 8 Or $sy == 10) And $x == 4 And ($sx == 3 Or $sx == 6 Or $sx == 11))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 2 And ($sy == 4 Or $sy == 9 Or $sy == 12) And $x == 5 And ($sx == 3 Or $sx == 6 Or $sx == 11))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 2 And ($sy == 7 Or $sy == 10 Or $sy == 11) And $x == 6 And ($sx == 3 Or $sx == 6 Or $sx == 11))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }


      if ($y == 3 And ($sy == 3 Or $sy == 11) And $x == 7 And ($sx == 2 Or $sx == 7 Or $sx == 12))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 3 And ($sy == 9 Or $sy == 12) And $x == 8 And ($sx == 2 Or $sx == 7 Or $sx == 12))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 3 And ($sy == 5 Or $sy == 8) And $x == 9 And ($sx == 2 Or $sx == 7 Or $sx == 12))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 3 And ($sy == 1 Or $sy == 8 Or $sy == 10) And $x == 4 And ($sx == 2 Or $sx == 7 Or $sx == 12))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 3 And ($sy == 4 Or $sy == 9 Or $sy == 12) And $x == 5 And ($sx == 2 Or $sx == 7 Or $sx == 12))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 3 And ($sy == 7 Or $sy == 10 Or $sy == 11) And $x == 6 And ($sx == 2 Or $sx == 7 Or $sx == 12))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }


      if ($y == 4 And ($sy == 3 Or $sy == 11) And $x == 7 And ($sx == 1 Or $sx == 8 Or $sx == 10))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 4 And ($sy == 9 Or $sy == 12) And $x == 8 And ($sx == 1 Or $sx == 8 Or $sx == 10))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 4 And ($sy == 5 Or $sy == 8) And $x == 9 And ($sx == 1 Or $sx == 8 Or $sx == 10))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 4 And ($sy == 4 Or $sy == 9 Or $sy == 12) And $x == 5 And ($sx == 1 Or $sx == 8 Or $sx == 10))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 4 And ($sy == 7 Or $sy == 10 Or $sy == 11) And $x == 6 And ($sx == 1 Or $sx == 8 Or $sx == 10))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }


      if ($y == 5 And ($sy == 3 Or $sy == 11) And $x == 7 And ($sx == 4 Or $sx == 9 Or $sx == 12))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 5 And ($sy == 9 Or $sy == 12) And $x == 8 And ($sx == 4 Or $sx == 9 Or $sx == 12))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 5 And ($sy == 5 Or $sy == 8) And $x == 9 And ($sx == 4 Or $sx == 9 Or $sx == 12))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 5 And ($sy == 7 Or $sy == 10 Or $sy == 11) And $x == 6 And ($sx == 4 Or $sx == 9 Or $sx == 12))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }


      if ($y == 6 And ($sy == 3 Or $sy == 11) And $x == 7 And ($sx == 7 Or $sx == 10 Or $sx == 11))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 6 And ($sy == 9 Or $sy == 12) And $x == 8 And ($sx == 7 Or $sx == 10 Or $sx == 11))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 6 And ($sy == 5 Or $sy == 8) And $x == 9 And ($sx == 7 Or $sx == 10 Or $sx == 11))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }


      if ($y == 7 And ($sy == 9 Or $sy == 12) And $x == 8 And ($sx == 3 Or $sx == 11))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }

      if ($y == 7 And ($sy == 5 Or $sy == 8) And $x == 9 And ($sx == 3 Or $sx == 11))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }


      if ($y == 8 And ($sy == 5 Or $sy == 8) And $x == 9 And ($sx == 9 Or $sx == 12))
      {
        $num_MRs++;
        $MRs[$y] += 1;
        $MRs[$x] += 1;
      }
    }
  }

  return $num_MRs;
}


Function GetNatalCosmodynes($pl_name, $longitude, $declination, $house_pos, $unknown_bt, $MRs, $name, &$planet_power, &$planet_harmony, &$sign_power, &$sign_harmony, &$house_power, &$house_harmony, &$totals)
{
  $dynes = array();
  $aspect_power = array();      //PlanetAspectPower()
  $harmony = array();       //PlanetHarmony()
  $discord = array();       //PlanetDiscord()
  $XHousePower = array();
  $XHousePowerVariation = array();
  $L1 = array();
  $D1 = array();
  $hc_start_at_Asc_with_1 = array();
  $hc_start_at_MC_with_1 = array();
  $InterceptedSign = array();
  $InterceptedHouse = array();


//put original array values into temporary arrays and use them
  for ($x = 0; $x <= 10; $x++)
  {
    $L1[$x] = $longitude[$x];
    $D1[$x] = $declination[$x];
  }

  $L1[11] = $longitude[19];   //MC
  $D1[11] = $declination[19];   //MC

  for ($x = 1; $x <= 12; $x++)    //house cusps - $hc_start_at_Asc_with_1[1] = 237 for AE
  {
    $hc_start_at_Asc_with_1[$x] = $longitude[$x + 9];
  }
//done


  for ($x = 10; $x <= 12; $x++)   //house cusps - $hc_start_at_MC_with_1[1] = 154 for AE
  {
    $hc_start_at_MC_with_1[$x - 9] = $longitude[$x + 9];
  }
//done

  for ($x = 1; $x <= 9; $x++)   //house cusps
  {
    $hc_start_at_MC_with_1[$x + 3] = $hc_start_at_Asc_with_1[$x];
  }
//done


//initialize some various
  $aspect_power[10] = 15;   //Asc
  $aspect_power[11] = 15;   //MC
  $pl_name[11] = "MC";
//done


//get planet distance from previous house cusp
  for ($x = 0; $x <= 9; $x++)
  {
  $y = floor($house_pos[$x]);     //get house number
  $DistanceFromCusp[$x] = $L1[$x] - $hc_start_at_Asc_with_1[$y];    //Asc starts with array element 10
  if ($DistanceFromCusp[$x] < 0)
  {
    $DistanceFromCusp[$x] = $DistanceFromCusp[$x] + 360;
  }
  }
//done


//get number of degrees in each house
  for ($x = 1; $x <= 11; $x++)
  {
    $DegreesInEachHouse[$x] = $hc_start_at_Asc_with_1[$x + 1] - $hc_start_at_Asc_with_1[$x];
  if ($DegreesInEachHouse[$x] < 0)
  {
    $DegreesInEachHouse[$x] = $DegreesInEachHouse[$x] + 360;
  }
  }

  $DegreesInEachHouse[12] = $hc_start_at_Asc_with_1[1] - $hc_start_at_Asc_with_1[12];
  if ($DegreesInEachHouse[12] < 0)
  {
    $DegreesInEachHouse[12] = $DegreesInEachHouse[12] + 360;
  }
//done


//calculate house power
  $XHousePower[1] = 15;
  $XHousePower[2] = 8.5;
  $XHousePower[3] = 8;
  $XHousePower[4] = 14;
  $XHousePower[5] = 7.5;
  $XHousePower[6] = 7;
  $XHousePower[7] = 14.5;
  $XHousePower[8] = 10.9;
  $XHousePower[9] = 10;
  $XHousePower[10] = 15;
  $XHousePower[11] = 11.9;
  $XHousePower[12] = 9.3;

  $XHousePowerVariation[1] = 2;
  $XHousePowerVariation[2] = 0.5;
  $XHousePowerVariation[3] = 0.5;
  $XHousePowerVariation[4] = 2;
  $XHousePowerVariation[5] = 0.5;
  $XHousePowerVariation[6] = 0.5;
  $XHousePowerVariation[7] = 2;
  $XHousePowerVariation[8] = 0.9;
  $XHousePowerVariation[9] = 0.7;
  $XHousePowerVariation[10] = 2;
  $XHousePowerVariation[11] = 1;
  $XHousePowerVariation[12] = 0.7;

  for ($x = 0; $x <= 9; $x++)
  {
  $y = floor($house_pos[$x]);     //get house number
  $PlanetHousePower[$x] = $XHousePowerVariation[$y] * $DistanceFromCusp[$x] / $DegreesInEachHouse[$y];
  $PlanetHousePower[$x] = $XHousePower[$y] - $PlanetHousePower[$x];
  }
//done



  $p1_limit = 11;

  if ($unknown_bt[1] == 1)
  {
    $p1_limit = 9;
  }


  for ($y = 0; $y <= $p1_limit; $y++)
  {
    if ($y == 1 And $p1_limit == 9)
    {
      // skip the Moon because of unknown birth time
      continue;
    }

    for ($x = 0; $x <= $p1_limit; $x++)
    {
      if ($x == $y)
      {
        continue;
      }

      if ($x == 1 And $p1_limit == 9)
      {
        // skip the Moon because of unknown birth time
        continue;
      }

      // get house of 1st planet
      if ($y >= 10)
      {
        $yh = 10;   //an angle
      }
      else
      {
        $yh = floor($house_pos[$y]);
      }

      if ($y >= 10)
      {
        $yh = 10;     //an angle
        $orb30y = 3;
        $orb45y = 5;
        $orb60y = 7;
        $orb90y = 10;
        $orb180y = 12;
        $pow30y = 3;
        $pow45y = 5;
        $pow60y = 7;
        $pow90y = 10;
        $pow180y = 12;
      }
      else
      {
        // find ORB for 1st planet ($y), dependent upon what house $y is in AND whether it is a Luminary or a planet
        if (($y != 0 And $y != 1) And ($yh == 3 Or $yh == 6 Or $yh == 9 Or $yh == 12))
        {
          $orb30y = 1;
          $orb45y = 3;
          $orb60y = 5;
          $orb90y = 6;
          $orb180y = 8;
          $pow30y = 1;
          $pow45y = 3;
          $pow60y = 5;
          $pow90y = 6;
          $pow180y = 8;
          if ($y == 2)
          {
            $pow30y += 1;
            $pow45y += 1;
            $pow60y += 1;
            $pow90y += 2;
            $pow180y += 3;
          }
        }
        elseif (($y == 0 Or $y == 1) And ($yh == 3 Or $yh == 6 Or $yh == 9 Or $yh == 12))
        {
          $orb30y = 2;
          $orb45y = 4;
          $orb60y = 6;
          $orb90y = 8;
          $orb180y = 11;
          $pow30y = 2;
          $pow45y = 4;
          $pow60y = 6;
          $pow90y = 8;
          $pow180y = 11;
        }
        elseif (($y != 0 And $y != 1) And ($yh == 2 Or $yh == 5 Or $yh == 8 Or $yh == 11))
        {
          $orb30y = 2;
          $orb45y = 4;
          $orb60y = 6;
          $orb90y = 8;
          $orb180y = 10;
          $pow30y = 2;
          $pow45y = 4;
          $pow60y = 6;
          $pow90y = 8;
          $pow180y = 10;
          if ($y == 2)
          {
            $pow30y += 1;
            $pow45y += 1;
            $pow60y += 1;
            $pow90y += 2;
            $pow180y += 3;
          }
        }
        elseif (($y == 0 Or $y == 1) And ($yh == 2 Or $yh == 5 Or $yh == 8 Or $yh == 11))
        {
          $orb30y = 3;
          $orb45y = 5;
          $orb60y = 7;
          $orb90y = 10;
          $orb180y = 13;
          $pow30y = 3;
          $pow45y = 5;
          $pow60y = 7;
          $pow90y = 10;
          $pow180y = 13;
        }
        elseif (($y != 0 And $y != 1) And ($yh == 1 Or $yh == 4 Or $yh == 7 Or $yh == 10))
        {
          $orb30y = 3;
          $orb45y = 5;
          $orb60y = 7;
          $orb90y = 10;
          $orb180y = 12;
          $pow30y = 3;
          $pow45y = 5;
          $pow60y = 7;
          $pow90y = 10;
          $pow180y = 12;
          if ($y == 2)
          {
            $pow30y += 1;
            $pow45y += 1;
            $pow60y += 1;
            $pow90y += 2;
            $pow180y += 3;
          }
        }
        elseif (($y == 0 Or $y == 1) And ($yh == 1 Or $yh == 4 Or $yh == 7 Or $yh == 10))
        {
          $orb30y = 4;
          $orb45y = 6;
          $orb60y = 8;
          $orb90y = 12;
          $orb180y = 15;
          $pow30y = 4;
          $pow45y = 6;
          $pow60y = 8;
          $pow90y = 12;
          $pow180y = 15;
        }
      }

      // get house of 2nd planet
      if ($x >= 10)
      {
        $xh = 10;   //an angle
      }
      else
      {
        $xh = floor($house_pos[$x]);
      }

      if ($x == 10)
      {
        $xh = 10;
        $orb30x = 3;
        $orb45x = 5;
        $orb60x = 7;
        $orb90x = 10;
        $orb180x = 12;
        $pow30x = 3;
        $pow45x = 5;
        $pow60x = 7;
        $pow90x = 10;
        $pow180x = 12;
      }
      else
      {
        // find ORB for 2nd planet ($x), dependent upon what house $x is in AND whether it is a Luminary or a planet
        if (($x != 0 And $x != 1) And ($xh == 3 Or $xh == 6 Or $xh == 9 Or $xh == 12))
        {
          $orb30x = 1;
          $orb45x = 3;
          $orb60x = 5;
          $orb90x = 6;
          $orb180x = 8;
          $pow30x = 1;
          $pow45x = 3;
          $pow60x = 5;
          $pow90x = 6;
          $pow180x = 8;
          if ($x == 2)
          {
            $pow30x += 1;
            $pow45x += 1;
            $pow60x += 1;
            $pow90x += 2;
            $pow180x += 3;
          }
        }
        elseif (($x == 0 Or $x == 1) And ($xh == 3 Or $xh == 6 Or $xh == 9 Or $xh == 12))
        {
          $orb30x = 2;
          $orb45x = 4;
          $orb60x = 6;
          $orb90x = 8;
          $orb180x = 11;
          $pow30x = 2;
          $pow45x = 4;
          $pow60x = 6;
          $pow90x = 8;
          $pow180x = 11;
        }
        elseif (($x != 0 And $x != 1) And ($xh == 2 Or $xh == 5 Or $xh == 8 Or $xh == 11))
        {
          $orb30x = 2;
          $orb45x = 4;
          $orb60x = 6;
          $orb90x = 8;
          $orb180x = 10;
          $pow30x = 2;
          $pow45x = 4;
          $pow60x = 6;
          $pow90x = 8;
          $pow180x = 10;
          if ($x == 2)
          {
            $pow30x += 1;
            $pow45x += 1;
            $pow60x += 1;
            $pow90x += 2;
            $pow180x += 3;
          }
        }
        elseif (($x == 0 Or $x == 1) And ($xh == 2 Or $xh == 5 Or $xh == 8 Or $xh == 11))
        {
          $orb30x = 3;
          $orb45x = 5;
          $orb60x = 7;
          $orb90x = 10;
          $orb180x = 13;
          $pow30x = 3;
          $pow45x = 5;
          $pow60x = 7;
          $pow90x = 10;
          $pow180x = 13;
        }
        elseif (($x != 0 And $x != 1) And ($xh == 1 Or $xh == 4 Or $xh == 7 Or $xh == 10))
        {
          $orb30x = 3;
          $orb45x = 5;
          $orb60x = 7;
          $orb90x = 10;
          $orb180x = 12;
          $pow30x = 3;
          $pow45x = 5;
          $pow60x = 7;
          $pow90x = 10;
          $pow180x = 12;
          if ($x == 2)
          {
            $pow30x += 1;
            $pow45x += 1;
            $pow60x += 1;
            $pow90x += 2;
            $pow180x += 3;
          }
        }
        elseif (($x == 0 Or $x == 1) And ($xh == 1 Or $xh == 4 Or $xh == 7 Or $xh == 10))
        {
          $orb30x = 4;
          $orb45x = 6;
          $orb60x = 8;
          $orb90x = 12;
          $orb180x = 15;
          $pow30x = 4;
          $pow45x = 6;
          $pow60x = 8;
          $pow90x = 12;
          $pow180x = 15;
        }
      }

      $orb30 = $orb30y;
      if ($orb30x >= $orb30y)
      {
        $orb30 = $orb30x;
      }

      $orb45 = $orb45y;
      if ($orb45x >= $orb45y)
      {
        $orb45 = $orb45x;
      }
      $orb60 = $orb60y;
      if ($orb60x >= $orb60y)
      {
        $orb60 = $orb60x;
      }
      $orb90 = $orb90y;
      if ($orb90x >= $orb90y)
      {
        $orb90 = $orb90x;
      }
      $orb180 = $orb180y;
      if ($orb180x >= $orb180y)
      {
        $orb180 = $orb180x;
      }

      $pow30 = $pow30y;
      if ($pow30x >= $pow30y)
      {
        $pow30 = $pow30x;
      }

      $pow45 = $pow45y;
      if ($pow45x >= $pow45y)
      {
        $pow45 = $pow45x;
      }
      $pow60 = $pow60y;
      if ($pow60x >= $pow60y)
      {
        $pow60 = $pow60x;
      }
      $pow90 = $pow90y;
      if ($pow90x >= $pow90y)
      {
        $pow90 = $pow90x;
      }
      $pow180 = $pow180y;
      if ($pow180x >= $pow180y)
      {
        $pow180 = $pow180x;
      }

      // is there an aspect within orb?
      $da = Abs($L1[$y] - $L1[$x]);     //$da means distance apart
      if ($da > 180)
      {
        $da = 360 - $da;
      }

      $q = 1;
      $k = $da;

      if ($k <= $orb180)
      {
        $q = 2;
        $orbxx = $pow180;
        $daxx = $da;
      }
      elseif (($k <= (30 + $orb30)) And ($k >= (30 - $orb30)))
      {
        $q = 8;
        $orbxx = $pow30;
        $daxx = $da - 30;
      }
      elseif (($k <= (45 + $orb45)) And ($k >= (45 - $orb45)))
      {
        $q = 9;
        $orbxx = $pow45;
        $daxx = $da - 45;
      }
      elseif (($k <= (60 + $orb60)) And ($k >= (60 - $orb60)))
      {
        $q = 3;
        $orbxx = $pow60;
        $daxx = $da - 60;
      }
      elseif (($k <= (90 + $orb90)) And ($k >= (90 - $orb90)))
      {
        $q = 4;
        $orbxx = $pow90;
        $daxx = $da - 90;
      }
      // $da is checked here to separate the overlap in the two aspects from 129 - 132 degrees for luminaries
      elseif (($da <= 130) And ($k <= (120 + $orb90)) And ($k >= (120 - $orb90)))
      {
        $q = 5;
        $orbxx = $pow90;
        $daxx = $da - 120;
      }
      elseif (($da > 130) And ($k <= (135 + $orb45)) And ($k >= (135 - $orb45)))
      {
        $q = 10;
        $orbxx = $pow45;
        $daxx = $da - 135;
      }
      elseif (($k <= (150 + $orb30)) And ($k >= (150 - $orb30)))
      {
        $q = 11;
        $orbxx = $pow30;
        $daxx = $da - 150;
      }
      elseif ($k >= (180 - $orb180))
      {
        $q = 6;
        $orbxx = $pow180;
        $daxx = $da - 180;
      }

      if ($q > 1)
      {
        // we have an aspect, so get all the proper numbers
        $dyne_val = $orbxx - Abs($daxx);
        $aspect_power[$y] += $dyne_val;

        //get planetary harmony and discord
        if ($q == 3 Or $q == 5 Or $q == 8)
        {
          $harmony[$y] += $dyne_val;
        }

        if ($q == 4 Or $q == 6 Or $q == 9 Or $q == 10)
        {
          $discord[$y] += $dyne_val;
        }

        if ($y == 5 Or $x == 5)
        {
          // these treated as harmonious
          $harmony[$y] += ($dyne_val / 2);
        }

        if ($y == 3 Or $x == 3)
        {
          // these oppositions treated as harmonious
          $harmony[$y] += ($dyne_val / 4);
        }

        if ($y == 6 Or $x == 6)
        {
          // Saturn aspects treated as discordant
          $discord[$y] += ($dyne_val / 2);
        }

        if ($y == 4 Or $x == 4)
        {
          // Mars aspects treated as discordant
          $discord[$y] += ($dyne_val / 4);
        }
      }

      // now do declinations
      $diff_decl = Abs(Abs($D1[$y]) - Abs($D1[$x]));
      if ($diff_decl < 1)
      {
        if (($y != 0 And $y != 1 And $y != 2) And ($yh == 3 Or $yh == 6 Or $yh == 9 Or $yh == 12))
        {
          $orb180y = 8;
        }
        if (($y == 0 Or $y == 1 Or $y == 2) And ($yh == 3 Or $yh == 6 Or $yh == 9 Or $yh == 12))
        {
          $orb180y = 11;
        }

        if (($y != 0 And $y != 1 And $y != 2) And ($yh == 2 Or $yh == 5 Or $yh == 8 Or $yh == 11))
        {
          $orb180y = 10;
        }
        if (($y == 0 Or $y == 1 Or $y == 2) And ($yh == 2 Or $yh == 5 Or $yh == 8 Or $yh == 11))
        {
          $orb180y = 13;
        }

        if (($y != 0 And $y != 1 And $y != 2) And ($yh == 1 Or $yh == 4 Or $yh == 7 Or $yh == 10))
        {
          $orb180y = 12;
        }
        if (($y == 0 Or $y == 1 Or $y == 2) And ($yh == 1 Or $yh == 4 Or $yh == 7 Or $yh == 10))
        {
          $orb180y = 15;
        }

        if (($x != 0 And $x != 1 And $x != 2) And ($xh == 3 Or $xh == 6 Or $xh == 9 Or $xh == 12))
        {
          $orb180x = 8;
        }
        if (($x == 0 Or $x == 1 Or $x == 2) And ($xh == 3 Or $xh == 6 Or $xh == 9 Or $xh == 12))
        {
          $orb180x = 11;
        }

        if (($x != 0 And $x != 1 And $x != 2) And ($xh == 2 Or $xh == 5 Or $xh == 8 Or $xh == 11))
        {
          $orb180x = 10;
        }
        if (($x == 0 Or $x == 1 Or $x == 2) And ($xh == 2 Or $xh == 5 Or $xh == 8 Or $xh == 11))
        {
          $orb180x = 13;
        }

        if (($x != 0 And $x != 1 And $x != 2) And ($xh == 1 Or $xh == 4 Or $xh == 7 Or $xh == 10))
        {
          $orb180x = 12;
        }
        if (($x == 0 Or $x == 1 Or $x == 2) And ($xh == 1 Or $xh == 4 Or $xh == 7 Or $xh == 10))
        {
          $orb180x = 15;
        }

        $orb180 = $orb180y;
        if ($orb180x >= $orb180y)
        {
          $orb180 = $orb180x;
        }

        $decl_power = $orb180 * (1 - $diff_decl);
        $aspect_power[$y] += $decl_power;

        if ($y == 5 Or $x == 5)
        {
          // these treated as harmonious
          $harmony[$y] += ($decl_power / 2);
        }

        if ($y == 3 Or $x == 3)
        {
          // these treated as harmonious
          $harmony[$y] += ($decl_power / 4);
        }

        if ($y == 6 Or $x == 6)
        {
          // Saturn aspects treated as discordant
          $discord[$y] += ($decl_power / 2);
        }

        if ($y == 4 Or $x == 4)
        {
          // Mars aspects treated as discordant
          $discord[$y] += ($decl_power / 4);
        }
      }
    }
  }


//add mutual reception to harmonies here
  for ($x = 0; $x <= 9; $x++)
  {
    $harmony[$x] = $harmony[$x] + ($MRs[$x] * 5);
  }
//done


//find planet dignities
  for ($x = 0; $x <= 9; $x++)
  {
  $z = floor($L1[$x] / 30) + 1;   //get planets sign

  if ($x == 0 And $z == 5)
  {
    $harmony[$x] = $harmony[$x] + 2;
  }
  elseif ($x == 1 And $z == 4)
  {
    $harmony[$x] = $harmony[$x] + 2;
  }
  elseif ($x == 2 And ($z == 3 Or $z == 6))
  {
    $harmony[$x] = $harmony[$x] + 2;
  }
  elseif ($x == 3 And ($z == 2 Or $z == 7))
  {
    $harmony[$x] = $harmony[$x] + 2;
  }
  elseif ($x == 4 And ($z == 1 Or $z == 8))
  {
    $harmony[$x] = $harmony[$x] + 2;
  }
  elseif ($x == 5 And ($z == 9 Or $z == 12))
  {
    $harmony[$x] = $harmony[$x] + 2;
  }
  elseif ($x == 6 And ($z == 10 Or $z == 11))
  {
    $harmony[$x] = $harmony[$x] + 2;
  }
  elseif ($x == 7 And $z == 11)
  {
    $harmony[$x] = $harmony[$x] + 2;
  }
  elseif ($x == 8 And $z == 12)
  {
    $harmony[$x] = $harmony[$x] + 2;
  }
  elseif ($x == 9 And $z == 8)
  {
    $harmony[$x] = $harmony[$x] + 2;
    }
//done


//find planet detriment
  if ($x == 0 And $z == 11)
  {
    $discord[$x] = $discord[$x] + 2;
  }
  elseif ($x == 1 And $z == 10)
  {
    $discord[$x] = $discord[$x] + 2;
  }
  elseif ($x == 2 And ($z == 9 Or $z == 12))
  {
    $discord[$x] = $discord[$x] + 2;
  }
  elseif ($x == 3 And ($z == 1 Or $z == 8))
  {
    $discord[$x] = $discord[$x] + 2;
  }
  elseif ($x == 4 And ($z == 2 Or $z == 7))
  {
    $discord[$x] = $discord[$x] + 2;
  }
  elseif ($x == 5 And ($z == 3 Or $z == 6))
  {
    $discord[$x] = $discord[$x] + 2;
  }
  elseif ($x == 6 And ($z == 4 Or $z == 5))
  {
    $discord[$x] = $discord[$x] + 2;
  }
  elseif ($x == 7 And $z == 5)
  {
    $discord[$x] = $discord[$x] + 2;
  }
  elseif ($x == 8 And $z == 6)
  {
    $discord[$x] = $discord[$x] + 2;
  }
  elseif ($x == 9 And $z == 2)
  {
    $discord[$x] = $discord[$x] + 2;
    }
//done


//find planet exaltation
  if ($x == 0 And $z == 1)
  {
    $harmony[$x] = $harmony[$x] + 3;
  }
  elseif ($x == 1 And $z == 2)
  {
    $harmony[$x] = $harmony[$x] + 3;
  }
  elseif ($x == 2 And $z == 11)
  {
    $harmony[$x] = $harmony[$x] + 3;
  }
  elseif ($x == 3 And $z == 12)
  {
    $harmony[$x] = $harmony[$x] + 3;
  }
  elseif ($x == 4 And $z == 10)
  {
    $harmony[$x] = $harmony[$x] + 3;
  }
  elseif ($x == 5 And $z == 4)
  {
    $harmony[$x] = $harmony[$x] + 3;
  }
  elseif ($x == 6 And $z == 7)
  {
    $harmony[$x] = $harmony[$x] + 3;
  }
  elseif ($x == 7 And $z == 3)
  {
    $harmony[$x] = $harmony[$x] + 3;
  }
  elseif ($x == 8 And $z == 9)
  {
    $harmony[$x] = $harmony[$x] + 3;
  }
  elseif ($x == 9 And $z == 5)
  {
    $harmony[$x] = $harmony[$x] + 3;
    }
//done


//find planet fall
  if ($x == 0 And $z == 7)
  {
    $discord[$x] = $discord[$x] + 3;
  }
  elseif ($x == 1 And $z == 8)
  {
    $discord[$x] = $discord[$x] + 3;
  }
  elseif ($x == 2 And $z == 5)
  {
    $discord[$x] = $discord[$x] + 3;
  }
  elseif ($x == 3 And $z == 6)
  {
    $discord[$x] = $discord[$x] + 3;
  }
  elseif ($x == 4 And $z == 4)
  {
    $discord[$x] = $discord[$x] + 3;
  }
  elseif ($x == 5 And $z == 10)
  {
    $discord[$x] = $discord[$x] + 3;
  }
  elseif ($x == 6 And $z == 1)
  {
    $discord[$x] = $discord[$x] + 3;
  }
  elseif ($x == 7 And $z == 9)
  {
    $discord[$x] = $discord[$x] + 3;
  }
  elseif ($x == 8 And $z == 3)
  {
    $discord[$x] = $discord[$x] + 3;
  }
  elseif ($x == 9 And $z == 11)
  {
    $discord[$x] = $discord[$x] + 3;
    }
//done


//find planet harmony
  if ($x == 0 And $z == 9)
  {
    $harmony[$x] = $harmony[$x] + 1;
  }
  elseif ($x == 1 And $z == 12)
  {
    $harmony[$x] = $harmony[$x] + 1;
  }
  elseif ($x == 2 And $z == 8)
  {
    $harmony[$x] = $harmony[$x] + 1;
  }
  elseif ($x == 3 And $z == 11)
  {
    $harmony[$x] = $harmony[$x] + 1;
  }
  elseif ($x == 4 And $z == 5)
  {
    $harmony[$x] = $harmony[$x] + 1;
  }
  elseif ($x == 5 And $z == 2)
  {
    $harmony[$x] = $harmony[$x] + 1;
  }
  elseif ($x == 6 And $z == 6)
  {
    $harmony[$x] = $harmony[$x] + 1;
  }
  elseif ($x == 7 And $z == 7)
  {
    $harmony[$x] = $harmony[$x] + 1;
  }
  elseif ($x == 8 And $z == 4)
  {
    $harmony[$x] = $harmony[$x] + 1;
  }
  elseif ($x == 9 And $z == 1)
  {
    $harmony[$x] = $harmony[$x] + 1;
    }
//done


//find planet inharmony
  if ($x == 0 And $z == 3)
  {
    $discord[$x] = $discord[$x] + 1;
  }
  elseif ($x == 1 And $z == 6)
  {
    $discord[$x] = $discord[$x] + 1;
  }
  elseif ($x == 2 And $z == 2)
  {
    $discord[$x] = $discord[$x] + 1;
  }
  elseif ($x == 3 And $z == 5)
  {
    $discord[$x] = $discord[$x] + 1;
  }
  elseif ($x == 4 And $z == 11)
  {
    $discord[$x] = $discord[$x] + 1;
  }
  elseif ($x == 5 And $z == 8)
  {
    $discord[$x] = $discord[$x] + 1;
  }
  elseif ($x == 6 And $z == 12)
  {
    $discord[$x] = $discord[$x] + 1;
  }
  elseif ($x == 7 And $z == 1)
  {
    $discord[$x] = $discord[$x] + 1;
  }
  elseif ($x == 8 And $z == 10)
  {
    $discord[$x] = $discord[$x] + 1;
  }
  elseif ($x == 9 And $z == 7)
  {
    $discord[$x] = $discord[$x] + 1;
    }
  }
//done


//find total planet power
  for ($x = 0; $x <= 11; $x++)
  {
    $PlanetTotalPower[$x] = $PlanetHousePower[$x] + $aspect_power[$x];
  }
//done


//figure sign interceptions - get how many house cusps each sign is on
  for ($x = 1; $x <= 12; $x++)
  {
    $InterceptedSign[$x] = 0;
  }

  for ($x = 1; $x <= 12; $x++)
  {
    $z = floor($hc_start_at_MC_with_1[$x] / 30) + 1;
    $InterceptedSign[$z] = $InterceptedSign[$z] + 1;
  }
//done


//calculate sign power
  //find unoccupied sign power
  if ($InterceptedSign[1] == 0) { $SignPower[1] = $PlanetTotalPower[4] / 4;}
  if ($InterceptedSign[1] == 1) { $SignPower[1] = $PlanetTotalPower[4] / 2;}
  if ($InterceptedSign[1] == 2) { $SignPower[1] = $PlanetTotalPower[4];}
  if ($InterceptedSign[1] == 3) { $SignPower[1] = $PlanetTotalPower[4] * 2;}
  if ($InterceptedSign[2] == 0) { $SignPower[2] = $PlanetTotalPower[3] / 4;}
  if ($InterceptedSign[2] == 1) { $SignPower[2] = $PlanetTotalPower[3] / 2;}
  if ($InterceptedSign[2] == 2) { $SignPower[2] = $PlanetTotalPower[3];}
  if ($InterceptedSign[2] == 3) { $SignPower[2] = $PlanetTotalPower[3] * 2;}
  if ($InterceptedSign[3] == 0) { $SignPower[3] = $PlanetTotalPower[2] / 4;}
  if ($InterceptedSign[3] == 1) { $SignPower[3] = $PlanetTotalPower[2] / 2;}
  if ($InterceptedSign[3] == 2) { $SignPower[3] = $PlanetTotalPower[2];}
  if ($InterceptedSign[3] == 3) { $SignPower[3] = $PlanetTotalPower[2] * 2;}
  if ($InterceptedSign[4] == 0) { $SignPower[4] = $PlanetTotalPower[1] / 4;}
  if ($InterceptedSign[4] == 1) { $SignPower[4] = $PlanetTotalPower[1] / 2;}
  if ($InterceptedSign[4] == 2) { $SignPower[4] = $PlanetTotalPower[1];}
  if ($InterceptedSign[4] == 3) { $SignPower[4] = $PlanetTotalPower[1] * 2;}
  if ($InterceptedSign[5] == 0) { $SignPower[5] = $PlanetTotalPower[0] / 4;}
  if ($InterceptedSign[5] == 1) { $SignPower[5] = $PlanetTotalPower[0] / 2;}
  if ($InterceptedSign[5] == 2) { $SignPower[5] = $PlanetTotalPower[0];}
  if ($InterceptedSign[5] == 3) { $SignPower[5] = $PlanetTotalPower[0] * 2;}
  if ($InterceptedSign[6] == 0) { $SignPower[6] = $PlanetTotalPower[2] / 4;}
  if ($InterceptedSign[6] == 1) { $SignPower[6] = $PlanetTotalPower[2] / 2;}
  if ($InterceptedSign[6] == 2) { $SignPower[6] = $PlanetTotalPower[2];}
  if ($InterceptedSign[6] == 3) { $SignPower[6] = $PlanetTotalPower[2] * 2;}
  if ($InterceptedSign[7] == 0) { $SignPower[7] = $PlanetTotalPower[3] / 4;}
  if ($InterceptedSign[7] == 1) { $SignPower[7] = $PlanetTotalPower[3] / 2;}
  if ($InterceptedSign[7] == 2) { $SignPower[7] = $PlanetTotalPower[3];}
  if ($InterceptedSign[7] == 3) { $SignPower[7] = $PlanetTotalPower[3] * 2;}
  if ($InterceptedSign[8] == 0) { $SignPower[8] = (($PlanetTotalPower[4] + $PlanetTotalPower[9]) / 2) / 4;}
  if ($InterceptedSign[8] == 1) { $SignPower[8] = (($PlanetTotalPower[4] + $PlanetTotalPower[9]) / 2) / 2;}
  if ($InterceptedSign[8] == 2) { $SignPower[8] = (($PlanetTotalPower[4] + $PlanetTotalPower[9]) / 2);}
  if ($InterceptedSign[8] == 3) { $SignPower[8] = (($PlanetTotalPower[4] + $PlanetTotalPower[9]) / 2) * 2;}
  if ($InterceptedSign[9] == 0) { $SignPower[9] = $PlanetTotalPower[5] / 4;}
  if ($InterceptedSign[9] == 1) { $SignPower[9] = $PlanetTotalPower[5] / 2;}
  if ($InterceptedSign[9] == 2) { $SignPower[9] = $PlanetTotalPower[5];}
  if ($InterceptedSign[9] == 3) { $SignPower[9] = $PlanetTotalPower[5] * 2;}
  if ($InterceptedSign[10] == 0) { $SignPower[10] = $PlanetTotalPower[6] / 4;}
  if ($InterceptedSign[10] == 1) { $SignPower[10] = $PlanetTotalPower[6] / 2;}
  if ($InterceptedSign[10] == 2) { $SignPower[10] = $PlanetTotalPower[6];}
  if ($InterceptedSign[10] == 3) { $SignPower[10] = $PlanetTotalPower[6] * 2;}
  if ($InterceptedSign[11] == 0) { $SignPower[11] = (($PlanetTotalPower[6] + $PlanetTotalPower[7]) / 2) / 4;}
  if ($InterceptedSign[11] == 1) { $SignPower[11] = (($PlanetTotalPower[6] + $PlanetTotalPower[7]) / 2) / 2;}
  if ($InterceptedSign[11] == 2) { $SignPower[11] = (($PlanetTotalPower[6] + $PlanetTotalPower[7]) / 2);}
  if ($InterceptedSign[11] == 3) { $SignPower[11] = (($PlanetTotalPower[6] + $PlanetTotalPower[7]) / 2) * 2;}
  if ($InterceptedSign[12] == 0) { $SignPower[12] = (($PlanetTotalPower[5] + $PlanetTotalPower[8]) / 2) / 4;}
  if ($InterceptedSign[12] == 1) { $SignPower[12] = (($PlanetTotalPower[5] + $PlanetTotalPower[8]) / 2) / 2;}
  if ($InterceptedSign[12] == 2) { $SignPower[12] = (($PlanetTotalPower[5] + $PlanetTotalPower[8]) / 2);}
  if ($InterceptedSign[12] == 3) { $SignPower[12] = (($PlanetTotalPower[5] + $PlanetTotalPower[8]) / 2) * 2;}

  //find unoccupied sign harmony
  if ($InterceptedSign[1] == 0) { $SignHarmony[1] = $harmony[4] / 4;}
  if ($InterceptedSign[1] == 1) { $SignHarmony[1] = $harmony[4] / 2;}
  if ($InterceptedSign[1] == 2) { $SignHarmony[1] = $harmony[4];}
  if ($InterceptedSign[1] == 3) { $SignHarmony[1] = $harmony[4] * 2;}
  if ($InterceptedSign[2] == 0) { $SignHarmony[2] = $harmony[3] / 4;}
  if ($InterceptedSign[2] == 1) { $SignHarmony[2] = $harmony[3] / 2;}
  if ($InterceptedSign[2] == 2) { $SignHarmony[2] = $harmony[3];}
  if ($InterceptedSign[2] == 3) { $SignHarmony[2] = $harmony[3] * 2;}
  if ($InterceptedSign[3] == 0) { $SignHarmony[3] = $harmony[2] / 4;}
  if ($InterceptedSign[3] == 1) { $SignHarmony[3] = $harmony[2] / 2;}
  if ($InterceptedSign[3] == 2) { $SignHarmony[3] = $harmony[2];}
  if ($InterceptedSign[3] == 3) { $SignHarmony[3] = $harmony[2] * 2;}
  if ($InterceptedSign[4] == 0) { $SignHarmony[4] = $harmony[1] / 4;}
  if ($InterceptedSign[4] == 1) { $SignHarmony[4] = $harmony[1] / 2;}
  if ($InterceptedSign[4] == 2) { $SignHarmony[4] = $harmony[1];}
  if ($InterceptedSign[4] == 3) { $SignHarmony[4] = $harmony[1] * 2;}
  if ($InterceptedSign[5] == 0) { $SignHarmony[5] = $harmony[0] / 4;}
  if ($InterceptedSign[5] == 1) { $SignHarmony[5] = $harmony[0] / 2;}
  if ($InterceptedSign[5] == 2) { $SignHarmony[5] = $harmony[0];}
  if ($InterceptedSign[5] == 3) { $SignHarmony[5] = $harmony[0] * 2;}
  if ($InterceptedSign[6] == 0) { $SignHarmony[6] = $harmony[2] / 4;}
  if ($InterceptedSign[6] == 1) { $SignHarmony[6] = $harmony[2] / 2;}
  if ($InterceptedSign[6] == 2) { $SignHarmony[6] = $harmony[2];}
  if ($InterceptedSign[6] == 3) { $SignHarmony[6] = $harmony[2] * 2;}
  if ($InterceptedSign[7] == 0) { $SignHarmony[7] = $harmony[3] / 4;}
  if ($InterceptedSign[7] == 1) { $SignHarmony[7] = $harmony[3] / 2;}
  if ($InterceptedSign[7] == 2) { $SignHarmony[7] = $harmony[3];}
  if ($InterceptedSign[7] == 3) { $SignHarmony[7] = $harmony[3] * 2;}
  if ($InterceptedSign[8] == 0) { $SignHarmony[8] = (($harmony[4] + $harmony[9]) / 2) / 4;}
  if ($InterceptedSign[8] == 1) { $SignHarmony[8] = (($harmony[4] + $harmony[9]) / 2) / 2;}
  if ($InterceptedSign[8] == 2) { $SignHarmony[8] = (($harmony[4] + $harmony[9]) / 2);}
  if ($InterceptedSign[8] == 3) { $SignHarmony[8] = (($harmony[4] + $harmony[9]) / 2) * 2;}
  if ($InterceptedSign[9] == 0) { $SignHarmony[9] = $harmony[5] / 4;}
  if ($InterceptedSign[9] == 1) { $SignHarmony[9] = $harmony[5] / 2;}
  if ($InterceptedSign[9] == 2) { $SignHarmony[9] = $harmony[5];}
  if ($InterceptedSign[9] == 3) { $SignHarmony[9] = $harmony[5] * 2;}
  if ($InterceptedSign[10] == 0) { $SignHarmony[10] = $harmony[6] / 4;}
  if ($InterceptedSign[10] == 1) { $SignHarmony[10] = $harmony[6] / 2;}
  if ($InterceptedSign[10] == 2) { $SignHarmony[10] = $harmony[6];}
  if ($InterceptedSign[10] == 3) { $SignHarmony[10] = $harmony[6] * 2;}
  if ($InterceptedSign[11] == 0) { $SignHarmony[11] = (($harmony[6] + $harmony[7]) / 2) / 4;}
  if ($InterceptedSign[11] == 1) { $SignHarmony[11] = (($harmony[6] + $harmony[7]) / 2) / 2;}
  if ($InterceptedSign[11] == 2) { $SignHarmony[11] = (($harmony[6] + $harmony[7]) / 2);}
  if ($InterceptedSign[11] == 3) { $SignHarmony[11] = (($harmony[6] + $harmony[7]) / 2) * 2;}
  if ($InterceptedSign[12] == 0) { $SignHarmony[12] = (($harmony[5] + $harmony[8]) / 2) / 4;}
  if ($InterceptedSign[12] == 1) { $SignHarmony[12] = (($harmony[5] + $harmony[8]) / 2) / 2;}
  if ($InterceptedSign[12] == 2) { $SignHarmony[12] = (($harmony[5] + $harmony[8]) / 2);}
  if ($InterceptedSign[12] == 3) { $SignHarmony[12] = (($harmony[5] + $harmony[8]) / 2) * 2;}

  //find unoccupied sign discord
  if ($InterceptedSign[1] == 0) { $SignDiscord[1] = $discord[4] / 4;}
  if ($InterceptedSign[1] == 1) { $SignDiscord[1] = $discord[4] / 2;}
  if ($InterceptedSign[1] == 2) { $SignDiscord[1] = $discord[4];}
  if ($InterceptedSign[1] == 3) { $SignDiscord[1] = $discord[4] * 2;}
  if ($InterceptedSign[2] == 0) { $SignDiscord[2] = $discord[3] / 4;}
  if ($InterceptedSign[2] == 1) { $SignDiscord[2] = $discord[3] / 2;}
  if ($InterceptedSign[2] == 2) { $SignDiscord[2] = $discord[3];}
  if ($InterceptedSign[2] == 3) { $SignDiscord[2] = $discord[3] * 2;}
  if ($InterceptedSign[3] == 0) { $SignDiscord[3] = $discord[2] / 4;}
  if ($InterceptedSign[3] == 1) { $SignDiscord[3] = $discord[2] / 2;}
  if ($InterceptedSign[3] == 2) { $SignDiscord[3] = $discord[2];}
  if ($InterceptedSign[3] == 3) { $SignDiscord[3] = $discord[2] * 2;}
  if ($InterceptedSign[4] == 0) { $SignDiscord[4] = $discord[1] / 4;}
  if ($InterceptedSign[4] == 1) { $SignDiscord[4] = $discord[1] / 2;}
  if ($InterceptedSign[4] == 2) { $SignDiscord[4] = $discord[1];}
  if ($InterceptedSign[4] == 3) { $SignDiscord[4] = $discord[1] * 2;}
  if ($InterceptedSign[5] == 0) { $SignDiscord[5] = $discord[0] / 4;}
  if ($InterceptedSign[5] == 1) { $SignDiscord[5] = $discord[0] / 2;}
  if ($InterceptedSign[5] == 2) { $SignDiscord[5] = $discord[0];}
  if ($InterceptedSign[5] == 3) { $SignDiscord[5] = $discord[0] * 2;}
  if ($InterceptedSign[6] == 0) { $SignDiscord[6] = $discord[2] / 4;}
  if ($InterceptedSign[6] == 1) { $SignDiscord[6] = $discord[2] / 2;}
  if ($InterceptedSign[6] == 2) { $SignDiscord[6] = $discord[2];}
  if ($InterceptedSign[6] == 3) { $SignDiscord[6] = $discord[2] * 2;}
  if ($InterceptedSign[7] == 0) { $SignDiscord[7] = $discord[3] / 4;}
  if ($InterceptedSign[7] == 1) { $SignDiscord[7] = $discord[3] / 2;}
  if ($InterceptedSign[7] == 2) { $SignDiscord[7] = $discord[3];}
  if ($InterceptedSign[7] == 3) { $SignDiscord[7] = $discord[3] * 2;}
  if ($InterceptedSign[8] == 0) { $SignDiscord[8] = (($discord[4] + $discord[9]) / 2) / 4;}
  if ($InterceptedSign[8] == 1) { $SignDiscord[8] = (($discord[4] + $discord[9]) / 2) / 2;}
  if ($InterceptedSign[8] == 2) { $SignDiscord[8] = (($discord[4] + $discord[9]) / 2);}
  if ($InterceptedSign[8] == 3) { $SignDiscord[8] = (($discord[4] + $discord[9]) / 2) * 2;}
  if ($InterceptedSign[9] == 0) { $SignDiscord[9] = $discord[5] / 4;}
  if ($InterceptedSign[9] == 1) { $SignDiscord[9] = $discord[5] / 2;}
  if ($InterceptedSign[9] == 2) { $SignDiscord[9] = $discord[5];}
  if ($InterceptedSign[9] == 3) { $SignDiscord[9] = $discord[5] * 2;}
  if ($InterceptedSign[10] == 0) { $SignDiscord[10] = $discord[6] / 4;}
  if ($InterceptedSign[10] == 1) { $SignDiscord[10] = $discord[6] / 2;}
  if ($InterceptedSign[10] == 2) { $SignDiscord[10] = $discord[6];}
  if ($InterceptedSign[10] == 3) { $SignDiscord[10] = $discord[6] * 2;}
  if ($InterceptedSign[11] == 0) { $SignDiscord[11] = (($discord[6] + $discord[7]) / 2) / 4;}
  if ($InterceptedSign[11] == 1) { $SignDiscord[11] = (($discord[6] + $discord[7]) / 2) / 2;}
  if ($InterceptedSign[11] == 2) { $SignDiscord[11] = (($discord[6] + $discord[7]) / 2);}
  if ($InterceptedSign[11] == 3) { $SignDiscord[11] = (($discord[6] + $discord[7]) / 2) * 2;}
  if ($InterceptedSign[12] == 0) { $SignDiscord[12] = (($discord[5] + $discord[8]) / 2) / 4;}
  if ($InterceptedSign[12] == 1) { $SignDiscord[12] = (($discord[5] + $discord[8]) / 2) / 2;}
  if ($InterceptedSign[12] == 2) { $SignDiscord[12] = (($discord[5] + $discord[8]) / 2);}
  if ($InterceptedSign[12] == 3) { $SignDiscord[12] = (($discord[5] + $discord[8]) / 2) * 2;}


  for ($x = 0; $x <= 11; $x++)
  {
    $z = floor($L1[$x] / 30) + 1;

    $SignPower[$z] = $SignPower[$z] + $PlanetTotalPower[$x];
    $SignHarmony[$z] = $SignHarmony[$z] + $harmony[$x];
    $SignDiscord[$z] = $SignDiscord[$z] + $discord[$x];
  }
// done



//figure house interceptions - get how many signs are intercepted in each house
  for ($x = 1; $x <= 12; $x++)
  {
    $InterceptedHouse[$x] = 0;
  }

  for ($x = 1; $x <= 11; $x++)
  {
    $z = floor($hc_start_at_MC_with_1[$x] / 30) + 1;
  $y = floor($hc_start_at_MC_with_1[$x + 1] / 30) + 1;
  if ($y - $z < 0)
  {
    $y = $y + 12;
  }

  $InterceptedHouse[$x] = $y - $z;
  }
//done


//calculate house power
  $hc_start_at_Asc_with_1[13] = $hc_start_at_Asc_with_1[1];   //so we can loop from houses 1 - 12

  //find unoccupied house power for houses 1 - 12
  for ($x = 1; $x <= 12; $x++)
  {
    $tmp7 = floor($hc_start_at_Asc_with_1[$x] / 30) + 1;      //get sign on this house cusp
    $tmp8 = floor($hc_start_at_Asc_with_1[$x + 1] / 30) + 1;    //get sign on next house cusp

    if ($tmp8 - $tmp7 < 0) { $tmp8 = $tmp8 + 12;}

    //do ruler of sign on house cusp
    if ($tmp7 == 1) { $HousePower[$x] = $PlanetTotalPower[4] / 2;}
    if ($tmp7 == 2) { $HousePower[$x] = $PlanetTotalPower[3] / 2;}
    if ($tmp7 == 3) { $HousePower[$x] = $PlanetTotalPower[2] / 2;}
    if ($tmp7 == 4) { $HousePower[$x] = $PlanetTotalPower[1] / 2;}
    if ($tmp7 == 5) { $HousePower[$x] = $PlanetTotalPower[0] / 2;}
    if ($tmp7 == 6) { $HousePower[$x] = $PlanetTotalPower[2] / 2;}
    if ($tmp7 == 7) { $HousePower[$x] = $PlanetTotalPower[3] / 2;}
    if ($tmp7 == 8) { $HousePower[$x] = (($PlanetTotalPower[4] + $PlanetTotalPower[9]) / 2) / 2;}
    if ($tmp7 == 9) { $HousePower[$x] = $PlanetTotalPower[5] / 2;}
    if ($tmp7 == 10) { $HousePower[$x] = $PlanetTotalPower[6] / 2;}
    if ($tmp7 == 11) { $HousePower[$x] = (($PlanetTotalPower[6] + $PlanetTotalPower[7]) / 2) / 2;}
    if ($tmp7 == 12) { $HousePower[$x] = (($PlanetTotalPower[5] + $PlanetTotalPower[8]) / 2) / 2;}

    //do ruler of intercepted sign in the house
    if ($tmp8 - $tmp7 >= 2)
    {
//CHP1:
      $tmp7 = $tmp7 + 1;

      while ($tmp7 < $tmp8)
      {
        $t7 = $tmp7;
        if ($t7 > 12) { $t7 = $t7 - 12;}
        if ($t7 == 1) { $HousePower[$x] = $HousePower[$x] + $PlanetTotalPower[4] / 4;}
        if ($t7 == 2) { $HousePower[$x] = $HousePower[$x] + $PlanetTotalPower[3] / 4;}
        if ($t7 == 3) { $HousePower[$x] = $HousePower[$x] + $PlanetTotalPower[2] / 4;}
        if ($t7 == 4) { $HousePower[$x] = $HousePower[$x] + $PlanetTotalPower[1] / 4;}
        if ($t7 == 5) { $HousePower[$x] = $HousePower[$x] + $PlanetTotalPower[0] / 4;}
        if ($t7 == 6) { $HousePower[$x] = $HousePower[$x] + $PlanetTotalPower[2] / 4;}
        if ($t7 == 7) { $HousePower[$x] = $HousePower[$x] + $PlanetTotalPower[3] / 4;}
        if ($t7 == 8) { $HousePower[$x] = $HousePower[$x] + (($PlanetTotalPower[4] + $PlanetTotalPower[9]) / 2) / 4;}
        if ($t7 == 9) { $HousePower[$x] = $HousePower[$x] + $PlanetTotalPower[5] / 4;}
        if ($t7 == 10) { $HousePower[$x] = $HousePower[$x] + $PlanetTotalPower[6] / 4;}
        if ($t7 == 11) { $HousePower[$x] = $HousePower[$x] + (($PlanetTotalPower[6] + $PlanetTotalPower[7]) / 2) / 4;}
        if ($t7 == 12) { $HousePower[$x] = $HousePower[$x] + (($PlanetTotalPower[5] + $PlanetTotalPower[8]) / 2) / 4;}
        $tmp7 = $tmp7 + 1;
      }
    }
  }


  //find unoccupied house harmony for houses 1 - 12
  for ($x = 1; $x <= 12; $x++)
  {
    $tmp7 = floor($hc_start_at_Asc_with_1[$x] / 30) + 1;      //get sign on this house cusp
    $tmp8 = floor($hc_start_at_Asc_with_1[$x + 1] / 30) + 1;    //get sign on next house cusp

    if ($tmp8 - $tmp7 < 0) { $tmp8 = $tmp8 + 12;}

    //do ruler of sign on house cusp
    if ($tmp7 == 1) { $HouseHarmony[$x] = $harmony[4] / 2;}
    if ($tmp7 == 2) { $HouseHarmony[$x] = $harmony[3] / 2;}
    if ($tmp7 == 3) { $HouseHarmony[$x] = $harmony[2] / 2;}
    if ($tmp7 == 4) { $HouseHarmony[$x] = $harmony[1] / 2;}
    if ($tmp7 == 5) { $HouseHarmony[$x] = $harmony[0] / 2;}
    if ($tmp7 == 6) { $HouseHarmony[$x] = $harmony[2] / 2;}
    if ($tmp7 == 7) { $HouseHarmony[$x] = $harmony[3] / 2;}
    if ($tmp7 == 8) { $HouseHarmony[$x] = (($harmony[4] + $harmony[9]) / 2) / 2;}
    if ($tmp7 == 9) { $HouseHarmony[$x] = $harmony[5] / 2;}
    if ($tmp7 == 10) { $HouseHarmony[$x] = $harmony[6] / 2;}
    if ($tmp7 == 11) { $HouseHarmony[$x] = (($harmony[6] + $harmony[7]) / 2) / 2;}
    if ($tmp7 == 12) { $HouseHarmony[$x] = (($harmony[5] + $harmony[8]) / 2) / 2;}

    //do ruler of intercepted sign in the house
    if ($tmp8 - $tmp7 >= 2)
    {
//CHP1:
      $tmp7 = $tmp7 + 1;

      while ($tmp7 < $tmp8)
      {
        $t7 = $tmp7;
        if ($t7 > 12) { $t7 = $t7 - 12;}
        if ($t7 == 1) { $HouseHarmony[$x] = $HouseHarmony[$x] + $harmony[4] / 4;}
        if ($t7 == 2) { $HouseHarmony[$x] = $HouseHarmony[$x] + $harmony[3] / 4;}
        if ($t7 == 3) { $HouseHarmony[$x] = $HouseHarmony[$x] + $harmony[2] / 4;}
        if ($t7 == 4) { $HouseHarmony[$x] = $HouseHarmony[$x] + $harmony[1] / 4;}
        if ($t7 == 5) { $HouseHarmony[$x] = $HouseHarmony[$x] + $harmony[0] / 4;}
        if ($t7 == 6) { $HouseHarmony[$x] = $HouseHarmony[$x] + $harmony[2] / 4;}
        if ($t7 == 7) { $HouseHarmony[$x] = $HouseHarmony[$x] + $harmony[3] / 4;}
        if ($t7 == 8) { $HouseHarmony[$x] = $HouseHarmony[$x] + (($harmony[4] + $harmony[9]) / 2) / 4;}
        if ($t7 == 9) { $HouseHarmony[$x] = $HouseHarmony[$x] + $harmony[5] / 4;}
        if ($t7 == 10) { $HouseHarmony[$x] = $HouseHarmony[$x] + $harmony[6] / 4;}
        if ($t7 == 11) { $HouseHarmony[$x] = $HouseHarmony[$x] + (($harmony[6] + $harmony[7]) / 2) / 4;}
        if ($t7 == 12) { $HouseHarmony[$x] = $HouseHarmony[$x] + (($harmony[5] + $harmony[8]) / 2) / 4;}
        $tmp7 = $tmp7 + 1;
      }
    }
  }


  //find unoccupied house discord for houses 1 - 12
  for ($x = 1; $x <= 12; $x++)
  {
    $tmp7 = floor($hc_start_at_Asc_with_1[$x] / 30) + 1;      //get sign on this house cusp
    $tmp8 = floor($hc_start_at_Asc_with_1[$x + 1] / 30) + 1;    //get sign on next house cusp

    if ($tmp8 - $tmp7 < 0) { $tmp8 = $tmp8 + 12;}

    //do ruler of sign on house cusp
    if ($tmp7 == 1) { $HouseDiscord[$x] = $discord[4] / 2;}
    if ($tmp7 == 2) { $HouseDiscord[$x] = $discord[3] / 2;}
    if ($tmp7 == 3) { $HouseDiscord[$x] = $discord[2] / 2;}
    if ($tmp7 == 4) { $HouseDiscord[$x] = $discord[1] / 2;}
    if ($tmp7 == 5) { $HouseDiscord[$x] = $discord[0] / 2;}
    if ($tmp7 == 6) { $HouseDiscord[$x] = $discord[2] / 2;}
    if ($tmp7 == 7) { $HouseDiscord[$x] = $discord[3] / 2;}
    if ($tmp7 == 8) { $HouseDiscord[$x] = (($discord[4] + $discord[9]) / 2) / 2;}
    if ($tmp7 == 9) { $HouseDiscord[$x] = $discord[5] / 2;}
    if ($tmp7 == 10) { $HouseDiscord[$x] = $discord[6] / 2;}
    if ($tmp7 == 11) { $HouseDiscord[$x] = (($discord[6] + $discord[7]) / 2) / 2;}
    if ($tmp7 == 12) { $HouseDiscord[$x] = (($discord[5] + $discord[8]) / 2) / 2;}

    //do ruler of intercepted sign in the house
    if ($tmp8 - $tmp7 >= 2)
    {
//CHP1:
      $tmp7 = $tmp7 + 1;

      while ($tmp7 < $tmp8)
      {
        $t7 = $tmp7;
        if ($t7 > 12) { $t7 = $t7 - 12;}
        if ($t7 == 1) { $HouseDiscord[$x] = $HouseDiscord[$x] + $discord[4] / 4;}
        if ($t7 == 2) { $HouseDiscord[$x] = $HouseDiscord[$x] + $discord[3] / 4;}
        if ($t7 == 3) { $HouseDiscord[$x] = $HouseDiscord[$x] + $discord[2] / 4;}
        if ($t7 == 4) { $HouseDiscord[$x] = $HouseDiscord[$x] + $discord[1] / 4;}
        if ($t7 == 5) { $HouseDiscord[$x] = $HouseDiscord[$x] + $discord[0] / 4;}
        if ($t7 == 6) { $HouseDiscord[$x] = $HouseDiscord[$x] + $discord[2] / 4;}
        if ($t7 == 7) { $HouseDiscord[$x] = $HouseDiscord[$x] + $discord[3] / 4;}
        if ($t7 == 8) { $HouseDiscord[$x] = $HouseDiscord[$x] + (($discord[4] + $discord[9]) / 2) / 4;}
        if ($t7 == 9) { $HouseDiscord[$x] = $HouseDiscord[$x] + $discord[5] / 4;}
        if ($t7 == 10) { $HouseDiscord[$x] = $HouseDiscord[$x] + $discord[6] / 4;}
        if ($t7 == 11) { $HouseDiscord[$x] = $HouseDiscord[$x] + (($discord[6] + $discord[7]) / 2) / 4;}
        if ($t7 == 12) { $HouseDiscord[$x] = $HouseDiscord[$x] + (($discord[5] + $discord[8]) / 2) / 4;}
        $tmp7 = $tmp7 + 1;
      }
    }
  }


  //wrap it up and collect the totals
  for ($x = 0; $x <= 9; $x++)
  {
    $tmp7 = floor($house_pos[$x]);    //get house number
    $HousePower[$tmp7] = $HousePower[$tmp7] + $PlanetTotalPower[$x];
    $HouseHarmony[$tmp7] = $HouseHarmony[$tmp7] + $harmony[$x];
    $HouseDiscord[$tmp7] = $HouseDiscord[$tmp7] + $discord[$x];
  }

  $HousePower[1] = $HousePower[1] + $PlanetTotalPower[10];    //Asc
  $HouseHarmony[1] = $HouseHarmony[1] + $harmony[10];
  $HouseDiscord[1] = $HouseDiscord[1] + $discord[10];

  $HousePower[10] = $HousePower[10] + $PlanetTotalPower[11];  //MC
  $HouseHarmony[10] = $HouseHarmony[10] + $harmony[11];
  $HouseDiscord[10] = $HouseDiscord[10] + $discord[11];
//done


// accumulate and totalize all the data
  $planet_power = array();
  $planet_harmony = array();
  $sign_power = array();
  $sign_harmony = array();
  $house_power = array();
  $house_harmony = array();
  $totals = array();        //the order is pp, ph, sp, sh, hp, hh

  $totals[0] = 0;   //planet power
  $totals[1] = 0;   //planet harmony
  $totals[2] = 0;   //sign power
  $totals[3] = 0;   //sign harmony
  $totals[4] = 0;   //house power
  $totals[5] = 0;   //house harmony

  for ($y = 1; $y <= 12; $y++)
  {
    $planet_power[$y - 1] = $PlanetTotalPower[$y - 1];
    $planet_harmony[$y - 1] = $harmony[$y - 1] - $discord[$y - 1];

    $totals[0] += $PlanetTotalPower[$y - 1];
    $totals[1] += $harmony[$y - 1] - $discord[$y - 1];

    $sign_power[$y] = $SignPower[$y];
    $sign_harmony[$y] = $SignHarmony[$y] - $SignDiscord[$y];

    $totals[2] += $SignPower[$y];
    $totals[3] += $SignHarmony[$y] - $SignDiscord[$y];

    $house_power[$y] = $HousePower[$y];
    $house_harmony[$y] = $HouseHarmony[$y] - $HouseDiscord[$y];

    $totals[4] += $HousePower[$y];
    $totals[5] += $HouseHarmony[$y] - $HouseDiscord[$y];
  }
//done


  return 1;     //successful
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

?>
