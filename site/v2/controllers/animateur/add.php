<?php
if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);

if (isset($_POST['add_animateur']))
{
	$animateur = new Animateur($compte, Animateurs::NOUVEAU, htmlspecialchars($_POST['animateur']), htmlspecialchars($_POST['password']));

	header('Location: animateur');
}