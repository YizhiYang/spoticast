<?php

require 'vendor/autoload.php';

$session = new SpotifyWebAPI\Session('9472382904b44584a6ccb2b90c2d99a6', '129d06387a6741fa8008ea15c4bbe8ba', 'http://s.box/projects/spoticast/authorize.php');

$scopes = [
    'playlist-read-private',
    'user-read-private',
    'user-library-read',
    'user-follow-read'
];

$authorizeUrl = $session->getAuthorizeUrl([
    'scope' => $scopes
]);

header('Location: ' . $authorizeUrl);
die();

?>
