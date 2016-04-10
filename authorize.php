<!doctype html>

<html>
	<head>
		<link rel="stylesheet" href="style.css">
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
	</head>
	<body class="loading">
		<div class="wrap">
			<h1>Loading your playlists...</h1>
			<img src="img/rings.svg">
		</div>
	</body>
</html>
<?php

session_start();
error_reporting(-1);
ini_set('display_errors', '1');

require 'vendor/autoload.php';

$session = new SpotifyWebAPI\Session('9472382904b44584a6ccb2b90c2d99a6', '129d06387a6741fa8008ea15c4bbe8ba', 'http://s.box/projects/spoticast/authorize.php');
$api = new SpotifyWebAPI\SpotifyWebAPI();

// Request a access token using the code from Spotify
$session->requestToken($_GET['code']);
$accessToken  = $session->getAccessToken();
$refreshToken = $session->getRefreshToken();
$expires = $session->getExpires();

// Set the access token on the API wrapper
$api->setAccessToken($accessToken);

try {
	$user_id = $api->me()->id;
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

$_SESSION['user_id'] = $user_id;
$_SESSION['access_token'] = $accessToken;
$_SESSION['refresh_token'] = $refreshToken;
$_SESSION['token_expires'] = time() + $expires;

header('location: playlists.php');
?>
