<?php
if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);

include MODEL.'Musiques.php';

if(!$compte->getStream()->hasStream())
{
	include 'views/stream/pas_de_stream.html';
} else {
	$stream = $compte->getStream();

	if(!empty($_GET['jour']))
	{
		$jour = intval($_GET['jour']);
		if($jour >= 1 AND $jour <= 7)
		{
			$events = Programmation::getEvents($jour, $compte);
			if(count($events) == 0) include VIEW.'aucun-evenement.html';
			else include VIEW.'liste-events.html';
		}
	} else
	{
		include VIEW.'liste-jours.html';
	}
}
