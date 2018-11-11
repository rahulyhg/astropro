<?php

Function Get_Dual_Cosmodyne_Harmony($longitude1, $declination1, $house_pos1, $longitude2, $declination2, $house_pos2, $hob, $mob)
{
  $dynes = array();

  $p1_limit = 10;
  $p2_limit = 10;

  if (($hob[1] == 12) And ($mob[1] == 0))
  {
    $p1_limit = 9;
  }

  if (($hob[2] == 12) And ($mob[2] == 0))
  {
    $p2_limit = 9;
  }

  for ($y = 0; $y <= $p1_limit; $y++)
  {
    for ($x = 0; $x <= $p2_limit; $x++)
    {
      $dynes[$y][$x] = 0;
      
      if (($y == 7 Or $y == 8 Or $y == 9) And ($x == 7 Or $x == 8 Or $x == 9))
      {
        // skip this planet and move to the next
        continue;
      }
      else
      {
        // get house of 1st planet
        $yh = floor($house_pos1[$y]);
        if ($y == 10)
        {
          $yh = 10;
          $orb30y = 3;
          $orb45y = 5;
          $orb60y = 7;
          $orb90y = 10;
          $orb180y = 12;
          $pow30y = 3;
          $pow45y = 5;
          $pow60y = 7;
          $pow90y = 10;
          $pow180y = 12;
        }
        else
        {
          // find ORB for 1st planet ($y), dependent upon what house $y is in AND whether it is a Luminary or a planet
          if (($y != 0 And $y != 1) And ($yh == 3 Or $yh == 6 Or $yh == 9 Or $yh == 12))
          {
            $orb30y = 1;
            $orb45y = 3;
            $orb60y = 5;
            $orb90y = 6;
            $orb180y = 8;
            $pow30y = 1;
            $pow45y = 3;
            $pow60y = 5;
            $pow90y = 6;
            $pow180y = 8;
            if ($y == 2)
            {
              $pow30y += 1;
              $pow45y += 1;
              $pow60y += 1;
              $pow90y += 2;
              $pow180y += 3;
            }
          }
          elseif (($y == 0 Or $y == 1) And ($yh == 3 Or $yh == 6 Or $yh == 9 Or $yh == 12))
          {
            $orb30y = 2;
            $orb45y = 4;
            $orb60y = 6;
            $orb90y = 8;
            $orb180y = 11;
            $pow30y = 2;
            $pow45y = 4;
            $pow60y = 6;
            $pow90y = 8;
            $pow180y = 11;
          }
          elseif (($y != 0 And $y != 1) And ($yh == 2 Or $yh == 5 Or $yh == 8 Or $yh == 11))
          {
            $orb30y = 2;
            $orb45y = 4;
            $orb60y = 6;
            $orb90y = 8;
            $orb180y = 10;
            $pow30y = 2;
            $pow45y = 4;
            $pow60y = 6;
            $pow90y = 8;
            $pow180y = 10;
            if ($y == 2)
            {
              $pow30y += 1;
              $pow45y += 1;
              $pow60y += 1;
              $pow90y += 2;
              $pow180y += 3;
            }
          }
          elseif (($y == 0 Or $y == 1) And ($yh == 2 Or $yh == 5 Or $yh == 8 Or $yh == 11))
          {
            $orb30y = 3;
            $orb45y = 5;
            $orb60y = 7;
            $orb90y = 10;
            $orb180y = 13;
            $pow30y = 3;
            $pow45y = 5;
            $pow60y = 7;
            $pow90y = 10;
            $pow180y = 13;
          }
          elseif (($y != 0 And $y != 1) And ($yh == 1 Or $yh == 4 Or $yh == 7 Or $yh == 10))
          {
            $orb30y = 3;
            $orb45y = 5;
            $orb60y = 7;
            $orb90y = 10;
            $orb180y = 12;
            $pow30y = 3;
            $pow45y = 5;
            $pow60y = 7;
            $pow90y = 10;
            $pow180y = 12;
            if ($y == 2)
            {
              $pow30y += 1;
              $pow45y += 1;
              $pow60y += 1;
              $pow90y += 2;
              $pow180y += 3;
            }
          }
          elseif (($y == 0 Or $y == 1) And ($yh == 1 Or $yh == 4 Or $yh == 7 Or $yh == 10))
          {
            $orb30y = 4;
            $orb45y = 6;
            $orb60y = 8;
            $orb90y = 12;
            $orb180y = 15;
            $pow30y = 4;
            $pow45y = 6;
            $pow60y = 8;
            $pow90y = 12;
            $pow180y = 15;
          }
        }

        // get house of 2nd planet
        $xh = floor($house_pos2[$x]);
        if ($x == 10)
        {
          $xh = 10;
          $orb30x = 3;
          $orb45x = 5;
          $orb60x = 7;
          $orb90x = 10;
          $orb180x = 12;
          $pow30x = 3;
          $pow45x = 5;
          $pow60x = 7;
          $pow90x = 10;
          $pow180x = 12;
        }
        else
        {
          // find ORB for 2nd planet ($x), dependent upon what house $x is in AND whether it is a Luminary or a planet
          if (($x != 0 And $x != 1) And ($xh == 3 Or $xh == 6 Or $xh == 9 Or $xh == 12))
          {
            $orb30x = 1;
            $orb45x = 3;
            $orb60x = 5;
            $orb90x = 6;
            $orb180x = 8;
            $pow30x = 1;
            $pow45x = 3;
            $pow60x = 5;
            $pow90x = 6;
            $pow180x = 8;
            if ($x == 2)
            {
              $pow30x += 1;
              $pow45x += 1;
              $pow60x += 1;
              $pow90x += 2;
              $pow180x += 3;
            }
          }
          elseif (($x == 0 Or $x == 1) And ($xh == 3 Or $xh == 6 Or $xh == 9 Or $xh == 12))
          {
            $orb30x = 2;
            $orb45x = 4;
            $orb60x = 6;
            $orb90x = 8;
            $orb180x = 11;
            $pow30x = 2;
            $pow45x = 4;
            $pow60x = 6;
            $pow90x = 8;
            $pow180x = 11;
          }
          elseif (($x != 0 And $x != 1) And ($xh == 2 Or $xh == 5 Or $xh == 8 Or $xh == 11))
          {
            $orb30x = 2;
            $orb45x = 4;
            $orb60x = 6;
            $orb90x = 8;
            $orb180x = 10;
            $pow30x = 2;
            $pow45x = 4;
            $pow60x = 6;
            $pow90x = 8;
            $pow180x = 10;
            if ($x == 2)
            {
              $pow30x += 1;
              $pow45x += 1;
              $pow60x += 1;
              $pow90x += 2;
              $pow180x += 3;
            }
          }
          elseif (($x == 0 Or $x == 1) And ($xh == 2 Or $xh == 5 Or $xh == 8 Or $xh == 11))
          {
            $orb30x = 3;
            $orb45x = 5;
            $orb60x = 7;
            $orb90x = 10;
            $orb180x = 13;
            $pow30x = 3;
            $pow45x = 5;
            $pow60x = 7;
            $pow90x = 10;
            $pow180x = 13;
          }
          elseif (($x != 0 And $x != 1) And ($xh == 1 Or $xh == 4 Or $xh == 7 Or $xh == 10))
          {
            $orb30x = 3;
            $orb45x = 5;
            $orb60x = 7;
            $orb90x = 10;
            $orb180x = 12;
            $pow30x = 3;
            $pow45x = 5;
            $pow60x = 7;
            $pow90x = 10;
            $pow180x = 12;
            if ($x == 2)
            {
              $pow30x += 1;
              $pow45x += 1;
              $pow60x += 1;
              $pow90x += 2;
              $pow180x += 3;
            }
          }
          elseif (($x == 0 Or $x == 1) And ($xh == 1 Or $xh == 4 Or $xh == 7 Or $xh == 10))
          {
            $orb30x = 4;
            $orb45x = 6;
            $orb60x = 8;
            $orb90x = 12;
            $orb180x = 15;
            $pow30x = 4;
            $pow45x = 6;
            $pow60x = 8;
            $pow90x = 12;
            $pow180x = 15;
          }
        }
      }

      $orb30 = $orb30y;
      if ($orb30x >= $orb30y)
      {
        $orb30 = $orb30x;
      }

      $orb45 = $orb45y;
      if ($orb45x >= $orb45y)
      {
        $orb45 = $orb45x;
      }
      $orb60 = $orb60y;
      if ($orb60x >= $orb60y)
      {
        $orb60 = $orb60x;
      }
      $orb90 = $orb90y;
      if ($orb90x >= $orb90y)
      {
        $orb90 = $orb90x;
      }
      $orb180 = $orb180y;
      if ($orb180x >= $orb180y)
      {
        $orb180 = $orb180x;
      }

      $pow30 = $pow30y;
      if ($pow30x >= $pow30y)
      {
        $pow30 = $pow30x;
      }

      $pow45 = $pow45y;
      if ($pow45x >= $pow45y)
      {
        $pow45 = $pow45x;
      }
      $pow60 = $pow60y;
      if ($pow60x >= $pow60y)
      {
        $pow60 = $pow60x;
      }
      $pow90 = $pow90y;
      if ($pow90x >= $pow90y)
      {
        $pow90 = $pow90x;
      }
      $pow180 = $pow180y;
      if ($pow180x >= $pow180y)
      {
        $pow180 = $pow180x;
      }

      // is there an aspect within orb?
      $da = Abs($longitude1[$y] - $longitude2[$x]);			//$da means distance apart
      if ($da > 180) { $da = 360 - $da; }

      $q = 1;
      $k = $da;

      if ($k <= $orb180)
      {
        $q = 2;
        $orbxx = $pow180;
        $daxx = $da;
      }
      elseif (($k <= (30 + $orb30)) And ($k >= (30 - $orb30)))
      {
        $q = 8;
        $orbxx = $pow30;
        $daxx = $da - 30;
      }
      elseif (($k <= (45 + $orb45)) And ($k >= (45 - $orb45)))
      {
        $q = 9;
        $orbxx = $pow45;
        $daxx = $da - 45;
      }
      elseif (($k <= (60 + $orb60)) And ($k >= (60 - $orb60)))
      {
        $q = 3;
        $orbxx = $pow60;
        $daxx = $da - 60;
      }
      elseif (($k <= (90 + $orb90)) And ($k >= (90 - $orb90)))
      {
        $q = 4;
        $orbxx = $pow90;
        $daxx = $da - 90;
      }
      // $da is checked here to separate the overlap in the two aspects from 129 - 132 degrees for luminaries
      elseif (($da <= 130) And ($k <= (120 + $orb90)) And ($k >= (120 - $orb90)))
      {
        $q = 5;
        $orbxx = $pow90;
        $daxx = $da - 120;
      }
      elseif (($da > 130) And ($k <= (135 + $orb45)) And ($k >= (135 - $orb45)))
      {
        $q = 10;
        $orbxx = $pow45;
        $daxx = $da - 135;
      }
      elseif (($k <= (150 + $orb30)) And ($k >= (150 - $orb30)))
      {
        $q = 11;
        $orbxx = $pow30;
        $daxx = $da - 150;
      }
      elseif ($k >= (180 - $orb180))
      {
        $q = 6;
        $orbxx = $pow180;
        $daxx = $da - 180;
      }

      if ($q > 1)
      {
        // we have an aspect, so get all the proper numbers
        $dyne_val = $orbxx - Abs($daxx);

        //get planetary harmony and discord
        if ($q == 3 Or $q == 5 Or $q == 8)
        {
          $dynes[$y][$x] += $dyne_val;
        }

        if ($q == 4 Or $q == 9 Or $q == 10)
        {
          $dynes[$y][$x] -= $dyne_val;
        }

        if ($q == 2)
        {
          // check out conjunctions between planets
          if (($y == 0 And $x == 1) Or ($x == 0 And $y == 1))
          {
            // these conjunctions treated as harmonious
            $dynes[$y][$x] += $dyne_val;
          }

          if (($y == 0 And $x == 3) Or ($x == 0 And $y == 3))
          {
            // these conjunctions treated as harmonious
            $dynes[$y][$x] += $dyne_val;
          }

          if (($y == 0 And $x == 5) Or ($x == 0 And $y == 5))
          {
            // these conjunctions treated as harmonious
            $dynes[$y][$x] += ($dyne_val / 2);
          }

          if (($y == 1 And $x == 3) Or ($x == 1 And $y == 3))
          {
            // these conjunctions treated as harmonious
            $dynes[$y][$x] += ($dyne_val / 2);
          }

          if (($y == 4 And $x == 6) Or ($x == 4 And $y == 6))
          {
            // these conjunctions treated as discordant
            $dynes[$y][$x] -= ($dyne_val / 2);
          }

          if (($y == 4 And $x == 9) Or ($x == 4 And $y == 9))
          {
            // these conjunctions treated as discordant
            $dynes[$y][$x] -= ($dyne_val / 2);
          }

          if (($y == 6 And $x == 9) Or ($x == 6 And $y == 9))
          {
            // these conjunctions treated as discordant
            $dynes[$y][$x] -= ($dyne_val / 2);
          }

          if (($y == 6 And $x == 10) Or ($x == 6 And $y == 10))
          {
            // these conjunctions treated as discordant
            $dynes[$y][$x] -= ($dyne_val / 2);
          }

          if (($y == 9 And $x == 10) Or ($x == 9 And $y == 10))
          {
            // these conjunctions treated as discordant
            $dynes[$y][$x] -= ($dyne_val / 2);
          }
        }

        if ($q == 6)
        {
          // some opposition aspects treated as discordant
          if (($y == 4 And $x != 5) Or ($x == 4 And $y != 5))
          {
            if (($y == 4 And $x != 10) Or ($x == 4 And $y != 10))
            {
              if (($y == 4 And $x != 3) Or ($x == 4 And $y != 3))
              {
                // these oppositions treated as discordant
                $dynes[$y][$x] -= $dyne_val;
              }
            }
          }

          if (($y == 6 And $x != 5) Or ($x == 6 And $y != 5))
          {
            if (($y == 6 And $x != 10) Or ($x == 6 And $y != 10))
            {
              if (($y == 6 And $x != 4) Or ($x == 6 And $y != 4))
              {
                // these oppositions treated as discordant
                if ($y == 6 And $x == 6)
                {
                  // do not do anything with Saturn opposite Saturn
                }
                else
                {
                  $dynes[$y][$x] -= $dyne_val;
                }
              }
            }
          }

          if (($y == 9 And $x != 5) Or ($x == 9 And $y != 5))
          {
            if (($y == 9 And $x != 10) Or ($x == 9 And $y != 10))
            {
              if (($y == 9 And $x != 4) Or ($x == 9 And $y != 4))
              {
                if (($y == 9 And $x != 6) Or ($x == 9 And $y != 6))
                {
                  // these oppositions treated as discordant
                  $dynes[$y][$x] -= $dyne_val;
                }
              }
            }
          }

          if (($y == 0 And $x == 1) Or ($x == 0 And $y == 1))
          {
            // these oppositions treated as harmonious
            $dynes[$y][$x] += ($dyne_val / 2);
          }

          if (($y == 0 And $x == 3) Or ($x == 0 And $y == 3))
          {
            // these oppositions treated as harmonious
            $dynes[$y][$x] += ($dyne_val / 2);
          }

          if (($y == 0 And $x == 10) Or ($x == 0 And $y == 10))
          {
            // these oppositions treated as harmonious
            $dynes[$y][$x] += ($dyne_val / 2);
          }

          if (($y == 1 And $x == 10) Or ($x == 1 And $y == 10))
          {
            // these oppositions treated as harmonious
            $dynes[$y][$x] += ($dyne_val / 2);
          }

          if (($y == 1 And $x == 3) Or ($x == 1 And $y == 3))
          {
            // these oppositions treated as harmonious
            $dynes[$y][$x] += ($dyne_val / 2);
          }

          if (($y == 3 And $x == 5) Or ($x == 3 And $y == 5))
          {
            // these oppositions treated as harmonious
            $dynes[$y][$x] += $dyne_val;
          }

          if (($y == 3 And $x == 10) Or ($x == 3 And $y == 10))
          {
            // these oppositions treated as harmonious
            $dynes[$y][$x] += ($dyne_val / 2);
          }

          if (($y == 5 And $x == 10) Or ($x == 5 And $y == 10))
          {
            // these oppositions treated as harmonious
            $dynes[$y][$x] += ($dyne_val / 2);
          }

          if (($y == 2 And $x == 3) Or ($x == 2 And $y == 3))
          {
            // these oppositions treated as harmonious
            $dynes[$y][$x] += ($dyne_val / 4);
          }
        }

        if ($y == 5 Or $x == 5)
        {
          if (($y == $x) And ($q == 2))
          {
            // ignore Jupiter conjunct Jupiter
          }
          else
          {
            // these treated as harmonious
            $dynes[$y][$x] += ($dyne_val / 2);
          }
        }

        if ($y == 3 Or $x == 3)
        {
          if (($y == 7 Or $y == 8 Or $y == 9 Or $x == 7 Or $x == 8 Or $x == 9) And ($q == 2 Or $q == 6 Or $q == 11))
          {
            // ignore
          }
          else
          {
            // these oppositions treated as harmonious
            $dynes[$y][$x] += ($dyne_val / 4);
          }
        }

        if ($y == 6 Or $x == 6)
        {
          if (($y == $x) And ($q == 2))
          {
            // ignore Saturn conjunct Saturn
          }
          else
          {
            // Saturn aspects treated as discordant
            $dynes[$y][$x] -= ($dyne_val / 2);
          }
        }

        if ($y == 4 Or $x == 4)
        {
          // Mars aspects treated as discordant
          $dynes[$y][$x] -= ($dyne_val / 4);
        }
      }
    }
  }

  return $dynes;
}

?>
