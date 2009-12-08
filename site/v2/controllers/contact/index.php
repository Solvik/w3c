<?php
/**
 * Oxycast
 * Formulaire de contact
 * @author alfo
 * @version 1.0
 */

$sujet = array (
	'Je souhaiterais obtenir plus d\'informations sur vos services' => 'webmaster@oxyradio.net',
    'Je souhaiterais obtenir une offre sur mesure' => 'staff@oxyradio.net',
    'Je souhaiterais beneficier d\'une prestation de service' => 'dedicaces@oxyradio.net',
	'Je vous contacte pour une demande de partenariat' => 'artistes@oxyradio.net',
	'Ma demande n\'est pas list&eacute;e' => 'contact@oxyradio.net');

// On vérifie que le formulaire a été envoyé
if (isset($_POST['envoyer']))
{
    if(!empty($_POST['nom']) AND !empty($_POST['mail']) AND !empty($_POST['sujet']) AND !empty($_POST['message']))
	{
		if(validation_email($_POST['mail']) === true) // Si on email est valide
		{
			if(!is_blacklisted(USERIP)) // Si son IP n'est pas blacklistée
			{
				$mail="Bonjour,<br><br>
Un message vous a été envoyé sur OxyCast.<br><br>
Nom : ".$_POST['nom']."<br>
Mail : ".$_POST['mail']."<br>
Sujet : ".$_POST['sujet']."<br><br>
Message : ".htmlentities(nl2br($_POST['message']));
         
				$headers = 'From: '.utf8_encode($_POST['nom']).' <'.$_POST['mail'] . ">\r\n" .
						   'X-Mailer: PHP/' . phpversion()."\r\nMIME-version: 1.0\r\nContent-type: text/html; charset=utf-8\r\n";
     
				mail($sujet[$_POST['sujet']], "Contact OxyCast -- ".utf8_encode($_POST['sujet']), $mail, $headers);
				include VIEW.'envoye.html';
				
			} else {
				$erreur = 'Votre IP est blacklist&eacute;e.'; 
				include VIEW.'index.html';
			}
		} else {
			$erreur = 'Votre adresse email n\'est pas valide.'; 
			include VIEW.'index.html';
		}
	} else {
		$erreur = 'Veuillez remplir tout les champs.'; 
		include VIEW.'index.html';
	}
} else {
	include VIEW.'index.html';
}