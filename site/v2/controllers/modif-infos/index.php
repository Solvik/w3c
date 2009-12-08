<?php
if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);

if (!empty($_POST['modifier'])) // Si le formulaire a été envoyé
{
	if(!empty($_POST['mail'])) // Si les champs obligatoires ont étés renseignés
	{
		if($_POST['mdp'] == $_POST['mdp_conf']) // Si les 2 mots de passe concordent
		{
			if(validation_email($_POST['mail']) === true) // Si l'email est valide
			{
				
				if(!empty($_POST['mdp']) AND $compte->hash != generate_hash($_POST['mdp'])) $compte->hash = generate_hash($_POST['mdp']);
				$compte->mail 		= $_POST['mail'];
				$compte->nom 		= $_POST['nom'];
				$compte->prenom 	= $_POST['prenom'];
				$compte->adresse 	= $_POST['adresse'];
				$compte->cp 		= trim($_POST['cp']);
				$compte->ville 		= $_POST['ville'];
				
				$compte->save();
				
				$erreur = 'Vos informations ont &eacute;t&eacute; modifi&eacute;es.';
				include VIEW.'modifier.html';
			
			} else {
				$erreur = 'Votre email est invalide.';
				include VIEW.'modifier.html';
			}
		} else {
			$erreur = 'Vos deux mots de passe ne concordent pas.';
			include VIEW.'modifier.html';
		}
	} else {
		$erreur = 'Veuillez remplir tous les champs marqu&eacute;s d\'une &eacute;toile.';
		include VIEW.'modifier.html';
	}
} else {
	$erreur = ' ';
	include VIEW.'modifier.html';
}
