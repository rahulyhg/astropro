<?php

session_start();
$username = $_SESSION['username'];
  //here is where I want to create a pdf file
  require('./fpdf.php');

  class PDF extends FPDF
  {
    var $B;
    var $I;
    var $U;
    var $HREF;

    // Page header
    function Header()
    {

    }

    // Page footer
    function Footer()
    {
      // Position at 1.5 cm from bottom
      $this->SetY(-15);
      $this->SetFont('Arial','I',8);
      $this->Cell(0,10,'Page '.$this->PageNo().' of {nb}',0,0,'C');
    }

    function PDF($orientation='P', $unit='mm', $size='Letter')
    {
      // Call parent constructor
      $this->FPDF($orientation,$unit,$size);

      // Initialization
      $this->B = 0;
      $this->I = 0;
      $this->U = 0;
      $this->HREF = '';
    }

    function WriteHTML($html)
    {
      // HTML parser
      $html = str_replace("\n",' ',$html);
      $a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
      foreach($a as $i=>$e)
      {
        if($i%2==0)
        {
          // Text
          if($this->HREF)
            $this->PutLink($this->HREF,$e);
          else
            $this->Write(5,$e);
        }
        else
        {
          // Tag
          if($e[0]=='/')
            $this->CloseTag(strtoupper(substr($e,1)));
          else
          {
            // Extract attributes
            $a2 = explode(' ',$e);
            $tag = strtoupper(array_shift($a2));
            $attr = array();
            foreach($a2 as $v)
            {
              if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                $attr[strtoupper($a3[1])] = $a3[2];
            }

            $this->OpenTag($tag,$attr);
          }
        }
      }
    }

    function OpenTag($tag, $attr)
    {
      // Opening tag
      if($tag=='B' || $tag=='I' || $tag=='U')
        $this->SetStyle($tag,true);
      if($tag=='A')
        $this->HREF = $attr['HREF'];
      if($tag=='BR')
        $this->Ln(5);
    }

    function CloseTag($tag)
    {
      // Closing tag
      if($tag=='B' || $tag=='I' || $tag=='U')
        $this->SetStyle($tag,false);
      if($tag=='A')
        $this->HREF = '';
    }

    function SetStyle($tag, $enable)
    {
      // Modify style and select corresponding font
      $this->$tag += ($enable ? 1 : -1);
      $style = '';
      foreach(array('B', 'I', 'U') as $s)
      {
        if($this->$s>0)
          $style .= $s;
      }

      $this->SetFont('',$style);
    }

    function PutLink($URL, $txt)
    {
      // Put a hyperlink
      $this->SetTextColor(0,0,255);
      $this->SetStyle('U',true);
      $this->Write(5,$txt,$URL);
      $this->SetStyle('U',false);
      $this->SetTextColor(0);
    }
  }



  // Instantiation of inherited class
  $pdf = new PDF('P', 'mm', 'Letter');              //'L' defines landscape mode, 'P' defines portrait mode
  $width_of_pdf = 216;                              //specify width of pdf

  $pdf->SetTitle("Astrological Natal Chart");
  $pdf->SetAuthor('Allen Edwall');
  $pdf->AliasNbPages();
  
  $left_margin = 26;
  $right_margin = 26;


  $pdf->AddPage();
  
  $pdf->Ln(6);

  $pdf->SetFont('Times','',22);
  $text = "Astrological Natal Chart";
  $pdf->Cell(0, 0, $text, 0, 1, C);

  $pdf->Ln(10);

//main heading of report - title
  $pdf->SetFont('Times','',16);
  $text = "Prepared for: " . $restored_name;
  $pdf->Cell(0, 0, $text, 0, 1, C);

  $pdf->Ln(8);

  $pdf->SetFont('Times','',12);
  $text = $profile_birthdata;
  $pdf->Cell(0, 0, $text, 0, 1, C);


  $pdf->SetLeftMargin($left_margin);             //if this isn't before the Ln(), then the next text margin is indented too far inward
  $pdf->SetRightMargin($right_margin);           //if this isn't before the Ln(), then the next text margin is indented too far inward
  $pdf->Ln(16);


  $server_name = strtolower($_SERVER['SERVER_NAME']);
  $crlf = "\n";
  if ($server_name == "localhost" Or $server_name == "zzee.php.gui") { $crlf = "\r\n"; }
  
  
