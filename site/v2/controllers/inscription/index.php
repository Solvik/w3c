<?php
if(is_online()) { include VIEW.'deja_connecte.html'; exit(); }

if (!empty($_POST['inscrire'])) // Si le formulaire a été envoyé
{
	if(isset($_SESSION['captcha']) && $_POST['captcha'] == $_SESSION['captcha']) // Si le captcha est bon
    {
		if(!empty($_POST['pseudo']) AND !empty($_POST['mail']) AND !empty($_POST['mdp']) AND !empty($_POST['mdp_conf'])) // Si les champs obligatoires ont étés renseignés
		{
			if(ereg("^[[:alnum:]]+$", trim($_POST['pseudo']))) // Si son pseudo ne contient bien que des caractères alphanumériques
			{
				if($_POST['mdp'] == $_POST['mdp_conf']) // Si les 2 mots de passe concordent
				{
					if(validation_email($_POST['mail']) === true) // Si on email est valide
					{
						if(!is_blacklisted(USERIP)) // Si son IP n'est pas blacklistée
						{
							if(Members::getCountByCond('login = \''.trim($_POST['pseudo']).'\'') == 0)
							{
								//Enfin !
								$login 		= trim($_POST['pseudo']);
								$pass 		= $_POST['mdp'];
								$email 		= $_POST['mail'];
								$nom 		= utf8_encode($_POST['nom']);
								$prenom 	= utf8_encode($_POST['prenom']);
								$adresse 	= utf8_encode($_POST['adresse']);
								$cp 		= trim($_POST['cp']);
								$ville 		= utf8_encode($_POST['ville']);
								
								$clef		= substr(md5(microtime() . rand(0,9999)), 0, 24);
								
								new Member(Member::NOUVEAU, 0, $login, $pass, $nom, $prenom, $email, $adresse, $cp, $ville, $clef);
								
								$mail="Bonjour $login,

Vous venez de vous inscrire sur OxyCast.

Afin de valider votre inscription, rendez-vous sur http://www.oxycast.net/validation-".$clef."

Pour rappel, vos identifiants sont :
Pseudo : $login
Mot de passe : $pass

Bonne visite sur OxyCast !

----
Si vous ne vous êtes pas inscrit à OxyCast, ne tenez pas compte de ce mail.";
		 
								$headers = 'From: noreply@oxycast.net' . "\r\n" .
										   'Reply-To: null@oxycast.net' . "\r\n" .
										   'X-Mailer: PHP/' . phpversion();

								mail($email, "Inscription OxyCast -- Webradio en 3 clics", $mail, $headers);
								
								Log::INFO('Nouveau Compte : '.$login);
								include VIEW.'inscription_reussie.html';
							
							} else {
								$erreur = 'Ce pseudo est d&eacute;j&agrave; utilis&eacute;.';
								include VIEW.'inscription.html';
							}
						} else {
							$erreur = 'Votre adresse IP est blacklist&eacute;e par : '.is_blacklisted(USERIP).'<br />
									   Veuillez contacter un administrateur.';
							include VIEW.'inscription.html';
						}
					} else {
						$erreur = 'Votre email est invalide.';
						include VIEW.'inscription.html';
					}
				} else {
					$erreur = 'Vos deux mots de passe ne concordent pas.';
					include VIEW.'inscription.html';
				}
			} else {
				$erreur = 'Votre pseudo ne peut contenir que des caract&egrave;res alphanum&eacute;riques.';
				include VIEW.'inscription.html';
			}
			
		} else {
			$erreur = 'Veuillez remplir tous les champs marqu&eacute;s d\'une &eacute;toile.';
			include VIEW.'inscription.html';
		}
	} else {
		$erreur = 'Le captcha a &eacute;t&eacute; mal retap&eacute;';
		include VIEW.'inscription.html';
	}
} else {
	$erreur = ' ';
	include VIEW.'inscription.html';
}
