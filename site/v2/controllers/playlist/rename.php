<?php
include MODEL.'Musiques.php';
if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);
$stream = $compte->getStream();

if (!empty($_POST['nom'])) // Si le formulaire a été envoyé
  {
    $playlistId = intval($_GET['playlist']);
    $playlist = new PlayList($playlistId, $compte, $stream);
	
    if(PlayList::belongs($playlist, $compte))
      {
	$playlist->nom = htmlspecialchars($_POST['nom']);
	$playlist->save();
	include VIEW.'rename-playlist-ok.html';
      }
  }
else
  {
	$playlistId = intval($_GET['playlist']);
	$playlist = new PlayList($playlistId, $compte, $stream);
    $erreur = ' ';
    include VIEW.'rename-playlist.html';
  }