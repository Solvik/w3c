<?php
include MODEL.'Musiques.php';

if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);
$stream = $compte->getStream();

if (!empty($_GET['playlist'])) // Si le formulaire a été envoyé
{
	$playlistId = intval($_GET['playlist']);
	$playlist = new PlayList($playlistId, $compte, $stream);
	$musiquesInPlaylist = $playlist->getMusiques();
	
	if(!empty($_POST['musiques']))
	{
		$musiques = array();
		$i = 0;
		foreach($_POST['musiques'] as $musique)
		{
			$musiques[$i] = intval($musique);
			$i++;
		}
		$playlist->musiques = $musiques;
		$playlist->save();
		include VIEW.'musique-add-list-ok.html';
	} else {
	  $musiques = getMusicList($compte, $stream, "id", "ASC");
		include VIEW.'musique-add-list.html';

	}
} else {
	$erreur = ' ';
	include VIEW.'playlist.html';
}
