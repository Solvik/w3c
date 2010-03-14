<?php

/* 
A GARDER POUR LA MOULINETTE:
$ip = ifconfig eth0|grep "inet adr:"|cut -d" " -f12 | cut -c "5-"
*/
include('/home/oxycast/private/moulinette/log.php');
include('/home/oxycast/private/moulinette/generate_conf.php');

$host = "88.191.250.170";
$user = "oxycast";
$base = "oxycast";
$password = "ene4UzAADHQ8Juxm";
//$password = "fhrximo5";

// $db = mysql_connect($host, $user, $password) or die(mail("root@oxycast.net", "OXYCAST: MYSQL Cannot connect !!", "Impossible de se connecter a la base de donee !\n", "From: OXYCAST.net <contact@oxycast.net>\r\nContent-Type:text/plain; charset=\"utf-8\"\r\n"));
// mysql_select_db($base, $db) or die(mail("root@oxycast.net", "OXYCAST: MYSQL Cannot select db !!", "Impossible de se connecter a la base de donee !\n", "From: OXYCAST.net <contact@oxycast.net>\r\nContent-Type:text/plain; charset=\"utf-8\"\r\n"));
$db = mysql_connect($host, $user, $password);
mysql_select_db($base, $db);

function activeProgStreams() 
{
  $ip = "88.191.250.170";
  $directory = "/home/oxycast/";
  
  $selectStreamsToCreate = "SELECT * FROM `streams` WHERE `ip_serveur` = '".$ip."' AND `status` = 'programme' AND `dateDebut` <= NOW() AND `dateFin` >= NOW()";
  $resultStreamsToCreate = mysql_query ($selectStreamsToCreate);
  $nbStreamsToCreate = mysql_num_rows ($resultStreamsToCreate);

  if ($nbStreamsToCreate != 0) 
    {
      while ($listeStreamsToCreate = mysql_fetch_object($resultStreamsToCreate)) 
	{
	  $updateStreamsToCreate = "UPDATE `streams` SET `status` = 'en cours' WHERE `id` = '".$listeStreamsToCreate->id."'";
	  $resultUpdateStreamsToCreate = mysql_query($updateStreamsToCreate);
	
	  $selectClientInfos = "SELECT * FROM `clients` WHERE `id` ='".$listeStreamsToCreate->clientId."'";
	  $resultClientInfos = mysql_query ($selectClientInfos);
	  $infosClient = mysql_fetch_object ($resultClientInfos);
	
	  // on cree les dossiers s'ils existent pas
	  if (!file_exists($directory.'streams/'.$infosClient->login.'-'.$infosClient->id))
	    {
	      exec('mkdir '.$directory.'streams/'.$infosClient->login.'-'.$listeStreamsToCreate->id);
	      exec('mkdir '.$directory.'streams/'.$infosClient->login.'-'.$listeStreamsToCreate->id.'/logs');
	      exec('mkdir '.$directory.'streams/'.$infosClient->login.'-'.$listeStreamsToCreate->id.'/public');
	      exec('mkdir '.$directory.'streams/'.$infosClient->login.'-'.$listeStreamsToCreate->id.'/public/wwww');
	      exec('mkdir '.$directory.'streams/'.$infosClient->login.'-'.$listeStreamsToCreate->id.'/public/playlist');
	      exec('mkdir '.$directory.'streams/'.$infosClient->login.'-'.$listeStreamsToCreate->id.'/public/jingles');
	      exec('mkdir '.$directory.'streams/'.$infosClient->login.'-'.$listeStreamsToCreate->id.'/public/podcast');
	      exec('cp -R '.$directory.'musique/* '.$directory.'streams/'.$infosClient->login.'-'.$listeStreamsToCreate->id.'/public/playlist/');
	      exec('cp -R '.$directory.'jingles/* '.$directory.'streams/'.$infosClient->login.'-'.$listeStreamsToCreate->id.'/public/jingles/');
	      exec('cp '.$directory.'private/oxycast/* '.$directory.'streams/'.$infosClient->login.'-'.$listeStreamsToCreate->id.'/');
	    }
	  // on cree l'axx ftp
	  $password = substr(md5(mt_rand()), 0, 8);
	  $create_ftp = 'INSERT INTO `pureftpd`.`users` (`User` , `Password` , `Uid` , `Gid` , `Dir`, `Ipaddress`) VALUES ("'.$infosClient->login.'", "'.md5($password).'", "1002", "1002", "/home/oxycast/streams/'.$infosClient->login.'-'.$listeStreamsToCreate->id.'/public/", "'.$ip.'")'; 
	  mysql_query($create_ftp);
	  
	  // on cree la base du client
	  $createDatabase = "CREATE DATABASE `".$infosClient->login."_".$listeStreamsToCreate->id."`";
	  $doCreateDatabase = mysql_query($createDatabase);
	
	  // on cree les tables de donnee du client
	  $createTables1 = "CREATE TABLE IF NOT EXISTS `".$infosClient->login."_".$listeStreamsToCreate->id."`.`musique` (
  `id` int(11) NOT NULL auto_increment,
  `titre` varchar(255) character set latin1 collate latin1_general_ci NOT NULL default '',
  `artiste` varchar(255) character set latin1 collate latin1_general_ci NOT NULL default '',
  `filename` varchar(255) character set latin1 collate latin1_general_ci NOT NULL default '',
  `path` varchar(255) character set latin1 collate latin1_general_ci NOT NULL default '',
  `dernier_passage` int(11) default '0',
  `passage` int(11) NOT NULL default '0',
  `fade_in` FLOAT NOT NULL DEFAULT  '0',
  `fade_out` FLOAT NOT NULL DEFAULT  '0',
  `duree` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
	  $doCreateTables1 = mysql_query($createTables1);

	  $createTables2 = "CREATE TABLE IF NOT EXISTS `".$infosClient->login."_".$listeStreamsToCreate->id."`.`planification` (
  `id` int(11) NOT NULL auto_increment,
  `type` enum('playlist','podcast') NOT NULL,
  `jour` varchar(255) character set latin1 collate latin1_general_ci NOT NULL default '',
  `heure_debut` int(11) NOT NULL,
  `heure_fin` int(11) NOT NULL,
  `minute_debut` int(11) NOT NULL,
  `minute_fin` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;";
	  $DoCreateTables2 = mysql_query($createTables2) or die(mysql_error());

	  $createTables3 = "CREATE TABLE IF NOT EXISTS `".$infosClient->login."_".$listeStreamsToCreate->id."`.`playlist` (
  `id` int(11) NOT NULL auto_increment,
  `nom` varchar(255) character set latin1 collate latin1_general_ci NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;";
	  $DoCreateTables3 = mysql_query($createTables3) or die(mysql_error());

	  $createTables4 = "CREATE TABLE IF NOT EXISTS `".$infosClient->login."_".$listeStreamsToCreate->id."`.`musique_playlist` (
  `id_musique` int(11) NOT NULL,
  `id_playlist` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
	  $DoCreateTables4 = mysql_query($createTables4) or die(mysql_error());

	  // on cree un user
	  $createUser = "CREATE USER '".$infosClient->login."'@'localhost' IDENTIFIED BY '".$infosClient->password."'";
	  $doCreateUser = mysql_query($createUser);

	  // on file les privileges a l'utilisateur
	  $grantUser = "GRANT SELECT , INSERT , UPDATE , DELETE , CREATE , DROP , INDEX , ALTER , CREATE TEMPORARY TABLES , CREATE VIEW , SHOW VIEW , CREATE ROUTINE, ALTER ROUTINE, EXECUTE ON `".$infosClient->login."\_".$listeStreamsToCreate->id."` . * TO '".$infosClient->login."'@'localhost';";
	  $DoGrantUser = mysql_query($grantUser);

	  // et on passe a la creation de la conf liq

	  $selectOffer = "SELECT * FROM `offers` WHERE `id` = '".$listeStreamsToCreate->offerId."'";
	  $resultOffer = mysql_query ($selectOffer);
	  $infosOffer = mysql_fetch_object ($resultOffer);

	  /*
	   ** Generate conf: file, login, idStream, port, password, format live, format output, descr, url, genre nom, bitrate, mountpoint
	   */
	  generateConf($directory.'streams/'.$infosClient->login.'-'.$listeStreamsToCreate->id.'/liquid_'.$listeStreamsToCreate->id.'.liq',
		       $infosClient->login, $listeStreamsToCreate->id, $listeStreamsToCreate->port, $listeStreamsToCreate->password,
		       $listeStreamsToCreate->format_live, $listeStreamsToCreate->format_output,
		       $listeStreamsToCreate->description, $listeStreamsToCreate->url, $listeStreamsToCreate->genre, $listeStreamsToCreate->nom, $infosOffer->bitrate, $listeStreamsToCreate->mountpoint, $listeStreamsToCreate->nb_jingles, $listeStreamsToCreate->start_before,
		       $listeStreamsToCreate->skip_blank_sec, $listeStreamsToCreate->skip_blank_db, $listeStreamsToCreate->skip_blank_mail);

	  // on cree la conf php pour les playlist
	  $configFile = $directory."streams/".$infosClient->login.'-'.$listeStreamsToCreate->id."/conf.php";
	  $handleFile = fopen($configFile, 'r');
	  $contenuFile = fread($handleFile, filesize($configFile));
	  fclose($handleFile);


	  $array_template = array('%database%', '%path%');
	  $array_replace = array($infosClient->login."_".$listeStreamsToCreate->id, $directory."streams/".$infosClient->login."-".$listeStreamsToCreate->id);

	  $newFile = str_replace($array_template, $array_replace, $contenuFile);

	  $handleFile = fopen($directory.'streams/'.$infosClient->login.'-'.$listeStreamsToCreate->id.'/conf.php', 'w+');
	  $writeFile = fwrite($handleFile, $newFile);
	  fclose($handleFile);


	  // on lance liquid
	  $cmd = '/usr/bin/screen -d -m -S '.$infosClient->login.'-'.$infosClient->id.'-'.$listeStreamsToCreate->id.' /usr/local/bin/liquidsoap '.$directory.'streams/'.$infosClient->login.'-'.$listeStreamsToCreate->id.'/liquid_'.$listeStreamsToCreate->id.'.liq'; 
	  exec($cmd);
	  write_log($cmd);

	  // envoi du mail au client

	  $recipient = $infosClient->mail;
	  $subject = "OXYCAST.NET - Creation de votre stream";
	  $content = "Bonjour ".$infosClient->prenom." ".$infosClient->nom.",\n\n";
	  $content .= "Nous avons le plaisir de vous informer que votre stream \"".$listeStreamsToCreate->id." - ".$listeStreamsToCreate->nom."\" a ete cree conformement a votre demande. \n\n";
	  $content .= "Pour connaitre les parametres de connexion pour diffuser votre musique nous vous invitons a vous connecter a notre site avec votre compte.\n\n";
	  $content .= "Pour rappel, votre identifiant est : ". $infosClient->login ."\n";
	  $content .= "Votre serveur FTP pour votre musique est : ".$ip."\n";
	  $content .= "Votre mot de passe ftp est : ". $password ."\n\n";
	  $content .= "Votre stream est un stream ".$infosOffer->slots." slots et est actif pour une periode de ".$infosOffer->duree." jours\n\n\n";
	  $content .= "A tres bientot sur http://www.oxycast.net\n";
	  $headers = "From: OXYCAST.net <contact@oxycast.net>\r\n";
	  $headers .= "Content-Type:text/plain; charset=\"utf-8\"\r\n";
	  mail ($recipient, $subject, $content, $headers);

	  $recip2 = "root@oxycast.net";
	  $subj2 = "Creation du stream ". $listeStreamsToCreate->id;
	  $con2 = "Creation du stream ". $listeStreamsToCreate->id ." reussi.\n\n";
	  $con2 .= "Ce stream appartient a : ". $infosClient->prenom . " " . $infosClient->nom . "(".$infosClient->id." - ".$infosClient->login.")";
	  $head2 = "From: OXYCAST.net <contact@oxycast.net>\r\n";
	  $head2 .= "Content-Type:text/plain; charset=\"utf-8\"\r\n";
	  mail ($recip2, $subj2, $con2, $head2);

	  exec ('php '.$directory.'streams/'.$infosClient->login.'-'.$listeStreamsToCreate->id.'/update.php');

	}
    }
}

