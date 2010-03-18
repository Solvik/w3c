<?php
if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);

include MODEL.'Musique.class.php';

$id = intval($_GET['id']);
if (isset($_POST['edit']))
  {
    $musique = new Musique($compte, $id);
    if ($musique)
      {
	$musique->titre = htmlspecialchars($_POST['titre']);
	$musique->artiste = htmlspecialchars($_POST['artiste']);
	$musique->fade_in = floatval($_POST['fade_in']);
	$musique->fade_out = floatval($_POST['fade_out']);

	include 'views/musique/edit-success.html';
      }
  }
else
  {
    $musique = new Musique($compte, $id);
    if ($musique) include VIEW.'edit.html';
  }
