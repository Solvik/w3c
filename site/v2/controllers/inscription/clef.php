<?php
if(!empty($_GET['clef']))
{
	if(Members::getCountByCond('cle_conf = \''.trim($_GET['clef']).'\'') > 0)
	{
		$pdo = SPDO::getInstance();
		$clef = trim($_GET['clef']);
		
		$pdo->query("UPDATE `clients` SET cle_conf='' WHERE cle_conf='$clef'");
		include VIEW.'clef_validee.html';
	} else {
		include VIEW.'clef_invalide.html';
	}
}