<?php
  session_start();

  require_once ('../../../mysqli_connect_online_calcs_db_MYSQLI.php');
  //require_once ('../../../my_functions_MYSQLI.php');

  // Is there a posted query string?
  if (isset($_POST['queryString']))
  {
    $queryString = trim(safeEscapeString($conn, $_POST["queryString"]));

    $username = $_SESSION['username'];

    // Is the querystring length greater than 0?
    if (strlen($queryString) > 0)
    {
      //mysqli_query($conn, "set names utf8");    //this MUST be here in order to see accented characters in the drop-down auto-complete area

      $sql = "SELECT ID, name FROM birth_info WHERE entered_by='$username' AND name LIKE '$queryString%' LIMIT 15";
      $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);

      if ($result)
      {
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        echo '<li onClick="fill(\'' . addslashes($row[name]) . ":" . addslashes($row[ID]) . '\');">' . $row[name] . ":" . $row[ID] . '</li>';
      }
    }
    else
    {
      echo 'ERROR: There was a problem with the query.';
    }
  }
  }
  else
  {
    echo 'There is no direct access to this script';
  }


Function safeEscapeString($conn, $string)
{
// replace HTML tags '<>' with '[]'
  $temp1 = str_replace("<", "[", $string);
  $temp2 = str_replace(">", "]", $temp1);

// but keep <br> or <br />
// turn <br> into <br /> so later it will be turned into ""
// using just <br> will add extra blank lines
  $temp1 = str_replace("[br]", "<br />", $temp2);
  $temp2 = str_replace("[br /]", "<br />", $temp1);

  if (get_magic_quotes_gpc())
  {
    return $temp2;
  }
  else
  {
    return mysqli_real_escape_string($conn, $temp2);
  }
}

?>
