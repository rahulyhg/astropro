<?php
  session_start();

  include("event_constants.php");
  include("../constants.php");

  require_once ('../../../mysqli_connect_online_calcs_db_MYSQLI.php');

  $copyright1 = "This chartwheel is copyrighted";
  $copyright2 = "& generated at " . YOUR_URL;

  $line1 = safeEscapeString($conn, $_GET["l1"]);
  $line2 = safeEscapeString($conn, $_GET["l2"]);
  $line3 = safeEscapeString($conn, $_GET["l3"]);
  $line4 = safeEscapeString($conn, $_GET["l4"]);

  $retrograde = safeEscapeString($conn, $_GET["rx1"]);

  $extra_height_for_graphic_data_table = safeEscapeString($conn, $_GET["eh"]);

  $y_top_margin = safeEscapeString($conn, $_GET["ytm"]);

  $extra_width_for_graphic_data_table = safeEscapeString($conn, $_GET["ew"]);    //graphic is 792 pixels wide, total

  $longitude = $_SESSION['longitude1'];
  $speed = $_SESSION['speed1'];
  $hc = $_SESSION['hc1'];
  $house_pos = $_SESSION['house_pos1'];

  $longitude[LAST_PLANET + 1] = $hc[1];
  $longitude[LAST_PLANET + 2] = $hc[10];

  $Ascendant1 = $hc[1];
  $hc[13] = $hc[1];


// set the content-type
  header("Content-type: image/png");

// create the blank image
  $overall_size = 640;        //size of the wheel itself
  $im = @imagecreatetruecolor($overall_size + $extra_width_for_graphic_data_table, $overall_size + $y_top_margin + $extra_height_for_graphic_data_table) or die("Cannot initialize new GD image stream");

// specify the colors
  $black = imagecolorallocate($im, 0, 0, 0);
  $white = imagecolorallocate($im, 255, 255, 255);
  $red = imagecolorallocate($im, 255, 0, 0);
  $blue = imagecolorallocate($im, 0, 0, 255);
  $magenta = imagecolorallocate($im, 255, 0, 255);
  $light_yellow = imagecolorallocate($im, 255, 255, 232);
  $orange = imagecolorallocate($im, 255, 128, 64);
  $cyan = imagecolorallocate($im, 0, 255, 255);
  $green = imagecolorallocate($im, 0, 224, 0);
  $light_green = imagecolorallocate($im, 232, 255, 232);
  $another_green = imagecolorallocate($im, 0, 128, 0);
  $grey = imagecolorallocate($im, 142, 142, 142);
  $olive = imagecolorallocate($im, 204, 204, 109);
  $light_blue = imagecolorallocate($im, 240, 240, 255);
  $very_light_blue = imagecolorallocate($im, 217, 255, 255);
  $brown = imagecolorallocate($im, 148, 101, 77);
  $blue_2 = imagecolorallocate($im, 0, 0, 224);
  $green_2 = imagecolorallocate($im, 0, 192, 0);
  $grey_2 = imagecolorallocate($im, 153, 153, 153);
  $red_2 = imagecolorallocate($im, 224, 0, 0);
  $background_color = imagecolorallocate($im, 192, 208, 255);

// specific colors
  $planet_color = $black;   //was $cyan;
  $deg_min_color = $black;    //$white;
  $sign_color = $magenta;

  $asp_color_special[0] = $blue;
  $asp_color_special[1] = $green;
  $asp_color_special[2] = $red;
  $asp_color_special[3] = $green;
  $asp_color_special[4] = $red;

  $size_of_rect = $overall_size;    // size of rectangle in which to draw the wheel
  $diameter = 520;            // diameter of circle drawn
  $outer_outer_diameter = 600;      // diameter of circle drawn
  $outer_diameter_distance = ($outer_outer_diameter - $diameter) / 2; // distance between outer-outer diameter and diameter

  $inner_diameter_offset = 91;      // diameter of inner circle drawn
  $inner_diameter_offset_2 = 91;    // diameter of nextmost inner circle drawn
  $dist_from_diameter1 = 32;      // distance inner planet glyph is from circumference of wheel

  $radius = $diameter / 2;        // radius of circle drawn
  $middle_radius = ($outer_outer_diameter + $diameter) / 4 - 0;   //the radius for the middle of the two outer circles

  $center_pt_x = $extra_width_for_graphic_data_table - 10 + ($size_of_rect / 2) + 20;       // center of circle
  $center_pt_y = 5 + $y_top_margin + ($size_of_rect / 2);   // center of circle

  $last_planet_num = LAST_PLANET;       //add a planet
  $num_planets = $last_planet_num + 1;
  $spacing = 5;     // spacing between planet glyphs around wheel - this number is really one more than shown here

// ------------------------------------------

// create colored rectangle on blank image
  imagefilledrectangle($im, 0, 0, $size_of_rect + $extra_width_for_graphic_data_table, $size_of_rect + $y_top_margin + $extra_height_for_graphic_data_table, $background_color);

// MUST BE HERE - I DO NOT KNOW WHY - MAYBE TO PRIME THE PUMP
  imagettftext($im, 11, 0, 0, 0, $black, 'arial.ttf', " ");


// ------------------------------------------

// draw the outer-outer circle of the chartwheel
  imagefilledellipse($im, $center_pt_x, $center_pt_y, $outer_outer_diameter, $outer_outer_diameter, $white);
  imageellipse($im, $center_pt_x, $center_pt_y, $outer_outer_diameter, $outer_outer_diameter, $black);

// draw the outer circle of the chartwheel
  imagefilledellipse($im, $center_pt_x, $center_pt_y, $diameter, $diameter, $white);
  imageellipse($im, $center_pt_x, $center_pt_y, $diameter, $diameter, $grey); //was red


// draw the outer-outer border of the chartwheel
  imagefilledellipse($im, $center_pt_x, $center_pt_y, $diameter - ($inner_diameter_offset * 2) - 1, $diameter - ($inner_diameter_offset * 2) - 1, $light_green);  //13 Nov 2010


//shade the areas of complete signs, alternating colors - do not move this code from here
  for ($i = 0; $i <= 330; $i = $i + 30)
  {
    $angle = $i + sprintf("%.0f", $Ascendant1);

    if ($flag == True)
    {
      imagefilledarc($im, $center_pt_x, $center_pt_y, $diameter - ($inner_diameter_offset * 2) - 1, $diameter - ($inner_diameter_offset * 2) - 1, $angle, $angle + 30, $light_blue, IMG_ARC_PIE); //13 Nov 2010
    }
    else
    {
      imagefilledarc($im, $center_pt_x, $center_pt_y, $diameter - ($inner_diameter_offset * 2) - 1, $diameter - ($inner_diameter_offset * 2) - 1, $angle, $angle + 30, $light_blue, IMG_ARC_PIE); //13 Nov 2010
    }

    $flag = !$flag;
  }


// draw the inner circle of the chartwheel
  imagefilledellipse($im, $center_pt_x, $center_pt_y, $diameter - ($inner_diameter_offset * 2) - 40, $diameter - ($inner_diameter_offset * 2) - 40, $white);  //13 Nov 2010

  imageellipse($im, $center_pt_x, $center_pt_y, $diameter - ($inner_diameter_offset_2 * 2), $diameter - ($inner_diameter_offset_2 * 2), $black);
  imageellipse($im, $center_pt_x, $center_pt_y, $diameter - ($inner_diameter_offset_2 * 2) - 40, $diameter - ($inner_diameter_offset_2 * 2) - 40, $black);      //13 Nov 2010


// ------------------------------------------

//data for chart
  drawboldtext($im, 10, 0, 2, 10, $black, 'arial.ttf', $line1, 0);
  drawboldtext($im, 10, 0, 2, 26, $black, 'arial.ttf', $line2, 0);
  drawboldtext($im, 10, 0, 2, 42, $black, 'arial.ttf', $line3, 0);
  drawboldtext($im, 10, 0, 2, 58, $black, 'arial.ttf', $line4, 0);

  drawboldtext($im, 11, 0, 555, 42, $black, 'arial.ttf', $copyright1, 0);
  drawboldtext($im, 11, 0, 555, 58, $black, 'arial.ttf', $copyright2, 0);

  imageline($im, 0, 64, $overall_size + $extra_width_for_graphic_data_table, 64, $olive);   //border

// ------------------------------------------

