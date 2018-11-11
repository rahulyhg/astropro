<?php
  $retrograde = $rx1;

  $longitude = $_SESSION['nL1'];
  $hc = $_SESSION['nHC1'];

  $longitude[LAST_PLANET + 1] = $hc1[1];
  $longitude[LAST_PLANET + 2] = $hc1[10];

// create the blank image
  $overall_size = 450;      //add a planet
  $extra_width = 255;       //in order to make total width = 680
  $margins = 20;            //left and right margins on the background graphic

  $im = @imagecreatetruecolor($overall_size + $extra_width, $overall_size) or die("Cannot initialize new GD image stream");

// specify the colors
  $white = imagecolorallocate($im, 255, 255, 255);
  $red = imagecolorallocate($im, 255, 0, 0);
  $blue = imagecolorallocate($im, 0, 0, 255);
  $magenta = imagecolorallocate($im, 255, 0, 255);
  $yellow = imagecolorallocate($im, 255, 255, 0);
  $cyan = imagecolorallocate($im, 0, 255, 255);
  $green = imagecolorallocate($im, 0, 224, 0);
  $grey = imagecolorallocate($im, 127, 127, 127);
  $black = imagecolorallocate($im, 0, 0, 0);
  $lavender = imagecolorallocate($im, 160, 0, 255);
  $orange = imagecolorallocate($im, 255, 127, 0);
  $light_blue = imagecolorallocate($im, 239, 255, 255);

  $pl_name[16] = "Midheaven";
  
  $asp_color[1] = $blue;
  $asp_color[2] = $red;
  $asp_color[3] = $green;
  $asp_color[4] = $red;           //$magenta;
  $asp_color[5] = $blue;          //$cyan;
  $asp_color[6] = $green;         //$orange;

  $cell_width = 25;
  $cell_height = 25;

  $last_planet_num = LAST_PLANET + 2;                //add a planet
  if ($ubt1 == 1) { $last_planet_num = LAST_PLANET - 2; }

  $num_planets = $last_planet_num + 1;

  $left_margin_planet_table = ($num_planets + 0.5) * $cell_width;

// ------------------------------------------

// create rectangle on blank image
  imagefilledrectangle($im, 0, 0, $overall_size + $extra_width, $overall_size, $white);     //705 x 450 - add a planet

// MUST BE HERE - I DO NOT KNOW WHY - MAYBE TO PRIME THE PUMP
  imagettftext($im, 10, 0, 0, 0, $black, 'arial.ttf', " ");

// ------------------------------------------

// draw the grid - horizontal lines
  for ($i = 0; $i <= $last_planet_num - 1; $i++)
  {
    imageline($im, $margins, $cell_height * ($i + 1), $margins + $cell_width * ($i + 1), $cell_height * ($i + 1), $black);
  }

  imageline($im, $margins, $cell_height * $num_planets, $margins + $cell_width * $i, $cell_height * $num_planets, $black);

// draw the grid - vertical lines
  for ($i = 1; $i <= $last_planet_num; $i++)
  {
    imageline($im, $margins + $cell_width * $i, $cell_height * $num_planets, $margins + $cell_width * $i, $cell_height * $i, $black);
  }

  imageline($im, $margins, $cell_height * $num_planets, $margins, $cell_height, $black);

// ------------------------------------------

// draw in the planet glyphs
  for ($i = 0; $i <= $last_planet_num; $i++)
  {
    drawboldtext_AG($im, 18, 0, $margins + $i * $cell_width, $cell_height * ($i + 1), $black, 'HamburgSymbols.ttf', chr($pl_glyph[$i]), 0);

    // display planet data in the right-hand table
    drawboldtext_AG($im, 16, 0, $margins + $left_margin_planet_table, $cell_height * ($i + 1), $black, 'HamburgSymbols.ttf', chr($pl_glyph[$i]), 0);
    imagettftext($im, 10, 0, $margins + $left_margin_planet_table + $cell_width * 2, $cell_height * ($i + 1) - 3, $blue, 'arial.ttf', $pl_name[$i]);
    $sign_num = floor($longitude[$i] / 30) + 1;
    drawboldtext_AG($im, 14, 0, $margins + $left_margin_planet_table + $cell_width * 5, $cell_height * ($i + 1), $black, 'HamburgSymbols.ttf', chr($sign_glyph[$sign_num]), 0);
    imagettftext($im, 10, 0, $margins + $left_margin_planet_table + $cell_width * 6, $cell_height * ($i + 1) - 3, $blue, 'arial.ttf', Convert_Longitude_AG($longitude[$i]) . " " . $rx1[$i]);
  }