//"My Philosophy of Astrology"
  $pdf->SetFont('Arial','B',11);
  $pdf->Cell(0, 0, "MY PHILOSOPHY OF ASTROLOGY", 0, 1, L);
  $pdf->SetFont('Arial','',11);

  $file = "natal_files/philo.txt";
  $fh = fopen($file, "r");
  $string = fread($fh, filesize($file));
  fclose($fh);

  $philo = nl2br($string);
  $philo = str_replace($crlf, "", $philo);
  print_justified_paragraphs($philo, $pdf);
  


  //display the chartwheel
  $pdf->Ln(10);

  $pdf->SetFont('Times','',12);
  $text = $restored_name;
  $pdf->Cell(0, 0, $text, 0, 1, C);

  $pdf->Ln(6);

  $text = $profile_birthdata;
  $pdf->Cell(0, 0, $text, 0, 1, C);

  $pdf->Ln(6);

  $filename = $_SESSION['chartwheel_filename'];
  $x_axis_value = center_this_graphic_horizontally($filename, $width_of_pdf);
  $pdf->Image($filename,$x_axis_value,NULL,0,0,'');

  $pdf->SetFont('Arial','',11);
  

  $pdf->AddPage();
  
  $pdf->Ln(6);

  if ($ubt1 == 0)
  {
    $pdf->SetFont('Arial','B',11);
    $pdf->Cell(0, 0, "THE RISING SIGN OR ASCENDANT", 0, 1, L);
    $pdf->SetFont('Arial','',11);

    $pdf->Ln(8);
  
    $file = "natal_files/ascsign.txt";
    $fh = fopen($file, "r");
    $string = fread($fh, filesize($file));
    fclose($fh);

    $text = nl2br($string);
    $text = str_replace($crlf, "", $text);
    //$pdf->WriteHTML($text);
    print_justified_paragraphs($text, $pdf);

    $pdf->Ln(6);

    $text = "<b>YOUR ASCENDANT IS:</b>";
    $pdf->WriteHTML($text);

    $pdf->Ln(6);

    $s_pos = floor($hc1[1] / 30) + 1;
    $phrase_to_look_for = $sign_name[$s_pos] . " rising";
    $file = "natal_files/rising.txt";
    $string = Find_Specific_Report_Paragraph($phrase_to_look_for, $file);
    $string = nl2br($string);
    $string = str_replace($crlf, "", $string);

    //$pdf->WriteHTML($string);
    print_justified_paragraphs($string, $pdf);
    
    $pdf->Ln(16);
  }



  $pdf->SetFont('Arial','B',11);
  $pdf->Cell(0, 0, "PLANETARY ASPECTS", 0, 1, L);
  $pdf->SetFont('Arial','',11);

  $file = "natal_files/aspect.txt";
  $fh = fopen($file, "r");
  $string = fread($fh, filesize($file));
  fclose($fh);

  $text = nl2br($string);
  $text = str_replace($crlf, "", $text);
  //$pdf->WriteHTML($text);
  print_justified_paragraphs($text, $pdf);

  $pdf->Ln(6);


  // get the individual power and harmony for each aspect
  include ('dyne_aspect_p_h.php');
  GetAspectPowerHarmony($longitude1, $house_pos1, $ubt1, $p_h, LAST_PLANET);

  $num_aspects = 0;
  $aspect_text = array();

  // loop through each planet
  for ($i = 0; $i <= LAST_PLANET + 1; $i++)         //was 8
  {
    for ($j = $i + 1; $j <= LAST_PLANET + 2; $j++)          //was 9
    {
      if (($i == 1 Or $i == SE_POF Or $i == SE_VERTEX Or $i == LAST_PLANET + 1 Or $i == LAST_PLANET + 2 Or $j == 1 Or $j == SE_POF Or $j == SE_VERTEX Or $j == LAST_PLANET + 1 Or $j == LAST_PLANET + 2) And $ubt1 == 1)
      {
        continue;           // do not allow Moon aspects or PoF or Vertex aspects if birth time is unknown
      }

      $da = Abs($longitude1[$i] - $longitude1[$j]);
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
          $aspect = " blending with ";
        }
        elseif ($q == 3 Or $q == 5)
        {
          $aspect = " harmonizing with ";
        }
        elseif ($q == 4 Or $q == 6)
        {
          $aspect = " discordant to ";
        }

        $phrase_to_look_for = $pl_name[$i] . $aspect . $pl_name[$j];
        $file = "natal_files/" . strtolower($pl_name[$i]) . ".txt";
        $string = Find_Specific_Report_Paragraph_ASPECTS($phrase_to_look_for, $file, $i, $j, $p_h, $crlf);
        $string = nl2br($string);
        $string = str_replace($crlf, "", $string);

        $aspect_text[0][$num_aspects] = $p_h[$i][$j];
        $aspect_text[1][$num_aspects] = $string;
    
        $num_aspects++;
      }
    }
  }

  //now sort the aspect interpretations according to power
  array_multisort($aspect_text[0], SORT_NUMERIC, SORT_DESC, $aspect_text[1], SORT_REGULAR);

  $p_aspect_interp = "";
  for ($i = 0; $i <= $num_aspects - 1; $i++)
  {
    if ($aspect_text[1][$i] != "")
    {
      //$p_aspect_interp .= $aspect_text[1][$i];
      print_justified_paragraphs($aspect_text[1][$i], $pdf);
      $pdf->Ln(4);
    }
  }

  //$pdf->WriteHTML($p_aspect_interp);     
  //print_justified_paragraphs($p_aspect_interp, $pdf);
  

  $pdf->AddPage();
  
  $pdf->Ln(6);
  
  
  $pdf->SetFont('Arial','B',11);
  $pdf->Cell(0, 0, "SIGN POSITIONS OF PLANETS", 0, 1, L);
  $pdf->SetFont('Arial','',11);

  $pdf->Ln(8);

  $file = "natal_files/sign.txt";
  $fh = fopen($file, "r");
  $string = fread($fh, filesize($file));
  fclose($fh);

  $text = nl2br($string);
  $text = str_replace($crlf, "", $text);
  $sign_interp = $text;

  // loop through each planet
  for ($i = 0; $i <= LAST_PLANET; $i++)         //was 6
  {
    $s_pos = floor($longitude1[$i] / 30) + 1;

    $deg = Reduce_below_30($longitude1[$i]);
    if ($ubt1 == 1 And $i == 1 And ($deg < 7.7 Or $deg > 22.3)) { continue; }    //if the Moon is too close to the beginning or the end of a sign, then do not include it

    $phrase_to_look_for = $pl_name[$i] . " in";
    $file = "natal_files/sign_" . trim($s_pos) . ".txt";
    $string = Find_Specific_Report_Paragraph($phrase_to_look_for, $file);

    $string = nl2br($string);
    $string = str_replace($crlf, "", $string);
    //$sign_interp .= $string;
    
    print_justified_paragraphs($string, $pdf);
    $pdf->Ln(4);
  }

  //$pdf->WriteHTML($sign_interp);
  //print_justified_paragraphs($sign_interp, $pdf);


  if ($ubt1 == 0)
  {
    $pdf->AddPage();

    $pdf->Ln(6);
  
  
    $pdf->SetFont('Arial','B',11);
    $pdf->Cell(0, 0, "HOUSE POSITIONS OF PLANETS", 0, 1, L);
    $pdf->SetFont('Arial','',11);

    $pdf->Ln(8);

    $file = "natal_files/house.txt";
    $fh = fopen($file, "r");
    $string = fread($fh, filesize($file));
    fclose($fh);

    $text = nl2br($string);
    $text = str_replace($crlf, "", $text);
    $house_interp = $txt;

    // loop through each planet
    for ($i = 0; $i <= LAST_PLANET; $i++)               //was 9
    {
      $h_pos = $house_pos1[$i];
      $phrase_to_look_for = $pl_name[$i] . " in";
      $file = "natal_files/house_" . trim($h_pos) . ".txt";
      $string = Find_Specific_Report_Paragraph($phrase_to_look_for, $file);

      $string = nl2br($string);
      $string = str_replace($crlf, "", $string);
      //$house_interp .= $string;

      print_justified_paragraphs($string, $pdf);
      $pdf->Ln(4);
    }

    //$pdf->WriteHTML($house_interp);
    //print_justified_paragraphs($house_interp, $pdf);
  }
  

  $pdf->AddPage();

  $pdf->Ln(6);

  
  $pdf->SetFont('Arial','B',11);
  $pdf->Cell(0, 0, "CLOSING COMMENTS", 0, 1, L);
  $pdf->SetFont('Arial','',11);

  $pdf->Ln(4);
  
  $file = "natal_files/closing.txt";
  if ($ubt1 == 1) { $file = "natal_files/closing_unk.txt"; }

  $fh = fopen($file, "r");
  $string = fread($fh, filesize($file));
  fclose($fh);

  $closing = nl2br($string);
  $closing = str_replace($crlf, "", $closing);
  //$pdf->WriteHTML($closing);
  print_justified_paragraphs($closing, $pdf);



  //display the aspect grid
  $pdf->SetLeftMargin($left_margin);           //if this isn't before the Ln(), then the next text margin is indented too far inward
  $pdf->SetRightMargin($right_margin);         //if this isn't before the Ln(), then the next text margin is indented too far inward

  $pdf->Ln(6);

  $pdf->SetFont('Times','',16);
  $text = $restored_name;
  $pdf->Cell(0, 0, $text, 0, 1, C);

  $pdf->Ln(6);

  $pdf->SetFont('Times','',12);
  $text = $profile_birthdata;
  $pdf->Cell(0, 0, $text, 0, 1, C);

  $pdf->Ln(6);

  $filename = $_SESSION['grids_filename'];
  $x_axis_value = center_this_graphic_horizontally($filename, $width_of_pdf);
  $pdf->Image($filename,$x_axis_value,NULL,0,0,'');

  $pdf->SetFont('Arial','',11);



