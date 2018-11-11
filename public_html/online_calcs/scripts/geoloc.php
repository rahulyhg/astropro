<!DOCTYPE html>
<title>Foo</title>
<div>Latitude:  <code id="lat"></code></div>
<div>Longitude: <code id="lng"></code></div>
<script>

    var dms = function(
        a, // decimal value (ex. -14.23463)
        b, // boundary; accepts "90" (Latitude) or "180" (Longitude)
        c  // precision for seconds
    ){
        window.globalVar
            // get the direction indicator
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



        // return formatted values joined by non-breaking space
        return [D+'°',M+'′',S+'″',H].join('\xA0');

    }

    var v = 15.1245;
    var t = -5.1245;
    var long = dms( v, 90)
    console.log(long)
    console.log(D)
    console.log(M)
    console.log(S)
    console.log(H)

    var lat = dms( t, 180)
    console.log(lat)
    console.log(D)
    console.log(M)
    console.log(S)
    console.log(H)
</script>