<?php
if (isset($_SESSION['login']))
{

  $reqClient = "SELECT * FROM `clients` WHERE `login` = '".$_SESSION['login']."'";
  $requeteClient = mysql_query($reqClient);
  $clientInfos = mysql_fetch_object($requeteClient);

  $reqStream = "SELECT * FROM `streams` WHERE clientID='".$clientInfos->id."' AND `status` != 'commande'";
  $requeteStream = mysql_query($reqStream);
  $stream = mysql_fetch_object($requeteStream);

  $reqOffre = "SELECT * FROM `offers` WHERE id='".@$stream->offerId."'";
  $requeteOffre = mysql_query($reqOffre);
  $offre = mysql_fetch_object($requeteOffre);

?>

<div class="boxtitle">Ma musique</div>
<div class="content_content">

<?php

// si un stream existe
   if($stream)
     {   
	// si on update la zik
       if ($_GET['act'] == "update")
       {
	 exec ('php /home/w3c/streams/'.$clientInfos->login."-".$stream->id.'/update.php');
	 echo '<div align="center">Mise &agrave; jour r&eacute;ussie !</div></div>';
       }
   
       else 
       { 
	 echo 'Mettre a jour sa musique en cliquant <a href="index.php?page=musique&act=update">ici</a>';
	 echo "<table>\n";
	 echo '<tr><th><b>Artiste</b></th><th><b>Titre</b></th><th><b>R&eacute;pertoire</b></th></tr>'."\n";  
       
	$searchMusique = "SELECT * FROM `".$clientInfos->login."_".$stream->id."`.`musique`";
	$resultSearchMusique = mysql_query($searchMusique);    

        while($musique = mysql_fetch_object($resultSearchMusique))
        {
	 $toDelete = "streams/".$clientInfos->login."-".$stream->id."/";
	 echo '<tr><th>'.$musique->artiste.'</th><th>'.$musique->titre.'</th><th>'.str_replace($toDelete, "", $musique->path).'/'.$musique->filename.'</th></tr>';
        }

       echo '</table></div>';
      }
     }
   else
     {
?>
<div align="center">Vous ne poss&eacute;dez pas de stream.</div>

</div>

<?php
					  }
   }
else
{
?>

<div class="boxtitle">Erreur</div>
   <div class="content_content">
   Vous n'etes pas identifi&eacute;.
   <div>


<?php
}
?>
