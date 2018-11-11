<?php
  include ('../accesscontrol.php');

  if ($is_logged_in == False)
  {
    echo "You are not yet logged in.";
    exit();
  }

  include ('../constants.php');           //nedded because of "../footer.html" statements
include '../header.html';
include '../nav2.php';

?>

<!-- Background white, links blue (unvisited), navy (visited), red (active) -->
<body>

<div class="container round_block">
    <div class="row">
        <div class="col-md-12">

<h1>Your Personal Database</h1>
<p><strong>Note: All fields are required</strong></p>

<p class="my_h3"><strong>Please double-check your data when you enter it so you are not doing incorrect charts. After entering state/country and city, then clicking on the city you want, you should NOT need to change the time zone, longitude, or latitude.</p>

<br>

<form name="astro_input_form" action="add_to_db_1.php" method="POST" style='text-align:center; margin: 0px 150px;'>
 <fieldset><legend><font size='4'><b>Enter your birth data here</b></font></legend>
  &nbsp;<font color='#ff0000'><b>All fields are required</b></font><br>
        </div>
    </div>
<div class="spacer-30"></div>

     <div class="row">
         <div class="form-inline col-lg-6">

             <div class="row-1">
             <label>Name:</label>
              <input type='text' id='name' name='name' class='form-control col-lg-7'>
             </div>

             <div class="row-1">
             <label>Sex:</label>
        <select class="form-control col-lg-2" name="sex" id="sel1">
                  <option>M</option>
                  <option>F</option>
              </select>
             </div>


             <div class="row-1">
            <label>Birth month:</label>
              <select size="1" id='month' name="month" class="form-control col-lg-3">
              <option selected value="1">January</option>
              <option value="2">February</option>
              <option value="3">March</option>
              <option value="4">April</option>
              <option value="5">May</option>
              <option value="6">June</option>
              <option value="7">July</option>
              <option value="8">August</option>
              <option value="9">September</option>
              <option value="10">October</option>
              <option value="11">November</option>
              <option value="12">December</option>
          </select>
             </div>



             <div class="row-1">
             <label>Birth day:</label>
            <select id="day" name="day" class="form-control col-lg-2">
          <option>1</option>
          <option>2</option>
          <option>3</option>
          <option>4</option>
          <option>5</option>
          <option>6</option>
          <option>7</option>
          <option>8</option>
          <option>9</option>
          <option>10</option>
          <option>11</option>
          <option>12</option>
          <option>13</option>
          <option>14</option>
          <option>15</option>
          <option>16</option>
          <option>17</option>
          <option>18</option>
          <option>19</option>
          <option>20</option>
          <option>21</option>
          <option>22</option>
          <option>23</option>
          <option>24</option>
          <option>25</option>
          <option>26</option>
          <option>27</option>
          <option>28</option>
          <option>29</option>
          <option>30</option>
          <option>31</option>
          </select>
             </div>



             <div class="row-1">
          <label>Birth year:</label>
        <select id="year" name="year" class="form-control col-lg-2"></select>
             </div>


             <div class="row-1">
         <label>Birth hour:</label>
        <select id="hour" name="hour" class="form-control col-lg-2">
		      <?php
		      for ($i=1; $i<=12; $i++){
			      echo "<option value='".$i."'>" . $i ."</option>";
		      }
		      ?>
          </select>
             </div>


             <div class="row-1">
        <label>Birth minute:</label>
        <select id="minute" name="minute" class="form-control col-lg-2">
		        <?php

		        for ($i=0; $i<=59; $i++){
			        echo "<option value='".$i."'>" . $i ."</option>";
		        }
		        ?>
            </select>
             </div>


             <div class="row-1">
                 <label> </label>
        <select id="amorpm" name="amorpm" class="form-control col-lg-2">
          <option value="AM"> AM </option>
          <option value="PM"> PM </option>
          <option value="unknown"> Unknown </option>
        </select>
             </div>

        &nbsp;
  </div>


          <div class="form-inline col-lg-6">

              <div class="row-1">
              <label>City</label>
              <div id="locationField">  <input id="autocomplete" placeholder="Enter your address" type="text" class="form-control col-lg-7"></input></div>
              </div>

              <div class="row-1">
              <label>City</label>
              <input id="locality" disabled="true" class="form-control col-lg-7"></input>
              </div>

              <div class="row-1">
              <label>State</label>
                  <input id="administrative_area_level_1" disabled="true" class="form-control col-lg-7"></input>
              </div>

              <div class="row-1">
              <label>Country</label>
                      <input id="country" disabled="true" class="form-control col-lg-7"></input>
              </div>

              <div class="row-1">
              <label>Latitude</label>
          <input name="latitude" id="latitude" placeholder="Latitude" class="form-control col-lg-2">
              </div>

              <div class="row-1">
              <label>Longitude</label>
              <input name="longitude" id="longitude" placeholder="Longitude" class="form-control col-lg-2">
              </div>

              <div class="row-1">
        <label>Time zone:</label>
              <input id='timezone' name='timezone' class='form-control col-lg-2'>
              </div>

<!-- <div class="hidden"> -->
        Longitude: <input maxlength='3' size='3' id='long_deg' name='long_deg' class='form-control col-lg-2'>&nbsp;
        <input maxlength='1' size='1' id='ew' name='ew' class='form-control col-lg-2'>&nbsp;
        <input maxlength='2' size='2' id='long_min' name='long_min' class='form-control col-lg-2'> (the format here is like 88 W 37 - don't change the order)



        Latitude: <input maxlength='3' size='3' id='lat_deg' name='lat_deg' class='form-control col-lg-2' value = "">&nbsp;
        <input maxlength='1' size='1' id='ns' name='ns' class='form-control col-lg-2'>&nbsp;
        <input maxlength='2' size='2' id='lat_min' name='lat_min' class='form-control col-lg-2'> (the format here is like 42 N 1 - don't change the order)

<!-- </div> -->
  
        </div>
    </div>
    <div class="spacer-30"></div>
    <input type="hidden" name="submitted" value="True">
    <center>
        <input type="submit" value="Add the above data to your database" class='btn btn-primary'>&nbsp;&nbsp;
        <input type="reset" value="Reset" class='btn btn-primary'>
    </center>
    </fieldset>
    </form>
</div>



<script>
    var year = 1930;
    var till = 2018;
    var options = "";
    for(var y=year; y<=till; y++){
        options += "<option>"+ y +"</option>";
    }
    document.getElementById("year").innerHTML = options;

</script>

</body>

