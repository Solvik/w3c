<?php

if(is_online()) { include DEJA_CONNECTE; exit(); }

if (!empty($_GET['clef'])) 
{ 
	$pdo = SPDO::getInstance();
	$clef = trim($_GET['clef']);
	$exist = Members::getCountByCond('recover = \''.$clef.'\'');
	
	if($exist == 1)
	{
		$login = $pdo->query('SELECT login FROM clients WHERE recover = \'' . $clef . '\'')->fetchColumn();
		$compte = new Member(Member::EXISTANT, $login);
		$compte->recover = null;
		$nouveau_pass = substr(md5(microtime() . rand(0,9999)), 0, 10);
		$compte->hash = generate_hash($nouveau_pass);
		$compte->save();
		
		$mail_txt="Bonjour,

Suite à votre demande de changement de mot de passe voici vous nouveaux identifiants :
Compte : $login
Mot de Passe : $nouveau_pass

OxyCast.";

        mail($compte->mail,"OXYCAST.NET - Récupération du mot de passe",$mail_txt,"From: noreply@oxycast.net.\r\n"."Reply-To: noreply@oxycast.net\r\n");
		Log::INFO('Pass récupéré : '.$login);
		include VIEW.'recover_ok.html';
	} else {
		include VIEW.'clef_invalide.html';
	}
} else {
	include VIEW.'clef_invalide.html';
}