<?php

error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ob_start();
session_start();

require('src/Facebook/autoload.php');

define('DB_DRIVER', 'mysql');
define('DB_SERVER', 'localhost');
define('DB_SERVER_USERNAME', 'aeronnic_aeronni');
define('DB_SERVER_PASSWORD', '9523385');
define('DB_DATABASE', 'aeronnic_astro');

// site URL and facebook credentials
define("APP_ID", "182044509112709");
define("APP_SECRET", "63d10cb9f4f108e243969ea7ada04ec9");
/* make sure the url end with a trailing slash */
define("SITE_URL", "http://aeronni.com/astro/public_html/");
/* the page where you will be redirected after login */
define("REDIRECT_URL", SITE_URL."fb_login.php");
/* Email permission for fetching emails. */
define("PERMISSIONS", "email");

// create a facebook object
$facebook = new Facebook(array('appId' => APP_ID, 'secret' => APP_SECRET));
$userID = $facebook->getUser();

// Login or logout url will be needed depending on current user login state.
if ($userID) {
  $logoutURL = $facebook->getLogoutUrl(array('next' => SITE_URL . 'logout.php'));
} else {
  $loginURL = $facebook->getLoginUrl(array('scope' => PERMISSIONS, 'redirect_uri' => REDIRECT_URL));
}

?>