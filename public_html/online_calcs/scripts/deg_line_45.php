<?php
  session_start();
  
  include("constants_eng.php");
  require_once("sr.php");

  require_once ('../../../mysqli_connect_online_calcs_db_MYSQLI.php');
  //require_once ('../../../my_functions_MYSQLI.php');
  
  $rx1 = safeEscapeString($conn, $_GET["rx1"]);

  $longitude1 = $_SESSION['nmpL1'];

  //$longitude1 = $_SESSION['nmpL1'];
  
  $longitude1[LAST_PLANET + 2] = $longitude1[LAST_PLANET + 10];


// set the content-type
  header("Content-type: image/png");

// create the blank image
  $graphic_width = 720;
  $left_margin = 10;
  $right_margin = 10;
  $total_margin = $left_margin + $right_margin;

  $overall_size_v = Detect_num_same_deg_planets_max_45($longitude1);
  $overall_size_v = 50 + ($overall_size_v * 18);
  
  $im = @imagecreatetruecolor($graphic_width + $total_margin, $overall_size_v) or die("Cannot initialize new GD image stream"); //add margins so that '0' and '30' shows up in degree line

// specify the colors
  $white = imagecolorallocate($im, 255, 255, 255);
  $red = imagecolorallocate($im, 224, 0, 0);
  $blue = imagecolorallocate($im, 0, 0, 224);
  $green = imagecolorallocate($im, 0, 192, 0);
  $brown = imagecolorallocate($im, 148, 101, 77);
  $black = imagecolorallocate($im, 0, 0, 0);
  $grey = imagecolorallocate($im, 153, 153, 153);
  $gray = imagecolorallocate($im, 208, 208, 208);
  $background_color = imagecolorallocate($im, 192, 208, 255);

  $planet_color = $black;

//variables
  $x_dist_per_deg = $graphic_width / 45;    //distance is 12 units
  $start_of_y_axis = 5;
  $y_dist_to_horiz_line = 12;         //where the horizontal line is drawn across the degree line
  $y_dist_to_nick_start = 21;         //where the little 'nick' marks start
  $y_dist_to_deg_text = 40;           //where the little 'nick' marks start
  $y_dist_to_planet_glyph = 60;         //where planet glyph is drawn
  $y_dist_to_sign_glyph = 85;         //where sign glyph is drawn
  $v_length_of_planet_connecting_line = 0;    //length of line that goes from horizontal line pointing to planet sign/glyph
  $y_offset = 18;               //vertical distance between rows of planet-sign combinations
  $segment_length_of_5_deg = $x_dist_per_deg * 5;   //the distance between every 5 deg marker 'nick'
  $glyph_size = 14;               //the size of the glyph for planets and signs


// ------------------------------------------

// create white rectangle on blank image
  imagefilledrectangle($im, 0, 0, $graphic_width + $total_margin, $overall_size_v, $background_color); //add margins so that '0' and '30' shows up in degree line

// MUST BE HERE - I DO NOT KNOW WHY - MAYBE TO PRIME THE PUMP
  imagettftext($im, 10, 0, 0, 0, $black, 'arial.ttf', " ");


// ------------------------------------------

// draw the top horizonal line
  imageline($im, $left_margin, $start_of_y_axis + $y_dist_to_horiz_line, $left_margin + $graphic_width, $start_of_y_axis + $y_dist_to_horiz_line, $black);


// ------------------------------------------


// draw deg 'nicks' across the length of the horizontal lines
  $spoke_length = 4;
  $minor_spoke_length = 2;
  for ($i = 0; $i <= 45; $i++)
  {
    if ($i % 5 == 0)
    {
      $y1 = -$spoke_length * 2;
      $y2 = $spoke_length;
    }
    else
    {
      $y1 = -$spoke_length * 2;
      $y2 = $minor_spoke_length - 5;
    }

    imageline($im, $left_margin + ($i * $x_dist_per_deg), $start_of_y_axis + $y1 + $y_dist_to_nick_start, $left_margin + ($i * $x_dist_per_deg), $start_of_y_axis + $y2 + $y_dist_to_nick_start, $black);
  }


// ------------------------------------------


