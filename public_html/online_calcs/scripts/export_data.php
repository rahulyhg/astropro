<?php
  include ('../accesscontrol.php');

  if ($is_logged_in == False)
  {
    echo "You are not yet logged in.";
    exit();
  }

  include ('../header.html');
  include ('../constants.php');           //nedded because of "../footer.html" statements

  // connect to the database and point to the proper database
  require_once ('../../../mysqli_connect_online_calcs_db_MYSQLI.php');

  echo "<br><center><b>Here is the data in your personal database: (import this data into Astro123)</b></center><br>";
  
  $months = array (0 => 'Choose month', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
  
  $username = $_SESSION['username'];

  $sql = "SELECT * FROM birth_info WHERE entered_by='$username' ORDER BY ID ASC";
  $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);

  echo "<br>";

  while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
  {
    if ($row['hour'] < 12)
    {
      $am_pm = "A";
      $hr = $row['hour'];
    }
    else
    {
      $am_pm = "P";
      $hr = $row['hour'] - 12;
    }
    
    $t1 = $row['name'] . "," . $row['day'] . " " . $months[$row['month']] . " " . $row['year'] . "," . $hr . "." . sprintf("%02d", $row['minute']) . ",";

    if ($row['ew'] == 1)
    {
      $t2 = $am_pm . "," . (-1 * $row['timezone']) . ",-" . $row['long_deg'] . "." . sprintf("%02d", $row['long_min']);
    }
    else
    {
      $t2 = $am_pm . "," . (-1 * $row['timezone']) . "," . $row['long_deg'] . "." . sprintf("%02d", $row['long_min']);
    }

    if ($row['ns'] == 1)
    {
      $t3 = "," . $row['lat_deg'] . "." . sprintf("%02d", $row['lat_min']) . ",";
    }
    else
    {
      $t3 = ",-" . $row['lat_deg'] . "." . sprintf("%02d", $row['lat_min']) . ",";
    }
    
    echo $t1 . $t2 . $t3 . "<br>";
  }

  echo "<br>";
  
  include ('../footer.html');
?>
