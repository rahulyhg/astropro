<!DOCTYPE html>
<html>
<head>
	<title>Place Autocomplete Address Form</title>
	<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
	<meta charset="utf-8">
	<link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500">
	<style>
        #map {
            height: 100%;
        }
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
		#locationField, #controls {
			position: relative;
			width: 480px;
		}
		#autocomplete {
			position: absolute;
			top: 0px;
			left: 0px;
			width: 99%;
		}
		.label {
			text-align: right;
			font-weight: bold;
			width: 100px;
			color: #303030;
		}
		#address {
			border: 1px solid #000090;
			background-color: #f0f0ff;
			width: 480px;
			padding-right: 2px;
		}
		#address td {
			font-size: 10pt;
		}
		.field {
			width: 99%;
		}
		.slimField {
			width: 80px;
		}
		.wideField {
			width: 200px;
		}
		#locationField {
			height: 20px;
			margin-bottom: 2px;
		}
	</style>
</head>

<body>
<script>
    var autocomplete;
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

        var dms = function(
            a, // decimal value (ex. -14.23463)
            b, // boundary; accepts "90" (Latitude) or "180" (Longitude)
            c  // precision for seconds
        ){
            window.globalVar
            H='NSEW'[
            2*(b!=90)      // expressions in brackets are booleans, that get coerced into 0 or 1
            +(a<0)         // is the decimal value less than 0, coerced into 0 or 1
                ],
                a=(a<0?-a:a)%b,  // convert value to absolute. shorten than Math.abs(a)
                                 // also get the modulo of the value and the boundary

                D=0|a,          // Degress: get the integer value; like Math.floor(a)
                a=(a-D)*60,     // calulate the rest and multiply by 60
                M=0|a,          // Minutes
                a=(a-M)*60,
                S=a.toFixed(c); // Seconds

            return [D+'°',M+'′',S+'″',H].join('\xA0');
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
<script src="https://maps.googleapis.com/maps/api/js?key= AIzaSyDKRtOkPBEPK9Ys5pXI5KPwyx8PWLTpGhM &libraries=places&callback=initAutocomplete"
        async defer></script>

<div id="locationField">
	<input id="autocomplete" placeholder="Enter your address" type="text"></input>
</div>

<table id="address">
	<tr>
		<td class="label">City</td>
		<td class="wideField" colspan="3">
			<input class="field" id="locality" disabled="true"></input></td>
	</tr>
	<tr>
		<td class="label">State</td>
		<td class="slimField">
			<input class="field" id="administrative_area_level_1" disabled="true"></input>
		</td>

	</tr>
	<tr>
		<td class="label">Country</td>
		<td class="wideField" colspan="3">
			<input class="field" id="country" disabled="true"></input></td>
	</tr>
</table>
<input name="latitude" id="latitude" placeholder="Latitude"><br>
<input name="longitude" id="longitude" placeholder="Longitude"><br>
Timezone
<input size='10' id='timezone' name='timezone' style='text-align:center;' value = "">
Longitude:
<input maxlength='3' size='3' id='long_deg' name='long_deg' style='text-align:center;' value = "">&nbsp;
<input maxlength='1' size='1' id='ew' name='ew' style='text-align:center;' value = "">&nbsp;
<input maxlength='2' size='2' id='long_min' name='long_min' style='text-align:center;' value = ""><br>
Latitude:
<input maxlength='3' size='3' id='lat_deg' name='lat_deg' style='text-align:center;' value = "">&nbsp;
<input maxlength='1' size='1' id='ns' name='ns' style='text-align:center;' value = "">&nbsp;
<input maxlength='2' size='2' id='lat_min' name='lat_min' style='text-align:center;' value = "">
</body>
</html>