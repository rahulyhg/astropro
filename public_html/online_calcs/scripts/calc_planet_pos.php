<?php
  // connect to and point to the proper database
  require_once ('../../../mysqli_connect_online_calcs_db_MYSQLI.php');


Function CalculatePlanets($id, &$longitude1, &$declination1, &$house_pos1, &$longitude2, &$declination2, &$house_pos2, &$hob, &$mob)
{
  require('../../../mysqli_connect_online_calcs_db_MYSQLI.php');

  for ($xx = 1; $xx <= 2; $xx++)
  {
    //fetch all data for this record
    $sql = "SELECT * FROM birth_info WHERE ID='$id[$xx]'";
    $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);
    $row = mysqli_fetch_array($result);

    //assign data from database to local variables
    $secs = "0";
    if ($row['timezone'] < 0)
    {
      $tz = $row['timezone'];
    }
    else
    {
      $tz = "+" . $row['timezone'];
    }

    if ($xx == 1)
    {
      CalculatePlanetHousePositions($id[$xx], $longitude, $declination, $house_pos);

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
      CalculatePlanetHousePositions($id[$xx], $longitude, $declination, $house_pos);

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


Function CalculatePlanetHousePositions($last_id, &$longitude, &$declination, &$house_pos)
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