//add the closing information
  $pdf->Ln(8);
  
  $pdf->SetFont('Arial','',9);
  $pdf->Cell(0, 0, "Created by:", 0, 1, C);

  $pdf->Ln(6);

  $pdf->SetFont('Arial','',9);
  $text = "Allen Edwall";
  $text_width = $pdf->GetStringWidth($text);          //get width of text  
  $x_axis_value = center_this_HTML_text_horizontally($text_width, $width_of_pdf);
  $pdf->SetLeftMargin($x_axis_value);
  $pdf->WriteHTML("<center><a href='http://www.astrowin.org/'>" . $text . "</a></center>");
  $pdf->SetLeftMargin($left_margin);

  $pdf->Ln(10);
  
  $pdf->SetFont('Arial','',9);
  $pdf->Cell(0, 0, "736 Center Dr #328, San Marcos, CA 92069", 0, 1, C);

  $pdf->Ln(8);
  
  $pdf->SetFont('Arial','',9);
  $pdf->Cell(0, 0, "Copyright 2012 by Allen Edwall. All rights reserved.", 0, 1, C);

  $pdf->Ln(8);
  
  $pdf->SetFont('Arial','',9);
  $pdf->Cell(0, 0, "Phone: 760.294.9577", 0, 1, C);

  //generate pdf to download
  $filename="$username.pdf";
  $pdf->Output($filename,'D');
 
  exit();


