<?php
  include ('../accesscontrol.php');

  if ($is_logged_in == False)
  {
    echo "You are not yet logged in.";
    exit();
  }

    include ('../header.html');
    include '../nav2.php';
    include ('../constants.php');           //nedded because of "../footer.html" statements


  // connect to the database and point to the proper database
  require_once ('../../../mysqli_connect_online_calcs_db_MYSQLI.php');

?>
<script type="text/javascript" src="jquery-1.2.1.pack.js"></script>

<script type="text/javascript">
function lookup(inputString)
{
  $('#my_data_list').hide();

  if(inputString.length == 0)
  {
  // Hide the suggestion box.
  $('#suggestions').hide();
  } else {
  $.post("auto_complete_name_search.php", {queryString: ""+inputString+""}, function(data)
  {
    if(data.length > 0)
    {
      $('#suggestions').show();
      $('#autoSuggestionsList').html(data);
    }
  });
  }
}

function fill(thisValue)
{
  $('#inputString').val(thisValue);
  setTimeout("$('#suggestions').hide();", 200);

  var $name_ID = document.astro_input_form.name.value;    //get name which includes the ID number separated by a ':'

  var temp = new Array();
  temp = $name_ID.split(':');     //split the name from the ID

  if (temp[1] == "undefined") { temp[1] = ""; }   //dont let the ID be called 'undefined'

  // remove anything like ' >' from the name
  var $idx = temp[0].indexOf(' >');

  if ($idx > -1) { temp[0] = temp[0].substring(0, $idx); }

  // remove anything like ' (' from the name
  var $idx = temp[0].indexOf(' (');

  if ($idx > -1) { temp[0] = temp[0].substring(0, $idx); }


  document.astro_input_form.name.value = temp[0];   //insert the name into the textbox


  var $okay = "okay_to_process";
  var $name = document.astro_input_form.name.value;
  var $name = temp[0];
  var $ID = temp[1];
}
</script>

<body>
    <div class="container round_block">
        <div class="row">
            <div class="col-md-12">
<?php


  $months = array (0 => 'Choose month', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

  $username = $_SESSION['username'];

  // get user options
  $sql = "SELECT num_recs_to_display, my_sort_by FROM member_info WHERE username='$username'";
  $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);
  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
  $num_records = MYSQLI_NUM_rows($result);

  $current_num_recs_to_display = $row['num_recs_to_display'];
  $current_sort_by = $row['my_sort_by'];

  // number of records to show per page - default settings are below
  $display = 15;
  $sort_by ="ID DESC";

  if ($num_records == 1)
  {
    if($row['num_recs_to_display'] > 0) { $display = $row['num_recs_to_display']; }

    $sort_by = $row['my_sort_by'];
  }

  // count the number of records that this user has entered
  //do NOT use Count(*) below as it gives a 1 when there are no matches - I do not know why
  $sql = "SELECT ID FROM birth_info WHERE entered_by='$username'";
  $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);
  $num_records = MYSQLI_NUM_rows($result);

  if ($num_records == 0)
  {
    echo "<br><br><center>I don't think you have entered any records. <a href='data_entry_1.php'>Please do so</a>.</center>";
    exit();
  }
  else
  {
    // determine how many pages there are
    if (isset($_GET['np']))
    {
    $num_pages = $_GET['np'];
    if (!is_numeric($num_pages))
    {
      // np is not numeric
      echo "<br />There is an error in the number of pages specified. I am exiting.<br />";
      exit();
    }
    }
    else
    {
    // calculate the number of pages
    if ($num_records > $display)
    {
      $num_pages = ceil ($num_records/$display);
    }
    else
    {
      $num_pages = 1;
    }
    }

    // determine where in the database to start returning results
    if (isset($_GET['s']))
    {
    $start = $_GET['s'];
    if (!is_numeric($start))
    {
      // np is not numeric
      echo "<br />There is an error in the specified start page. I am exiting.<br />";
      exit();
    }
    }
    else
    {
    $start = 0;
    }

    // make the query
    $sql = "SELECT * FROM birth_info WHERE entered_by='$username' ORDER BY $sort_by LIMIT $start, $display";
    $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);



// Grabs the URI and breaks it apart in case we have querystring stuff
$request_uri = explode('?', $_SERVER['REQUEST_URI'], 2);

