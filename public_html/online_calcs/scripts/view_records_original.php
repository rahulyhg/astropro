<?php
  include ('../accesscontrol.php');

  if ($is_logged_in == False)
  {
    echo "You are not yet logged in.";
    exit();
  }

    include ('../header.html');
    include '../nav.html';
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




    ?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php echo "<h1><strong>Welcome, " . $username . ", to your private database</strong></h1>"; ?>
    <div class="tabber" id="tab1">
      <div class="tabbertab" id="tab1" title="Search for a name">
        <br>
        <table cellspacing="0" cellpadding="1" style="font-size:12px;">
          <tr>
            <td valign="middle">Search for a name (the correct ID number will appear after the colon after the name):</td>

            <td>
              <input type="text" size="20" name="search_name" id="inputString" onkeyup="lookup(this.value);" onblur="fill();" />

              <div class="suggestionsBox" id="suggestions" style="display: none;">
                <img src="upArrow.png" style="position: relative; top: -12px; left: 30px;" alt="upArrow" />
            <div class="suggestionList" id="autoSuggestionsList">
              &nbsp;
            </div>
              </div>
            </td>
          </tr>

          <tr>
            <td valign="middle" align='right'>&nbsp;</td>
            <td valign="middle" align='right'>&nbsp;</td>
          </tr>
        </table>
      </div>

      <div class="tabbertab" id="tab1" title="User Options">
        <br>
        <form name="astro_input_form" action="user_options.php" method="post" style="width: 750px;">
          <table cellspacing="0" cellpadding="1" style="font-size:12px;">
            <tr>
              <td valign="middle">Number of records to display on this page:</td>

              <td>
                <input maxlength="3" size="4" name="num_recs_to_display" value="<?php echo $current_num_recs_to_display; ?>">
              </td>
            </tr>

            <tr>
              <td valign="middle" align='right'>Sort by (usually in ascending order):</td>

              <td>
                <select name="my_sort_by" size="1">
                  <?php
                  echo "<option value='entry_date DESC' ";
                  if ($current_sort_by == "entry_date DESC"){ echo " selected"; }
                  echo "> Data entered on (descending order) </option>";

                  echo "<option value='ID' ";
                  if ($current_sort_by == "id"){ echo " selected"; }
                  echo "> ID </option>";

                  echo "<option value='ID desc' ";
                  if ($current_sort_by == "id desc"){ echo " selected"; }
                  echo "> ID (descending order)</option>";

                  echo "<option value='month' ";
                  if ($current_sort_by == "month"){ echo " selected"; }
                  echo "> Month </option>";

                  echo "<option value='name' ";
                  if ($current_sort_by == "name"){ echo " selected"; }
                  echo "> Name </option>";

                  echo "<option value='sex' ";
                  if ($current_sort_by == "sex"){ echo " selected"; }
                  echo "> Sex </option>";

                  echo "<option value='timezone' ";
                  if ($current_sort_by == "timezone"){ echo " selected"; }
                  echo "> Time zone </option>";

                  echo "<option value='year' ";
                  if ($current_sort_by == "year"){ echo " selected"; }
                  echo "> Year </option>";

                  ?>
                </select>
              </td>
            </tr>

            <tr>
              <td valign="middle" align='right'>&nbsp;</td>
              <td valign="middle" align='right'>&nbsp;</td>
            </tr>

            <tr>
              <td colspan='2'>
                <input type="hidden" name="submitted" value="submitted">
                &nbsp;&nbsp;&nbsp;<INPUT type="submit" name="submit11" value="Click here to save your options" style="background-color:#ddddee;color:#0000ff;font-size:12px;font-weight:normal">
              </td>
            </tr>
          </table>
        </form>
      </div>
    </div>


    <?php

    echo "<br />";

    // table header
    echo '<table style="font-family: verdana; font-size: 8pt; color: 000066;" cellspacing="1" cellpadding="5" width="750">
          <tr bgcolor="bebeee">
          <td align="left"><b>ID #</b></td>
          <td align="left"><b>Name</b></td>
          <td align="left"><b>Sex</b></td>
          <td align="left"><b>Mon</b></td>
          <td align="left"><b>Day</b></td>
          <td align="left"><b>Year</b></td>
          <td align="left"><b>Hr</b></td>
          <td align="left"><b>Min</b></td>
          <td align="left"><b>TZ</b></td>
          <td align="left"><b>Long<br />Deg</b></td>
          <td align="left"><b>E/W</b></td>
          <td align="left"><b>Long<br />Min</b></td>
          <td align="left"><b>Lat<br />Deg</b></td>
          <td align="left"><b>N/S</b></td>
          <td align="left"><b>Lat<br />Min</b></td>
          <td align="left"><b>Data<br />entered on</b></td>
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

      echo '<tr bgcolor="' . $bg . '">
        <td align="center"><a href="entry_update.php?ID=' . $row['ID'] . '">' . $row['ID'] . '</a></td>
        <td align="left">' . $row['name'] . '</td>
        <td align="center">' . $row['sex'] . '</td>
        <td align="center">' . $row['month'] . '</td>
        <td align="center">' . $row['day'] . '</td>
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
        </tr>
     ';
    }

    echo '</table>';

    echo "<br /><b>Click on a person's ID number to change their data. You have entered " . $num_records . " birth data records.</b><br />";

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
            ID #: <INPUT maxlength="7" size="10" name="ID" value="">
          </TD>

          <td align="center">
            &nbsp;&nbsp;&nbsp;<INPUT type="submit" name="submit3" value="Delete this person from your database" style="background-color:#ff0000;color:#ffffff;font-size:16px;font-weight:normal">
          </TD>
        </TR>
      </table>
    </form>

<?php

?>

    <p>&nbsp;</p><p>&nbsp;</p>


<script type="text/javascript" src="tabber.js"></script>
<link rel="stylesheet" href="example.css" TYPE="text/css" MEDIA="screen">

<script type="text/javascript">
  /* Optional: Temporarily hide the "tabber" class so it does not "flash" on the page as plain HTML. After tabber runs, the class is changed to "tabberlive" and it will appear. */
  document.write('<style type="text/css">.tabber{display:none;}<\/style>');
</script>

