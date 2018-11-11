<?php
session_start();
session_unset();
session_destroy();
ob_start();
header("index.php");
ob_end_flush();
include 'index.php';
//include 'home.php';
exit();
?>