<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();

require 'vendor/autoload.php';

$access_token = $_SESSION['access_token'];

$playlist_id = $_GET['id'];
$offset = $_GET['offset'];
$owner = $_GET['owner'];

$api = new SpotifyWebAPI\SpotifyWebAPI();
$api->setAccessToken($access_token);

try {
   $tracks = $api->getUserPlaylistTracks($owner, $playlist_id, ['limit'=>50, 'offset'=>$offset])->items;
} catch (Exception $e) {
	header('Location: index.php');
}

$playlist_data = [];
foreach($tracks as $track) {
	$artist = $track->track->artists[0]->name;
	$name = $track->track->name;
	$playlist_data[] = "$name $artist";
	$offset += 1;
}

$full_responses = [];
$results = [];

foreach($playlist_data as $key => $kwd){
		
		$result = curlIt($kwd);
		
		$full_responses[] = $result;
		$sub_results = [];
		
		foreach($result['items'] as $key => $video){
			if(strpos(strtolower($video['snippet']['title']), 'full album') === false){
				$sub_results[] = [
					'id' => $video['id']['videoId'],
					'title' => $video['snippet']['title']
				];
			}
		}
		
		$results[] = $sub_results;
}

function curlIt($kwd){
	$curl = curl_init();
	curl_setopt_array($curl, array(
	    CURLOPT_RETURNTRANSFER => 1,
	    CURLOPT_URL => "https://www.googleapis.com/youtube/v3/search?part=snippet&q=".urlencode($kwd)."&maxResults=3&type=video&order=viewCount&key=AIzaSyBcx9Du3PBMTUKYtrl2pRJTHetXPgGb0ZQ"
	));
	$response = json_decode(curl_exec($curl), true);
	curl_close($curl);
	return $response;
}

function remove_querystring_var($url, $key) { 
	$url = preg_replace('/(.*)(?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&'); 
	$url = substr($url, 0, -1); 
	return $url; 
}

//$url = remove_querystring_var($_SERVER[REQUEST_URI], "offset");
echo json_encode($results);


?>