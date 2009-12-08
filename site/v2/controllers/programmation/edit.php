﻿<?php
if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);
$id = intval($_GET['id']);

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
		
		$event->heure_debut		= $hdebut;
		$event->minute_debut	= $mdebut;
		$event->heure_fin		= $hfin;
		$event->minute_fin		= $mfin;
		$event->action			= $action;
		$event->save();
		include VIEW.'edit-success.html';
	}
}
else
{
	$event = new Event ($id, $compte);
	if($event) include VIEW.'edit.html';
}