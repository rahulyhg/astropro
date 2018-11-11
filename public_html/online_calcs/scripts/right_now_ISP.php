<?php
  session_start();

  include ('header_right_now_ISP.html');

  $use_browser_IP = True;
  $use_MaxMind_db = True;

  require_once ('../../../mysqli_connect_online_calcs_db_MYSQLI.php');

  $h_sys = "p";

  if ($use_browser_IP == True)
  {
    if ($use_MaxMind_db == True)
    {
      include("geoipcity.inc");
      include("geoipregionvars.php");

      $gi = geoip_open("GeoLiteCity.dat",GEOIP_STANDARD);

      $record = geoip_record_by_addr($gi, $_SERVER['REMOTE_ADDR']);

      $place[0] = $record->longitude;
      $place[1] = $record->latitude;
      $place[2] = $record->city;
      $place[3] = $record->region;
      $place[4] = $record->country_name;

      //print_r($place);

    }
    else
    {
      $URL = "http://netgeo.caida.org/perl/netgeo.cgi?target=" . $_SERVER['REMOTE_ADDR'];
      $NetGeoHTML = file_get_contents($URL, 2000);

      preg_match ("/LONG:(.*)/i", $NetGeoHTML, $temp);
      $place[0] = substr($temp[1], 0, strlen($temp[1]) - 4);

      preg_match ("/LAT:(.*)/i", $NetGeoHTML, $temp);
      $place[1] = substr($temp[1], 0, strlen($temp[1]) - 4);

      preg_match ("/CITY:(.*)/i", $NetGeoHTML, $temp);
      $place[2] = substr($temp[1], 0, strlen($temp[1]) - 4);

      preg_match ("/STATE:(.*)/i", $NetGeoHTML, $temp);
      $place[3] = substr($temp[1], 0, strlen($temp[1]) - 4);

      preg_match ("/COUNTRY:(.*)/i", $NetGeoHTML, $temp);
      $place[4] = substr($temp[1], 0, strlen($temp[1]) - 4);

      settype($place[0], "float");
      settype($place[1], "float");
    }
  }
  else
  {
    // change your location here
    $place[0] = "23.71667";
    $place[1] = "37.96667";
    $place[2] = "Athens";
    $place[3] = "";
    $place[4] = "Greece";
  }

  $my_longitude = $place[0];
  $my_latitude = $place[1];


  // calculate astronomic data
  $swephsrc = './sweph';    //sweph MUST be in a folder no less than at this level
  $sweph = './sweph';

  // Unset any variables not initialized elsewhere in the program
  unset($PATH,$out,$pl_name,$longitude1,$speed1,$house_pos, $ruling_pl, $pl_hour);

  //get date and time right now
  $date_now = date ("Y-m-d");

  $inmonth = gmdate("m");
  $inday = gmdate("d");
  $inyear = gmdate("Y");

  $inmins = gmdate("i");
  $insecs = "0";

  if ($use_browser_IP == True)
  {
    $inhours = gmdate("H");
    $intz = 0;
    // adjust date and time for minus hour due to time zone taking the hour negative
    $utdatenow = strftime("%d.%m.%Y", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));
    $utnow = strftime("%H:%M:%S", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));
  }
  else
  {
    $inhours = gmdate("H") + 2;
    $intz = -2;
    // adjust date and time for minus hour due to time zone taking the hour negative
    $utdatenow = strftime("%d.%m.%Y", mktime($inhours - 2, $inmins, $insecs, $inmonth, $inday, $inyear));
    $utnow = strftime("%H:%M:%S", mktime($inhours - 2, $inmins, $insecs, $inmonth, $inday, $inyear));
  }



  putenv("PATH=$PATH:$swephsrc");

  // get LAST_PLANET planets and all house cusps
  exec ("swetest -edir$sweph -b$utdatenow -ut$utnow -p0123456789DAttt -eswe -house$my_longitude,$my_latitude,$h_sys -flsj -g, -head", $out);

  // Each line of output data from swetest is exploded into array $row, giving these elements:
  // 0 = longitude
  // 1 = speed
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
        if (($pl >= $longitude1[$x + LAST_PLANET] And $pl < 360) Or ($pl < $longitude1[$x + LAST_PLANET + 1] And $pl >= 0))
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


//display right now data
  echo "<center>";

