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
    	<div id="logo"><a href="index"><img src="images/logo.png" alt="" title="" border="0" /></a></div>        

<?php
if (!is_online())
{
?>
				<form id="mainlogin" method="post" action="login">
					<fieldset>

						<input class="headerpseudo" type="text" name="login" value="" />
						<input class="headerpassword" type="password" name="pass" value="" />
						<input class="headergo" type="submit" name="connexion" value="" /><br />
						<a href="inscription" style="padding-left: 24em;">Inscription</a>    <a style="color: white;">|</a>  <a href="password">Mot de passe oubli&eacute; ?</a><br />
					</fieldset>
				</form>

<?php
}
  else
{
    ?>
	<div id="usermenu">
		<ul>
			<li><a href="infos">Mes infos perso</a></li>
			<li><a href="stream">Mon stream</a></li>
			<li><a href="playlists">Mes playlists</a></li>
			<li><a href="musique">Ma musique</a></li>
			<li><a href="programmation">Programmation</a></li>
			<li><a href="blog">Mon site</a></li>
			<?php if(isset($_SESSION['is_admin'])): ?><li><a href="root"><strong>ADMIN</strong></a></li><?php endif; ?>
			<li><a href="deconnexion">D&eacute;connexion</a></li>
		</ul>
	</div>
<?php } ?>				

        <div id="menu">
            <ul>                                              
                <li><a href="index" title=""><img src="images/icons/house.png" alt="" />&nbsp;ACCUEIL</a></li>
                <li><a href="offres" title=""><img src="images/icons/cart.png" alt="" />&nbsp;OFFRES</a></li>
                <li><a href="http://wiki.oxycast.net" title=""><img src="images/icons/zoom.png" alt="" />&nbsp;WIKI</a></li>
                <li><a href="support" title=""><img src="images/icons/world.png" alt="" />&nbsp;SUPPORT</a></li>
                <li><a href="contact" title=""><img src="images/icons/email.png" alt="" />&nbsp;CONTACT</a></li>
				</ul>
        </div>
        <div class="top_text">		
		</div>
    
  </div>
  
  
  <div class="main_content">
  <div class="main_content_top"></div>