//draw the horizontal line for the Ascendant
  $x1 = -($radius - $inner_diameter_offset) * cos(deg2rad(0));
  $y1 = -($radius - $inner_diameter_offset) * sin(deg2rad(0));

  $x2 = -$radius * cos(deg2rad(0));
  $y2 = -$radius * sin(deg2rad(0));

  imageline($im, $x1 + $center_pt_x, $y1 + $center_pt_y, $x2 + $center_pt_x, $y2 + $center_pt_y, $black);

//draw the arrow for the Ascendant
  $x1 = -$radius;
  $y1 = 30 * sin(deg2rad(0));

  $x2 = -($radius - 12);
  $y2 = 12 * sin(deg2rad(-15));
  imageline($im, $x1 + $center_pt_x, $y1 + $center_pt_y, $x2 + $center_pt_x, $y2 + $center_pt_y, $black);

  $y2 = 12 * sin(deg2rad(15));
  imageline($im, $x1 + $center_pt_x, $y1 + $center_pt_y, $x2 + $center_pt_x, $y2 + $center_pt_y, $black);

// ------------------------------------------

// draw in the actual house cusp numbers and sign
  for ($i = 1; $i <= 12; $i = $i + 1)
  {
    $angle = -($Ascendant1 - $hc[$i]);

    $sign_pos = floor($hc[$i] / 30) + 1;

    if ($sign_pos == 1 Or $sign_pos == 5 Or $sign_pos == 9)
    {
      $clr_to_use = $red;
    }
    elseif ($sign_pos == 2 Or $sign_pos == 6 Or $sign_pos == 10)
    {
      $clr_to_use = $another_green;
    }
    elseif ($sign_pos == 3 Or $sign_pos == 7 Or $sign_pos == 11)
    {
      $clr_to_use = $orange;
    }
    elseif ($sign_pos == 4 Or $sign_pos == 8 Or $sign_pos == 12)
    {
      $clr_to_use = $blue;
    }

    // sign glyph
    display_house_cusp($i, $angle, $middle_radius, $xy);
    imagettftext($im, 16, 0, $xy[0] + $center_pt_x, $xy[1] + $center_pt_y, $clr_to_use, 'HamburgSymbols.ttf', chr($sign_glyph[$sign_pos])); //was 14

    if ($i >= 1 And $i <= 6)
    {
      display_house_cusp($i, $angle - 5, $middle_radius, $xy);
    }
    else
    {
      display_house_cusp($i, $angle + 6, $middle_radius, $xy);
    }

    $reduced_pos = Reduce_below_30($hc[$i]);
    $int_reduced_pos = floor($reduced_pos);

    if ($int_reduced_pos < 10)
    {
      $t = "0" . $int_reduced_pos;
    }
    else
    {
      $t = $int_reduced_pos;
    }

    imagettftext($im, 10, 0, $xy[0] + $center_pt_x, $xy[1] + $center_pt_y, $black, 'arial.ttf', $t . chr(176)); //was 8

    // house cusp minute
    if ($i >= 1 And $i <= 4)
    {
      display_house_cusp($i, $angle + 5, $middle_radius, $xy);
    }
    elseif ($i == 5 Or $i == 6)
    {
      display_house_cusp($i, $angle + 6, $middle_radius, $xy);
    }
    elseif ($i == 7)
    {
      display_house_cusp($i, $angle - 5, $middle_radius, $xy);
    }
    else
    {
      display_house_cusp($i, $angle - 6, $middle_radius, $xy);
    }

    $reduced_pos = Reduce_below_30($hc[$i]);
    $int_reduced_pos = floor(60 * ($reduced_pos - floor($reduced_pos)));

    if ($int_reduced_pos < 10)
    {
      $t = "0" . $int_reduced_pos;
    }
    else
    {
      $t = $int_reduced_pos;
    }

    imagettftext($im, 9, 0, $xy[0] + $center_pt_x, $xy[1] + $center_pt_y, $black, 'arial.ttf', $t . " " . chr(39)); //was 8
  }

// ------------------------------------------

// draw the lines for the house cusps
  for ($i = 1; $i <= 12; $i = $i + 1)
  {

    $angle = $Ascendant1 - $hc[$i];

    $x1 = -$radius * cos(deg2rad($angle));
    $y1 = -$radius * sin(deg2rad($angle));

    $x2 = -($radius - $inner_diameter_offset) * cos(deg2rad($angle));
    $y2 = -($radius - $inner_diameter_offset) * sin(deg2rad($angle));

    if ($i != 1 And $i != 10)
    {
      imageline($im, $x1 + $center_pt_x, $y1 + $center_pt_y, $x2 + $center_pt_x, $y2 + $center_pt_y, $another_green);
    }

    // display the house numbers themselves - 26 March 2010
    $angle_diff = $hc[$i + 1] - $hc[$i];
    if ($angle_diff < -180) { $angle_diff = $angle_diff + 360; }

    $angle_to_use = $angle_sum + ($angle_diff / 2);

    //display_house_number_new($i, $angle_to_use, $radius - $inner_diameter_offset, $xy);
    display_house_number_new($i, $angle_to_use, $radius, $xy);

    // display the house numbers themselves
    imagettftext($im, 10, 0, $xy[0] + $center_pt_x, $xy[1] + $center_pt_y, $another_green, 'arial.ttf', $i); //was 8

    $angle_sum = $angle_sum + $angle_diff;    //26 March 2010
  }

// ------------------------------------------

// draw the little degree marks around of the wheel
  $spoke_length = 9;
  $minor_spoke_length = 4;

  for ($i = 0; $i <= 359; $i = $i + 1)
  {
    $angle = $i + $Ascendant1;

    $x1 = -$radius * cos(deg2rad($angle));
    $y1 = -$radius * sin(deg2rad($angle));

    if ($i % 5 == 0)
    {
      $x2 = -($radius - $spoke_length) * cos(deg2rad($angle));
      $y2 = -($radius - $spoke_length) * sin(deg2rad($angle));
      imageline($im, $x1 + $center_pt_x, $y1 + $center_pt_y, $x2 + $center_pt_x, $y2 + $center_pt_y, $red);
    }
    else
    {
      $x2 = -($radius - $minor_spoke_length) * cos(deg2rad($angle));
      $y2 = -($radius - $minor_spoke_length) * sin(deg2rad($angle));
      imageline($im, $x1 + $center_pt_x, $y1 + $center_pt_y, $x2 + $center_pt_x, $y2 + $center_pt_y, $black);
    }
  }


// ------------------------------------------



// draw the near-vertical line for the MC
  $angle = $Ascendant1 - $hc[10];
  $dist_mc_asc = $angle;

  if ($dist_mc_asc < 0)
  {
    $dist_mc_asc = $dist_mc_asc + 360;
  }

  $value = 90 - $dist_mc_asc;
  $angle1 = 65 - $value;
  $angle2 = 65 + $value;

  $x1 = -($radius - $inner_diameter_offset) * cos(deg2rad($angle));
  $y1 = -($radius - $inner_diameter_offset) * sin(deg2rad($angle));

  $x2 = -$radius * cos(deg2rad($angle));
  $y2 = -$radius * sin(deg2rad($angle));

  imageline($im, $x1 + $center_pt_x, $y1 + $center_pt_y, $x2 + $center_pt_x, $y2 + $center_pt_y, $black);

// draw the arrow for the 10th house cusp (MC)
  $x1 = $x2 + (15 * cos(deg2rad($angle1)));
  $y1 = $y2 + (15 * sin(deg2rad($angle1)));
  imageline($im, $x1 + $center_pt_x, $y1 + $center_pt_y, $x2 + $center_pt_x, $y2 + $center_pt_y, $black);

  $x1 = $x2 - (15 * cos(deg2rad($angle2)));
  $y1 = $y2 + (15 * sin(deg2rad($angle2)));
  imageline($im, $x1 + $center_pt_x, $y1 + $center_pt_y, $x2 + $center_pt_x, $y2 + $center_pt_y, $black);

// ------------------------------------------


// draw the dividing lines between the signs
  for ($i = 0; $i <= 330; $i = $i + 30)
  {
    $angle = $i + sprintf("%.0f", $Ascendant1);

    $x1 = -(150) * cos(deg2rad($angle));    //13 Nov 2010 - was 320
    $y1 = -(150) * sin(deg2rad($angle));

    $x2 = -(168) * cos(deg2rad($angle));    //13 Nov 2010 - was 300
    $y2 = -(168) * sin(deg2rad($angle));

    imageline($im, $x1 + $center_pt_x, $y1 + $center_pt_y, $x2 + $center_pt_x, $y2 + $center_pt_y, $red);
  }


