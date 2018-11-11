<?php
  session_start();
  
  include("constants_eng.php");
  require_once("sr.php");

  require_once ('../../../mysqli_connect_online_calcs_db_MYSQLI.php');
  //require_once ('../../../my_functions_MYSQLI.php');
  
  $ubt1 = safeEscapeString($conn, $_GET["ubt1"]);
  
  $longitude1 = $_SESSION['nmp_p2'];

  $mp = $_SESSION['mp'];
  

// set the content-type
  header("Content-type: image/png");

// create the blank image
  $graphic_width = 900;
  $left_margin = 10;
  $right_margin = 10;
  $total_margin = $left_margin + $right_margin;

  $overall_size_v = Detect_max_num_of_midpoints(0, 6, $longitude1, $mp, LAST_PLANET, $ubt1);
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
  $x_dist_per_deg = 132;
  $start_of_y_axis = 5;
  $y_dist_to_horiz_line = 12;         //where the horizontal line is drawn across the degree line
  $y_offset = 18;               //vertical distance between rows of planet-sign combinations
  $glyph_size = 12;               //the size of the glyph for planets and signs


// ------------------------------------------

// create white rectangle on blank image
  imagefilledrectangle($im, 0, 0, $graphic_width + $total_margin, $overall_size_v, $background_color); //add margins so that '0' and '30' shows up in degree line

// MUST BE HERE - I DO NOT KNOW WHY - MAYBE TO PRIME THE PUMP
  imagettftext($im, 10, 0, 0, 0, $black, 'arial.ttf', " ");


// ------------------------------------------

// draw the top horizonal line
  imageline($im, $left_margin - 5, $start_of_y_axis, $left_margin + $graphic_width + 9, $start_of_y_axis, $black);

// draw the left side line
  imageline($im, $left_margin - 5, $start_of_y_axis, $left_margin - 5, $start_of_y_axis + $overall_size_v - 20, $black);

// draw the right side line
  imageline($im, $left_margin + $graphic_width + 9, $start_of_y_axis, $left_margin + $graphic_width + 9, $start_of_y_axis + $overall_size_v - 20, $black);

// draw the bottom horizonal line
  imageline($im, $left_margin - 5, $start_of_y_axis + $overall_size_v - 20, $left_margin + $graphic_width + 9, $start_of_y_axis + $overall_size_v - 20, $black);

  for ($i = 1; $i <= 6; $i++)           // draw the vertical lines separating the planets
  {
    imageline($im, ($i * 132.5), $start_of_y_axis, ($i * 132.5), $start_of_y_axis + $overall_size_v - 20, $black);  
  }


// ------------------------------------------


// display midpoint tree - first 6 bodies here
    for ($k = 0; $k <= 6; $k++)                           // natal planets, Sun - Jupiter
    {
      $row_cntr = 0;

      $v_dist = $y_offset;
      $x1 = 30 + ($k * $x_dist_per_deg) - ($glyph_size / 2);
      imagettftext($im, $glyph_size, 0, $left_margin + $x1, $start_of_y_axis + $v_dist, $planet_color, 'HamburgSymbols.ttf', chr($pl_glyph[$k]));
      
      $x1 = 27 + ($k * $x_dist_per_deg) - ($glyph_size / 2);
      imagettftext($im, 10, 0, $left_margin + $x1 + 50, $start_of_y_axis + $y_offset, $planet_color, 'arial.ttf', "Orb");
      
      for ($i = 0; $i <= LAST_PLANET + 1; $i++)                   // natal midpoint
      {
        if ($i == SE_LILITH Or $i == SE_POF Or $i == SE_VERTEX) { continue; }   // don't do every midpoint combination

        for ($j = $i + 1; $j <= LAST_PLANET + 2; $j++)                // natal midpoint
        {
          if ($j == SE_LILITH Or $j == SE_POF Or $j == SE_VERTEX) { continue; }   // don't do every midpoint combination
        
          if ($k == $i Or $k == $j) { continue; }                 // don't allow the same planet to appear more than once
          
          if ($ubt1 == 1 And ($i > SE_TNODE Or $j > SE_TNODE Or $k > SE_TNODE)) { continue; }

          $q = 0;
          $da = abs($mp[$i][$j] - $longitude1[$k]);

          if ($da > 180) { $da = 360 - $da; }

          $orb = 1.0001;

          // is there an aspect within orb?
          if ($da <= $orb)
          {
            $q = 1;
            $dax = $da;
            $aspect_glyph = 113;
          }
          elseif (($da <= (90 + $orb)) And ($da >= (90 - $orb)))
          {
            $q = 4;
            $dax = $da - 90;
            $aspect_glyph = 114;
          }
          elseif ($da >= (180 - $orb))
          {
            $q = 2;
            $dax = 180 - $da;
            $aspect_glyph = 119;
          }

          if ($q > 0)
          {
            // aspect exists
            $row_cntr++;
            
            $v_dist = $y_offset + ($row_cntr * 18);
            $x1 = 9 + ($k * $x_dist_per_deg) - ($glyph_size / 2);
            imagettftext($im, $glyph_size, 0, $left_margin + $x1, $start_of_y_axis + $v_dist, $planet_color, 'HamburgSymbols.ttf', chr($pl_glyph[$i]));

            $x1 = 46 + ($k * $x_dist_per_deg) - ($glyph_size / 2);
            imageline($im, $x1 - 5, $start_of_y_axis + $v_dist - 6, $x1 + 5, $start_of_y_axis + $v_dist - 6, $black);   //draw horizontal line between planets  
            
            $x1 = 51 + ($k * $x_dist_per_deg) - ($glyph_size / 2);
            imagettftext($im, $glyph_size, 0, $left_margin + $x1, $start_of_y_axis + $v_dist, $planet_color, 'HamburgSymbols.ttf', chr($pl_glyph[$j]));

            $x1 = 75 + ($k * $x_dist_per_deg) - ($glyph_size / 2);
            imagettftext($im, 10, 0, $left_margin + $x1, $start_of_y_axis + $v_dist, $planet_color, 'arial.ttf', sprintf("%.2f", abs($dax)));

            $x1 = 108 + ($k * $x_dist_per_deg) - ($glyph_size / 2);
            imagettftext($im, $glyph_size, 0, $left_margin + $x1, $start_of_y_axis + $v_dist, $planet_color, 'HamburgSymbols.ttf', chr($aspect_glyph));
          }
        }
      }
      
      if ($row_cntr > 0)
      {
        // draw vertical line connecting the tree elements
        $x1 = 46 + ($k * $x_dist_per_deg) - ($glyph_size / 2);
        $v_dist = $y_offset + ($row_cntr * 18) - 7;
        imageline($im, $x1, $start_of_y_axis + 25, $x1, $start_of_y_axis + $v_dist, $black);  
      }
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

?>