// ------------------------------------------

// display the aspect glyphs in the aspect grid
  for ($i = 0; $i <= $last_planet_num - 1; $i++)
  {
    for ($j = $i + 1; $j <= $last_planet_num; $j++)
    {
      //if ($i >= SE_LILITH Or $j >= SE_LILITH) { continue; }
      
      $q = 0;
      $da = abs($longitude[$i] - $longitude[$j]);

      if ($da > 180) { $da = 360 - $da; }

      // set orb - 8 if Sun or Moon, 6 if not Sun or Moon
      $orb = 6;
      if ($i == 0 Or $i == 1 Or $j == 0 Or $j == 1) { $orb = 8; }

      // is there an aspect within orb?
      if ($da <= $orb)
      {
        $q = 1;
      }
      elseif (($da <= (60 + $orb)) And ($da >= (60 - $orb)))
      {
        $q = 6;
      }
      elseif (($da <= (90 + $orb)) And ($da >= (90 - $orb)))
      {
        $q = 4;
      }
      elseif (($da <= (120 + $orb)) And ($da >= (120 - $orb)))
      {
        $q = 3;
      }
      elseif (($da <= (150 + $orb)) And ($da >= (150 - $orb)))
      {
        $q = 5;
      }
      elseif ($da >= (180 - $orb))
      {
        $q = 2;
      }

      if ($q > 0)
      {
        drawboldtext_AG($im, 14, 0, $margins + $cell_width * ($i + 0.15), $cell_height * ($j + 1 - 0.20), $asp_color[$q], 'HamburgSymbols.ttf', chr($asp_glyph[$q]), 0);
      }
    }
  }


  // draw the image in png format - using imagepng() results in clearer text compared with imagejpeg()

  $grids_filename = $_SESSION['grids_filename'];
  
  imagepng($im, $grids_filename);          //output image to a file
  imagedestroy($im);


Function Convert_Longitude_AG($longitude)
{
  $signs = array (0 => 'Ari', 'Tau', 'Gem', 'Can', 'Leo', 'Vir', 'Lib', 'Sco', 'Sag', 'Cap', 'Aqu', 'Pis');

  $sign_num = floor($longitude / 30);
  $pos_in_sign = $longitude - ($sign_num * 30);
  $deg = floor($pos_in_sign);
  $full_min = ($pos_in_sign - $deg) * 60;
  $min = floor($full_min);
  $full_sec = round(($full_min - $min) * 60);

  if ($deg < 10)
  {
    $deg = "0" . $deg;
  }

  if ($min < 10)
  {
    $min = "0" . $min;
  }

  if ($full_sec < 10)
  {
    $full_sec = "0" . $full_sec;
  }

  return $deg . " " . $signs[$sign_num] . " " . $min . "' " . $full_sec . chr(34);
}


Function drawboldtext_AG($image, $size, $angle, $x_cord, $y_cord, $clr_to_use, $fontfile, $text, $boldness)
{
  $_x = array(1, 0, 1, 0, -1, -1, 1, 0, -1);
  $_y = array(0, -1, -1, 0, 0, -1, 1, 1, 1);

  for($n = 0; $n <= $boldness; $n++)
  {
    ImageTTFText($image, $size, $angle, $x_cord+$_x[$n], $y_cord+$_y[$n], $clr_to_use, $fontfile, $text);
  }
}

?>