// put signs around chartwheel
  $cw_sign_glyph = 14;
  $ch_sign_glyph = 12;
  $gap_sign_glyph = -44;

  for ($i = 1; $i <= 12; $i++)
  {
    $angle_to_use = deg2rad((($i - 1) * 30) + 15 - $Ascendant1);

    $center_pos_x = -$cw_sign_glyph / 2;
    $center_pos_y = $ch_sign_glyph / 2;

    $offset_pos_x = $center_pos_x * cos($angle_to_use);
    $offset_pos_y = $center_pos_y * sin($angle_to_use);

    $x1 = $center_pos_x + $offset_pos_x + ((-108 + $gap_sign_glyph) * cos($angle_to_use));    //13 Nov 2010 - was 260 instead of 108
    $y1 = $center_pos_y + $offset_pos_y + ((108 - $gap_sign_glyph) * sin($angle_to_use));

    if ($i == 1 Or $i == 5 Or $i == 9)
    {
      $clr_to_use = $red;
    }
    elseif ($i == 2 Or $i == 6 Or $i == 10)
    {
      $clr_to_use = $another_green;
    }
    elseif ($i == 3 Or $i == 7 Or $i == 11)
    {
      $clr_to_use = $orange;
    }
    elseif ($i == 4 Or $i == 8 Or $i == 12)
    {
      $clr_to_use = $blue;
    }

    drawboldtext($im, 10, 0, $x1 + $center_pt_x, $y1 + $center_pt_y, $clr_to_use, 'HamburgSymbols.ttf', chr($sign_glyph[$i]), 0); //used to be boldness of 1 - last number
  }

// ------------------------------------------

// put planets in chartwheel
  // sort longitudes in descending order from 360 down to 0
  Sort_planets_by_descending_longitude($num_planets, $longitude, $sort, $sort_pos);

  // count how many planets are in each house
  Count_planets_in_each_house($num_planets, $house_pos, $sort_pos, $sort, $nopih, $home);

  //find the best planet to start displaying the wheel and reorder the $sort[] array starting with that planet
  Find_best_planet_to_start_with($num_planets, $house_pos, $sort_pos, $sort, $nopih);


  for ($i = 0; $i <= 359; $i++) { $spot_filled[$i] = 0; }

  $house_num = 0;

  // add planet glyphs around circle
  for ($i = $num_planets - 1; $i >= 0; $i--)
  {
    // $sort() holds longitudes in descending order from 360 down to 0
    // $sort_pos() holds the planet number corresponding to that longitude
    $temp = $house_num;
    $house_num = $house_pos[$sort_pos[$i]];              // get the house this planet is in

    if ($temp != $house_num)
    {
      // this planet is in a different house than the last one - this planet is the first one in this house, in other words
      $planets_done = 1;
    }

    // get index for this planet as to where it should be in the possible xx different positions around the wheel
    $from_cusp = Crunch($sort[$i] - $hc[$house_num]);
    $to_next_cusp = Crunch($hc[$house_num + 1] - $sort[$i]);
    $next_cusp = $hc[$house_num + 1];

    $angle = $sort[$i];
    $how_many_more_can_fit_in_this_house = floor($to_next_cusp / ($spacing + 1));

    //if ($nopih[$house_num] - $planets_done > $how_many_more_can_fit_in_this_house)
    if ($nopih[$house_num] - $planets_done >= $how_many_more_can_fit_in_this_house)
    {
      // problem - adjust this planet backwards so others can fit
      $angle = Crunch($next_cusp - (($nopih[$house_num] - $planets_done + 1) * ($spacing + 1)));
    }

    while (Check_for_overlap($angle, $spot_filled, $spacing) == True)
    {
      $angle = $angle + 1;
    }

    // mark this position as being filled
    $spot_filled[round($angle)] = 1;
    $spot_filled[Crunch(round($angle) - 1)] = 1;  // allows for a little better separation between Mars and Sun on 3/13/1966 test example

    // take the above index and convert it into an angle
    $planet_angle[$sort_pos[$i]] = $angle;              // needed for aspect lines
    $angle_to_use = Crunch($angle - $Ascendant1);       // needed for placing info on chartwheel
    $our_angle = $angle_to_use;                     // in degrees

    $angle_to_use = deg2rad($angle_to_use);

    // denote that we have done at least one planet in this house (actually count the planets in this house that we have done)
    $planets_done++;

    display_planet_glyph($our_angle, $angle_to_use, $radius - $dist_from_diameter1, $xy, 0);
    imagettftext($im, 16, 0, $xy[0] + $center_pt_x, $xy[1] + $center_pt_y, $planet_color, 'HamburgSymbols.ttf', chr($pl_glyph[$sort_pos[$i]])); //was 14

    // display degrees of longitude for each planet
    $reduced_pos = Reduce_below_30($sort[$i]);
    $int_reduced_pos = floor($reduced_pos);
    if ($int_reduced_pos < 10)
    {
      $t = "0" . $int_reduced_pos;
    }
    else
    {
      $t = $int_reduced_pos;
    }

    display_planet_glyph($our_angle, $angle_to_use, $radius - $dist_from_diameter1 - 18, $xy, 1);
    imagettftext($im, 9, 0, $xy[0] + $center_pt_x, $xy[1] + $center_pt_y, $planet_color, 'arial.ttf', $t . chr(176));

    // display planet sign
    $sign_pos = floor($sort[$i] / 30) + 1;
    display_planet_glyph($our_angle, $angle_to_use, $radius - $dist_from_diameter1 - 34, $xy, 2);
    if ($sign_pos == 1 Or $sign_pos == 5 Or $sign_pos == 9)
    {
      $clr_to_use = $red;
    }
    elseif ($sign_pos == 2 Or $sign_pos == 6 Or $sign_pos == 10)
    {
      $clr_to_use = $another_green;
    }
    elseif ($sign_pos == 3 Or $sign_pos == 7 Or $sign_pos == 11)
    {
      $clr_to_use = $orange;
    }
    elseif ($sign_pos == 4 Or $sign_pos == 8 Or $sign_pos == 12)
    {
      $clr_to_use = $blue;
    }
    imagettftext($im, 9, 0, $xy[0] + $center_pt_x, $xy[1] + $center_pt_y, $clr_to_use, 'HamburgSymbols.ttf', chr($sign_glyph[$sign_pos]));

    // display minutes of longitude for each planet
    $int_reduced_pos = floor(60 * ($reduced_pos - floor($reduced_pos)));
    if ($int_reduced_pos < 10)
    {
      $t = "0" . $int_reduced_pos;
    }
    else
    {
      $t = $int_reduced_pos;
    }
    display_planet_glyph($our_angle, $angle_to_use, $radius - $dist_from_diameter1 - 52, $xy, 1);
    //imagettftext($im, 8, 0, $xy[0] + $center_pt_x, $xy[1] + $center_pt_y, $planet_color, 'arial.ttf', $t . " " . chr(39));
    imagettftext($im, 9, 0, $xy[0] + $center_pt_x, $xy[1] + $center_pt_y, $planet_color, 'arial.ttf', $t);

    // display Rx symbol
    if ($retrograde[$sort_pos[$i]] == "R")
    {
      display_planet_glyph($our_angle, $angle_to_use, $radius - $dist_from_diameter1 - 65, $xy, 3);
      imagettftext($im, 8, 0, $xy[0] + $center_pt_x, $xy[1] + $center_pt_y, $red, 'HamburgSymbols.ttf', chr(62));
    }
  }

// ------------------------------------------


// draw the aspect lines
  for ($i = 0; $i <= LAST_PLANET - 1; $i++)
  {
    for ($j = $i + 1; $j <= LAST_PLANET; $j++)
    {
      if ($sort_pos[$i] > SE_SATURN Or $sort_pos[$j] > SE_SATURN) { continue; }

      $q = -99;
      $da = abs($longitude[$sort_pos[$i]] - $longitude[$sort_pos[$j]]);

      $orb = $moiety[$sort_pos[$i]] + $moiety[$sort_pos[$j]];

      // is there an aspect within orb?
      for ($k = 0; $k <= 4; $k++)
      {
        if ((($da <= $exact_aspect_special[$k] + $orb) And ($da >= $exact_aspect_special[$k] - $orb)) Or (($da <= 360 - $exact_aspect_special[$k] + $orb) And ($da >= 360 - $exact_aspect_special[$k] - $orb)))
        {
          $q = $k;
          break;
        }
      }

      if ($q >= 0)
      {
        if ($q != 0)
        {
          //non-conjunctions
          $x1 = (-$radius + 111) * cos(deg2rad($planet_angle[$sort_pos[$i]] - $Ascendant1));      //13 Nov 2010 - the 111 was 91 previously, as per above
          $y1 = ($radius - 111) * sin(deg2rad($planet_angle[$sort_pos[$i]] - $Ascendant1));
          $x2 = (-$radius + 111) * cos(deg2rad($planet_angle[$sort_pos[$j]] - $Ascendant1));
          $y2 = ($radius - 111) * sin(deg2rad($planet_angle[$sort_pos[$j]] - $Ascendant1));

          //set image style
          $style = array($asp_color_special[$q], $asp_color_special[$q], $asp_color_special[$q], $asp_color_special[$q]);

          imagesetstyle($im, $style);
          imageline($im, $x1 + $center_pt_x, $y1 + $center_pt_y, $x2 + $center_pt_x, $y2 + $center_pt_y, IMG_COLOR_STYLED);

          //draw in aspect glyph
          drawboldtext($im, 15, 0, (($x1 + $x2) / 2) - 7 + $center_pt_x, (($y1 + $y2) / 2) + 7 + $center_pt_y, $asp_color_special[$q], 'HamburgSymbols.ttf', chr($asp_glyph_special[$q]), 0); //thicker
        }
      }
    }
  }





