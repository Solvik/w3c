<?php

$host = "localhost";
$user = "oxycast";
$base = "oxycast";
$password = "ene4UzAADHQ8Juxm";

$db = mysql_connect($host,$user,$password);
mysql_select_db($base, $db);


function connect_icecast($host, $port)
{
    $fp = @fsockopen($host, $port, $errno, $errstr, 3);
    
    if(!$fp)
      return false;
    
    fputs($fp, 'GET /status2.xsl' . " HTTP/1.0\r\nUser-Agent: Mozilla/5.0 (X11; U; Linux i686; en-US; rv:0.9.9)\r\n\r\n");
    
    $page = '';
    while(!feof($fp))
      $page .= fread($fp, 1000);
    
    fclose($fp);
    return $page;
}

echo $page;

function tab_icecast($host, $port)
{
  $contenu = connect_icecast($host, $port);
    
    if ($contenu)
      {
      $tabChamps = array(2 => 'name', 3 => 'listeners', 4 => 'description', 5 => 'artist', 6 => 'title', 7 => 'url');
      
      if (preg_match_all('`(/[^,]*),,([^,]*),([^,]*),([^,]*),([^-]*) - ([^,]*),([^,<\/]*)`', $contenu, $resultat))
	{
	  foreach($resultat[1] as $numPoint => $mountName)
	    foreach ($tabChamps as $index => $champ)
	      $tabIce[$mountName][$champ] = $resultat[$index][$numPoint];
	  return $tabIce;
	}
      else
	return false;
      }
    else
      return false;
}


$tabIceCast = tab_icecast('www.oxycast.net', 8000);

if ($tabIceCast)
{
  foreach($tabIceCast as $key => $value)
    {
      // on selectionne les infos du stream correspondant au mointpoint
      $selectInfoStream = 'SELECT * FROM `streams` WHERE `mountpoint` = "'. substr($key,1,-4) .'"';
      $resultInfoStream = mysql_query($selectInfoStream);
      $InfoStream = mysql_fetch_object($resultInfoStream);

      // on selectionne les infos de l'offre correspondant au stream
      $selectInfosOffer = 'SELECT slots FROM `offers` WHERE `id` = "'. $InfoStream->offerId .'"';
      $resultInfosOffer = mysql_query($selectInfosOffer);
      $InfosOffer = mysql_fetch_object($resultInfosOffer);

      // on selectionne les infos du clients
      $selectInfosClient = 'SELECT * FROM `clients` WHERE `id` = "'. $InfoStream->clientId .'"';
      $resultInfosClient = mysql_query($selectInfosClient);
      $InfosClient = mysql_fetch_object($resultInfosClient);
      $nbaudi = $value['listeners'];

      echo 'Sur ' . substr($key,1,-4)  . ' il y a ' . $nbaudi . ' auditeurs.'."\n";

      // si le nb d'audis est superieur a ce que propose l'offre, dede ca va couper
      if ($nbaudi > $InfosOffer->slots)
	{
	  $recipient = $InfosClient->mail;
	  $subject = "OXYCAST.NET - Depassement du nombre d'auditeurs";
	  $content = "Bonjour ".$InfosClient->prenom." ".$InfosClient->nom.",\n\n";
	  $content .= "Nous vous informons que vous avez depasse le nombre d'auditeurs autorise sur votre stream \"".$InfoStream->id." - ".$InfoStream->nom."\".\n\n";
	  $content .= "Nous vous invitons a changer d'offre afin d'adapter celle ci a votre succes. Dans le cas contraire, nous serons force de suspendre le flux.\n\n";
	  $content .= "Pour rappel votre stream est un stream ".$InfosOffer->slots." slots et est actif pour une periode de ".$InfosOffer->duree." jours\n\n\n";
	  $content .= "A tres bientot sur http://www.oxycast.net\n";
	  $headers = "From: OXYCAST.net <contact@oxycast.net>\r\n";
	  $headers .= "Content-Type:text/plain; charset=\"utf-8\"\r\n";
	  mail($recipient, $subject, $content, $headers);
	  
	  mail("root@oxycast.net",
	       "Depassement d'auditeur",
	       "Attention, ".$InfosClient->nom.", stream ".$InfoStream->id." a depasse le nb d'audi !!!",
	       $headers);
	}
      else
	echo "cool\n";
    }
}
else
    echo 'Parsing impossible';


?>
