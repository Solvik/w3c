<?php

// si il est loguye
if (isset($_SESSION['login']))
{
  // si y a modif du forumalaire, on post les infos
if (isset($_POST['modifier']))
{

    $message_retour_err = "";
        
    // le nom, le mountpoint et le mdp pour le live sont obligatoires

    if (isset($_POST['nom']) && trim($_POST['nom']) != "" || isset($_POST['mountpoint']) && trim($_POST['mountpoint']) != "" || isset($_POST['password']) && trim($_POST['password']) != "")
    {
        $message_retour_err.= existance_champ($_POST['nom'],"Le nom du stream est obligatoire.<br />");
        $message_retour_err.= existance_champ($_POST['mountpoint'],"Le mountpoint est obligatoire.<br />");    
        $message_retour_err.= existance_champ($_POST['password'],"Le mot de passe pour le live est obligatoire.<br />");        
       
        $message_retour_err.= contenu_champ($_POST['password'],"Le mot de passe ne peut contenir que des caract&egrave;res alphanum&eacute;riques<br />");
    }
    
    if (trim($message_retour_err)=="")
    {
             
    $nom = htmlentities(trim($_POST['nom']));
    $description = htmlentities(trim($_POST['description']));
    $genre = htmlentities(trim($_POST['genre']));
    $url = htmlentities(trim($_POST['url']));
    $mountpoint = htmlentities(trim($_POST['mountpoint']));
    $password = htmlentities(trim($_POST['password']));
     
    $message_retour="Les informations de votre stream ont &eacute;t&eacute; mises &agrave; jour";
        
	// car faut aller recup l'id du client..
	$selectId = "SELECT `id` FROM `clients` WHERE `login` = '".$_SESSION['login']."'";
	$resultId = mysql_query($selectId);
	$Id = mysql_fetch_object($resultId);
	

        $req = "UPDATE `streams` SET `nom` = '$nom', `description`='$description', `genre`='$genre', `url`='$url', `mountpoint`='$mountpoint', `password`='$password', `status` = 'change_password' WHERE clientId='".$Id->id."'";
	$requete = mysql_query($req);
             
    }    
        
    if (isset ($message_retour_err) && trim($message_retour_err) != "")
    {
	echo "<div align=\"center\"><b>$message_retour_err</b> <a href=\"stream\"><b>Retour</b></a></div>";
    }

    if (isset($message_retour) && trim($message_retour) != "")
    {
	echo "<div align=\"center\"><b>$message_retour_err</b> <a href=\"stream\"><b>Retour</b></a></div>";
    }

}

// sinon, on affiche le formulaire
else
{    

$reqClient = "SELECT * FROM `clients` WHERE `login` = '".$_SESSION['login']."'";
$requeteClient = mysql_query($reqClient);
$clientInfos = mysql_fetch_assoc($requeteClient);

$reqStream = "SELECT * FROM `streams` WHERE clientID='".$clientInfos['id']."'";
$requeteStream = mysql_query($reqStream);
$stream = mysql_fetch_assoc($requeteStream);

$reqOffre = "SELECT * FROM `offers` WHERE id='".$stream['offerId']."'";
$requeteOffre = mysql_query($reqOffre);
$offre = mysql_fetch_assoc($requeteOffre);

?>

<div class="boxtitle">Mon stream</div>
<div class="content_content">

<?php
if ($stream)
{
  // on affiche seulement si y a un stream qui existe et qu'il n'est pas suspendu
  if ($stream['id'] != "" && $stream['status'] != "suspendu")
  {

?>

<p><u>Information &agrave; propos de votre offre:</u><br />
Vous b&eacute;n&eacute;ficiez actuellement de l'offre <?php echo "<b>". $offre['bitrate'] ."</b> kbps / <b>". $offre['slots'] ." slots</b> jusqu'au <b>".$stream['dateFin']."</b>"; ?>
<div align="center">

<?php 
if($stream['status'] == "en cours")
{
echo '<a href="index.php?page=stream&act=reboot&streamid='.$stream['id'].'>"<u>Red&eacute;marrer mon stream</u><br /><br /></a>';
 if ($_GET['act'] == "reboot" && $_GET['streamid'] != "")
   {
         $updateChangeStatus = "UPDATE `streams` SET `status` = 'change_password' WHERE `id` = '".$stream['id']."'";
         $resultChangeStatus = mysql_query($updateChangeStatus);

         echo '<div align="center"><b>Votre stream est en cours de red&eacute;marrage.</b></div>';
       }
     else
       {
         echo '<div align="center">Votre stream ne peut pas etre red&eacute;marr&eacute;</div>';
       }
   }


?>
<u>ETAT DE VOTRE STREAM:</u> 
<?php 

      // affichage de l'état
      if ($stream['status'] == 'en cours') { echo '<br /><img src="images/icons/accept.png" alt="" />&nbsp;<b><u>'.$stream['status'].'</b></u>'; }
      else if ($stream['status'] == 'programme') { echo '<br /><img src="images/icons/bell.png" alt="" />&nbsp;Votre stream est en <u><b>cours de cr&eacute;ation.</b></u>'; }
      else if ($stream['status'] == 'commande') { echo '<br /><img src="images/icons/bell.png" alt="" />&nbsp;Stream <u><b>command&eacute;</b></u>.<br /> En attente de paiement/validation.</b></u>'; }
      else if ($stream['status'] == 'suspendu') { echo '<br /><img src="images/icons/delete.png" alt="" />&nbsp;Votre stream est <u><b>suspendu</b></u>'; }
      else if ($stream['status'] == 'en cours') { echo '<br /><img src="images/icons/delete.png" alt="" />&nbsp;Votre stream a <u><b>expir&eacute;</b></u>'; }
      else if ($stream['status'] == 'change_password') { echo '<br /><img src="images/icons/bell.png" alt="" />&nbsp;Votre stream est en cours de modification.'; }
 
      // si le stream n'est pas suspendu ou bien pas termine, on affiche le formulaire.
      if ($stream['status'] != 'suspendu' || $stream['status'] != 'termine') 
	{ 
?>

<form action="" method="post">
<table width="850" border="0" cellpadding="0" cellspacing="5">
  <tr>
    <td align="right">Nom de votre stream</td>
    <td colspan="2"><input type="text" name="nom" value="<?php echo $stream['nom']; ?>" /></td>
    </tr>
  <tr>
    <td align="right">Description</td>
    <td colspan="2"><input type="text" name="description" value="<?php echo $stream['description']; ?>" /></td>
  </tr>
  <tr>
    <td align="right">Genre</td>
    <td colspan="2"><input type="text" name="genre" value="<?php echo $stream['genre']; ?>" /></td>
  </tr>
  <tr>
    <td align="right">Mountpoint</td>
    <td colspan="2"><input type="text" name="mountpoint" value="<?php echo $stream['mountpoint']; ?>" />.mp3</td>
  </tr>
  <tr>
    <td align="right">URL</td>
    <td colspan="2"><input type="text" name="url" value="<?php echo $stream['url']; ?>" /></td>
  </tr>
  <tr>
    <td align="right">Mot de passe (pour le live)</td>
    <td colspan="2"><input type="text" name="password" value="<?php echo $stream['password']; ?>" /></td>
  </tr>
  <tr>
    <td align="right">Port</td>
    <td colspan="2"><?php echo $stream['port']; ?></td>
  </tr>
<br />
<br />
  <tr>
    <td>&nbsp;</td>
    <td><input class="submit" type="submit" name="modifier" /></td>
  </tr>
</table>
</form>
</div>
<?php
	}
      else 
	{ 
	  echo "Vous ne pouvez pas modifier les informations de votre stream"; 
	}
  }
?>

<p><u>Mon flux</u></br />
<div align="center">Votre flux est disponible à l'adresse:<br /><input type="text" name="nom" size="40" value="http://www.oxycast.net:8000/<?php echo $stream['mountpoint']; ?>.mp3" /><input type="button" onclick="copie()" value="copier" /></div>

<?php
  // si le stream est suspendu..
  if ($stream['status'] == "suspendu")
  {
    echo "Votre stream est suspendu, merci de contacter le support.";
  }
?>

</div>

<?php 
} 

else
  {
    echo '<div align="center">Vous n\'avez pas de stream</div>';
  }
}
}

// et ben il est pas identifie !
else 
{
?>

<div class="boxtitle">Erreur</div>
<div class="content_content">
Vous n'etes pas identifi&eacute;.
</div>

<?php 
} 
?>