//start of planet listing - list all horary data
  $left_margin = 6;
  $top_of_planet_listing = $y_top_margin + 650;

  $y_spacing = 20;
  $cnt = 0;

  imageline($im, $left_margin, $top_of_planet_listing, $overall_size + $extra_width_for_graphic_data_table - $left_margin, $top_of_planet_listing, $black);       //horizontal line above table - was $olive
  imageline($im, $left_margin, $top_of_planet_listing, $left_margin, $top_of_planet_listing + $extra_height_for_graphic_data_table - 30, $black);   //left side line
  imageline($im, $overall_size + $extra_width_for_graphic_data_table - $left_margin, $top_of_planet_listing, $overall_size + $extra_width_for_graphic_data_table - $left_margin, $top_of_planet_listing + $extra_height_for_graphic_data_table - 30, $black);   //right side line
  imageline($im, $left_margin, $top_of_planet_listing + $extra_height_for_graphic_data_table - 30, $overall_size + $extra_width_for_graphic_data_table - $left_margin, $top_of_planet_listing + $extra_height_for_graphic_data_table - 30, $black);   //horizontal line below table

  imageline($im, $left_margin, $top_of_planet_listing + 24, $overall_size + $extra_width_for_graphic_data_table - $left_margin, $top_of_planet_listing + 24, $black);   //horizontal line under the headings


