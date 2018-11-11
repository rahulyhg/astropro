<?php
include ('../accesscontrol.php');

if ($is_logged_in == False)
{
	echo "You are not yet logged in.";
	exit();
}
require_once ('../../../mysqli_connect_online_calcs_db_MYSQLI.php');

include('../constants.php');           //nedded because of "../footer.html" statements
include '../header.html';
include '../nav.html';



?>
<body>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<h1><strong>Welcome to HELp!</strong></h1>
		</div>
	</div>
</div>
</body>