<style type="text/css">
h9
{
  font-family: Arial, Helvetica, sans-serif;
  color: #B90000;
  font-size: 12px;
}
#content a:link
{
  font-family: Arial, Helvetica, sans-serif;
  color: #0000ff;
  font-size: 12px;
  font-weight: bold;
}
#content a:visited
{
  font-family: Arial, Helvetica, sans-serif;
  color: #B90000;
  font-size: 12px;
  font-weight: bold;
}
#content a:hover
{
  font-family: Arial, Helvetica, sans-serif;
  color: #ff00ff;
  font-size: 12px;
  font-weight: bold;
}
</style>


<div class="tabber" id="tab1">
  <div class="tabbertab tabbertabdefault" id="tab1" title="Natal Chart Report">
    <br>
    <h9>Natal Chart Report - </h9><strong>Enter an ID number in the box below to generate a natal chart report for that person.</strong><br><br>

    <form action="natal.php" method="post" style="width: 750px;">
      <table cellspacing="0" cellpadding="1" style="font-size:12px;">
        <TR>
          <td align="left">
            ID #:
            <INPUT size="6" name="id1" value="">
          </TD>

          <td>
            <input type="hidden" name="submitted" value="submitted">
            &nbsp;&nbsp;&nbsp;<INPUT type="submit" name="submit1" value="Click here to produce a natal chart and its interpretation report" style="background-color:#ddddee;color:#0000ff;font-size:12px;font-weight:normal">
          </TD>
        </TR>
      </table>
    </form>
  </div>


  <div class="tabbertab" id="tab1" title="Natal Cosmodynes">
    <br>
    <h9>Natal Cosmodynes - </h9><strong>Enter an ID number in the box below to generate that person's natal cosmodynes.</strong><br><br>

    <form action="natal_cosmodynes.php" method="post" style="width: 750px;">
      <table cellspacing="0" cellpadding="1" style="font-size:12px;">
        <TR>
          <td align="left">
            ID #:
            <INPUT size="6" name="id1" value="">
          </TD>

          <td>
            <input type="hidden" name="submitted" value="submitted">
            &nbsp;&nbsp;&nbsp;<INPUT type="submit" name="submit1" value="Click here to generate the natal cosmodynes" style="background-color:#ddddee;color:#0000ff;font-size:12px;font-weight:normal">
          </TD>
        </TR>
      </table>
    </form>
  </div>


  <div class="tabbertab" id="tab1" title="Vocational Analysis">
    <br>
    <h9>Vocational Analysis - </h9><strong>Enter an ID number in the box below to generate vocational analysis and natal chart report for that person.</strong><br><br>

    <form action="vocation.php" method="post" style="width: 750px;">
      <table cellspacing="0" cellpadding="1" style="font-size:12px;">
        <TR>
          <td align="left">
            ID #:
            <INPUT size="6" name="id1" value="">
          </TD>

          <td>
            <input type="hidden" name="submitted" value="submitted">
            &nbsp;&nbsp;&nbsp;<INPUT type="submit" name="submit1" value="Click here to produce vocational analysis and natal chart report" style="background-color:#ddddee;color:#0000ff;font-size:12px;font-weight:normal">
          </TD>
        </TR>
      </table>
    </form>
  </div>


  <div class="tabbertab" id="tab1" title="Contests">
    <br>
    <h9>Contest Charts - </h9><strong>Enter an ID number in the box below to generate the contest chart.</strong><br><br>

    <form action="contests.php" method="post" style="width: 750px;">
      <table cellspacing="0" cellpadding="1" style="font-size:12px;">
        <tr>
          <td align="left">
            ID #: &nbsp;<input size="6" name="id1" value="" style="text-align:center;">
          </td>

          <td align="left">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;House of Ruler 1: &nbsp;<input size="2" name="r1h" value="1" style="text-align:center;">
          </td>

          <td align="left">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;House of Ruler 2: &nbsp;<input size="2" name="r2h" value="7" style="text-align:center;">
          </td>

          <td>
            <input type="hidden" name="submitted" value="submitted">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="submit1" value="Click here to generate a contest chart" style="background-color:#ddddee;color:#0000ff;font-size:12px;font-weight:normal">
          </td>
        </tr>
      </table>
    </form>
  </div>


  <div class="tabbertab" id="tab1" title="Natal Midpoints">
    <br>
    <h9>Natal Midpoints - </h9><strong>Enter an ID number in the box below to generate that person's natal midpoints.</strong><br><br>

    <form action="natal_midpoints.php" method="post" style="width: 750px;">
      <table cellspacing="0" cellpadding="1" style="font-size:12px;">
        <TR>
          <td align="left">
            ID #:
            <INPUT size="6" name="id1" value="">
          </TD>

          <td>
            <input type="hidden" name="submitted" value="submitted">
            &nbsp;&nbsp;&nbsp;<INPUT type="submit" name="submit1" value="Click here to generate the natal midpoints" style="background-color:#ddddee;color:#0000ff;font-size:12px;font-weight:normal">
          </TD>
        </TR>
      </table>
    </form>
  </div>


  <div class="tabbertab" id="tab1" title="Harmonics">
    <br>
    <h9>Harmonic Charts - </h9><strong>Enter an ID number in the box below to generate the harmonic chart.</strong><br><br>

    <form action="harmonics.php" method="post" style="width: 750px;">
      <table cellspacing="0" cellpadding="1" style="font-size:12px;">
        <tr>
          <td align="left">
            ID #: &nbsp;<input size="6" name="id1" value="" style="text-align:center;">

            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Harmonic number (2 - 31): &nbsp;<input size="3" maxlength="2" name="harmonic_val" value="9" style="text-align:center;">

            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="harmonic_age" value="1" checked>Use Age Harmonic rather than numerical harmonic
          </td>
        </tr>
        
        <tr><td>&nbsp;</td></tr>
        
        <tr>
          <td align="left">
            <b>&nbsp;&nbsp;&nbsp;Harmonic Age Date:</b>

            <?php
              $start_month = strftime("%m", time());
              $start_day = strftime("%d", time());
              $start_year = strftime("%Y", time());

              echo '&nbsp;&nbsp;&nbsp;<select name="start_month">';
              foreach ($months as $key => $value)
              {
                echo "<option value=\"$key\"";
                if ($key == $start_month)
                {
                  echo ' selected="selected"';
                }
                echo ">$value</option>\n";
              }
              echo '</select>';
            ?>

            &nbsp;<input size="2" maxlength="2" name="start_day" value="<?php echo $start_day; ?>">
            <b>,</b>&nbsp;&nbsp;<input size="4" maxlength="4" name="start_year" value="<?php echo $start_year; ?>">
          </td>
        </tr>
        
        <tr><td>&nbsp;</td></tr>

        <tr>  
          <td>
            <input type="hidden" name="submitted" value="submitted">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="submit1" value="Click here to generate a harmonic chart" style="background-color:#ddddee;color:#0000ff;font-size:12px;font-weight:normal">
          </td>
        </tr>
      </table>
    </form>