//display header line
  $top_of_planet_listing = $y_top_margin + 20 + 650;
  imagettftext($im, 9, 0, $left_margin + 22, $top_of_planet_listing + ($cnt * $y_spacing) - 3, $blue, 'arial.ttf', "Longitude    Ruler     Exlt      Trip                                  Detr     Fall                                Antiscia       Contra                               Velocity       Spd    Peregr.");


  $top_of_planet_listing = $y_top_margin + 40 + 650;

  $peregrine = array();

  for ($i = 0; $i <= LAST_PLANET; $i++)
  {
    $peregrine[$i] = 1;
  }

  for ($i = 0; $i <= LAST_PLANET; $i++)
  {
    $sign_pos = floor($longitude[$i] / 30) + 1;
    if ($sign_pos == 1 Or $sign_pos == 5 Or $sign_pos == 9)
    {
      $clr_to_use = $red;
    }
    elseif ($sign_pos == 2 Or $sign_pos == 6 Or $sign_pos == 10)
    {
      $clr_to_use = $another_green;
    }
    elseif ($sign_pos == 3 Or $sign_pos == 7 Or $sign_pos == 11)
    {
      $clr_to_use = $orange;
    }
    elseif ($sign_pos == 4 Or $sign_pos == 8 Or $sign_pos == 12)
    {
      $clr_to_use = $blue;
    }

//display table of planet positions, etc.
    drawboldtext($im, 14, 0, $left_margin + 3, $top_of_planet_listing + ($cnt * $y_spacing), $black, 'HamburgSymbols.ttf', chr($pl_glyph[$i]), 0);              //draw planet glyph


    // display planet data in the right-hand table
    imagettftext($im, 10, 0, $left_margin + 25, $top_of_planet_listing + ($cnt * $y_spacing), $black, 'arial.ttf', Convert_Longitude1($longitude[$i]));           //display longitude degrees
    imagettftext($im, 12, 0, $left_margin + 42, $top_of_planet_listing + ($cnt * $y_spacing), $clr_to_use, 'HamburgSymbols.ttf', chr($sign_glyph[$sign_pos]));        //display sign
    imagettftext($im, 10, 0, $left_margin + 58, $top_of_planet_listing + ($cnt * $y_spacing), $black, 'arial.ttf', Convert_Longitude2($longitude[$i]) . $retrograde[$i]); //display longitude minutes and Rx


//find ruler of this planet's sign
  if ($sign_pos == 1 Or $sign_pos == 8)
  {
    $ruler = SE_MARS;
  }
  elseif ($sign_pos == 2 Or $sign_pos == 7)
  {
    $ruler = SE_VENUS;
  }
  elseif ($sign_pos == 3 Or $sign_pos == 6)
  {
    $ruler = SE_MERCURY;
  }
  elseif ($sign_pos == 4)
  {
    $ruler = SE_MOON;
  }
  elseif ($sign_pos == 5)
  {
    $ruler = SE_SUN;
  }
  elseif ($sign_pos == 9 Or $sign_pos == 12)
  {
    $ruler = SE_JUPITER;
  }
  elseif ($sign_pos == 10 Or $sign_pos == 11)
  {
    $ruler = SE_SATURN;
  }

    drawboldtext($im, 14, 0, $left_margin + 95, $top_of_planet_listing + ($cnt * $y_spacing), $black, 'HamburgSymbols.ttf', chr($pl_glyph[$ruler]), 0);                 //draw planet glyph
    if ($i == $ruler)
    {
      imagettftext($im, 10, 0, $left_margin + 110, $top_of_planet_listing + ($cnt * $y_spacing), $black, 'arial.ttf', "+");                             //mark a "+"
      $peregrine[$i] = 0;
    }


//find exaltation of this planet's sign
  $exalt = -1;

  if ($sign_pos == 1)
  {
    $exalt = SE_SUN;
  }
  elseif ($sign_pos == 2)
  {
    $exalt = SE_MOON;
  }
  elseif ($sign_pos == 4)
  {
    $exalt = SE_JUPITER;
  }
  elseif ($sign_pos == 6)
  {
    $exalt = SE_MERCURY;
  }
  elseif ($sign_pos == 7)
  {
    $exalt = SE_SATURN;
  }
  elseif ($sign_pos == 10)
  {
    $exalt = SE_MARS;
  }
  elseif ($sign_pos == 12)
  {
    $exalt = SE_VENUS;
  }

    if ($exalt >= 0) { drawboldtext($im, 14, 0, $left_margin + 135, $top_of_planet_listing + ($cnt * $y_spacing), $black, 'HamburgSymbols.ttf', chr($pl_glyph[$exalt]), 0); }     //draw planet glyph

    if ($i == $exalt)
    {
      imagettftext($im, 10, 0, $left_margin + 150, $top_of_planet_listing + ($cnt * $y_spacing), $black, 'arial.ttf', "+");                             //mark a "+"
      $peregrine[$i] = 0;
    }


//find triplicity of this planet's sign
  if ($house_pos[SE_SUN] < 7)
  {
    //night chart
    if ($sign_pos == 1 Or $sign_pos == 5 Or $sign_pos == 9)
    {
      $triplicity = SE_JUPITER;
    }
    elseif ($sign_pos == 2 Or $sign_pos == 6 Or $sign_pos == 10)
    {
      $triplicity = SE_MOON;
    }
    elseif ($sign_pos == 3 Or $sign_pos == 7 Or $sign_pos == 11)
    {
      $triplicity = SE_MERCURY;
    }
    elseif ($sign_pos == 4 Or $sign_pos == 8 Or $sign_pos == 12)
    {
      $triplicity = SE_MARS;
    }
    }
    else
    {
      //day chart
    if ($sign_pos == 1 Or $sign_pos == 5 Or $sign_pos == 9)
    {
      $triplicity = SE_SUN;
    }
    elseif ($sign_pos == 2 Or $sign_pos == 6 Or $sign_pos == 10)
    {
      $triplicity = SE_VENUS;
    }
    elseif ($sign_pos == 3 Or $sign_pos == 7 Or $sign_pos == 11)
    {
      $triplicity = SE_SATURN;
    }
    elseif ($sign_pos == 4 Or $sign_pos == 8 Or $sign_pos == 12)
    {
      $triplicity = SE_MARS;
    }
    }

    drawboldtext($im, 12, 0, $left_margin + 175, $top_of_planet_listing + ($cnt * $y_spacing), $black, 'HamburgSymbols.ttf', chr($pl_glyph[$triplicity]), 0);               //draw planet glyph
    if ($i == $triplicity)
    {
      imagettftext($im, 8, 0, $left_margin + 190, $top_of_planet_listing + ($cnt * $y_spacing), $black, 'arial.ttf', "+");                              //mark a "+"
      $peregrine[$i] = 0;
    }


//find term of this planet's sign
  if ($longitude[$i] >= 0 And $longitude[$i] < 6) { $term = SE_JUPITER; }
  if ($longitude[$i] >= 6 And $longitude[$i] < 14) { $term = SE_VENUS; }
  if ($longitude[$i] >= 14 And $longitude[$i] < 21) { $term = SE_MERCURY; }
  if ($longitude[$i] >= 21 And $longitude[$i] < 26) { $term = SE_MARS; }
  if ($longitude[$i] >= 26 And $longitude[$i] < 30) { $term = SE_SATURN; }
  if ($longitude[$i] >= 30 And $longitude[$i] < 38) { $term = SE_VENUS; }
  if ($longitude[$i] >= 38 And $longitude[$i] < 45) { $term = SE_MERCURY; }
  if ($longitude[$i] >= 45 And $longitude[$i] < 52) { $term = SE_JUPITER; }
  if ($longitude[$i] >= 52 And $longitude[$i] < 56) { $term = SE_SATURN; }
  if ($longitude[$i] >= 56 And $longitude[$i] < 60) { $term = SE_MARS; }
  if ($longitude[$i] >= 60 And $longitude[$i] < 67) { $term = SE_MERCURY; }
  if ($longitude[$i] >= 67 And $longitude[$i] < 74) { $term = SE_JUPITER; }
  if ($longitude[$i] >= 74 And $longitude[$i] < 81) { $term = SE_VENUS; }
  if ($longitude[$i] >= 81 And $longitude[$i] < 85) { $term = SE_SATURN; }
  if ($longitude[$i] >= 85 And $longitude[$i] < 90) { $term = SE_MARS; }
  if ($longitude[$i] >= 90 And $longitude[$i] < 96) { $term = SE_MARS; }
  if ($longitude[$i] >= 96 And $longitude[$i] < 103) { $term = SE_JUPITER; }
  if ($longitude[$i] >= 103 And $longitude[$i] < 110) { $term = SE_MERCURY; }
  if ($longitude[$i] >= 110 And $longitude[$i] < 117) { $term = SE_VENUS; }
  if ($longitude[$i] >= 117 And $longitude[$i] < 120) { $term = SE_SATURN; }
  if ($longitude[$i] >= 120 And $longitude[$i] < 126) { $term = SE_SATURN; }
  if ($longitude[$i] >= 126 And $longitude[$i] < 133) { $term = SE_MERCURY; }
  if ($longitude[$i] >= 133 And $longitude[$i] < 139) { $term = SE_VENUS; }
  if ($longitude[$i] >= 139 And $longitude[$i] < 145) { $term = SE_JUPITER; }
  if ($longitude[$i] >= 145 And $longitude[$i] < 150) { $term = SE_MARS; }
  if ($longitude[$i] >= 150 And $longitude[$i] < 157) { $term = SE_MERCURY; }
  if ($longitude[$i] >= 157 And $longitude[$i] < 163) { $term = SE_VENUS; }
  if ($longitude[$i] >= 163 And $longitude[$i] < 168) { $term = SE_JUPITER; }
  if ($longitude[$i] >= 168 And $longitude[$i] < 174) { $term = SE_SATURN; }
  if ($longitude[$i] >= 174 And $longitude[$i] < 180) { $term = SE_MARS; }
  if ($longitude[$i] >= 180 And $longitude[$i] < 186) { $term = SE_SATURN; }
  if ($longitude[$i] >= 186 And $longitude[$i] < 191) { $term = SE_VENUS; }
  if ($longitude[$i] >= 191 And $longitude[$i] < 199) { $term = SE_JUPITER; }
  if ($longitude[$i] >= 199 And $longitude[$i] < 204) { $term = SE_MERCURY; }
  if ($longitude[$i] >= 204 And $longitude[$i] < 210) { $term = SE_MARS; }
  if ($longitude[$i] >= 210 And $longitude[$i] < 216) { $term = SE_MARS; }
  if ($longitude[$i] >= 216 And $longitude[$i] < 224) { $term = SE_JUPITER; }
  if ($longitude[$i] >= 224 And $longitude[$i] < 231) { $term = SE_VENUS; }
  if ($longitude[$i] >= 231 And $longitude[$i] < 237) { $term = SE_MERCURY; }
  if ($longitude[$i] >= 237 And $longitude[$i] < 240) { $term = SE_SATURN; }
  if ($longitude[$i] >= 240 And $longitude[$i] < 248) { $term = SE_JUPITER; }
  if ($longitude[$i] >= 248 And $longitude[$i] < 254) { $term = SE_VENUS; }
  if ($longitude[$i] >= 254 And $longitude[$i] < 259) { $term = SE_MERCURY; }
  if ($longitude[$i] >= 259 And $longitude[$i] < 265) { $term = SE_SATURN; }
  if ($longitude[$i] >= 265 And $longitude[$i] < 270) { $term = SE_MARS; }
  if ($longitude[$i] >= 270 And $longitude[$i] < 276) { $term = SE_VENUS; }
  if ($longitude[$i] >= 276 And $longitude[$i] < 282) { $term = SE_MERCURY; }
  if ($longitude[$i] >= 282 And $longitude[$i] < 289) { $term = SE_JUPITER; }
  if ($longitude[$i] >= 289 And $longitude[$i] < 295) { $term = SE_MARS; }
  if ($longitude[$i] >= 295 And $longitude[$i] < 300) { $term = SE_SATURN; }
  if ($longitude[$i] >= 300 And $longitude[$i] < 306) { $term = SE_SATURN; }
  if ($longitude[$i] >= 306 And $longitude[$i] < 312) { $term = SE_MERCURY; }
  if ($longitude[$i] >= 312 And $longitude[$i] < 320) { $term = SE_VENUS; }
  if ($longitude[$i] >= 320 And $longitude[$i] < 325) { $term = SE_JUPITER; }
  if ($longitude[$i] >= 325 And $longitude[$i] < 330) { $term = SE_MARS; }
  if ($longitude[$i] >= 330 And $longitude[$i] < 338) { $term = SE_VENUS; }
  if ($longitude[$i] >= 338 And $longitude[$i] < 344) { $term = SE_JUPITER; }
  if ($longitude[$i] >= 344 And $longitude[$i] < 350) { $term = SE_MERCURY; }
  if ($longitude[$i] >= 350 And $longitude[$i] < 356) { $term = SE_MARS; }
  if ($longitude[$i] >= 356 And $longitude[$i] < 360) { $term = SE_SATURN; }

  if ($i == $term) { $peregrine[$i] = 0; }


//find face of this planet's sign
  $t = $longitude[$i];

    while ($t >= 70)
    {
      $t = $t - 70;
    }

  if ($t >= 0 And $t < 10)
  {
    $face = SE_MARS;
  }
  elseif ($t >= 10 And $t < 20)
  {
    $face = SE_SUN;
  }
  elseif ($t >= 20 And $t < 30)
  {
    $face = SE_VENUS;
  }
  elseif ($t >= 30 And $t < 40)
  {
    $face = SE_MERCURY;
  }
  elseif ($t >= 40 And $t < 50)
  {
    $face = SE_MOON;
  }
  elseif ($t >= 50 And $t < 60)
  {
    $face = SE_SATURN;
  }
  elseif ($t >= 60 And $t < 70)
  {
    $face = SE_JUPITER;
  }

    if ($i == $face) { $peregrine[$i] = 0; }


   $_SESSION['peregrine'] = $peregrine;


//find detriment of this planet's sign
  if ($sign_pos == 7 Or $sign_pos == 2)
  {
    $detriment = SE_MARS;
  }
  elseif ($sign_pos == 8 Or $sign_pos == 1)
  {
    $detriment = SE_VENUS;
  }
  elseif ($sign_pos == 9 Or $sign_pos == 12)
  {
    $detriment = SE_MERCURY;
  }
  elseif ($sign_pos == 10)
  {
    $detriment = SE_MOON;
  }
  elseif ($sign_pos == 11)
  {
    $detriment = SE_SUN;
  }
  elseif ($sign_pos == 3 Or $sign_pos == 6)
  {
    $detriment = SE_JUPITER;
  }
  elseif ($sign_pos == 4 Or $sign_pos == 5)
  {
    $detriment = SE_SATURN;
  }

    drawboldtext($im, 14, 0, $left_margin + 295, $top_of_planet_listing + ($cnt * $y_spacing), $black, 'HamburgSymbols.ttf', chr($pl_glyph[$detriment]), 0);                  //draw planet glyph


//find fall of this planet's sign
  $fall = -1;

  if ($sign_pos == 7)
  {
    $fall = SE_SUN;
  }
  elseif ($sign_pos == 8)
  {
    $fall = SE_MOON;
  }
  elseif ($sign_pos == 10)
  {
    $fall = SE_JUPITER;
  }
  elseif ($sign_pos == 12)
  {
    $fall = SE_MERCURY;
  }
  elseif ($sign_pos == 1)
  {
    $fall = SE_SATURN;
  }
  elseif ($sign_pos == 4)
  {
    $fall = SE_MARS;
  }
  elseif ($sign_pos == 6)
  {
    $fall = SE_VENUS;
  }

    drawboldtext($im, 12, 0, $left_margin + 335, $top_of_planet_listing + ($cnt * $y_spacing), $black, 'HamburgSymbols.ttf', chr($pl_glyph[$fall]), 0);                 //draw planet glyph


//antiscia
    // display planet data in the right-hand table
    $antiscia = Crunch(180 - $longitude[$i]);

    $sign_pos = floor($antiscia / 30) + 1;

    if ($sign_pos == 1 Or $sign_pos == 5 Or $sign_pos == 9)
    {
      $clr_to_use = $red;
    }
    elseif ($sign_pos == 2 Or $sign_pos == 6 Or $sign_pos == 10)
    {
      $clr_to_use = $another_green;
    }
    elseif ($sign_pos == 3 Or $sign_pos == 7 Or $sign_pos == 11)
    {
      $clr_to_use = $orange;
    }
    elseif ($sign_pos == 4 Or $sign_pos == 8 Or $sign_pos == 12)
    {
      $clr_to_use = $blue;
    }

    imagettftext($im, 10, 0, $left_margin + 446, $top_of_planet_listing + ($cnt * $y_spacing), $black, 'arial.ttf', Convert_Longitude1($antiscia));               //display longitude degrees
    imagettftext($im, 12, 0, $left_margin + 463, $top_of_planet_listing + ($cnt * $y_spacing), $clr_to_use, 'HamburgSymbols.ttf', chr($sign_glyph[$sign_pos]));         //display sign
    imagettftext($im, 10, 0, $left_margin + 479, $top_of_planet_listing + ($cnt * $y_spacing), $black, 'arial.ttf', Convert_Longitude2($antiscia));               //display longitude minutes and Rx


//contra-antiscia
    // display planet data in the right-hand table
    $antiscia = Crunch(180 - $longitude[$i] + 180);

    $sign_pos = floor($antiscia / 30) + 1;

    if ($sign_pos == 1 Or $sign_pos == 5 Or $sign_pos == 9)
    {
      $clr_to_use = $red;
    }
    elseif ($sign_pos == 2 Or $sign_pos == 6 Or $sign_pos == 10)
    {
      $clr_to_use = $another_green;
    }
    elseif ($sign_pos == 3 Or $sign_pos == 7 Or $sign_pos == 11)
    {
      $clr_to_use = $orange;
    }
    elseif ($sign_pos == 4 Or $sign_pos == 8 Or $sign_pos == 12)
    {
      $clr_to_use = $blue;
    }

    imagettftext($im, 10, 0, $left_margin + 512, $top_of_planet_listing + ($cnt * $y_spacing), $black, 'arial.ttf', Convert_Longitude1($antiscia));               //display longitude degrees
    imagettftext($im, 12, 0, $left_margin + 529, $top_of_planet_listing + ($cnt * $y_spacing), $clr_to_use, 'HamburgSymbols.ttf', chr($sign_glyph[$sign_pos]));         //display sign
    imagettftext($im, 10, 0, $left_margin + 545, $top_of_planet_listing + ($cnt * $y_spacing), $black, 'arial.ttf', Convert_Longitude2($antiscia));               //display longitude minutes and Rx


//speed value
  $the_speed = abs($speed[$i]);
  $spd_deg = floor($the_speed);
  $spd_min = ($the_speed - floor($the_speed)) * 60;

  if ($speed[$i] >= 0)
  {
    $t = "+";
  }
  else
  {
    $t = "-";
  }

  $t = $t . sprintf("%02d", $spd_deg) . " " . sprintf("%02d", $spd_min);
    imagettftext($im, 10, 0, $left_margin + 642, $top_of_planet_listing + ($cnt * $y_spacing), $black, 'arial.ttf', $t);


//speed literal
    $speed_literal = "Mean";

    if ($i == SE_SUN And abs($speed[$i]) > .9856103) { $speed_literal = "Fast"; }
    if ($i == SE_MERCURY And abs($speed[$i]) > 1.66667) { $speed_literal = "Fast"; }
    if ($i == SE_VENUS And abs($speed[$i]) > 1.166667) { $speed_literal = "Fast"; }
    if ($i == SE_MARS And abs($speed[$i]) > .716667) { $speed_literal = "Fast"; }
    if ($i == SE_JUPITER And abs($speed[$i]) > .2000) { $speed_literal = "Fast"; }
    if ($i == SE_SATURN And abs($speed[$i]) > .1000) { $speed_literal = "Fast"; }
    if ($i == SE_MOON And abs($speed[$i]) > 14.166667) { $speed_literal = "Fast"; }

    if ($i == SE_SUN And abs($speed[$i]) < .9856103) { $speed_literal = "Slow"; }
    if ($i == SE_MERCURY And abs($speed[$i]) < .333333) { $speed_literal = "Slow"; }
    if ($i == SE_VENUS And abs($speed[$i]) < .25000) { $speed_literal = "Slow"; }
    if ($i == SE_MARS And abs($speed[$i]) < .166667) { $speed_literal = "Slow"; }
    if ($i == SE_JUPITER And abs($speed[$i]) < .05000) { $speed_literal = "Slow"; }
    if ($i == SE_SATURN And abs($speed[$i]) < .033333) { $speed_literal = "Slow"; }
    if ($i == SE_MOON And abs($speed[$i]) < 12.2500) { $speed_literal = "Slow"; }

    if (abs($speed[$i]) < .00833333) { $speed_literal = "stat"; }

    if ($i >= SE_URANUS) { $speed_literal = ""; }

    imagettftext($im, 10, 0, $left_margin + 706, $top_of_planet_listing + ($cnt * $y_spacing), $black, 'arial.ttf', $speed_literal);


//peregrine
    $peregrineX = "";

    if ($peregrine[$i] == 1 And $i <= SE_SATURN) { $peregrineX = "X"; }

    imagettftext($im, 8, 0, $left_margin + 760, $top_of_planet_listing + ($cnt * $y_spacing), $black, 'arial.ttf', $peregrineX);


    $cnt++;
  }