// place the text degrees across the horizontal line
  $cnt = 0;
  for ($i = 0; $i <= 45; $i = $i + 5)
  {
    $deg_filled_idx[$i] = 0;      //initialize
    
    $array = imagettfbbox(10, 0, 'arial.ttf', sprintf("%'02d", $cnt));
  $width_of_text = $array[4] - $array[6];

    $x1 = $i * $x_dist_per_deg;
    imagettftext($im, 10, 0, $left_margin + $x1 - ($width_of_text / 2), $start_of_y_axis + $y_dist_to_deg_text, $black, 'arial.ttf', sprintf("%'02d", $cnt));
    $cnt = $cnt + 5;
  }


// ------------------------------------------


// put planet glyphs across line
  // sort longitudes in descending order from 360 down to 0
  Sort_planets_by_descending_longitude(LAST_PLANET + 3, $longitude1, $sort, $sort_pos);


  for ($i = LAST_PLANET + 2; $i >= 0; $i--)
  {
    $pl_pos = floor($sort[$i]);

    $v_dist = $y_offset * $deg_filled_idx[$pl_pos];
    $deg_filled_idx[$pl_pos]++;
    
    $x1 = ($pl_pos * $x_dist_per_deg) - ($glyph_size / 2);

    if (substr($rx1, $sort_pos[$i], 1) == "R")
    {
      imagettftext($im, $glyph_size, 0, $left_margin + $x1, $start_of_y_axis + $y_dist_to_planet_glyph + $v_dist, $red, 'HamburgSymbols.ttf', chr($pl_glyph[$sort_pos[$i]]));
    }
    else
    {
      imagettftext($im, $glyph_size, 0, $left_margin + $x1, $start_of_y_axis + $y_dist_to_planet_glyph + $v_dist, $planet_color, 'HamburgSymbols.ttf', chr($pl_glyph[$sort_pos[$i]]));
    }

    $pl_sign = floor($longitude1[$sort_pos[$i]] / 30) + 1;
    if ($pl_sign == 1 Or $pl_sign == 5 Or $pl_sign == 9)
    {
      $clr_to_use = $red;
    }
    elseif ($pl_sign == 2 Or $pl_sign == 6 Or $pl_sign == 10)
    {
      $clr_to_use = $black;
    }
    elseif ($pl_sign == 3 Or $pl_sign == 7 Or $pl_sign == 11)
    {
      $clr_to_use = $blue;
    }
    elseif ($pl_sign == 4 Or $pl_sign == 8 Or $pl_sign == 12)
    {
      $clr_to_use = $green;
    }

    // put the signs across the top and bottom of the horizontal lines
//    imagettftext($im, $glyph_size, 0, $left_margin + $x1, $start_of_y_axis + $y_dist_to_sign_glyph + $v_dist, $clr_to_use, 'HamburgSymbols.ttf', chr($sign_glyph[$pl_sign]));
    //imagettftext($im, $glyph_size, 0, $left_margin + $x1, $start_of_y_axis + $y_dist_to_sign_glyph + $v_dist, $gray, 'HamburgSymbols.ttf', chr($sign_glyph[$pl_sign]));
  }


// ------------------------------------------


  // draw the image in png format - using imagepng() results in clearer text compared with imagejpeg()
  imagepng($im);
  imagedestroy($im);
  exit();


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


Function Sort_planets_by_descending_longitude($num_planets, $longitude1, &$sort, &$sort_pos)
{
// load all $longitude1[] into sort[] and keep track of the planet numbers in $sort_pos[]
  for ($i = 0; $i <= $num_planets - 1; $i++)
  {
    $sort[$i] = Reduce_below_45($longitude1[$i]);
    $sort_pos[$i] = $i;
  }

// do the actual sort
  for ($i = 0; $i <= $num_planets - 2; $i++)
  {
    for ($j = $i + 1; $j <= $num_planets - 1; $j++)
    {
      if ($sort[$j] > $sort[$i])
      {
        $temp = $sort[$i];
        $temp1 = $sort_pos[$i];

        $sort[$i] = $sort[$j];
        $sort_pos[$i] = $sort_pos[$j];

        $sort[$j] = $temp;
        $sort_pos[$j] = $temp1;
      }
    }
  }
}

?>