function deActiveCurrentStreams() 
{
  $ip = "88.191.250.170";
  $directory = "/home/oxycast/";

  // on selectionne les flux a supprimer car la date de fin est inférieure a now()
  $selectStreamsToDeActive = "SELECT * FROM `streams` WHERE `ip_serveur` = '".$ip."' AND `status` = 'en cours' AND `dateFin` < NOW()";
  $resultStreamsToDeActive = mysql_query($selectStreamsToDeActive);
  $nbStreamsToDeActive = mysql_num_rows ($resultStreamsToDeActive);

  if ($nbStreamsToDeActive != 0) 
    {
      while ($listeStreamsToDeActive = mysql_fetch_object($resultStreamsToDeActive)) 
	{

	  // on kill le stream

	  $selectClientInfos = "SELECT * FROM `clients` WHERE `id` ='".$listeStreamsToDeActive->clientId."'";
	  $resultClientInfos = mysql_query ($selectClientInfos);
	  $infosClient = mysql_fetch_object ($resultClientInfos);

	  $pgrep = '/usr/bin/pgrep -of '.$infosClient->login.'-'.$infosClient->id.'-'.$listeStreamsToDeActive->id;
	  $pid = exec($pgrep);
	  if ($pid)
	    $killok = exec ("/bin/kill -9 " . $pid);
	  write_log("KILL ".$pid." of ".$pgrep."\n");
	  // on modifie le statut du stream a termine
	  $changeStreamStatus = "UPDATE `streams` SET `status` = 'termine' WHERE `id` = '".$listeStreamsToDeActive->id."'";
	  $doChangeStreamStatus = mysql_query($changeStreamStatus);

	  // envoi du mail au client
	  $recipient = $infosClient->mail;
	  $subject = "OXYCAST.NET - Suppression de votre stream";
	  $content = "Bonjour ".$infosClient->prenom." ".$infosClient->nom.",\n\n";
	  $content .= "Nous vous informons que votre stream \"".$listeStreamsToDeActive->id." - ".$listeStreamsToDeActive->nom."\" a ete supprime faute de renouvellement du service. \n\n";
	  $content .= "Vos donnees sont sauvegardees pour une duree de 1 mois et vous pouvez renouveller à tout momenet le service pour les recuperer.\n\n";
	  $content .= "Pour rappel, votre identifiant est : ". $infosClient->login ."\n\n\n";
	  $content .= "A tres bientot sur http://www.oxycast.net\n";
	  $headers = "From: OXYCAST.net <contact@oxycast.net>\r\n";
	  $headers .= "Content-Type:text/plain; charset=\"utf-8\"\r\n";
	  mail ($recipient, $subject, $content, $headers);

	  $recip2 = "root@oxycast.net";
	  $subj2 = "Suppression du stream ". $listeStreamsToDeActive->id;
	  $con2 = "Suppresion du stream ". $listeStreamsToDeActive->id ." reussi.\n\n";
	  $con2 .= "Ce stream appartenait a : ". $infosClient->prenom . " " . $infosClient->nom . "(".$infosClient->id." - ".$infosClient->login.")";
	  $head2 = "From: OXYCAST.net <contact@oxycast.net>\r\n";
	  $head2 .= "Content-Type:text/plain; charset=\"utf-8\"\r\n";
	  mail ($recip2, $subj2, $con2, $head2);
	}
    }
}