//here is where it will list the aspects found - this is to the left of the chartwheel
//display planet aspects
  $left_col_print_idx = 2;
  $line_cntr = 0;
  drawboldtext($im, 12, 0, $left_col_print_idx, $y_top_margin + 20 + (15 * $line_cntr), $red, 'arial.ttf', "W. Lilly Aspects", 0);

  $line_cntr++;

  for ($i = SE_SUN; $i <= SE_JUPITER; $i++)
  {
    for ($j = $i + 1; $j <= SE_PLUTO; $j++)
    {
      $xbuf = "";
      $q = 0;
      $dayy = $longitude[$i] - $longitude[$j];
      $da = abs($longitude[$i] - $longitude[$j]);

      if ($da > 180) { $da = 360 - $da; }

      $orb = $moiety[$i] + $moiety[$j];

      // is there an aspect within orb?
      if ($da <= $orb)
      {
        $q = 1;
        $da = $da;
      }
      elseif (($da <= (60 + $orb)) And ($da >= (60 - $orb)))
      {
        $q = 5;
        $da = $da - 60;
      }
      elseif (($da <= (90 + $orb)) And ($da >= (90 - $orb)))
      {
        $q = 4;
        $da = $da - 90;
      }
      elseif (($da <= (120 + $orb)) And ($da >= (120 - $orb)))
      {
        $q = 3;
        $da = $da - 120;
      }
      elseif ($da >= (180 - $orb))
      {
        $q = 2;
        $da = 180 - $da;
      }

      $daxx = $da;
      $da = abs($da);

      if ($q > 0)
      {
    //determine applying or separating here
        $da1 = $da;               //save for later restoration
        $daxx1 = $daxx;
        $dayy1 = $dayy;

        $p1 = $longitude[$i] + $speed[$i];
        $p2 = $longitude[$j] + $speed[$j];

        $dayy = $p1 - $p2;
        $da = abs($p1 - $p2);

        if ($da > 180) { $da = 360 - $da; }

        if ($q == 5)
        {
          $da = $da - 60;
        }
        elseif ($q == 4)
        {
          $da = $da - 90;
        }
        elseif ($q == 3)
        {
          $da = $da - 120;
        }
        elseif ($q == 2)
        {
          $da = $da - 180;
        }

        $daxx = $da;
        $da = abs($da);

        if (abs($da) <= abs($da1))
        {
          $xbuf .= "app";
        }
        elseif ((sgnx($daxx1) <= 0 And sgnx($daxx) >= 0) Or (sgnx($daxx1) >= 0 And sgnx($daxx) <= 0))
        {
          $xbuf .= "app";
        }
        elseif (($dayy > 350 And $dayy1 < 10) Or ($dayy < 10 And $dayy1 > 350))
        {
          $xbuf .= "app";
        }
        elseif ((sgnx($dayy1) <= 0 And sgnx($dayy) >= 0) Or (sgnx($dayy1) >= 0 And sgnx($dayy) <= 0))
        {
          $xbuf .= "app";
        }
        elseif ($q == 2 And ((abs($dayy1) < 180 And abs($dayy) >= 180) Or (abs($dayy1) >= 180 And abs($dayy) < 180)))
        {
          $xbuf .= "app";
        }
        else
        {
          $xbuf .= "sep";
        }

        $da = $da1;
        $daxx = $daxx1;
        $dayy = $dayy1;


        if ($daxx >= -.0166666 And $daxx <= .0166666)
        {
          $xbuf .= "partile";
        }
        elseif ($q != 1 And (($dayy < 0 And abs($dayy) < 180) Or ($dayy > 0 And abs($dayy) > 180)))
        {
          $xbuf .= ", sin ";
        }
        elseif ($q != 1 And (($dayy > 0 And abs($dayy) < 180) Or ($dayy < 0 And abs($dayy) > 180)))
        {
          $xbuf .= ", dex ";
        }


        // aspect exists
        $t = chr($pl_glyph[$i]) . "   " . chr($asp_glyph[$q]) . "   " . chr($pl_glyph[$j]) . "   " . sprintf("%.2f", $da);
        imagettftext($im, 10, 0, $left_col_print_idx, $y_top_margin + 20 + (15 * $line_cntr), $black, 'HamburgSymbols.ttf', $t);
        imagettftext($im, 10, 0, 110, $y_top_margin + 20 + (15 * $line_cntr), $black, 'arial.ttf', $xbuf);

        $line_cntr++;
      }
    }
  }

  $line_cntr++;
  $end_of_aspect_listing_height = $y_top_margin + 20 + (15 * $line_cntr);



  //draw the border around the whole table
  imageline($im, $left_col_print_idx, $start_of_y_axis, $left_col_print_idx, $start_of_y_axis - 30, $black);              //left side line
  imageline($im, $overall_size + $extra_width_for_graphic_data_table - $left_col_print_idx, $start_of_y_axis, $overall_size + $extra_width_for_graphic_data_table - $left_col_print_idx, $start_of_y_axis - 30, $black);    //right side line
  imageline($im, $left_col_print_idx, $start_of_y_axis - 30, $overall_size + $extra_width_for_graphic_data_table - $left_col_print_idx, $start_of_y_axis - 30, $black);   //horizontal line below table


