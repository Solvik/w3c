<?php
include MODEL.'Musiques.php';
if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);
$stream = $compte->getStream();

if (!empty($_GET['playlist'])) // Si le formulaire a été envoyé
{
	$playlistId = intval($_GET['playlist']);
	$playlist = new PlayList($playlistId, $compte, $stream);
	
	if(PlayList::belongs($playlist, $compte))
	{
		$musiques = $playlist->getMusiques();
		foreach($musiques as $musique)
		{
			$musique = getMusicInfos($musique, $compte, $stream);
			$playlist->delMusique($musique['id']);
		}
		$playlist->delete();
		include VIEW.'suppression-playlist-ok.html';
	}
	
}