</div>


  <div class="tabbertab" id="tab1" title="Event">
    <br>
    <h9>Event Charts - </h9><strong>Enter an ID number in the box below to generate the event chart.</strong><br><br>

    <form action="event.php" method="post" style="width: 750px;">
      <table cellspacing="0" cellpadding="1" style="font-size:12px;">
        <tr>
          <td align="left">
            ID #: &nbsp;<input size="6" name="id1" value="" style="text-align:center;">
          </td>

          <td>
            <input type="hidden" name="submitted" value="submitted">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="submit1" value="Click here to generate an event chart" style="background-color:#ddddee;color:#0000ff;font-size:12px;font-weight:normal">
          </td>
        </tr>
      </table>
    </form>
</div>
</div>


<br><br>


<div class="tabber" id="tab1">
  <div class="tabbertab tabbertabdefault" id="tab1" title="Synastry Chart Report">
    <br>
    <h9>Synastry Report - </h9><strong>Enter two ID numbers in the boxes below to see your synastry with another person (including a report).</strong><br><br>

    <form action="synastry.php" method="post" style="width: 750px;">
      <table cellspacing="0" cellpadding="1" style="font-size:12px;">
        <TR>
          <td align="left">
            ID #1:
            <INPUT size="6" name="id1" value="">
          </TD>

          <td align="left">
            &nbsp;&nbsp;&nbsp;ID #2:
            <INPUT size="6" name="id2" value="">
          </TD>

          <td colspan='2'>
            <input type="hidden" name="submitted" value="submitted">
            &nbsp;&nbsp;&nbsp;<INPUT type="submit" name="submit1" value="Click to see your synastry (compatibility) with another person - plus a report" style="background-color:#ddddee;color:#0000ff;font-size:12px;font-weight:normal">
          </TD>
        </TR>
      </table>
    </form>
  </div>


  <div class="tabbertab" id="tab1" title="Dual Cosmodynes">
    <br>
    <h9>Dual Cosmodynes (with synastry report) - </h9><strong>Enter two ID numbers in the boxes below to see your astrological compatibility.</strong><br><br>

    <form action="dual_cosmodynes.php" method="post" style="width: 750px;">
      <table cellspacing="0" cellpadding="1" style="font-size:12px;">
        <TR>
          <td align="left">
            ID #1:
            <INPUT size="6" name="id1" value="">
          </TD>

          <td align="left">
            &nbsp;&nbsp;&nbsp;ID #2:
            <INPUT size="6" name="id2" value="">
          </TD>

          <td colspan='2'>
            <input type="hidden" name="submitted" value="submitted">
            &nbsp;&nbsp;&nbsp;<INPUT type="submit" name="submit1" value="Click here to see your dual cosmodynes (check your compatibility)" style="background-color:#ddddee;color:#0000ff;font-size:12px;font-weight:normal">
          </TD>
        </TR>
      </table>
    </form>
  </div>


  <div class="tabbertab" id="tab1" title="View all your dual cosmodyne scores">
    <br>
    <h9>Dual Cosmodyne Scores - </h9><strong>Enter an ID number in the box below to generate all the dual cosmodyne scores for all members of the opposite sex.</strong><br><br>

    <form action="view_dc_scores.php" method="post" style="width: 750px;">
      <table cellspacing="0" cellpadding="1" style="font-size:12px;">
        <tr>
          <td align="left">
            ID #: <INPUT size="6" name="id1" value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sex of that ID #: <input type='text' size='2' maxlength='1' id='sex' name='sex'> (m for male and f for female)&nbsp;&nbsp;&nbsp;
            <input type="hidden" name="submit_dc" value="submit_dc">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <INPUT type="submit" name="submit1" value="Click here to calculate &amp; see your dual cosmodyne scores" style="background-color:#ddddee;color:#0000ff;font-size:12px;font-weight:normal">
            <br><br><br>
            <a href="view_dc_scores.php">Click here</a> if you just want to see current scores in your database (without calculating new scores)
          </td>
        </tr>
      </table>
    </form>
  </div>


  <div class="tabbertab" id="tab1" title="Composite Chart">
    <br>
    <h9>Composite Chart - </h9><strong>Enter two ID numbers in the boxes below to generate a composite chart for a couple.</strong><br><br>

    <form action="composite.php" method="post" style="width: 750px;">
      <table cellspacing="0" cellpadding="1" style="font-size:12px;">
        <TR>
          <td align="left">
            ID #1:
            <INPUT size="6" name="id1" value="">
          </TD>

          <td align="left">
            &nbsp;&nbsp;&nbsp;ID #2:
            <INPUT size="6" name="id2" value="">
          </TD>

          <td colspan='2'>
            <input type="hidden" name="submitted" value="submitted">
            &nbsp;&nbsp;&nbsp;<INPUT type="submit" name="submit1" value="Click here to produce a composite chart for your relationship" style="background-color:#ddddee;color:#0000ff;font-size:12px;font-weight:normal">
          </TD>
        </TR>
      </table>
    </form>
  </div>


  <div class="tabbertab" id="tab1" title="Davison Relationship Chart">
    <br>
    <h9>Davison Relationship Chart - </h9><strong>Enter two ID numbers in the boxes below to generate a Davison relationship chart for a couple.</strong><br><br>

    <form action="davison.php" method="post" style="width: 750px;">
      <table cellspacing="0" cellpadding="1" style="font-size:12px;">
        <TR>
          <td align="left">
            ID #1:
            <INPUT size="6" name="id1" value="">
          </TD>

          <td align="left">
            &nbsp;&nbsp;&nbsp;ID #2:
            <INPUT size="6" name="id2" value="">
          </TD>

          <td colspan='2'>
            <input type="hidden" name="submitted" value="submitted">
            &nbsp;&nbsp;&nbsp;<INPUT type="submit" name="submit1" value="Click here to produce a Davison relationship chart" style="background-color:#ddddee;color:#0000ff;font-size:12px;font-weight:normal">
          </TD>
        </TR>
      </table>
    </form>
  </div>
