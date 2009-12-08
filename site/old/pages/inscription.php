<?php

if (isset($_GET["cle"]) and $_GET["cle"] != '') { $cle=htmlentities($_GET["cle"]); }
else { $cle=""; }

if (trim($cle)=="")
{

if (isset($_POST['inscrire']))
{

	$message_retour_err = "";
	
	$message_retour_err.= existance_champ($_POST['pseudo'],"Le pseudo est obligatoire<br />");
	$message_retour_err.= existance_champ($_POST['mail'],"L'adresse mail est obligatoire<br />");
	$message_retour_err.= existance_champ($_POST['mdp'],"Le mot de passe est obligatoire<br />");
	$message_retour_err.= existance_champ($_POST['mdp_conf'],"La confirmation du mot de passe est obligatoire<br />");	
	
	if ($_POST['mdp']!=$_POST['mdp_conf']) { $message_retour_err.="Le mot de passe et sa confirmation sont diff&eacute;rents<br />"; }
	
	if (trim($message_retour_err)=="")
	{
		$message_retour_err.= test_email($_POST['mail'],"L'adresse mail n'est pas valide<br />");
		$message_retour_err.= contenu_champ($_POST['pseudo'],"Le pseudo ne peut contenir que des caract&egrave;res alphanum&eacute;riques<br />");
		$message_retour_err.= contenu_champ($_POST['mdp'],"Le mot de passe ne peut contenir que des caract&egrave;res alphanum&eacute;riques<br />");
	}

	if (trim($message_retour_err)=="")
	{
		$req = "SELECT id FROM `clients` WHERE `login`='".htmlentities($_POST['pseudo'])."' OR `mail`='".htmlentities($_POST['mail'])."'";
		$requete = mysql_query($req);
		if (mysql_num_rows($requete) != 0)
		{
		 	$message_retour_err.="Un membre utilise d&eacute;j&agrave; ce pseudo ou cette adresse mail<br />";
		}
	}
	
	if (trim($message_retour_err)=="")
	{
		
		
		$nom = htmlentities(trim($_POST['nom']));
		$prenom = htmlentities(trim($_POST['prenom']));
		$adresse = htmlentities(trim($_POST['adresse']));
		$cp = htmlentities(trim($_POST['cp']));
		$ville = htmlentities(trim($_POST['ville']));
		$ip = $_SERVER['REMOTE_ADDR'];
		$date_register = time();
		
		
		$message_retour="L'inscription a bien &eacute;t&eacute; effectu&eacute;e.<br />Vous allez recevoir un mail contenant un lien pour valider la cr&eacute;ation de votre compte.";
		
		$cle=substr(md5(microtime() . rand(0,9999)), 0, 24);  	
		$req = "INSERT INTO `clients` ( `id` , `login` , `password` , `nom` , `prenom` , `mail`, `adresse`, `cp`, `ville`, `ip`, `dateInscription`, `dateUpdate`, `admin`, `cle_conf`) VALUES ('', '".htmlentities($_POST['pseudo'])."', '".md5($_POST['mdp'])."', '$nom', '$prenom', '".htmlentities($_POST['mail'])."', '$adresse', '$cp', '$ville', '$ip', '$date_register', '', '0', '$cle')";
		$requete = mysql_query($req);
		 
		$mail="Bonjour ".htmlentities($_POST['pseudo']).",

Vous venez de vous inscrire sur OxyCast

Afin de valider votre inscription, rendez vous sur http://www.oxycast.net/index.php?page=inscription&cle=".$cle."

Pour rappel, vos identifiants sont :
Pseudo : ".htmlentities($_POST['pseudo'])."
Mot de passe : ".$_POST['mdp']."


Bonne visite sur OxyCast !

----
Si vous ne vous e¿½tes pas inscria ï¿½ adiRadio, ne tenez pas compte de ce mail.";
		 
     $headers = 'From: null@oxycast.net' . "\r\n" .
     'Reply-To: null@oxycast.net' . "\r\n" .
     'X-Mailer: PHP/' . phpversion();

     mail($_POST['mail'], "Inscription Webradio en 3 clics", $mail, $headers);
	}	
		
if (isset ($message_retour_err) && trim($message_retour_err) != "")
{
	echo "<div style=\"font-size: 12px; color: #FF0000; text-align: center;\"><b>$message_retour_err</b></div><br /><br /><br /><div style=\"font-size: 12px; text-align: center;\"><a href=\"inscription\"><b>Retour</b></a></div>";
}

if (isset($message_retour) && trim($message_retour) != "")
{
	echo "<br /><br /><br /><br /><br /><div style=\"font-size: 12px; text-align: center;\"><b>$message_retour</b><br /><br /><br /><a href=\"index.php\"><b>Retour &agrave; l'accueil</b></a></div>";
}

	
}
else
{
?>

<div class="boxtitle">Inscription</div>
<div class="content_content">
  <p>Veuillez remplir le formulaire pour proc&eacute;d&eacute; &agrave; votre inscription.</p>


<form action="" method="post"><br />
<table width="718" border="0" cellpadding="0" cellspacing="5">
  <tr>
    <td width="127" align="right">Pseudo *</td>
    <td colspan="2"><input type="text" name="pseudo"  /></td>
  </tr>
  <tr>
    <td align="right">Adresse mail *</td>
    <td colspan="2"><input type="text" name="mail"  /></td>
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
    <td colspan="2"><input type="text" name="nom"  /></td>
  </tr>
  <tr>
    <td align="right">Pr&eacute;nom</td>
    <td colspan="2"><input type="text" name="prenom"  /></td>
  </tr>
  <tr>
    <td align="right">Adresse</td>
    <td colspan="2"><input type="text" name="adresse"  /></td>
  </tr>
  <tr>
    <td align="right">Code Postal</td>
    <td colspan="2"><input type="text" name="cp"  /></td>
  </tr>
  <tr>
    <td align="right">Ville</td>
    <td colspan="2"><input type="text" name="ville"  /></td>
  </tr>
  <tr>
    <td align="right">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2">Les champs marqu&eacute;s d'une <b>*</b> sont obligatoires</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center"><input class="submit" type="submit" name="inscrire" /></td>
  </tr>
</table>
</form>

</div>
<?php 
}	
}
else
{
	$req = "SELECT cle_conf FROM `clients` WHERE cle_conf='$cle'";
	$requete = mysql_query($req);

	if (mysql_num_rows($requete)!=1)
	{
		 $message_retour_err="La cl&eacute; de validation ne correspond &agrave; aucun compte<br />";
	}
	else
	{
		$req = "UPDATE `clients` SET cle_conf='' WHERE cle_conf='$cle'";
		$requete = mysql_query($req);
		$message_retour="Votre compte a bien &eacute;t&eacute; activ&eacute;, vous pouvez maintenant vous loguer<br />";
	}
	
	if (isset ($message_retour_err) && trim($message_retour_err) != "")
{
	echo "<div style=\"font-size: 12px; color: #FF0000; text-align: center;\"><b>$message_retour_err</b></div><br /><br /><br /><div style=\"font-size: 12px; text-align: center;\"><a href=\"index.php\"><b>Retour</b></a></div>";
}

if (isset($message_retour) && trim($message_retour) != "")
{
	echo "<br /><br /><br /><br /><br /><div style=\"font-size: 12px; text-align: center;\"><b>$message_retour</b><br /><br /><br /><a href=\"index.php\"><b>Retour &agrave; l'accueil</b></a></div>";
}
}
?>

