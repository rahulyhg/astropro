<?php

Function safeEscapeString($conn, $string)           //note that this function expects the connection string as an argument
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
    return addcslashes($temp2, '%');            //I don't think get_magic_quotes_gpc is active any more, so this is probably not necessary
  }
  else
  {
    $t1 = mysqli_real_escape_string($conn, $temp2);
    
    return addcslashes($t1, '%');               //this escapes the '%' and '_' characters
  }
}


Function mysqli_real_escape_string_mimic($inp)
{
  if(is_array($inp))
    return array_map(__METHOD__, $inp);

// replace HTML tags '<>' with '[]'
  $temp1 = str_replace("<", "[", $inp);
  $temp2 = str_replace(">", "]", $temp1);

// but keep <br> or <br />
// turn <br> into <br /> so later it will be turned into ""
// using just <br> will add extra blank lines
  $temp1 = str_replace("[br]", "<br />", $temp2);
  $temp2 = str_replace("[br /]", "<br />", $temp1);

  if(!empty($temp2) && is_string($temp2))
  {
    return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $temp2);
  }

  return $temp2;
}


Function validate_email($email)
{
  return (ereg('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+'. '@'. '[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.' . '[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$', $email));
}


Function post_back_message($form_name)
{
  //$form_name is something like "Forgot password"
  $msg = "<br>Please close this window to return to the<br>'$form_name' form and correct your error.<br><br>";
  $msg .= "<br><form><input class='btn btn_purple' type='button' value=' Close Window ' onClick='window.location=`../templates/page_03.php`;'></form><br>";
  return($msg);
}

?>
