<?php
if(is_online()) { include VIEW.'deja_connecte.html'; exit(); }

if (isset($_POST['connexion']))
{ 
	// Pas très pro...
	/*if(isset($_SESSION['captcha']) && $_POST['captcha'] == $_SESSION['captcha'])
    {*/
		if (!empty($_POST['login']) AND !empty($_POST['pass'])) 
		{ 
			if(!is_blacklisted(USERIP))
			{
				$login = trim($_POST['login']);
				$pass = generate_hash($_POST['pass']);
				
				$exist = Members::getCountByCond('login = \''.$login.'\' AND password = \''.$pass.'\' AND cle_conf = \'\'');
				
				if($exist == 1)
				{
					$_SESSION['login'] = $login;
					$member = new Member(Member::EXISTANT, $login);
					if($member->isAdmin == 1) $_SESSION['admin'] = true;
					Log::INFO('Connexion réussie : '.$login);
					include VIEW.'connecte.html';
				} else {
					$exist_mais_non_valide = Members::getCountByCond('login = \''.$login.'\' AND password = \''.$pass.'\'');
					if($exist_mais_non_valide == 1) $erreur = 'Votre compte n\'est pas encore activ&eacute;.';
					else $erreur = 'Mauvais login/pass !';
					Log::INFO('Mauvais login/pass : '.$login);
					include VIEW.'connexion.html';
				}
			} else {
				$erreur = 'Votre IP est blacklist&eacute;e par : '.is_blacklisted(USERIP).'<br />
				Veuillez contacter un administrateur.';
				Log::INFO('IP blacklistée : '.USERIP);
				include VIEW.'connexion.html';
			}
		  
	   } 
	   else {
		  $erreur = 'Au moins un des champs est vide.'; 
		  include VIEW.'connexion.html';
	   }
	/*} else {
		$erreur = 'Le captcha n\'est pas bon !';
		include VIEW.'connexion.html';
	}*/
} else {
	$erreur = 'Veuillez remplir les champs suivants :';
	include VIEW.'connexion.html';
}