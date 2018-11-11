<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
session_start();

  
  $months = array (0 => 'Choose month', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
  $my_error = "";

  // check if the form has been submitted
  if (isset($_POST['submitted']))
  {
    // get all variables from form
    $h_sys = safeEscapeString($_POST["h_sys"]);
    $name = safeEscapeString($_POST["name"]);

    $month = safeEscapeString($_POST["month"]);
    $day = safeEscapeString($_POST["day"]);
    $year = safeEscapeString($_POST["year"]);

    $hour = safeEscapeString($_POST["hour"]);
    $minute = safeEscapeString($_POST["minute"]);

    $timezone = safeEscapeString($_POST["timezone"]);

    $long_deg = safeEscapeString($_POST["long_deg"]);
    $long_min = safeEscapeString($_POST["long_min"]);
    $ew = safeEscapeString($_POST["ew"]);

    $lat_deg = safeEscapeString($_POST["lat_deg"]);
    $lat_min = safeEscapeString($_POST["lat_min"]);
    $ns = safeEscapeString($_POST["ns"]);

    $name = $_SESSION['username'];
    $month = $_SESSION['month'];
    $day = $_SESSION['day'];
    $year = $_SESSION['year'];
    $hour = $_SESSION['hour'];
    $minute = $_SESSION['minute'];
    $timezone = $_SESSION['timezone'];
    $long_deg = $_SESSION['long_deg'];
    $ew = $_SESSION['ew'];
    $long_min = $_SESSION['long_min'];
    $lat_deg = $_SESSION['lat_deg'];
    $ns = $_SESSION['ns'];
    $lat_min = $_SESSION['lat_min'];

    // set cookie containing natal data here
    setcookie ('name', stripslashes($name), time() + 60 * 60 * 24 * 30, '/', '', 0);

    setcookie ('month', $month, time() + 60 * 60 * 24 * 30, '/', '', 0);
    setcookie ('day', $day, time() + 60 * 60 * 24 * 30, '/', '', 0);
    setcookie ('year', $year, time() + 60 * 60 * 24 * 30, '/', '', 0);

    setcookie ('hour', $hour, time() + 60 * 60 * 24 * 30, '/', '', 0);
    setcookie ('minute', $minute, time() + 60 * 60 * 24 * 30, '/', '', 0);

    setcookie ('timezone', $timezone, time() + 60 * 60 * 24 * 30, '/', '', 0);

    setcookie ('long_deg', $long_deg, time() + 60 * 60 * 24 * 30, '/', '', 0);
    setcookie ('long_min', $long_min, time() + 60 * 60 * 24 * 30, '/', '', 0);
    setcookie ('ew', $ew, time() + 60 * 60 * 24 * 30, '/', '', 0);

    setcookie ('lat_deg', $lat_deg, time() + 60 * 60 * 24 * 30, '/', '', 0);
    setcookie ('lat_min', $lat_min, time() + 60 * 60 * 24 * 30, '/', '', 0);
    setcookie ('ns', $ns, time() + 60 * 60 * 24 * 30, '/', '', 0);

    include("validation_class.php");

    //error check
    $my_form = new Validate_fields;

    $my_form->check_4html = true;

    $my_form->add_text_field("Name", $name, "text", "y", 40);

    $my_form->add_text_field("Month", $month, "text", "y", 2);
    $my_form->add_text_field("Day", $day, "text", "y", 2);
    $my_form->add_text_field("Year", $year, "text", "y", 4);

    $my_form->add_text_field("Hour", $hour, "text", "y", 2);
    $my_form->add_text_field("Minute", $minute, "text", "y", 2);

    $my_form->add_text_field("Time zone", $timezone, "text", "y", 12);

    $my_form->add_text_field("Longitude degree", $long_deg, "text", "y", 3);
    $my_form->add_text_field("Longitude minute", $long_min, "text", "y", 2);
    $my_form->add_text_field("Longitude E/W", $ew, "text", "y", 2);

    $my_form->add_text_field("Latitude degree", $lat_deg, "text", "y", 2);
    $my_form->add_text_field("Latitude minute", $lat_min, "text", "y", 2);
    $my_form->add_text_field("Latitude N/S", $ns, "text", "y", 2);

    // additional error checks on user-entered data
    if ($month == 0)
    {
      $my_error .= "Please enter a month.<br>";
    }

    if ($month != "" And $day != "" And $year != "")
    {
      if (!$date = checkdate(settype ($month, "integer"), settype ($day, "integer"), settype ($year, "integer")))
      {
        $my_error .= "The date of birth you entered is not valid.<br>";
      }
    }

    if (($year < 1800) Or ($year >= 2400))
    {
      $my_error .= "Please enter a year between 1800 and 2399.<br>";
    }

    if (($hour < 0) Or ($hour > 23))
    {
      $my_error .= "Birth hour must be between 0 and 23.<br>";
    }

    if (($minute < 0) Or ($minute > 59))
    {
      $my_error .= "Birth minute must be between 0 and 59.<br>";
    }

    if (($long_deg < 0) Or ($long_deg > 179))
    {
      $my_error .= "Longitude degrees must be between 0 and 179.<br>";
    }

    if (($long_min < 0) Or ($long_min > 59))
    {
      $my_error .= "Longitude minutes must be between 0 and 59.<br>";
    }

    if (($lat_deg < 0) Or ($lat_deg > 65))
    {
      $my_error .= "Latitude degrees must be between 0 and 65.<br>";
    }

    if (($lat_min < 0) Or ($lat_min > 59))
    {
      $my_error .= "Latitude minutes must be between 0 and 59.<br>";
    }

    if (($ew == '-1') And ($timezone > 2))
    {
      $my_error .= "You have marked West longitude but set an east time zone.<br>";
    }

    if (($ew == '1') And ($timezone < 0))
    {
      $my_error .= "You have marked East longitude but set a west time zone.<br>";
    }

    $ew_txt = "e";
    if ($ew < 0) { $ew_txt = "w"; }

    $ns_txt = "s";
    if ($ns > 0) { $ns_txt = "n"; }

    $validation_error = $my_form->validation();

    if ((!$validation_error) || ($my_error != ""))
    {
      $error = $my_form->create_msg();
      echo "<TABLE align='center' WIDTH='98%' BORDER='0' CELLSPACING='15' CELLPADDING='0'><tr><td><center><b>";
      echo "<font color='#ff0000' size=+2>Error! - The following error(s) occurred:</font><br>";

      if ($error)
      {
        echo $error . $my_error;
      }
      else
      {
        echo $error . "<br>" . $my_error;
      }

      echo "</font>";
      echo "<font color='#c020c0'";
      echo "<br>PLEASE RE-ENTER YOUR TIME ZONE DATA. THANK YOU.<br><br>";
      echo "</font>";
      echo "</b></center></td></tr></table>";
    }
    else
    {
      // no errors in filling out form, so process form
      // calculate astronomic data
      $swephsrc = 'sweph';
      $sweph = 'sweph';

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


      //gather data in order to create a file name for the chartwheel graphic
      $restored_name = stripslashes($name);
      $filename = $restored_name . "_" . strftime("%Y_%m_%d_%H_%M_tz=$intz" . "_", mktime($hour, $inmins, $insecs, $inmonth, $inday, $inyear));
      $filename .= $long_deg . $ew_txt . sprintf("%02d", $long_min) . "_" . $lat_deg . $ns_txt . sprintf("%02d", $lat_min) . ".png";

      $grids_filename = "./grids/" . $filename;
      $chartwheel_filename = "./charts/" . $filename;
  
      $_SESSION['chartwheel_filename'] = $chartwheel_filename;
      $_SESSION['grids_filename'] = $grids_filename;


      // adjust date and time for minus hour due to time zone taking the hour negative
      $utdatenow = strftime("%d.%m.%Y", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));
      $utnow = strftime("%H:%M:%S", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));

      putenv("PATH=$PATH:$swephsrc");

      // get LAST_PLANET planets and all house cusps
      if (strlen($h_sys) != 1)
      {
        $h_sys = "p";
      }

      exec ("swetest -edir$sweph -b$utdatenow -ut$utnow -p0123456789DAttt -eswe -house$my_longitude,$my_latitude,$h_sys -flsj -g, -head", $out);

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


      include("constants.php");         // this is here because we must rename the planet names


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
      $longitude1[LAST_PLANET] = $longitude1[LAST_PLANET + 16];     //Asc = +13, MC = +14, RAMC = +15, Vertex = +16


      $hr_ob = $hour;
      $min_ob = $minute;

      $ubt1 = 0;
      if (($hr_ob == 12) And ($min_ob == 0)) { $ubt1 = 1; }             // this person has an unknown birth time


      if ($ubt1 == 1)
      {
        $longitude1[1 + LAST_PLANET] = 0;       //make flat chart with natural houses
        $longitude1[2 + LAST_PLANET] = 30;
        $longitude1[3 + LAST_PLANET] = 60;
        $longitude1[4 + LAST_PLANET] = 90;
        $longitude1[5 + LAST_PLANET] = 120;
        $longitude1[6 + LAST_PLANET] = 150;
        $longitude1[7 + LAST_PLANET] = 180;
        $longitude1[8 + LAST_PLANET] = 210;
        $longitude1[9 + LAST_PLANET] = 240;
        $longitude1[10 + LAST_PLANET] = 270;
        $longitude1[11 + LAST_PLANET] = 300;
        $longitude1[12 + LAST_PLANET] = 330;
      }
      

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


      $secs = "0";
      
      $tz = "+" . $timezone;
      if ($timezone < 0) { $tz = $timezone; }

      $profile_birthdata = strftime("%B %d, %Y at %H:%M (GMT $tz hours), ", mktime(intval($hour), intval($minute), intval($secs), intval($month), intval($day), intval($year)));
      $profile_birthdata .= $long_deg . $ew_txt . sprintf("%02d", $long_min) . ", " . $lat_deg . $ns_txt . sprintf("%02d", $lat_min);

      $hr_ob = $hour;
      $min_ob = $minute;

      $ubt1 = 0;
      if (($hr_ob == 12) And ($min_ob == 0)) { $ubt1 = 1; }              // this person has an unknown birth time

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

      for ($i = 1; $i <= LAST_PLANET; $i++)
      {
        $hc1[$i] = $longitude1[LAST_PLANET + $i];
      }


      $_SESSION['nL1'] = $longitude1;
      $_SESSION['nHC1'] = $hc1;
      $_SESSION['nH1'] = $house_pos1;
      
      include ('create_natal_wheel.php');           //create the chartwheel
      
      include ('create_natal_aspect_grid.php');     //create the aspect grid
      
      include ('create_natal_pdf.php');             //create a .pdf file

      exit();
    }
  }
  else
  {
    $name = stripslashes($_COOKIE['name']);

    $month = $_COOKIE['month'];
    $day = $_COOKIE['day'];
    $year = $_COOKIE['year'];

    $hour = $_COOKIE['hour'];
    $minute = $_COOKIE['minute'];

    $timezone = $_COOKIE['timezone'];

    $long_deg = $_COOKIE["long_deg"];
    $long_min = $_COOKIE["long_min"];
    $ew = $_COOKIE["ew"];

    $lat_deg = $_COOKIE["lat_deg"];
    $lat_min = $_COOKIE["lat_min"];
    $ns = $_COOKIE["ns"];
  }
  

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" target="_blank" style="margin: 0px 20px;">
  <fieldset><legend><font size='4'><b>Data entry for Natal Chart - Enter your birth information</b></font></legend>

  &nbsp;&nbsp;<font color="#ff0000"><b>All fields are required.</b></font><br>

  <table style="font-size:12px;">
    <TR>
      <TD>
        <P align="right">Name:</P>
      </TD>

      <TD>
        <?php 
         echo "<input class='form-control' placeholder='Choose a username:' maxLength=12 size=17 name='username' value=" . $name . ">"
        ?>
      </TD>
    </TR>

    <TR>
      <TD>
        <P align="right">Birth date:</P>
      </TD>

      <TD>
 
        <?php
        echo '<select name="month">';
        foreach ($months as $key => $value)
        {
          echo "<option value=\"$key\"";
          if ($key == $month)
          {
            echo ' selected="selected"';
          }
          echo ">$value</option>\n";
        }
        echo '</select>';
        ?>

      <?php 
       echo "<input class='form-control' name='day' value=" . $day . ">"
        ?>

        <b>,</b>&nbsp;
        <?php        
       echo "<input class='form-control' name='year' value=" . $year . ">"
        ?>
     </TD>
    </TR>

    <TR>
      <td valign="top"><P align="right">Birth time:</P></td>
      <TD>
        <INPUT maxlength="2" size="2" name="hour" value="<?php echo $hour; ?>">
        <b>:</b>
        <INPUT maxlength="2" size="2" name="minute" value="<?php echo $minute; ?>">

        <br>
      </TD>
    </TR>

    <TR>
      <td valign="top">
        <P align="right">
        <b>&nbsp;</b>
        </P>
      </td>

    
    </TR>
    <TR>
      <td valign="top"><P align="right">Time zone:</P></td>

      <TD>
        <select name="timezone" size="1">
          <?php
          echo "<option value='' ";
          if ($timezone == ""){ echo " selected"; }
          echo "> Select Time Zone </option>";

          echo "<option value='-12' ";
          if ($timezone == "-12"){ echo " selected"; }
          echo ">GMT -12:00 hrs - IDLW</option>";

          echo "<option value='-11' ";
          if ($timezone == "-11"){ echo " selected"; }
          echo ">GMT -11:00 hrs - BET or NT</option>";

          echo "<option value='-10.5' ";
          if ($timezone == "-10.5"){ echo " selected"; }
          echo ">GMT -10:30 hrs - HST</option>";

          echo "<option value='-10' ";
          if ($timezone == "-10"){ echo " selected"; }
          echo ">GMT -10:00 hrs - AHST</option>";

          echo "<option value='-9.5' ";
          if ($timezone == "-9.5"){ echo " selected"; }
          echo ">GMT -09:30 hrs - HDT or HWT</option>";

          echo "<option value='-9' ";
          if ($timezone == "-9"){ echo " selected"; }
          echo ">GMT -09:00 hrs - YST or AHDT or AHWT</option>";

          echo "<option value='-8' ";
          if ($timezone == "-8"){ echo " selected"; }
          echo ">GMT -08:00 hrs - PST or YDT or YWT</option>";

          echo "<option value='-7' ";
          if ($timezone == "-7"){ echo " selected"; }
          echo ">GMT -07:00 hrs - MST or PDT or PWT</option>";

          echo "<option value='-6' ";
          if ($timezone == "-6"){ echo " selected"; }
          echo ">GMT -06:00 hrs - CST or MDT or MWT</option>";

          echo "<option value='-5' ";
          if ($timezone == "-5"){ echo " selected"; }
          echo ">GMT -05:00 hrs - EST or CDT or CWT</option>";

          echo "<option value='-4' ";
          if ($timezone == "-4"){ echo " selected"; }
          echo ">GMT -04:00 hrs - AST or EDT or EWT</option>";

          echo "<option value='-3.5' ";
          if ($timezone == "-3.5"){ echo " selected"; }
          echo ">GMT -03:30 hrs - NST</option>";

          echo "<option value='-3' ";
          if ($timezone == "-3"){ echo " selected"; }
          echo ">GMT -03:00 hrs - BZT2 or AWT</option>";

          echo "<option value='-2' ";
          if ($timezone == "-2"){ echo " selected"; }
          echo ">GMT -02:00 hrs - AT</option>";

          echo "<option value='-1' ";
          if ($timezone == "-1"){ echo " selected"; }
          echo ">GMT -01:00 hrs - WAT</option>";

          echo "<option value='0' ";
          if ($timezone == "0"){ echo " selected"; }
          echo ">Greenwich Mean Time - GMT or UT</option>";

          echo "<option value='1' ";
          if ($timezone == "1"){ echo " selected"; }
          echo ">GMT +01:00 hrs - CET or MET or BST</option>";

          echo "<option value='2' ";
          if ($timezone == "2"){ echo " selected"; }
          echo ">GMT +02:00 hrs - EET or CED or MED or BDST or BWT</option>";

          echo "<option value='3' ";
          if ($timezone == "3"){ echo " selected"; }
          echo ">GMT +03:00 hrs - BAT or EED</option>";

          echo "<option value='3.5' ";
          if ($timezone == "3.5"){ echo " selected"; }
          echo ">GMT +03:30 hrs - IT</option>";

          echo "<option value='4' ";
          if ($timezone == "4"){ echo " selected"; }
          echo ">GMT +04:00 hrs - USZ3</option>";

          echo "<option value='5' ";
          if ($timezone == "5"){ echo " selected"; }
          echo ">GMT +05:00 hrs - USZ4</option>";

          echo "<option value='5.5' ";
          if ($timezone == "5.5"){ echo " selected"; }
          echo ">GMT +05:30 hrs - IST</option>";

          echo "<option value='6' ";
          if ($timezone == "6"){ echo " selected"; }
          echo ">GMT +06:00 hrs - USZ5</option>";

          echo "<option value='6.5' ";
          if ($timezone == "6.5"){ echo " selected"; }
          echo ">GMT +06:30 hrs - NST</option>";

          echo "<option value='7' ";
          if ($timezone == "7"){ echo " selected"; }
          echo ">GMT +07:00 hrs - SST or USZ6</option>";

          echo "<option value='7.5' ";
          if ($timezone == "7.5"){ echo " selected"; }
          echo ">GMT +07:30 hrs - JT</option>";

          echo "<option value='8' ";
          if ($timezone == "8"){ echo " selected"; }
          echo ">GMT +08:00 hrs - AWST or CCT</option>";

          echo "<option value='8.5' ";
          if ($timezone == "8.5"){ echo " selected"; }
          echo ">GMT +08:30 hrs - MT</option>";

          echo "<option value='9' ";
          if ($timezone == "9"){ echo " selected"; }
          echo ">GMT +09:00 hrs - JST or AWDT</option>";

          echo "<option value='9.5' ";
          if ($timezone == "9.5"){ echo " selected"; }
          echo ">GMT +09:30 hrs - ACST or SAT or SAST</option>";

          echo "<option value='10' ";
          if ($timezone == "10"){ echo " selected"; }
          echo ">GMT +10:00 hrs - AEST or GST</option>";

          echo "<option value='10.5' ";
          if ($timezone == "10.5"){ echo " selected"; }
          echo ">GMT +10:30 hrs - ACDT or SDT or SAD</option>";

          echo "<option value='11' ";
          if ($timezone == "11"){ echo " selected"; }
          echo ">GMT +11:00 hrs - UZ10 or AEDT</option>";

          echo "<option value='11.5' ";
          if ($timezone == "11.5"){ echo " selected"; }
          echo ">GMT +11:30 hrs - NZ</option>";

          echo "<option value='12' ";
          if ($timezone == "12"){ echo " selected"; }
          echo ">GMT +12:00 hrs - NZT or IDLE</option>";

          echo "<option value='12.5' ";
          if ($timezone == "12.5"){ echo " selected"; }
          echo ">GMT +12:30 hrs - NZS</option>";

          echo "<option value='13' ";
          if ($timezone == "13"){ echo " selected"; }
          echo ">GMT +13:00 hrs - NZST</option>";
          ?>
        </select>

        <br>
      </TD>
    </TR>

    <TR>
      <td valign="top"><P align="right">Longitude:</P></td>
      <TD>
        <INPUT maxlength="3" size="3" name="long_deg" value="<?php echo $long_deg; ?>">
        <INPUT maxlength="2" size="3" name="ew" value="<?php echo $ew; ?>">
        <INPUT maxlength="2" size="2" name="long_min" value="<?php echo $long_min; ?>">
        <font color="#0000ff">
        (example: Chicago is 87 W 39, Sydney is 151 E 13)
        </font>
      </TD>
    </TR>

    <TR>
      <td valign="top"><P align="right">Latitude:</P></td>

      <TD>
        <INPUT maxlength="2" size="3" name="lat_deg" value="<?php echo $lat_deg; ?>">
        <INPUT maxlength="2" size="3" name="ns" value="<?php echo $ns; ?>">
        <INPUT maxlength="2" size="2" name="lat_min" value="<?php echo $lat_min; ?>">
        <font color="#0000ff">
        (example: Chicago is 41 N 51, Sydney is 33 S 52)
        </font>
        <br><br>
      </TD>
    </TR>
  </table>

  <br>
  <center>
  <font color="#ff0000"><b>Most people mess up the time zone selection. Please make sure your selection is correct.</b></font><br><br>
  <input type="hidden" name="submitted" value="TRUE">
  <INPUT type="submit" name="submit" value="Submit data (AFTER DOUBLE-CHECKING IT FOR ERRORS)" align="middle" style="background-color:#66ff66;color:#000000;font-size:16px;font-weight:bold">
  </center>

  <br>
  </fieldset>
</form>

<?php
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

  return $deg . " " . $signs[$sign_num] . " " . $min . "' " . $full_sec . chr(34);
}


Function mid($midstring, $midstart, $midlength)
{
  return(substr($midstring, $midstart-1, $midlength));
}


Function safeEscapeString($string)
{
// replace HTML tags '<>' with '[]'
  $temp1 = str_replace("<", "[", $string);
  $temp2 = str_replace(">", "]", $temp1);

// but keep <br> or <br>
// turn <br> into <br> so later it will be turned into ""
// using just <br> will add extra blank lines
  $temp1 = str_replace("[br]", "<br>", $temp2);
  $temp2 = str_replace("[br /]", "<br>", $temp1);

  if (get_magic_quotes_gpc())
  {
    return $temp2;
  }
  else
  {
    return mysql_escape_string($temp2);
  }
}

?>