function renewStreams() 
{
  $ip = "88.191.250.170";
  $directory = "/home/oxycast/";

  // on selectionne les flux a renouveller (status = renew)
  $selectStreamsToRenew = "SELECT * FROM `streams` WHERE `ip_serveur` = '".$ip."' AND `status` = 'renew'";
  $resultStreamsToRenew = mysql_query($selectStreamsToRenew);
  $nbreStreamsToRenew = mysql_num_rows ($resultStreamsToRenew);

  if ($nbStreamsToRenew != 0) 
    {
      while ($listeStreamsToRenew = mysql_fetch_object($resultStreamsToRenew)) 
	{

	  $selectClientInfos = "SELECT * FROM `clients` WHERE `id` ='".$listeStreamsToRenew->clientId."'";
	  $resultClientInfos = mysql_query ($selectClientInfos);
	  $infosClient = mysql_fetch_object ($resultClientInfos);

	  $doStreamsToRenew = "UPDATE `streams` SET `status` = 'en cours' AND `dateFin` = '".$listeStreamsToRenew->dateFin." + INTERVAL 1 MONTH'";
	  $resultStreamsToRenew = mysql_query($doStreamsToRenew);


	  // envoi du mail au client

	  $recipient = $infosClient->mail;
	  $subject = "OXYCAST.NET - Renouvellement";
	  $content = "Bonjour ".$infosClient->prenom." ".$infosClient->nom.",\n\n";
	  $content .= "Nous vous informons que votre stream \"".$listeStreamsToRenew->id." - ".$listeStreamsToRenewe->nom."\" a bien ete renouvelle. \n\n";
	  $content .= "Merci pour la confiance que vous nous accordez.\n\n";
	  $content .= "Pour rappel, votre identifiant est : ". $infosClient->login ."\n\n\n";
	  $content .= "A tres bientot sur http://www.oxycast.net\n";
	  $headers = "From: OXYCAST.net <contact@oxycast.net>\r\n";
	  $headers .= "Content-Type:text/plain; charset=\"utf-8\"\r\n";
	  mail ($recipient, $subject, $content, $headers);

	  $recip2 = "root@oxycast.net";
	  $subj2 = "Renouvellement du stream ". $listeStreamsToRenewe->id;
	  $con2 = "Renouvellement du stream ". $listeStreamsToRenew->id ." reussi.\n\n";
	  $con2 .= "Ce stream appartenait a : ". $infosClient->prenom . " " . $infosClient->nom . "(".$infosClient->id." - ".$infosClient->login.")";
	  $head2 = "From: OXYCAST.net <contact@oxycast.net>\r\n";
	  $head2 .= "Content-Type:text/plain; charset=\"utf-8\"\r\n";
	  mail ($recip2, $subj2, $con2, $head2);
	}
    }
}

