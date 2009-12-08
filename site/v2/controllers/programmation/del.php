<?php
if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);

if(!empty($_GET['id']))
{
	Programmation::delEvent($_GET['id'], $compte);
	header('Location: '.$_SERVER['HTTP_REFERER']);
}