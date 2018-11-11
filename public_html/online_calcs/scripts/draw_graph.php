<?php
  session_start();

  include ('../accesscontrol.php');

  if ($is_logged_in == False) { exit(); }

  $declination2 = $_SESSION['declination2'];

  require_once ('../../../mysqli_connect_online_calcs_db_MYSQLI.php');      //for the below safeEscapeString() function
  require_once ('../../../my_functions_MYSQLI.php');

  $start_year = intval(safeEscapeString($conn, $_GET["sy"]));
  $years_to_run = intval(safeEscapeString($conn, $_GET["ny"]));
  $line1 = safeEscapeString($conn, $_GET["l1"]);

//FOR DEBUG
  //$start_year = 1949;
  //$years_to_run = 80;
  //$line1 = "Otis_Lewd, born September 22, 1949 at 10:43 (time zone = GMT -6 hours) at 95w24 and 29n43";

  display_graph($start_year, $years_to_run, $declination2, $line1);

  exit();


Function display_graph($start_year, $years_to_run, $declination2, $line1)
{
  define(GRAPH_WIDTH, 1024);                 //was 800
  define(GRAPH_HEIGHT, 700);

  define(TOPLINE, 50);
  define(BOTTOMLINE, 650);
  define(TOP_TO_BOTTOM_DIFF, 600);

  define(LEFTMARGIN, 50);
  define(RIGHTMARGIN, 994);                 //was 770

  define(Y_AXIS_VALUES_XPOS, 20);
  define(Y_AXIS_VALUES_HALF_CHAR_HEIGHT, 6);
  define(X_AXIS_VALUES_CHAR_WIDTH, 6);


  // set the content-type
  header("Content-type: image/png");

  // create the blank image
  $im = @imagecreatetruecolor(GRAPH_WIDTH, GRAPH_HEIGHT) or die("Cannot initialize new GD image stream");

  // specify the colors
  $white = imagecolorallocate($im, 255, 255, 255);
  $red = imagecolorallocate($im, 255, 0, 0);
  $blue = imagecolorallocate($im, 0, 0, 255);
  $magenta = imagecolorallocate($im, 255, 0, 255);
  $yellow = imagecolorallocate($im, 255, 255, 0);
  $cyan = imagecolorallocate($im, 0, 255, 255);
  $green = imagecolorallocate($im, 0, 255, 0);
  $gray = imagecolorallocate($im, 127, 127, 127);
  $light_gray = imagecolorallocate($im, 32, 32, 32);
  $black = imagecolorallocate($im, 0, 0, 0);
  $lavender = imagecolorallocate($im, 160, 0, 255);
  $background_color = imagecolorallocate($im, 192, 208, 255);

  $bg_color = $black;     // set background color


  //create black rectangle on blank image
  imagefilledrectangle($im, 0, 0, GRAPH_WIDTH, GRAPH_HEIGHT, $bg_color);

  //MUST BE HERE - I DO NOT KNOW WHY - MAYBE TO PRIME THE PUMP
  imagettftext($im, 10, 0, 0, 0, $bg_color, 'arial.ttf', " ");


  //the first 8 cover the main cases, generally from younger person to older person
  //colors for lines
  $clr_to_use = $red;


  //$start_date = strftime("%d %B %Y", mktime(12, 0, 0, $start_date_month, $start_date_day, $start_date_year));
  //$text = "Degrees from exact for " . $names_to_use . " - starting on " . $start_date . " - (" . $collision_aspect_names[$q] . ")";
  //imagettftext($im, 12, 0, LEFTMARGIN, 25, $white, 'arial.ttf', $text);


// START OF CODE
  $starty = 1;
  $endery = $years_to_run + 1;

  $HighY = 30;
  $LowY = -30;

  $Range = $HighY - $LowY;

  $YDivisionAmount = $Range / 6;

  $XDIV = (RIGHTMARGIN - LEFTMARGIN) / ($endery - $starty);


  //draw the zero line
  $y0 = BOTTOMLINE - TOP_TO_BOTTOM_DIFF * (0 - $LowY) / ($HighY - $LowY);
  imageline($im, LEFTMARGIN - 4, $y0 - 1, RIGHTMARGIN, $y0 - 1, $blue);
  imageline($im, LEFTMARGIN - 4, $y0, RIGHTMARGIN, $y0, $blue);
  imageline($im, LEFTMARGIN - 4, $y0 + 1, RIGHTMARGIN, $y0 + 1, $blue);


  //draw the ecliptic lines for OOB
  $y0 = BOTTOMLINE - TOP_TO_BOTTOM_DIFF * (23.5 - $LowY) / ($HighY - $LowY);
  imageline($im, LEFTMARGIN - 4, $y0, RIGHTMARGIN, $y0, $yellow);

  $y0 = BOTTOMLINE - TOP_TO_BOTTOM_DIFF * (-23.5 - $LowY) / ($HighY - $LowY);
  imageline($im, LEFTMARGIN - 4, $y0, RIGHTMARGIN, $y0, $yellow);


  // draw the y axis division labels and lines (horizontal)
  for ($j = 0; $j <= 6; $j++)
  {
    $a = $HighY - ($YDivisionAmount * $j);         //y-axis values

    $offset = 0;

    if ($YDivisionAmount - floor($YDivisionAmount) != 0)
    {
      $a = sprintf("%.1f", $a);
      $offset = 5;
    }

    if ($a <= -10)
    {
      $a = "" . $a;
    }
    elseif ($a < 0)
    {
      $a = " " . $a;
    }
    elseif ($a < 10)
    {
      $a = "  " . $a;
    }

    imagettftext($im, 12, 0, Y_AXIS_VALUES_XPOS - $offset, (TOPLINE + (100 * $j)) + Y_AXIS_VALUES_HALF_CHAR_HEIGHT, $white, 'arial.ttf', $a);

    imageline($im, LEFTMARGIN - 4, BOTTOMLINE - (100 * $j), RIGHTMARGIN, BOTTOMLINE - (100 * $j), $gray);           //major y-axis horizontal lines
  }


  // draw the y axis sub-division labels and lines (horizontal)
  $YSDIV = 5;

  for ($i = 0; $i <= 5; $i++)               //because there are 6 major sections between 30 and -30
  {
    for ($j = 1; $j <= $YSDIV - 1; $j++)
    {
      $z = TOPLINE + ($i * (TOP_TO_BOTTOM_DIFF / 6)) + ($j * (TOP_TO_BOTTOM_DIFF / 6) / $YSDIV);
      imageline($im, LEFTMARGIN, $z, RIGHTMARGIN, $z, $light_gray);             //minor y-axis horizontal lines

      $a = $HighY - (($i * $YDivisionAmount) + $j * $YDivisionAmount / $YSDIV);

      if ($a <= -10)
      {
        $a = "" . $a;
      }
      elseif ($a < 0)
      {
        $a = " " . $a;
      }
      elseif ($a < 10)
      {
        $a = "  " . $a;
      }

      imagettftext($im, 12, 0, Y_AXIS_VALUES_XPOS, $z + Y_AXIS_VALUES_HALF_CHAR_HEIGHT, $gray, 'arial.ttf', $a);
    }
  }

  imageline($im, LEFTMARGIN, TOPLINE, LEFTMARGIN, BOTTOMLINE, $gray);
  imageline($im, RIGHTMARGIN, TOPLINE, RIGHTMARGIN, BOTTOMLINE, $gray);


  // draw the rest of the x-axis dates
  $current_year = $start_year;

  for ($i = $starty; $i <= $endery; $i = $i + 4)         //because I am calculating 6 times per year (day)
  {
    $x2 = LEFTMARGIN + ($XDIV * ($i - $starty));

    imageline($im, $x2, BOTTOMLINE, $x2, BOTTOMLINE + 4, $white);
    imageline($im, $x2, TOPLINE - 4, $x2, TOPLINE, $white);

    imageline($im, $x2, TOPLINE, $x2, BOTTOMLINE, $light_gray);        //vertical lines every 4 years along the x-axis

    imagettftext($im, 10, 0, $x2 - X_AXIS_VALUES_CHAR_WIDTH * 2, BOTTOMLINE + 20, $white, 'arial.ttf', sprintf("%'04d", $current_year));

    $current_year = $current_year + 4;
  }


  //draw lines on graph
  $xdiv_yr = $XDIV / 6;

  for ($j = $starty - 1; $j <= $years_to_run * 6 - 1; $j++)
  {
    $this_data = $declination2[$j];
    $next_data = $declination2[$j + 1];;

    $x1 = LEFTMARGIN + ($xdiv_yr * ($j - ($starty - 1)));
    $x2 = LEFTMARGIN + ($xdiv_yr * ($j + $starty));

    $y1 = BOTTOMLINE - TOP_TO_BOTTOM_DIFF * ($this_data - $LowY) / ($HighY - $LowY);

    if ($y1 < BOTTOMLINE - TOP_TO_BOTTOM_DIFF) { $y1 = BOTTOMLINE - TOP_TO_BOTTOM_DIFF; }

    if ($y1 >= BOTTOMLINE) { $y1 = BOTTOMLINE; }

    $y2 = BOTTOMLINE - TOP_TO_BOTTOM_DIFF * ($next_data - $LowY) / ($HighY - $LowY);

    if ($y2 < BOTTOMLINE - TOP_TO_BOTTOM_DIFF) { $y2 = BOTTOMLINE - TOP_TO_BOTTOM_DIFF; }

    if ($y2 >= BOTTOMLINE) { $y2 = BOTTOMLINE; }

    imageline($im, $x1, $y1, $x2, $y2, $clr_to_use);
  }

  // display color legend on graph, for this color
  imagettftext($im, 12, 0, 22, 24, $white, 'arial.ttf', "Progressed Moon Declinations for " . $line1);


  // draw the image in png format - using imagepng() results in clearer text compared with imagejpeg()
  imagepng($im);
  imagedestroy($im);
}

?>
