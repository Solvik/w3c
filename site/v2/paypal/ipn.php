<?php
	include '../global/config.php';
	include '../libs/spdo.php';
	include '../models/Commande.class.php';
	include '../models/Stream.class.php';
	include '../models/Member.class.php';
	include 'log.php';
	$log = new Log;
	
    // Lire le formulaire provenant du système PayPal et ajouter 'cmd'
    $req = 'cmd=_notify-validate';
    
    foreach ($_POST as $key => $value) {
        $value = urlencode(stripslashes($value));
        $req .= "&$key=$value";
    }
	
	// renvoyer au système PayPal pour validation
    $header  = "POST /cgi-bin/webscr HTTP/1.0\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
    if(PAYPAL_TEST_MODE) $fp = fsockopen ('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);
	else $fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);
	
	@$item_name 			= $_POST['item_name'];
    @$item_number 		= $_POST['item_number'];
    @$payment_status		= $_POST['payment_status'];
    @$payment_amount 	= $_POST['mc_gross'];
    @$payment_currency 	= $_POST['mc_currency'];
    @$txn_id 			= $_POST['txn_id'];
    @$receiver_email 	= $_POST['receiver_email'];
    @$payer_email 		= $_POST['payer_email'];
    @$transacId 			= $_POST['custom'];

	Log::PAYPAL('Paypal IPN Call :');
    @Log::PAYPAL('Offer ID : '.$_POST['item_number']);
    @Log::PAYPAL('Transac ID : '.$_POST['custom']);
    @Log::PAYPAL('Payment Status : '.$_POST['payment_status']);
    @Log::PAYPAL('Montant : '.$_POST['mc_gross'].$_POST['mc_currency']);
    @Log::PAYPAL('Email : '.$_POST['payer_email']);
	
    if (!$fp) {
    // ERREUR HTTP
    } else {
        fputs ($fp, $header . $req);
        while (!feof($fp)) {
            $res = fgets ($fp, 1024);
            if (strcmp ($res, "VERIFIED") == 0)
			{
                Log::PAYPAL('Paiement vérifié, début des tests...');

                if ( $payment_status == "Completed")
				{
					Log::PAYPAL('Paiement complété...');
                    if ($receiver_email == PAYPAL_RECEIVER_EMAIL)
					{
						Log::PAYPAL('Paiement bien destiné à nous...');
                        $commande = new Commande($transacId);
						if($commande->transacId != null)
						{
							Log::PAYPAL('La transaction existe bien de notre côté...');
							if($commande->amount == $payment_amount)
							{
								Log::PAYPAL('Le montant est le bon...');
								Log::PAYPAL('Tout est bon, on valide !');
								
								$stream = new Stream($commande->clientId);
								$compte = new Member(Member::EXISTANT, (int)$commande->clientId);
								$commande->status = 'done';
								$commande->save();
								
								if($stream->status == 'commande')
								{
									$stream->dateDebut = date("Y-m-d G:i:s");
									$stream->dateFin = date("Y-m-d G:i:s", mktime(date("h"), date("i"), date("s"), date("m")+1, date("d"), date("Y")));
								}
								elseif($stream->status == 'termine' OR $stream->status == 'programme')
								{
									$parsed = date_parse($stream->dateFin);
									$stream->dateFin = date("Y-m-d G:i:s", mktime($parsed['hour'], $parsed['minute'], $parsed['second'], $parsed['month']+1, $parsed['day'], $parsed['year']));
								}
								$stream->status = 'programme';
								$stream->save();

								
								$mail = "Bonjour $compte->login,\n

Votre commande n°$commande->transacId d'un montant de $commande->amount euros a été validée.\n

Bonne visite sur OxyCast !";
		 
								$headers = 'From: noreply@oxycast.net' . "\r\n" .
										   'Reply-To: null@oxycast.net' . "\r\n" .
										   'X-Mailer: PHP/' . phpversion();

								mail($compte->mail, "Commande OxyCast -- $commande->transacId", $mail, $headers);
								
$mail = "La commande $commande->transacId d'un montant de $commande->amount a été validée.";
		 
								$headers = 'From: noreply@oxycast.net' . "\r\n" .
										   'Reply-To: null@oxycast.net' . "\r\n" .
										   'X-Mailer: PHP/' . phpversion();

								mail(PAYPAL_RECEIVER_EMAIL, "AutoMailer - Commande $commande->transacId", $mail, $headers);
							}
						}
                    }
				}
			}
		}
    }
    
	@fclose ($fp);
    Log::PAYPAL('--------------------------------------');
