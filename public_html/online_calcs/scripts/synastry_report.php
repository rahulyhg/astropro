<?php

Function Generate_synastry_report($name1, $name2, $line1, $line2, $pl_name, $longitude1, $longitude2, $Asc1, $Asc2, $ubt1, $ubt2, $dynes)
{
  echo '<center><table width="61.8%" cellpadding="0" cellspacing="0" border="0">';
  echo '<tr><td><font face="Verdana" size="3">';

  //display philosophy of astrology
  echo "<center><font size='+1' color='#0000ff'><b>Synastry Report For $name1 and $name2</b></font><br /><br />";
  echo "<font size=2>" . $line1 . "</font><br /><br />";
  echo "<font size=2>" . $line2 . "</font><br /><br />";
  echo "</center>";

  $file = "synastry_files/intro.txt";
  $fh = fopen($file, "r");
  $string = fread($fh, filesize($file));
  fclose($fh);

  $intro = nl2br($string);
  echo "<font size=2>" . $intro . "</font>";


  //display planetary interaspect interpretations
  echo "<br /><center><font size='+1' color='#0000ff'><b>MEANINGS OF INTERPLANETARY ASPECTS</b></font></center><br />";

  $num_aspects = 0;
  $aspect_text = array();
  
  for ($i = 0; $i <= 9; $i++)   //NATAL person
  {
    for ($j = 0; $j <= 9; $j++)   //2nd person
    {
      if (($i == 1 And $ubt1 == 1) Or ($j == 1 And $ubt2 == 1))
      {
        continue;     // do not allow Moon aspects if persons birth time is unknown
      }

      $da = Abs($longitude1[$i] - $longitude2[$j]);
      if ($da > 180) { $da = 360 - $da; }

      // set orb - 8 if Sun or Moon, 6 if not Sun or Moon
      if ($i == 0 Or $i == 1 Or $j == 0 Or $j == 1)
      {
        $orb = 8;
      }
      else
      {
        $orb = 6;
      }

      // are planets within orb?
      $q = 1;
      if ($da <= $orb)
      {
        $q = 2;
      }
      elseif (($da <= 60 + $orb) And ($da >= 60 - $orb))
      {
        $q = 3;
      }
      elseif (($da <= 90 + $orb) And ($da >= 90 - $orb))
      {
        $q = 4;
      }
      elseif (($da <= 120 + $orb) And ($da >= 120 - $orb))
      {
        $q = 5;
      }
      elseif ($da >= 180 - $orb)
      {
        $q = 6;
      }

      if ($q > 1)
      {
        if ($q == 2)
        {
          $aspect = " conjunct ";
          $ext = "cnj";
        }
        elseif ($q == 3)
        {
          $aspect = " sextile ";
          $ext = "sxt";
        }
        elseif ($q == 4)
        {
          $aspect = " square ";
          $ext = "sqr";
        }
        elseif ($q == 5)
        {
          $aspect = " trine ";
          $ext = "tri";
        }
        elseif ($q == 6)
        {
          $aspect = " opposite ";
          $ext = "opp";
        }

        $phrase_to_look_for = $pl_name[$i] . $aspect . $pl_name[$j];

        if ($j < $i)
        {
          $the_phrase = $pl_name[$j] . $aspect . $pl_name[$i];
          $file = "synastry_files/" . left(strtolower($pl_name[$j]), 3) . left(strtolower($pl_name[$i]), 3) . "." . $ext;
        }
        else
        {
          $the_phrase = $pl_name[$i] . $aspect . $pl_name[$j];
          $file = "synastry_files/" . left(strtolower($pl_name[$i]), 3) . left(strtolower($pl_name[$j]), 3) . "." . $ext;
        }

        if (file_exists($file))
        {
          $fh = fopen($file, "r");
          $string = fread($fh, filesize($file));
          fclose($fh);

          $txt = Finalize_text($string, $name1, $pl_name[$i], $name2, $pl_name[$j], $aspect, $the_phrase, $i, $j, $dynes, $q);

          $aspect_text[0][$num_aspects] = $dynes[$i][$j];
          $aspect_text[1][$num_aspects] = $txt;
        
          $num_aspects++;
        }
      }
    }
  }


  if ($ubt1 == 0)
  {
    $longitude2[10] = $Asc2;
    for ($i = 0; $i <= 9; $i++)   //$i is 2nd person
    {
      if ($i == 1 And $ubt2 == 1)
      {
        continue;     // do not allow Moon or Ascendant aspects if birth time for person #2 is unknown
      }

      $da = Abs($longitude2[$i] - $Asc1);
      if ($da > 180) { $da = 360 - $da; }

      // set orb - 8 if Sun or Moon, 6 if not Sun or Moon
      if ($i == 0 Or $i == 1)
      {
        $orb = 8;
      }
      else
      {
        $orb = 6;
      }

      // are planets within orb?
      $q = 1;
      if ($da <= $orb)
      {
        $q = 2;
      }
      elseif (($da <= 60 + $orb) And ($da >= 60 - $orb))
      {
        $q = 3;
      }
      elseif (($da <= 90 + $orb) And ($da >= 90 - $orb))
      {
        $q = 4;
      }
      elseif (($da <= 120 + $orb) And ($da >= 120 - $orb))
      {
        $q = 5;
      }
      elseif ($da >= 180 - $orb)
      {
        $q = 6;
      }

      if ($q > 1)
      {
        if ($q == 2)
        {
          $aspect = " conjunct ";
          $ext = "cnj";
        }
        elseif ($q == 3)
        {
          $aspect = " sextile ";
          $ext = "sxt";
        }
        elseif ($q == 4)
        {
          $aspect = " square ";
          $ext = "sqr";
        }
        elseif ($q == 5)
        {
          $aspect = " trine ";
          $ext = "tri";
        }
        elseif ($q == 6)
        {
          $aspect = " opposite ";
          $ext = "opp";
        }

        $phrase_to_look_for = $pl_name[$i] . $aspect . "Ascendant";
        $file = "synastry_files/" . left(strtolower($pl_name[$i]), 3) . "asc." . $ext;

        if (file_exists($file))
        {
          $fh = fopen($file, "r");
          $string = fread($fh, filesize($file));
          fclose($fh);

          $txt = Finalize_text($string, $name1, "Ascendant", $name2, $pl_name[$i], $aspect, $phrase_to_look_for, 10, $i, $dynes, $q);

          $aspect_text[0][$num_aspects] = $dynes[10][$i];
          $aspect_text[1][$num_aspects] = $txt;

          $num_aspects++;
        }
      }
    }
  }


  if ($ubt2 == 0)
  {
    $longitude1[10] = $Asc1;
    for ($i = 0; $i <= 9; $i++)   //$i is NATAL person
    {
      if ($i == 1 And $ubt1 == 1)
      {
        continue;     // do not allow Moon or Ascendant aspects if birth time for NATAL person is unknown
      }

      $da = Abs($longitude1[$i] - $Asc2);
      if ($da > 180) { $da = 360 - $da; }

      // set orb - 8 if Sun or Moon, 6 if not Sun or Moon
      if ($i == 0 Or $i == 1)
      {
        $orb = 8;
      }
      else
      {
        $orb = 6;
      }

      // are planets within orb?
      $q = 1;
      if ($da <= $orb)
      {
        $q = 2;
      }
      elseif (($da <= 60 + $orb) And ($da >= 60 - $orb))
      {
        $q = 3;
      }
      elseif (($da <= 90 + $orb) And ($da >= 90 - $orb))
      {
        $q = 4;
      }
      elseif (($da <= 120 + $orb) And ($da >= 120 - $orb))
      {
        $q = 5;
      }
      elseif ($da >= 180 - $orb)
      {
        $q = 6;
      }

      if ($q > 1)
      {
        if ($q == 2)
        {
          $aspect = " conjunct ";
          $ext = "cnj";
        }
        elseif ($q == 3)
        {
          $aspect = " sextile ";
          $ext = "sxt";
        }
        elseif ($q == 4)
        {
          $aspect = " square ";
          $ext = "sqr";
        }
        elseif ($q == 5)
        {
          $aspect = " trine ";
          $ext = "tri";
        }
        elseif ($q == 6)
        {
          $aspect = " opposite ";
          $ext = "opp";
        }

        $phrase_to_look_for = $pl_name[$i] . $aspect . "Ascendant";
        $file = "synastry_files/" . left(strtolower($pl_name[$i]), 3) . "asc." . $ext;

        if (file_exists($file))
        {
          $fh = fopen($file, "r");
          $string = fread($fh, filesize($file));
          fclose($fh);

          $txt = Finalize_text($string, $name1, $pl_name[$i], $name2, "Ascendant", $aspect, $phrase_to_look_for, $i, 10, $dynes, $q);

          $aspect_text[0][$num_aspects] = $dynes[$i][10];
          $aspect_text[1][$num_aspects] = $txt;

          $num_aspects++;
        }
      }
    }
  }


  if ($ubt1 == 0 And $ubt2 == 0)
  {
    $da = Abs($Asc1 - $Asc2);
    if ($da > 180) { $da = 360 - $da; }

    $orb = 6;

    // are Ascendants within orb?
    $q = 1;
    if ($da <= $orb)
    {
      $q = 2;
    }
    elseif (($da <= 60 + $orb) And ($da >= 60 - $orb))
    {
      $q = 3;
    }
    elseif (($da <= 90 + $orb) And ($da >= 90 - $orb))
    {
      $q = 4;
    }
    elseif (($da <= 120 + $orb) And ($da >= 120 - $orb))
    {
      $q = 5;
    }
    elseif ($da >= 180 - $orb)
    {
      $q = 6;
    }

    if ($q > 1)
    {
      if ($q == 2)
      {
        $aspect = " conjunct ";
        $ext = "cnj";
      }
      elseif ($q == 3)
      {
        $aspect = " sextile ";
        $ext = "sxt";
      }
      elseif ($q == 4)
      {
        $aspect = " square ";
        $ext = "sqr";
      }
      elseif ($q == 5)
      {
        $aspect = " trine ";
        $ext = "tri";
      }
      elseif ($q == 6)
      {
        $aspect = " opposite ";
        $ext = "opp";
      }

      $file = "synastry_files/ascasc." . $ext;

      if (file_exists($file))
      {
        $fh = fopen($file, "r");
        $string = fread($fh, filesize($file));
        fclose($fh);

        $txt = Finalize_text($string, $name1, "Ascendant", $name2, "Ascendant", $aspect, "Ascendant " . $aspect . " Ascendant", 10, 10, $dynes, $q);

        $aspect_text[0][$num_aspects] = $dynes[10][10];
        $aspect_text[1][$num_aspects] = $txt;

        $num_aspects++;
      }
    }
  }


  //now sort the aspect interpretations according to power
  array_multisort($aspect_text[0], SORT_NUMERIC, SORT_ASC, $aspect_text[1], SORT_REGULAR);
  
  $p_aspect_interp = "";
  for ($i = 0; $i <= $num_aspects - 1; $i++)
  {
    $p_aspect_interp .= $aspect_text[1][$i];
  }

  //echo "<font size='2'>" . $p_aspect_interp . "</font>";
  echo $p_aspect_interp;

  echo "<center><font size='+1' color='#0000ff'><b>FINAL THOUGHTS</b></font></center><br />";

  //display closing
  $file = "synastry_files/closing.txt";
  $fh = fopen($file, "r");
  $string = fread($fh, filesize($file));
  fclose($fh);

  $closing = nl2br($string);
  echo "<font size=2>" . $closing . "</font>";

  echo '</font></td></tr>';
  echo '</table></center>';
  echo "<br /><br />";
}


