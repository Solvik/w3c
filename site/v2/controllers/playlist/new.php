<?php
if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);
$stream = $compte->getStream();

if (!empty($_POST['nom'])) // Si le formulaire a été envoyé
{
	$nom = addslashes($_POST['nom']);
	$playlist = new PlayList(PlayList::NOUVEAU, $compte, $stream, $nom);
	include VIEW.'nouvelle-playlist-ok.html';
} else {
	$erreur = ' ';
	include VIEW.'nouvelle-playlist.html';
}