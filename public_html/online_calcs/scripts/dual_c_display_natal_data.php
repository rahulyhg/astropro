<?php

Function DisplayNatalData($pl_name, $longitude, $declination, $house_pos, $hr_ob, $min_ob)
{
  $unknown_time = 0;
  if (($hr_ob == 12) And ($min_ob == 0))
  {
    $unknown_time = 1;		// this person has an unknown birth time
  }

  echo '<table width="50%" cellpadding="0" cellspacing="0" border="0">',"\n";

  echo '<tr>';
  echo "<td><font color='#0000ff'><b> Name </b></font></td>";
  echo "<td><font color='#0000ff'><b> Longitude </b></font></td>";
  echo "<td><font color='#0000ff'><b> Declination </b></font></td>";
  if ($unknown_time == 1)
  {
    echo "<td>&nbsp;</td>";
  }
  else
  {
    echo "<td><font color='#0000ff'><b> House<br>position </b></font></td>";
  }
  echo '</tr>';

  for ($i = 0; $i <= 9; $i++)
  {
    echo '<tr>';
    echo "<td>" . $pl_name[$i] . "</td>";
    echo "<td><font face='Courier New'>" . Convert_Longitude($longitude[$i]) . "</font></td>";
    echo "<td>" . Convert_Declination($declination[$i]) . "</td>";
    if ($unknown_time == 1)
    {
      echo "<td>&nbsp;</td>";
    }
    else
    {
      $hse = floor($house_pos[$i]);
      if ($hse < 10)
      {
        echo "<td>&nbsp; " . $hse . "</td>";
      }
      else
      {
        echo "<td>" . $hse . "</td>";
      }
    }
    echo '</tr>';
  }

  echo '<tr>';
  echo "<td> &nbsp </td>";
  echo "<td> &nbsp </td>";
  echo "<td> &nbsp </td>";
  echo "<td> &nbsp </td>";
  echo '</tr>';

  if ($unknown_time == 0)
  {
    echo '<tr>';
    echo "<td><font color='#0000ff'><b> Name </b></font></td>";
    echo "<td><font color='#0000ff'><b> Longitude </b></font></td>";
    echo "<td><font color='#0000ff'><b> Declination </b></font></td>";
    echo "<td> &nbsp </td>";
    echo '</tr>';

    for ($i = 10; $i <= 21; $i++)
    {
      echo '<tr>';
      if ($i == 10)
      {
        echo "<td>Ascendant </td>";
      }
      elseif ($i == 19)
      {
        echo "<td>MC (Midheaven) </td>";
      }
      else
      {
        echo "<td>House " . ($i - 9) . "</td>";
      }
      echo "<td><font face='Courier New'>" . Convert_Longitude($longitude[$i]) . "</font></td>";
      echo "<td>" . Convert_Declination($declination[$i]) . "</td>";
      echo "<td> &nbsp </td>";
      echo '</tr>';
    }
  }

  echo '</table>',"\n";
  echo "<br /><br />";
}

Function Convert_Longitude($longitude)
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

Function Convert_Declination($declination)
{
  $deg = floor(abs($declination));
  $min = round((abs($declination) - $deg) * 60);

  if ($deg < 10)
  {
    $deg = "0" . $deg;
  }

  if ($min < 10)
  {
    $min = "0" . $min;
  }

  if ($declination < 0)
  {
    return $deg . " S " . $min;
  }
  else
  {
    return $deg . " N " . $min;
  }
}

?>
