<?php
  include ('../accesscontrol.php');

  if ($is_logged_in == False)
  {
    echo "You are not yet logged in.";
    exit();
  }

  include ('../header.html');

  echo "</center>";

  // connect to the database and point to the proper database
  require_once ('../../../mysqli_connect_online_calcs_db_MYSQLI.php');
  require_once ('../../../my_functions_MYSQLI.php');

  // check if the form has been submitted
  if (isset($_POST['submitted']))
  {
    // get all variables from form - Person
    $num_recs_to_display = safeEscapeString($conn, $_POST["num_recs_to_display"]);
    $my_sort_by = strtolower(safeEscapeString($conn, $_POST["my_sort_by"]));

    if ($num_recs_to_display < 1 Or $num_recs_to_display > 500)
    {
      $num_recs_to_display = 25;
    }

    $username = $_SESSION['username'];

    //enter this data into the database
    $sql = "UPDATE member_info SET num_recs_to_display='$num_recs_to_display', my_sort_by='$my_sort_by' WHERE username='$username'";
    $result_1 = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);

    echo "<br><br><center>I have updated your options. Thank you.<br><br>I will return you to the view_records page in 3 seconds.</center>";
    echo "<meta HTTP-EQUIV='REFRESH' content='3; url=./view_records.php'>";
  }
?>
