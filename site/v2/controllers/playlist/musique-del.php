<?php
if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);
$stream = $compte->getStream();

if (!empty($_GET['playlist']) AND !empty($_GET['music'])) // Si le formulaire a été envoyé
{
	$playlistId = intval($_GET['playlist']);
	$playlist = new PlayList($playlistId, $compte, $stream);
	
	$musiqueId = intval($_GET['music']);
	
	$playlist->delMusique($musiqueId);
	$playlist->save();
	header('Location: '.$_SERVER['HTTP_REFERER']);
}