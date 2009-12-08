<?php
if(!is_online()) { include NON_CONNECTE; exit(); }

$compte = new Member(Member::EXISTANT, $_SESSION['login']);

if($compte->getStream()->hasStream() === true) { include VIEW.'deja_un_stream.html'; }
else {
	if(empty($_GET['offre'])) $offre = 'S'; else $offre = trim($_GET['offre']);
	
	if(!empty($_POST['commander']))
	{
		if(!empty($_POST['nom']) AND !empty($_POST['mountpoint']) AND !empty($_POST['password']))
		{
			if(ereg("^[[:alnum:]]+$", trim($_POST['nom'])) AND ereg("^[[:alnum:]]+$", trim($_POST['mountpoint'])))
			{
				if(!Stream::exist('nom', trim($_POST['nom'])))
				{
					if(!Stream::exist('mountpoint', trim($_POST['mountpoint'])))
					{
						if($_POST['offer'] > 0 AND $_POST['offer'] <= 4)
						{
							//Enfin !
							$stream = new Stream(0);
							$stream->create($compte->id,
											intval($_POST['offer']),
											'commande',
											trim($_POST['nom']),
											$_POST['description'],
											$_POST['genre'],
											trim($_POST['mountpoint']),
											$_POST['url'],
											trim($_POST['password']));
						
							$offre = new Offre(intval($_POST['offer']));
							$commande = new Commande(0);
							$commande->create($compte->id, 'none', 'waiting', $offre->prix, intval($_POST['offer']), $compte->mail);
						
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
							Log::INFO('Nouvelle commande : '.$compte->login);
							include VIEW.'commande_effectuee.html';
						}
					} else {
						$erreur = 'Le mountpoint est d&eacute;j&agrave; utilis&eacute;.';
						include VIEW.'commande.html';
					}
				} else {
					$erreur = 'Le nom du stream est d&eacute;j&agrave; utilis&eacute;.';
					include VIEW.'commande.html';
				}
			} else {
				$erreur = 'Le nom du stream et le mountpoint doivent &ecirc;tre uniquement composés de caract&egrave;res alphanum&eacute;riques (sans espaces).';
				include VIEW.'commande.html';
			}
		} else {
			$erreur = 'Veuillez compl&eacute;ter tous les champs marqu&eacute;s d\'une &eacute,toile.';
			include VIEW.'commande.html';
		}
	} else {
		include VIEW.'commande.html';
	}
}

