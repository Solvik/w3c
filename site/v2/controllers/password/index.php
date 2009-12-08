<?php
if(is_online()) { include DEJA_CONNECTE; exit(); }

if (isset($_POST['password']) AND !empty($_POST['captcha'])) 
{ 
	if(isset($_SESSION['captcha']) && $_POST['captcha'] == $_SESSION['captcha'])
    {
		if (!empty($_POST['login'])) 
		{ 
			if(!is_blacklisted(USERIP))
			{
				$login = trim($_POST['login']);
				
				$exist = Members::getCountByCond('login = \''.$login.'\' AND cle_conf = \'\'');
				if($exist == 1)
				{
					$compte = new Member(Member::EXISTANT, $login);
					$clef = substr(md5(microtime() . rand(0,9999)), 0, 24);
					$compte->recover = $clef;
					$compte->save();
					
					$mail_txt="Bonjour,

Vous avez demandé à récupérer le mot de passe d'accès au site OxyCast.

Pour terminer la procédure, merci de vous rendre à la page :

http://www.oxycast.net/password-".$clef."

";

                $do_act=mail($compte->mail,"OXYCAST.NET - Récupération du mot de passe",$mail_txt,"From: noreply@oxycast.net.\r\n"."Reply-To: noreply@oxycast.net\r\n");
					Log::INFO('Pass perdu, mail envoyé : '.$login);
					include VIEW.'pass_envoye.html';
				} else {
					$exist_mais_non_actif = Members::getCountByCond('login = \''.$login.'\'');
					if		($exist_mais_non_actif == 1) $erreur = 'Ce compte n\'est pas encore activ&eacute;.';
					else 	$erreur = 'Aucun compte avec ce pseudo.';
					Log::INFO('Pass perdu, mauvais login : '.$login);
					include VIEW.'pass.html';
				}
			} else {
				$erreur = 'Votre IP est blacklist&eacute;e par : '.is_blacklisted(USERIP).'<br />
				Veuillez contacter un administrateur.'; 
				include VIEW.'pass.html';
			}
		  
	   } else {
		  $erreur = 'Veuillez remplir le champs "login".'; 
		  include VIEW.'pass.html';
	   }
	} else {
		$erreur = 'Le captcha n\'est pas bon !';
		include VIEW.'pass.html';
	}
} else {
	$erreur = ' ';
	include VIEW.'pass.html';
}