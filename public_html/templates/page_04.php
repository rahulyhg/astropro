<?php
session_start();

require_once '../../mysqli_connect_online_calcs_db_MYSQLI.php';
require_once '../../my_functions_MYSQLI.php';

if(isset($_POST['email']))
$_SESSION['email']=$_POST['email'];

//Set values for Social Login
if (!empty($_SESSION['email'])) {
    $email = $_SESSION['email'];
} else {
    $email = $email;
}

if (!empty($_SESSION['name'])) {
    $name = $_SESSION['name'];
} else if (!empty($_SESSION['username'])){
    $name = $_SESSION['username'];
} else {
    $name = '';
}


include 'modules/head.php';
?>

<body>

    <div class="page">

        <?php
include 'modules/header.php';



include 'modules/nav.php';
?>


        <div class="main main_steps">

            <div class="main__wrap">
                <div class="container">
                    <div class="box">
                        <form class="form" name="form1" action="process_new.php" method="post">
                            <div class="box__heading">
                                <div>Customize Your Horoscope</div>
                                <a href="page_03.php" class="box__prev" title="prev"></a>
                                                           </div>

                            <div class="box__gray">
                                <div class="box__inner">
                                    <div class="form_group">
                                        <label class="form_label">First Name*</label>
                                        <div class="formBox">
                                            <input type="text" name="username" class="formBox__control" value="<?php echo $name; ?>">
                                            <!-- <i class="formBox__icon">
                                                <svg class="ico-svg" viewBox="0 0 30 30" xmlns="http://www.w3.org/2000/svg">
                                                    <use xlink:href="img/sprite-icons.svg#icon_check" xmlns:xlink="http://www.w3.org/1999/xlink"></use>
                                                </svg>
                                            </i> -->
                                        </div>
                                    </div>

                                    <div class="form_group">
                                    <label class="form_label">Birthday*</label>
                                    <ul class="form_date">
                                      <li>
                                      <select size="1" id='month' name="month" class="form_select">
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
                                    </li>
                                    <li>

        <select id="day" name="day" class="form_select">
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
          </li>
                                    <li>



                                             <select id="year" name="year" class="form_select"></select>
                                             </ul>
                                        </div>
                                     

                                        <div class="form_group">
                                        <label class="form_label">Place of birth*</label>
                                      <div id="locationField">  <input id="autocomplete" placeholder="Your city of birth" type="text" class="form_select"></input></div>

 </div>

      <div class="form_group">
                                    <ul class="form_date">
                                               <li>
                                      <select id="hour" name="hour" class="form_select">
		      <?php
for ($i = 1; $i <= 12; $i++) {
    echo "<option value='" . $i . "'>" . $i . "</option>";
}
?>
            </select>
</li>
<li>
             <select id="minute" name="minute" class="form_select">
		        <?php
for ($i = 0; $i <= 59; $i++) {
    echo "<option value='" . $i . "'>" . $i . "</option>";
}
?>
                 </select>
                 </li>
<li>


                  <select id="amorpm" name="amorpm" class="form_select">
            <option value="AM"> AM </option>
            <option value="PM"> PM </option>
            <option value="unknown"> Unknown </option>
            </select>
            </li>

