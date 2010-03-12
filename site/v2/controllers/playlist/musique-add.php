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
      } 
    else 
      {
	$orderBy = array('id', 'artiste', 'titre', 'filename');
	$sens = array('asc', 'desc');
	if (!empty($_GET['order']) AND in_array($_GET['order'], $orderBy))
	  {
	    if (!empty($_GET['sens']) AND in_array($_GET['sens'], $sens))
	      $musiques = getMusicList($compte, $compte->getStream(), $_GET['order'], $_GET['sens']);
	    else
	      $musiques = getMusicList($compte, $compte->getStream(), $_GET['order'], 'ASC');	 
	  }
	else
	  $musiques = getMusicList($compte, $compte->getStream());
	include VIEW.'musique-add-list.html';
      }
  } else {
	$erreur = ' ';
	include VIEW.'playlist.html';
}