</div>


<br><br>


<div class="tabber" id="tab1">
  <div class="tabbertab tabbertabdefault" id="tab1" title="Solar Arcs">
    <br>
    <h9>Solar Arcs for Any Date compared to your Natal Chart</h9><br /><strong>Enter an ID number in the box below to generate Solar Arcs for Any Date compared to your Natal Chart.</strong><br><br>

    <form action="solar_arcs.php" method="post" style="width: 750px;">
      <table cellspacing="0" cellpadding="7" style="font-size:12px;">
        <TR>
          <td align="left">
            ID #:
            <INPUT size="6" name="id1" value="">

            <b>&nbsp;&nbsp;&nbsp;Solar arc date:</b>
            <?php

            $start_month = strftime("%m", time());
      $start_day = strftime("%d", time());
      $start_year = strftime("%Y", time());

            echo '&nbsp;&nbsp;&nbsp;<select name="start_month">';
            foreach ($months as $key => $value)
            {
              echo "<option value=\"$key\"";
              if ($key == $start_month)
              {
                echo ' selected="selected"';
              }
              echo ">$value</option>\n";
            }
            echo '</select>';
            ?>

            &nbsp;&nbsp;&nbsp;<INPUT size="2" maxlength="2" name="start_day" value="<?php echo $start_day; ?>">
            <b>,</b>&nbsp;
            &nbsp;&nbsp;&nbsp;<INPUT size="4" maxlength="4" name="start_year" value="<?php echo $start_year; ?>">
          </td>
        </tr>

        <tr>
          <td colspan='2'>
            <input type="hidden" name="submitted" value="submitted">
            &nbsp;&nbsp;&nbsp;<INPUT type="submit" name="submit1" value="Click here for Solar Arcs for Any Date compared to your Natal Chart" style="background-color:#ddddee;color:#0000ff;font-size:12px;font-weight:normal">
          </TD>
        </TR>
      </table>
    </form>
  </div>


  <div class="tabbertab" id="tab1" title="Progressions">
    <br>
    <h9>Progressions for Any Date compared to your Natal Chart</h9><br /><strong>Enter an ID number in the box below to generate Progressions for Any Date compared to your Natal Chart.</strong><br><br>

    <form action="progressions.php" method="post" style="width: 750px;">
      <table cellspacing="0" cellpadding="7" style="font-size:12px;">
        <TR>
          <td align="left">
            ID #:
            <INPUT size="6" name="id1" value="">

            <b>&nbsp;&nbsp;&nbsp;Progressed date:</b>
            <?php

            $start_month = strftime("%m", time());
            $start_day = strftime("%d", time());
            $start_year = strftime("%Y", time());

            echo '&nbsp;&nbsp;&nbsp;<select name="start_month">';
            foreach ($months as $key => $value)
            {
              echo "<option value=\"$key\"";
              if ($key == $start_month)
              {
                echo ' selected="selected"';
              }
              echo ">$value</option>\n";
            }
            echo '</select>';
            ?>

            &nbsp;&nbsp;&nbsp;<INPUT size="2" maxlength="2" name="start_day" value="<?php echo $start_day; ?>">
            <b>,</b>&nbsp;
            &nbsp;&nbsp;&nbsp;<INPUT size="4" maxlength="4" name="start_year" value="<?php echo $start_year; ?>">
          </td>
        </tr>

        <tr>
          <td colspan='2'>
            <input type="hidden" name="submitted" value="submitted">
            &nbsp;&nbsp;&nbsp;<INPUT type="submit" name="submit1" value="Click here for Progressions for Any Date compared to your Natal Chart" style="background-color:#ddddee;color:#0000ff;font-size:12px;font-weight:normal">
          </TD>
        </TR>
      </table>
    </form>
  </div>

  <div class="tabbertab" id="tab1" title="Progressed Moon Declinations">
    <br>
    <h9>Progressed Moon Declinations</h9><br /><strong>Enter an ID number in the box below to generate Progressed Moon Declinations for any period of time.</strong><br><br>

    <form action="progressed_moon_declinations.php" method="post" style="width: 750px;">
      <table cellspacing="0" cellpadding="7" style="font-size:12px;">
        <TR>
          <td align="left">
            ID #:
            <INPUT size="6" name="id1" value="">

            <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Number of years:</b>

            <?php
            $num_years = 80;
            ?>

            &nbsp;&nbsp;&nbsp;<INPUT size="3" maxlength="2" name="num_years" value="<?php echo $num_years; ?>">
          </td>
        </tr>

        <tr>
          <td colspan='2'>
            <input type="hidden" name="submitted" value="submitted">
            &nbsp;&nbsp;&nbsp;<INPUT type="submit" name="submit1" value="Click here for Progressed Moon Declinations" style="background-color:#ddddee;color:#0000ff;font-size:12px;font-weight:normal">
          </td>
        </tr>

        <tr>
          <td colspan='2'>
            <br>
            Want more info? Here are two links where you can read more about this technique:<br><br>
            <a href='http://www.astrologyweekly.com/astrology-articles/presidential-cycles.php'>Astrology Weekly</a>
            <br><br>
            <a href='http://karenchristino.com/wp-content/uploads/2013/08/Progressed-Moon-Declination-1.pdf'>Karen Christino</a>
          </td>
        </tr>
      </table>
    </form>
  </div>

  <div class="tabbertab" id="tab1" title="Solar Returns">
    <br>
    <h9>Solar Return - </h9><strong>Enter an ID number in the box below to generate a Solar Return for that person.</strong><br><br>

    <form action="solar_return.php" method="post" style="width: 750px;">
      <table cellspacing="0" cellpadding="1" style="font-size:12px;">
        <tr>
          <td align="left">
            ID #:
            <INPUT size="6" name="id1" value="">
          </TD>

          <?php
          $start_year = strftime("%Y", time());
          ?>

          <td align="left">
            Solar return year:
            <INPUT size="4" maxlength="4" name="start_year" value="<?php echo $start_year; ?>">
          </td>
        </tr>

    <TR>
      <TD>
        &nbsp;
      </TD>

      <TD>
        &nbsp;
      </TD>
    </TR>

    <TR>
      <td align="left">
        <P align="right"><font color="#ff0000">
        <b>IMPORTANT</b>
        </font></P>
      </td>

      <td>
        <font color="#ff0000">
        <b>NOTICE:</b>
        </font>
        <b>&nbsp;&nbsp;West longitudes are MINUS time zones.&nbsp;&nbsp;East longitudes are PLUS time zones.</b>
      </td>
    </TR>

    <TR>
      <td valign="top">Time zone #2:</td>

      <TD>
        <select name="timezone2" size="1">
          <option value="" selected>Select Time Zone</option>
          <option value="-12" >GMT -12:00 hrs - IDLW</option>
          <option value="-11" >GMT -11:00 hrs - BET or NT</option>
          <option value="-10.5" >GMT -10:30 hrs - HST</option>
          <option value="-10" >GMT -10:00 hrs - AHST</option>
          <option value="-9.5" >GMT -09:30 hrs - HDT or HWT</option>
          <option value="-9" >GMT -09:00 hrs - YST or AHDT or AHWT</option>
          <option value="-8" >GMT -08:00 hrs - PST or YDT or YWT</option>
          <option value="-7" >GMT -07:00 hrs - MST or PDT or PWT</option>
          <option value="-6" >GMT -06:00 hrs - CST or MDT or MWT</option>
          <option value="-5" >GMT -05:00 hrs - EST or CDT or CWT</option>
          <option value="-4" >GMT -04:00 hrs - AST or EDT or EWT</option>
          <option value="-3.5" >GMT -03:30 hrs - NST</option>
          <option value="-3" >GMT -03:00 hrs - BZT2 or AWT</option>
          <option value="-2" >GMT -02:00 hrs - AT</option>
          <option value="-1" >GMT -01:00 hrs - WAT</option>
          <option value="0" >Greenwich Mean Time - GMT or UT</option>
          <option value="1" >GMT +01:00 hrs - CET or MET or BST</option>
          <option value="2" >GMT +02:00 hrs - EET or CED or MED or BDST or BWT</option>
          <option value="3" >GMT +03:00 hrs - BAT or EED</option>
          <option value="3.5" >GMT +03:30 hrs - IT</option>
          <option value="4" >GMT +04:00 hrs - USZ3</option>
          <option value="5" >GMT +05:00 hrs - USZ4</option>
          <option value="5.5" >GMT +05:30 hrs - IST</option>
          <option value="6" >GMT +06:00 hrs - USZ5</option>
          <option value="6.5" >GMT +06:30 hrs - NST</option>
          <option value="7" >GMT +07:00 hrs - SST or USZ6</option>
          <option value="7.5" >GMT +07:30 hrs - JT</option>
          <option value="8" >GMT +08:00 hrs - AWST or CCT</option>
          <option value="8.5" >GMT +08:30 hrs - MT</option>
          <option value="9" >GMT +09:00 hrs - JST or AWDT</option>
          <option value="9.5" >GMT +09:30 hrs - ACST or SAT or SAST</option>
          <option value="10" >GMT +10:00 hrs - AEST or GST</option>
          <option value="10.5" >GMT +10:30 hrs - ACDT or SDT or SAD</option>
          <option value="11" >GMT +11:00 hrs - UZ10 or AEDT</option>
          <option value="11.5" >GMT +11:30 hrs - NZ</option>
          <option value="12" >GMT +12:00 hrs - NZT or IDLE</option>
          <option value="12.5" >GMT +12:30 hrs - NZS</option>
          <option value="13" >GMT +13:00 hrs - NZST</option>
        </select>

        <br />

        <font color="#0000ff">
        (example: Chicago is "GMT -06:00 hrs" (standard time), Paris is "GMT +01:00 hrs" (standard time).<br />
        Add 1 hour if Daylight Saving was in effect when you were born (select next time zone down in the list).
        <br /><br />
        </font>
      </TD>
    </TR>

    <TR>
      <td valign="top">Longitude #2:</td>
      <TD>
        <INPUT maxlength="3" size="3" name="long_deg2" value="<?php echo $_POST['long_deg2']; ?>">
        <select name="ew2">
          <?php
          if ($ew2 == "-1")
          {
            echo "<option value='-1' selected>W</option>";
            echo "<option value='1'>E</option>";
          }
          elseif ($ew2 == "1")
          {
            echo "<option value='-1'>W</option>";
            echo "<option value='1' selected>E</option>";
          }
          else
          {
            echo "<option value='' selected>Select</option>";
            echo "<option value='-1'>W</option>";
            echo "<option value='1'>E</option>";
          }
          ?>
        </select>

        <INPUT maxlength="2" size="2" name="long_min2" value="<?php echo $_POST['long_min2']; ?>">
        <font color="#0000ff">
        (example: Chicago is 87 W 39, Sydney is 151 E 13)
        </font>
      </TD>
    </TR>

    <TR>
      <td valign="top">Latitude #2:</td>

      <TD>
        <INPUT maxlength="2" size="3" name="lat_deg2" value="<?php echo $_POST['lat_deg2']; ?>">
        <select name="ns2">
          <?php
          if ($ns2 == "1")
          {
            echo "<option value='1' selected>N</option>";
            echo "<option value='-1'>S</option>";
          }
          elseif ($ns2 == "-1")
          {
            echo "<option value='1'>N</option>";
            echo "<option value='-1' selected>S</option>";
          }
          else
          {
            echo "<option value='' selected>Select</option>";
            echo "<option value='1'>N</option>";
            echo "<option value='-1'>S</option>";
          }
          ?>
        </select>

        <INPUT maxlength="2" size="2" name="lat_min2" value="<?php echo $_POST['lat_min2']; ?>">
        <font color="#0000ff">
        (example: Chicago is 41 N 51, Sydney is 33 S 52)
        </font>
        <br /><br />
        <a href='get_long_lat_tz.php'>Click here to access the atlas to get longitude, latitude, and time zone information</a>
        <br /><br />
      </TD>
    </TR>

        <tr>
          <td colspan='2'>
            <input type="hidden" name="submitted" value="submitted">
            &nbsp;&nbsp;&nbsp;<INPUT type="submit" name="submit1" value="Click here to produce a Solar Return" style="background-color:#ddddee;color:#0000ff;font-size:12px;font-weight:normal">
          </td>
        </tr>
      </table>
    </form>
  </div>


  <div class="tabbertab" id="tab1" title="Lunar Returns">
    <br>
    <h9>Lunar Return - </h9><strong>Enter an ID number in the box below to generate the next Lunar Return for that person.</strong><br><br>

    <form action="lunar_return.php" method="post" style="width: 750px;">
      <table cellspacing="0" cellpadding="5" style="font-size:12px;">
        <TR>
          <td align="left">
            ID #:
            <INPUT size="6" name="id1" value="">
          </TD>

          <?php
          $start_month = strftime("%m", time());
          $start_year = strftime("%Y", time());
          ?>

          <td align="left">
            &nbsp;&nbsp;&nbsp;Lunar return month:
            <INPUT size="3" maxlength="2" name="start_month" value="<?php echo $start_month; ?>">

            &nbsp;&nbsp;&nbsp;Lunar return day:
            <INPUT size="3" maxlength="2" name="start_day" value="<?php echo $start_day; ?>">

            &nbsp;&nbsp;&nbsp;Lunar return year:
            <INPUT size="4" maxlength="4" name="start_year" value="<?php echo $start_year; ?>">
          </TD>
        </tr>

    <TR>
      <td valign="top">
        <P align="right"><font color="#ff0000">
        <b>IMPORTANT</b>
        </font></P>
      </td>

      <td>
        <font color="#ff0000">
        <b>NOTICE:</b>
        </font>
        <b>&nbsp;&nbsp;West longitudes are MINUS time zones.&nbsp;&nbsp;East longitudes are PLUS time zones.</b>
      </td>
    </TR>

    <TR>
      <td valign="top">Time zone #2:</td>

      <TD>
        <select name="timezone2" size="1">
          <option value="" selected>Select Time Zone</option>
          <option value="-12" >GMT -12:00 hrs - IDLW</option>
          <option value="-11" >GMT -11:00 hrs - BET or NT</option>
          <option value="-10.5" >GMT -10:30 hrs - HST</option>
          <option value="-10" >GMT -10:00 hrs - AHST</option>
          <option value="-9.5" >GMT -09:30 hrs - HDT or HWT</option>
          <option value="-9" >GMT -09:00 hrs - YST or AHDT or AHWT</option>
          <option value="-8" >GMT -08:00 hrs - PST or YDT or YWT</option>
          <option value="-7" >GMT -07:00 hrs - MST or PDT or PWT</option>
          <option value="-6" >GMT -06:00 hrs - CST or MDT or MWT</option>
          <option value="-5" >GMT -05:00 hrs - EST or CDT or CWT</option>
          <option value="-4" >GMT -04:00 hrs - AST or EDT or EWT</option>
          <option value="-3.5" >GMT -03:30 hrs - NST</option>
          <option value="-3" >GMT -03:00 hrs - BZT2 or AWT</option>
          <option value="-2" >GMT -02:00 hrs - AT</option>
          <option value="-1" >GMT -01:00 hrs - WAT</option>
          <option value="0" >Greenwich Mean Time - GMT or UT</option>
          <option value="1" >GMT +01:00 hrs - CET or MET or BST</option>
          <option value="2" >GMT +02:00 hrs - EET or CED or MED or BDST or BWT</option>
          <option value="3" >GMT +03:00 hrs - BAT or EED</option>
          <option value="3.5" >GMT +03:30 hrs - IT</option>
          <option value="4" >GMT +04:00 hrs - USZ3</option>
          <option value="5" >GMT +05:00 hrs - USZ4</option>
          <option value="5.5" >GMT +05:30 hrs - IST</option>
          <option value="6" >GMT +06:00 hrs - USZ5</option>
          <option value="6.5" >GMT +06:30 hrs - NST</option>
          <option value="7" >GMT +07:00 hrs - SST or USZ6</option>
          <option value="7.5" >GMT +07:30 hrs - JT</option>
          <option value="8" >GMT +08:00 hrs - AWST or CCT</option>
          <option value="8.5" >GMT +08:30 hrs - MT</option>
          <option value="9" >GMT +09:00 hrs - JST or AWDT</option>
          <option value="9.5" >GMT +09:30 hrs - ACST or SAT or SAST</option>
          <option value="10" >GMT +10:00 hrs - AEST or GST</option>
          <option value="10.5" >GMT +10:30 hrs - ACDT or SDT or SAD</option>
          <option value="11" >GMT +11:00 hrs - UZ10 or AEDT</option>
          <option value="11.5" >GMT +11:30 hrs - NZ</option>
          <option value="12" >GMT +12:00 hrs - NZT or IDLE</option>
          <option value="12.5" >GMT +12:30 hrs - NZS</option>
          <option value="13" >GMT +13:00 hrs - NZST</option>
        </select>

        <br />

        <font color="#0000ff">
        (example: Chicago is "GMT -06:00 hrs" (standard time), Paris is "GMT +01:00 hrs" (standard time).<br />
        Add 1 hour if Daylight Saving was in effect when you were born (select next time zone down in the list).
        <br /><br />
        </font>
      </TD>
    </TR>

    <TR>
      <td valign="top">Longitude #2:</td>
      <TD>
        <INPUT maxlength="3" size="3" name="long_deg2" value="<?php echo $_POST['long_deg2']; ?>">
        <select name="ew2">
          <?php
          if ($ew2 == "-1")
          {
            echo "<option value='-1' selected>W</option>";
            echo "<option value='1'>E</option>";
          }
          elseif ($ew2 == "1")
          {
            echo "<option value='-1'>W</option>";
            echo "<option value='1' selected>E</option>";
          }
          else
          {
            echo "<option value='' selected>Select</option>";
            echo "<option value='-1'>W</option>";
            echo "<option value='1'>E</option>";
          }
          ?>
        </select>

        <INPUT maxlength="2" size="2" name="long_min2" value="<?php echo $_POST['long_min2']; ?>">
        <font color="#0000ff">
        (example: Chicago is 87 W 39, Sydney is 151 E 13)
        </font>
      </TD>
    </TR>

    <TR>
      <td valign="top">Latitude #2:</td>

      <TD>
        <INPUT maxlength="2" size="3" name="lat_deg2" value="<?php echo $_POST['lat_deg2']; ?>">
        <select name="ns2">
          <?php
          if ($ns2 == "1")
          {
            echo "<option value='1' selected>N</option>";
            echo "<option value='-1'>S</option>";
          }
          elseif ($ns2 == "-1")
          {
            echo "<option value='1'>N</option>";
            echo "<option value='-1' selected>S</option>";
          }
          else
          {
            echo "<option value='' selected>Select</option>";
            echo "<option value='1'>N</option>";
            echo "<option value='-1'>S</option>";
          }
          ?>
        </select>

        <INPUT maxlength="2" size="2" name="lat_min2" value="<?php echo $_POST['lat_min2']; ?>">
        <font color="#0000ff">
        (example: Chicago is 41 N 51, Sydney is 33 S 52)
        </font>
        <br /><br />
        <a href='get_long_lat_tz.php'>Click here to access the atlas to get longitude, latitude, and time zone information</a>
        <br /><br />
      </TD>
    </TR>

        <tr>
          <td colspan='3'>
            <input type="hidden" name="submitted" value="submitted">
            &nbsp;&nbsp;&nbsp;<INPUT type="submit" name="submit1" value="Click here to produce a Lunar Return" style="background-color:#ddddee;color:#0000ff;font-size:12px;font-weight:normal">
          </TD>
        </TR>
      </table>
    </form>
  </div>


  <div class="tabbertab" id="tab1" title="Solar Arcs & Transits">
    <br>
    <h9>Solar Arcs and Transits for Any Date compared to your Natal Chart</h9><br /><strong>Enter an ID number in the box below to generate Solar Arcs/Transits for Any Date compared to your Natal Chart.</strong><br><br>

    <form action="transits_solar_arcs.php" method="post" style="width: 750px;">
      <table cellspacing="0" cellpadding="7" style="font-size:12px;">
        <TR>
          <td align="left">
            ID #:
            <INPUT size="6" name="id1" value="">

            <b>&nbsp;&nbsp;&nbsp;Solar arc date:</b>
            <?php

            $start_month = strftime("%m", time());
            $start_day = strftime("%d", time());
            $start_year = strftime("%Y", time());

            echo '&nbsp;&nbsp;&nbsp;<select name="start_month">';
            foreach ($months as $key => $value)
            {
              echo "<option value=\"$key\"";
              if ($key == $start_month)
              {
                echo ' selected="selected"';
              }
              echo ">$value</option>\n";
            }
            echo '</select>';
            ?>

            &nbsp;&nbsp;&nbsp;<INPUT size="2" maxlength="2" name="start_day" value="<?php echo $start_day; ?>">
            <b>,</b>&nbsp;
            &nbsp;&nbsp;&nbsp;<INPUT size="4" maxlength="4" name="start_year" value="<?php echo $start_year; ?>">
          </td>
        </tr>

        <tr>
          <td colspan='2'>
            <input type="hidden" name="submitted" value="submitted">
            &nbsp;&nbsp;&nbsp;<INPUT type="submit" name="submit1" value="Click here for Solar Arcs/Transits for Any Date compared to your Natal Chart" style="background-color:#ddddee;color:#0000ff;font-size:12px;font-weight:normal">
          </TD>
        </TR>
      </table>
    </form>
  </div>


  <div class="tabbertab" id="tab1" title="Progs & Transits">
    <br>
    <h9>Progressions and Transits for Any Date compared to your Natal Chart</h9><br /><strong>Enter an ID number in the box below to generate Progressions/Transits for Any Date compared to your Natal Chart.</strong><br><br>

    <form action="transits_progressed.php" method="post" style="width: 750px;">
      <table cellspacing="0" cellpadding="7" style="font-size:12px;">
        <TR>
          <td align="left">
            ID #:
            <INPUT size="6" name="id1" value="">

            <b>&nbsp;&nbsp;&nbsp;Progressed date:</b>
            <?php

            $start_month = strftime("%m", time());
            $start_day = strftime("%d", time());
            $start_year = strftime("%Y", time());

            echo '&nbsp;&nbsp;&nbsp;<select name="start_month">';
            foreach ($months as $key => $value)
            {
              echo "<option value=\"$key\"";
              if ($key == $start_month)
              {
                echo ' selected="selected"';
              }
              echo ">$value</option>\n";
            }
            echo '</select>';
            ?>

            &nbsp;&nbsp;&nbsp;<INPUT size="2" maxlength="2" name="start_day" value="<?php echo $start_day; ?>">
            <b>,</b>&nbsp;
            &nbsp;&nbsp;&nbsp;<INPUT size="4" maxlength="4" name="start_year" value="<?php echo $start_year; ?>">
          </td>
        </tr>

        <tr>
          <td colspan='2'>
            <input type="hidden" name="submitted" value="submitted">
            &nbsp;&nbsp;&nbsp;<INPUT type="submit" name="submit1" value="Click here for Progressions/Transits for Any Date compared to your Natal Chart" style="background-color:#ddddee;color:#0000ff;font-size:12px;font-weight:normal">
          </TD>
        </TR>
      </table>
    </form>
  </div>
</div>


<br><br>


<div class="tabber" id="tab1">
  <div class="tabbertab" id="tab1" title="Transits to Natal Chart">
    <br>
    <h9>Transits for Any Date compared to your Natal Chart</h9><br /><strong>Enter an ID number in the box below to generate Transits for Any Date compared to your Natal Chart.</strong><br><br>

    <form action="transits_mp.php" method="post" style="width: 750px;">
      <table cellspacing="0" cellpadding="7" style="font-size:12px;">
        <TR>
          <td align="left">
            ID #:
            <INPUT size="6" name="id1" value="">

            <b>&nbsp;&nbsp;&nbsp;Transit date:</b>
            <?php

            $start_month = strftime("%m", time());
            $start_day = strftime("%d", time());
            $start_year = strftime("%Y", time());

            echo '&nbsp;&nbsp;&nbsp;<select name="start_month">';
            foreach ($months as $key => $value)
            {
              echo "<option value=\"$key\"";
              if ($key == $start_month)
              {
                echo ' selected="selected"';
              }
              echo ">$value</option>\n";
            }
            echo '</select>';
            ?>

            &nbsp;&nbsp;&nbsp;<INPUT size="2" maxlength="2" name="start_day" value="<?php echo $start_day; ?>">
            <b>,</b>&nbsp;
            &nbsp;&nbsp;&nbsp;<INPUT size="4" maxlength="4" name="start_year" value="<?php echo $start_year; ?>">
          </td>
        </tr>

        <tr>
          <td colspan='2'>
            <input type="hidden" name="submitted" value="submitted">
            &nbsp;&nbsp;&nbsp;<INPUT type="submit" name="submit1" value="Click here to produce Transits for Any Date compared to your Natal Chart" style="background-color:#ddddee;color:#0000ff;font-size:12px;font-weight:normal">
          </TD>
        </TR>
      </table>
    </form>
  </div>


  <div class="tabbertab" id="tab1" title="Transits for Right Now at your ISP location">
    <br>
    <a href="right_now_ISP.php">Transits for Right Now at your ISP location</a>
  </div>


  <div class="tabbertab" id="tab1" title="Transits for Any Day, Any Location">
    <br>
    <a href="transits_any_day.php">Transits for Any Day, Any Location</a>
  </div>


  <div class="tabbertab" id="tab1" title="Transits for Right Now">
    <br>
    <a href="right_now.php">Transits for Right Now</a>
  </div>
</div>


<br><br>


<div class="tabber" id="tab1">
  <div class="tabbertab tabbertabdefault" id="tab1" title="Progressed Planets vs Natal Planets">
    <br>
    <h9>Progressions (ID #2) vs Natal (ID #1)</h9> - <strong>Enter two ID numbers in the boxes below.</strong><br><br>

    <form action="progressed2_natal1.php" method="post" style="width: 750px;">
      <table cellspacing="0" cellpadding="7" style="font-size:12px;">
        <TR>
          <td align="left">
            ID #1:
            <INPUT size="6" name="id1" value="">

            &nbsp;&nbsp;&nbsp;ID #2:
            <INPUT size="6" name="id2" value="">

            <b>&nbsp;&nbsp;&nbsp;Progressed date:</b>
            <?php

            $start_month = strftime("%m", time());
            $start_day = strftime("%d", time());
            $start_year = strftime("%Y", time());

            echo '&nbsp;&nbsp;&nbsp;<select name="start_month">';
            foreach ($months as $key => $value)
            {
              echo "<option value=\"$key\"";
              if ($key == $start_month)
              {
                echo ' selected="selected"';
              }
              echo ">$value</option>\n";
            }
            echo '</select>';
            ?>

            &nbsp;&nbsp;&nbsp;<INPUT size="2" maxlength="2" name="start_day" value="<?php echo $start_day; ?>">
            <b>,</b>&nbsp;
            &nbsp;&nbsp;&nbsp;<INPUT size="4" maxlength="4" name="start_year" value="<?php echo $start_year; ?>">
          </td>
        </tr>

        <tr>
          <td colspan='2'>
            <input type="hidden" name="submitted" value="submitted">
            &nbsp;&nbsp;&nbsp;<INPUT type="submit" name="submit1" value="Click here to produce Progressed vs Natal Positions" style="background-color:#ddddee;color:#0000ff;font-size:12px;font-weight:normal">
          </TD>
        </TR>
      </table>
    </form>
  </div>


  <div class="tabbertab" id="tab1" title="Progressed Planets vs Progressed Planets">
    <br>
    <h9>Progressions for a Couple</h9> - <strong>Enter two ID numbers in the boxes below to generate Progressed Synastry.</strong><br><br>

    <form action="progressed_synastry.php" method="post" style="width: 750px;">
      <table cellspacing="0" cellpadding="7" style="font-size:12px;">
        <TR>
          <td align="left">
            ID #1:
            <INPUT size="6" name="id1" value="">

            &nbsp;&nbsp;&nbsp;ID #2:
            <INPUT size="6" name="id2" value="">

            <b>&nbsp;&nbsp;&nbsp;Progressed date:</b>
            <?php

            $start_month = strftime("%m", time());
            $start_day = strftime("%d", time());
            $start_year = strftime("%Y", time());

            echo '&nbsp;&nbsp;&nbsp;<select name="start_month">';
            foreach ($months as $key => $value)
            {
              echo "<option value=\"$key\"";
              if ($key == $start_month)
              {
                echo ' selected="selected"';
              }
              echo ">$value</option>\n";
            }
            echo '</select>';
            ?>

            &nbsp;&nbsp;&nbsp;<INPUT size="2" maxlength="2" name="start_day" value="<?php echo $start_day; ?>">
            <b>,</b>&nbsp;
            &nbsp;&nbsp;&nbsp;<INPUT size="4" maxlength="4" name="start_year" value="<?php echo $start_year; ?>">
          </td>
        </tr>

        <tr>
          <td colspan='2'>
            <input type="hidden" name="submitted" value="submitted">
            &nbsp;&nbsp;&nbsp;<INPUT type="submit" name="submit1" value="Click here to produce Progressed Synastry" style="background-color:#ddddee;color:#0000ff;font-size:12px;font-weight:normal">
          </TD>
        </TR>
      </table>
    </form>
  </div>


  <div class="tabbertab" id="tab1" title="Moon Aspects & Void-of-Course Moon">
    <br>
    <a href="moon_aspects.php">Moon Aspects and Void-of-Course Moon</a>
  </div>
</div>


<br><br>

        </div>
    </div>
</div>


    <?php
  }

  include ('../footer.html');
?>
