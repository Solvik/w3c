<?php
if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);

if($compte->getStream()->hasStream() === false OR $compte->getStream()->status == 'suspendu') { include VIEW.'pas_de_stream.html'; }
else {
	$stream = $compte->getStream();
	$offre = new Offre(intval($stream->offerId));
	$commande = new Commande(0);
	$commande->create($compte->id, 'none', 'waiting', $offre->prix, intval($stream->offerId), $compte->mail);
						
	$mail="Bonjour {$compte->login},

	Vous venez de créer un nouveau bon de commande sur OxyCast.

	Afin de procéder au paiement vous disposez de 2 moyens :
	Paypal : http://www.oxycast.net/paiement-{$commande->transacId}-paypal
	Chèque : http://www.oxycast.net/paiement-{$commande->transacId}-cheque

	Bonne visite sur OxyCast !";
			 
	$headers = 'From: noreply@oxycast.net' . "\r\n" .
			   'Reply-To: null@oxycast.net' . "\r\n" .
			   'X-Mailer: PHP/' . phpversion();

	mail($compte->mail, "Commande OxyCast", $mail, $headers);
	Log::INFO('Renouvellement demandé : '.$compte->login);
	include VIEW.'renew_ok.html';


}