//  echo "<FONT color='#0000ff' SIZE='4' FACE='Arial'><b>Transits</b></font><br />";
//  echo "<FONT color='#0000ff' SIZE='2' FACE='Arial'>(in " . $place[2] . ", " . $place[3] . " - " . $place[4] . ")<br />";
//  echo "(longitude =  " . $place[0] . ", latitude = " . $place[1] . ")</font><br />";
//  echo '<FONT color="#0000ff" SIZE="3" FACE="Arial"><b>On ' . strftime("%A, %B %d, %Y<br />%X (time zone = GMT)</b><br />\n", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));
//  echo "</font>";

  if ($use_browser_IP == True)
  {
    $line1 = "Transits in " . trim($place[2]) . ", " . trim($place[3]) . " - " . trim($place[4]) . " (longitude =  " . $place[0] . ", latitude = " . $place[1] . ")";
    $line2 = strftime("%A, %B %d, %Y at %H:%M (time zone = GMT)", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));
  }
  else
  {
    $line1 = "Transits in " . $place[2] . ", " . $place[4] . " (longitude =  " . $place[0] . ", latitude = " . $place[1] . ")";
    $line2 = strftime("%A, %B %d, %Y at %H:%M (time zone = GMT plus 2 hours)", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));
  }

  echo "</center>";

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
  $_SESSION['right_now_ISP_p1'] = $longitude1;
  $_SESSION['right_now_ISP_hc1'] = $hc1;
  $_SESSION['right_now_ISP_house_pos1'] = $house_pos1;

  $wheel_width = 640;
  $wheel_height = $wheel_width + 50;    //includes space at top of wheel for header

  echo "<center>";
  echo "<img border='0' src='right_now_ISP_wheel.php?rx1=$rx1&l1=$line1&l2=$line2' width='$wheel_width' height='$wheel_height'>";

  echo "<br><br>";


  require_once("sr.php");     //18 April 2010

  $tempXX = $longitude1[LAST_PLANET + 2];

  $longitude1[LAST_PLANET + 2] = $hc1[10];
  $L2_height = Detect_num_same_deg_planets_max($longitude1);
  $L2_height = ($L2_height + 1) * 50;

  echo "<img border='0' src='deg_line_ISP.php?rx1=$rx1' width='740' height='$L2_height'>";

  $longitude1[LAST_PLANET + 2] = $tempXX;


  echo "</center>";
  echo "<br /><br />";


//Moon aspectarian and void of course Moon
  include ('moon_aspects_now.php');


//Planetary hours
  echo "<center><font size='5'><strong><font face='Arial'>Planetary Hours</font></strong></font></center><br />";

  $ew = 1;
  if ($my_longitude <= 0) { $ew = -1; }

  $starting_JD = gregoriantojd($inmonth, $inday, $inyear) + ($ew * 0.25);

  $intz = round(abs($my_longitude) / 15);

  if ($my_longitude < 0)
  {
    $intz = -$intz;
  }

  echo "<center><font size='2'>(time zone used = " . $intz . " hours from GMT - adjust the below times if your time zone is different than given)</font></center><br />";

  $result = exec ("swetest -edir$sweph -bj$starting_JD -eswe -geopos$my_longitude,$my_latitude,400 -rise");

  $first_colon = strpos($result, ":") + 1;
  $rise_hour = mid($result, $first_colon - 2, 2) + $intz;
  if ($rise_hour >= 24)
  {
    $rise_hour = $rise_hour - 24;
  }
  $rise_minute = mid($result, $first_colon + 1, 2);

  $second_colon = strpos($result, ":", $first_colon + 1) + 1;
  $rise_seconds = mid($result, $second_colon + 1, 2);

  $third_colon = strpos($result, ":", $second_colon + 1) + 1;
  $set_hour = mid($result, $third_colon - 2, 2) + $intz;
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


//display right now data
  echo "<center><font size='5'><strong><font face='Arial'>Planet Positions</font></strong></font><br /></center>";

  echo '<center><table width="40%" cellpadding="0" cellspacing="0" border="0">',"<br />";

  echo '<tr>';
  echo "<td><font color='#0000ff'><b> Planet </b></font></td>";
  echo "<td><font color='#0000ff'><b> Longitude </b></font></td>";
  echo "<td><font color='#0000ff'><b> House<br />position </b></font></td>";
  echo '</tr>';

  for ($i = 0; $i <= LAST_PLANET; $i++)
  {
    echo '<tr>';
    echo "<td>" . $pl_name[$i] . "</td>";
    echo "<td><font face='Courier New'>" . Convert_Longitude($longitude1[$i]) . " " . Mid($rx1, $i + 1, 1) . "</font></td>";
    $hse = floor($house_pos1[$i]);
    if ($hse < 10)
    {
      echo "<td>&nbsp;&nbsp;&nbsp;&nbsp; " . $hse . "</td>";
    }
    else
    {
      echo "<td>&nbsp;&nbsp;&nbsp;" . $hse . "</td>";
    }

    echo '</tr>';
  }

  echo '<tr>';
  echo "<td> &nbsp </td>";
  echo "<td> &nbsp </td>";
  echo "<td> &nbsp </td>";

  echo '</tr>';
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

  echo '</table></center>',"<br />";
  echo "<br />";


  // display right now data - aspect table
  echo "<center><font size='5'><strong><font face='Arial'>Planetary Aspects</font></strong></font><br /></center>";

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

  for ($i = 0; $i <= LAST_PLANET + 2; $i++)
  {
    echo "<tr><td colspan='4'>&nbsp;</td></tr>";
    for ($j = 0; $j <= LAST_PLANET + 2; $j++)
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
        $orb = 3;
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

  echo "</table></center>";
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
  echo "<center><table width='80%' cellpadding='0' cellspacing='0' border='0'>";

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
  $sql = "SELECT transits_at_your_ISP FROM reports";
  $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);
  $row = mysqli_fetch_array($result);
  $count = $row[transits_at_your_ISP] + 1;

  $sql = "UPDATE reports SET transits_at_your_ISP = '$count'";
  $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);


  echo "<br /><br />";

  include ('footer.html');
  exit();


Function left($leftstring, $leftlength)
{
  return(substr($leftstring, 0, $leftlength));
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
