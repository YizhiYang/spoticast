<?php
	
	session_start();
	
	require 'vendor/autoload.php';
	
	$user_id = $_SESSION['user_id'];
	$access_token = $_SESSION['access_token'];
	$expires = $_SESSION['token_expires'];
	$refresh_token = $_SESSION['token_expires'];
	
	
	$api = new SpotifyWebAPI\SpotifyWebAPI();
	
	$api->setAccessToken($access_token);
	
	$offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
	
	try {
	   $playlists = $api->getUserPlaylists($user_id, ['limit'=>50, 'offset'=>$offset])->items;
	} catch (Exception $e) {
		header('Location: index.php');
	    echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	
	$expires_readable = date('h:i A', $expires);

	if(isset($_GET['offset'])){
		print_r(json_encode($playlists));
	}
	
?>