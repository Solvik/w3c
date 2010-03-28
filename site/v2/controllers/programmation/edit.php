<?php
if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);
$id = intval($_GET['id']);

include MODEL.'Musiques.php';

if(isset($_POST['edit']))
  {
    $event = new Event ($id, $compte);
    if($event)
      {
	$hdebut = intval($_POST['hdebut']);
	$mdebut = intval($_POST['mdebut']);
	$hfin = intval($_POST['hfin']);
	$mfin = intval($_POST['mfin']);
	$action = intval($_POST['action']);
		
	$event->heure_debut		= nb_with_zero($hdebut).":".nb_with_zero($mdebut).":00";
	$event->heure_fin		= nb_with_zero($hfin).":".nb_with_zero($mfin).":00";
	$event->minute_debut	= $mdebut;
	$event->minute_fin	= $mfin;
	$event->action		= $action;
	$event->save();
	include VIEW.'edit-success.html';
      }
  }
else
{
	$event = new Event ($id, $compte);
	if($event) include VIEW.'edit.html';
}
