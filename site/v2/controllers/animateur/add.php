<?php
if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);

include MODEL.'Animateur.php';

if (isset($_POST['add_animateur']))
{
	$animateur = new Animateur($compte, Animateur::NOUVEAU, htmlspecialchars($_POST['animateur']), htmlspecialchars($_POST['password']));
	header('Location: animateur');
}
elseif (isset($_POST['add_creneaux']))
{

  $heure_debut = nb_with_zero(htmlspecialchars($_POST['hdebut'])).":".nb_with_zero(htmlspecialchars($_POST['mdebut']))."00";
  $heure_fin = nb_with_zero(htmlspecialchars($_POST['hfin'])).":".nb_with_zero(htmlspecialchars($_POST['mfin']))."00";

  Creneaux::addCreneau($compte, Creneaux::NOUVEAU,
			   intval($_POST['animateur']),
			   $heure_debut,
			   $heure_fin,
			   htmlspecialchars($_POST['jour']));
}