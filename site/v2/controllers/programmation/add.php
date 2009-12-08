<?php
if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);
$jour = intval($_GET['jour']);
	
if(isset($_POST['etape2']))
{
	if($jour >= 1 AND $jour <= 7)
	{
		if($_POST['type'] == 'podcast') $type = 'podcast';
		else $type = 'playlist';
		$hdebut = intval($_POST['hdebut']);
		$mdebut = intval($_POST['mdebut']);
		$hfin = intval($_POST['hfin']);
		$mfin = intval($_POST['mfin']);
		$action = intval($_POST['action']);
		
		Programmation::addEvent($compte, $type, $jour, $hdebut, $hfin, $mdebut, $mfin, $action);
		include VIEW.'formulaire-add-success.html';
	}
}
elseif (isset($_POST['etape1']))
{
	$hdebut = intval($_POST['hdebut']);
	$mdebut = intval($_POST['mdebut']);
	$hfin = intval($_POST['hfin']);
	$mfin = intval($_POST['mfin']);
	
	if($_POST['type'] == 'playlist') include VIEW.'formulaire-add-playlist.html';
	else include VIEW.'formulaire-add-podcast.html';
}
else
{
	include VIEW.'formulaire-add.html';
}