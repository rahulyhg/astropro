<?php
  include ('../accesscontrol.php');

  if ($is_logged_in == False)
  {
    echo "You are not yet logged in.";
    exit();
  }

  include ('../header.html');
  include ('../constants.php');

  echo "</center>";

  // connect to the database and point to the proper database
  require_once ('../../../mysqli_connect_online_calcs_db_MYSQLI.php');
  require_once ('../../../my_functions_MYSQLI.php');

  $my_error = "";

  // check if the form has been submitted
  if (isset($_POST['submitted']))
  {
    // get all variables from form - Person
    $name1 = safeEscapeString($conn, $_POST["name"]);

    $name1 = str_replace(" ", "_", $name1);
    $name1 = str_replace(chr(39), "~", $name1);
    
    
    $sex1 = strtolower(safeEscapeString($conn, $_POST["sex"]));

    $month1 = safeEscapeString($conn, $_POST["month"]);
    $day1 = safeEscapeString($conn, $_POST["day"]);
    $year1 = safeEscapeString($conn, $_POST["year"]);

    $hour1 = safeEscapeString($conn, $_POST["hour"]);
    $minute1 = safeEscapeString($conn, $_POST["minute"]);
    $amorpm1 = safeEscapeString($conn, $_POST["amorpm"]);

    $timezone1 = safeEscapeString($conn, $_POST["timezone"]);

    $long_deg1 = safeEscapeString($conn, $_POST["long_deg"]);
    $long_min1 = safeEscapeString($conn, $_POST["long_min"]);
    $ew1 = strtoupper(safeEscapeString($conn, $_POST["ew"]));

    $lat_deg1 = safeEscapeString($conn, $_POST["lat_deg"]);
    $lat_min1 = safeEscapeString($conn, $_POST["lat_min"]);
    $ns1 = strtoupper(safeEscapeString($conn, $_POST["ns"]));

    include("validation_class.php");

    //error check
    $my_form = new Validate_fields;

    $my_form->check_4html = true;

    $my_form->add_text_field("Name", $name1, "text", "y", 40);
    $my_form->add_text_field("Sex", $sex1, "text", "y", 1);

    $my_form->add_text_field("Month", $month1, "text", "y", 2);
    $my_form->add_text_field("Day", $day1, "text", "y", 2);
    $my_form->add_text_field("Year", $year1, "text", "y", 4);

    $my_form->add_text_field("Hour", $hour1, "text", "y", 2);
    $my_form->add_text_field("Minute", $minute1, "text", "y", 2);

    $my_form->add_text_field("Time zone", $timezone1, "text", "y", 21);

    $my_form->add_text_field("Longitude degree", $long_deg1, "text", "y", 3);
    $my_form->add_text_field("Longitude minute", $long_min1, "text", "y", 2);
    $my_form->add_text_field("Longitude E/W", $ew1, "text", "y", 2);

    $my_form->add_text_field("Latitude degree", $lat_deg1, "text", "y", 2);
    $my_form->add_text_field("Latitude minute", $lat_min1, "text", "y", 2);
    $my_form->add_text_field("Latitude N/S", $ns1, "text", "y", 2);

//for later use
//$my_form->add_num_field("Number_with_decimals", $_POST['decimal'], "decimal", "n", 3);
//$my_form->add_date_field("US_date", $_POST['date'], "date", "us", "n");

    // additional error checks on user-entered data
    if (is_numeric($timezone1) == False)
    {
      $my_error .= "The time zone entry has to be a number, either positive or negative - it cannot be text.<br>";
    }

    if ($month1 != "" And $day1 != "" And $year1 != "")
    {
      if (!$date = checkdate($month1, $day1, $year1))
      {
        $my_error .= "The date of birth you entered is not valid.<br>";
      }
    }

    if (($year1 < 1200) Or ($year1 >= 2400))
    {
      $my_error .= "Birth year person - please enter a year between 1200 and 2399.<br>";
    }

    if ((intval($hour1) < 1) Or (intval($hour1) > 12))
    {
      $my_error .= "Birth hour must be between 1 and 12.<br>";
    }

  if (is_numeric($hour1) == False)
  {
    $my_error .= "Birth hour must be a numeric value between 1 and 12.<br>";
  }

    if ($minute1 < 0 Or $minute1 > 59)
    {
      $my_error .= "Birth minute must be between 0 and 59.<br>";
    }

  if ($amorpm1 == "")
  {
    $my_error .= "Please tell me AM, PM, or birth time unknown.<br>";
  }

  if (is_numeric($minute1) == False)
  {
    $my_error .= "Birth minute must be a numeric value between 0 and 59.<br>";
  }

    if ($timezone1 < -13 Or $timezone1 > 13)
    {
      $my_error .= "Time zone must be between -13 and 13.<br>";
    }

    if (($long_deg1 < 0) Or ($long_deg1 > 179))
    {
      $my_error .= "Longitude degrees must be between 0 and 179.<br>";
    }

    if (($long_min1 < 0) Or ($long_min1 > 59))
    {
      $my_error .= "Longitude minutes must be between 0 and 59.<br>";
    }

    if (($lat_deg1 < 0) Or ($lat_deg1 > 65))
    {
      $my_error .= "Latitude degrees must be between 0 and 65.<br>";
    }

    if (($lat_min1 < 0) Or ($lat_min1 > 59))
    {
      $my_error .= "Latitude minutes must be between 0 and 59.<br>";
    }

    if (($ew1 == 'W') And ($timezone1 > 2))
    {
      $my_error .= "You have marked West longitude but set an east time zone.<br>";
    }

    if (($ew1 == 'E') And ($timezone1 < -1))
    {
      $my_error .= "You have marked East longitude but set a west time zone.<br>";
    }

    if ($ew1 != 'E' And $ew1 != 'W')
    {
      $my_error .= "Longitude must be either E or W.<br>";
    }

    if ($ns1 != 'N' And $ns1 != 'S')
    {
      $my_error .= "Latitude must be either N or S.<br>";
    }

  if (is_numeric($long_deg1) == False)
  {
    $my_error .= "Longitude degrees must be a numeric value between 0 and 180.<br>";
  }
  
  if (is_numeric($long_min1) == False)
  {
    $my_error .= "Longitude minutes must be a numeric value between 0 and 59.<br>";
  }

  if (is_numeric($lat_deg1) == False)
  {
    $my_error .= "Latitude degrees must be a numeric value between 0 and 66.<br>";
  }
  
  if (is_numeric($lat_min1) == False)
  {
    $my_error .= "Latitude minutes must be a numeric value between 0 and 59.<br>";
  }


    $validation_error = $my_form->validation();

    if ((!$validation_error) || ($my_error != ""))
    {
      echo "<div id='content'>";
      echo "<h1>Your Personal Database</h1>";

      $error = $my_form->create_msg();
      echo "<strong>Error! - The following error(s) occurred:</font><br>";

      if ($error)
      {
        echo $error . $my_error;
      }
      else
      {
        echo $error . "<br>" . $my_error;
      }

      echo "</strong>";
      echo "<p>&nbsp;</p>";
      echo "</div>";

      include ('../footer.html');
      exit();
    }
    else
    {
      // no errors in filling out form, so process form
      //get today's date
      $date_now = date ("Y-m-d");

//insert information into database - Person
      if (trim($amorpm1) == "PM")
      {
        if ($hour1 != 12)
        {
          $hour1 = $hour1 + 12;
        }
      }
      elseif (trim($amorpm1) == "AM")
      {
        if ($hour1 == 12)
        {
          $hour1 = 0;
        }
      }
    

      if ($ew1 == "W")
      {
        $east_west = -1;
      }
      else
      {
        $east_west = 1;
      }

      if ($ns1 == "S")
      {
        $north_south = -1;
      }
      else
      {
        $north_south = 1;
      }

      $username = $_SESSION['username'];
      $email = $_SESSION['email'];

      //check to see if this data is already in the database
      $sql = "SELECT name, sex, entered_by FROM birth_info WHERE name='$name1' And sex='$sex1' And entered_by='$username'";
      $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);
      $num_records = MYSQLI_NUM_rows($result);

      if ($num_records != 0)
      {
        echo "<center>";
        echo "<br><b>You have already entered this person into the database<br><br>";
        echo "<a href='view_records.php'>Click here</a> to view your database entries</a><br><br>";
        echo "</center>";

        include ('../footer.html');
        exit();
      }

      //now it is safe to enter this data into the database
      $sql = "INSERT INTO birth_info (ID,entered_by,name,sex,month,day,year,hour,minute,timezone,long_deg,long_min,ew,lat_deg,lat_min,ns,entry_date) VALUES ('','$username','$name1','$sex1','$month1','$day1','$year1','$hour1','$minute1','$timezone1','$long_deg1','$long_min1','$east_west','$lat_deg1','$lat_min1','$north_south','$date_now')";
      $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);

      if (mysqli_affected_rows($conn) != 1)
      {
        echo "<TABLE align='center' WIDTH='98%' BORDER='0' CELLSPACING='15' CELLPADDING='0'><tr><td><center>";
        echo "<font color='#ff0000'><h2>There is some sort of system problem with your registration.<br>
             Please contact the webmaster.</h2><h3>";
        echo "</h3></font></center></td></tr></table>";
      }
      else
      {
        $person1_id = mysqli_insert_id($conn);

        $emailTo = EMAIL_ADDRESS;

        $emailFrom =  $email;
        $emailSubject = "Database Birth Data Entry";
        $emailText =  "This is the e-mail submitted:\n\n";

        //here is the data to be submitted
        $emailText .= "Username          = " . stripslashes($username) . "\n";
        $emailText .= "Name              = " . stripslashes($name1) . "\n";
        $emailText .= "Sex               = " . stripslashes($sex1) . "\n\n";

        $emailText .= "Month             = " . stripslashes($month1) . "\n";
        $emailText .= "Day               = " . stripslashes($day1) . "\n";
        $emailText .= "Year              = " . stripslashes($year1) . "\n\n";

        $emailText .= "Hour              = " . stripslashes($hour1) . "\n";
        $emailText .= "Minute            = " . stripslashes($minute1) . "\n";
        $emailText .= "Time zone         = " . stripslashes($timezone1) . "\n\n";

        $emailText .= "Longitude deg     = " . stripslashes($long_deg1) . "\n";
        $emailText .= "Longitude min     = " . stripslashes($long_min1) . "\n";
        $emailText .= "E or W            = " . stripslashes($ew1) . "\n\n";

        $emailText .= "Latitude deg      = " . stripslashes($lat_deg1) . "\n";
        $emailText .= "Latitude min      = " . stripslashes($lat_min1) . "\n";
        $emailText .= "N or S            = " . stripslashes($ns1) . "\n\n";

        $emailText .= "Sign up date = $date_now \n\n";

        //send e-mail to user
//        @mail($emailTo, $emailSubject, $emailText, "From: $email");

        //update member_info table in database for this record
        $sql = "UPDATE member_info SET last_transaction='$date_now' WHERE username='$username'";
        $result_1 = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);

        echo "<meta HTTP-EQUIV='REFRESH' content='0; url=../thanksdata.php'>";

        exit();
      }

      mysqli_close($conn);
    }
  }
?>
