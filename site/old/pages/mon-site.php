<?php

if (isset($_SESSION['login']))
{
	include('../blog/minisite.class.php');
	
	function isOk($string)
	{
	$select = "SELECT blog FROM `clients` WHERE `blog` = '".$string."'";
	$query = mysql_query ($select);
	$result = mysql_fetch_object($query);
	 return ereg("^[a-zA-Z0-9_-]+$",$string) && !$result;	
	}
	
	$select = "SELECT id,blog FROM `clients` WHERE `login` = '".$_SESSION['login']."'";
	$query = mysql_query ($select);
	$result = mysql_fetch_object($query);
	
	echo '<div class="boxtitle">Mon Site</div>';

	if ($result->blog == '') {
		if (!isset($_POST['nom'])) {
		?>
		<p>Vous n'avez pas encore de mini-site pour votre stream! Pour en cr&eacute;er un choisissez un nom et cliquez sur creer:</p>
		<form action="" method="post">
		<label for="nom">Nom du mini-site:</label>
  		<input type="text" name="nom"/>
  		 <input type="submit" value="Creer" />
		</from>
		<?php
		} else {
			if (isOk($_POST['nom'])) {
				$site = new miniSite($result->id,$_POST['nom']);
				if ($site->install()) {
					echo '<p>Votre site est correctement install&eacute; vous pouvez le configurer en cliquand <a href="http://blog.oxycast.net/'.$_POST['nom'].'">ICI</a></p>';
				} else {
					echo '<p>Une erreur &agrave; eu lieu lors de l\'installation de votre site, veuillez contacter votre administrateur.</p>';
				}
			} else {
				echo '<p>Le nom de votre site est incorrect ou d&eacute;j&agrave; reserv&eacute;. (le nom doit contenir uniquement des chiffre, lettes et tirets "-" ou underscore "_".)</p>';
			}
		}
	} else {
		echo '<p>Accedez &agrave; votre site sur: <a href="'.$result->blog.'">'.$result->blog.'</a></p>';
	}
}

else 
{
?>

				<div class="boxtitle">Erreur</div>
					<div class="content_content">
                          Vous n'etes pas identifi&eacute;.
                    </div>

<?php } ?>