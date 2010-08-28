<?php
include BLOG_SCRIPT_URL;

if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);

if($compte->hasBlog() === true) { include VIEW.'deja_un_blog.html'; }
else {
	if(!empty($_POST['blog']))
	{
		if(!empty($_POST['nom']))
		{
			if(preg_match("^[\w\_\-]+$^",$_POST['nom']) AND str_replace(' ','',$_POST['nom']) == $_POST['nom']) //TODO: Pas joli, mais fonctionnel
			{
				$blogName = $_POST['nom'];
				if(!Members::getCountByCond('blog = \''.$blogName.'\''))
				{
					$site = new miniSite($compte->id, $blogName);
					$site->install();
					$compte->blog = $blogName;
					$compte->save();
					
					include VIEW.'blog_pret.html';
				} else {
					$erreur = 'Le nom du blog est d&eacute;j&agrave; utilis&eacute;.';
					include VIEW.'blog.html';
				}
			} else {
				$erreur = 'Le nom du blog est invalide : le nom doit contenir uniquement des chiffres, lettres et tirets "-" ou underscore "_".';
				include VIEW.'blog.html';
			}
		} else {
			$erreur = 'Veuillez compl&eacute;ter le champ "nom".';
			include VIEW.'blog.html';
		}
	} else {
		include VIEW.'blog.html';
	}
}