function suspendStreams() 
{
  $ip = "88.191.250.170";
  $directory = "/home/oxycast/";

  // on selectionne le flux a suspendre
  $selectStreamsToSuspend = "SELECT * FROM `streams` WHERE `ip_serveur` = '".$ip."' AND `status` = 'suspendu'";
  $resultStreamsToSuspend = mysql_query($selectStreamsToSuspend);
  $nbStreamsToSuspend = mysql_num_rows ($resultStreamsToSuspend);

  if ($nbStreamsToSuspend != 0) 
    {
      while ($listeStreamsToSuspend = mysql_fetch_object($resultStreamsToSuspend)) 
	{

	  // on kill le stream

	  $selectClientInfos = "SELECT * FROM `clients` WHERE `id` ='".$listeStreamsToSuspend->clientId."'";
	  $resultClientInfos = mysql_query ($selectClientInfos);
	  $infosClient = mysql_fetch_object ($resultClientInfos);

	  $pid = exec ('/usr/bin/pgrep -of '.$infosClient->login.'-'.$infosClient->id.'-'.$listeStreamsToSuspend->id.'.liq'); 
	  if ($pid) 
	    {
	      $killok = exec ("/bin/kill -9 " . $pid); 

	      // envoi du mail au client
	      $recipient = $infosClient->mail;
	      $subject = "OXYCAST.NET - Suppression de votre stream";
	      $content = "Bonjour ".$infosClient->prenom." ".$infosClient->nom.",\n\n";
	      $content .= "Nous vous informons que votre stream \"".$listeStreamsToSuspend->id." - ".$listeStreamsToSuspend->nom."\" a ete suspendu. \n\n";
	      $content .= "Merci de contacter le support dans les plus bref delais. Dans le cas contraire, votre flux sera supprime.\n\n";
	      $content .= "Pour rappel, votre identifiant est : ". $infosClient->login ."\n\n\n";
	      $content .= "A tres bientot sur http://www.oxycast.net\n";
	      $headers = "From: OXYCAST.net <contact@oxycast.net>\r\n";
	      $headers .= "Content-Type:text/plain; charset=\"utf-8\"\r\n";
	      mail ($recipient, $subject, $content, $headers);

	      $recip2 = "root@oxycast.net";
	      $subj2 = "Suspension du stream ". $listeStreamsToSuspend->id;
	      $con2 = "Suspension du stream ". $listeStreamsToSuspend->id ." reussi.\n\n";
	      $con2 .= "Ce stream appartenait a : ". $infosClient->prenom . " " . $infosClient->nom . "(".$infosClient->id." - ".$infosClient->login.")";
	      $head2 = "From: OXYCAST.net <contact@oxycast.net>\r\n";
	      $head2 .= "Content-Type:text/plain; charset=\"utf-8\"\r\n";
	      mail ($recip2, $subj2, $con2, $head2);
	    }
	  else
	    {
	      write_log("ERROR: Cannot kill ".$infosClient->login.'-'.$infosClient->id.'-'.$listeStreamsToSuspend->id."\n");
	      mail("root@oxycast.net",
		   "Warning: Error on suspend",
		   "Cannot kill".$infosClient->login.'-'.$infosClient->id.'-'.$listeStreamsToSuspend->id."\n",
		   $head2);
	    }
	}
    }
}

