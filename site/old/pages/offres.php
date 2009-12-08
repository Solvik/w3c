<?php
if(isset($_SESSION['login']))
{
	$selectClientId = 'SELECT `id` FROM `clients` WHERE `login` = "'.$_SESSION['login'].'"';
	$resultClientId = mysql_query($selectClientId);
	$ClientId = mysql_fetch_object($resultClientId);

	$id = $ClientId->id;
}

// s'il commande
if (isset($_GET['act']) && $_GET['act'] == "commande")
{

echo '<div class="boxtitle">Commande d\'un pack</div>';
echo '<div class="content_content">';

// on verifie qu'il est log
if (isset($_SESSION['login']))
	{
      $selectOffres = 'SELECT `id` FROM `streams` WHERE `clientId` = "'.$id.'"';
      $resultOffres = mysql_query($selectOffres);
      
      // on verifie qu'il n'a pas deja une offre
      if (mysql_num_rows($resultOffres) < 1)
		{

			$selectClientId = 'SELECT `id` FROM `clients` WHERE `login` = "'.$_SESSION['login'].'"';
			$resultClientId = mysql_query($selectClientId);
			$ClientId = mysql_fetch_object($resultClientId);

			$id = $ClientId->id;

			// s'il soumet le formulaire
			if (isset($_POST['commander']))
				{
					// FORMULAIRE
					$message_retour_err = "";

					$message_retour_err.= existance_champ($_POST['offer'],"La selection d'une offre est obligatoire<br />");
					$message_retour_err.= existance_champ($_POST['nom'],"Le nom du stream est obligatoire<br />");
					$message_retour_err.= existance_champ($_POST['mountpoint'],"Le point de montage est obligatoire<br />");
					$message_retour_err.= existance_champ($_POST['password'],"Le mot de passe est obligatoire<br />");


					if (trim($message_retour_err)=="")
						{
							$message_retour_err.= contenu_champ($_POST['mountpoint'],"Le point de montage ne peut contenir que des caract&egrave;res alphanum&eacute;riques<br />");
							$message_retour_err.= contenu_champ($_POST['password'],"Le mot de passe ne peut contenir que des caract&egrave;res alphanum&eacute;riques<br />");
						}

					if (trim($message_retour_err)=="")
						{
							$req = "SELECT id FROM `streams` WHERE `mountpoint`='".htmlentities($_POST['mountpoint'])."'";
							$requete = mysql_query($req);
							if (mysql_num_rows($requete) != 0)
								{
									$message_retour_err.="Un membre utilise d&eacute;j&agrave; ce mountpoint ou cette adresse mail<br />";
								}
						}

					if (trim($message_retour_err)=="")
						{
						$description = htmlentities(trim($_POST['description']));
						$genre = htmlentities(trim($_POST['genre']));
						$url = htmlentities(trim($_POST['url']));

						$message_retour='<div align="center">La souscription a &eacute;t&eacute; valid&eacute;e. Merci de proc&eacute;der au paiement.</div>';

						$selectId = 'SELECT `id` FROM `streams` ORDER BY `id` DESC';
						$resultId = mysql_query($selectId);
						$Id = mysql_fetch_assoc($resultId);

						$port = "9000" + $Id['id'];
// A MODIFIER POUR LIP SERVEUR
						$req = 'INSERT INTO `streams` ( `id` , `offerId` , `clientId` , `status` , `nom` , `description`, `genre`, `mountpoint`, `url`, `password`, `port`, `ip_serveur`) VALUES ("", "'.htmlentities($_POST['offer']).'", "'.$id.'", "commande", "'.htmlentities($_POST['nom']).'", "'.$description.'", "'.$genre.'", "'.$_POST['mountpoint'].'", "'.$_POST['url'].'", "'.$_POST['password'].'", "'.$port.'", "88.191.114.8")';
						$requete = mysql_query($req);

						// AFFICHER LE TRUC POUR COMMANDER ICI :
						echo $message_retour;  
						}
				}
		

?>

<form action="" method="post"><br />
<table width="718" border="0" cellpadding="0" cellspacing="5">
  <tr>
    <td width="127" align="right">Votre offre</td>
    <td colspan="2">
	  <select name="offer">
	    <option value="1" <?php if ($_GET['offre'] == "S") { echo "selected"; } else {}  ?>>Pack S (50 auditeurs)</option>
	    <option value="2" <?php if ($_GET['offre'] == "L") { echo "selected"; } else {} ?>>Pack L (100 auditeurs)</option>
	    <option value="3" <?php if ($_GET['offre'] == "XL") { echo "selected"; } else {} ?>>Pack XL (200 auditeurs)</option>
	    <option value="4" <?php if ($_GET['offre'] == "XXL") { echo "selected"; } else {} ?>>Pack XXL (500 auditeurs)</option>
	  </select>
	</td>
  </tr>
  <tr>
    <td align="right">Nom de votre flux *</td>
    <td colspan="2"><input type="text" name="nom"  /></td>
  </tr>
  <tr>
    <td align="right">Point de montage (mountpoint) *</td>
    <td colspan="2"><input type="text" name="mountpoint"  /></td>
  </tr>
  <tr>
    <td align="right">Mot de passe pour le live *</td>
    <td colspan="2"><input type="text" name="password"  /></td>
  </tr>
  <tr>
    <td align="right">Description</td>
    <td colspan="2"><input type="text" name="description"  /></td>
  </tr>
  <tr>
   <td align="right">Genre</td>
    <td colspan="2"><input type="text" name="genre"  /></td>
  </tr>
  <tr>
    <td align="right">URL</td>
    <td colspan="2"><input type="text" name="adresse"  /></td>
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
    <td colspan="2" align="center"><input class="submit" type="submit" name="commander" /></td>
  </tr>
</table>
</form>

<?php
		}
		
		else
		{
			echo '<br /><div align="center">Vous poss&eacute;dez d&eacute;j&agrave; un stream. Merci de nous <a href="contact">contacter</a> pour migrer.</div>';
		}
	
	}
	else
	{
			echo "<br />Vous n'etes pas connect&eacute;";
	}
	echo "</div>";
}
else
{
?>
 
<div class="boxtitle">Offres</div> 
  	<div id="boxes_content">
    
    	<div class="box">
        	<div class="box_content">
        	<div class="boxtitle">Pack S</div>
			<ul>
				<li>50 auditeurs</li>
				<li>160 kbps</li>
				<li>D&eacute;crochage automatique pour live</li>
				<li>Syst&egrave;me de playlist</li>
				<li>Graphiques</li>
				<li>Minisite &eacute;ditable</li>
				<li>etc...</li>
			</ul>
 <div align="center"><a href="commande-S" class="button"><span class="add">Acheter</span></a></div>
            </div>                  
        </div>

    	<div class="box">
        	<div class="box_content">
        	<div class="boxtitle">Pack L</div>
			<ul>
				<li>100 auditeurs</li>
				<li>160 kbps</li>
				<li>D&eacute;crochage automatique pour live</li>
				<li>Syst&egrave;me de playlist</li>
				<li>Graphiques</li>
				<li>Minisite &eacute;ditable</li>
				<li>etc...</li>
			</ul>
 <div align="center"><a href="commande-L" class="button"><span class="add">Acheter</span></a></div>
            </div>                  
        </div>
            
     	<div class="box">
        	<div class="box_content">
        	<div class="boxtitle">Pack XL</div>
			<ul>
				<li>200 auditeurs</li>
				<li>160 kbps</li>
				<li>D&eacute;crochage automatique pour live</li>
				<li>Syst&egrave;me de playlist</li>
				<li>Graphiques</li>
				<li>Minisite &eacute;ditable</li>
				<li>etc...</li>
			</ul>
 <div align="center"><a href="commande-XL" class="button"><span class="add">Acheter</span></a></div>
            </div>                  
        </div>
                
        
    </div><!--end of boxes content-->
	
	 	<div id="boxes_content">
    
    	<div class="box">
        	<div class="box_content">
        	<div class="boxtitle">Pack XXL</div>
			<ul>
				<li>500 auditeurs</li>
				<li>160 kbps</li>
				<li>MP3 & OGG</li>
				<li>D&eacute;crochage automatique pour live</li>
				<li>Syst&egrave;me de playlist</li>
				<li>Graphiques</li>
				<li>Minisite &eacute;ditable</li>
				<li>etc...</li>
			</ul>
 <div align="center"><a href="commande-XXL" class="button"><span class="add">Acheter</span></a></div>
            </div>                  
        </div>

    	<div class="box">
        	<div class="box_content">
        	<div class="boxtitle">Offre sur mesure</div>
             <ul>
	       <li>Nous contacter par mail.</li> 
             </ul>
 <br /><br /><br /><br /><br /><br /><br />
 <div align="center"><a href="contact" class="button"><span class="add">Nous contacter</span></a></div>
            </div>                  
        </div>
                
        
    </div><!--end of boxes content-->
 
  
<?php
      }
?>