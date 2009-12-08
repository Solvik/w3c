<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>OxyCast.net - Ta Webradio en quelques clics</title>
<link rel="stylesheet" type="text/css" href="style.css" media="screen" />
</head>

<body>
<div id="main_container">
	<div class="center_content">
	<div id="header">
    	<div id="logo"><a href="/"><img src="images/logo.png" alt="" title="" border="0" /></a></div>
        
        <div id="menu">
            <ul>                                              
                <li><a href="/" title=""><img src="images/icons/house.png" alt="" />&nbsp;ACCUEIL</a></li>
                <li><a href="offres" title=""><img src="images/icons/cart.png" alt="" />&nbsp;OFFRES</a></li>
                <li><a href="http://wiki.oxycast.net" title=""><img src="images/icons/zoom.png" alt="" />&nbsp;DOCUMENTATION</a></li>
                <li><a href="support" title=""><img src="images/icons/world.png" alt="" />&nbsp;SUPPORT</a></li>
                <li><a href="contact" title=""><img src="images/icons/email.png" alt="" />&nbsp;CONTACT</a></li>
				</ul>
        </div>
        
        <div class="top_text">
<?php
if (!isset($_SESSION['login']))
{
?>
				<form id="login" method="post" action="index.php">
					<p>Identification</p>
					<?php if(isset($erreur)) echo '<p style="color: red;">',$erreur,'</p>'; ?>
					<label class="label" for="login">Utilisateur:</label>
					<input type="text" id="login" name="login" value="" /><br />
					<label class="label" for="pass">Mot de passe:</label>
					<input type="password" id="pass" name="pass" value="" /><br />
					<input type="submit" name="connexion" value="Go" />
					<p><a href="inscription">Inscription</a>      <a href="password">Mot de passe oubli&eacute; ?</a></p>
				</form>

<?php
}
  else
{
                             echo '<ul>';
                             echo '<li><a href="infos">Mes infos perso</a></li>';
                             echo '<li><a href="stream">Mon stream</a></li>';
                             echo '<li><a href="playlists">Mes playlists</a></li>';
			     echo '<li><a href="musique">Ma musique</a></li>';
			     echo '<li><a href="programmation">Programmation</a></li>';
                             echo '<li><a href="monsite">Mon site</a></li>';
if(isset($_SESSION['is_admin'])) { echo '<li><a href="root"><strong>ADMIN</strong></a></li>'; }
                             echo '<li><a href="deconnexion">D&eacute;connexion</a></li>';
                             echo '</ul>';
}
?>				
			
		</div>
    
  </div>
  
  
  <div class="main_content">
  <div class="main_content_top"></div>

  <?php
                             	if(in_array($page,$pages_available) and is_file("pages/$page.php"))
				{
                                  include("pages/$page.php");
				}
                                else
                                {
                                  include("pages/news.php");
				}
	?>
