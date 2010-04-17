﻿<?php
if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);

include MODEL.'Animateur.php';

$id = intval($_GET['id']);
if (isset($_POST['add_animateur']))
  {
    $animateur = new Animateurs($compte, $id);
    if ($animateur)
      {
	$animateur->name = htmlspecialchars($_POST['animateur']);
	$animateur->password = htmlspecialchars($_POST['password']);
	$animateur->save();

	echo "success";
	//	include 'views/animateur/edit-success.html';
      }
  }
else
  {
    $musique = new Musique($compte, $id);
    if ($musique) include VIEW.'edit.html';
  }
