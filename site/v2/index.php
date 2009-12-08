<?php
/*
 * Projet OxyCast
 * Requiert : PHP 5.3+
 * @author alfo
 * @version 1.0
 */

// Début de la tamporisation de sortie
ob_start();

// Initialisation de l'environnement
include 'global/init.php';

// Début du code HTML
include 'global/header.php';

// Si un controlleur est specifié, on regarde s'il existe
if (!empty($_GET['module']))
{
	$controlleur = dirname(__FILE__).'/controllers/'.$_GET['module'].'/';
	
	// Si l'action est specifiée, on l'utilise, sinon, on tente une action par défaut
	$action = (!empty($_GET['act'])) ? $_GET['act'].'.php' : 'index.php';
	
	// Si l'action existe, on l'exécute
	if (is_file($controlleur.$action)) {

		include $controlleur.$action;

	// Sinon, on affiche la page d'accueil !
	} else {

		include 'global/accueil.php';
	}

// Module non specifié ou invalide ? On affiche la page d'accueil !
} else
{
	include 'global/accueil.php';
}
// Fin de la tamporisation de sortie
echo ob_get_clean();

// Fin du code HTML
include 'global/footer.php';