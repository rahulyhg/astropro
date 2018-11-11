<?php
  include ('../accesscontrol.php');

  if ($is_logged_in == False)
  {
    echo "You are not yet logged in.";
    exit();
  }

  include ('../header.html');
  include ('../constants.php');           //nedded because of "../footer.html" statements

  // connect to the database
  require_once ('../../../mysqli_connect_online_calcs_db_MYSQLI.php');
  require_once ('../../../my_functions_MYSQLI.php');

  // get username
  $username = $_SESSION['username'];

  // get name to delete
  $ID = safeEscapeString($conn, $_POST["ID"]);


  // get name corresponding to this ID number
  $sql = "SELECT name FROM birth_info WHERE entered_by='$username' And ID='$ID'";
  $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);
  $num_records = @MYSQLI_NUM_rows($result);
  
  if ($num_records == 1)
  {
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $name_to_delete = $row['name'];

    //delete this name from the 'scores' table
    $sql = "DELETE FROM scores WHERE entered_by='$username' And name_f='$name_to_delete'";
    $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);

    //delete this name from the 'scores' table
    $sql = "DELETE FROM scores WHERE entered_by='$username' And name_m='$name_to_delete'";
    $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);

    //delete this name from the 'birth_info' table
    $sql = "DELETE FROM birth_info WHERE entered_by='$username' And name='$name_to_delete' And ID='$ID'";
    $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);

    echo "<center>";
    echo "<font FACE='Arial' size='5' color='#0000FF'><br />";

    if (mysqli_affected_rows($conn) == 1)
    {
      echo "I have deleted ID # = " . $ID . ".<br />";
    }
    else
    {
      echo "There was a problem - I could NOT delete ID = " . $ID . ".<br />";
    }
  }
  else
  {
    //wrong ID number, probably
    echo "<center>";
    echo "<font FACE='Arial' size='5' color='#0000FF'><br />";
    echo "There was a problem - I could NOT delete ID # = " . $ID . ".<br />";
  }


  echo "</font>";
  echo "<br /><br />";

  echo "<a href='view_records.php'>Return to previous page</a>";

  echo "</center>";
  echo "<br /><br />";

  include ('../footer.html');
?>