Function center_this_graphic_horizontally($filename, $width_of_pdf)
{
  //get dimensions of graphic - 3.78 px = 1 mm (we assume there are 96 dpi in each graphic)
  $size = getimagesize($filename);
  
  $graphic_width = $size[0] / 3.78;         //graphic width in mm
  
  $x_axis_value = ($width_of_pdf - $graphic_width) / 2;
  
  return $x_axis_value;
}


Function center_this_HTML_text_horizontally($width, $width_of_pdf)
{
  return ($width_of_pdf - $width) / 2;
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
      while (trim($file_array[$i]) != "*")
      {
        $string .= $file_array[$i];

        $i++;
      }

      break;
    }
  }

  return $string;
}


Function Find_Specific_Report_Paragraph_ASPECTS($phrase_to_look_for, $file, $x, $y, $p_h, $crlf)
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
          if ($p_h[$y][$x] == 0)
          {
            $t = " (power = " . sprintf("%.2f", $p_h[$x][$y]) . " and this aspect is neutral)";
          }
          if ($p_h[$y][$x] > 0)
          {
            $t = " (power = " . sprintf("%.2f", $p_h[$x][$y]) . " and this aspect is harmonious = " . sprintf("%.2f", $p_h[$y][$x]) . ")";
          }
          if ($p_h[$y][$x] < 0)
          {
            $t = " (power = " . sprintf("%.2f", $p_h[$x][$y]) . " and this aspect is discordant = " . sprintf("%.2f", $p_h[$y][$x]) . ")";
          }

          if ($crlf == "\n")
          {
            $string .= left($file_array[$i], strlen($file_array[$i]) - 1) . $t . $crlf;
          }
          else
          {
            $string .= left($file_array[$i], strlen($file_array[$i]) - 2) . $t . $crlf;
          }
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


Function print_justified_paragraphs($text, $pdf)
{
  $x = explode("<br />", $text);
  
  for($i = 0; $i < count($x) - 1; $i++)
  {
    $pdf->MultiCell(0,5,$x[$i]);
  }      
}

?>
