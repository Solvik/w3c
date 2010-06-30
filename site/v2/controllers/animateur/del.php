<?php
include MODEL.'Musiques.php';
include MODEL.'Animateur.php';
if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);
$stream = $compte->getStream();

$id = intval($_GET['id']);
if ($_GET['type'] == "animateur")
{
    $result1 = Animateur::deleteAnim($compte, $id);
	$result2 = Creneaux::deleteAnimateurCreneaux($compte, $id);
	
    if ($result1 AND $result2)
		header('Location: animateur');
    else
		include VIEW.'suppression-animateur-fail.html';
}
else
	Creneaux::deleteCreneaux($compte, $id);
       