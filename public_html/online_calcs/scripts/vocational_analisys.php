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






	<?php
}

include ('../footer.html');
?>
