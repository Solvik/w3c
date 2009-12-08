<?php
if (isset($_SESSION['login']))
{
  if (isset($_POST['modifier']))
    {
      
      $message_retour_err = "";
      
      $message_retour_err.= existance_champ($_POST['mail'],"L'adresse mail est obligatoire<br />");
      
      if (isset($_POST['mdp']) && trim($_POST['mdp']) != "")
	{
	  $message_retour_err.= existance_champ($_POST['mdp'],"Le mot de passe est obligatoire<br />");
	  $message_retour_err.= existance_champ($_POST['mdp_conf'],"La confirmation du mot de passe est obligatoire<br />");    
	  
	  if ($_POST['mdp']!=$_POST['mdp_conf']) { $message_retour_err.="Le mot de passe et sa confirmation sont diff&eacute;rents<br />"; }
	  
	  $message_retour_err.= contenu_champ($_POST['mdp'],"Le mot de passe ne peut contenir que des caract&egrave;res alphanum&eacute;riques<br />");
	}
      
      if (trim($message_retour_err)=="")
	{
	  $message_retour_err.= test_email($_POST['mail'],"L'adresse mail n'est pas valide<br />");       
	  
	}
      
      if (trim($message_retour_err)=="")
	{
	  
	  
	  $nom = htmlentities(trim($_POST['nom']));
	  $prenom = htmlentities(trim($_POST['prenom']));
	  $adresse = htmlentities(trim($_POST['adresse']));
	  $cp = htmlentities(trim($_POST['cp']));
	  $ville = htmlentities(trim($_POST['ville']));
	  
	  $message_retour="Les informations de votre compte ont &eacute;t&eacute; mises &agrave; jour";
	  
	  
	  $req = "UPDATE `clients` SET `mail` = '".htmlentities($_POST['mail'])."', `nom`='".$nom."', `prenom`='".$prenom."', `adresse`='".$adresse."', `cp`='".$cp."', `ville`='".$ville."' WHERE `login`='".$_SESSION['login']."'";
	  $requete = mysql_query($req);
	  
	  if (isset($_POST['mdp']) && trim($_POST['mdp']) != "")
	    { 
	      $req = "UPDATE `clients` SET `password` = '".md5($_POST['mdp'])."' WHERE `login`='".$_SESSION['login']."'";
	      $requete = mysql_query($req);
	    }
	  
	}    
      
      if (isset ($message_retour_err) && trim($message_retour_err) != "")
	{
	  echo "<div style=\"font-size: 12px; color: #FF0000; text-align: center;\"><b>$message_retour_err</b></div><br /><br /><br /><div style=\"font-size: 12px; text-align: center;\"><a href=\"infos\"><b>Retour</b></a></div>";
	}
      
      if (isset($message_retour) && trim($message_retour) != "")
	{
	  echo "<br /><br /><br /><br /><br /><div style=\"font-size: 12px; text-align: center;\"><b>$message_retour</b><br /><br /><br /><a href=\"index.php\"><b>Retour &agrave; l'accueil</b></a></div>";
	}
      
    }
  else
    {    
      
      $req = "SELECT * FROM `clients` WHERE login='".$_SESSION['login']."'";
      
      $requete = mysql_query($req);
      $compte = mysql_fetch_assoc($requete);
?>

<div class="boxtitle">Modification de vos informations</div>
<div class="content_content">

<form action="" method="post">
<table width="718" border="0" cellpadding="0" cellspacing="5">
  <tr>
    <td width="127" align="right">Pseudo</td>
    <td width="576"><?php echo $compte['login']; ?></td>
    </tr>
  <tr>
    <td align="right">Adresse mail *</td>
    <td colspan="2"><input type="text" name="mail" value="<?php echo $compte['mail']; ?>" /></td>
  </tr>
  <tr>
    <td align="right">Mot de passe *</td>
    <td colspan="2"><input type="password" name="mdp"  /></td>
  </tr>
  <tr>
    <td align="right">Confirmation *</td>
    <td colspan="2"><input type="password" name="mdp_conf"  /></td>
  </tr>
  <tr>
    <td align="right">Nom</td>
    <td colspan="2"><input type="text" name="nom" value="<?php echo $compte['nom']; ?>" /></td>
  </tr>
  <tr>
    <td align="right">Pr&eacute;nom</td>
    <td colspan="2"><input type="text" name="prenom" value="<?php echo $compte['prenom']; ?>" /></td>
  </tr>
  <tr>
    <td align="right">Adresse</td>
    <td colspan="2"><input type="text" name="adresse" value="<?php echo $compte['adresse']; ?>" /></td>
  </tr>
  <tr>
    <td align="right">Code Postal</td>
    <td colspan="2"><input type="text" name="cp" value="<?php echo $compte['cp']; ?>" /></td>
  </tr>
  <tr>
    <td align="right">Ville</td>
    <td colspan="2"><input type="text" name="ville" value="<?php echo $compte['ville']; ?>" /></td>
  </tr>
<br />
<br />
  <tr>
    <td>&nbsp;</td>
    <td align="center"><input class="submit" type="submit" name="modifier" /></td>
  </tr>
</table>
</form>

</div>

<?php 
		  } 
}

else 
{
?>

                <div class="post">
                     <h2 class="title">Erreur</h2>
                     <div class="story">
                          Vous n'etes pas identifi&eacute;.
                     </div>
                </div>

<?php

}

?>