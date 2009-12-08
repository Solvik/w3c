<?php
$db = mysql_connect('localhost', 'oxycast', 'ene4UzAADHQ8Juxm')  or die('Erreur de connexion '.mysql_error());
mysql_select_db('%database%',$db)  or die('Erreur de selection '.mysql_error()); 

define("PATH",  "%path%/");
$directory = "/home/oxycast";

/* Répétitiond'une même musique possible au bout de X chanso*/
define("REPETE", 15);


?>