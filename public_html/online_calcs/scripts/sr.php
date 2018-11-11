<?php

Function Detect_num_same_deg_planets_max($longitude1)
{
  $deg_filled_idx = array();
  
  for ($i = 0; $i <= 30; $i++)
  {
    $deg_filled_idx[$i] = 0;			//initialize
  }
  
  $cnt = 0;
  for ($i = 0; $i <= LAST_PLANET + 2; $i++)
  {
    $pl_pos = floor(Reduce_below_30($longitude1[$i]));
    $deg_filled_idx[$pl_pos]++;
  }
  
  $mx = 0;
  for ($i = 0; $i <= 30; $i++)
  {
    if ($deg_filled_idx[$i] > $mx)
    {
      $mx = $deg_filled_idx[$i];
    }
  }

  return $mx;
}


Function Detect_num_same_deg_planets_max_45($longitude1)
{
  $deg_filled_idx = array();
  
  for ($i = 0; $i <= 45; $i++)
  {
    $deg_filled_idx[$i] = 0;			//initialize
  }
  
  $cnt = 0;
  for ($i = 0; $i <= LAST_PLANET + 2; $i++)
  {
    $pl_pos = floor(Reduce_below_45($longitude1[$i]));
    $deg_filled_idx[$pl_pos]++;
  }
  
  $mx = 0;
  for ($i = 0; $i <= 45; $i++)
  {
    if ($deg_filled_idx[$i] > $mx)
    {
      $mx = $deg_filled_idx[$i];
    }
  }

  return $mx;
}


Function Detect_num_same_deg_planets_max_90($longitude1)
{
  $deg_filled_idx = array();
  
  for ($i = 0; $i <= 90; $i++)
  {
    $deg_filled_idx[$i] = 0;			//initialize
  }
  
  $cnt = 0;
  for ($i = 0; $i <= LAST_PLANET + 2; $i++)
  {
    $pl_pos = floor(Reduce_below_90($longitude1[$i]));
    $deg_filled_idx[$pl_pos]++;
  }
  
  $mx = 0;
  for ($i = 0; $i <= 90; $i++)
  {
    if ($deg_filled_idx[$i] > $mx)
    {
      $mx = $deg_filled_idx[$i];
    }
  }

  return $mx;
}


Function Reduce_below_30($longitude)
{
  $lng = $longitude;

  while ($lng >= 30)
  {
    $lng = $lng - 30;
  }

  return $lng;
}


Function Reduce_below_45($longitude)
{
  $lng = $longitude;

  while ($lng >= 45)
  {
    $lng = $lng - 45;
  }

  return $lng;
}


Function Reduce_below_90($longitude)
{
  $lng = $longitude;

  while ($lng >= 90)
  {
    $lng = $lng - 90;
  }

  return $lng;
}


Function Detect_max_num_of_midpoints($p1, $p2, $L2, $mp, $last_planet, $ubt1)
{
//define(SE_LILITH, 11);
//define(SE_TNODE, 12);
//define(SE_POF, 13);
//define(SE_VERTEX, 14);
//define(LAST_PLANET, 14);

  $mx = -99;
  
  for ($k = $p1; $k <= $p2; $k++)					// natal planet
  {
    $cnt = 0;
    
    if ($k == 11 Or $k == 13 Or $k == 14) { continue; }			// don't do every midpoint combination

    for ($i = 0; $i <= $last_planet + 1; $i++)					// natal midpoint
    {
      if ($i == 11 Or $i == 13 Or $i == 14) { continue; }		// don't do every midpoint combination

      for ($j = $i + 1; $j <= $last_planet + 2; $j++)			// natal midpoint
      {
        if ($j == 11 Or $j == 13 Or $j == 14) { continue; }		// don't do every midpoint combination
      
        if ($k == $i Or $k == $j) { continue; }					// don't allow the same planet to appear more than once
        
        if ($ubt1 == 1 And ($i > 12 Or $j > 12 Or $k > 12)) { continue; }

        $q = 0;
        $da = Abs($mp[$i][$j] - $L2[$k]);

        if ($da > 180) { $da = 360 - $da; }

        $orb = 1.0001;

        // is there an aspect within orb?
        if ($da <= $orb)
        {
          $cnt++;
        }
        elseif (($da <= (90 + $orb)) And ($da >= (90 - $orb)))
        {
          $cnt++;
        }
        elseif ($da >= (180 - $orb))
        {
          $cnt++;
        }
      }
    }
    
    if ($cnt > $mx) { $mx = $cnt; }
  }
  
  return $mx;
}

?>