Function left($leftstring, $leftlength)
{
  return(substr($leftstring, 0, $leftlength));
}


Function Find_Specific_Report_Paragraph($phrase_to_look_for, $file)
{
  $string = "";
  $len = strlen($phrase_to_look_for);

  //put entire file contents into an array, line by line
  $file_array = file($file);

  // look through each line searching for $phrase_to_look_for
  for($i = 0; $i < count($file_array); $i++)
  {
    if (left(trim($file_array[$i]), $len) == $phrase_to_look_for)
    {
      $flag = 0;
      while (trim($file_array[$i]) != "*")
      {
        if ($flag == 0)
        {
          $string .= "<b>" . $file_array[$i] . "</b>";
        }
        else
        {
          $string .= $file_array[$i];
        }
        $flag = 1;
        $i++;
      }
      break;
    }
  }

  return $string;
}


Function Finalize_text($text, $name1, $pl_name1, $name2, $pl_name2, $aspect, $phrase_to_look_for, $p1, $p2, $dynes, $q)
{
//$p1 is planet number for natal person and $p2 is planet number for 2nd person
  $interp = str_replace(chr(13) . chr(10) . chr(13) . chr(10), "XYZZ", $text);
  $interp = str_replace(chr(13) . chr(10), " ", $interp);
  $interp = str_replace("XYZZ", chr(13) . chr(10) . chr(13) . chr(10), $interp);
  $interp = nl2br($interp);

  $interp = str_replace($phrase_to_look_for, "VCDFE", $interp);


//  if ($pl_name1 != "Ascendant" And $pl_name2 != "Ascendant")
//  {
//    $interp = str_replace($pl_name1, $name1, $interp);
//    $interp = str_replace($pl_name2, $name2, $interp);
//  }
  

  if ($pl_name1 == "Ascendant" And $pl_name2 == "Ascendant")
  {
  }
  else
  {
    $interp = str_replace($pl_name1, $name1, $interp);
    $interp = str_replace($pl_name2, $name2, $interp);
  }


  $interp = str_replace("VCDFE", $phrase_to_look_for, $interp);

  if ($pl_name1 != $pl_name2)
  {
    if ($dynes[$p1][$p2] > 0)
    {
      $t = "<b>" . $name1 . " is " . $pl_name1 . " and " . $name2 . " is " . $pl_name2 . "</b> (harmony = " . sprintf("%.2f", $dynes[$p1][$p2]) . ") -- ";
    }
    elseif ($dynes[$p1][$p2] == 0)
    {
      if ($q == 3 Or $q == 5)
      {
        $t = "<b>" . $name1 . " is " . $pl_name1 . " and " . $name2 . " is " . $pl_name2 . "</b> (this aspect is mildly harmonious) -- ";    
      }
      elseif ($q == 4 Or $q == 6)
      {
        $t = "<b>" . $name1 . " is " . $pl_name1 . " and " . $name2 . " is " . $pl_name2 . "</b> (this aspect is mildly discordant) -- ";    
      }
      else
      {
        $t = "<b>" . $name1 . " is " . $pl_name1 . " and " . $name2 . " is " . $pl_name2 . "</b> (this aspect is neutral and could go either way) -- ";
      }
    }
    else
    {
      $t = "<b>" . $name1 . " is " . $pl_name1 . " and " . $name2 . " is " . $pl_name2 . "</b> (discord = " . sprintf("%.2f", $dynes[$p1][$p2]) . ") -- ";
    }

    return "<font size=2>" . $t . $interp . "</font><br />";
  }
  else
  {
    if ($dynes[$p1][$p2] > 0)
    {
      return "<font size=2>(harmony = " . sprintf("%.2f", $dynes[$p1][$p2]) . ") -- " . $interp . "</font><br />";
    }
    elseif ($dynes[$p1][$p2] == 0)
    {
      if ($q == 3 Or $q == 5)
      {
        return "<font size=2>(this aspect is mildly harmonious) -- " . $interp . "</font><br />";    
      }
      elseif ($q == 4 Or $q == 6)
      {
        return "<font size=2>(this aspect is mildly discordant) -- " . $interp . "</font><br />";    
      }
      else
      {
        return "<font size=2>(this aspect is neutral and could go either way) -- " . $interp . "</font><br />";
      }
    }
    else
    {
      return "<font size=2>(discord = " . sprintf("%.2f", $dynes[$p1][$p2]) . ") -- " . $interp . "</font><br />";
    }
  }
}

?>
