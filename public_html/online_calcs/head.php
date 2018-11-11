<?php

include ('accesscontrol.php');

if ($is_logged_in == False)
{
	echo "You are not yet logged in.";
	exit();
}


?>