<?php
include MODEL.'Musiques.php';

if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);
if(!$compte->getStream()->hasStream())
{
	include 'views/stream/pas_de_stream.html';
} else {
	$stream = $compte->getStream();
	$playlists = PlayList::getPlaylists($compte);

	include VIEW.'playlist.html';
}