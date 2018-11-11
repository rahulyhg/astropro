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
        { types: ['geocode'] });

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

    var lt = dms(lt, 90);
    document.getElementById('lat_deg').value = D;
    document.getElementById('lat_min').value = M;
    var lts = S;
    document.getElementById('ns').value = H;

    var ln = dms(ln, 180);
    document.getElementById('long_deg').value = D;
    document.getElementById('long_min').value = M;
    var lns = S;
    document.getElementById('ew').value = H;
    console.log(long_deg)
}
