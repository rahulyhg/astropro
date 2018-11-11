<?php

Function GetMutualReceptions($longitude1, $longitude2)
{
  $num_MRs = 0;

  for ($y = 0; $y <= 9; $y++)
  {

    $sy = floor($longitude1[$y] / 30) + 1;
    for ($x = 0; $x <= 9; $x++)
    {
      // get sign of each planet
      $sx = floor($longitude2[$x] / 30) + 1;

      // look for all mutual receptions
      if ($y == 0 And ($sy == 4 Or $sy == 2) And $x == 1 And ($sx == 1 Or $sx == 5))
        $num_MRs++;
      if ($y == 0 And ($sy == 3 Or $sy == 11) And $x == 7 And ($sx == 1 Or $sx == 5))
        $num_MRs++;
      if ($y == 0 And ($sy == 9 Or $sy == 12) And $x == 8 And ($sx == 1 Or $sx == 5))
        $num_MRs++;
      if ($y == 0 And ($sy == 5 Or $sy == 8) And $x == 9 And ($sx == 1 Or $sx == 5))
        $num_MRs++;
      if ($y == 0 And ($sy == 3 Or $sy == 6 Or $sy == 11) And $x == 2 And ($sx == 1 Or $sx == 5))
        $num_MRs++;
      if ($y == 0 And ($sy == 2 Or $sy == 7 Or $sy == 12) And $x == 3 And ($sx == 1 Or $sx == 5))
        $num_MRs++;
      if ($y == 0 And ($sy == 1 Or $sy == 8 Or $sy == 10) And $x == 4 And ($sx == 1 Or $sx == 5))
        $num_MRs++;
      if ($y == 0 And ($sy == 4 Or $sy == 9 Or $sy == 12) And $x == 5 And ($sx == 1 Or $sx == 5))
        $num_MRs++;
      if ($y == 0 And ($sy == 7 Or $sy == 10 Or $sy == 11) And $x == 6 And ($sx == 1 Or $sx == 5))
        $num_MRs++;

      if ($y == 1 And ($sy == 3 Or $sy == 11) And $x == 7 And ($sx == 2 Or $sx == 4))
        $num_MRs++;
      if ($y == 1 And ($sy == 9 Or $sy == 12) And $x == 8 And ($sx == 2 Or $sx == 4))
        $num_MRs++;
      if ($y == 1 And ($sy == 5 Or $sy == 8) And $x == 9 And ($sx == 2 Or $sx == 4))
        $num_MRs++;
      if ($y == 1 And ($sy == 3 Or $sy == 6 Or $sy == 11) And $x == 2 And ($sx == 2 Or $sx == 4))
        $num_MRs++;
      if ($y == 1 And ($sy == 2 Or $sy == 7 Or $sy == 12) And $x == 3 And ($sx == 2 Or $sx == 4))
        $num_MRs++;
      if ($y == 1 And ($sy == 1 Or $sy == 8 Or $sy == 10) And $x == 4 And ($sx == 2 Or $sx == 4))
        $num_MRs++;
      if ($y == 1 And ($sy == 4 Or $sy == 9 Or $sy == 12) And $x == 5 And ($sx == 2 Or $sx == 4))
        $num_MRs++;
      if ($y == 1 And ($sy == 7 Or $sy == 10 Or $sy == 11) And $x == 6 And ($sx == 2 Or $sx == 4))
        $num_MRs++;

      if ($y == 2 And ($sy == 3 Or $sy == 11) And $x == 7 And ($sx == 3 Or $sx == 6 Or $sx == 11))
        $num_MRs++;
      if ($y == 2 And ($sy == 9 Or $sy == 12) And $x == 8 And ($sx == 3 Or $sx == 6 Or $sx == 11))
        $num_MRs++;
      if ($y == 2 And ($sy == 5 Or $sy == 8) And $x == 9 And ($sx == 3 Or $sx == 6 Or $sx == 11))
        $num_MRs++;
      if ($y == 2 And ($sy == 2 Or $sy == 7 Or $sy == 12) And $x == 3 And ($sx == 3 Or $sx == 6 Or $sx == 11))
        $num_MRs++;
      if ($y == 2 And ($sy == 1 Or $sy == 8 Or $sy == 10) And $x == 4 And ($sx == 3 Or $sx == 6 Or $sx == 11))
        $num_MRs++;
      if ($y == 2 And ($sy == 4 Or $sy == 9 Or $sy == 12) And $x == 5 And ($sx == 3 Or $sx == 6 Or $sx == 11))
        $num_MRs++;
      if ($y == 2 And ($sy == 7 Or $sy == 10 Or $sy == 11) And $x == 6 And ($sx == 3 Or $sx == 6 Or $sx == 11))
        $num_MRs++;

      if ($y == 3 And ($sy == 3 Or $sy == 11) And $x == 7 And ($sx == 2 Or $sx == 7 Or $sx == 12))
        $num_MRs++;
      if ($y == 3 And ($sy == 9 Or $sy == 12) And $x == 8 And ($sx == 2 Or $sx == 7 Or $sx == 12))
        $num_MRs++;
      if ($y == 3 And ($sy == 5 Or $sy == 8) And $x == 9 And ($sx == 2 Or $sx == 7 Or $sx == 12))
        $num_MRs++;
      if ($y == 3 And ($sy == 1 Or $sy == 8 Or $sy == 10) And $x == 4 And ($sx == 2 Or $sx == 7 Or $sx == 12))
        $num_MRs++;
      if ($y == 3 And ($sy == 4 Or $sy == 9 Or $sy == 12) And $x == 5 And ($sx == 2 Or $sx == 7 Or $sx == 12))
        $num_MRs++;
      if ($y == 3 And ($sy == 7 Or $sy == 10 Or $sy == 11) And $x == 6 And ($sx == 2 Or $sx == 7 Or $sx == 12))
        $num_MRs++;

      if ($y == 4 And ($sy == 3 Or $sy == 11) And $x == 7 And ($sx == 1 Or $sx == 8 Or $sx == 10))
        $num_MRs++;
      if ($y == 4 And ($sy == 9 Or $sy == 12) And $x == 8 And ($sx == 1 Or $sx == 8 Or $sx == 10))
        $num_MRs++;
      if ($y == 4 And ($sy == 5 Or $sy == 8) And $x == 9 And ($sx == 1 Or $sx == 8 Or $sx == 10))
        $num_MRs++;
      if ($y == 4 And ($sy == 4 Or $sy == 9 Or $sy == 12) And $x == 5 And ($sx == 1 Or $sx == 8 Or $sx == 10))
        $num_MRs++;
      if ($y == 4 And ($sy == 7 Or $sy == 10 Or $sy == 11) And $x == 6 And ($sx == 1 Or $sx == 8 Or $sx == 10))
        $num_MRs++;

      if ($y == 5 And ($sy == 3 Or $sy == 11) And $x == 7 And ($sx == 4 Or $sx == 9 Or $sx == 12))
        $num_MRs++;
      if ($y == 5 And ($sy == 9 Or $sy == 12) And $x == 8 And ($sx == 4 Or $sx == 9 Or $sx == 12))
        $num_MRs++;
      if ($y == 5 And ($sy == 5 Or $sy == 8) And $x == 9 And ($sx == 4 Or $sx == 9 Or $sx == 12))
        $num_MRs++;
      if ($y == 5 And ($sy == 7 Or $sy == 10 Or $sy == 11) And $x == 6 And ($sx == 4 Or $sx == 9 Or $sx == 12))
        $num_MRs++;

      if ($y == 6 And ($sy == 3 Or $sy == 11) And $x == 7 And ($sx == 7 Or $sx == 10 Or $sx == 11))
        $num_MRs++;
      if ($y == 6 And ($sy == 9 Or $sy == 12) And $x == 8 And ($sx == 7 Or $sx == 10 Or $sx == 11))
        $num_MRs++;
      if ($y == 6 And ($sy == 5 Or $sy == 8) And $x == 9 And ($sx == 7 Or $sx == 10 Or $sx == 11))
        $num_MRs++;

      if ($y == 7 And ($sy == 9 Or $sy == 12) And $x == 8 And ($sx == 3 Or $sx == 11))
        $num_MRs++;
      if ($y == 7 And ($sy == 5 Or $sy == 8) And $x == 9 And ($sx == 3 Or $sx == 11))
        $num_MRs++;

      if ($y == 8 And ($sy == 5 Or $sy == 8) And $x == 9 And ($sx == 9 Or $sx == 12))
        $num_MRs++;


      if ($x == 0 And ($sx == 4 Or $sx == 2) And $y == 1 And ($sy == 1 Or $sy == 5))
        $num_MRs++;
      if ($x == 0 And ($sx == 3 Or $sx == 11) And $y == 7 And ($sy == 1 Or $sy == 5))
        $num_MRs++;
      if ($x == 0 And ($sx == 9 Or $sx == 12) And $y == 8 And ($sy == 1 Or $sy == 5))
        $num_MRs++;
      if ($x == 0 And ($sx == 5 Or $sx == 8) And $y == 9 And ($sy == 1 Or $sy == 5))
        $num_MRs++;
      if ($x == 0 And ($sx == 3 Or $sx == 6 Or $sx == 11) And $y == 2 And ($sy == 1 Or $sy == 5))
        $num_MRs++;
      if ($x == 0 And ($sx == 2 Or $sx == 7 Or $sx == 12) And $y == 3 And ($sy == 1 Or $sy == 5))
        $num_MRs++;
      if ($x == 0 And ($sx == 1 Or $sx == 8 Or $sx == 10) And $y == 4 And ($sy == 1 Or $sy == 5))
        $num_MRs++;
      if ($x == 0 And ($sx == 4 Or $sx == 9 Or $sx == 12) And $y == 5 And ($sy == 1 Or $sy == 5))
        $num_MRs++;
      if ($x == 0 And ($sx == 7 Or $sx == 10 Or $sx == 11) And $y == 6 And ($sy == 1 Or $sy == 5))
        $num_MRs++;

      if ($x == 1 And ($sx == 3 Or $sx == 11) And $y == 7 And ($sy == 2 Or $sy == 4))
        $num_MRs++;
      if ($x == 1 And ($sx == 9 Or $sx == 12) And $y == 8 And ($sy == 2 Or $sy == 4))
        $num_MRs++;
      if ($x == 1 And ($sx == 5 Or $sx == 8) And $y == 9 And ($sy == 2 Or $sy == 4))
        $num_MRs++;
      if ($x == 1 And ($sx == 3 Or $sx == 6 Or $sx == 11) And $y == 2 And ($sy == 2 Or $sy == 4))
        $num_MRs++;
      if ($x == 1 And ($sx == 2 Or $sx == 7 Or $sx == 12) And $y == 3 And ($sy == 2 Or $sy == 4))
        $num_MRs++;
      if ($x == 1 And ($sx == 1 Or $sx == 8 Or $sx == 10) And $y == 4 And ($sy == 2 Or $sy == 4))
        $num_MRs++;
      if ($x == 1 And ($sx == 4 Or $sx == 9 Or $sx == 12) And $y == 5 And ($sy == 2 Or $sy == 4))
        $num_MRs++;
      if ($x == 1 And ($sx == 7 Or $sx == 10 Or $sx == 11) And $y == 6 And ($sy == 2 Or $sy == 4))
        $num_MRs++;


      if ($x == 2 And ($sx == 3 Or $sx == 11) And $y == 7 And ($sy == 3 Or $sy == 6 Or $sy == 11))
        $num_MRs++;
      if ($x == 2 And ($sx == 9 Or $sx == 12) And $y == 8 And ($sy == 3 Or $sy == 6 Or $sy == 11))
        $num_MRs++;
      if ($x == 2 And ($sx == 5 Or $sx == 8) And $y == 9 And ($sy == 3 Or $sy == 6 Or $sy == 11))
        $num_MRs++;
      if ($x == 2 And ($sx == 2 Or $sx == 7 Or $sx == 12) And $y == 3 And ($sy == 3 Or $sy == 6 Or $sy == 11))
        $num_MRs++;
      if ($x == 2 And ($sx == 1 Or $sx == 8 Or $sx == 10) And $y == 4 And ($sy == 3 Or $sy == 6 Or $sy == 11))
        $num_MRs++;
      if ($x == 2 And ($sx == 4 Or $sx == 9 Or $sx == 12) And $y == 5 And ($sy == 3 Or $sy == 6 Or $sy == 11))
        $num_MRs++;
      if ($x == 2 And ($sx == 7 Or $sx == 10 Or $sx == 11) And $y == 6 And ($sy == 3 Or $sy == 6 Or $sy == 11))
        $num_MRs++;


      if ($x == 3 And ($sx == 3 Or $sx == 11) And $y == 7 And ($sy == 2 Or $sy == 7 Or $sy == 12))
        $num_MRs++;
      if ($x == 3 And ($sx == 9 Or $sx == 12) And $y == 8 And ($sy == 2 Or $sy == 7 Or $sy == 12))
        $num_MRs++;
      if ($x == 3 And ($sx == 5 Or $sx == 8) And $y == 9 And ($sy == 2 Or $sy == 7 Or $sy == 12))
        $num_MRs++;
      if ($x == 3 And ($sx == 1 Or $sx == 8 Or $sx == 10) And $y == 4 And ($sy == 2 Or $sy == 7 Or $sy == 12))
        $num_MRs++;
      if ($x == 3 And ($sx == 4 Or $sx == 9 Or $sx == 12) And $y == 5 And ($sy == 2 Or $sy == 7 Or $sy == 12))
        $num_MRs++;
      if ($x == 3 And ($sx == 7 Or $sx == 10 Or $sx == 11) And $y == 6 And ($sy == 2 Or $sy == 7 Or $sy == 12))
        $num_MRs++;

      if ($x == 4 And ($sx == 3 Or $sx == 11) And $y == 7 And ($sy == 1 Or $sy == 8 Or $sy == 10))
        $num_MRs++;
      if ($x == 4 And ($sx == 9 Or $sx == 12) And $y == 8 And ($sy == 1 Or $sy == 8 Or $sy == 10))
        $num_MRs++;
      if ($x == 4 And ($sx == 5 Or $sx == 8) And $y == 9 And ($sy == 1 Or $sy == 8 Or $sy == 10))
        $num_MRs++;
      if ($x == 4 And ($sx == 4 Or $sx == 9 Or $sx == 12) And $y == 5 And ($sy == 1 Or $sy == 8 Or $sy == 10))
        $num_MRs++;
      if ($x == 4 And ($sx == 7 Or $sx == 10 Or $sx == 11) And $y == 6 And ($sy == 1 Or $sy == 8 Or $sy == 10))
        $num_MRs++;

      if ($x == 5 And ($sx == 3 Or $sx == 11) And $y == 7 And ($sy == 4 Or $sy == 9 Or $sy == 12))
        $num_MRs++;
      if ($x == 5 And ($sx == 9 Or $sx == 12) And $y == 8 And ($sy == 4 Or $sy == 9 Or $sy == 12))
        $num_MRs++;
      if ($x == 5 And ($sx == 5 Or $sx == 8) And $y == 9 And ($sy == 4 Or $sy == 9 Or $sy == 12))
        $num_MRs++;
      if ($x == 5 And ($sx == 7 Or $sx == 10 Or $sx == 11) And $y == 6 And ($sy == 4 Or $sy == 9 Or $sy == 12))
        $num_MRs++;

      if ($x == 6 And ($sx == 3 Or $sx == 11) And $y == 7 And ($sy == 7 Or $sy == 10 Or $sy == 11))
        $num_MRs++;
      if ($x == 6 And ($sx == 9 Or $sx == 12) And $y == 8 And ($sy == 7 Or $sy == 10 Or $sy == 11))
        $num_MRs++;
      if ($x == 6 And ($sx == 5 Or $sx == 8) And $y == 9 And ($sy == 7 Or $sy == 10 Or $sy == 11))
        $num_MRs++;

      if ($x == 7 And ($sx == 9 Or $sx == 12) And $y == 8 And ($sy == 3 Or $sy == 11))
        $num_MRs++;
      if ($x == 7 And ($sx == 5 Or $sx == 8) And $y == 9 And ($sy == 3 Or $sy == 11))
        $num_MRs++;

      if ($x == 8 And ($sx == 5 Or $sx == 8) And $y == 9 And ($sy == 9 Or $sy == 12))
        $num_MRs++;
    }
  }

  return $num_MRs;
}

?>