</ul>
</div>

             
            <input type="checkbox" id="myCheck" onclick="enableText(this.checked)">
            <label>I don't know my birth time</label>

                <div class="hidden">
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

        Longitude: <input maxlength='3' size='3' id='long_deg' name='long_deg' class='form-control col-lg-2'>&nbsp;
        <input maxlength='1' size='1' id='ew' name='ew' class='form-control col-lg-2'>&nbsp;
        <input maxlength='2' size='2' id='long_min' name='long_min' class='form-control col-lg-2'> (the format here is like 88 W 37 - don't change the order)

        Latitude: <input maxlength='3' size='3' id='lat_deg' name='lat_deg' class='form-control col-lg-2' value = "">&nbsp;
        <input maxlength='1' size='1' id='ns' name='ns' class='form-control col-lg-2'>&nbsp;
        <input maxlength='2' size='2' id='lat_min' name='lat_min' class='form-control col-lg-2'> (the format here is like 42 N 1 - don't change the order)

<input class='form-control' id='email' type='email' maxLength='16' size='17' name='email' VALUE='<?php echo $email ?>' placeholder='email' required>
</div>
                                    </div>
                                </div>
                     
                            <div class="box__content box__content_radius">
                                <div class="box__inner">
                                    <div class="box__buttons">
                                        <button type="submit" class="btn btn_purple">Continue
                                            <i class="fa fa-caret-right"></i>
                                        </button>
                                        <div class="box__buttons_text">Your information is 100% secure and will not be shared.</div>
                                    </div>
                                </div>
                            </div>

                        </form>

                        <div class="box__step">
                            <div class="box__step_value">
                                <span>70%</span> complete</div>
                            <div class="box__step_progress">
                                <span style="width: 70%;"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>

        </div>

    <script src="js/vendor/modernizr-3.5.0.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
        crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="js/vendor/jquery-3.2.1.min.js"><\/script>')</script>
    <script src="js/vendor/svg4everybody.legacy.min.js"></script>
    <script src="js/vendor/jquery.fancybox/jquery.fancybox.min.js"></script>
    <script src="js/vendor/swiper/js/swiper.min.js"></script>
    <script src="js/vendor/jquery.scrollbar/jquery.scrollbar.min.js"></script>
    <script src="js/plugins.js"></script>
    <script src="js/main.js"></script>


</body>
<script>
      // This example displays an address form, using the autocomplete feature
      // of the Google Places API to help users fill in the information.

      // This example requires the Places library. Include the libraries=places
      // parameter when you first load the API. For example:
      // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

      var placeSearch, autocomplete;
      var componentForm = {
        locality: 'long_name',
        administrative_area_level_1: 'short_name',
        country: 'long_name'
      };

      function initAutocomplete() {
        // Create the autocomplete object, restricting the search to geographical
        // location types.
        autocomplete = new google.maps.places.Autocomplete(
            /** @type {!HTMLInputElement} */(document.getElementById('autocomplete')),
            {types: ['geocode']});

        // When the user selects an address from the dropdown, populate the address
        // fields in the form.
        autocomplete.addListener('place_changed', fillInAddress);
      }

      function fillInAddress() {
    var place = autocomplete.getPlace();

    for (var component in componentForm) {
        document.getElementById(component).value = '';
        document.getElementById(component).disabled = false;
    }

    for (var i = 0; i < place.address_components.length; i++) {
        var addressType = place.address_components[i].types[0];
        if (componentForm[addressType]) {
            var val = place.address_components[i][componentForm[addressType]];
            document.getElementById(addressType).value = val;
            var lat = place.geometry.location.lat();
            var lng = place.geometry.location.lng();
            document.getElementById("latitude").value = lat;
            document.getElementById("longitude").value = lng;
        }
    }
    document.getElementById('timezone').value = Math.round(longitude.value * 24 / 360);

    var dms = function (
        a, // decimal value (ex. -14.23463)
        b, // boundary; accepts "90" (Latitude) or "180" (Longitude)
        c  // precision for seconds
    ) {
        window.globalVar
        H = 'NSEW'[
            2 * (b != 90)      // expressions in brackets are booleans, that get coerced into 0 or 1
            + (a < 0)         // is the decimal value less than 0, coerced into 0 or 1
        ],
            a = (a < 0 ? -a : a) % b,  // convert value to absolute. shorten than Math.abs(a)
            // also get the modulo of the value and the boundary

            D = 0 | a,          // Degress: get the integer value; like Math.floor(a)
            a = (a - D) * 60,     // calulate the rest and multiply by 60
            M = 0 | a,          // Minutes
            a = (a - M) * 60,
            S = a.toFixed(c); // Seconds

        return [D + '°', M + '′', S + '″', H].join('\xA0');
    }
    console.log(timezone)
    var lt = latitude.value;
    var ln = longitude.value;

    var lt = dms( lt, 90);
    document.getElementById('lat_deg').value = D;
    document.getElementById('lat_min').value = M;
    var lts = S;
    document.getElementById('ns').value = H;

    var ln = dms( ln, 180);
    document.getElementById('long_deg').value = D;
    document.getElementById('long_min').value = M;
    var lns = S;
    document.getElementById('ew').value = H;
    console.log(long_deg)

}

    </script>

 <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDKRtOkPBEPK9Ys5pXI5KPwyx8PWLTpGhM&libraries=places&callback=initAutocomplete" async defer></script>
    <!-- <script src="http://localhost/astro_new/public_html/online_calcs/scripts/geoloc.js"></script> -->




 <script type="text/javascript">
$("#myModal").modal('show');

//    $(function () {
//         // var input = document.getElementById("keyword");
//         // var autocomplete = new google.maps.places.Autocomplete(input);


// });
</script>
<script>
    var year = 1930;
    var till = 2018;
    var options = "";
    for(var y=year; y<=till; y++){
        options += "<option>"+ y +"</option>";
    }
    document.getElementById("year").innerHTML = options;
</script>
<script>
function enableText(checked){
    if(!checked){
        document.getElementById('hour').disabled = false;
        document.getElementById('minute').disabled = false;
        document.getElementById('amorpm').disabled = false;
        document.getElementById('txt').disabled = true;
        }
    else{
        document.getElementById('hour').disabled = true;
        document.getElementById('minute').disabled = true;
        document.getElementById('amorpm').disabled = true;
        document.getElementById('txt').disabled = false;
    }
}
</script>
<!-- <script>
var autoElm = document.getElementById('elm-name');
autocomplete = new google.maps.places.Autocomplete(autoElm);
google.maps.event.addListener(autocomplete, 'place_changed', function () {
    var place = autocomplete.getPlace();
    if (!place.geometry) {
        return;
    }
});


// need to stop prop of the touchend event
if (navigator.userAgent.match(/(iPad|iPhone|iPod)/g)) {
    setTimeout(function() {
        var container = document.getElementsByClassName('pac-container')[0];
        container.addEventListener('touchend', function(e) {
            e.stopImmediatePropagation();
        });
    }, 500);
}
</script> -->
<style>
    .pac-container {
        z-index: 10000 !important;
    }
</style>
</html>