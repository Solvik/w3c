<div class="boxtitle">Contact</div>
<div class="content_content">


	<script language="javascript" type="text/javascript" src="apps/jscripts/tiny_mce/tiny_mce.js"></script>
	<script language="javascript" type="text/javascript">
	// Notice: The simple theme does not use all options some of them are limited to the advanced theme
tinyMCE.init({
	mode : "textareas",
	theme : "simple"
});
	</script>

<?php

$array_choix_sujet = array ( "Je souhaiterais obtenir plus d'informations sur vos services" => "webmaster@oxyradio.net",
                       				"Je souhaiterais obtenir une offre sur mesure" => "staff@oxyradio.net",
                       				"Je souhaiterais beneficier d'une prestation de service" => "dedicaces@oxyradio.net",
					   	"Je vous contacte pour une demande de partenariat" => "artistes@oxyradio.net",
					   	"Ma demande n'est pas list&eacute;e !" => "contact@oxyradio.net",
					   );


if (isset($_POST['envoyer']))
{
    
    $message_retour_err = "";
    
    $message_retour_err.= existance_champ($_POST['nom'],"Le nom est obligatoire<br />");
    $message_retour_err.= existance_champ($_POST['mail'],"L'adresse mail est obligatoire<br />");
    $message_retour_err.= existance_champ($_POST['sujet'],"Le sujet est obligatoire<br />");
    $message_retour_err.= existance_champ($_POST['message'],"Le message est obligatoire<br />");
    
    if (trim($message_retour_err)=="")
    {
        $message_retour_err.= test_email($_POST['mail'],"L'adresse mail n'est pas valide<br />");
    }
    
    if (trim($message_retour_err)=="")
    {
        $mail="Bonjour,<br><br>
Un message vous a été envoyé par le site de Radiofacile.<br><br>
Nom : ".stripslashes($_POST['nom'])."<br>
Mail : ".stripslashes($_POST['mail'])."<br>
Sujet : ".stripslashes($_POST['sujet'])."<br><br>
Message : ".strip_tags(stripslashes($_POST['message']));
         
        $headers = 'From: '.utf8_encode(stripslashes($_POST['nom'])).' <'.utf8_encode(stripslashes($_POST['mail'])) . ">\r\n" .
	  'X-Mailer: PHP/' . phpversion()."\r\nMIME-version: 1.0\r\nContent-type: text/html; charset=utf-8\r\n";
     
        mail($array_choix_sujet[stripslashes($_POST['sujet'])], utf8_encode("Radiofacile: ".stripslashes($_POST['sujet'])), utf8_encode($mail), $headers);
        
        $message_retour="Votre message a bien été envoyé et vous recevrez une réponse rapidement.";
    }    
    
    if (isset ($message_retour_err) && trim($message_retour_err) != "")
    {
        echo "<div style=\"font-size: 12px; color: #FF0000; text-align: center;\"><b>$message_retour_err</b></div><br /><br /><br /><div style=\"font-size: 12px; text-align: center;\"><a href=\"contact.html\"><b>Retour</b></a></div>";
    }

    if (isset($message_retour) && trim($message_retour) != "")
    {
        echo "<br /><br /><br /><br /><br /><div style=\"font-size: 12px; text-align: center;\"><b>$message_retour</b><br /><br /><br /><a href=\"index.html\"><b>Retour à l'accueil</b></a></div>";
    }
}
else
{                       
?>

<img src="images/icons/information.png" alt="" />&nbsp;<span style="font-size: 14px;">Pour tout demande d'informations, de partenariat, contactez nous !</span>
<form action="" method="post">
<br />
<table width="718" border="0" cellpadding="0" cellspacing="5">
  <tr>
    <td width="127" align="right">Nom</td>
    <td colspan="2"><input type="text" name="nom"  /></td>
    </tr>
  <tr>
    <td align="right"><label>Adresse mail</label></td>
    <td colspan="2"><input type="text" name="mail"  /></td>
    </tr>
  <tr>
    <td align="right"><label>Sujet</label></td>
    <td colspan="2"><?php

echo "<select name=\"sujet\">";
echo "<option value=\"\">--- Choisissez ---</option>\n";
foreach ($array_choix_sujet as $choix => $email)
{
    echo "<option value=\"$choix\">$choix</option>\n";
}
echo "</select>";

?></td>
    </tr>
  <tr>
    <td align="right">&nbsp;</td>
    <td colspan="2">Et maintenant votre message :</td>
    </tr>
  <tr>
    <td align="right">&nbsp;</td>
    <td colspan="2"><textarea class="textarea" name="message" cols="50" rows="10"></textarea></td>
  </tr>
  <tr>
    <td align="right">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td align="right">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    </tr>
  <tr>
    <td align="left">&nbsp;</td>
    <td colspan="2" align="left">Tous les champs sont obligatoires</td>
    </tr>
  <tr>
    <td align="right">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td align="right">&nbsp;</td>
    <td colspan="2" align="center"><input class="submit" type="submit" name="envoyer" /></td>
  </tr>
</table>
</form>

<?php } ?>

</div>