// draw the image in png format - using imagepng() results in clearer text compared with imagejpeg()
  //$hash_x = $_SESSION['hash_x'];

  imagepng($im);

  //imagepng($im, "./downloads/" . $hash_x . ".png");     //output image to a file

  imagedestroy($im);
  exit();


Function mid($midstring, $midstart, $midlength)
{
  return(substr($midstring, $midstart-1, $midlength));
}


Function Reduce_below_30($longitude)
{
  $lng = $longitude;

  while ($lng >= 30) { $lng = $lng - 30; }

  return $lng;
}


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


Function Sort_planets_by_descending_longitude($num_planets, $longitude, &$sort, &$sort_pos)
{
// load all $longitude() into sort() and keep track of the planet numbers in $sort_pos()
  for ($i = 0; $i <= $num_planets - 1; $i++)
  {
    $sort[$i] = $longitude[$i];
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


Function Count_planets_in_each_house($num_planets, $house_pos, &$sort_pos, &$sort, &$nopih, &$home)
{
// reset and count the number of planets in each house
  for ($i = 1; $i <= 12; $i++)
  {
    $nopih[$i] = 0;
  }

// run through all the planets and see how many planets are in each house
  for ($i = 0; $i <= $num_planets - 1; $i++)
  {
    $temp = $house_pos[$sort_pos[$i]];

    // get house planet is in
    $nopih[$temp]++;

    $home[$i] = $temp;
  }

  // now check for Aries planets in same house as Pisces planets that do not start a new house
  while ($home[$num_planets - 1] == $home[0])
  {
    $temp1 = $sort[$num_planets - 1];
    $temp2 = $sort_pos[$num_planets - 1];
    $temp3 = $home[$num_planets - 1];

    for ($i = $num_planets - 1; $i >= 1; $i--)
    {
      $sort[$i] = $sort[$i - 1];
      $sort_pos[$i] = $sort_pos[$i - 1];
      $home[$i] = $home[$i - 1];
    }

    $sort[0] = $temp1;
    $sort_pos[0] = $temp2;
    $home[0] = $temp3;
  }
}


Function display_house_number_new($num, $angle, $radii, &$xy)
{
  if ($num < 10)
  {
    $char_width = 10;
  }
  else
  {
    $char_width = 18;
  }
  $half_char_width = $char_width / 2;
  $char_height = 12;
  $half_char_height = $char_height / 2;
  $quarter_char_height = $char_height / 4;

//puts center of character right on circumference of imaginary circle
  $xpos0 = -$half_char_width;
  $ypos0 = $char_height;

  if ($num == 1)
  {
    $radii = $radii + 1;

    $x_adj = -cos(deg2rad($angle)) * $half_char_width;
    $ypos0 = $half_char_height - ($half_char_height / 2);
    $y_adj = sin(deg2rad($angle)) * $char_height;
  }
  elseif ($num == 2)
  {
    $radii = $radii + 3;

    $x_adj = -cos(deg2rad($angle));
    $ypos0 = $half_char_height - ($half_char_height / 2);
    $y_adj = sin(deg2rad($angle)) * $char_height;
  }
  elseif ($num == 3)
  {
    $xpos0 = -$half_char_width;
    $x_adj = -cos(deg2rad($angle)) * $half_char_width;
    $ypos0 = $char_height - 4;
    $y_adj = sin(deg2rad($angle)) * $half_char_height;
  }
  elseif ($num == 4)
  {
    $xpos0 = -$half_char_width;
    $x_adj = -cos(deg2rad($angle)) * $half_char_width;
    $ypos0 = $char_height - 2;
    $y_adj = sin(deg2rad($angle)) * $half_char_height;
  }
  elseif ($num == 5)
  {
    $xpos0 = 3;
    $x_adj = -cos(deg2rad($angle)) * $half_char_width;
    $ypos0 = $half_char_height;
    $y_adj = sin(deg2rad($angle)) * $half_char_height;
  }
  elseif ($num == 6)
  {
    $radii = $radii + 5;

    $xpos0 = $half_char_width / 2;
    $x_adj = -cos(deg2rad($angle));
    $ypos0 = $half_char_height;
    $y_adj = sin(deg2rad($angle)) * char_height;
  }
  elseif ($num == 7)
  {
    $radii = $radii + 3;

    $x_adj = -cos(deg2rad($angle)) * $char_width;
    $ypos0 = 0;
    $y_adj = -sin(deg2rad($angle)) * $char_height;
  }
  elseif ($num == 8)
  {
    $radii = $radii - 2;

    $xpos0 = $half_char_width;
    $x_adj = -cos(deg2rad($angle));
    $ypos0 = $half_char_height;
    $y_adj = sin(deg2rad($angle)) * $char_height;
  }
  elseif ($num == 9)
  {
    $xpos0 = 0;
    $x_adj = -cos(deg2rad($angle)) * $char_width;
    $ypos0 = 2;
    $y_adj = sin(deg2rad($angle)) * $half_char_height;
  }
  elseif ($num == 10)
  {
    $xpos0 = -2;
    $x_adj = -cos(deg2rad($angle)) * $char_width;
    $ypos0 = 2;
    $y_adj = sin(deg2rad($angle)) * $half_char_height;
  }
  elseif ($num == 11)
  {
    $radii = $radii + 7;

    $xpos0 = 0;
    $x_adj = -cos(deg2rad($angle)) * $half_char_width;
    $y_adj = sin(deg2rad($angle)) * $char_height;
  }
  elseif ($num == 12)
  {
    $radii = $radii + 2;

    $xpos0 = -$half_char_width;
    $x_adj = -cos(deg2rad($angle)) * $half_char_width / 2;
    $ypos0 = $half_char_height;
    $y_adj = sin(deg2rad($angle));
  }

  $xy[0] = $xpos0 + $x_adj - ($radii * cos(deg2rad($angle)));
  $xy[1] = $ypos0 + $y_adj + ($radii * sin(deg2rad($angle)));;

  return ($xy);
}


Function drawboldtext($image, $size, $angle, $x_cord, $y_cord, $clr_to_use, $fontfile, $text, $boldness)
{
  $_x = array(1, 0, 1, 0, -1, -1, 1, 0, -1);
  $_y = array(0, -1, -1, 0, 0, -1, 1, 1, 1);

  for ($n = 0; $n <= $boldness; $n++)
  {
    imagettftext($image, $size, $angle, $x_cord+$_x[$n], $y_cord+$_y[$n], $clr_to_use, $fontfile, $text);
  }
}


Function display_planet_glyph($our_angle, $angle_to_use, $radii, &$xy, $code)
{
// $code = 0 for planet glyph, 1 for text, 2 for sign glyph, 3 for Rx symbol
// $our_angle in degree, $angle_to_use in radians
  $this_angle = Crunch($our_angle);

  if ($this_angle >= 1 And $this_angle <= 181)
  {
    if ($code == 0)
    {
      $cw_pl_glyph = 15;
      $ch_pl_glyph = 15;
    }
    elseif ($code == 1)
    {
      $cw_pl_glyph = 12;
      $ch_pl_glyph = 10;
    }
    elseif ($code == 2)
    {
      $cw_pl_glyph = 12;
      $ch_pl_glyph = 10;
    }
    else
    {
      $cw_pl_glyph = 8;
      $ch_pl_glyph = 10;
    }
  }
  else
  {
    if ($code == 0)
    {
      $cw_pl_glyph = 13;
      $ch_pl_glyph = 17;
    }
    elseif ($code == 1)
    {
      $cw_pl_glyph = 10;
      $ch_pl_glyph = 10;
    }
    elseif ($code == 2)
    {
      $cw_pl_glyph = 8;
      $ch_pl_glyph = 8;
    }
    else
    {
      $cw_pl_glyph = 6;
      $ch_pl_glyph = 10;
    }
  }

  $gap_pl_glyph = -8;

// take into account the width and height of the glyph, defined below
// get distance we need to shift the glyph so that the absolute middle of the glyph is the start point
  $center_pos_x = -$cw_pl_glyph / 2;
  $center_pos_y = $ch_pl_glyph / 2;

// get the offset we have to move the center point to in order to be properly placed
  $offset_pos_x = $center_pos_x * cos($angle_to_use);
  $offset_pos_y = $center_pos_y * sin($angle_to_use);

// now get the final X, Y coordinates
  $xy[0] = $center_pos_x + $offset_pos_x + ((-$radii + $gap_pl_glyph) * cos($angle_to_use));
  $xy[1] = $center_pos_y + $offset_pos_y + (($radii - $gap_pl_glyph) * sin($angle_to_use));

  return ($xy);
}


Function display_house_cusp($num, $angle, $radii, &$xy)
{
  $char_width = 18;       //16;
  $half_char_width = $char_width / 2;
  $char_height = 14;      //10;
  $half_char_height = $char_height / 2;

//puts center of character right on circumference of circle
  $xpos0 = -$half_char_width;
  $ypos0 = $half_char_height;

  $x_adj = -cos(deg2rad($angle));
  $y_adj = sin(deg2rad($angle));

  $xy[0] = $xpos0 + $x_adj - ($radii * cos(deg2rad($angle)));
  $xy[1] = $ypos0 + $y_adj + ($radii * sin(deg2rad($angle)));

  return ($xy);
}


Function Crunch($x)
{
  if ($x >= 0)
  {
    $y = $x - floor($x / 360) * 360;
  }
  else
  {
    $y = 360 + ($x - ((1 + floor($x / 360)) * 360));
  }

  return $y;
}


Function Check_for_overlap($angle, $spot_filled, $spacing)
{
// spacing is really 1 more than we enter with, but we use assign $spacing = 1 less for easier math below
  $result = False;

  for ($i = $angle - $spacing; $i <= $angle + $spacing; $i++)
  {
    if ($spot_filled[Crunch(round($i))] == 1)
    {
      $result = True;
      break;
    }
  }

  return $result;
}


Function Convert_Longitude1($longitude)
{
  $sign_num = floor($longitude / 30);
  $pos_in_sign = $longitude - ($sign_num * 30);
  $deg = floor($pos_in_sign);
  $full_min = ($pos_in_sign - $deg) * 60;
  $min = floor($full_min);
  $full_sec = round(($full_min - $min) * 60);

  if ($deg < 10) { $deg = "0" . $deg; }

  if ($min < 10) { $min = "0" . $min; }

  if ($full_sec < 10) { $full_sec = "0" . $full_sec; }

  return $deg;
}


Function Convert_Longitude2($longitude)
{
  $sign_num = floor($longitude / 30);
  $pos_in_sign = $longitude - ($sign_num * 30);
  $deg = floor($pos_in_sign);
  $full_min = ($pos_in_sign - $deg) * 60;
  $min = floor($full_min);
  $full_sec = round(($full_min - $min) * 60);

  if ($deg < 10) { $deg = "0" . $deg; }

  if ($min < 10) { $min = "0" . $min; }

  if ($full_sec < 10) { $full_sec = "0" . $full_sec; }

  return $min . "' ";
}


Function left($leftstring, $leftlength)
{
  return(substr($leftstring, 0, $leftlength));
}


Function Sort_planets_by_descending_longitude_2($num_planets, $longitude, &$sort, &$sort_pos)
{
// load all $longitude[] into sort[] and keep track of the planet numbers in $sort_pos[]
  for ($i = 0; $i <= $num_planets - 1; $i++)
  {
    $sort[$i] = Reduce_below_30($longitude[$i]);
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


Function sgnx($x)
{
  if ($x > 0)
  {
    return 1;
  }
  elseif ($x < 0)
  {
    return -1;
  }
  elseif ($x == 0)
  {
    return 0;
  }
}


Function Find_best_planet_to_start_with($num_planets, $house_pos, &$sort_pos, &$sort, $nopih)
{
  
  //step 1 - find planets which have at least 20 deg clearance between themselves and the next lower planet in the array
  for ($i = $num_planets - 2; $i >= 0; $i--)
  {
    if ($sort[$i] - $sort[$i + 1] >= 20)
    {
      //step 2 - is this planet the first (and only) planet in the house?
      $pl_num = $sort_pos[$i];
      $house_of_pl = $house_pos[$pl_num];
      if ($nopih[$house_of_pl] == 1)
      {
        $start_planet = $pl_num;
        $start_planet_idx = $i;
        break;
      }
    }
  }
  
  if ($i < 0) { return; }       //we did not find a planet that meets our needs so do not change the $sort[] array
  
  //here we reorder the $sort[] and $sort_pos[] arrays so that we start with the indicated planet, which is $start_planet
  $sp = array();
  $s = array();
  
  $cnt = $num_planets - 1;
  for ($i = $start_planet_idx; $i >= 0; $i--)
  {
    $sp[$cnt] = $sort_pos[$i];
    $s[$cnt] = $sort[$i];
    $cnt--;
  } 

  for ($i = $num_planets - 1; $i > $start_planet_idx; $i--)
  {
    $sp[$cnt] = $sort_pos[$i];
    $s[$cnt] = $sort[$i];
    $cnt--;
  } 

  $sort_pos = $sp;
  $sort = $s;
}

?>