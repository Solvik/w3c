<?php
if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);

include MODEL.'Animateur.php';

if (isset($_POST['add_animateur']))
{
  $animateur = new Animateur($compte, Animateur::NOUVEAU, htmlspecialchars($_POST['animateur']), htmlspecialchars($_POST['password']), htmlspecialchars($_POST['mail']));
	header('Location: animateur');
}
elseif (isset($_POST['add_creneaux']))
{
	if($_POST['jour'] >= 1 AND $_POST['jour'] <= 8)
	{
		$heure_debut = nb_with_zero(htmlspecialchars($_POST['hdebut'])).":".nb_with_zero(htmlspecialchars($_POST['mdebut'])).":00";
		$heure_fin = nb_with_zero(htmlspecialchars($_POST['hfin'])).":".nb_with_zero(htmlspecialchars($_POST['mfin'])).":00";

		if($_POST['jour'] == 8)
		{
			$heure_debut = '00:00:00';
			$heure_fin = '23:59:59';
		}
		
		Creneaux::addCreneau($compte,							
							intval($_POST['animateur']),
							intval($_POST['jour']),
							$heure_debut,
							$heure_fin
							);

		header('Location: animateur');
	}
}