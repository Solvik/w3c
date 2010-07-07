<?php
if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);

include MODEL.'Animateur.php';

if (isset($_POST['add_animateur']))
  {
    if (!Animateur::animExiste($compte, htmlspecialchars($_POST['animateur']), htmlspecialchars($_POST['password'])))
      {
	$animateur = new Animateur($compte, Animateur::NOUVEAU, htmlspecialchars($_POST['animateur']), htmlspecialchars($_POST['password']), htmlspecialchars($_POST['mail']));
	header('Location: animateur');
      }
    else
      {
	header('Location: animateur');
      }
  }
elseif (isset($_POST['add_creneaux']))
{
  if($_POST['jour'] >= 1 AND $_POST['jour'] <= 8)
    {
      if($_POST['jour'] == 8)
	{
	  $heure_debut = '00:00:00';
	  $heure_fin = '23:59:59';
	} else {
	$heure_debut = time_format(intval($_POST['hdebut']), intval($_POST['mdebut'])).":00";
	$heure_fin = time_format(intval($_POST['hfin']), intval($_POST['mfin'])).":00";
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