function reActiveStreams()
{
  $ip = "88.191.250.170";
  $directory = "/home/oxycast/";

  $selectStreamsToReActive = "SELECT * FROM `streams` WHERE `ip_serveur` = '".$ip."' AND (`status` = 'change_password' AND `dateDebut` <= NOW() AND `dateFin` >= NOW()) OR `status` = 'reboot'";
  $resultStreamsToReActive = mysql_query ($selectStreamsToReActive);
  $nbStreamsToReActive = mysql_num_rows ($resultStreamsToReActive);

  if ($nbStreamsToReActive != 0) 
    {
      while ($listeStreamsToReActive = mysql_fetch_object($resultStreamsToReActive))
	{
	  $selectClientInfos = "SELECT * FROM `clients` WHERE `id` ='".$listeStreamsToReActive->clientId."'";
	  $resultClientInfos = mysql_query ($selectClientInfos);
	  $infosClient = mysql_fetch_object ($resultClientInfos);

	  $selectOffer = "SELECT * FROM `offers` WHERE `id` = '".$listeStreamsToReActive->offerId."'";
	  $resultOffer = mysql_query ($selectOffer);
	  $infosOffer = mysql_fetch_object ($resultOffer);

	  //si c'est un reboot on update en en_cours
	  if ($listeStreamsToReActive->status == "reboot")
	    {
	      $requete = "UPDATE `streams` SET `status` = 'en cours' WHERE `clientId` = '".$listeStreamsToReActive->clientId."'";
	      mysql_query($requete);
	    }
	  
	  $pid = exec ('/usr/bin/pgrep -of '.$infosClient->login.'-'.$infosClient->id.'-'.$listeStreamsToReActive->id.'');	  
	  $killok = exec ("/bin/kill -9 ".$pid);
	  write_log("KILL ".$infosClient->login.'-'.$infosClient->id.'-'.$listeStreamsToReActive->id."\n");
	  
	  sleep(2);
	  
	  // on lance le relance
	  $reboot = '/usr/bin/screen -d -m -S '.$infosClient->login.'-'.$infosClient->id.'-'.$listeStreamsToReActive->id.' /usr/local/bin/liquidsoap '.$directory.'streams/'.$infosClient->login.'-'.$listeStreamsToReActive->id.'/liquid_'.$listeStreamsToReActive->id.'.liq'; 
	  exec ($reboot);
	  write_log("REBOOT: ".$reboot."\n");

	  // envoi du mail au client
	  $recipient = $infosClient->mail;
	  $subject = "OXYCAST.NET - Redemarrage de votre stream";
	  $content = "Bonjour ".$infosClient->prenom." ".$infosClient->nom.",\n\n";
	  $content .= "Nous vous informons que suite a un incident technique, votre stream \"".$listeStreamsToReActive->id." - ".$listeStreamsToReActive->nom."\" a ete redemarre. \n\n";
	  $content .= "Pour connaitre les parametres de connexion pour diffuser votre musique nous vous invitons a vous connecter a notre site avec votre compte.\n\n";
	  $content .= "Pour rappel, votre identifiant est : ". $infosClient->login ."\n\n";
	  $content .= "Votre stream est un stream ".$infosOffer->slots." slots et est actif pour une periode de ".$infosOffer->duree." jours\n\n\n";
	  $content .= "A tres bientot sur http://www.oxycast.net\n";
	  $headers = "From: OXYCAST.net <contact@oxycast.net>\r\n";
	  $headers .= "Content-Type:text/plain; charset=\"utf-8\"\r\n";
	  mail ($recipient, $subject, $content, $headers);

	  $recip2 = "root@oxycast.net";
	  $subj2 = "Modification du stream ". $listeStreamsToReActive->id;
	  $con2 = "Modification du stream ". $listeStreamsToReActive->id ." reussi.\n\n";
	  $con2 .= "Ce stream appartient a : ". $infosClient->prenom . " " . $infosClient->nom . "(".$infosClient->id." - ".$infosClient->login.")";
	  $head2 = "From: OXYCAST.net <contact@oxycast.net>\r\n";
	  $head2 .= "Content-Type:text/plain; charset=\"utf-8\"\r\n";
	  mail ($recip2, $subj2, $con2, $head2);
	  $streams_relances[] = "Stream ".$listeStreamsToCreate->id." de ".$infosClient->login." relance";
	}

      if (isset($streams_relances)) 
	{
	  foreach ($streams_relances as $key => $value) 
	    {
	      $content_mail .= "[" . $key . "] => " . $value ."\n"; 
	    }
	  mail("root@oxycast.net", "Liste des streams relances", $content_mail);
	}
    }
}

