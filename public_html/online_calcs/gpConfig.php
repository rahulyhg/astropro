<?php
session_start( );

//Include Google client library 
include_once 'src/Google_Client.php';
include_once 'src/contrib/Google_Oauth2Service.php';

/*
 * Configuration and setup Google API
 */
$clientId = '1057787326331-n730ohid8eprb0vo3tgkjjstg7jr3grk.apps.googleusercontent.com';
$clientSecret = 'LU4gcve0PNwq8-Pf8TOdqldv';
$redirectURL = 'http://localhost/astro/public_html/online_calcs';

//Call Google API
$gClient = new Google_Client();
$gClient->setApplicationName('Login to CodexWorld.com');
$gClient->setClientId($clientId);
$gClient->setClientSecret($clientSecret);
$gClient->setRedirectUri($redirectURL);

$google_oauthV2 = new Google_Oauth2Service($gClient);
?>
