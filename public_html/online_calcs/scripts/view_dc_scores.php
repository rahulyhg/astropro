<?php
  include ('../accesscontrol.php');

  if ($is_logged_in == False)
  {
    echo "You are not yet logged in.";
    exit();
  }

  include ('../header.html');
  include ('../constants.php');           //nedded because of "../footer.html" statements

  $username = $_SESSION['username'];

  require_once ('../../../mysqli_connect_online_calcs_db_MYSQLI.php');
  require_once ('../../../my_functions_MYSQLI.php');

  if (isset($_POST['submit_dc']))
  {
    $user_id = safeEscapeString($conn, $_POST["id1"]);

    if (!is_numeric($user_id))
    {
      echo "<center><br><br>You have forgotten to specify a valid ID number. Please try again.</center>";
      include ('footer.html');
      exit();
    }

    $sex = strtolower(safeEscapeString($conn, $_POST["sex"]));
    
    if ($sex != "m" And $sex != "f")
    {
      echo "<center><br><br>Please specify your sex - m for male or f for female.</center>";
      include ('footer.html');
      exit();
    }
        
    //fetch all the ID numbers, names, and sex of all the people in this users database
    $sql = "SELECT ID,entered_by,name,sex FROM birth_info WHERE entered_by='$username'";
    $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);

    $num_records_females = 0;
    $num_records_males = 0;

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
      if ($sex == "m")
      {
        if ($row[sex] == "f")
        {
          $id_f[$num_records_females] = $row[ID];
          $name_f[$num_records_females] = $row[name];

          $num_records_females++;
        }
        elseif ($row[sex] == "m")
        {
          if ($row[ID] == $user_id And $row[entered_by] == $username)
          {
            $name_m[0] = $row[name];
            $num_records_males = 1;
          }
        }
      }
      elseif ($sex == "f")
      {
        if ($row[sex] == "m")
        {
          $id_m[$num_records_males] = $row[ID];
          $name_m[$num_records_males] = $row[name];

          $num_records_males++;
        }
        elseif ($row[sex] == "f")
        {
          if ($row[ID] == $user_id And $row[entered_by] == $username)
          {
            $name_f[0] = $row[name];
            $num_records_females = 1;
          }
        }
      }
    }

    if (($sex == "m" And $num_records_females > 0 And $num_records_males == 1) Or ($sex == "f" And $num_records_males > 0 And $num_records_females == 1))
    {
      //check for database entries for all these records - if no record, then we have to calculate to insert the data
      for ($i = 0; $i <= $num_records_females - 1; $i++)
      {
        for ($j = 0; $j <= $num_records_males - 1; $j++)
        {
          if ($sex == "f")
          {
            $sql = "SELECT * FROM scores WHERE id_f='$user_id' AND id_m='$id_m[$j]'";
            $id[1] = $user_id;
            $id[2] = $id_m[$j];
          }
          elseif ($sex == "m")
          {
            $sql = "SELECT * FROM scores WHERE id_f='$id_f[$i]' AND id_m='$user_id'";
            $id[1] = $id_f[$i];
            $id[2] = $user_id;
          }
          
          $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);
          $records_found = MYSQLI_NUM_rows($result);

          if ($records_found != 1)
          {
            //calculate dual cosmodynes here
            require_once ('calc_planet_pos.php');
            require_once ('dual_c_calcs.php');
            require_once ('dual_c_mrs.php');

            //just calculate planet positions
            CalculatePlanets($id, $longitude1, $declination1, $house_pos1, $longitude2, $declination2, $house_pos2, $hob, $mob);

            $num_MRs = GetMutualReceptions($longitude1, $longitude2);
            $dynes = GetCosmodynes($longitude1, $declination1, $house_pos1, $longitude2, $declination2, $house_pos2, $hob, $mob);

            //enter this data into the database
            $tot_harmony = $dynes[1] + ($num_MRs * 5);

            $sql = "INSERT INTO scores (ID,entered_by,id_f,name_f,id_m,name_m,power,harmony,mrs) VALUES ('','$username','$id[1]','$name_f[$i]','$id[2]','$name_m[$j]','$dynes[0]','$tot_harmony','$num_MRs')";
            $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);
          }
        }
      }
    }
  }


  //check current users data
  $sql = "SELECT * FROM scores WHERE entered_by='$username' ORDER BY harmony DESC";
  $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);
  $num_records = MYSQLI_NUM_rows($result);

  echo "<div id='content'>";
  echo "<h1><strong>Welcome, " . $username . ", to your private database of Dual Cosmodyne Scores</strong></h1>";

  echo "<br>";

  // table header
  echo '<table style="font-family: verdana; font-size: 8pt; color: 000066;" cellspacing="1" cellpadding="5">
        <tr bgcolor="bebeee">
        <td align="left"><b>Female ID #</b></td>
        <td align="left"><b>Female Name</b></td>
        <td align="left"><b>Male ID #</b></td>
        <td align="left"><b>Male Name</b></td>
        <td align="left"><b>Power</b></td>
        <td align="left"><b>Harmony (+) or Discord (-)</b></td>
        <td align="left"><b>Mutual Receptions</b></td>
        </tr>';

  // fetch and print all the records
  $bg = '#eeeeee';                    // Set the background color.
  while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
  {
    $bg = ($bg=='#eeeeee' ? '#ffffff' : '#eeeeee');           // Switch the background color.
    echo '<tr bgcolor="' . $bg . '">';
    echo '<td align="center">' . $row['id_f'] . '</a></td>';
    echo '<td align="center">' . $row['name_f'] . '</td>';
    echo '<td align="center">' . $row['id_m'] . '</a></td>';
    echo '<td align="center">' . $row['name_m'] . '</td>';
    echo '<td align="center">' . sprintf("%.2f", $row['power']) . '</td>';

    if (sprintf("%.2f", $row['harmony']) > 10)
  {
    echo '<td align="center"><font size="+1" color="#009000">' . sprintf("%.2f", $row['harmony']) . '</font></td>';
  }
  elseif (sprintf("%.2f", $row['harmony']) < 0)
  {
    echo '<td align="center"><font size="+1" color="#ff0000">' . sprintf("%.2f", $row['harmony']) . '</font></td>';
  }
  else
  {
    echo '<td align="center"><font size="+1" color="#000000">' . sprintf("%.2f", $row['harmony']) . '</font></td>';
  }

    echo '<td align="center">' . $row['mrs'] . '</td>';
    echo '</tr>';
  }

  echo '</table>';

  echo "<br><b>You have " . $num_records . " combinations of females and males.</b>";

  echo "</div>";


  // update count
  $sql = "SELECT view_all_dc_scores FROM reports";
  $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);
  $row = mysqli_fetch_array($result);
  $count = $row[view_all_dc_scores] + 1;

  $sql = "UPDATE reports SET view_all_dc_scores = '$count'";
  $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);


  include ('../footer.html');
?>