function updateConfig() 
{
  $ip = "88.191.250.170";
  $directory = "/home/oxycast/";

  $selectStreamsToUpdate = "SELECT * FROM `streams` WHERE `ip_serveur` = '".$ip."' AND `status` = 'change_password' AND `dateDebut` <= NOW() AND `dateFin` >= NOW()";
  $resultStreamsToUpdate = mysql_query ($selectStreamsToUpdate);
  $nbStreamsToUpdate = mysql_num_rows ($resultStreamsToUpdate);

  if ($nbStreamsToUpdate != 0) 
    {
      while ($listeStreamsToUpdate = mysql_fetch_object($resultStreamsToUpdate)) 
	{
	  $updateStreamsToUpdate = "UPDATE `streams` SET `status` = 'en cours' WHERE `id` = '".$listeStreamsToUpdate->id."'";
	  $resultUpdateStreamsToUpdate = mysql_query($updateStreamsToUpdate);
	  // on recree la conf
	  $configFile = $directory."private/moulinette/template.liq";
	  $handleFile = fopen ($configFile, 'r');
	  $contenuFile = fread ($handleFile, filesize($configFile));
	  fclose($handleFile);
	
	  $selectClientInfos = "SELECT * FROM `clients` WHERE `id` ='".$listeStreamsToUpdate->clientId."'";
	  $resultClientInfos = mysql_query ($selectClientInfos);
	  $infosClient = mysql_fetch_object ($resultClientInfos);
	
	  $selectOffer = "SELECT * FROM `offers` WHERE `id` = '".$listeStreamsToUpdate->offerId."'";
	  $resultOffer = mysql_query ($selectOffer);
	  $infosOffer = mysql_fetch_object ($resultOffer);

	  /*
	   ** Generate conf: file, login, idStream, port, password, format live, format output, descr, url, genre nom, bitrate, mountpoint
	   */
	  generateConf($directory.'streams/'.$infosClient->login.'-'.$listeStreamsToUpdate->id.'/liquid_'.$listeStreamsToUpdate->id.'.liq',
		       $infosClient->login, $listeStreamsToUpdate->id, $listeStreamsToUpdate->port, $listeStreamsToUpdate->password,
		       $listeStreamsToUpdate->format_live, $listeStreamsToUpdate->format_output,
		       $listeStreamsToUpdate->description, $listeStreamsToUpdate->url, $listeStreamsToUpdate->genre, $listeStreamsToUpdate->nom, $infosOffer->bitrate, $listeStreamsToUpdate->mountpoint, $listeStreamsToUpdate->nb_jingles, $listeStreamsToUpdate->start_before,
		       $listeStreamsToUpdate->skip_blank_sec, $listeStreamsToUpdate->skip_blank_db, $listeStreamsToUpdate->skip_blank_mail);
	
	  // on kill le flux  
	  $pid = exec ('/usr/bin/pgrep -of '.$infosClient->login.'-'.$infosClient->id.'-'.$listeStreamsToUpdate->id.'');
	  if ($pid)
	    $killok = exec ("/bin/kill -9 ".$pid); 

	  // on lance le relance
	  $launch = exec ('/usr/bin/screen -d -m -S '.$infosClient->login.'-'.$infosClient->id.'-'.$listeStreamsToUpdate->id.' /usr/local/bin/liquidsoap '.$directory.'streams/'.$infosClient->login.'-'.$listeStreamsToUpdate->id.'/liquid_'.$listeStreamsToUpdate->id.'.liq');

	  // envoi du mail au client
	  $recipient = $infosClient->mail;
	  $subject = "OXYCAST.NET - Modification de votre stream";
	  $content = "Bonjour ".$infosClient->prenom." ".$infosClient->nom.",\n\n";
	  $content .= "Nous vous informons que la modification de votre stream \"".$listeStreamsToUpdate->id." - ".$listeStreamsToUpdate->nom."\" a eu lieu conformement a votre demande. \n\n";
	  $content .= "Pour connaitre les parametres de connexion pour diffuser votre musique nous vous invitons a vous connecter a notre site avec votre compte.\n\n";
	  $content .= "Pour rappel, votre identifiant est : ". $infosClient->login ."\n\n";
	  $content .= "Votre stream est un stream ".$infosOffer->slots." slots et est actif pour une periode de ".$infosOffer->duree." jours\n\n\n";
	  $content .= "A tres bientot sur http://www.oxycast.net\n";
	  $headers = "From: OXYCAST.net <contact@oxycast.net>\r\n";
	  $headers .= "Content-Type:text/plain; charset=\"utf-8\"\r\n";
	  mail ($recipient, $subject, $content, $headers);

	  $recip2 = "root@oxycast.net";
	  $subj2 = "Modification du stream ". $listeStreamsToUpdate->id;
	  $con2 = "Modification du stream ". $listeStreamsToUpdate->id ." reussi.\n\n";
	  $con2 .= "Ce stream appartient a : ". $infosClient->prenom . " " . $infosClient->nom . "(".$infosClient->id." - ".$infosClient->login.")\n\n".$launch;
	  $head2 = "From: OXYCAST.net <contact@oxycast.net>\r\n";
	  $head2 .= "Content-Type:text/plain; charset=\"utf-8\"\r\n";
	  mail ($recip2, $subj2, $con2, $head2);
	}
    }
}

// kill tous les streams en etat suspendu 
suspendStreams();

// active les streams payes et pas encore actifs 
activeProgStreams(); 
 
// desactive les streams en fin de vie 
deActiveCurrentStreams(); 
 
// reactive les streams coupes 
$period = exec('uptime | cut -d " " -f 5'); 
$min = exec('uptime | cut -d " " -f 4'); 
if ($period == "min" && $min < "2") 
     reActiveStreams(); 
 
// renouvelle les streams "renew" 
renewStreams(); 
 
// change la config a la demande du client
updateConfig();

reActiveStreams();

?>