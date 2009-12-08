	<?php

// On demande a etre loguer et etre admin

if (isset($_SESSION['login']))
{
if(isset($_SESSION['is_admin']))
{


echo '    <div class="boxtitle">Panel admin</div>'."\n";
echo '     <div class="content_content">'."\n";
echo '	   	<ul>'."\n";
echo '          <li><a href="index.php?page=root&act=client">G&eacute;r&eacute;r les clients</a></li>'."\n";
echo '          </ul>'."\n";

// partie ADMIN CLIENT

if ($_GET['act'] == "client") 
{

// si on suspend le stream du client

   if ($_GET['do'] == "suspend") 
    {
       $req = "UPDATE `streams` SET `status` = 'suspendu' WHERE `clientId`='". $id ."' LIMIT 1";
       $requete = mysql_query($req);
       $message_retour="L'utilisateur a bien &eacute;t&eacute; suspendu";
       echo "<p><b>$message_retour</b></p><a href=\"index.php?page=root\"><b>Retour</b></a>";
    }

   if ($_GET['do'] == "unsuspend")
    {
       $req = "UPDATE `streams` SET `status` = 'en cours' WHERE `clientId`='". $id ."' LIMIT 1";
       $requete = mysql_query($req);
       $message_retour="L'utilisateur a bien &eacute;t&eacute; d&eacute;suspendu";
       echo "<p><b>$message_retour</b></p><a href=\"index.php?page=root\"><b>Retour</b></a>";
    }

   if ($_GET['do'] == "renew")
    {
       $req = "UPDATE `streams` SET `status` = 'renew' WHERE `clientId`='". $id ."' LIMIT 1";
       $requete = mysql_query($req);
       $message_retour="L'utilisateur a bien &eacute;t&eacute; renouvell&eacute;";
       echo "<p><b>$message_retour</b></p><a href=\"index.php?page=root\"><b>Retour</b></a>";
    }

   if ($_GET['do'] == "active")
    {
       $req = "UPDATE `streams` SET `status` = 'programme' WHERE `clientId`='". $id ."' LIMIT 1";
       $requete = mysql_query($req);

       $active = "UPDATE `streams` SET `dateDebut` = NOW(), `dateFin` = NOW() + INTERVAL 1 MONTH WHERE `clientId` = '". $id ."' LIMIT 1";
       $doactive = mysql_query($active);
       $message_retour="L'utilisateur a bien &eacute;t&eacute; activ&eacute;";
       echo "<p><b>$message_retour</b></p><a href=\"index.php?page=root\"><b>Retour</b></a>";
    }
    
$req = "SELECT * FROM `clients` ORDER BY `login` ASC";
$requete = mysql_query($req);
echo "<table>\n";
echo '<tr><th width="30%"><b>Pseudo</b></th><th width="30%"><b>Mail</b></th><th width="30%"><b>Offres</b></th><th width="30%"><b>Actions</b></th></tr>'."\n";
  while ($membre = mysql_fetch_assoc($requete))
  {
	  echo "<tr>\n<td>".$membre['login']."</a></td>\n";

	  // on recupere l'offre du client
	  $selectStream = "SELECT * FROM `streams` WHERE `clientId` = '".$membre['id']."'";
	  $resultSelectStream = mysql_query($selectStream);
	  $stream = mysql_fetch_object($resultSelectStream);

	  // on recupere les infos de l'offre
	  $selectOffer = "SELECT * FROM `offers` WHERE `id` = '".$stream->offerId."'";
	  $resultOffer = mysql_query ($selectOffer);
	  $infosOffer = mysql_fetch_object ($resultOffer);

	  echo "<td>".$membre['mail']."</td>\n";

	  if ($infosOffer) { echo "<td>".$infosOffer->bitrate." kbps / ".$infosOffer->slots." slots</td>\n"; } else { echo "<td> N/A </td>\n"; }

	  if (mysql_num_rows($resultSelectStream) != "0") 
	  {
	    echo "<td><a href=\"index.php?page=root&act=client&do=renew&id=".$membre['id']."\"\><img src=\"images/icons/hourglass_add.png\" alt=\"\" title=\"Renouveller\" /></a>  ";

	  if ($stream->status != "commande")
	  {
	    if ($stream->status != "suspendu") 
	    { 
	      echo "<a href=\"index.php?page=root&act=client&do=suspend&id=".$membre['id']."\"><img src=\"images/icons/delete.png\" alt=\"\" title=\"Suspendre\" /></a></td></tr>\n"; 
	    } 
	    else
	    { 
	      echo "<a href=\"index.php?page=root&act=client&do=unsuspend&id=".$membre['id']."\"><img src=\"images/icons/connect.png\" alt=\"\" title=\"Desuspendre\" /></a></td></tr>\n"; 
	    }
	   }
	   else
	   {
	      echo "<a href=\"index.php?page=root&act=client&do=active&id=".$membre['id']."\"><img src=\"images/icons/star.png\" alt=\"\" title=\"Activer\" /></a></td></tr>\n";
	   }
	  }

}
echo "</table>";
}


}
}
echo "</div>";

?>


