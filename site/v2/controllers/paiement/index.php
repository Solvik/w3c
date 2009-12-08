<?php

if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);
if(empty($_GET['transacid'])) include VIEW.'paiement_invalide.html';
else
{
	$transacId = intval($_GET['transacid']);
	$commande = new Commande($transacId);
	if($commande->clientId == $compte->id)
	{
		if($commande->status == 'waiting')
		{
			if($_GET['type'] == 'cheque')
			{
				$commande->payment_type = 'cheque';
				$commande->save();
				include VIEW.'cheque.html';
			} elseif($_GET['type'] == 'paypal')
			{
				$commande->payment_type = 'paypal';
				$commande->save();
				include VIEW.'paypal.html';
			} else include VIEW.'paiement_invalide.html';
		} else include VIEW.'paiement_invalide.html';
	} else include VIEW.'paiement_invalide.html';
}