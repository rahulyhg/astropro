<?php

  // set the database access information as constants
  DEFINE ('DB_HOST', 'localhost');
  DEFINE ('DB_USER', 'root');
  DEFINE ('DB_PASSWORD', '');
  DEFINE ('DB_NAME', 'astronew');

  //make the connection
  $conn = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);      //this is procedural style
  if (!$conn) { die ("Could not connect to database"); }
?>