// Route it up!
switch ($request_uri[0]) {
	// Home page
	case '/':
		require 'logout.php';
		break;
}

    ?>


            <?php echo "<h1><strong>Welcome, " . $username . ", to your private database</strong></h1>"; ?>

      <?php

    echo "<br />";

    // table header
    echo '<table class="table">
 <thead>
          <tr bgcolor="bebeee">
          <td align="center"><b>Name</b></td>
          <td align="center"><b>ID#</b></td>
          <td align="center"><b>Sex</b></td>
          <td align="center"><b>Day</b></td>
          <td align="center"><b>Mon</b></td>
          <td align="center"><b>Year</b></td>
          <td align="center"><b>Hr</b></td>
          <td align="center"><b>Min</b></td>
          <td align="center"><b>TZ</b></td>
          <td align="center"><b>Long<br />Deg</b></td>
          <td align="center"><b>E/W</b></td>
          <td align="center"><b>Long<br />Min</b></td>
          <td align="center"><b>Lat<br />Deg</b></td>
          <td align="center"><b>N/S</b></td>
          <td align="center"><b>Lat<br />Min</b></td>
          <td align="center"><b>Data<br />entered on</b></td>
           </thead>
        </tr>
       ';

    // fetch and print all the records
    $bg = '#eeeeee';                    // Set the background color.
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
    $bg = ($bg=='#eeeeee' ? '#ffffff' : '#eeeeee');           // Switch the background color.
      if ($row['ns'] == 1)
      {
        $n_or_s = "n";
      }
      else
      {
        $n_or_s = "s";
      }

      if ($row['ew'] == 1)
      {
        $e_or_w = "e";
      }
      else
      {
        $e_or_w = "w";
      }


	    $monthNum  = $row['month'];
	    $monthName = date('F', mktime(0, 0, 0, $monthNum, 10));

        echo '
       <thead>
        <tr bgcolor="' . $bg . '">
        <td align="center"><strong><a href="entry_update.php?ID=' . $row['ID'] . '">' . $row['name'] . '</a></strong></td>
        <td align="center">' . $row['ID'] . '</td>
        <td align="center">' . $row['sex'] . '</td>
        <td align="center">' . $row['day'] . '</td>
        <td align="center">' . $monthName . '</td>        
        <td align="center">' . $row['year'] . '</td>
        <td align="center">' . $row['hour'] . '</td>
        <td align="center">' . $row['minute'] . '</td>
        <td align="center">' . $row['timezone'] . '</td>
        <td align="center">' . $row['long_deg'] . '</td>
        <td align="center">' . $e_or_w . '</td>
        <td align="center">' . $row['long_min'] . '</td>
        <td align="center">' . $row['lat_deg'] . '</td>
        <td align="center">' . $n_or_s . '</td>
        <td align="center">' . $row['lat_min'] . '</td>
        <td align="center">' . $row['entry_date'] . '</td>
         </thead>
        </tr>
     ';
    }

    echo '</table>';


    echo "<br /><b>Click on a person's name to change their data. You have entered " . $num_records . " birth data records.</b><br />";

    // make the links to the other pages, if necessary
    if ($num_pages > 1)
    {
      echo '<center><p>';

      // determine what page the script is on
      $current_page = ($start/$display) + 1;

    // if it's not the first page, then make a Previous button
    if ($current_page != 1) {
      echo '<a href="' . $_SERVER['PHP_SELF'] . '?s=' . ($start - $display) . '&np=' . $num_pages .'">Previous</a> ';
    }

    // make all the numbered pages
    for ($i = 1; $i <= $num_pages; $i++)
    {
      if ($i != $current_page)
      {
        echo '<a href="' . $_SERVER['PHP_SELF'] . '?s=' . (($display * ($i - 1))) . '&np=' . $num_pages .'">' . $i . '</a> ';
      }
      else
      {
        echo $i . ' ';
      }
    }

    // if it's not the last page, then make a Next button
    if ($current_page != $num_pages)
    {
      echo '<a href="' . $_SERVER['PHP_SELF'] . '?s=' . ($start + $display) . '&np=' . $num_pages .'">Next</a>';
    }

    echo '</p></center>';
    }

    echo "<br /><a href='data_entry_1.php'>Click here to add another person's birth data to your database</a><br />";

?>

    <br><br><br>

    <form action="entry_delete.php" method="post" style="width: 750px;">
      <table cellspacing="0" cellpadding="1" style="font-size:12px;">
        <TR>
          <td align="center">
	          <?php

	          $sql = "SELECT * FROM birth_info WHERE entered_by='$username' ORDER BY $sort_by LIMIT $start, $display";
	          $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);

	          echo "<select name='ID' class=\"form-control\">";

	          while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {

		          unset($id, $name);
		          $id = $row['ID'];
		          $name = $row['name'];
		          echo '<option value="'.$id.'">'.$name.'</option>';

	          }

	          echo "</select>";
	          ?>
          </TD>

          <td align="center">
            &nbsp;&nbsp;&nbsp;<INPUT type="submit" name="submit3" value="Delete this person from your database" class="btn btn-primary">
          </TD>
        </TR>
      </table>
    </form>





    <p>&nbsp;</p><p>&nbsp;</p>
<a href="natal_chart_report.php">Natal Chart Reports</a><br>
<a href="vocational_analisys.php">Vocational Analysis Reports</a><br>
<a href="event_chart.php">Event Chart</a>
        </div>
    </div>
</div>


    <?php
  }
?>
<?php include ('../footer.html'); ?>
</body>
